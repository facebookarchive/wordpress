<?php
/**
 * @package Facebook
 * @version 1.0
 */
/*
Plugin Name: Facebook
Plugin URI: http://wordpress.org/extend/plugins/facebook/
Description: Facebook for WordPress.  Deep social integration in just a couple of clicks.
Author: facebook, mattwkelly, niallkennedy, jamesgpearce, Otto42
Version: 1.0
Author URI: http://developers.facebook.com/wordpress
License: GPL
*/

global $facebook;

$options = get_option( 'fb_options' );

$facebook_plugin_directory = dirname(__FILE__);

if ( ! class_exists( 'Facebook_WP' ) )
	require_once( $facebook_plugin_directory . '/includes/facebook-php-sdk/class-facebook-wp.php' );

// appId and secret are required by BaseFacebook
if ( ( ! empty( $options['app_id'] ) && ! empty( $options['app_secret'] ) ) ) {
	$facebook = new Facebook_WP(array(
		'appId'  => $options['app_id'],
		'secret' => $options['app_secret'],
	));
}

require_once( $facebook_plugin_directory . '/fb-admin-menu.php');
require_once( $facebook_plugin_directory . '/fb-open-graph.php');
require_once( $facebook_plugin_directory . '/social-plugins/fb-social-plugins.php');
require_once( $facebook_plugin_directory . '/fb-login.php' );
require_once( $facebook_plugin_directory . '/fb-social-publisher.php' );
require_once( $facebook_plugin_directory . '/fb-wp-helpers.php' );
unset( $facebook_plugin_directory );

add_action( 'init', 'fb_init' );
add_action( 'init', 'fb_channel_file' );
add_action( 'admin_notices', 'fb_install_warning' );
add_action( 'wp_enqueue_scripts', 'fb_style' );

/**
 * Display an admin-facing warning if the current user hasn't authenticated with Facebook yet
 *
 * @since 1.0
 */
function fb_install_warning() {
	$options = get_option('fb_options');

	$page = (isset($_GET['page']) ? $_GET['page'] : null);

	if ((empty($options['app_id']) || empty($options['app_secret'])) && $page != 'facebook/fb-admin-menu.php' && current_user_can( 'manage_options' ) ) {
		fb_admin_dialog( sprintf( __('You must <a href="%s">configure the plugin</a> to enable Facebook for WordPress.', 'facebook' ), 'admin.php?page=facebook/fb-admin-menu.php' ), true);
	}
}

/**
 * Inits the Facebook JavaScript SDK.
 *
 * @since 1.0
 */
function fb_js_sdk_setup() {
	$options = get_option( 'fb_options' );

	if ( empty( $options['app_id'] ) )
		return;

	$args = apply_filters( 'fb_init', array(
		'appId' => $options['app_id'],
		'channelUrl' => add_query_arg( 'fb-channel-file', 1, site_url( '/' ) ),
		'status' => true,
		'cookie' => true,
		'xfbml' => true,
		'oauth' => true
	) );

	// enforce minimum requirements
	if ( empty( $args['appId'] ) )
		return;

	echo '<script type="text/javascript">window.fbAsyncInit=function(){FB.init(' . json_encode( $args ) . ');';
	do_action( 'fb_async_init', $args );
	echo '}</script>';

	$locale = fb_get_locale();
	if ( ! $locale )
		return;
	wp_enqueue_script( 'fb-connect', ( is_ssl() ? 'https' : 'http' ) . '://connect.facebook.net/' . $locale . '/all.js', array(), null, true );

	add_action( 'wp_footer', 'fb_root' );
}

/**
 * Adds a root element for the Facebook JavaScript SDK
 * This is required
 *
 * @since 1.0
 */
function fb_root() {
	echo '<div id="fb-root"></div>';
}

/**
 * Initialize the plugin and its hooks.
 *
 * @since 1.0
 */
function fb_init() {
	$options = get_option( 'fb_options' );

	if ( empty( $options['app_id'] ) )
		return;

	add_action( 'wp_head', 'fb_js_sdk_setup' );
	add_action( 'admin_head', 'fb_js_sdk_setup' );
}

/**
 * Expose the cross-domain channel needed to make Facebook Platform API calls
 *
 * @since 1.0
 */
function fb_channel_file() {
	if (!empty($_GET['fb-channel-file'])) {
		$cache_expire = 60 * 60 * 24 * 365;
		header('Pragma: public');
		header('Cache-Control: max-age='.$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/' . fb_get_locale() . '/all.js"></script>';
		exit;
	}
}

/**
 * Get the locale and set it for the Facebook SDK
 *
 * @since 1.0
 */
function fb_get_locale() {
	$fb_valid_fb_locales = array(
		'ca_ES', 'cs_CZ', 'cy_GB', 'da_DK', 'de_DE', 'eu_ES', 'en_PI', 'en_UD', 'ck_US', 'en_US', 'es_LA', 'es_CL', 'es_CO', 'es_ES', 'es_MX',
		'es_VE', 'fb_FI', 'fi_FI', 'fr_FR', 'gl_ES', 'hu_HU', 'it_IT', 'ja_JP', 'ko_KR', 'nb_NO', 'nn_NO', 'nl_NL', 'pl_PL', 'pt_BR', 'pt_PT',
		'ro_RO', 'ru_RU', 'sk_SK', 'sl_SI', 'sv_SE', 'th_TH', 'tr_TR', 'ku_TR', 'zh_CN', 'zh_HK', 'zh_TW', 'fb_LT', 'af_ZA', 'sq_AL', 'hy_AM',
		'az_AZ', 'be_BY', 'bn_IN', 'bs_BA', 'bg_BG', 'hr_HR', 'nl_BE', 'en_GB', 'eo_EO', 'et_EE', 'fo_FO', 'fr_CA', 'ka_GE', 'el_GR', 'gu_IN',
		'hi_IN', 'is_IS', 'id_ID', 'ga_IE', 'jv_ID', 'kn_IN', 'kk_KZ', 'la_VA', 'lv_LV', 'li_NL', 'lt_LT', 'mk_MK', 'mg_MG', 'ms_MY', 'mt_MT',
		'mr_IN', 'mn_MN', 'ne_NP', 'pa_IN', 'rm_CH', 'sa_IN', 'sr_RS', 'so_SO', 'sw_KE', 'tl_PH', 'ta_IN', 'tt_RU', 'te_IN', 'ml_IN', 'uk_UA',
		'uz_UZ', 'vi_VN', 'xh_ZA', 'zu_ZA', 'km_KH', 'tg_TJ', 'ar_AR', 'he_IL', 'ur_PK', 'fa_IR', 'sy_SY', 'yi_DE', 'gn_PY', 'qu_PE', 'ay_BO',
		'se_NO', 'ps_AF', 'tl_ST'
	);

	$locale = get_locale();

	// convert locales like "es" to "es_ES", in case that works for the given locale (sometimes it does)
	if (strlen($locale) == 2) {
		$locale = strtolower($locale).'_'.strtoupper($locale);
	}

	// convert things like de-DE to de_DE
	$locale = str_replace('-', '_', $locale);

	// check to see if the locale is a valid FB one, if not, use en_US as a fallback
	if ( !in_array($locale, $fb_valid_fb_locales) ) {
		$locale = 'en_US';
	}

	return $locale;
}

/**
 * Set styles for elements like Social Plugins
 *
 * @since 1.0
 */
function fb_style() {
	wp_enqueue_style( 'fb', plugins_url( 'style/style.css', __FILE__), array(), '1.0' );
}

?>