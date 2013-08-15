<?php
/**
 * @package Facebook
 * @version 1.5
 */
/*
Plugin Name: Facebook
Plugin URI: http://wordpress.org/plugins/facebook/
Description: Add Facebook social plugins and the ability to publish new posts to a Facebook Timeline or Facebook Page. Official Facebook plugin.
Author: Facebook
Author URI: https://developers.facebook.com/wordpress/
Version: 1.5
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
	const VERSION = '1.5';

	/**
	 * Default Facebook locale
	 *
	 * @since 1.1.11
	 * @var string
	 */
	const DEFAULT_LOCALE = 'en_US';

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
	 * Is the current site's primary audience children under the age of 13 in the United States?
	 * Restricts the availability of Facebook social plugins for compliance with United States laws
	 *
	 * @since 1.5
	 * @link https://developers.facebook.com/docs/plugins/restrictions/ Facebook Social Plugin restrictions
	 * @var bool
	 */
	public $kid_directed = false;

	/**
	 * List of locales supported by Facebook.
	 * Two-letter languages codes stored in WordPress are translated to full locales; if a language has multiple country localizations place the first choice earlier in the array to make it the language default
	 * @link https://www.facebook.com/translations/FacebookLocales.xml Facebook locales
	 *
	 * @since 1.1
	 */
	public static $locales = array(
		'af_ZA' => true, // Afrikaans
		'ar_AR' => true, // Arabic
		'az_AZ' => true, // Azerbaijani
		'be_BY' => true, // Belarusian
		'bg_BG' => true, // Bulgarian
		'bn_IN' => true, // Bengali
		'bs_BA' => true, // Bosnian
		'ca_ES' => true, // Catalan
		'cs_CZ' => true, // Czech
		'cy_GB' => true, // Welsh
		'da_DK' => true, // Danish
		'de_DE' => true, // German
		'el_GR' => true, // Greek
		'en_US' => true, // English (US)
		'en_GB' => true, // English (UK)
		'eo_EO' => true, // Esperanto
		'es_LA' => true, // Spanish
		'es_ES' => true, // Spanish (Spain)
		'et_EE' => true, // Estonian
		'eu_ES' => true, // Basque
		'fa_IR' => true, // Persian
		'fi_FI' => true, // Finnish
		'fo_FO' => true, // Faroese
		'fr_FR' => true, // French (France)
		'fr_CA' => true, // French (Canada)
		'fy_NL' => true, // Frisian
		'ga_IE' => true, // Irish
		'gl_ES' => true, // Galician
		'he_IL' => true, // Hebrew
		'hi_IN' => true, // Hindi
		'hr_HR' => true, // Croatian
		'hu_HU' => true, // Hungarian
		'hy_AM' => true, // Armenian
		'id_ID' => true, // Indonesian
		'is_IS' => true, // Icelandic
		'it_IT' => true, // Italian
		'ja_JP' => true, // Japanese
		'ka_GE' => true, // Georgian
		'km_KH' => true, // Khmer
		'ko_KR' => true, // Korean
		'ku_TR' => true, // Kurdish
		'la_VA' => true, // Latin
		'lt_LT' => true, // Lithuanian
		'lv_LV' => true, // Latvian
		'mk_MK' => true, // Macedonian
		'ml_IN' => true, // Malayalam
		'ms_MY' => true, // Malay
		'nb_NO' => true, // Norwegian (bokmal)
		'ne_NP' => true, // Nepali
		'nl_NL' => true, // Dutch
		'nn_NO' => true, // Norwegian (nynorsk)
		'pa_IN' => true, // Punjabi
		'pl_PL' => true, // Polish
		'ps_AF' => true, // Pashto
		'pt_BR' => true, // Portuguese (Brazil)
		'pt_PT' => true, // Portuguese (Portugal)
		'ro_RO' => true, // Romanian
		'ru_RU' => true, // Russian
		'sk_SK' => true, // Slovak
		'sl_SI' => true, // Slovenian
		'sq_AL' => true, // Albanian
		'sr_RS' => true, // Serbian
		'sv_SE' => true, // Swedish
		'sw_KE' => true, // Swahili
		'ta_IN' => true, // Tamil
		'te_IN' => true, // Telugu
		'th_TH' => true, // Thai
		'tl_PH' => true, // Filipino
		'tr_TR' => true, // Turkish
		'uk_UA' => true, // Ukrainian
		'vi_VN' => true, // Vietnamese
		'zh_CN' => true, // Simplified Chinese (China)
		'zh_HK' => true, // Traditional Chinese (Hong Kong)
		'zh_TW' => true  // Traditional Chinese (Taiwan)
	);

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
		$this->kid_directed = (bool) get_option( 'facebook_kid_directed_site' );

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
			add_action( 'init', array( &$this, 'public_early_init' ), 1, 0 );
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
		add_filter( 'script_loader_src', array( 'Facebook_Loader', 'async_script_loader_src' ), 1, 2 );

		$args = array(
			'channelUrl' => plugins_url( 'channel.php', __FILE__ ),
			'xfbml' => true
		);
		if ( is_admin() ) {
			$args['status'] = true;
			$args['cookie'] = true;
		} else if ( $this->kid_directed ) {
			$args['kidDirectedSite'] = true;
		}

		// appId optional
		if ( ! empty( $this->credentials['app_id'] ) )
			$args['appId'] = $this->credentials['app_id'];

		$args = apply_filters( 'facebook_jssdk_init_options', $args );

		// allow the publisher to short circuit the init through the filter
		if ( ! empty( $args ) && isset( $wp_scripts ) ) {
			$wp_scripts->add_data( $handle, 'data', 'var FB_WP=FB_WP||{};FB_WP.queue={_methods:[],flushed:false,add:function(fn){FB_WP.queue.flushed?fn():FB_WP.queue._methods.push(fn)},flush:function(){for(var fn;fn=FB_WP.queue._methods.shift();){fn()}FB_WP.queue.flushed=true}};window.fbAsyncInit=function(){FB.init(' . json_encode( $args ) . ');if(FB_WP && FB_WP.queue && FB_WP.queue.flush){FB_WP.queue.flush()}' . apply_filters( 'facebook_jssdk_init_extras', '', isset( $this->credentials['app_id'] ) ? $this->credentials['app_id'] : '' ) . '}' );
		}
	}

	/**
	 * Proactively resolve Facebook JavaScript SDK domain name asynchronously before later use
	 *
	 * @since 1.1.9
	 * @link http://dev.chromium.org/developers/design-documents/dns-prefetching Chromium prefetch behavior
	 * @link https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching Firefox prefetch behavior
	 */
	public static function dns_prefetch_js_sdk() {
		echo '<link rel="dns-prefetch" href="//connect.facebook.net"';
		if ( ! current_theme_supports('html5') )
			echo ' /';
		echo '>' . "\n";
	}

	/**
	 * Enqueue the JavaScript SDK
	 *
	 * @since 1.1
	 * @uses wp_enqueue_script()
	 */
	public static function enqueue_js_sdk() {
		wp_enqueue_script( 'facebook-jssdk', false, array(), false, true );
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
	public static function async_script_loader_src( $src, $handle ) {
		global $wp_scripts;

		if ( $handle !== 'facebook-jssdk' )
			return $src;

		// @link https://developers.facebook.com/docs/reference/javascript/#loading
		$html = '<div id="fb-root"></div><script type="text/javascript">(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src=' . json_encode($src) . ';fjs.parentNode.insertBefore(js,fjs)}(document,"script","facebook-jssdk"));</script>' . "\n";
		if ( isset( $wp_scripts ) && $wp_scripts->do_concat )
			$wp_scripts->print_html .= $html;
		else
			echo $html;

		// empty out the src response
		// results in extra DOM but nothing to load
		return '';
	}

	/**
	 * Has the current site stored an application identifier, application secret, had the pair verified by Facebook, and stored the resulting application access token?
	 * Access token only saved if WP_HTTP supports HTTPS
	 *
	 * @return bool true if application access token set, else false
	 */
	public function app_access_token_exists() {
		if ( ! empty( $this->credentials['access_token'] ) )
			return true;
		return false;
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

		$facebook_php_sdk = $this->get_php_sdk();
		if ( $facebook_php_sdk ) {
			$facebook = $facebook_php_sdk;
			return true;
		}

		return false;
	}

	/**
	 * Initialize the Facebook PHP SDK using an application identifier and secret
	 *
	 * @since 1.2
	 * @return Facebook_WP_Extend Facebook PHP SDK class or null if minimum requirements not met
	 */
	public function get_php_sdk() {
		if ( empty( $this->credentials['app_id'] ) || empty( $this->credentials['app_secret'] ) )
			return;

		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( $this->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

		return new Facebook_WP_Extend( array(
			'appId' => $this->credentials['app_id'],
			'secret' => $this->credentials['app_secret']
		) );
	}

	/**
	 * Add overrides early in the WordPress loading process for front-end views
	 *
	 * @since 1.3.1
	 */
	public function public_early_init() {
		// add possible comments submission override if Comments Box enabled for one or more post types
		if ( get_option( 'facebook_comments_enabled' ) ) {
			if ( ! class_exists( 'Facebook_Comments' ) )
				require_once( $this->plugin_directory . 'social-plugins/class-facebook-comments.php' );

			// cutoff new comment attempts for post types under management by Comments Box
			add_action( 'pre_comment_on_post', array( 'Facebook_Comments', 'pre_comment_on_post' ), 1, 1 );
		}
	}

	/**
	 * Intialize the public, front end views
	 *
	 * @since 1.1
	 */
	public function public_init() {
		global $facebook_loader;

		// no feed filters yet
		if ( is_feed() || is_404() )
			return;

		// always include Open Graph protocol markup
		if ( ! class_exists( 'Facebook_Open_Graph_Protocol' ) )
			require_once( $this->plugin_directory . 'open-graph-protocol.php' );
		add_action( 'wp_head', array( 'Facebook_Open_Graph_Protocol', 'add_og_protocol' ) );

		add_action( 'wp_head', array( 'Facebook_Loader', 'dns_prefetch_js_sdk' ), 1, 0 );
		add_action( 'wp_enqueue_scripts', array( 'Facebook_Loader', 'enqueue_js_sdk' ) );
		self::plugin_extras();

		// include comment count filters on all pages
		if ( ! $this->kid_directed && get_option( 'facebook_comments_enabled' ) ) {
			if ( ! class_exists( 'Facebook_Comments' ) )
				require_once( $this->plugin_directory . 'social-plugins/class-facebook-comments.php' );

			// treat as if comments are open for post types with comments under management by Comments Box
			add_filter( 'comments_open', array( 'Facebook_Comments', 'comments_open_filter' ), 10, 2 );

			if ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() ) {
				// add Facebook comments count to WordPress comments count
				add_filter( 'get_comments_number', array( 'Facebook_Comments', 'get_comments_number_filter' ), 10, 2 );
			}

			// display comments number XFBML for JS SDK interpretation if used in template
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
		if ( isset( $enabled_features['follow'] ) )
			add_filter( 'the_content', 'facebook_the_content_follow_button', $priority );

		// individual posts, pages, and custom post types features
		if ( isset( $post_type ) && ! $this->kid_directed ) {
			if ( isset( $enabled_features['recommendations_bar'] ) )
				add_filter( 'the_content', 'facebook_the_content_recommendations_bar', $priority );

			// only load comments class and features if enabled and post type supports
			if ( isset( $enabled_features['comments'] ) && post_type_supports( $post_type, 'comments' ) ) {
				if ( ! class_exists( 'Facebook_Comments' ) )
					require_once( $this->plugin_directory . 'social-plugins/class-facebook-comments.php' );

				add_filter( 'comments_template', array( 'Facebook_Comments', 'comments_template' ) );
			}
		}
	}

	/**
	 * Initialize the backend, administrative views
	 *
	 * @since 1.1
	 */
	public function admin_init() {
		$admin_dir = $this->plugin_directory . 'admin/';

		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( $admin_dir . 'settings.php' );
		Facebook_Settings::init();

		if ( $this->app_access_token_exists() ) {
			if ( ! class_exists( 'Facebook_Social_Publisher' ) )
				require_once( $admin_dir . 'social-publisher/social-publisher.php' );
			add_action( 'admin_init', array( 'Facebook_Social_Publisher', 'init' ) );

			if ( ! class_exists( 'Facebook_Mentions_Search' ) )
				require_once( $admin_dir . 'social-publisher/mentions/mentions-search.php' );
			Facebook_Mentions_Search::wp_ajax_handlers();
		}
	}

	/**
	 * Register available widgets
	 *
	 * @since 1.1
	 * @uses register_widget()
	 */
	public function widgets_init() {
		$widget_directory = $this->plugin_directory . 'social-plugins/widgets/';

		$widgets = array(
			'like-button' => 'Facebook_Like_Button_Widget',
			'send-button' => 'Facebook_Send_Button_Widget',
			'follow-button' => 'Facebook_Follow_Button_Widget'
		);
		if ( ! $this->kid_directed ) {
			$widgets['like-box'] = 'Facebook_Like_Box_Widget';
			$widgets['recommendations-box'] = 'Facebook_Recommendations_Widget';
			$widgets['activity-feed'] = 'Facebook_Activity_Feed_Widget';
		}

		foreach ( $widgets as $filename => $classname ) {
			if ( class_exists( $classname ) ) {
				register_widget( $classname );
			} else {
				$file = $widget_directory . $filename . '.php';
				if ( file_exists( $file ) ) {
					include_once( $file );
					if ( class_exists( $classname ) )
						register_widget( $classname );
				}
				unset( $file );
			}
		}
	}

	/**
	 * Test if a given locale is a valid Facebook locale
	 *
	 * @since 1.1.11
	 * @see Facebook_Loader::$locales
	 * @param @param string $locale language and localization combined in a single string. ISO 639-1 (alpha-2) language + underscore character (_) + ISO 3166-1 (alpha-2) country code. example: en_US, es_ES
	 * @return bool true if locals in list of valid locales. else false
	 */
	public static function is_valid_locale( $locale ) {
		if ( is_string( $locale ) && isset( self::$locales[$locale] ) )
			return true;
		return false;
	}

	/**
	 * Sanitize a locale input against a list of Facebook-specific locales
	 *
	 * @since 1.1.11
	 * @param string $locale language and localization combined in a single string. The function will attempt to convert an ISO 639-1 (alpha-2) language or a language combined with a ISO 3166-1 (alpha-2) country code separated by a dash or underscore. examples: en, en-US, en_US
	 * @return string a Facebook-friendly locale
	 */
	public static function sanitize_locale( $locale ) {
		if ( ! is_string( $locale ) )
			return self::DEFAULT_LOCALE;

		$locale_length = strlen( $locale );
		if ( ! ( $locale_length === 2 || $locale_length === 5 ) )
			return self::DEFAULT_LOCALE;

		// convert locales like "es" to "es_ES"
		if ( $locale_length === 2 ) {
			if ( ! ctype_alpha( $locale ) )
				return self::DEFAULT_LOCALE;

			$locale = strtolower( $locale );
			foreach( self::$locales as $facebook_locale => $exists ) {
				if ( substr_compare( $facebook_locale, $locale, 0, 2 ) === 0 )
					return $facebook_locale;
			}

			// no ISO 639-1 match found
			return self::DEFAULT_LOCALE;
		}
		unset( $locale_length );

		$lang = substr( $locale, 0, 2 );
		if ( ! ctype_alpha( $lang ) )
			return self::DEFAULT_LOCALE;

		$localization = substr( $locale, 3, 2 );
		if ( ! ctype_alpha( $localization ) )
			return self::DEFAULT_LOCALE;

		// rebuild based on expectations
		return strtolower( $lang ) . '_' . strtoupper( $localization );
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

		// sanitize the locale. e.g. en-US to en_US
		// filter the result in case a site would like to override
		$locale = apply_filters( 'fb_locale', self::sanitize_locale( get_locale() ) );

		// validate our sanitized value and a possible filter override
		if ( ! self::is_valid_locale( $locale ) )
			$locale = self::DEFAULT_LOCALE;

		set_transient( $transient_key, $locale, 60*60*24 );
		$this->locale = $locale;
	}

	/**
	 * Tie-in to popular site features handled by popular WordPress plugins
	 *
	 * @since 1.1.9
	 */
	public static function plugin_extras() {
		// add Google Analytics social trackers
		if ( defined( 'GOOGLE_ANALYTICATOR_VERSION' ) && function_exists( 'add_google_analytics' ) && has_action( 'wp_head', 'add_google_analytics' ) !== false ) {
			if ( ! class_exists( 'Facebook_Google_Analytics' ) )
				require_once( dirname(__FILE__) . '/extras/google-analytics.php' );
			add_action( 'google_analyticator_extra_js_after', array( 'Facebook_Google_Analytics', 'enqueue' ) );
		}
		if ( ( defined( 'GAWP_VERSION' ) && class_exists( 'GA_Filter' ) && has_action( 'wp_head', array( 'GA_Filter', 'spool_analytics' ) ) !== false ) ) {
			if ( ! class_exists( 'Facebook_Google_Analytics' ) )
				require_once( dirname(__FILE__) . '/extras/google-analytics.php' );
			add_filter( 'yoast-ga-push-after-pageview', array( 'Facebook_Google_Analytics', 'gaq_filter' ) );
		}
	}

	/**
	 * Useful for blanking a string filter
	 *
	 * @since 1.3
	 * @return string empty string
	 */
	public static function __return_empty_string() {
		return '';
	}
}

/**
 * Load plugin function during the WordPress init action
 *
 * @since 1.1
 */
function facebook_loader_init() {
	global $facebook_loader;

	$facebook_loader = new Facebook_Loader();
}
add_action( 'init', 'facebook_loader_init', 0 ); // load before widgets_init at 1

?>
