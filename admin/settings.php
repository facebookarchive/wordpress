<?php
/**
 * Store settings related to the Facebook plugin
 *
 * @since 1.1
 */
class Facebook_Settings {

	/**
	 * All plugin features supported
	 *
	 * @since 1.1
	 * @var array
	 */
	public static $features = array( 'like', 'send', 'subscribe', 'recommendations_bar', 'comments', 'social_publisher' );

	/**
	 * Add hooks
	 *
	 * @since 1.1
	 */
	public static function init() {
		self::migrate_options_10();
		add_action( 'admin_menu', 'Facebook_Settings::settings_menu_items' );
		add_filter( 'plugin_action_links', 'Facebook_Settings::plugin_action_links', 10, 2 );

		if ( self::app_credentials_exist() ) {
			$available_features = apply_filters( 'facebook_features', self::$features );
			if ( is_array( $available_features ) && ! empty( $available_features ) ) {
				if ( in_array( 'social_publisher', $available_features, true ) ) {
					// check user capability to publish to Facebook
					$current_user = wp_get_current_user();
					if ( user_can( $current_user, 'edit_posts' ) )
						add_action( 'admin_init', 'Facebook_Settings::prompt_user_login' );
				}
			}
		}
	}

	/**
	 * Check if Facebook application credentials are stored for the current site
	 * Limit displayed features based on the existence of app data
	 *
	 * @since 1.1
	 * @return bool true if app_id and app_secret stored
	 */
	public static function app_credentials_exist() {
		global $facebook_loader;

		if ( isset( $facebook_loader ) && isset( $facebook_loader->credentials ) && isset( $facebook_loader->credentials['app_id'] ) && isset( $facebook_loader->credentials['app_secret'] ) )
			return true;
		return false;
	}

	/**
	 * Add Facebook to the WordPress administration menu
	 *
	 * @since 1.1
	 */
	public static function settings_menu_items() {
		global $facebook_loader, $submenu;

		if ( ! class_exists( 'Facebook_Application_Settings' ) )
			require_once( dirname( __FILE__ ) . '/settings-app.php' );

		$menu_hook = Facebook_Application_Settings::menu_item();
		if ( ! $menu_hook )
			return;

		$app_credentials_exist = self::app_credentials_exist();

		$menu_slug = Facebook_Application_Settings::PAGE_SLUG;

		$available_features = apply_filters( 'facebook_features', self::$features );

		// publisher could short-circuit all features
		if ( ! is_array( $available_features ) || empty( $available_features ) )
			return;

		if ( in_array( 'like', $available_features, true ) ) {
			if ( ! class_exists( 'Facebook_Like_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-like-button.php' );

			Facebook_Like_Button_Settings::add_submenu_item( $menu_slug );
		}

		if ( in_array( 'send', $available_features, true ) ) {
			if ( ! class_exists( 'Facebook_Send_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-send-button.php' );

			Facebook_Send_Button_Settings::add_submenu_item( $menu_slug );
		}

		if ( in_array( 'subscribe', $available_features, true ) ) {
			if ( ! class_exists( 'Facebook_Subscribe_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-subscribe-button.php' );

			Facebook_Subscribe_Button_Settings::add_submenu_item( $menu_slug );
		}

		// some features require stored Facbook application credentials. don't be a tease.
		if ( $app_credentials_exist ) {
			if ( in_array( 'recommendations_bar', $available_features, true ) ) {
				if ( ! class_exists( 'Facebook_Recommendations_Bar_Settings' ) )
					require_once( dirname(__FILE__) . '/settings-recommendations-bar.php' );

				Facebook_Recommendations_Bar_Settings::add_submenu_item( $menu_slug );
			}

			if ( in_array( 'comments', $available_features, true ) ) {
				if ( ! class_exists( 'Facebook_Comments_Settings' ) )
					require_once( dirname(__FILE__) . '/settings-comments.php' );

				Facebook_Comments_Settings::add_submenu_item( $menu_slug );
			}

			if ( in_array( 'social_publisher', $available_features, true ) && wp_http_supports( array( 'ssl' => true ) ) ) {
				if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
					require_once( dirname(__FILE__) . '/settings-social-publisher.php' );

				Facebook_Social_Publisher_Settings::add_submenu_item( $menu_slug );
			}
		}

		// make an assumption about submenu mappings, but don't fail if our assumption is wrong
		// WordPress will automatically duplicate the top-level menu destination when a submenu is created
		// Change wording based on Facebook parent
		if ( is_array( $submenu ) && isset( $submenu[$menu_slug] ) && is_array( $submenu[$menu_slug] ) && is_array( $submenu[$menu_slug][0] ) && is_string( $submenu[$menu_slug][0][0] ) ) {
			$submenu[$menu_slug][0][0] = __('General');
			if ( $app_credentials_exist ) {
				$submenu[$menu_slug][] = array(
					_x( 'Insights', 'Facebook Insights', 'facebook' ),
					'manage_options',
					'https://www.facebook.com/insights/?' . http_build_query( array( 'sk' => 'ao_' . $facebook_loader->credentials['app_id'] ) ),
					''
				);
			}
		}
	}

	/**
	 * Prompt a logged-in user to associate his or her account with a Facebook account
	 *
	 * @since 1.1
	 */
	public static function prompt_user_login() {
		if ( ! class_exists( 'Facebook_Admin_Login' ) )
			require_once( dirname(__FILE__) . '/login.php' );

		// show admin dialog on post creation, post edit, or user profile screen
		foreach( array( 'post-new.php','post.php','profile.php' ) as $pagenow ) {
			add_action( 'load-' . $pagenow, 'Facebook_Admin_Login::connect_facebook_account' );
		} 
	}

	/**
	 * Standardize the form flow through settings API
	 *
	 * @since 1.1
	 * @uses settings_fields()
	 * @uses do_settings_sections()
	 * @param string $page_slug constructs custom actions. passed to Settings API functions
	 */
	public static function settings_page_template( $page_slug, $page_title ) {
		echo '<div class="wrap">';
		do_action( 'facebook_settings_before_header_' . $page_slug );
		echo '<header><h2>' . esc_html( $page_title ) . '</h2></header>';
		do_action( 'facebook_settings_after_header_' . $page_slug );

		// handle general messages such as settings updated up top
		// place individual settings errors alongside their fields
		settings_errors( 'general' );

		echo '<form method="post" action="options.php">';

		settings_fields( $page_slug );
		do_settings_sections( $page_slug );

		submit_button();
		echo '</form>';
		echo '</div>';
		do_action( 'facebook_settings_footer_' . $page_slug );
		self::stats_beacon();
	}

	/**
	 * Link to settings from the plugin listing page
	 *
	 * @since 1.1
	 * @param array $links links displayed under the plugin
	 * @param string $file plugin main file path relative to plugin dir
	 * @return array links array passed in, possibly with our settings link added
	 */
	public static function plugin_action_links( $links, $file ) {
		if ( $file === plugin_basename( dirname( dirname(__FILE__) ) . '/facebook.php' ) ) {
			if ( ! class_exists( 'Facebook_Application_Settings' ) )
				require_once( dirname( __FILE__ ) . '/settings-app.php' );

			$links[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?' . http_build_query( array( 'page' => Facebook_Application_Settings::PAGE_SLUG ) ) ) . '">' . __( 'Settings' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Report basic usage data back to Facebook
	 *
	 * @since 1.1
	 * @param string $app_id Facebook application identifier
	 */
	public static function stats_beacon( $app_id = '' ) {
		$debug = self::debug_output( $app_id );
		if ( ! empty( $debug ) )
			echo '<div><img src="http://www.facebook.com/impression.php?' . http_build_query( array( 'plugin' => 'wordpress', 'payload' => json_encode( $debug ) ) ) . '" width="1" height="1" alt=" " /></div>';
	}

	/**
	 * Identify active features for debugging purposes or sent via a Facebook beacon
	 *
	 * @since 1.1
	 * @param string $app_id application identifier
	 * @return array debug information
	 */
	public static function debug_output( $app_id = '' ) {
		global $facebook_loader;

		if ( ! $app_id && isset( $facebook_loader ) && isset( $facebook_loader->credentials['app_id'] ) )
			$app_id = $facebook_loader->credentials['app_id'];

		$debug = array();

		if ( $app_id )
			$debug['appid'] = $app_id;

		if ( isset( $facebook_loader ) )
			$debug['version'] = $facebook_loader::VERSION;

		$hostname = parse_url( site_url(), PHP_URL_HOST );
		if ( $hostname )
			$debug['domain'] = $hostname;
		unset( $hostname );

		// where are we running?
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-social-plugin.php' );

		$enabled_features = array();
		$all_targets = Facebook_Social_Plugin_Settings::get_show_on_choices( 'all' );
		$option_name = 'facebook_%s_features';
		foreach ( $all_targets as $target ) {
			$features = get_option( sprintf( $option_name, $target ) );
			if ( is_array( $features ) && ! empty( $features ) )
				$enabled_features[$target] = $features;
			else
				$enabled_features[$target] = false; // show a potential target where nothing appears
			unset( $features );
		}
		unset( $option_name );
		unset( $all_targets );
		if ( ! empty( $enabled_features ) )
			$debug['features'] = $enabled_features;
		unset( $enabled_features );

		$sidebar_widgets = wp_get_sidebars_widgets();
		unset( $sidebar_widgets['wp_inactive_widgets'] ); // no need to track inactives
		$sidebar_widgets = array_unique( array_merge( array_values( $sidebar_widgets ) ) ); // track widgets, not sidebar names
		if ( is_array( $sidebar_widgets ) && isset( $sidebar_widgets[0] ) ) {
			$sidebar_widgets = $sidebar_widgets[0];
			$widgets = array();

			// iterate through each sidebar configuration
			// note any facebook widgets we find along the way
			foreach( $sidebar_widgets as $widget_id ) {
				if ( strlen( $widget_id ) > 9 && substr_compare( $widget_id, 'facebook-', 0, 9 ) === 0 ) {
					$feature = substr( $key, 9, strrpos( $key, '-' ) - 9 );
					if ( ! in_array( $feature, $widgets, true ) )
						$widgets[] = $feature;
					unset( $feature );
				}
			}

			if ( ! empty( $widgets ) )
				$debug['widgets'] = $widgets;
			unset( $widgets );
		}

		return $debug;
	}

	/**
	 * Check if the wordpress user has plugins that may conflict with the Facebook plugin due to Open Graph.
	 * Display an admin dialog if conflicts found
	 */
	public static function plugin_conflicts() {
		$og_conflicting_plugins = apply_filters( 'fb_conflicting_plugins', array(
			'http://wordpress.org/extend/plugins/opengraph/',
			'http://wordbooker.tty.org.uk',
			'http://ottopress.com/wordpress-plugins/simple-facebook-connect/',
			'http://www.whiletrue.it',
			'http://aaroncollegeman.com/sharepress'
		) );

		// allow for short circuit
		if ( ! is_array( $og_conflicting_plugins ) || empty( $og_conflicting_plugins ) )
			return;

		//fetch activated plugins
		$plugins_list = get_option( 'active_plugins' );
		if ( ! is_array( $plugins_list ) )
			$plugins_list = array();

		$conflicting_plugins = array();

		// iterate through activated plugins, checking if they are in the list of conflict plugins
		foreach ( $plugins_list as $val ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $val );
			if ( ! ( isset( $plugin_data['PluginURI'] ) && isset( $plugin_data['Name'] ) ) || $plugin_data['PluginURI'] === 'http://wordpress.org/extend/plugins/facebook/' )
				continue;

			if( in_array( $plugin_data['PluginURI'], $og_conflicting_plugins, true ) )
				$conflicting_plugins[] = $plugin_data['Name'];

			unset( $plugin_data );
		}

		//if there are more than 1 plugins relying on Open Graph, warn the user on this plugins page
		if ( ! empty( $conflicting_plugins ) ) {
			fb_admin_dialog( sprintf( __( 'You have plugins installed that could potentially conflict with the Facebook plugin. Please consider disabling the following plugins on the %s:', 'facebook' ) . '<br />' . implode( ', ', $conflicting_plugins ), '<a href="' . admin_url( 'plugins.php' ) .'">' . esc_html( __( 'Plugins Settings page', 'facebook' ) ) . '</a>' ), true);
		}
	}

	/**
	 * Migrate options from plugin version 1.0
	 *
	 * @since 1.1
	 */
	public static function migrate_options_10() {
		if ( get_option( 'facebook_migration_10' ) )
			return;

		if ( current_user_can( 'manage_options' ) ) {
			if ( ! class_exists( 'Facebook_Migrate_Options_10' ) )
				require_once( dirname(__FILE__) . '/migrate-options-10.php' );
			Facebook_Migrate_Options_10::migrate();
			update_option( 'facebook_migration_10', '1' );
		}
	}
}
?>