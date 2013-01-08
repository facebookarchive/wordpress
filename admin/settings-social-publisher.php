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
	 * Define the option name for mentions
	 *
	 * @since 1.1
	 * @var string
	 */
	const MENTIONS_OPTION_NAME = 'facebook_mentions';

	/**
	 * Option name for target Facebook page
	 *
	 * @since 1.1
	 * @var string
	 */
	const OPTION_PUBLISH_TO_PAGE = 'facebook_publish_page';

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
			register_setting( $hook_suffix, self::MENTIONS_OPTION_NAME, array( 'Facebook_Social_Publisher_Settings', 'sanitize_mentions_options' ) );
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
		global $facebook;

		$mentions_options = get_option( self::MENTIONS_OPTION_NAME );
		if ( ! is_array( $mentions_options ) )
			$mentions_options = array();
		$this->mentions_options = $mentions_options;

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

		$section = 'facebook-publish';
		add_settings_section(
			$section,
			__( 'Publish to Facebook', 'facebook' ), // no title for main section
			array( &$this, 'section_header_publish' ),
			$this->hook_suffix
		);

		if ( current_user_can( 'edit_posts' ) && $this->user_associated_with_facebook_account ) {
			add_settings_field(
				'facebook-publish-author',
				__( 'Publish to my timeline', 'facebook' ),
				array( &$this, 'display_publish_author' ),
				$this->hook_suffix,
				$section
			);
		}
		add_settings_field(
			'facebook-publish-page',
			__( 'Publish to a page', 'facebook' ),
			array( &$this, 'display_publish_page' ),
			$this->hook_suffix,
			$section
		);

		// when and where to show mentions
		$section = 'facebook-mentions';
		add_settings_section(
			$section,
			__( 'Mentions', 'facebook' ),
			array( &$this, 'section_header_mentions' ),
			$this->hook_suffix
		);
		add_settings_field(
			'facebook-mentions-show-on',
			__( 'Show on', 'facebook' ),
			array( &$this, 'display_mentions_show_on' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-mentions-position',
			__( 'Position', 'facebook' ),
			array( &$this, 'display_mentions_position' ),
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
	public function section_header_publish() {
		global $facebook_loader;

		$app_id = '';
		if ( isset( $facebook_loader ) && isset( $facebook_loader->credentials ) && isset( $facebook_loader->credentials['app_id'] ) )
			$app_id = $facebook_loader->credentials['app_id'];

		$yay = ' <span style="font-style:bold;color:green">&#10003;</span>';
		$boo = ' <span style="font-style:bold;color:red">X</span>';

		echo '<p>' . esc_html( __( 'Promote social engagement and readership by publishing new posts to the Facebook timeline of an authenticated author or page.', 'facebook' ) ) . '</p>';

		echo '<p>' . esc_html( __( 'Prerequisites', 'facebook' ) ) . ': </p>';

		echo '<ol>';

		echo '<li>' . esc_html( sprintf( __( 'A %1$s associated with %2$s','facebook' ), __( 'Facebook application identifier', 'facebook' ), get_bloginfo( 'name' ) ) );
		if ( $app_id )
			echo $yay;
		else
			echo $boo;
		echo '</li>';

		echo '<li>';
		$og_action = esc_html( __( 'Associate an Open Graph action-object pair for your application:', 'facebook' ) ) . ' ' . sprintf( esc_html( __( 'people can %1$s an %2$s', 'facebook' ) ), '<strong>publish</strong>', '<strong>article</strong>' );
		if ( $app_id )
			echo '<a href="' . esc_url( 'https://developers.facebook.com/apps/' . $app_id . '/opengraph/getting-started/', array( 'http', 'https' ) ) . '">' . $og_action . '</a>';
		else
			echo $og_action;
		echo '</li>';

		echo '<li>';
		echo esc_html( __( 'Authenticate with Facebook to allow your Facebook application to post to your timeline or page on your behalf when a post is published.', 'facebook' ) );
		if ( $this->user_associated_with_facebook_account )
			echo $yay;
		else
			echo $boo;
		echo '</li>';

		echo '</ol>';

		if ( ! $this->user_associated_with_facebook_account ) {
			// connect your account
			echo '<p>' . sprintf( esc_html( __( '%s to publish new posts to your personal or page Facebook timeline.', 'facebook' ) ), '<span class="facebook-login" data-scope="page" style="font-weight:bold">' . esc_html( __( 'Associate your WordPress account with a Facebook profile', 'facebook' ) ) . '</span>' ) . '</p>';
		} else if ( ! ( isset( $this->user_permissions ) && isset( $this->user_permissions['publish_stream'] ) && isset( $this->user_permissions['publish_actions'] ) ) ) {
			// grant additional permissions needed to complete the task
			echo '<p>' . sprintf( esc_html( __( '%s to publish new posts to your personal or page Facebook timeline.', 'facebook' ) ), '<span class="facebook-login" data-scope="page" style="font-weight:bold">' . esc_html( __( 'Grant application permissions', 'facebook' ) ) . '</span>' ) . '</p>';
		}
	}

	/**
	 * Get a list of publishable Facebook pages for the currently authenticated Facebook account
	 *
	 * @since 1.1
	 * @return array associative array of id, name, and access token for pages with create content permissions
	 */
	public static function get_publishable_pages_for_current_user() {
		global $facebook;

		if ( ! isset( $facebook ) )
			return array();

		try {
			$accounts = $facebook->api( '/me/accounts', 'GET', array( 'ref' => 'fbwpp' ) );
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
			if ( is_array( $account['perms'] ) && in_array( 'CREATE_CONTENT', $account['perms'], true ) ) {
				$pages[] = array(
					'id' => $account['id'],
					'name' => $account['name'],
					'access_token' => $account['access_token']
				);
			}
		}

		return $pages;
	}

	/**
	 * Publish new posts to your Facebook timeline
	 *
	 * @since 1.1
	 */
	public function display_publish_author() {
		if ( isset( $this->user_permissions ) && isset( $this->user_permissions['publish_stream'] ) && isset( $this->user_permissions['publish_actions'] ) ) {
			echo '<label><input type="checkbox" name="' . self::PUBLISH_OPTION_NAME . '[author_timeline]" value="1"';
			echo checked( Facebook_User::get_user_meta( $this->current_user->ID, 'facebook_timeline_disabled', true ), '' );
			echo ' /> ';
			echo esc_html( __( 'Post an article to my Facebook Timeline after it is public.', 'facebook' ) );
			echo '</label>';
		} else {
			echo '<p><span class="facebook-login" data-scope="person" style="font-weight:bold">' . esc_html( __( 'Allow new posts to your Facebook Timeline', 'facebook' ) ) . '</span></p>';
		}
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
			echo '<p>' . sprintf( esc_html( __( 'Publishing to %s', 'facebook' ) ), '<a href="' . esc_url( 'https://www.facebook.com/' . $existing_page['id'], array( 'http', 'https' ) ) . '" title="' . esc_attr( sprintf( __( '%s page on Facebook', 'facebook' ), $existing_page['name'] ) ) . '">' . esc_html( $existing_page['name'] ) . '</a>' );
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
	 * Introduce the mentions section
	 *
	 * @since 1.1
	 */
	public function section_header_mentions() {
		echo '<p>' . esc_html( __( 'Mention Facebook profiles and pages alongside a post.', 'facebook' ) ) . '</p>';
	}

	/**
	 * Where should the button appear?
	 *
	 * @since 1.1
	 * @param array $extra_attributes custom form attributes
	 */
	public function display_mentions_show_on() {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-social-plugin.php' );

		echo '<fieldset id="facebook-mentions-show-on">' . Facebook_Social_Plugin_Settings::show_on_choices( self::MENTIONS_OPTION_NAME . '[show_on]', Facebook_Social_Plugin_Settings::get_display_conditionals_by_feature( 'mentions', 'all' ), 'all' ) . '</fieldset>';
		echo '<p>' . esc_html( Facebook_Social_Plugin_Settings::show_on_description( __( 'Social Mentions', 'facebook' ) ) ) . '</p>';
	}

	/**
	 * Where would you like it?
	 *
	 * @since 1.1
	 * @param array $extra_attributes custom form attributes
	 */
	public function display_mentions_position() {
		$key = 'position';

		if ( ! class_exists( 'Facebook_Social_Plugin_Button_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-social-plugin-button.php' );

		echo '<select name="' . self::MENTIONS_OPTION_NAME . '[' . $key . ']">' . Facebook_Social_Plugin_Button_Settings::position_choices( isset( $this->mentions_options[$key] ) ? $this->mentions_options[$key] : '' ) . '</select>';
	}

	/**
	 * Display inline help for publisher functionality
	 *
	 * @since 1.1.11
	 * @return string HTML
	 */
	public static function help_tab_publisher() {
		$content = '<p>' . esc_html( __( 'The Facebook plugin for WordPress can publish to Facebook on your behalf through a properly configured Facebook application when a post becomes public.', 'facebook' ) ) . ' ' . esc_html( __( 'An author must grant your application permission to publish to his or her Facebook Timeline before the post will appear.', 'facebook' ) ) . ' ' . esc_html( __( 'A Facebook account with the ability to create content on one or more Facebook Pages may store publishing permissions for your WordPress site.', 'facebook' ) ) . '</p>';

		$content .= '<p>' . esc_html( sprintf( __( 'You must associate an Open Graph action-object pair for your Facebook application and submit the action to Facebook for approval before articles from %s will appear in Facebook News Feed.', 'facebook' ), get_bloginfo('name') ) ) . ' ' . esc_html( __( "The Facebook plugin for WordPress cannot programmatically verify your application's Open Graph approval status: the second item on the displayed prerequisites list will not display a checkmark.", 'facebook' ) ) . '</p>';

		return $content;
	}

	/**
	 * Display inline help for mentions functionality
	 *
	 * @since 1.1.11
	 * @return string HTML
	 */
	public static function help_tab_mentions() {
		return '<p>' . esc_html( __( 'Sites may enable Facebook mentions functionality for one or more post types.', 'facebook' ) ) . ' ' . esc_html( __( 'Authors may tag Facebook friends associated with the post.', 'facebook' ) ) . ' ' . esc_html( __( 'Authors or editors may tag a Facebook Page associated with the post.', 'facebook' ) ) . ' ' . esc_html( __( 'A linked profile image and name will appear alongside your post for each mentioned Facebook friend or Facebook Page.', 'facebook' ) ) . '</p>';
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

		$screen->add_help_tab( array(
			'id' => 'facebook-mentions-help',
			'title' => _x( 'Mentions', 'mentions tagging', 'facebook' ),
			'content' => self::help_tab_mentions()
		) );

		$screen->set_help_sidebar( '<p><a href="https://developers.facebook.com/apps/">' . esc_html( __( 'Facebook Apps Tool', 'facebook' ) ) . '</a></p>' );
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
		global $facebook;

		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );

		// publish to timeline is really a per-user setting, not a per-site setting
		// handle the special user case
		$user_meta_key = 'facebook_timeline_disabled';
		$current_user = wp_get_current_user();
		if ( isset( $options['author_timeline'] ) && $options['author_timeline'] == 1 )
			Facebook_User::delete_user_meta( $current_user->ID, $user_meta_key ); // delete if stored
		else
			Facebook_User::update_user_meta( $current_user->ID, $user_meta_key, '1' );

		// is a new page chosen?
		// if the same page selected on new_page_timeline as currently stored don't overwrite the access token
		// it is possible multiple users can create content from the page but should not overwrite each other when editing the page without changing the target page
		if ( isset( $options['new_page_timeline'] ) ) {
			$page_id = trim( $options['new_page_timeline'] );
			if ( ! $page_id && isset( $options['page_timeline']['id'] ) ) {
				delete_option( self::OPTION_PUBLISH_TO_PAGE );
			} else if ( $page_id && ! ( isset( $options['page_timeline']['id'] ) && $options['page_timeline']['id'] == $options['new_page_timeline'] ) ) {
				$pages_for_current_user = self::get_publishable_pages_for_current_user();
				foreach ( $pages_for_current_user as $page ) {
					if ( isset( $page['id'] ) && $page['id'] === $page_id ) {
						self::update_publish_to_page( $page );
						break;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Process changes to mentions options
	 *
	 * @since 1.1
	 * @param array $options form options
	 */
	public static function sanitize_mentions_options( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		if ( isset( $options['show_on'] ) || isset( $options['position'] ) ) {
			if ( ! class_exists( 'Facebook_Social_Plugin_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-social-plugin-button.php' );

			$options = Facebook_Social_Plugin_Button_Settings::sanitize_options( $options );
			if ( isset( $options['show_on'] ) ) {
				Facebook_Social_Plugin_Button_Settings::update_display_conditionals( 'mentions', $options['show_on'], Facebook_Social_Plugin_Button_Settings::get_show_on_choices( 'all' ) );
				unset( $options['show_on'] );
			}

			// limit what is stored to our whitelist of properties
			if ( isset( $options['position'] ) )
				return array( 'position' => $options['position'] );
		}

		return array();

	}
}
?>