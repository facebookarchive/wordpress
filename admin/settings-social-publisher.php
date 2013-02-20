<?php
/**
 * Associate Facebook friends and pages with a post
 *
 * @since 1.1
 */
class Facebook_Social_Publisher_Settings {
	/**
	 * Setting page identifier
	 *
	 * @since 1.1
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-social-publisher';

	/**
	 * Define the option name used to process the form
	 *
	 * @since 1.1
	 * @var string
	 */
	const PUBLISH_OPTION_NAME = 'facebook_publish';

	/**
	 * Option name for target Facebook page
	 *
	 * @since 1.1
	 * @var string
	 */
	const OPTION_PUBLISH_TO_PAGE = 'facebook_publish_page';

	/**
	 * Option name for advanced Facebook Open Graph action functionality
	 *
	 * @since 1.2.4
	 * @var string
	 */
	const OPTION_OG_ACTION = 'facebook_og_action';

	/**
	 * Reference the social plugin by name
	 *
	 * @since 1.1
	 * @return string social plugin name
	 */
	public static function social_plugin_name() {
		return __( 'Social Publisher', 'facebook' );
	}

	/**
	 * Navigate to the settings page through the Facebook top-level menu item
	 *
	 * @since 1.1
	 * @uses add_submenu_page()
	 * @param string $parent_slug Facebook top-level menu item slug
	 * @return string submenu hook suffix
	 */
	public static function add_submenu_item( $parent_slug ) {
		$social_publisher_settings = new Facebook_Social_Publisher_Settings();

		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( &$social_publisher_settings, 'settings_page' )
		);

		if ( $hook_suffix ) {
			$social_publisher_settings->hook_suffix = $hook_suffix;
			register_setting( $hook_suffix, self::PUBLISH_OPTION_NAME, array( 'Facebook_Social_Publisher_Settings', 'sanitize_publish_options' ) );
			add_action( 'load-' . $hook_suffix, array( &$social_publisher_settings, 'onload' ) );
		}

		return $hook_suffix;
	}

	/**
	 * Load extra assets early in the page build process to tap into proper hooks
	 *
	 * @since 1.1
	 */
	public function onload() {
		global $facebook, $facebook_loader;

		// prompt to log in or update account info
		if ( ! class_exists( 'Facebook_Admin_Login' ) )
			require_once( dirname(__FILE__) . '/login.php' );
		Facebook_Admin_Login::connect_facebook_account( array( 'manage_pages', 'publish_actions', 'publish_stream' ) );

		// prep user-specific functionality and comparisons
		$this->current_user = wp_get_current_user();
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );

		// does the current user have associated Facebook account data stored in WordPress?
		$facebook_user_data = Facebook_User::get_user_meta( $this->current_user->ID, 'fb_data', true );
		if ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) {
			$this->user_associated_with_facebook_account = true;
			if ( isset( $facebook ) || ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
				$this->user_permissions = $facebook->get_current_user_permissions( $this->current_user );
			if ( ! is_array( $this->user_permissions ) )
				$this->user_permissions = array();
		} else {
			$this->user_associated_with_facebook_account = false;
			$this->user_permissions = array();
		}

		$this->settings_api_init();
	}

	/**
	 * Load the page
	 *
	 * @since 1.1
	 */
	public function settings_page() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		Facebook_Settings::settings_page_template( $this->hook_suffix, sprintf( __( '%s Settings', 'facebook' ), self::social_plugin_name() ) );
	}

	/**
	 * Hook into the settings API
	 *
	 * @since 1.1
	 * @uses add_settings_section()
	 * @uses add_settings_field()
	 * @param string $page parent page slug
	 */
	private function settings_api_init() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		// Timeline customizations
		$section = 'facebook-publish-advanced';
		add_settings_section(
			$section,
			__( 'Facebook Timeline', 'facebook' ),
			array( &$this, 'section_timeline_publish' ),
			$this->hook_suffix
		);
		add_settings_field(
			'facebook-author-profile',
			__( 'Facebook permissions', 'facebook' ),
			array( 'Facebook_Social_Publisher_Settings', 'display_facebook_author' ),
			$this->hook_suffix
		);
		add_settings_field(
			'facebook-publish-author',
			__( 'Publish to my Timeline', 'facebook' ),
			array( &$this, 'display_publish_author' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-og-action',
			__( 'Enable Open Graph features', 'facebook' ),
			array( &$this, 'display_og_action' ),
			$this->hook_suffix,
			$section
		);

		$section = 'facebook-publish-page-section';
		add_settings_section(
			$section,
			__( 'Facebook Page', 'facebook' ),
			array( &$this, 'section_page_publish' ),
			$this->hook_suffix
		);
		add_settings_field(
			'facebook-publish-page',
			__( 'Publish to a page', 'facebook' ),
			array( &$this, 'display_publish_page' ),
			$this->hook_suffix,
			$section
		);

		self::inline_help_content();
	}

	/**
	 * Introduce the publish to Facebook feature
	 *
	 * @since 1.1
	 */
	public function section_timeline_publish() {
		global $facebook_loader;

		$app_id = '';
		if ( isset( $facebook_loader ) && isset( $facebook_loader->credentials ) && isset( $facebook_loader->credentials['app_id'] ) )
			$app_id = $facebook_loader->credentials['app_id'];

		$yay = ' <span style="font-style:bold;color:green">&#10003;</span>';
		$boo = ' <span style="font-style:bold;color:red">X</span>';

		echo '<p>' . esc_html( __( 'Promote social engagement and readership by publishing new posts to the Facebook Timeline of a connected author.', 'facebook' ) ) . '</p>';

		echo '<p>' . sprintf( esc_html( __( 'Open Graph %s', 'facebook' ) ), '<a href="' . esc_url( 'https://developers.facebook.com/wordpress/#author-og-setup', array( 'http', 'https' ) ) . '" target="_blank" title="' . esc_attr( __( 'Facebook Open Graph action submission process', 'facebook' ) ) . '">' . esc_html( __( 'prerequisites', 'facebook' ) ) . '</a>' ) . ': </p>';

		echo '<ol>';

		echo '<li>' . esc_html( sprintf( __( 'A %1$s associated with %2$s','facebook' ), __( 'Facebook application identifier', 'facebook' ), get_bloginfo( 'name' ) ) );
		if ( $app_id )
			echo $yay;
		else
			echo $boo;
		echo '</li>';

		echo '<li>';
		$og_action = esc_html( __( 'Associate an Open Graph action-object pair for your application:', 'facebook' ) ) . ' ' . sprintf( esc_html( _x( 'people can %1$s an %2$s', 'Open Graph. people can ACTION an OBJECT', 'facebook' ) ), '<strong>publish</strong>', '<strong>article</strong>' );
		if ( $app_id )
			echo '<a href="' . esc_url( 'https://developers.facebook.com/apps/' . $app_id . '/opengraph/getting-started/', array( 'http', 'https' ) ) . '" target="_blank">' . $og_action . '</a>';
		else
			echo $og_action;
		echo '</li>';
		unset( $og_action );

		echo '<li>';
		echo esc_html( __( 'Authenticate with Facebook to allow your Facebook application to post to your Timeline or Page on your behalf when a post is published', 'facebook' ) );
		if ( $this->user_associated_with_facebook_account )
			echo $yay;
		else
			echo $boo;
		echo '</li>';

		echo '<li>' . esc_html( __( 'Publish an article to your Facebook Timeline', 'facebook' ) ) . '</li>';

		$og_action_text = esc_html( __( 'your Publish action', 'facebook' ) );
		echo '<li>' . sprintf( esc_html( __( 'Submit %s for approval.', 'facebook' ) ), $app_id ? '<a href="' . esc_url( 'https://developers.facebook.com/apps/' . $app_id . '/opengraph/action_type/331247406956072', array( 'http', 'https' ) ) . '" target="_blank">' . $og_action_text . '</a>' : $og_action_text );
		echo ' ' . esc_html( __( 'Request optional capabilities:', 'facebook' ) ) . ' <a href="' . esc_url( 'https://developers.facebook.com/docs/submission-process/opengraph/guidelines/action-properties/#usermessages', array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'User Messages', 'facebook' ) ) . '</a>, <a href="' . esc_url( 'https://developers.facebook.com/docs/submission-process/opengraph/guidelines/action-properties/#mentiontagging', array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'Tags', 'facebook' ) ) . '</a>, <a href="' . esc_url( 'https://developers.facebook.com/docs/submission-process/opengraph/guidelines/action-properties/#explicitlyshared', array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'Explicitly Shared', 'facebook' ) ) . '</a></li>';
		unset( $og_action_text );

		echo '</ol>';

		if ( ! $this->user_associated_with_facebook_account ) {
			// connect your account
			echo '<p>' . sprintf( esc_html( __( '%s to publish new posts to your Facebook Timeline or Page.', 'facebook' ) ), '<span class="facebook-login" data-scope="page" style="font-weight:bold">' . esc_html( __( 'Associate your WordPress account with a Facebook account', 'facebook' ) ) . '</span>' ) . '</p>';
		} else if ( ! ( isset( $this->user_permissions ) && isset( $this->user_permissions['publish_stream'] ) && isset( $this->user_permissions['publish_actions'] ) ) ) {
			// grant additional permissions needed to complete the task
			echo '<p>' . sprintf( esc_html( __( '%s to publish new posts to your Facebook Timeline or Page.', 'facebook' ) ), '<span class="facebook-login" data-scope="page" style="font-weight:bold">' . esc_html( __( 'Grant application permissions', 'facebook' ) ) . '</span>' ) . '</p>';
		}
	}

	/**
	 * Describe publish to Facebook Page functionality
	 *
	 * @since 1.2.4
	 */
	public function section_page_publish() {
		echo '<p>' . sprintf( esc_html( __( 'Publish to a Facebook Page using the credentials of a Facebook account with %s permissions for the Page.', 'facebook' ) ), '<a href="' . esc_html( 'https://www.facebook.com/help/289207354498410/', array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'content creator', 'facebook' ) ) . '</a>' ) . '</p>';
	}

	/**
	 * Get a list of publishable Facebook pages for the currently authenticated Facebook account
	 *
	 * @since 1.1
	 * @return array associative array of id, name, and access token for pages with create content permissions
	 */
	public static function get_publishable_pages_for_current_user() {
		global $facebook, $facebook_loader;

		if ( ! isset( $facebook ) && ! ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
			return array();

		try {
			$accounts = $facebook->api( '/me/accounts', 'GET', array( 'fields' => 'id,name,category,access_token,perms', 'ref' => 'fbwpp' ) );
		} catch (WP_FacebookApiException $e) {}
		if ( ! ( isset( $accounts ) && is_array( $accounts['data'] ) ) )
			return array();

		$accounts = $accounts['data'];

		$pages = array();
		foreach ( $accounts as $account ) {
			// pages only
			if ( isset( $account['category'] ) && $account['category'] === 'Application' )
				continue;

			// can the authenticated user create new content on the page?
			if ( is_array( $account['perms'] ) && in_array( 'CREATE_CONTENT', $account['perms'], true ) && ! empty( $account['id'] ) && ! empty( $account['name'] ) && ! empty( $account['access_token'] ) ) {
				$pages[ $account['name'] ] = array(
					'id' => $account['id'],
					'name' => $account['name'],
					'access_token' => $account['access_token']
				);
			}
		}
		if ( ! empty( $pages ) ) {
			ksort( $pages, SORT_LOCALE_STRING );
			$pages = array_values( $pages );
		}

		return $pages;
	}

	/**
	 * Publish new posts to your Facebook timeline
	 *
	 * @since 1.1
	 */
	public static function display_publish_author() {
		echo '<p>' . sprintf( esc_html( __( 'An author can associate his or her WordPress account with a Facebook account on his or her %s', 'facebook' ) ), '<a href="' . esc_url( self_admin_url( 'profile.php' ), array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'profile page', 'facebook' ) ) . '</a>' ) . '</p>';
	}

	/**
	 * Publish new posts to your Facebook page
	 *
	 * @since 1.1
	 */
	public function display_publish_page() {
		$key = 'page_timeline';
		$existing_page = get_option( self::OPTION_PUBLISH_TO_PAGE );
		if ( is_array( $existing_page ) && isset( $existing_page['id'] ) && isset( $existing_page['name'] ) && isset( $existing_page['access_token'] ) ) {
			$page_id = $existing_page['id'];
		} else {
			$page_id = '';
		}

		if ( $page_id ) {
			echo '<input type="hidden" name="' . self::PUBLISH_OPTION_NAME . '[' . $key . '][id]" value="' . esc_attr( $existing_page['id'] ) . '" />';
			echo '<input type="hidden" name="' . self::PUBLISH_OPTION_NAME . '[' . $key . '][name]" value="' . esc_attr( $existing_page['name'] ) . '" />';
			echo '<input type="hidden" name="' . self::PUBLISH_OPTION_NAME . '[' . $key . '][access_token]" value="' . esc_attr( $existing_page['access_token'] ) . '" />';
			echo '<p>' . sprintf( esc_html( __( 'Publishing to %s', 'facebook' ) ), '<a href="' . esc_url( 'https://www.facebook.com/' . $existing_page['id'], array( 'http', 'https' ) ) . '" title="' . esc_attr( sprintf( __( '%s page on Facebook', 'facebook' ), $existing_page['name'] ) ) . '" target="_blank">' . esc_html( $existing_page['name'] ) . '</a>' );
			if ( is_multi_author() && isset( $existing_page['set_by_user'] ) ) {
				if ( $this->current_user->ID == $existing_page['set_by_user'] ) {
					echo '. ' . esc_html( __( 'Saved by you.', 'facebook' ) );
				} else {
					$setter = get_userdata( $existing_page['set_by_user'] );
					if ( $setter ) {
						echo '. ' . esc_html( sprintf( _x( 'Saved by %s.', 'saved by person name', 'facebook' ), $setter->display_name ) );
					}
				}
			}
			echo '</p>';
		} else if ( ! $this->user_associated_with_facebook_account ) {
			echo '<p>' . sprintf( esc_html( __( '%s to get started.', '' ) ), '<a href="#facebook-login">' . esc_html( __( 'Connect your Facebook account', 'facebook' ) ) . '</a>') . '</p>';
		}

		if ( $this->user_associated_with_facebook_account ) {
			// does the current user have the ability to change the page?
			if ( isset( $this->user_permissions['manage_pages'] ) ) {
				$pages = self::get_publishable_pages_for_current_user();
				if ( ! empty( $pages ) ) {
					echo '<select name="' . self::PUBLISH_OPTION_NAME . '[new_' . $key . ']' . '" id="publish-to-page"><option value=""' . selected( $page_id, '', false ) . '>';
					if ( $page_id )
						echo esc_html( sprintf( __( 'None: remove %s', 'facebook' ), $existing_page['name'] ) );
					else
						echo ' ';
					echo '</option>';
					foreach ( $pages as $page ) {
						echo '<option value="' . esc_attr( $page['id'] ) . '"' . selected( $page_id, $page['id'], false ) . '>' . esc_html( $page['name'] ) . '</option>';
					}
					echo '</select>';
				}
			} else {
				// request manage_pages permission
				echo '<p><span class="facebook-login" data-scope="page" style="font-weight:bold">' . esc_html( __( 'Allow new posts to a Facebook Page', 'facebook' ) ) . '</span></p>';
			}
		}
	}

	/**
	 * Display an option for the publisher to enable advanced Open Graph action functionality
	 *
	 * @since 1.2.4
	 */
	public function display_og_action() {
		$id = 'og-action';
		echo '<div><input type="checkbox" class="checkbox" name="' . self::PUBLISH_OPTION_NAME . '[og_action]" id="' . $id . '" value="1"';
		checked( get_option( self::OPTION_OG_ACTION ) );
		echo ' /> <label for="' . $id . '">' . esc_html( __( 'Post to Facebook Timeline using Open Graph actions', 'facebook' ) ) . '</label></div>';
		echo '<p class="description">' . esc_html( __( 'Publish new posts to Facebook using Open Graph actions.', 'facebook' ) ) . ' ' . esc_html( __( 'Increases News Feed engagement through news-specific classification, explicitly shared posts, custom messages, and mention tagging.', 'facebook' ) ) . '</p>';
	}

	/**
	 * Display inline help for publisher functionality
	 *
	 * @since 1.1.11
	 * @return string HTML
	 */
	public static function help_tab_publisher() {
		$content = '<p>' . esc_html( __( 'The Facebook plugin for WordPress can publish to Facebook on your behalf through a properly configured Facebook application when a public post type becomes public.', 'facebook' ) ) . ' ' . esc_html( __( 'An author must grant your application permission to publish to his or her Facebook Timeline before the post will appear.', 'facebook' ) ) . ' ' . esc_html( __( 'A Facebook account with the ability to create content on one or more Facebook Pages may store publishing permissions for use by your WordPress site.', 'facebook' ) ) . '</p>';

		$content .= '<p>' . esc_html( sprintf( __( 'You must associate an Open Graph action-object pair for your Facebook application and submit the action to Facebook for approval before articles from %s and its authors will appear in Facebook News Feed.', 'facebook' ), get_bloginfo('name') ) ) . ' ' . esc_html( __( "The Facebook plugin for WordPress cannot programmatically verify your application's Open Graph approval status..", 'facebook' ) ) . '</p>';

		return $content;
	}

	/**
	 * Display help content on the settings page
	 *
	 * @since 1.1
	 */
	public static function inline_help_content() {
		$screen = get_current_screen();
		if ( ! $screen ) // null if global not set
			return;

		$screen->add_help_tab( array(
			'id' => 'facebook-publish-help',
			'title' => __( 'Publish to Facebook', 'facebook' ),
			'content' => self::help_tab_publisher()
		) );

		$screen->set_help_sidebar( '<p><a href="' . esc_url( 'https://developers.facebook.com/apps/', array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'Facebook Apps Tool', 'facebook' ) ) . '</a></p><p><a href="' . esc_url( 'https://developers.facebook.com/wordpress', array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( __( 'Plugin help page', 'facebook' ) ) . '</a></p>' );
	}

	/**
	 * Update the Facebook page information stored for the site
	 *
	 * @since 1.1
	 * @uses update_option()
	 * @param array $page_data data returned from Facebook Graph API permissions call
	 */
	public static function update_publish_to_page( $page_data ) {
		if ( ! ( is_array( $page_data ) && ! empty( $page_data ) && isset( $page_data['id'] ) && isset( $page_data['access_token'] ) && isset( $page_data['name'] ) ) )
			return;

		$current_user_id = get_current_user_id();
		if ( ! $current_user_id )
			return;

		update_option( self::OPTION_PUBLISH_TO_PAGE, array(
			'id' => $page_data['id'],
			'name' => $page_data['name'],
			'access_token' => $page_data['access_token'],
			'set_by_user' => $current_user_id
		) );
	}

	/**
	 * Set the appropriate settings for each form component
	 *
	 * @since 1.1
	 * @param array $options social publisher options
	 * @return array clean option sets.
	 */
	public static function sanitize_publish_options( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		$og_action_field = 'og_action';
		if ( isset( $options[ $og_action_field ] ) && $options[ $og_action_field ] == '1' ) {
			update_option( self::OPTION_OG_ACTION, '1' );
		} else {
			delete_option( self::OPTION_OG_ACTION );
		}
		unset( $og_action_field );

		// is a new page chosen?
		// if the same page selected on new_page_timeline as currently stored don't overwrite the access token
		// it is possible multiple users can create content from the page but should not overwrite each other when editing the page without changing the target page
		$page_field = 'page_timeline';
		$new_page_field = 'new_' . $page_field;
		if ( isset( $options[ $new_page_field ] ) ) {
			$page_id = trim( $options[ $new_page_field ] );
			if ( ! $page_id && isset( $options[ $page_field ]['id'] ) ) {
				delete_option( self::OPTION_PUBLISH_TO_PAGE );
			} else if ( $page_id && ! ( isset( $options[ $page_field ]['id'] ) && $options[ $page_field ]['id'] == $options[ $new_page_field ] ) ) {
				$pages_for_current_user = self::get_publishable_pages_for_current_user();
				foreach ( $pages_for_current_user as $page ) {
					if ( isset( $page['id'] ) && $page['id'] === $page_id ) {
						self::update_publish_to_page( $page );
						break;
					}
				}
			}
		}
		unset( $page_field );
		unset( $new_page_field );

		return false;
	}
}
?>