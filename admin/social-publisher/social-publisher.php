<?php

/**
 * Post to a Facebook profile or page timeline
 *
 * @since 1.1
 */
class Facebook_Social_Publisher {
	/**
	 * Initialize social publisher hooks
	 *
	 * @since 1.2
	 *
	 * @global \Facebook_Loader $facebook_loader test if Facebook app access token exists
	 * @return void
	 */
	public static function init() {
		global $facebook_loader;

		if ( ! ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() ) )
			return;

		// always load publish and delete hooks
		// post can be published or deleted many different ways
		add_action( 'transition_post_status', array( 'Facebook_Social_Publisher', 'publish' ), 10, 3 );
		add_action( 'before_delete_post', array( 'Facebook_Social_Publisher', 'delete_facebook_post' ) );

		if ( is_admin() ) {
			self::add_save_post_hooks();

			// load meta box hooks on post creation screens
			foreach( array( 'post', 'post-new' ) as $hook ) {
				add_action( 'load-' . $hook . '.php', array( 'Facebook_Social_Publisher', 'load' ), 1, 0 );
			}
		}
	}

	/**
	 * Check for meta box content on post save.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public static function add_save_post_hooks() {
		// verify if this is an auto save routine.
		// If it is exit early without loading additional files
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Profile' ) )
			require_once( dirname(__FILE__) . '/publish-box-profile.php' );
		add_action( 'save_post', array( 'Facebook_Social_Publisher_Meta_Box_Profile', 'save' ) );

		if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Page' ) )
			require_once(  dirname(__FILE__) . '/publish-box-page.php' );
		add_action( 'save_post', array( 'Facebook_Social_Publisher_Meta_Box_Page', 'save' ) );
	}

	/**
	 * Add actions to post edit page.
	 *
	 * @since 1.2.3
	 *
	 * @return void
	 */
	public static function load() {
		// on post pages
		add_action( 'admin_notices', array( 'Facebook_Social_Publisher', 'output_post_admin_notices' ) );

		// wait until after post data loaded, then evaluate post
		add_action( 'add_meta_boxes', array( 'Facebook_Social_Publisher', 'load_post_features' ), 1, 0 );
	}

	/**
	 * Can the current user publish to Facebook?
	 *
	 * @since 1.1
	 *
	 * @param int $wordpress_user_id WordPress user identifier
	 * @return bool true if Facebook data stored for user and permissions exist
	 */
	public static function user_can_publish_to_facebook( $wordpress_user_id = null ) {
		global $facebook_loader;

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( $facebook_loader->plugin_directory . 'facebook-user.php' );

		return Facebook_User::can_publish_to_facebook( $wordpress_user_id );
	}

	/**
	 * Can the site possibly publish to Facebook on behalf of a WordPress user?
	 *
	 * Access token stored along with the token may fail, but its existence is an indicator of possible success.
	 *
	 * @since 1.1
	 *
	 * @return array associative array of stored page data or empty array
	 */
	public static function get_publish_page() {
		$page = get_option( 'facebook_publish_page' );
		if ( is_array( $page ) && isset( $page['access_token'] ) && isset( $page['id'] ) && isset( $page['name'] ) )
			return $page;
		return array();
	}

	/**
	 * Test if a post type is intended for use publicly.
	 *
	 * If not explicitly declared as public a post type is considered non-public (default false).
	 *
	 * @since 1.2.3
	 *
	 * @see register_post_type()
	 * @param string $post_type WordPress post type
	 * @return bool true if public else false
	 */
	public static function post_type_is_public( $post_type ) {
		// empty string or a false response from get_post_type()
		if ( ! $post_type )
			return false;

		$post_type_object = get_post_type_object( $post_type );
		if ( isset( $post_type_object->public ) && $post_type_object->public )
			return true;

		return false;
	}

	/**
	 * Test if a post's post status is public.
	 *
	 * @since 1.2.3
	 *
	 * @param int $post_id WordPress post identifier
	 * @return bool true if public, else false
	 */
	public static function post_status_is_public( $post_id ) {
		$post_status_object = get_post_status_object( get_post_status( $post_id ) );
		if ( ! $post_status_object )
			return false;

		if ( isset( $post_status_object->public ) && $post_status_object->public )
			return true;

		return false;
	}

	/**
	 * Get post capability singular base to be used when gating access.
	 *
	 * @since 1.1
	 *
	 * @param string $post_type post type
	 * @return string post type object capability type or empty string
	 */
	public static function post_type_capability_base( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );
		if ( ! isset( $post_type_object->capability_type ) )
			return '';

		$capability_singular_base = '';
		if ( is_string( $post_type_object->capability_type ) ) {
			$capability_singular_base = $post_type_object->capability_type;
		} else if ( is_array( $post_type_object->capability_type ) ) {
			if ( isset( $post_type_object->capability_type[0] ) )
				$capability_singular_base = $post_type_object->capability_type[0];
		}

		return $capability_singular_base;
	}

	/**
	 * Load post meta boxes and actions after post data loaded if post matches publisher preferences and capabilities.
	 *
	 * @since 1.2.3
	 *
	 * @global stdClass|WP_Post $post WordPress post object
	 * @return void
	 */
	public static function load_post_features() {
		global $post;

		if ( ! isset( $post ) )
			return;

		$post_type = get_post_type( $post );
		// do not load meta boxes if post type not public or post status is public
		if ( ! self::post_type_is_public( $post_type ) || self::post_status_is_public( $post->ID ) )
			return;

		$capability_singular_base = self::post_type_capability_base( $post_type );

		// unable to verify capability for the post type
		// most likely reason: custom post type incorrectly registered
		if ( ! $capability_singular_base )
			return;

		// only display post meta boxes if current user can edit the current post
		if ( ! current_user_can( 'edit_' . $capability_singular_base, $post->ID ) )
			return;

		// load page meta box if Facebook Page data saved
		$page_to_publish = self::get_publish_page();
		if ( ! empty( $page_to_publish ) ) {
			if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Page' ) )
				require_once(  dirname(__FILE__) . '/publish-box-page.php' );

			Facebook_Social_Publisher_Meta_Box_Page::add_meta_box( $post_type, $page_to_publish );
		}
		unset( $page_to_publish );

		// does the current post support authorship? can the post author post to Facebook Timeline?
		if ( post_type_supports( $post_type, 'author' ) && isset( $post->post_author ) && self::user_can_publish_to_facebook( (int) $post->post_author ) ) {
			if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Profile' ) )
				require_once( dirname(__FILE__) . '/publish-box-profile.php' );

			Facebook_Social_Publisher_Meta_Box_Profile::add_meta_box( $post_type );
		}
	}

	/**
	 * Act on the publish action.
	 *
	 * Attempt to post to author timeline if Facebook data exists for author. Attempt to post to associated site page if page data saved.
	 *
	 * @since 1.1
	 *
	 * @param string $new_status name of the new WordPress post status
	 * @param string $old_status name of the old WordPress post status
	 * @param stdClass|W{_Post $post WordPress post object
	 * @return void
	 */
	public static function publish( $new_status, $old_status, $post ) {
		// content not public even if status public
		if ( ! empty( $post->post_password ) )
			return;

		if ( ! self::post_type_is_public( get_post_type( $post ) ) )
			return;

		// transition from non-public to public?
		$new_status_object = get_post_status_object( $new_status );
		if ( ! ( $new_status_object && isset( $new_status_object->public ) && $new_status_object->public ) )
			return;

		$old_status_object = get_post_status_object( $old_status );
		if ( ! $old_status_object || ( isset( $old_status_object->public ) && $old_status_object->public ) )
			return;

		// transition post status happens before save post
		// wait until the end of the insert / update process to send to Facebook
		if ( isset( $post->post_author ) && self::user_can_publish_to_facebook( (int) $post->post_author ) )
			add_action( 'wp_insert_post', array( 'Facebook_Social_Publisher', 'publish_to_facebook_profile' ), 10, 2 );

		$publish_page = self::get_publish_page();
		if ( ! empty( $publish_page ) )
			add_action( 'wp_insert_post', array( 'Facebook_Social_Publisher', 'publish_to_facebook_page' ), 10, 2 );
	}

	/**
	 * Publish a post to a Facebook Page.
	 *
	 * @since 1.0
	 *
	 * @link https://developers.facebook.com/docs/reference/api/page/#posts Facebook Graph API create page post
	 * @param int $post_id WordPress post identifier
	 * @param stdClass|WP_Post $post WordPress post object
	 * @return void
	 */
	public static function publish_to_facebook_page( $post_id, $post ) {
		global $facebook_loader;

		$post_id = absint( $post_id );
		if ( ! ( $post && $post_id ) )
			return;

		// check if this post has previously been posted to the Facebook page
		// no need to publish again
		if ( get_post_meta( $post_id, 'fb_fan_page_post_id', true ) )
			return;

		if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Page' ) )
			require_once( dirname(__FILE__) . '/publish-box-page.php' );

		$meta_box_present = true;
		if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
			$meta_box_present = false;

		if ( $meta_box_present && get_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Page::POST_META_KEY_FEATURE_ENABLED, true ) === '0' )
			return;

		setup_postdata( $post );

		$post_type = get_post_type( $post );
		if ( ! self::post_type_is_public( $post_type ) )
			return;

		// do not publish a protected post
		if ( ! empty( $post->post_password ) )
			return;

		$facebook_page = self::get_publish_page();
		if ( ! $facebook_page )
			$facebook_page = get_option( 'facebook_publish_page' );
		if ( ! ( is_array( $facebook_page ) && ! empty( $facebook_page['access_token'] ) && ! empty( $facebook_page['id'] ) && isset( $facebook_page['name'] ) ) )
			return;

		// check our assumptions about a valid link in place
		// fail if a piece of the filter process killed our response
		$link = apply_filters( 'facebook_rel_canonical', get_permalink( $post_id ) );
		if ( ! $link )
			return;

		$args = array(
			'access_token' => $facebook_page['access_token'],
			'link' => $link
		);
		if ( isset( $facebook_page['appsecret_proof'] ) && $facebook_page['appsecret_proof'] )
			$args['appsecret_proof'] = $facebook_page['appsecret_proof'];

		if ( $meta_box_present )
			$args['fb:explicitly_shared'] = 'true';

		// either message or link is required
		// confident we have link, making message optional
		$fan_page_message = get_post_meta( $post_id, 'fb_fan_page_message', true );
		if ( ! empty( $fan_page_message ) )
			$args['message'] = $fan_page_message;
		unset( $fan_page_message );

		$status_messages = array();
		try {
			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( $facebook_loader->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

			$publish_result = Facebook_WP_Extend::graph_api( $facebook_page['id'] . '/feed', 'POST', $args );

			if ( isset( $publish_result['id'] ) ) {
				update_post_meta( $post_id, 'fb_fan_page_post_id', sanitize_text_field( $publish_result['id'] ) );
				delete_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Page::POST_META_KEY_FEATURE_ENABLED );
				delete_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Page::POST_META_KEY );
			}
		} catch (WP_FacebookApiException $e) {
			$error_result = $e->getResult();

			// error validating access token
			if ( $e->getCode() == 190 ) {
				delete_option( 'facebook_publish_page' );

				$status_messages[] = array( 'message' => esc_html( sprintf( __( 'Failed posting to %s Timeline because the access token expired.', 'facebook' ), $facebook_page['name'] ) ) . ' ' . esc_html( __( 'To reactivate publishing, visit Facebook Social Publisher settings page and associate a Page through the "Allow new posts to a Facebook Page" link.', 'facebook' ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
			} else {
				$status_messages[] = array( 'message' => esc_html( sprintf( __( 'Failed posting to %s Timeline.', 'facebook' ), $facebook_page['name'] ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
			}
			unset( $error_result );
		}

		if ( isset( $publish_result ) && isset( $publish_result['id'] ) ) {
			$link = '<a href="' . esc_url( self::get_permalink_from_feed_publish_id( sanitize_text_field( $publish_result['id'] ) ), array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( $facebook_page['name'] ) . '</a>';
			if ( isset( $args['message'] ) )
				$message = sprintf( esc_html( __( 'Posted to %1$s with message "%2$s"', 'facebook' ) ), $link, esc_html( $args['message'] ) );
			else
				$message = sprintf( esc_html( __( 'Posted to %s', 'facebook' ) ), $link );
			unset( $link );
			$status_messages[] = array( 'message' => $message, 'error' => false );
			unset( $message );
		}
		unset( $publish_result );

		if ( ! empty( $status_messages ) ) {
			// allow author and page messages on the same post
			$existing_status_messages = get_post_meta( $post_id, 'facebook_status_messages', true );
			if ( is_array( $existing_status_messages ) && ! empty( $existing_status_messages ) )
				$status_messages = array_merge( $existing_status_messages, $status_messages );
			unset( $existing_status_messages );

			update_post_meta( $post_id, 'facebook_status_messages', $status_messages );
			add_filter( 'redirect_post_location', array( 'Facebook_Social_Publisher', 'add_new_post_location' ) );
		}
	}

	/**
	 * Publish a post to a Facebook User Timeline.
	 *
	 * @since 1.0
	 *
	 * @global \Facebook_Loader $facebook_loader Access Facebook application credentials
	 * @param int $post_id WordPress post identifier
	 * @param stdClass|WP_Post $post WordPress post object
	 * @return void
	 */
	public static function publish_to_facebook_profile( $post_id, $post ) {
		global $facebook_loader;

		$post_id = absint( $post_id );
		if ( ! ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() && $post && $post_id ) )
			return;

		// does the current post have an existing Facebook post id stored? no need to publish again
		if ( get_post_meta( $post_id, 'fb_author_post_id', true ) )
			return;

		$meta_box_present = true;
		if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
			$meta_box_present = false;

		if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Profile' ) )
			require_once( dirname(__FILE__) . '/publish-box-profile.php' );
		if ( $meta_box_present && get_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Profile::POST_META_KEY_FEATURE_ENABLED, true ) === '0' )
			return;

		setup_postdata( $post );

		$post_type = get_post_type( $post );
		if ( ! ( self::post_type_is_public( $post_type ) && post_type_supports( $post_type, 'author' ) && isset( $post->post_author ) ) )
			return;

		$post_author = (int) $post->post_author;
		if ( ! $post_author )
			return;

		// test the author, not the current actor
		if ( ! self::user_can_publish_to_facebook( $post_author ) )
			return;

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( $facebook_loader->plugin_directory . 'facebook-user.php' );
		$author_facebook_id = Facebook_User::get_facebook_profile_id( $post_author );
		if ( ! $author_facebook_id )
			return;

		// check our assumptions about a valid link in place
		// fail if a piece of the filter process killed our response
		$link = apply_filters( 'facebook_rel_canonical', get_permalink( $post_id ) );
		if ( ! $link )
			return;

		$og_action = false;
		if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
			require_once( $facebook_loader->plugin_directory . 'admin/settings-social-publisher.php' );
		if ( get_option( Facebook_Social_Publisher_Settings::OPTION_OG_ACTION ) )
			$og_action = true;

		if ( ! class_exists( 'Facebook_Open_Graph_Protocol' ) )
			require_once( $facebook_loader->plugin_directory . 'open-graph-protocol.php' );

		$path = $author_facebook_id . '/';
		if ( $og_action && Facebook_Open_Graph_Protocol::get_post_og_type( $post ) === 'article' ) {
			$story = array( 'article' => $link );
			$path .= 'news.publishes';
			if ( $meta_box_present )
				$story['fb:explicitly_shared'] = 'true';
		} else {
			$story = array( 'link' => $link );
			$path .= 'feed';
		}

		$message = get_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Profile::POST_META_KEY_MESSAGE, true );
		if ( is_string( $message ) && $message )
			$story['message'] = trim( $message );

		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( $facebook_loader->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

		$status_messages = array();
		try {
			$publish_result = Facebook_WP_Extend::graph_api_with_app_access_token( $path, 'POST', $story );

			if ( isset( $publish_result['id'] ) ) {
				update_post_meta( $post_id, 'fb_author_post_id', sanitize_text_field( $publish_result['id'] ) );
				delete_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Profile::POST_META_KEY_MESSAGE );
				delete_post_meta( $post_id, Facebook_Social_Publisher_Meta_Box_Profile::POST_META_KEY_FEATURE_ENABLED );
			}
		} catch (WP_FacebookApiException $e) {
			$error_result = $e->getResult();

			$status_messages[] = array( 'message' => esc_html( __( 'Failed posting to your Facebook Timeline.', 'facebook' ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
		}

		if ( isset( $publish_result ) && isset( $publish_result['id'] ) ) {
			$link = '<a href="' . esc_url( 'https://www.facebook.com/' . $publish_result['id'], array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'Facebook Timeline', 'facebook' ) ) . '</a>';
			if ( empty( $message ) )
				$message = sprintf( esc_html( __( 'Posted to %s', 'facebook' ) ), $link );
			else
				$message = sprintf( esc_html( __( 'Posted to %1$s with message "%2$s"', 'facebook' ) ), $link, esc_html( $message ) );
			$status_messages[] = array( 'message' => $message, 'error' => false );
		}

		// add new status messages
		if ( ! empty( $status_messages ) ) {
			$existing_status_messages = get_post_meta( $post_id, 'fb_status_messages', true );

			if ( is_array( $existing_status_messages ) && ! empty( $existing_status_messages ) )
				$status_messages = array_merge( $existing_status_messages, $status_messages );

			update_post_meta( $post_id, 'facebook_status_messages', $status_messages );
			add_filter( 'redirect_post_location', array( 'Facebook_Social_Publisher', 'add_new_post_location' ) );
		}
	}

	/**
	 * Parse the unique post id from a feed.
	 *
	 * @since 1.0
	 *
	 * @param string $id feed publish identifier
	 * @return string Facebook URL
	 */
	public static function get_permalink_from_feed_publish_id( $id ) {
		preg_match_all( "/(.*?)_(.*?)$/su", $id, $ids, PREG_SET_ORDER );

		return 'https://www.facebook.com/' . $ids[0][2];
	}

	/**
	 * Add a query argument to trigger displaying admin messages on the front-end.
	 *
	 * @since 1.0
	 *
	 * @param string $loc URL
	 * $return string URL with facebook_message query parameter appended
	 */
	public static function add_new_post_location( $loc ) {
		return add_query_arg( 'facebook_message', 1, $loc );
	}

	/**
	 * Output admin notices saved to post data during the Facebook publish process.
	 *
	 * Triggers if our GET argument is present from redirecting the post location.
	 *
	 * @since 1.1
	 *
	 * @global stdClass|WP_Post WordPress post object
	 * @return void
	 */
	public static function output_post_admin_notices() {
		global $post;

		if ( empty( $_GET['facebook_message'] ) || ! isset( $post ) || ! isset( $post->ID ) )
			return;

		$post_id = absint( $post->ID );
		if ( ! $post_id )
			return;

		$post_meta_key = 'facebook_status_messages';
		$messages = get_post_meta( $post_id, $post_meta_key, true );
		if ( ! is_array( $messages ) )
			return;

		foreach ( $messages as $message ) {
			if ( ! isset( $message['message'] ) )
				continue;

			$div = '<div class="fade ';
			if ( isset( $message['error'] ) && $message['error'] )
				$div .= 'error';
			else
				$div .= 'updated';
			$div .= '"><p>';
			$div .= $message['message']; // escaped when generated. may contain links
			$div .= '</p></div>';
			echo $div;
			unset( $div );
		}

		// display once
		delete_post_meta( $post_id, $post_meta_key );
	}

	/**
	 * Delete post data from Facebook when deleted in WordPress
	 *
	 * @since 1.0
	 *
	 * @global \Facebook_Loader $facebook_loader Reference plugin directory
	 * @param int $post_id WordPress post identifer
	 * @return void
	 */
	public static function delete_facebook_post( $post_id ) {
		global $facebook_loader;

		$post_id = absint( $post_id );
		if ( ! $post_id )
			return;

		$fb_page_post_id = get_post_meta( $post_id, 'fb_fan_page_post_id', true );
		if ( $fb_page_post_id ) {
			$page_to_publish = self::get_publish_page();
			if ( isset( $page_to_publish['access_token'] ) ) {
				if ( ! class_exists( 'Facebook_WP_Extend' ) )
					require_once( $facebook_loader->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

				// act as the saved credential, not current user
				try {
					Facebook_WP_Extend::graph_api( $fb_page_post_id, 'DELETE', array( 'access_token' => $page_to_publish['access_token'] ) );
				} catch (WP_FacebookApiException $e) {}
			}
			unset( $page_to_publish );
		}
		unset( $fb_page_post_id );

		$post = get_post( $post_id );
		if ( isset( $post->post_author ) && self::user_can_publish_to_facebook( (int) $post->post_author ) ) {

			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( $facebook_loader->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

			$fb_author_post_id = get_post_meta( $post_id, 'fb_author_post_id', true );
			if ( $fb_author_post_id ) {
				try {
					Facebook_WP_Extend::graph_api_with_app_access_token( $fb_author_post_id, 'DELETE' );
				} catch (WP_FacebookApiException $e) {}
			}
			unset( $fb_author_post_id );

			// support old post mentions
			$fb_mentioned_pages_post_ids = get_post_meta( $post_id, 'fb_mentioned_pages_post_ids', true );
			if ( $fb_mentioned_pages_post_ids ) {
				foreach( $fb_mentioned_pages_post_ids as $page_post_id ) {
					try {
						Facebook_WP_Extend::graph_api_with_app_access_token( $page_post_id, 'DELETE' );
					} catch (WP_FacebookApiException $e) {}
				}
			}
			unset( $fb_mentioned_pages_post_ids );

			$fb_mentioned_friends_post_ids = get_post_meta( $post_id, 'fb_mentioned_friends_post_ids', true );
			if ( $fb_mentioned_friends_post_ids ) {
				foreach( $fb_mentioned_friends_post_ids as $page_post_id ) {
					try {
						Facebook_WP_Extend::graph_api_with_app_access_token( $page_post_id, 'DELETE' );
					} catch (WP_FacebookApiException $e) {}
				}
			}
			unset( $fb_mentioned_friends_post_ids );
		}
	}
}

?>
