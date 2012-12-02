<?php
/**
 * @package Facebook
 * @version 1.1.8
 */
/*
Plugin Name: Facebook
Plugin URI: http://wordpress.org/extend/plugins/facebook/
Description: Facebook for WordPress. Make your site deeply social in just a couple of clicks.
Author: Facebook
Author URI: https://developers.facebook.com/wordpress/
Version: 1.1.8
License: GPL2
License URI: license.txt
Domain Path: /languages/
*/

/**
 * Load the Facebook plugin
 *
 * @since 1.1
 */
class Facebook_Loader {
	/**
	 * Uniquely identify plugin version
	 * Bust caches based on this value
	 *
	 * @since 1.1
	 * @var string
	 */
	const VERSION = '1.1.8';

	/**
	 * Locale of the site expressed as a Facebook locale
	 *
	 * @since 1.1
	 * @var string
	 */
	public $locale = 'en_US';

	/**
	 * Store Facebook application information (id, secret, namespace) if available.
	 *
	 * @since 1.1
	 * @var array
	 */
	public $credentials = array();

	/**
	 * List of locales supported by Facebook.
	 * Two-letter languages codes stored in WordPress are translated to full locales; if a language has multiple country localizations place the first choice earlier in the array to make it the language default
	 *
	 * @since 1.1
	 */
	public static $locales = array( 'af_ZA' => true, 'ar_AR' => true, 'ay_BO' => true, 'az_AZ' => true, 'be_BY' => true, 'bg_BG' => true, 'bn_IN' => true, 'bs_BA' => true, 'ca_ES' => true, 'ck_US' => true, 'cs_CZ' => true, 'cy_GB' => true, 'da_DK' => true, 'de_DE' => true, 'el_GR' => true, 'en_US' => true, 'en_GB' => true, 'eo_EO' => true, 'es_CL' => true, 'es_ES' => true, 'es_CO' => true, 'es_LA' => true, 'es_MX' => true, 'es_VE' => true, 'et_EE' => true, 'eu_ES' => true, 'fa_IR' => true, 'fb_FI' => true, 'fb_LT' => true, 'fi_FI' => true, 'fo_FO' => true, 'fr_FR' => true, 'fr_CA' => true, 'ga_IE' => true, 'gl_ES' => true, 'gn_PY' => true, 'gu_IN' => true, 'he_IL' => true, 'hi_IN' => true, 'hr_HR' => true, 'hu_HU' => true, 'hy_AM' => true, 'id_ID' => true, 'is_IS' => true, 'it_IT' => true, 'ja_JP' => true, 'jv_ID' => true, 'ka_GE' => true, 'kk_KZ' => true, 'km_KH' => true, 'kn_IN' => true, 'ko_KR' => true, 'ku_TR' => true, 'la_VA' => true, 'li_NL' => true, 'lt_LT' => true, 'lv_LV' => true, 'mg_MG' => true, 'mk_MK' => true, 'ml_IN' => true, 'mn_MN' => true, 'mr_IN' => true, 'ms_MY' => true, 'mt_MT' => true, 'nb_NO' => true, 'ne_NP' => true, 'nl_NL' => true, 'nl_BE' => true, 'nn_NO' => true, 'pa_IN' => true, 'pl_PL' => true, 'ps_AF' => true, 'pt_PT' => true, 'pt_BR' => true, 'qu_PE' => true, 'rm_CH' => true, 'ro_RO' => true, 'ru_RU' => true, 'sa_IN' => true, 'se_NO' => true, 'sk_SK' => true, 'sl_SI' => true, 'so_SO' => true, 'sq_AL' => true, 'sr_RS' => true, 'sv_SE' => true, 'sw_KE' => true, 'sy_SY' => true, 'ta_IN' => true, 'te_IN' => true, 'tg_TJ' => true, 'th_TH' => true, 'tl_PH' => true, 'tl_ST' => true, 'tr_TR' => true, 'tt_RU' => true, 'uk_UA' => true, 'ur_PK' => true, 'uz_UZ' => true, 'vi_VN' => true, 'xh_ZA' => true, 'yi_DE' => true, 'zh_CN' => true, 'zh_HK' => true, 'zh_TW' => true, 'zu_ZA' => true );

	/**
	 * Let's get it started
	 *
	 * @since 1.1
	 */
	public function __construct() {
		// load plugin files relative to this directory
		$this->plugin_directory = dirname(__FILE__) . '/';
		$this->set_locale();

		// Load the textdomain for translations
		load_plugin_textdomain( 'facebook', false, $this->plugin_directory . 'languages/' );

		$credentials = get_option( 'facebook_application' );
		if ( ! is_array( $credentials ) )
			$credentials = array();
		$this->credentials = $credentials;
		unset( $credentials );

		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

		// load shortcodes
		if ( ! class_exists( 'Facebook_Shortcodes' ) )
			require_once( $this->plugin_directory . 'social-plugins/shortcodes.php' );
		Facebook_Shortcodes::init();

		if ( is_user_logged_in() ) {
			// admin bar may show on public-facing site as well as administrative section
			add_action( 'add_admin_bar_menus', array( &$this, 'admin_bar' ) );
		}

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'register_js_sdk' ), 1 );
			$this->admin_init();
		} else {
			add_action( 'wp_enqueue_scripts', array( &$this, 'register_js_sdk' ), 1 );
			add_action( 'wp', array( &$this, 'public_init' ) );
		}
	}

	/**
	 * Add Facebook functionality to the WordPress admin bar
	 *
	 * @since 1.1
	 * @param WP_Admin_Bar $wp_admin_bar existing WordPress admin bar object
	 */
	public function admin_bar() {
		if ( isset( $this->credentials ) && isset( $this->credentials['app_id'] ) ) {
			if ( get_option( 'facebook_comments_enabled' ) ) {
				if ( ! class_exists( 'Facebook_Comments' ) )
					require_once( $this->plugin_directory . 'social-plugins/class-facebook-comments.php' );
				Facebook_Comments::admin_bar_menu();
			}
		}
	}

	/**
	 * Register the Facebook JavaScript SDK for later enqueueing
	 *
	 * @since 1.1
	 * @uses wp_register_script
	 */
	public function register_js_sdk() {
		global $wp_scripts;

		$handle = 'facebook-jssdk'; // match the Facebook async snippet ID to avoid double load
		wp_register_script( $handle, ( is_ssl() ? 'https' : 'http' ) . '://connect.facebook.net/' . $this->locale . '/' . ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'all/debug.js' : 'all.js' ), array(), null, true );

		// register the script but take it back with an async load
		add_filter( 'script_loader_src', array( &$this, 'async_script_loader_src' ), 1, 2 );

		$args = array(
			'channelUrl' => plugins_url( 'channel.php', __FILE__ ),
			'status' => true,
			'cookie' => true,
			'xfbml' => true
		);

		// appId optional
		if ( ! empty( $this->credentials['app_id'] ) )
			$args['appId'] = $this->credentials['app_id'];

		$args = apply_filters( 'facebook_jssdk_init_options', $args );

		// allow the publisher to short circuit the init through the filter
		if ( ! empty( $args ) && isset( $wp_scripts ) ) {
			$wp_scripts->add_data( $handle, 'data', 'window.fbAsyncInit=function(){FB.init(' . json_encode( $args ) . ');' . apply_filters( 'facebook_jssdk_init_extras', '', isset( $this->credentials['app_id'] ) ? $this->credentials['app_id'] : '' ) . '}' );
		}
	}

	/**
	 * Load Facebook JS SDK async
	 * Called from script_loader_src filter
	 *
	 * @since 1.1
	 * @param string $src script URL
	 * @param string $handle WordPress registered script handle
	 * @return string empty string if Facebook JavaScript SDK, else give back the src variable
	 */
	public function async_script_loader_src( $src, $handle ) {
		global $wp_scripts;

		if ( $handle !== 'facebook-jssdk' )
			return $src;

		// @link https://developers.facebook.com/docs/reference/javascript/#loading
		$html = '<div id="fb-root"></div><script type="text/javascript">(function(d){var js,id="facebook-jssdk",ref=d.getElementsByTagName("script")[0];if(d.getElementById(id)){return;}js=d.createElement("script");js.id=id;js.async=true;js.src=' . json_encode( $src ) . ';ref.parentNode.insertBefore(js,ref);}(document));</script>' . "\n";
		if ( isset( $wp_scripts ) && $wp_scripts->do_concat )
			$wp_scripts->print_html .= $html;
		else
			echo $html;

		// empty out the src response
		// results in extra DOM but nothing to load
		return '';
	}

	/**
	 * Styles applied to public-facing pages
	 *
	 * @since 1.1
	 * @uses enqueue_styles()
	 */
	public static function enqueue_styles() {
		wp_enqueue_style( 'facebook', plugins_url( 'static/css/style' . ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min' ) . '.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Initialize a global $facebook variable if one does not already exist and credentials stored for this site
	 *
	 * @since 1.1
	 * @return true if $facebook global exists, else false
	 */
	public function load_php_sdk() {
		global $facebook;

		if ( isset( $facebook ) )
			return true;

		if ( ! empty( $this->credentials['app_id'] ) && ! empty( $this->credentials['app_secret'] ) ) {
			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( $this->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

			$facebook = new Facebook_WP_Extend( array(
				'appId' => $this->credentials['app_id'],
				'secret' => $this->credentials['app_secret']
			) );
			if ( $facebook )
				return true;
		}

		return false;
	}

	/**
	 * Intialize the public, front end views
	 *
	 * @since 1.1
	 */
	public function public_init() {
		// no feed filters yet
		if ( is_feed() || is_404() )
			return;

		// always include Open Graph protocol markup
		if ( ! class_exists( 'Facebook_Open_Graph_Protocol' ) )
			require_once( $this->plugin_directory . 'open-graph-protocol.php' );
		add_action( 'wp_head', array( 'Facebook_Open_Graph_Protocol', 'add_og_protocol' ) );

		add_action( 'wp_enqueue_scripts', array( 'Facebook_Loader', 'enqueue_jssdk' ) );

		// include comment count filters on all pages
		if ( get_option( 'facebook_comments_enabled' ) ) {
			if ( ! class_exists( 'Facebook_Comments' ) )
				require_once( $this->plugin_directory . 'social-plugins/class-facebook-comments.php' );

			add_filter( 'comments_array', array( 'Facebook_Comments', 'comments_array_filter' ), 10, 2 );
			add_filter( 'comments_open', array( 'Facebook_Comments', 'comments_open_filter' ), 10, 2 );

			// override comment count to a garbage number
			add_filter( 'get_comments_number', array( 'Facebook_Comments', 'get_comments_number_filter' ), 10, 2 );
			// display comments number if used in template
			add_filter( 'comments_number', array( 'Facebook_Comments', 'comments_number_filter' ), 10, 2 );
		}

		// check for enabled features by page type
		$enabled_features = array();
		$option_name = 'facebook_%s_features';
		if ( is_home() || is_front_page() ) {
			$enabled_features = get_option( sprintf( $option_name, 'home' ) );
		} else if ( is_archive() ) {
			// all archives wrapped in one option
			// is_post_type_archive || is_date || is_author || is_category || is_tag || is_tax
			$enabled_features = get_option( sprintf( $option_name, 'archive' ) );
		} else {
			$post_type = get_post_type();
			if ( $post_type )
				$enabled_features = get_option( sprintf( $option_name, $post_type ) );
		}
		if ( ! is_array( $enabled_features ) || empty( $enabled_features ) )
			return;

		require_once( $this->plugin_directory . 'social-plugins/social-plugins.php' );

		$priority = apply_filters( 'facebook_content_filter_priority', 30 );

		// features available for archives and singular
		if ( isset( $enabled_features['like'] ) )
			add_filter( 'the_content', 'facebook_the_content_like_button', $priority );
		if ( isset( $enabled_features['send'] ) )
			add_filter( 'the_content', 'facebook_the_content_send_button', $priority );
		if ( isset( $enabled_features['subscribe'] ) )
			add_filter( 'the_content', 'facebook_the_content_subscribe_button', $priority );
		if ( isset( $enabled_features['mentions'] ) ) {
			if ( ! function_exists( 'facebook_social_publisher_mentioning_output' ) )
				require_once( dirname(__FILE__) . '/social-publisher/mentions.php' );
			add_filter( 'the_content', 'facebook_social_publisher_mentioning_output', $priority );
		}

		// individual posts, pages, and custom post types features
		if ( isset( $post_type ) ) {
			if ( isset( $enabled_features['recommendations_bar'] ) )
				add_filter( 'the_content', 'facebook_the_content_recommendations_bar', $priority );

			// only load comments class and features if enabled and post type supports
			if ( isset( $enabled_features['comments'] ) && post_type_supports( $post_type, 'comments' ) ) {
				if ( ! class_exists( 'Facebook_Comments' ) )
					require_once( $this->plugin_directory . 'social-plugins/class-facebook-comments.php' );

				add_filter( 'the_content', array( 'Facebook_Comments', 'the_content_comments_box' ), $priority );
				add_action( 'wp_enqueue_scripts', array( 'Facebook_Comments', 'css_hide_comments' ), 0 );
			}
		}

		add_action( 'wp_enqueue_scripts', array( 'Facebook_Loader', 'enqueue_styles' ) );
	}

	/**
	 * Enqueue the JavaScript SDK
	 *
	 * @since 1.1
	 * @uses wp_enqueue_script()
	 */
	public static function enqueue_jssdk() {
		wp_enqueue_script( 'facebook-jssdk', false, array(), false, true );
	}

	/**
	 * Initialize the backend, administrative views
	 *
	 * @since 1.1
	 */
	public function admin_init() {
		$admin_dir = $this->plugin_directory . 'admin/';

		$sdk = $this->load_php_sdk();

		if ( $sdk ) {
			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname(__FILE__) . '/facebook-user.php' );
			Facebook_User::extend_access_token();
		}

		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( $admin_dir . 'settings.php' );
		Facebook_Settings::init();

		if ( ! class_exists( 'Facebook_Social_Publisher' ) )
			require_once( $admin_dir . 'social-publisher/social-publisher.php' );
		new Facebook_Social_Publisher();

		if ( ! class_exists( 'Facebook_Mentions_Search' ) )
			require_once( $admin_dir . 'social-publisher/mentions/mentions-search.php' );
		Facebook_Mentions_Search::wp_ajax_handlers();
	}

	/**
	 * Register available widgets
	 *
	 * @since 1.1
	 * @uses register_widget()
	 */
	public function widgets_init() {
		$widget_directory = $this->plugin_directory . 'social-plugins/widgets/';

		foreach ( array(
			'like-button' => 'Facebook_Like_Button_Widget',
			'send-button' => 'Facebook_Send_Button_Widget',
			'subscribe-button' => 'Facebook_Subscribe_Button_Widget',
			'recommendations-box' => 'Facebook_Recommendations_Widget',
			'activity-feed' => 'Facebook_Activity_Feed_Widget'
		) as $filename => $classname ) {
			include_once( $widget_directory . $filename . '.php' );
			register_widget( $classname );
		}
	}

	/**
	 * Map the site locale to a supported Facebook locale
	 * Affects OGP and SDK outputs
	 *
	 * @since 1.1
	 */
	public function set_locale() {
		$transient_key = 'facebook_locale';
		$locale = get_transient( $transient_key );
		if ( $locale ) {
			$this->locale = $locale;
			return;
		}

		$locale = str_replace( '-', '_', get_locale() );

		// convert locales like "es" to "es_ES"
		if ( strlen( $locale ) === 2 ) {
			$locale = strtolower( $locale );
			foreach( self::$locales as $facebook_locale => $exists ) {
				if ( substr_compare( $facebook_locale, $locale, 0, 2 ) === 0 ) {
					$locale = $facebook_locale;
					break;
				}
			}
		}

		// check to see if the locale is a valid FB one, if not, use en_US as a fallback
		if ( ! isset( self::$locales[$locale] ) ) {
			$locale = 'en_US';
		}

		$locale = apply_filters( 'fb_locale', $locale ); // filter the locale in case somebody has a weird case and needs to change it
		if ( $locale ) {
			set_transient( $transient_key, $locale, 60*60*24 );
			$this->locale = $locale;
		}
	}
}

function facebook_loader_init() {
	global $facebook_loader;

	$facebook_loader = new Facebook_Loader();
}
add_action( 'init', 'facebook_loader_init', 0 ); // load before widgets_init at 1

?>
