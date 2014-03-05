<?php

/**
 * Summarize the plugin configuration on a single page
 *
 * @since 1.1.6
 */
class Facebook_Settings_Debugger {

	/**
	 * Page identifier.
	 *
	 * @since 1.1.6
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-debug';

	/**
	 * HTML span noting a feature exists.
	 *
	 * @since 1.1.6
	 *
	 * @var string
	 */
	const EXISTS = '<span class="feature-present">&#10003;</span>';

	/**
	 * HTML span noting a feature does not exist.
	 *
	 * @since 1.1.6
	 *
	 * @var string
	 */
	const DOES_NOT_EXIST = '<span class="feature-not-present">X</span>';

	/**
	 * Reference the social plugin by name.
	 *
	 * @since 1.1.6
	 *
	 * @return string social plugin name
	 */
	public static function social_plugin_name() {
		return __( 'Debugger', 'facebook' );
	}

	/**
	 * Navigate to the debugger page through the Facebook top-level menu item.
	 *
	 * @since 1.1.6
	 *
	 * @uses add_submenu_page()
	 * @param string $parent_slug Facebook top-level menu item slug
	 * @return string submenu hook suffix
	 */
	public static function add_submenu_item( $parent_slug ) {
		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( 'Facebook_Settings_Debugger', 'content' )
		);

		if ( $hook_suffix ) {
			add_action( 'load-' . $hook_suffix, array( 'Facebook_Settings_Debugger', 'onload' ) );
		}

		return $hook_suffix;
	}

	/**
	 * Load scripts and other setup functions on page load.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public static function onload() {
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Settings_Debugger', 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( self::PAGE_SLUG, plugins_url( 'static/css/admin/debug' . ( ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min' )  . '.css', dirname( __FILE__ ) ), array(), '1.5' );
	}

	/**
	 * Page content.
	 *
	 * @since 1.1.6
	 *
	 * @global Facebook_Loader $facebook_loader test if Facebook app access token exists
	 * @return void
	 */
	public static function content() {
		global $facebook_loader;

		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( dirname(__FILE__) . '/settings.php' );

		echo '<div class="wrap">';
		echo '<header><h2>' . esc_html( self::social_plugin_name() ) . '</h2></header>';

		// only show users if app credentials stored
		if ( $facebook_loader->app_access_token_exists() ) {
			self::app_section();
			self::users_section();
			self::post_to_page_section();
		}

		self::enabled_features_by_view_type();
		self::widgets_section();
		self::server_info();

		echo '</div>';

		Facebook_Settings::stats_beacon();
	}

	/**
	 * Get all users with edit_posts capabilities broken out into Facebook-permissioned users and non-Facebook permissioned users.
	 *
	 * @since 1.1.6
	 *
	 * @see Facebook_User::get_wordpress_users_associated_with_facebook_accounts()
	 * @return array WordPress users with and without Facebook data stored.
	 */
	public static function get_all_wordpress_facebook_users() {
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/facebook-user.php' );
		// fb => [], wp => []
		$users = Facebook_User::get_wordpress_users_associated_with_facebook_accounts();

		$users_with_app_permissions = array();

		if ( ! empty( $users['fb'] ) ) {

			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( dirname( dirname( __FILE__ ) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

			foreach ( $users['fb'] as $user ) {
				if ( ! isset( $user->fb_data['fb_uid'] ) ) {
					$users['wp'][] = $user;
					continue;
				}

				$facebook_user_permissions = Facebook_WP_Extend::get_permissions_by_facebook_user_id( $user->fb_data['fb_uid'] );
				if ( ! is_array( $facebook_user_permissions ) || ! isset( $facebook_user_permissions['installed'] ) ) {
					$users['wp'][] = $user;
					continue;
				}
				$user->fb_data['permissions'] = $facebook_user_permissions;
				unset( $facebook_user_permissions );
				$users_with_app_permissions[] = $user;
			}
		}

		$users['fb'] = $users_with_app_permissions;

		return $users;
	}

	/**
	 * URL of the Facebook app editor for a specific Facebook app id
	 *
	 * @since 1.5.3
	 *
	 * @param string Facebook application identifier
	 * @return string absolute URI of the passed Facebook app_id editor
	 */
	public static function get_app_edit_base_uri( $app_id ) {
		return 'https://developers.facebook.com/apps/' . $app_id . '/';
	}

	/**
	 * Display Facebook application settings, prompt for missing values.
	 *
	 * Help WordPress administrators troubleshoot missing minimum requirements for a Facebook app using Facebook Login and/or an approved Open Graph action.
	 *
	 * @since 1.5.3
	 *
	 * @global \Facebook_Loader $facebook_loader access Facebook app id
	 * @return void
	 */
	public static function app_section() {
		global $facebook_loader;

		if ( ! ( isset( $facebook_loader->credentials['app_id'] ) && $facebook_loader->credentials['app_id'] ) )
			return;

		echo '<section id="debug-app">';
		echo '<header><h3><a href="' . esc_url( self::get_app_edit_base_uri( $facebook_loader->credentials['app_id'] ), array('http', 'https') ) . '" target="_blank">' . esc_html( sprintf( __( 'App %s', 'facebook' ), $facebook_loader->credentials['app_id'] ) ) . '</a></h3></header>';

		self::app_editors( $facebook_loader->credentials['app_id'] );
		self::app_details( $facebook_loader->credentials['app_id'] );

		echo '</section>';
	}

	/**
	 * Mention WordPress users with manage_options capability who can also edit the Facebook app
	 *
	 * @since 1.5.3
	 *
	 * @param string $app_id Facebook application identifier
	 * @return void
	 */
	public static function app_editors( $app_id ) {
		// HTTP interface to Facebook
		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

		$app_roles = Facebook_WP_Extend::graph_api_with_app_access_token( $app_id . '/roles', 'GET', array( 'fields' => 'user,role' ) );
		if ( empty( $app_roles ) || ! isset( $app_roles['data'] ) )
			return;
		$app_roles = $app_roles['data'];

		// Facebook to WordPress user helper class
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/facebook-user.php' );
		$current_user_facebook_id = Facebook_User::get_facebook_profile_id( get_current_user_id() );
		$facebook_users_can_edit = array();
		foreach( $app_roles as $facebook_user ) {
			if ( ! ( isset( $facebook_user['user'] ) && $facebook_user['user'] && isset( $facebook_user['role'] ) && in_array( $facebook_user['role'], array( 'administrators', 'developers' ), true ) ) )
				continue;

			// confirm the current WordPress user's ability to edit Facebook app values
			if ( $current_user_facebook_id && $facebook_user['user'] == $current_user_facebook_id ) {
				echo '<p>' . __( 'You have the ability to change these application settings on Facebook.', 'facebook' ) . '</p>';
				return;
			}
			$facebook_users_can_edit[ $facebook_user['user'] ] = true;
		}
		unset( $current_user_facebook_id );
		unset( $app_roles );
		if ( empty( $facebook_users_can_edit ) )
			return;

		// fb => [], wp => []
		$facebook_users = Facebook_User::get_wordpress_users_associated_with_facebook_accounts( 'manage_options' );
		if ( empty( $facebook_users ) || ! isset( $facebook_users['fb'] ) || empty( $facebook_users['fb'] ) )
			return;
		$facebook_users = $facebook_users['fb'];

		// WordPress accounts capable of managing WordPress site options who have associated a Facebook account capable of editing the current WordPress site's Facebook app
		$wordpress_users_can_edit = array();
		foreach( $facebook_users as $facebook_user ) {
			if ( isset( $facebook_user->fb_data ) && isset( $facebook_user->fb_data['fb_uid'] ) && isset( $facebook_users_can_edit[ $facebook_user->fb_data['fb_uid'] ] ) )
				$wordpress_users_can_edit[] = $facebook_user;
		}
		unset( $facebook_users );
		if ( empty( $wordpress_users_can_edit ) )
			return;

		// display a list of people who could help edit Facebook app values
		// link to Facebook account page instead of email due to the more public nature of a Facebook account
		$wordpress_users_display = array();
		foreach( $wordpress_users_can_edit as $wordpress_user ) {
			if ( ! isset( $wordpress_user->display_name ) )
				continue;

			$facebook_profile_link = Facebook_User::facebook_profile_link( $wordpress_user->fb_data );
			if ( $facebook_profile_link )
				$wordpress_users_display[] = '<a href="' . esc_url( $facebook_profile_link, array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( $wordpress_user->display_name ) . '</a>';
			else
				$wordpress_users_display[] = esc_html( $wordpress_user->display_name );
			unset( $facebook_profile_link );
		}
		if ( empty( $wordpress_users_display ) )
			return;

		// format the display of the list of people
		$wordpress_users_display_count = count( $wordpress_users_display );
		$ask_string = '';
		if ( $wordpress_users_display_count === 1 ) {
			$ask_string = $wordpress_users_display[0];
		} else if ( $wordpress_users_display_count === 2 ) {
			$ask_string = $wordpress_users_display[0] . ' ' . esc_html( _x( 'or', 'bridge between two options: this or that or these', 'facebook' ) ) . ' ' . $wordpress_users_display[1];
		} else {
			$ask_string = ', ' . esc_html( _x( 'or', 'bridge between two options: this or that or these', 'facebook' ) ) . ' ' . array_pop( $wordpress_users_display );
			$ask_string = implode( ', ', $wordpress_users_display ) . $ask_string;
		}
		echo '<p>' . sprintf( esc_html( __( '%s can change these application settings on Facebook.', 'facebook' ) ), $ask_string ) . '</p>';
	}

	/**
	 * Display Facebook application details; suggest new values if value not set
	 *
	 * Request stored details for the site's stored Facebook application. Highlight values relevant to a proper functioning Facebook Login experience
	 *
	 * @since 1.5.3
	 *
	 * @param string $app_id Facebook application identifier
	 * @return void
	 */
	public static function app_details( $app_id ) {
		// HTTP interface to Facebook
		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

		// request application data for the app id using stored app access token
		$app_details = Facebook_WP_Extend::graph_api_with_app_access_token( $app_id, 'GET', array( 'fields' => 'name,icon_url,logo_url,app_domains,website_url,privacy_policy_url,terms_of_service_url,auth_dialog_headline,auth_dialog_perms_explanation' ) );

		if ( empty( $app_details ) )
			return;

		// link to the relevant Facebook app editor screen
		$app_edit_base_uri = self::get_app_edit_base_uri( $app_id );
		$app_details_uri = $app_edit_base_uri . 'appdetails/';
		$app_summary_uri = $app_edit_base_uri . 'summary/';

		echo '<table id="facebook-app-login-fields">';
		echo '<caption>' . esc_html( __( 'Facebook Login', 'facebook' ) ) . '</caption>';
		echo '<thead><tr><th>' . esc_html( _x( 'Setting', 'Table column header. The Facebook application setting.', 'facebook' ) ) . '</th><th>' . esc_html( _x( 'Value', 'Facebook application setting retrieved from Facebook servers.', 'facebook' ) ) . '</th></tr></thead>';
		echo '<tbody>';

		// app name
		echo '<tr><th><a href="' . esc_url( $app_details_uri . '#name', array('http', 'https') ) . '" target="_blank">' . esc_html( __( 'App name', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['name'] ) && $app_details['name'] ) {
			echo '>"' . esc_html( $app_details['name'] ) . '"';
		} else {
			echo ' class="error-message">';
			$site_name = trim( get_bloginfo( 'name' ) );
			// consider the WordPress default the same as not set
			if ( $site_name && $site_name !== __( 'My Site' ) )
				echo esc_html( sprintf( __( 'Not set. Consider using: %s', 'facebook' ), $site_name ) );
			else
				echo esc_html( _x( 'Not set.', 'No stored value found.', 'facebook' ) );
			unset( $site_name );
		}
		echo '</td></tr>';

		// app domains able to act on behalf of the application
		echo '<tr><th><a href="' . esc_url( $app_summary_uri, array('http', 'https') ) . '" target="_blank">' . esc_html( __( 'App Domains', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['app_domains'] ) && ! empty( $app_details['app_domains'] ) ) {
			echo '><ul>';
			foreach( $app_details['app_domains'] as $app_domain ) {
				echo '<li><code>' . esc_html( $app_domain ) . '</code></li>';
			}
			echo '</ul>';
		} else {
			echo ' class="error-message">';
			echo esc_html( sprintf( __( 'Not set. Consider using: %s', 'facebook' ), parse_url( admin_url(), PHP_URL_HOST ) ) );
		}
		echo '</td></tr>';

		// Website with Facebook Login
		echo '<tr><th><a href="' . esc_url( $app_summary_uri .'#site_url_input', array('http', 'https') ) . '" target="_blank">' . esc_html( __( 'Website', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['website_url'] ) && $app_details['website_url'] ) {
			echo '><a href="' . esc_url( $app_details['website_url'], array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( $app_details['website_url'] ) . '</a>';
		} else {
			echo ' class="error-message">';
			echo esc_html( sprintf( __( 'Not set. Consider using: %s', 'facebook' ), home_url( '/' ) ) );
		}
		echo '</td></tr>';

		// One-line description
		echo '<tr><th><a href="' . esc_url( $app_details_uri, array('http', 'https') ) . '" target="_blank">' . esc_html( __( 'One-line description', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['auth_dialog_headline'] ) && $app_details['auth_dialog_headline'] ) {
			echo '>"' . esc_html( $app_details['auth_dialog_headline'] ) . '"';
		} else {
			echo ' class="error-message">';
			$site_description = trim( get_bloginfo( 'description' ) );
			// do not suggest WordPress default site description
			if ( $site_description && $site_description !== __( 'Just another WordPress site' ) )
				echo esc_html( sprintf( __( 'Not set. Consider using: %s', 'facebook' ), '"' . $site_description . '"' ) );
			else
				echo esc_html( __( 'Not set.', 'facebook' ) );
			unset( $site_description );
		}
		echo '</td></tr>';

		// publish permissions explanation
		echo '<tr><th><a href="' . esc_url( $app_details_uri, array('http', 'https') ) . '" target="_blank">' . esc_html( _x( 'Publish permissions explanation', 'Explain the reason for requesting publish permissions from a Facebook user', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['auth_dialog_perms_explanation'] ) && $app_details['auth_dialog_perms_explanation'] )
			echo '>"' . esc_html( $app_details['auth_dialog_perms_explanation'] ) . '"';
		else
			echo ' class="error-message">' . esc_html( sprintf( __( 'Not set. Consider using: %s', 'facebook' ), '"' . __( 'Publish new posts to your Facebook Timeline or Page.', 'facebook' ) . '"' ) );
		echo '</td></tr>';

		// Privacy Policy
		echo '<tr><th><a href="' . esc_url( $app_details_uri . '#privacy_url', array('http', 'https') ) . '" target="_blank">' . esc_html( __( 'Privacy Policy', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['privacy_policy_url'] ) && $app_details['privacy_policy_url'] ) {
			echo '><a href="' . esc_url( $app_details['privacy_policy_url'], array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( $app_details['privacy_policy_url'] ) . '</a>';
		} else {
			echo ' class="error-message">' . esc_html( __( 'Not set.', 'facebook' ) ) . ' ' . esc_html( _x( 'Create a new page?', 'Create a new WordPress page', 'facebook' ) );
		}
		echo '</td></tr>';

		// Terms of Service
		echo '<tr><th><a href="' . esc_url( $app_details_uri . '#tos_url', array('http', 'https') ) . '" target="_blank">' . esc_html( __( 'Terms of Service', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['terms_of_service_url'] ) && $app_details['terms_of_service_url'] ) {
			$app_details['terms_of_service_url'] = esc_url( $app_details['terms_of_service_url'], array( 'http', 'https' ) );
			echo '><a href="' . esc_url( $app_details['terms_of_service_url'], array( 'http', 'https' ) ) . '" target="_blank">' . esc_html( $app_details['terms_of_service_url'] ) . '</a>';
		} else {
			echo ' class="error-message">';
			echo esc_html( __( 'Not set.', 'facebook' ) ) . ' ' . esc_html( _x( 'Create a new page?', 'Create a new WordPress page', 'facebook' ) );
		}
		echo '</td></tr>';

		// Logo
		echo '<tr><th><a href="' . esc_url( $app_details_uri, array('http', 'https') ) . '" target="_blank">' . esc_html( _x( 'Logo', 'Facebook application logo', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['logo_url'] ) && $app_details['logo_url'] ) {
			echo '><img alt="' . esc_attr( __( 'Facebook application logo', 'facebook' ) ) . '" src="' . esc_url( $app_details['logo_url'], array( 'http', 'https' ) ) . '" />';
		} else {
			echo ' class="error-message">' . esc_html( __( 'Not set.', 'facebook' ) );
		}
		echo '</td></tr>';

		// Icon
		echo '<tr><th><a href="' . esc_url( $app_details_uri, array('http', 'https') ) . '" target="_blank">' . esc_html( _x( 'Icon', 'Facebook application icon', 'facebook' ) ) . '</a></th><td';
		if ( isset( $app_details['icon_url'] ) && $app_details['icon_url'] ) {
			echo '><img alt="' . esc_attr( __( 'Facebook application icon', 'facebook' ) ) . '" src="' . esc_url( $app_details['icon_url'], array( 'http', 'https' ) ) . '" />';
		} else {
			echo ' class="error-message">' . esc_html( __( 'Not set.', 'facebook' ) );
		}
		echo '</td></tr>';

		echo '</tbody></table>';
	}

	/**
	 * Detail site users and their association with Facebook
	 *
	 * @since 1.1.6
	 *
	 * @global wpdb $wpdb escape SQL
	 * @return void
	 */
	public static function users_section() {
		global $wpdb;

		$users = self::get_all_wordpress_facebook_users();

		// should only happen if errors
		if ( empty( $users['fb'] ) && empty( $users['wp'] ) )
			return;

		echo '<section id="debug-users"><header><h3>' . esc_html( __( 'Authors' ) ) . '</h3></header>';

		if ( ! empty( $users['fb'] ) ) {
			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );

			echo '<table><caption>' . esc_html( _x( 'Connected to Facebook', 'Local user account has an associated Facebook account stored', 'facebook' ) ) . '</caption><colgroup><col><col span="2" class="permissions"></colgroup><thead><tr><th>' . esc_html( __( 'Name' ) ) . '</th><th title="' . esc_attr( __( 'Facebook account', 'facebook' ) ) . '"></th><th>' . esc_html( __( 'Post to timeline', 'facebook' ) ) . '</th><th>' . esc_html( __( 'Manage pages', 'facebook' ) ) . '</th></tr></thead><tbody>';
			foreach( $users['fb'] as $user ) {
				echo '<tr><th><a href="' . esc_url( get_author_posts_url( $user->id ) ) . '">' . esc_html( $user->display_name ) . '</a></th>';

				echo '<td>';
				$profile_link = Facebook_User::facebook_profile_link( $user->fb_data );
				if ( $profile_link )
					echo '<a class="facebook-icon" href="' . esc_url( $profile_link, array( 'http', 'https' ) ) . '"></a>';
				unset( $profile_link );
				echo '</td>';

				echo '<td>';
				if ( isset( $user->fb_data['permissions']['publish_actions'] ) && $user->fb_data['permissions']['publish_actions'] )
					echo self::EXISTS;
				else
					echo self::DOES_NOT_EXIST;
				echo '</td>';

				echo '<td>';
				if ( isset( $user->fb_data['permissions']['manage_pages'] ) && $user->fb_data['permissions']['manage_pages'] && isset( $user->fb_data['permissions']['publish_stream'] ) && $user->fb_data['permissions']['publish_stream'] )
					echo self::EXISTS;
				else
					echo self::DOES_NOT_EXIST;
				echo '</td></tr>';
			}
			echo '</tbody></table>';
		}

		if ( ! empty( $users['wp'] ) ) {
			// last 90 days
			$where = ' AND ' . $wpdb->prepare( 'post_date > %s', date( 'Y-m-d H:i:s', time() - 90*24*60*60 ) );

			$public_post_types = get_post_types( array( 'public' => true ) );
			if ( is_array( $public_post_types ) && ! empty( $public_post_types ) ) {
				$public_post_types = array_values( $public_post_types );
				$where .= ' AND post_type';
				if ( count( $public_post_types ) === 1 ) {
					$where .= $wpdb->prepare( ' = %s', $public_post_types[0] );
				} else {
					$s = '';
					foreach( $public_post_types as $post_type ) {
						$s .= "'" . esc_sql( $post_type ) . "',";
					}
					$where .= ' IN (' . rtrim( $s, ',' ) . ')';
					unset( $s );
				}
			}

			$public_states = get_post_stati( array( 'public' => true ) );
			if ( is_array( $public_states ) && ! empty( $public_states ) ) {
				$public_states = array_values( $public_states );
				$where .= ' AND post_status';
				if ( count( $public_states ) === 1 ) {
					$where .= $wpdb->prepare( ' = %s', $public_states[0] );
				} else {
					$s = '';
					foreach( $public_states as $state ) {
						$s .= "'" . esc_sql( $state ) . "',";
					}
					$where .= ' IN (' . rtrim( $s, ',' ) . ')';
					unset( $s );
				}
			}
			unset( $public_states );

			echo '<table><caption>' . esc_html( __( 'Not connected to Facebook', 'facebook' ) ) . '</caption><thead><th>' . esc_html( __( 'Name' ) ) . '</th><th><abbr title="' . esc_attr( sprintf( __( 'Number of published posts in the last %u days', 'facebook' ), 90 ) ) . '">' . esc_html( _x( '# of recent posts', 'recent articles. used as a table column header', 'facebook' ) ) . '</abbr></th></thead><tbody>';

			foreach( $users['wp'] as $user ) {
				echo '<tr><th><a href="' . esc_url( get_author_posts_url( $user->id ) ) . '">' . esc_html( $user->display_name ) . '</a></th>';
				echo '<td>' . $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d $where", $user->id ) ) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		}

		echo '</section>';
	}

	/**
	 * Display the currently associated Facebook page, if one exists.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public static function post_to_page_section() {
		if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
			require_once( dirname( __FILE__ ) . '/settings-social-publisher.php' );

		$post_to_page = get_option( Facebook_Social_Publisher_Settings::OPTION_PUBLISH_TO_PAGE );
		if ( ! ( is_array( $post_to_page ) && isset( $post_to_page['id'] ) && isset( $post_to_page['name'] ) && isset( $post_to_page['access_token'] ) ) )
			return;

		echo '<section id="debug-page"><header><h3>' . esc_html( __( 'Facebook Page', 'facebook' ) ) . '</h3></header>';
		$page_link = '';
		if ( isset( $post_to_page['link'] ) )
			$page_link = $post_to_page['link'];
		else
			$page_link = 'https://www.facebook.com/' . $post_to_page['id'];
		echo '<p>' . sprintf( esc_html( _x( 'Publishing to %s.', 'publishing to a page name on Facebook.com', 'facebook' ) ), '<a href="' . esc_url( $page_link, array( 'http', 'https' ) ) . '">' . esc_html( $post_to_page['name'] ) . '</a>' );
		unset( $page_link );
		if ( isset( $post_to_page['set_by_user'] ) ) {
			if ( get_current_user_id() == $post_to_page['set_by_user'] ) {
				echo ' ' . esc_html( __( 'Saved by you.', 'facebook' ) );
			} else {
				$setter = get_userdata( $post_to_page['set_by_user'] );
				if ( $setter ) {
					echo ' ' . esc_html( sprintf( _x( 'Saved by %s.', 'saved by person name', 'facebook' ), $setter->display_name ) );
				}
				unset( $setter );
			}
			unset( $current_user );
		}
		echo '</p>';

		echo '</section>';
	}

	/**
	 * Which features are enabled for the site on each major view type?
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public static function enabled_features_by_view_type() {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname( __FILE__ ) . '/settings-social-plugin.php' );
		$views = Facebook_Social_Plugin_Settings::get_show_on_choices( 'all' );
		if ( ! is_array( $views ) || empty( $views ) )
			return;


		echo '<section id="debug-social-plugins"><header><h3>' . esc_html( __( 'Social Plugins', 'facebook' ) ) . '</h3></header>';
		echo '<table><caption>' . esc_html( _x( 'Features enabled by view type', 'software features available based on different classifications website view', 'facebook' ) ) . '</caption>';

		$features = array(
			'like' => array(
				'name' => _x( 'Like Button', 'Facebook Like Button social plugin', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/like-button/'
			),
			'send' => array(
				'name' => _x( 'Send Button', 'Facebook Send Button social plugin', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/send-button/'
			),
			'follow' => array(
				'name' => _x( 'Follow Button', 'Facebook Follow Button social plugin', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/follow-button/'
			),
			'recommendations_bar' => array(
				'name' => _x( 'Recommendations Bar', 'Facebook Recommendations Bar social plugin', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/recommendations-bar/'
			),
			'comments' => array(
				'name' => _x( 'Comments Box', 'Facebook Comments Box social plugin', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/comments/'
			)
		);

		echo '<thead><tr><th>' . esc_html( __( 'View', 'facebook' ) ) . '</th>';
		foreach ( $features as $slug => $properties ) {
			echo '<th>';
			if ( isset( $properties['url'] ) )
				echo '<a href="' . esc_url( $properties['url'], array( 'http', 'https' ) ) . '">' . esc_html( $properties['name'] ) . '</a>';
			else
				echo esc_html( $properties['name'] );
			echo '</th>';
		}
		echo '</tr></thead><tbody>';

		foreach( $views as $view ) {
			echo '<tr><th>' . $view . '</th>';
			$view_features = get_option( 'facebook_' . $view . '_features' );
			if ( ! is_array( $view_features ) )
				$view_features = array();
			foreach( $features as $feature => $properties ) {
				echo '<td>';
				if ( isset( $view_features[$feature] ) )
					echo self::EXISTS;
				else
					echo ' ';
				echo '</td>';
			}
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</section>';
	}

	/**
	 * Widgets enabled for the site.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public static function widgets_section() {
		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( dirname(__FILE__) . '/settings.php' );

		$active_widgets = Facebook_Settings::get_active_widgets();
		if ( ! is_array( $active_widgets ) || empty( $active_widgets ) )
			return;

		$all_widgets = array(
			'activity-feed' => array(
				'name' => __( 'Activity Feed', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/activity/'
			),
			'like' => array(
				'name' => __( 'Like Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/like-button/'
			),
			'recommendations' => array(
				'name' => __( 'Recommendations Box', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/recommendations/'
			),
			'send' => array(
				'name' => __( 'Send Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/send-button/'
			),
			'follow' => array(
				'name' => __( 'Follow Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/plugins/follow-button/'
			)
		);

		echo '<section id="debug-widgets"><header><h3>' . esc_html( __( 'Widgets' ) ) . '</h3></header>';
		echo '<table><thead><tr><th>' . esc_html( _x( 'Widget name', 'name of a page component', 'facebook' ) ) . '</th><th>' . esc_html( __( 'Active', 'facebook' ) ) . '</th></tr></thead><tbody>';
		foreach( $all_widgets as $slug => $widget ) {
			echo '<tr><th><a href="' . esc_url( $widget['url'], array( 'http', 'https' ) ) . '">' . esc_html( $widget['name'] ) . '</a></th><td>';
			if ( in_array( $slug , $active_widgets ) )
				echo self::EXISTS;
			else
				echo ' ';
			echo '</td></tr>';
		}
		echo '</tbody></table></section>';
	}

	/**
	 * How does the site communicate with Facebook?
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public static function server_info() {
		echo '<section id="debug-server"><header><h3>' . esc_html( __( 'Server configuration', 'facebook' ) ) . '</h3></header><table><thead><th>' . esc_html( __( 'Feature', 'facebook' ) ) . '</th><th>' . esc_html( _x( 'Info', 'Information', 'facebook' ) ) . '</th></thead><tbody>';

		// PHP version
		echo '<tr><th>' . esc_html( sprintf( _x( '%s version', 'software version', 'facebook' ), 'PHP' ) ) . '</th><td>';
		// PHP > 5.2.7
		if ( defined( 'PHP_MAJOR_VERSION' ) && defined( 'PHP_MINOR_VERSION' ) && defined( 'PHP_RELEASE_VERSION' ) )
			echo esc_html( PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION );
		else
			esc_html( phpversion() );
		echo '</td></tr>';

		// WordPress version
		echo '<tr><th>' . esc_html( sprintf( _x( '%s version', 'software version', 'facebook' ), 'WordPress' ) ) . '</th><td>' . esc_html( get_bloginfo( 'version' ) ) . '</td></tr>';

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) )
			echo '<tr><th>' . esc_html( __( 'Server software', 'facebook' ) ) . '</th><td>' . esc_html( $_SERVER['SERVER_SOFTWARE'] ) . '</td></tr>';

		// WP_HTTP connection for SSL
		echo '<tr id="debug-server-http">';
		echo '<th>' . sprintf( esc_html( _x( '%s connection method', 'server-to-server connection', 'facebook' ) ), '<a href="http://codex.wordpress.org/HTTP_API">WP_HTTP</a>' ) . '</th><td>';
		$http_obj = _wp_http_get_object();
		$http_transport = $http_obj->_get_first_available_transport( array( 'ssl' => true ) );
		if ( is_string( $http_transport ) && strlen( $http_transport ) > 8 ) {
			$http_transport = strtolower( substr( $http_transport, 8 ) );
			if ( $http_transport === 'curl' ) {
				echo '<a href="http://php.net/manual/book.curl.php">cURL</a>';
				$curl_version = curl_version();
				if ( isset( $curl_version['version'] ) )
					echo ' ' . esc_html( $curl_version['version'] );
				if ( isset( $curl_version['ssl_version'] ) ) {
					echo '; ';
					$ssl_version = $curl_version['ssl_version'];
					if ( strlen( $curl_version['ssl_version'] ) > 8 && substr_compare( $ssl_version, 'OpenSSL/', 0, 8 ) === 0 )
						echo '<a href="http://openssl.org/">OpenSSL</a>/' . esc_html( substr( $ssl_version, 8 ) );
					else
						echo esc_html( $ssl_version );
					unset( $ssl_version );
				}
				unset( $curl_version );
			} else if ( $http_transport === 'streams' ) {
				echo '<a href="http://www.php.net/manual/book.stream.php">Stream</a>';
			} else if ( $http_transport === 'fsockopen' ) {
				echo '<a href="http://php.net/manual/function.fsockopen.php">fsockopen</a>';
			} else {
				echo $http_transport;
			}
		} else {
			echo _x( 'none available', 'No available solution found.', 'facebook' );
		}
		echo '</td></tr>';
		unset( $http_transport );
		unset( $http_obj );

		echo '</table></section>';
	}
}

?>
