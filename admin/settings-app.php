<?php

/**
 * Display a settings page for Facebook application data
 *
 * @since 1.1
 */
class Facebook_Application_Settings {
	/**
	 * Settings page identifier.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-application-settings';

	/**
	 * Define our option array value.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const OPTION_NAME = 'facebook_application';

	/**
	 * Define the kid-directed option value.
	 *
	 * @since 1.5
	 *
	 * @var string
	 */
	const OPTION_NAME_KID_DIRECTED = 'facebook_kid_directed_site';

	/**
	 * The hook suffix assigned by add_submenu_page()
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $hook_suffix = '';

	/**
	 * Initialize with an options array.
	 *
	 * @since 1.1
	 *
	 * @param array $options existing options
	 */
	public function __construct( $options = array() ) {
		if ( is_array( $options ) && ! empty( $options ) )
			$this->existing_options = $options;
		else
			$this->existing_options = array();
	}

	/**
	 * Add a menu item to WordPress admin.
	 *
	 * @since 1.1
	 *
	 * @uses add_utility_page()
	 * @return string page hook
	 */
	public static function menu_item() {
		$app_settings = new Facebook_Application_Settings();

		$hook_suffix = add_utility_page(
			__( 'Facebook Plugin Settings', 'facebook' ), // page <title>
			'Facebook', // menu title
			'manage_options', // capability needed
			self::PAGE_SLUG, // what should I call you?
			array( &$app_settings, 'settings_page' ), // pageload callback
			'none' // to be replaced by Facebook dashicon
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
	 * Load stored options and scripts on settings page view.
	 *
	 * @since 1.1
	 *
	 * @uses get_option() load existing options
	 * @return void
	 */
	public function onload() {
		$options = get_option( self::OPTION_NAME );
		if ( ! is_array( $options ) )
			$options = array();
		$this->existing_options = $options;

		// notify of lack of HTTPS
		if ( ! wp_http_supports( array( 'ssl' => true ) ) )
			add_action( 'admin_notices', array( 'Facebook_Application_Settings', 'admin_notice' ) );

		$this->settings_api_init();

		add_action( 'admin_enqueue_scripts', array( 'Facebook_Application_Settings', 'enqueue_scripts' ) );
	}

	/**
	 * Warn of minimum requirements not met for app access token.
	 *
	 * @since 1.5
	 *
	 * @return void
	 */
	public static function admin_notice() {
		echo '<div class="error">';
		echo '<p>' . esc_html( __( 'Your server does not support communication with Facebook servers over HTTPS.', 'facebook' ) ) . '</p>';
		echo '<p>' . esc_html( __( 'Facebook application functionality such as posting to your Facebook Timeline requires a HTTPS connection to Facebook servers.', 'facebook' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Load the settings page.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function settings_page() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		add_action( 'facebook_settings_after_header_' . $this->hook_suffix, array( 'Facebook_Application_Settings', 'after_header' ) );

		Facebook_Settings::settings_page_template( $this->hook_suffix, __( 'Facebook for WordPress', 'facebook' ) );
	}

	/**
	 * Enhance settings page with JavaScript.
	 *
	 * @since 1.1
	 *
	 * @uses wp_enqueue_script()
	 * @return void
	 */
	public static function enqueue_scripts() {
		wp_enqueue_script( 'facebook-jssdk' );
	}

	/**
	 * Facebook Like Button after header.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public static function after_header() {
		// Facebook Like Button social plugin markup builder
		if ( ! class_exists( 'Facebook_Like_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-like-button.php' );

		// promote Facebook for WordPress page on Facebook Developers site
		$like_button = new Facebook_Like_Button(false);
		$like_button->setURL( 'https://developers.facebook.com/docs/wordpress/' );
		$like_button->setLayout( 'button_count' );
		$like_button->includeShareButton();
		$like_button->setFont( 'arial' );
		$like_button->setReference( 'wp-admin' );
		echo $like_button->asHTML();
	}

	/**
	 * Hook into the settings API.
	 *
	 * @since 1.1
	 *
	 * @uses add_settings_section()
	 * @uses add_settings_field()
	 * @return void
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

		add_settings_field(
			'facebook-app-id',
			_x( '<abbr title="application">App</abbr> ID', 'Facebook application identifier', 'facebook' ),
			array( &$this, 'display_app_id' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-app-id' )
		);
		add_settings_field(
			'facebook-app-secret',
			_x( '<abbr title="application">App</abbr> Secret', 'Facebook application secret', 'facebook' ),
			array( &$this, 'display_app_secret' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-app-secret' )
		);

		$section = 'facebook-restrictions';

		add_settings_section(
			$section,
			__( 'Restrictions', 'facebook' ),
			array( 'Facebook_Application_Settings', 'restriction_section_header' ),
			$this->hook_suffix
		);

		add_settings_field(
			'facebook-kid-directed-site',
			__( 'Child-Directed Site', 'facebook' ),
			array( 'Facebook_Application_Settings', 'display_kid_directed_site' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-kid-directed-site' )
		);

		$this->inline_help_content();
	}

	/**
	 * Introduction to the application settings section.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function section_header() {
		if ( ! empty( $this->existing_options['app_id'] ) )
			echo '<p><a href="' . esc_url( 'https://developers.facebook.com/apps/' . $this->existing_options['app_id'] ) . '">' . esc_html( __( 'Edit your application settings on Facebook', 'facebook' ) ) . '</a></p>';
		else
			echo '<p><a href="https://developers.facebook.com/apps/">' . esc_html( sprintf( __( 'Create a new Facebook application or associate %s with an existing Facebook application.', 'facebook' ), get_bloginfo( 'name' ) ) ) . '</a></p>';
	}

	/**
	 * Introduction to Facebook restrictions configurations.
	 *
	 * @since 1.5
	 *
	 * @return void
	 */
	public static function restriction_section_header() {
		echo '<p>' . esc_html( _x( 'Limit Facebook functionality', 'Section header for options limiting Facebook on your site', 'facebook' ) ) . '</p>';
	}

	/**
	 * Display the application ID input field.
	 *
	 * @since 1.1
	 *
	 * @return void
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

		echo '<p class="description">' . esc_html( __( 'An application identifier associates your site, its pages, and visitor actions with a registered Facebook application.', 'facebook' ) ) . '</p>';
	}

	/**
	 * Display the Facebook application secret input field.
	 *
	 * @since 1.1
	 *
	 * @return void
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

		echo '<p class="description">' . esc_html( __( 'An application secret is a secret shared between Facebook and your application, similar to a password.', 'facebook' ) ) . '</p>';
	}

	/**
	 * Display a checkbox to designate the site as child-focused.
	 *
	 * @since 1.5
	 *
	 * @global Facebook_Loader $facebook_loader determine child directed site status
	 * @return void
	 */
	public static function display_kid_directed_site() {
		global $facebook_loader;

		echo '<label><input type="checkbox" name="' . self::OPTION_NAME . '[kid_directed_site]" id="facebook-kid-directed-site" value="1"';
		checked( $facebook_loader->kid_directed );
		echo ' /> ';
		echo esc_html( __( 'Is your site directed at children in the United States under the age of 13?', 'facebook' ) );
		echo '</label>';
	}

	/**
	 * Clean user inputs before saving to database.
	 *
	 * @since 1.1
	 *
	 * @param array $options form options values
	 * @return array $options sanitized options
	 */
	public static function sanitize_options( $options ) {
		// start fresh
		$clean_options = array();

		if ( isset( $options['kid_directed_site'] ) )
			update_option( self::OPTION_NAME_KID_DIRECTED, '1' );
		else
			delete_option( self::OPTION_NAME_KID_DIRECTED );

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
					$app_secret_proof = hash_hmac( 'sha256', $access_token, $clean_options['app_secret'] );
					$app_info = Facebook_WP_Extend::get_app_details_by_access_token( $access_token, array( 'id', 'namespace' ), $app_secret_proof );
					if ( empty( $app_info ) ) {
						if ( function_exists( 'add_settings_error' ) )
							add_settings_error( 'facebook-app-auth', 'facebook-app-auth-error', __( 'Application access token failed on authentication with Facebook.', 'facebook' ) );
						unset( $clean_options['app_id'] );
						unset( $clean_options['app_secret'] );
					} else {
						if ( isset( $app_info['namespace'] ) )
							$clean_options['app_namespace'] = $app_info['namespace'];
						$clean_options['access_token'] = $access_token;
						if ( $app_secret_proof )
							$clean_options['appsecret_proof'] = $app_secret_proof;
					}
					unset( $app_info );
					unset( $app_secret_proof );
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
					if ( function_exists( 'add_settings_error' ) )
							add_settings_error( 'facebook-app-info', 'facebook-app-info-error', __( 'Unable to request application data from Facebook.', 'facebook' ) );
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
	 * Display helpful information about setting up a new application.
	 *
	 * @since 1.1
	 *
	 * @return string HTML content
	 */
	public static function help_tab_new_app() {
		$content = '<p>' . sprintf ( esc_html( __( '%s to take advantage of advantage of advanced Facebook features such as post to timeline, recommendations bar, and more.', 'facebook' ) ), '<a href="https://developers.facebook.com/apps/">' . __( 'Register for a Facebook application', 'facebook' ) . '</a>' ) . ' ' . esc_html( sprintf( __( 'You may need to register your %1$s account as a developer account if this is your first time managing a %1$s application.', 'facebook' ), 'Facebook' ) ) . '</p>';

		$content .= '<p>' . sprintf( esc_html( __( 'Click the %s button near the top right corner of the page to trigger an application creation dialog.', 'facebook' ) ), '<span style="background-color:#EEE;border:1px solid #999;color:#333;font-family:\'lucinda grande\',tahoma,verdana,arial,sans-serif;font-size:11px;font-weight:bold;line-height:13px;margin:0;padding-top:1px;padding-right:0;padding-bottom:2px;padding-left:0;text-align:center;white-space:nowrap;">+ Create New App</span>' ) . '</p>';

		$content .= '<div style="text-align:center"><img alt="' . esc_attr( __( 'Facebook new application creation dialog', 'facebook' ) ) . '" src="' . plugins_url( 'static/img/create-app.png', dirname(__FILE__) ) .  '" width="665" height="225" /></div>';

		$content .= '<p>' . esc_html( __( 'Uniquely identify your site on Facebook with an application name.', 'facebook' ) );
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
				$content .= esc_html( sprintf( __( 'You may choose to use "%s" as your Facebook application name.', 'facebook' ), $site_name ) );
			}
		}
		$content .= '</p>';
		return $content;
	}

	/**
	 * Display helpful information about retrieving application credentials from Facebook Developers site.
	 *
	 * @since 1.1
	 *
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
	 * Help applications associate basic data with their Facebook application.
	 *
	 * @since 1.1
	 *
	 * @param string $app_id application identifier. used to construct a link to the Facebook Developers site
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
	 * Explain the child-directed site option
	 *
	 * @since 1.5
	 *
	 * @return string HTML string
	 */
	public static function help_tab_kid_directed() {
		$content = '<p>' . esc_html( __( 'Comply with privacy laws of your audience including information collected about children.', 'facebook' ) ) . '</p>';
		$content .= '<p>' . esc_html( __( 'Example: a site primary directed at children in the United States under the age of 13 might set this option to comply with privacy policies in the United States.', 'facebook' ) ) . '</p>';
		$content .= '<p><a href="https://developers.facebook.com/docs/plugins/restrictions/">' . esc_html( __( 'Facebook social plugins: Information for Child-Directed Sites and Services', 'facebook' ) ) . '</a></p>';

		return $content;
	}

	/**
	 * Display help content on the settings page
	 *
	 * @since 1.1
	 *
	 * @uses get_current_screen()
	 * @return void
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
				'title' => __( 'Create a Facebook application', 'facebook' ),
				'content' => self::help_tab_new_app()
			) );
		} else {
			$screen->add_help_tab( array(
				'id' => 'facebook-existing-app-help',
				'title' => __( 'Existing Facebook application', 'facebook' ),
				'content' => self::help_tab_existing_app()
			) );
		}

		$screen->add_help_tab( array(
			'id' => 'facebook-application-details-help',
			'title' => __( 'Application details', 'facebook' ),
			'content' => self::help_tab_edit_app( $app_id )
		) );

		$screen->add_help_tab( array(
			'id' => 'facebook-kid-directed-help',
			'title' => __( 'Child directed', 'facebook' ),
			'content' => self::help_tab_kid_directed()
		) );

		$screen->set_help_sidebar( '<p><a href="https://developers.facebook.com/apps/">' . esc_html( __( 'Facebook Apps Tool', 'facebook' ) ) . '</a></p>' );
	}
}

?>
