<?php

/**
 * Post to a Facebook profile or page timeline
 * Mention profiles or pages in the post
 *
 * @since 1.1
 */
class Facebook_Social_Publisher {
	/**
	 * Can the current user publish the current post?
	 *
	 * @since 1.1
	 * @var bool
	 */
	protected $user_can_publish_post = false;

	/**
	 * Is the post already published to the public site?
	 * Avoid republishing social data for public posts or older posts added before the plugin was enabled
	 *
	 * @since 1.1
	 * @var bool
	 */
	protected $post_is_public = false;

	/**
	 * Add init action
	 *
	 * @since 1.1
	 */
	public function __construct() {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		// always load publish and delete hooks
		// post can be published or deleted many different ways
		add_action( 'transition_post_status', array( 'Facebook_Social_Publisher', 'publish' ), 10, 3 );
		add_action( 'before_delete_post', array( 'Facebook_Social_Publisher', 'delete_facebook_post' ) );
		self::add_save_post_hooks();

		// load meta box hooks on post creation screens
		foreach( array( 'post', 'post-new' ) as $hook ) {
			add_action( 'load-' . $hook . '.php', array( &$this, 'load' ) );
		}
	}

	/**
	 * Check for meta box content on post save
	 *
	 * @since 1.1
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

		if ( ! class_exists( 'Facebook_Mentions_Box' ) )
			require_once( dirname(__FILE__) . '/mentions/mentions-box.php' );
		Facebook_Mentions_Box::add_save_post_hooks();
	}

	/**
	 * Add actions after init
	 *
	 * @since 1.1
	 */
	public function load() {
		$this->user_can_facebook_publish = self::user_can_publish_to_facebook();
		$this->page_to_publish = self::get_publish_page();

		// need at least publisher permissions or page permissions for social publisher
		if ( ! $this->user_can_facebook_publish && empty( $this->page_to_publish ) )
			return;

		// on post pages
		add_action( 'admin_notices', array( 'Facebook_Social_Publisher', 'output_post_admin_notices' ) );

		// wait until after post data loaded, then evaluate post
		add_action( 'add_meta_boxes', array( &$this, 'load_post_features' ) );
	}

	/**
	 * Can the current user publish to Facebook?
	 *
	 * @since 1.1
	 * @return bool true if Facebook data stored for user and permissions exist
	 */
	public static function user_can_publish_to_facebook() {
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/facebook-user.php' );

		$current_user = wp_get_current_user();

		if ( Facebook_User::can_publish_to_facebook() && ! Facebook_User::get_user_meta( $current_user->ID, 'facebook_timeline_disabled', true ) )
			return true;
		return false;
	}

	/**
	 * Can the site possibly publish to Facebook on behalf of a WordPress user?
	 * Access token stored along with the token may fail, but its existence is an indicator of possible success
	 *
	 * @since 1.1
	 * @return array associative array of stored page data or empty array 
	 */
	public static function get_publish_page() {
		$page = get_option( 'facebook_publish_page' );
		if ( is_array( $page ) && isset( $page['access_token'] ) && isset( $page['id'] ) && isset( $page['name'] ) )
			return $page;
		return array();
	}

	/**
	 * Test if a post's post status is public
	 *
	 * @since 1.1
	 * @param int $post_id post identifier
	 * @return bool true if public, else false
	 */
	public static function post_is_public( $post_id ) {
		$post_status_object = get_post_status_object( get_post_status( $post_id ) );
		if ( ! $post_status_object )
			return false;

		if ( isset( $post_status_object->public ) && $post_status_object->public )
			return true;

		return false;
	}

	/**
	 * Get post capability singular base to be used when gating access
	 *
	 * @since 1.1
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
	 * Load post meta boxes and actions after post data loaded if post matches publisher preferences and capabilities
	 *
	 * @since 1.1
	 */
	public function load_post_features() {
		global $post;

		if ( ! isset( $post ) )
			return;

		$post_type = get_post_type( $post );
		if ( ! $post_type )
			return;

		$capability_singular_base = self::post_type_capability_base( $post_type );

		// unable to verify capability for the post type
		// most likely reason: custom post type incorrectly registered
		if ( ! $capability_singular_base )
			return;

		if ( current_user_can( 'publish_' . $capability_singular_base, $post->ID ) )
			$this->user_can_publish_post = true;

		// is the current post already public?
		if ( self::post_is_public( $post->ID ) )
			$this->post_is_public = true;

		// post to page data exists. load features
		if ( ! $this->post_is_public && ! empty( $this->page_to_publish ) ) {
			if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Page' ) )
				require_once(  dirname(__FILE__) . '/publish-box-page.php' );

			Facebook_Social_Publisher_Meta_Box_Page::add_meta_box( $post_type, $this->page_to_publish );
		}

		// can the current user post to Facebook? Does the current post support authorship?
		if ( $this->user_can_publish_post && $this->user_can_facebook_publish && post_type_supports( $post_type, 'author' ) ) {
			$current_user = wp_get_current_user();

			if ( ! isset( $post->post_author ) || $post->post_author == $current_user->ID ) {
				if ( ! $this->post_is_public ) {
					if ( ! class_exists( 'Facebook_Social_Publisher_Meta_Box_Profile' ) )
						require_once( dirname(__FILE__) . '/publish-box-profile.php' );

					Facebook_Social_Publisher_Meta_Box_Profile::add_meta_box( $post_type );
				}

				// it does not make sense to re-publish a post with mentions to Facebook but an author may want to show additional mentions in the post after it is published.
				// allow for local mentions not sent to Facebook at the time the post was made public
				if ( ! class_exists( 'Facebook_Mentions_Box' ) )
					require_once( dirname(__FILE__) . '/mentions/mentions-box.php' );
				Facebook_Mentions_Box::after_posts_load( $post_type );
			}
		}
	}

	/**
	 * Act on the publish action
	 * Attempt to post to author timeline if Facebook data exists for author
	 * Attempt to post to associated site page if page data saved
	 *
	 * @since 1.1
	 * @param int $post_id post identifier
	 * @param stdClass $post post object
	 */
	public static function publish( $new_status, $old_status, $post ) {
		// content not public even if status public
		if ( ! empty( $post->post_password ) )
			return;

		// transition from non-public to public?
		$new_status_object = get_post_status_object( $new_status );
		if ( ! ( $new_status_object && isset( $new_status_object->public ) && $new_status_object->public ) )
			return;

		$old_status_object = get_post_status_object( $old_status );
		if ( ! $old_status_object || ( isset( $old_status_object->public ) && $old_status_object->public ) )
			return;

		if ( self::user_can_publish_to_facebook() )
			self::publish_to_facebook_profile( $post );

		$page_to_publish = self::get_publish_page();
		if ( ! empty( $page_to_publish ) )
			self::publish_to_facebook_page( $post, $page_to_publish );
	}

	/**
	 * Publish a post to a Facebook page
	 *
	 * @since 1.0
	 * @link https://developers.facebook.com/docs/reference/api/page/#posts Facebook Graph API create page post
	 * @param stdClass $post post object
	 * @param array $facebook_page stored Facebook page data
	 */
	public static function publish_to_facebook_page( $post, $facebook_page = null ) {
		global $facebook, $post;

		if ( ! ( isset( $facebook ) && $post ) )
			return;

		$post_id = $post->ID;

		// check if this post has previously been posted to the Facebook page
		// no need to publish again
		if ( get_post_meta( $post_id, 'fb_fan_page_post_id', true ) )
			return;

		// thanks to Tareq Hasan on http://wordpress.org/support/topic/plugin-facebook-bug-problems-when-publishing-to-a-page
		$post = get_post( $post );
		setup_postdata( $post );

		// do not publish a protected post
		if ( ! empty( $post->post_password ) )
			return;

		if ( ! $facebook_page )
			$facebook_page = get_option( 'facebook_publish_page' );
		if ( ! ( is_array( $facebook_page ) && isset( $facebook_page['access_token'] ) && isset( $facebook_page['id'] ) && isset( $facebook_page['name'] ) ) )
			return;

		$post_type = get_post_type( $post );
		if ( ! $post_type )
			return $post_type;

		// check our assumptions about a valid link in place
		// fail if a piece of the filter process killed our response
		$link = apply_filters( 'facebook_rel_canonical', get_permalink( $post_id ) );
		if ( ! $link )
			return;

		$args = array(
			'access_token' => $facebook_page['access_token'],
			'from' => $facebook_page['id'],
			'link' => $link,
			'fb:explicitly_shared' => 'true',
			'ref' => 'fbwpp'
		);

		// either message or link is required
		// confident we have link, making message optional
		$fan_page_message = get_post_meta( $post_id, 'fb_fan_page_message', true );
		if ( ! empty( $fan_page_message ) )
			$args['message'] = $fan_page_message;

		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( $post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			if ( ! empty( $post_thumbnail_url ) )
				$args['picture'] = $post_thumbnail_url;
		}

		if ( post_type_supports( $post_type, 'title' ) ) {
			$title = trim( html_entity_decode( get_the_title( $post_id ), ENT_COMPAT, 'UTF-8' ) );
			if ( $title )
				$args['name'] = $title;
			unset( $title );
		}

		if ( ! class_exists( 'Facebook_Open_Graph_Protocol' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/open-graph-protocol.php' );

		if ( post_type_supports( $post_type, 'excerpt' ) && ! empty( $post->post_excerpt ) ) {
			$excerpt = trim( apply_filters( 'get_the_excerpt', $post->post_excerpt ) );
			if ( $excerpt ) {
				$excerpt = Facebook_Open_Graph_Protocol::clean_description( $excerpt );
				if ( $excerpt )
					$args['caption'] = $excerpt;
			}
			unset( $excerpt );
		}

		$post_content = Facebook_Open_Graph_Protocol::clean_description( $post->post_content, false );
		if ( $post_content )
			$args['description'] = $post_content;

		$status_messages = array();
		try {
			$publish_result = $facebook->api( '/' . $facebook_page['id'] . '/feed', 'POST', $args );

			update_post_meta( $post_id, 'fb_fan_page_post_id', sanitize_text_field( $publish_result['id'] ) );
		} catch (WP_FacebookApiException $e) {
			$error_result = $e->getResult();

			// error validating access token
			if ( $e->getCode() == 190 ) {
				delete_option( 'facebook_publish_page' );

				$status_messages[] = array( 'message' => esc_html( sprintf( __( 'Failed posting to %s Timeline because the access token expired.', 'facebook' ), $facebook_page['name'] ) ) . esc_html( __( 'To reactivate publishing, visit the Facebook settings page and re-enable the "Publish to fan page" setting.', 'facebook' ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
			} else {
				$status_messages[] = array( 'message' => esc_html( sprintf( __( 'Failed posting to %s Timeline.', 'facebook' ), $facebook_page['name'] ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
			}
		}

		if ( isset( $publish_result ) && isset( $publish_result['id'] ) ) {
			$link = '<a href="' . esc_url( self::get_permalink_from_feed_publish_id( sanitize_text_field( $publish_result['id'] ) ), array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( $facebook_page['name'] ) . '</a>';
			if ( isset( $args['message'] ) )
				$message = sprintf( esc_html( __( 'Posted to %1$s with message "%2$s"', 'facebook' ) ), $link, esc_html( $args['message'] ) );
			else
				$message = sprintf( esc_html( __( 'Posted to %s', 'facebook' ) ), $link );
			$status_messages[] = array( 'message' => $message, 'error' => false );
		}

		if ( ! empty( $status_messages ) ) {
			// allow author and page messages on the same post
			$existing_status_messages = get_post_meta( $post_id, 'facebook_status_messages', true );
			if ( is_array( $existing_status_messages ) && ! empty( $existing_status_messages ) )
				$status_messages = array_merge( $existing_status_messages, $status_messages );

			update_post_meta( $post_id, 'facebook_status_messages', $status_messages );
			add_filter( 'redirect_post_location', array( 'Facebook_Social_Publisher', 'add_new_post_location' ) );
		}
	}

	/**
	 * Publish a post to a Facebook Timeline
	 *
	 * @since 1.0
	 * @param stdClass $post post object
	 */
	public static function publish_to_facebook_profile( $post ) {
		global $facebook, $post;

		if ( ! ( isset( $facebook ) && isset( $post ) ) )
			return;

		$post_id = $post->ID;

		// does the current post have an existing Facebook post id stored? no need to publish again
		if ( get_post_meta( $post_id, 'fb_author_post_id', true ) )
			return;

		$post = get_post( $post );
		setup_postdata( $post );

		$post_type = get_post_type( $post );
		if ( ! ( $post_type && post_type_supports( $post_type, 'author' ) ) )
			return;

		// the person publishing the post may not be the same person who authored the post
		// publish to the timeline of the author, not the post approver / publisher
		// TODO: allow an author without publish capability to allow WordPress users with the capability to publish on his or her behalf
		$current_user = wp_get_current_user();
		if ( isset( $post->post_author ) && $post->post_author != $current_user->ID )
			return;

		if ( ! self::user_can_publish_to_facebook() )
			return;

		$author_messages = self::post_to_author_timeline( $post );
		if ( is_array( $author_messages ) && ! empty( $author_messages ) )
			$status_messages = $author_messages;
		else
			$status_messages = array();
		unset( $author_messages );

		$friends_messages = self::post_to_mentioned_friends_timelines( $post );
		if ( is_array( $friends_messages ) )
			$status_messages = array_merge( $status_messages, $friends_messages );
		unset( $friends_messages );

		$pages_messages = self::post_to_mentioned_pages_timelines( $post );
		if ( is_array( $pages_messages ) && ! empty( $pages_messages ) )
			$status_messages = array_merge( $status_messages, $pages_messages );
		unset( $pages_messages );

		if ( ! empty( $status_messages ) ) {
			$existing_status_messages = get_post_meta( $post_id, 'fb_status_messages', true );

			if ( is_array( $existing_status_messages ) && ! empty( $existing_status_messages ) )
				$status_messages = array_merge($existing_status_messages, $status_messages);

			update_post_meta( $post_id, 'facebook_status_messages', $status_messages );
			add_filter( 'redirect_post_location', array( 'Facebook_Social_Publisher', 'add_new_post_location' ) );
		}
	}

	/**
	 * Post to the timelines of mentioned friends
	 *
	 * @since 1.1
	 * @param stdClass $post post object
	 * @return array status messages with message and error. used to update publisher of performed action on admin_notices
	 */
	public static function post_to_mentioned_friends_timelines( $post ) {
		global $facebook;

		$post_id = $post->ID;
		$post_type = get_post_type( $post );

		$mentioned_friends = get_post_meta( $post_id, 'fb_mentioned_friends', true );
		if ( empty( $mentioned_friends ) )
			return array();

		// check our assumptions about a valid link in place
		// fail if a piece of the filter process killed our response
		$link = apply_filters( 'facebook_rel_canonical', get_permalink( $post_id ) );
		if ( ! $link )
			return;

		$story = array(
			'link' => $link,
			'ref' => 'fbwpp'
		);

		// either message or link is required
		// confident we have link, making message optional
		$mentioned_friends_message = get_post_meta( $post_id, 'fb_mentioned_friends_message', true );
		if ( ! empty( $mentioned_friends_message ) )
			$story['message'] = $mentioned_friends_message;

		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( $post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			if ( ! empty( $post_thumbnail_url ) )
				$story['picture'] = $post_thumbnail_url;
		}

		if ( post_type_supports( $post_type, 'title' ) ) {
			$title = trim( html_entity_decode( get_the_title( $post_id ), ENT_COMPAT, 'UTF-8' ) );
			if ( $title )
				$story['name'] = $title;
			unset( $title );
		}

		if ( ! class_exists( 'Facebook_Open_Graph_Protocol' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/open-graph-protocol.php' );

		if ( post_type_supports( $post_type, 'excerpt' ) && ! empty( $post->post_excerpt ) ) {
			$excerpt = trim( apply_filters( 'get_the_excerpt', $post->post_excerpt ) );
			if ( $excerpt ) {
				$excerpt = Facebook_Open_Graph_Protocol::clean_description( $excerpt );
				if ( $excerpt )
					$story['caption'] = $excerpt;
			}
			unset( $excerpt );
		}

		$post_content = Facebook_Open_Graph_Protocol::clean_description( $post->post_content, false );
		if ( $post_content )
			$story['description'] = $post_content;

		$publish_ids_friends = array();
		$friends_posts = array();
		$status_messages = array();

		// define a photo URL template once, with SSL goodness. sprintf later inside the loop
		$photo_params = array( 'width' => 15, 'height' => 15 );
		if ( is_ssl() )
			$photo_params['return_ssl_resources'] = 1;
		$photo_url = 'http' . ( is_ssl() ? 's' : '' ) . '://graph.facebook.com/%s/picture?' . http_build_query( $photo_params );
		unset( $photo_params );

		foreach( $mentioned_friends as $friend )  {
			try {
				$publish_result = $facebook->api( '/' . $friend['id'] . '/feed', 'POST', $story );

				$publish_ids_friends[] = sanitize_text_field( $publish_result['id'] );

				$friends_post = '<a href="' . esc_url( self::get_permalink_from_feed_publish_id( $publish_result['id'] ), array( 'http', 'https' ) ) . '" target="_blank">';
				$friends_post .= '<img src="' . esc_url( sprintf( $photo_url, $friend['id'] ), array( 'http', 'https' ) ) . '" width="15" height="15" alt="' . ( isset( $friend['name'] ) ? esc_attr( $friend['name'] ) : '' ) . '" />';
				$friends_post .= '</a>';
				$friends_posts[] = $friends_post;
				unset( $friends_post );
			} catch (WP_FacebookApiException $e) {
				$error_result = $e->getResult();

				if ( $e->getCode() == 210) {
					$status_messages[] = array( 'message' => esc_html( __( 'Failed posting to mentioned friend\'s Facebook Timeline.', 'facebook' ) ) . '<img src="' . esc_url( sprintf( $photo_url, $friend['id'] ), array( 'http', 'https' ) ) . '" width="15" height="15" alt="' . ( isset( $friend['name'] ) ? esc_attr( $friend['name'] ) : '' ) . ' /> ' . esc_html( __( 'Error: Page doesn\'t allow posts from other Facebook users.', 'facebook' ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
				} else {
					$status_messages[] = array( 'message' => esc_html( __( 'Failed posting to mentioned friend\'s Facebook Timeline.', 'facebook' ) ) . '<img src="' . esc_url( sprintf( $photo_url, $friend['id'] ), array( 'http', 'https' ) ) . '" width="15" height="15" alt="' . ( isset( $friend['name'] ) ? esc_attr( $friend['name'] ) : '' ) . '" /> ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
				}
			}
		}

		if ( ! empty( $publish_ids_friends ) )
			update_post_meta( $post_id, 'fb_mentioned_friends_post_ids', $publish_ids_friends );

		if ( ! empty( $friends_posts ) )
			$status_messages[] = array( 'message' => esc_html( __( 'Posted to mentioned friends\' Facebook Timelines.', 'facebook' ) ) . implode( ' ', $friends_posts ), 'error' => false );

		return $status_messages;
	}

	/**
	 * Post to the timeline of mentioned Facebook pages
	 *
	 * @since 1.1
	 * @param stdClass $post post object
	 * @return array status messages with message and error. used to update publisher of performed action on admin_notices
	 */
	public static function post_to_mentioned_pages_timelines( $post ) {
		global $facebook;

		$post_id = $post->ID;
		$post_type = get_post_type( $post );
		if ( ! $post_type )
			return;

		$mentioned_pages = get_post_meta( $post_id, 'fb_mentioned_pages', true );
		if ( ! is_array( $mentioned_pages ) || empty( $mentioned_pages ) )
			return;

		// check our assumptions about a valid link in place
		// fail if a piece of the filter process killed our response
		$link = apply_filters( 'facebook_rel_canonical', get_permalink( $post_id ) );
		if ( ! $link )
			return;

		$story = array(
			'link' => $link,
			'ref' => 'fbwpp'
		);

		// either message or link is required
		// confident we have link, making message optional
		$mentioned_pages_message = get_post_meta( $post_id, 'fb_mentioned_pages_message', true );
		if ( ! empty( $mentioned_pages_message ) )
			$story['message'] = $mentioned_pages_message;
		unset( $mentioned_pages_message );

		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( $post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			if ( ! empty( $post_thumbnail_url ) )
				$story['picture'] = $post_thumbnail_url;
		}

		if ( post_type_supports( $post_type, 'title' ) ) {
			$title = trim( html_entity_decode( get_the_title( $post_id ), ENT_COMPAT, 'UTF-8' ) );
			if ( $title )
				$story['name'] = $title;
			unset( $title );
		}

		if ( ! class_exists( 'Facebook_Open_Graph_Protocol' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/open-graph-protocol.php' );

		if ( post_type_supports( $post_type, 'excerpt' ) && ! empty( $post->post_excerpt ) ) {
			$excerpt = trim( apply_filters( 'get_the_excerpt', $post->post_excerpt ) );
			if ( $excerpt ) {
				$excerpt = Facebook_Open_Graph_Protocol::clean_description( $excerpt );
				if ( $excerpt )
					$story['caption'] = $excerpt;
			}
			unset( $excerpt );
		}

		$post_content = Facebook_Open_Graph_Protocol::clean_description( $post->post_content, false );
		if ( $post_content )
			$story['description'] = $post_content;
		unset( $post_content );

		// define a photo URL template once, with SSL goodness. sprintf later inside the loop
		$photo_params = array( 'width' => 15, 'height' => 15 );
		if ( is_ssl() )
			$photo_params['return_ssl_resources'] = 1;
		$photo_url = 'http' . ( is_ssl() ? 's' : '' ) . '://graph.facebook.com/%s/picture?' . http_build_query( $photo_params );
		unset( $photo_params );

		$pages_posts = array();
		$publish_ids_pages = array();

		foreach( $mentioned_pages as $page ) {
			try {
				$publish_result = $facebook->api( '/' . $page['id'] . '/feed', 'POST', $story );

				$published_id = sanitize_text_field( $publish_result['id'] );
				$publish_ids_pages[] = $published_id;

				$pages_posts[] = '<a href="' . esc_url( self::get_permalink_from_feed_publish_id( $published_id ), array( 'http', 'https' ) ) . '" target="_blank"><img src="' . esc_url( sprintf( $photo_url, $page['id'] ), array( 'http', 'https' ) ) . '" alt="' . ( isset( $page['name'] ) ? esc_attr( $page['name'] ) : '' ) . '" width="15" height="15" /></a>';

			} catch (WP_FacebookApiException $e) {
				$error_result = $e->getResult();

				if ( $e->getCode() == 210 ) {
					$status_messages[] = array( 'message' => esc_html( __( 'Failed posting to mentioned page\'s Facebook Timeline.', 'facebook' ) ) . ' <img src="' . esc_url( sprintf( $photo_url, $page['id'] ), array( 'http', 'https' ) ) . '" alt="' . ( isset( $page['name'] ) ? esc_attr( $page['name'] ) : '' ) . '" width="15" height="15" /> ' . esc_html( __( 'Error: Page doesn\'t allow posts from other Facebook users. Full error:', 'facebook' ) ) . ' ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
				} else {
					$status_messages[] = array( 'message' => __( 'Failed posting to mentioned page\'s Facebook Timeline.', 'facebook' ) . ' <img src="' . esc_url( sprintf( $photo_url, $page['id'] ), array( 'http', 'https' ) ) . '" alt="' . ( isset( $page['name'] ) ? esc_attr( $page['name'] ) : '' ) . '" width="15" height="15" /> ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
				}
			}
		}

		if ( ! empty( $publish_ids_pages ) )
			update_post_meta( $post_id, 'fb_mentioned_pages_post_ids', $publish_ids_pages );

		if ( ! empty( $publish_ids_pages ) )
			$status_messages[] = array( 'message' => esc_html( __( 'Posted to mentioned pages\' Facebook Timelines.', 'facebook' ) ) . ' ' . implode( ' ' , $pages_posts ), 'error' => false );

		return $status_messages;
	}

	/**
	 * Post to the post author's Facebook timeline
	 * Note: function currently assumes the author of the post is publishing the post
	 *
	 * @since 1.1
	 * @param stdClass $post post object
	 * @return array status messages to display in admin notices
	 */
	public static function post_to_author_timeline( $post ) {
		global $facebook;

		$post_id = $post->ID;

		// check our assumptions about a valid link in place
		// fail if a piece of the filter process killed our response
		$link = apply_filters( 'facebook_rel_canonical', get_permalink( $post_id ) );
		if ( ! $link )
			return;
		$story = array( 'article' => $link );
		$message = get_post_meta( $post_id, 'fb_author_message', true );
		if ( is_string( $message ) && $message )
			$story['message'] = $message;

		try {
			//POST https://graph.facebook.com/me/news.reads?article=[article object URL]
			$publish_result = $facebook->api( '/me/news.publishes', 'POST', $story );

			if ( isset( $publish_result['id'] ) )
				update_post_meta( $post_id, 'fb_author_post_id', sanitize_text_field( $publish_result['id'] ) );
		} catch (WP_FacebookApiException $e) {
			$error_result = $e->getResult();

			//Unset the option to publish to an author's Timeline, since the likely failure is because the admin didn't set up the proper OG action and object in their App Settings
			//if it's a token issue, it's because the Author hasn't auth'd the WP site yet, so don't unset the option (since that will turn it off for all authors)
			/*if ($e->getType() != 'OAuthException') {
				$options['social_publisher']['publish_to_authors_facebook_timeline'] = false;

				update_option( 'fb_options', $options );
			}*/

			$status_messages[] = array( 'message' => esc_html( __( 'Failed posting to your Facebook Timeline.', 'facebook' ) ) . ' ' . esc_html( __( 'Error', 'facebook' ) ) . ': ' . esc_html( json_encode( $error_result['error'] ) ), 'error' => true );
		}

		if ( isset( $publish_result ) && isset( $publish_result['id'] ) ) {
			$link = '<a href="' . esc_url( 'https://www.facebook.com/' . $publish_result['id'], array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'your Facebook Timeline', 'facebook' ) ) . '</a>';
			if ( empty( $author_message ) )
				$message = sprintf( esc_html( __( 'Posted to %s', 'facebook' ) ), $link );
			else
				$message = sprintf( esc_html( __( 'Posted to %1$s with message "%2$s"', 'facebook' ) ), $link, $author_message );
			$status_messages[] = array( 'message' => $message, 'error' => false );
		}

		return $status_messages;
	}

	/**
	 * Parse the unique post id from a feed
	 *
	 * @since 1.0
	 * @param string $id feed publish identifier
	 * @return string Facebook URL
	 */
	public static function get_permalink_from_feed_publish_id( $id ) {
		preg_match_all( "/(.*?)_(.*?)$/su", $id, $ids, PREG_SET_ORDER );

		return 'https://www.facebook.com/' . $ids[0][2];
	}

	/**
	 * Add a query argument to trigger displaying admin messages on the front-end
	 *
	 * @since 1.0
	 * @param string $loc URL
	 * $return string URL with facebook_message query parameter appended
	 */
	public static function add_new_post_location( $loc ) {
		return add_query_arg( 'facebook_message', 1, $loc );
	}

	/**
	 * Output admin notices saved to post data during the Facebook publish process
	 * Triggers if our GET argument is present from redirecting the post location
	 *
	 * @since 1.1
	 */
	public static function output_post_admin_notices() {
		global $post;

		if ( empty( $_GET['facebook_message'] ) || ! isset( $post ) )
			return;

		$post_meta_key = 'facebook_status_messages';
		$messages = get_post_meta( $post->ID, $post_meta_key, true );
		if ( ! is_array( $messages ) )
			return;

		foreach ( $messages as $message ) {
			if ( ! isset( $message['message'] ) )
				continue;

			$div = '<div ';
			if ( isset( $message['error'] ) && $message['error'] )
				$div .= 'id="facebook-warning" class="error fade"';
			else
				$div .= 'class="updated fade"';
			$div .= '><p>';
			$div .= $message['message'];
			$div .= '</p></div>';
			echo $div;
			unset( $div );
		}

		// display once
		delete_post_meta( $post->ID, $post_meta_key );
	}

	/**
	 * Delete post data from Facebook when deleted in WordPress
	 *
	 * @since 1.0
	 * @param int $post_id post identifer
	 */
	public static function delete_facebook_post( $post_id ) {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		$fb_page_post_id = get_post_meta( $post_id, 'fb_fan_page_post_id', true );
		if ( $fb_page_post_id ) {
			$page_to_publish = self::get_publish_page();
			if ( isset( $page_to_publish['access_token'] ) ) {
				// act as the saved credential, not current user
				try {
					$delete_result = $facebook->api( '/' . $fb_page_post_id, 'DELETE', array( 'access_token' => $page_to_publish['access_token'], 'ref' => 'fbwpp' ) );
				} catch (WP_FacebookApiException $e) {}
			}
			unset( $page_to_publish );
		}
		unset( $fb_page_post_id );

		// no use proceeding if the current user has no Facebook credentials
		if ( self::user_can_publish_to_facebook() ) {
			$current_user = wp_get_current_user();
			$post = get_post( $post_id );
			if ( isset( $post->post_author ) && $post->post_author == $current_user->ID ) {
				$fb_author_post_id = get_post_meta( $post_id, 'fb_author_post_id', true );
				if ( $fb_author_post_id ) {
					try {
						$delete_result = $facebook->api( '/' . $fb_author_post_id, 'DELETE', array( 'ref' => 'fbwpp' ) );
					} catch (WP_FacebookApiException $e) {}
				}
				unset( $fb_author_post_id );

				$fb_mentioned_pages_post_ids = get_post_meta( $post_id, 'fb_mentioned_pages_post_ids', true );
				if ( $fb_mentioned_pages_post_ids ) {
					foreach($fb_mentioned_pages_post_ids as $page_post_id) {
						try {
							$delete_result = $facebook->api( '/' . $page_post_id, 'DELETE', array( 'ref' => 'fbwpp' ) );
						} catch (WP_FacebookApiException $e) {}
					}
				}
				unset( $fb_mentioned_pages_post_ids );

				$fb_mentioned_friends_post_ids = get_post_meta( $post_id, 'fb_mentioned_friends_post_ids', true );
				if ( $fb_mentioned_friends_post_ids ) {
					foreach($fb_mentioned_friends_post_ids as $page_post_id) {
						try {
							$delete_result = $facebook->api( '/' . $page_post_id, 'DELETE', array( 'ref' => 'fbwpp' ) );
						} catch (WP_FacebookApiException $e) {}
					}
				}
				unset( $fb_mentioned_friends_post_ids );
			}
		}
	}
}

?>