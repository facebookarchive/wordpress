<?php

class Facebook_Application_Settings {
	/**
	 * Settings page identifier
	 *
	 * @since 1.1
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-application-settings';

	/**
	 * Define our option array value
	 *
	 * @since 1.1
	 * @var string
	 */
	const OPTION_NAME = 'facebook_application';

	/**
	 * Initialize with an options array
	 *
	 * @since 1.1
	 * @param array $options existing options
	 * @param string $hook_suffix (optional) page slug. used to build settings fields
	 */
	public function __construct( $options = array() ) {
		if ( is_array( $options ) && ! empty( $options ) )
			$this->existing_options = $options;
		else
			$this->existing_options = array();
	}

	/**
	 * Add a menu item to WordPress admin
	 *
	 * @since 1.1
	 * @uses add_utility_page()
	 * @return string page hook
	 */
	public static function menu_item() {
		$app_settings = new Facebook_Application_Settings();

		$hook_suffix = add_utility_page(
			sprintf( __( '%s Plugin Settings', 'facebook' ), 'Facebook' ), // page <title>
			'Facebook', // menu title
			'manage_options', // capability needed
			self::PAGE_SLUG, // what should I call you?
			array( &$app_settings, 'settings_page' ), // pageload callback
			plugins_url( 'static/img/icon-bw.png', dirname(__FILE__) ) // icon make pretty
		);

		// conditional load CSS, scripts
		if ( $hook_suffix ) {
			$app_settings->hook_suffix = $hook_suffix;
			register_setting( $hook_suffix, self::OPTION_NAME, array( 'Facebook_Application_Settings', 'sanitize_options' ) );
			add_action( 'load-' . $hook_suffix, array( &$app_settings, 'onload' ) );
		}

		return $hook_suffix;
	}

	/**
	 * Load stored options and scripts on settings page view
	 *
	 * @since 1.1
	 */
	public function onload() {
		$options = get_option( self::OPTION_NAME );
		if ( ! is_array( $options ) )
			$options = array();
		$this->existing_options = $options;

		$this->settings_api_init();

		add_action( 'admin_enqueue_scripts', array( 'Facebook_Application_Settings', 'enqueue_scripts' ) );
	}

	/**
	 * Load the settings page
	 *
	 * @since 1.1
	 */
	public function settings_page() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		// notify of conflicts on the main settings page
		// tie to an action to allow easy removal on sites/networks that rather not run checks
		add_action( 'facebook_notify_plugin_conflicts', array( 'Facebook_Settings', 'plugin_conflicts' ) );

		add_action( 'facebook_settings_after_header_' . $this->hook_suffix, array( 'Facebook_Application_Settings', 'after_header' ) );

		Facebook_Settings::settings_page_template( $this->hook_suffix, __( 'Facebook for WordPress', 'facebook' ) );
	}

	/**
	 * Enhance settings page with JavaScript
	 *
	 * @since 1.1
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'facebook-jssdk' );
	}

	/**
	 * Facebook Like Button after header
	 *
	 * @since 1.1
	 */
	public static function after_header() {
		if ( ! class_exists( 'Facebook_Like_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-like-button.php' );

		// promote Facebook for WordPress page on Facebook Developers site
		$like_button = new Facebook_Like_Button(false);
		$like_button->setURL( 'http://developers.facebook.com/wordpress/' );
		$like_button->setLayout( 'button_count' );
		$like_button->includeSendButton();
		$like_button->setFont( 'arial' );
		$like_button->setReference( 'wp-admin' );
		echo $like_button->asHTML();

		do_action( 'facebook_notify_plugin_conflicts' );
	}

	/**
	 * Hook into the settings API
	 *
	 * @since 1.1
	 * @uses add_settings_section()
	 * @uses add_settings_field()
	 */
	private function settings_api_init() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		// Facebook application settings
		$section = 'facebook-app';
		add_settings_section(
			$section,
			__( 'Application information', 'facebook' ),
			array( &$this, 'section_header' ),
			$this->hook_suffix
		);

		$app_abbr = '<abbr title="' . esc_attr( _x( 'application', 'computer application or program', 'facebook' ) ) . '">' . esc_html( _x( 'App', 'application', 'facebook' ) ) . '</abbr>';

		add_settings_field(
			'facebook-app-id',
			sprintf( __( '%s ID', 'facebook' ), $app_abbr ),
			array( &$this, 'display_app_id' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-app-id' )
		);
		add_settings_field(
			'facebook-app-secret',
			sprintf( __( '%s secret', 'facebook' ), $app_abbr ),
			array( &$this, 'display_app_secret' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-app-secret' )
		);

		$this->inline_help_content();
	}

	/**
	 * Introduction to the application settings section
	 *
	 * @since 1.1
	 */
	public function section_header() {
		if ( ! empty( $this->existing_options['app_id'] ) )
			echo '<p><a href="' . esc_url( 'https://developers.facebook.com/apps/' . $this->existing_options['app_id'] ) . '">' . esc_html( __( 'Edit your application settings on Facebook', 'facebook' ) ) . '</a></p>';
		else
			echo '<p><a href="https://developers.facebook.com/apps/">' . esc_html( sprintf( __( 'Create a new Facebook application or associate %s with an existing Facebook application.', 'facebook' ), get_bloginfo( 'name' ) ) ) . '</a></p>';
	}

	/**
	 * Display the application ID input field
	 *
	 * @since 1.1
	 */
	public function display_app_id() {
		$key = 'app_id';

		if ( isset( $this->existing_options[$key] ) && $this->existing_options[$key] )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = '';

		$id = 'facebook-app-id';
		settings_errors( $id );
		echo '<input type="text" name="' . self::OPTION_NAME . '[' . $key . ']" id="' . $id . '"';
		if ( $existing_value )
			echo ' value="' . esc_attr( $existing_value ) . '"';
		echo ' maxlength="32" size="40" autocomplete="off" pattern="[0-9]+" />';

		echo '<p class="description">' . esc_html( sprintf( __( 'An application identifier associates your site, its pages, and visitor actions with a registered %s application.', 'facebook' ), 'Facebook' ) ) . '</p>';
	}

	/**
	 * Display the Facebook application secret input field
	 *
	 * @since 1.1
	 */
	public function display_app_secret() {
		$key = 'app_secret';

		if ( isset( $this->existing_options[$key] ) && $this->existing_options[$key] )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = '';

		$id = 'facebook-app-secret';
		settings_errors( $id );
		echo '<input type="text" name="' . self::OPTION_NAME . '[' . $key . ']" id="' . $id . '"';
		if ( $existing_value )
			echo ' value="' . esc_attr( $existing_value ) . '"';
		echo ' size="40" autocomplete="off" pattern="[0-9a-f]+" />';

		echo '<p class="description">' . esc_html( sprintf( __( 'An application secret is a secret shared between %s and your application, similar to a password.', 'facebook' ), 'Facebook' ) ) . '</p>';
	}

	/**
	 * Clean user inputs before saving to database
	 *
	 * @since 1.1
	 * @param array $options form options values
	 */
	public static function sanitize_options( $options ) {
		// start fresh
		$clean_options = array();

		if ( isset( $options['app_id'] ) ) {
			// leading spaces is a common copy-paste mistake
			$app_id = trim( $options['app_id'] );
			if ( $app_id ) {
				// digit characters only
				// better to reject a known bad value than remove its bad characters & save bad value
				if ( function_exists( 'ctype_digit' ) ) { // ctype might not always be present
					if ( ctype_digit( $app_id ) )
						$clean_options['app_id'] = $app_id;
				} else if ( preg_match( '/^[0-9]+$/', $app_id ) ) {
					$clean_options['app_id'] = $app_id;
				} else if ( function_exists( 'add_settings_error' ) ) {
					add_settings_error( 'facebook-app-id', 'facebook-app-id-error', __( 'App ID must contain only digits.', 'facebook' ) );
				}
			} else {
				// removing app id disables other features such as comments
				delete_option( 'facebook_comments_enabled' );
			}
			unset( $app_id );
		}

		if ( isset( $options['app_secret'] ) ) {
			$app_secret = strtolower( trim( $options['app_secret'] ) );
			if ( $app_secret ) {
				if ( preg_match( '/^[0-9a-f]+$/', $app_secret ) ) // hex
					$clean_options['app_secret'] = $app_secret;
				else if ( function_exists( 'add_settings_error' ) )
					add_settings_error( 'facebook-app-secret', 'facebook-app-secret-error', __( 'Invalid app secret.', 'facebook' ) );
			}
			unset( $app_secret );
		}

		// store an application access token and verify additional data
		if ( isset( $clean_options['app_id'] ) && isset( $clean_options['app_secret'] ) ) {
			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( dirname( dirname(__FILE__) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

			if ( wp_http_supports( array( 'ssl' => true ) ) ) {
				$access_token = Facebook_WP_Extend::get_app_access_token( $clean_options['app_id'], $clean_options['app_secret'] );
				if ( $access_token ) {
					$app_info = Facebook_WP_Extend::get_app_details_by_access_token( $access_token, array( 'id', 'namespace' ) );
					if ( empty( $app_info ) ) {
						unset( $clean_options['app_id'] );
						unset( $clean_options['app_secret'] );
					} else {
						if ( isset( $app_info['namespace'] ) )
							$clean_options['app_namespace'] = $app_info['namespace'];
						$clean_options['access_token'] = $access_token;
					}
					unset( $app_info );
				} else {
					if ( function_exists( 'add_settings_error' ) )
						add_settings_error( 'facebook-app-auth', 'facebook-app-auth-error', __( 'Application ID and secret failed on authentication with Facebook.', 'facebook' ) );
					unset( $clean_options['app_id'] );
					unset( $clean_options['app_secret'] );
				}
				unset( $access_token );
			} else {
				$app_info = Facebook_WP_Extend::get_app_details( $clean_options['app_id'], array( 'id','namespace' ) );
				if ( empty( $app_info ) ) {
					unset( $clean_options['app_id'] );
					unset( $clean_options['app_secret'] );
				} else if ( isset( $app_info['namespace'] ) ) {
					$clean_options['app_namespace'] = $app_info['namespace'];
				}
				unset( $app_info );
			}
		} else {
			unset( $clean_options['app_id'] );
			unset( $clean_options['app_secret'] );
		}

		return $clean_options;
	}

	/**
	 * Display helpful information about setting up a new application
	 *
	 * @since 1.1
	 * @return string HTML content
	 */
	public static function help_tab_new_app() {
		$content = '<p>' . sprintf ( esc_html( __( '%1$s to take advantage of advantage of advanced %2$s features such as post to timeline, recommendations bar, and more.', 'facebook' ) ), '<a href="https://developers.facebook.com/apps/">' . __( 'Register for a Facebook application', 'facebook' ) . '</a>', 'Facebook' ) . ' ' . esc_html( sprintf( __( 'You may need to register your %1$s account as a developer account if this is your first time managing a %1$s application.', 'facebook' ), 'Facebook' ) ) . '</p>';

		$content .= '<p>' . sprintf( esc_html( __( 'Click the %s button near the top right corner of the page to trigger an application creation dialog.', 'facebook' ) ), '<span style="background-color:#EEE;border:1px solid #999;color:#333;font-family:\'lucinda grande\',tahoma,verdana,arial,sans-serif;font-size:11px;font-weight:bold;line-height:13px;margin:0;padding-top:1px;padding-right:0;padding-bottom:2px;padding-left:0;text-align:center;white-space:nowrap;">+ Create New App</span>' ) . '</p>';

		$content .= '<div style="text-align:center"><img alt="' . esc_attr( sprintf( __( '%s new application creation dialog', 'facebook' ), 'Facebook' ) ) . '" src="' . plugins_url( 'static/img/create-app.png', dirname(__FILE__) ) .  '" width="610" height="179" /></div>';

		$content .= '<p>' . sprintf( esc_html( __( 'Uniquely identify your site on %1$s with an application name.', 'facebook' ) ), 'Facebook' );
		$site_name = get_bloginfo( 'name' );
		if ( $site_name ) {
			$content .= ' ';
			$site_name_length = strlen( $site_name );
			$min_length = 3;
			$max_length = 32;
			if ( $site_name_length < $min_length ) {
				$content .= esc_html( sprintf( __( 'You must choose an application name longer than "%s."', 'facebook' ), $site_name ) );
				$content .= ' ' . esc_html( sprintf( __( 'An application name must be between %1$u and %2$u characters in length.', 'facebook' ), $min_length, $max_length ) );
			} else if ( $site_name_length > 32 ) {
				$content .= esc_html( sprintf( __( 'You must choose an application name shorter than "%s."','facebook' ), $site_name ) );
				$content .= ' ' . esc_html( sprintf( __( 'An application name must be between %1$u and %2$u characters in length.', 'facebook' ), $min_length, $max_length ) );
			} else {
				$content .= esc_html( sprintf( __( 'You may choose to use "%1$s" as your %2$s application name.', 'facebook' ), $site_name, 'Facebook' ) );
			}
		}
		$content .= '</p>';
		return $content;
	}

	/**
	 * Display helpful information about retrieving application credentials from Facebook Developers site
	 *
	 * @since 1.1
	 * @return string HTML content
	 */
	public static function help_tab_existing_app() {
		$content = '<ol>';
		$content .= '<li>';
		$content .= sprintf(
			esc_html( _x( 'Open the %s.', 'open the link to Facebook Developers dashboard site.', 'facebook' ) ),
			'<a href="https://developers.facebook.com/apps?view=all_apps">' . esc_html( __( 'Facebook Developers Applications dashboard', 'facebook' ) ) . '</a>'
		);
		$content .= '</li>';
		$content .= '<li>' . esc_html( __( 'Select your existing application from the list of applications', 'facebook' ) ) . '</li>';
		$content .= '<li>' . esc_html( __( 'Copy your App ID and App Secret from the Settings Summary section of your application.', 'facebook' ) ) . '</li>';
		$content .= '</ol>';

		return $content;
	}

	/**
	 * Help applications associate basic data with their Facebook application
	 *
	 * @since 1.1
	 * @return string HTML content
	 */
	public static function help_tab_edit_app( $app_id = '' ) {
		if ( $app_id ) {
			$app_base_link = esc_url( 'https://developers.facebook.com/apps/' . $app_id . '/', array( 'https' ) );
			$app_link = '<a href="' . $app_base_link . 'summary' . '">' . esc_html( __( 'Facebook application', 'facebook' ) ) .  '</a>';
		} else {
			$app_base_link = '';
			$app_link = '<a href="https://developers.facebook.com/apps/">' . esc_html( __( 'new Facebook application', 'facebook' ) ) .  '</a>';
		}

		$site_url = site_url( '/' );

		$content = '<p>' . sprintf( esc_html( __( 'Your %1$s should be associated with %2$s across desktop web, mobile web, iPhone, Android, or any other presence you have established.', 'facebook' ) ), $app_link, '<a href="' . esc_url( $site_url ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>' );
		$content .= ' ' . esc_html( __( 'Facebook can send site visitors to the most appropriate URL based on their browsing context, market your site, and properly identify quality content with some extra information for your application.', 'facebook' ) );
		$content .= '</p>';
		unset( $app_link );

		// Basic Settings screen
		$content .= '<section id="facebook-application-details-help-basic"><header><h3>' . esc_html( __( 'Basic Settings', 'facebook' ) ) .  '</h3></header>';
		$content .= '<p>' . sprintf( esc_html( __( 'Associate your Facebook application with a domain, a desktop URL, and a mobile URL through your application\'s %s.', 'facebook' ) ), $app_base_link ? '<a href="' . $app_base_link . '">' . esc_html( __( 'basic settings', 'facebook' ) ) . '</a>' : esc_html( __( 'basic settings', 'facebook' ) ) ) . '</p>';
		$content .= '<p>' . __( 'For example:', 'facebook' ) . '</p>';

		$content .= '<table style="min-width:50%"><thead><tr><th>' . esc_html( _x( 'Field', 'data entry field', 'facebook' ) ) . '</th><th>' . esc_html( _x( 'Value', 'data entry value', 'facebook' ) ) . '</th></tr></thead><tbody>';

		$user = wp_get_current_user();
		if ( $user && isset( $user->user_email ) )
			$content .= '<tr><th>' . esc_html( __( 'Contact Email', 'facebook' ) ) . '</th><td>' . esc_html( $user->user_email ) . '</td></tr>';

		$content .= '<tr><th>' . esc_html( __( 'App Domains', 'facebook' ) ) . '</th><td>' . esc_html( parse_url( $site_url, PHP_URL_HOST ) ) . '</td></tr>';
		$content .= '<tr><th>' . esc_html( __( 'Website with Facebook Login', 'facebook' ) ) . '</th><td>' . esc_html( $site_url ) . '</td></tr>';
		$content .= '<tr><th>' . esc_html( __( 'Mobile website', 'facebook' ) ) . '</th><td>' . esc_html( $site_url ) . '</td></tr>';

		$content .= '</tbody></table></section>';

		// App Details
		$content .= '<section id="facebook-application-details-help-details"><header><h3>' . esc_html( __( 'App Details', 'facebook' ) ) . '</h3></header>';
		$content .= '<p>';
		$content .= esc_html( __( 'Set your primary language, site description, and categorize your site.', 'facebook' ) ) . ' ';
		$content .= esc_html( __( 'Add icons and images to establish trust when asking your authors for publish permissions or marketing your site through Facebook.', 'facebook' ) );
		$content .= '</p>';
		$content .= '</section>';

		return $content;
	}

	/**
	 * Display help content on the settings page
	 *
	 * @since 1.1
	 */
	private function inline_help_content() {
		$screen = get_current_screen();
		if ( ! $screen ) // null if global not set
			return;

		$app_id = empty( $this->existing_options['app_id'] ) ? '' : $this->existing_options['app_id'];

		if ( ! $app_id || empty( $this->existing_options['app_secret'] ) ) {
			$app_id = '';
			$screen->add_help_tab( array(
				'id' => 'facebook-new-app-help',
				'title' => sprintf( __( 'Create a %s application', 'facebook' ), 'Facebook' ),
				'content' => self::help_tab_new_app()
			) );
		} else {
			$screen->add_help_tab( array(
				'id' => 'facebook-existing-app-help',
				'title' => sprintf( __( 'Existing %s application', 'facebook' ), 'Facebook' ),
				'content' => self::help_tab_existing_app()
			) );
		}

		$screen->add_help_tab( array(
			'id' => 'facebook-application-details-help',
			'title' => __( 'Application details', 'facebook' ),
			'content' => self::help_tab_edit_app( $app_id )
		) );

		$screen->set_help_sidebar( '<p><a href="https://developers.facebook.com/apps/">' . esc_html( sprintf( __( '%s Apps Tool', 'facebook' ), 'Facebook' ) ) . '</a></p>' );
	}
}

?>