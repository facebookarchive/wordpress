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
	public static $features = array( 'like' => true, 'send' => true, 'follow' => true, 'recommendations_bar' => true, 'comments' => true, 'social_publisher' => true );

	/**
	 * Add hooks
	 *
	 * @since 1.1
	 */
	public static function init() {
		add_action( 'admin_init', array( 'Facebook_Settings', 'migrate_options' ), 0, 0 );
		add_action( 'admin_menu', array( 'Facebook_Settings', 'settings_menu_items' ) );
		add_filter( 'plugin_action_links', array( 'Facebook_Settings', 'plugin_action_links' ), 10, 2 );
		add_action( 'admin_init', array( 'Facebook_Settings', 'load_social_settings' ), 1 );
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Settings', 'enqueue_scripts' ) );
	}

	/**
	 * Load extra settings only if Facebook application credentials exist
	 *
	 * @since 1.2
	 */
	public static function load_social_settings() {
		global $facebook_loader;

		if ( ! ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() ) )
			return;

		$available_features = apply_filters( 'facebook_features', self::$features );
		if ( is_array( $available_features ) && ! empty( $available_features ) ) {
			if ( isset( $available_features['social_publisher'] ) ) {
				// check user capability to publish to Facebook
				$current_user = wp_get_current_user();
				if ( user_can( $current_user, 'edit_posts' ) ) {
					if ( ! class_exists( 'Facebook_User_Profile' ) )
						require_once( dirname(__FILE__) . '/profile.php' );
					add_action( 'load-profile.php', array( 'Facebook_User_Profile', 'init' ) );
				}
			}
		}
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.1.6
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'facebook-admin-icons', plugins_url( 'static/css/admin/icons' . ( ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min' ) . '.css', dirname( __FILE__ ) ), array(), '1.1.9' );
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

		if ( isset( $available_features['like'] ) ) {
			if ( ! class_exists( 'Facebook_Like_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-like-button.php' );

			Facebook_Like_Button_Settings::add_submenu_item( $menu_slug );
		}

		if ( isset( $available_features['send'] ) ) {
			if ( ! class_exists( 'Facebook_Send_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-send-button.php' );

			Facebook_Send_Button_Settings::add_submenu_item( $menu_slug );
		}

		if ( isset( $available_features['follow'] ) ) {
			if ( ! class_exists( 'Facebook_Follow_Button_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-follow-button.php' );

			Facebook_Follow_Button_Settings::add_submenu_item( $menu_slug );
		}

		// some features require stored Facbook application credentials. don't be a tease.
		if ( $app_credentials_exist ) {
			if ( isset( $available_features['recommendations_bar'] ) ) {
				if ( ! class_exists( 'Facebook_Recommendations_Bar_Settings' ) )
					require_once( dirname(__FILE__) . '/settings-recommendations-bar.php' );

				Facebook_Recommendations_Bar_Settings::add_submenu_item( $menu_slug );
			}

			if ( isset( $available_features['comments'] ) ) {
				if ( ! class_exists( 'Facebook_Comments_Settings' ) )
					require_once( dirname(__FILE__) . '/settings-comments.php' );

				Facebook_Comments_Settings::add_submenu_item( $menu_slug );
			}

			if ( isset( $available_features['social_publisher'] ) && wp_http_supports( array( 'ssl' => true ) ) ) {
				if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
					require_once( dirname(__FILE__) . '/settings-social-publisher.php' );

				Facebook_Social_Publisher_Settings::add_submenu_item( $menu_slug );
			}

			if ( ! class_exists( 'Facebook_Settings_Debugger' ) )
				require_once( dirname(__FILE__) . '/settings-debug.php' );
			Facebook_Settings_Debugger::add_submenu_item( $menu_slug );
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
			add_action( 'load-' . $pagenow, array( 'Facebook_Admin_Login', 'connect_facebook_account' ) );
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
			$debug['version'] = Facebook_Loader::VERSION;

		$hostname = parse_url( site_url(), PHP_URL_HOST );
		if ( $hostname )
			$debug['domain'] = $hostname;
		unset( $hostname );

		// where are we running?
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-social-plugin.php' );

		$enabled_features = array();
		$views = Facebook_Social_Plugin_Settings::get_show_on_choices( 'all' );
		foreach ( $views as $view ) {
			$features = get_option( 'facebook_' . $view . '_features' );
			if ( is_array( $features ) && ! empty( $features ) )
				$enabled_features[$view] = array_keys( $features );
			else
				$enabled_features[$view] = false; // show a potential target where nothing appears
			unset( $features );
		}
		unset( $views );
		if ( ! empty( $enabled_features ) )
			$debug['features'] = $enabled_features;
		unset( $enabled_features );

		$widgets = self::get_active_widgets();
		if ( ! empty( $widgets ) )
			$debug['widgets'] = $widgets;

		return $debug;
	}

	/**
	 * Get a list of Facebook widgets in one or more sidebars
	 *
	 * @since 1.1.6
	 * @return array Facebook widget feature slugs
	 */
	public static function get_active_widgets() {
		$sidebar_widgets = wp_get_sidebars_widgets();
		if ( ! is_array( $sidebar_widgets ) )
			return array();

		// actives only
		unset( $sidebar_widgets['wp_inactive_widgets'] ); // no need to track inactives

		$widgets = array();
		// iterate through each sidebar, then widgets within, looking for Facebook widgets
		foreach ( $sidebar_widgets as $sidebar => $widget_list ) {
			if ( ! is_array( $widget_list ) )
				continue;
			foreach ( $widget_list as $widget_id ) {
				if ( strlen( $widget_id ) > 9 && substr_compare( $widget_id, 'facebook-', 0, 9 ) === 0 ) {
					$feature = substr( $widget_id, 9, strrpos( $widget_id, '-' ) - 9 );
					if ( ! isset( $widgets[$feature] ) )
						$widgets[$feature] = true;
					unset( $feature );
				}
			}
		}

		if ( ! empty( $widgets ) )
			return array_keys( $widgets );

		return array();
	}

	/**
	 * Check if the wordpress user has plugins that may conflict with the Facebook plugin due to Open Graph.
	 * Display an admin dialog if conflicts found
	 */
	public static function plugin_conflicts() {
		$og_conflicting_plugins = apply_filters( 'fb_conflicting_plugins', array(
			'http://wordpress.org/extend/plugins/opengraph/' => true,
			'http://wordbooker.tty.org.uk' => true,
			'http://ottopress.com/wordpress-plugins/simple-facebook-connect/' => true,
			'http://www.whiletrue.it' => true,
			'http://aaroncollegeman.com/sharepress' => true
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

			if ( isset( $og_conflicting_plugins[ $plugin_data['PluginURI'] ] ) )
				$conflicting_plugins[] = esc_html( $plugin_data['Name'] );

			unset( $plugin_data );
		}

		//if there are more than 1 plugins relying on Open Graph, warn the user on this plugins page
		if ( ! empty( $conflicting_plugins ) ) {
			echo '<div id="facebook-warning" class="error fade"><p>' . sprintf( esc_html( __( 'You have plugins installed that could potentially conflict with the Facebook plugin. Please consider disabling the following plugins on the %s:', 'facebook' ) . '<br />' . implode( ', ', $conflicting_plugins ) ), '<a href="' . admin_url( 'plugins.php' ) .'">' . esc_html( __( 'Plugins Settings page', 'facebook' ) ) . '</a>' ) . '</p></div>';
		}
	}

	/**
	 * Migrate options from plugin version 1.0
	 *
	 * @since 1.1
	 */
	public static function migrate_options() {
		if ( get_option( 'facebook_migration_118' ) )
			return;

		// wait for an appropirate user
		if ( ! current_user_can( 'manage_options' ) )
			return;

		// the options migration from 1.1 sets migrations from 1.1.5 and 1.1.8
		if ( get_option( 'facebook_migration_10' ) ) {
			// run 1.1.5 migration if 1.0 migration already run
			if ( ! get_option( 'facebook_migration_115' ) ) {
				if ( ! class_exists( 'Facebook_Migrate_Options_115' ) )
					require_once( dirname(__FILE__) . '/migrate-options-115.php' );
				Facebook_Migrate_Options_115::migrate();
				update_option( 'facebook_migration_115', '1' );
			}
			if ( ! class_exists( 'Facebook_Migrate_Options_118' ) )
				require_once( dirname(__FILE__) . '/migrate-options-118.php' );
			Facebook_Migrate_Options_118::migrate();
			update_option( 'facebook_migration_118', '1' );
		} else {
			if ( ! class_exists( 'Facebook_Migrate_Options_10' ) )
				require_once( dirname(__FILE__) . '/migrate-options-10.php' );
			Facebook_Migrate_Options_10::migrate();
			update_option( 'facebook_migration_10', '1' );
			update_option( 'facebook_migration_115', '1' ); // 1.0 covers the changes from 1.1.5
			update_option( 'facebook_migration_118', '1' ); // 1.0 covers the changes from 1.1.8
		}
	}
}
?>