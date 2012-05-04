<?php
/**
 * @package Facebook for WordPress
 * @version 1.0
 */
/*
Plugin Name: Facebook
Plugin URI: [TODO]
Description: [TODO]
Author: Facebook: Matt Kelly (matthwk)
Version: 1.0
Author URI: http://developers.facebook.com/
License: [TODO]
*/

$options = get_option('fb_options');

global $facebook;

require_once('includes/facebook_php_sdk/src/facebook.php');

// Create our Application instance (replace this with your appId and secret)
if ((!empty($options["app_id"]) || !empty($options["app_secret"]))) {
	$facebook = new Facebook(array(
		'appId'  => $options["app_id"],
		'secret' => $options["app_secret"],
	));
}

require_once('fb_admin_menu.php');
require_once('fb_open_graph.php');
require_once('social_plugins/fb_social_plugins.php');
require_once('fb_login.php');
require_once('fb_social_publisher.php');
require_once('fb_wp_helpers.php');

add_action('wp_footer','fb_add_base_js',20);
add_action('init','fb_channel_file');

function fb_install_warning() {
	$options = get_option('fb_options');
	
	$page = (isset($_GET['page']) ? $_GET['page'] : null);
	
	if ( ( empty($options["app_id"]) || empty($options["app_secret"])) && $page != 'facebook/fb_admin_menu.php' ) {
		fb_admin_dialog( __( 'You must <a href="facebook">configure the plugin</a> to enable Facebook for WordPress.', 'facebook' ), true);
	}
}
add_action('admin_notices', 'fb_install_warning');

function fb_add_base_js($args = array()) {
	$options = get_option('fb_options');
	
	fb_init($options['app_id'], $args);
};

//add_filter('language_attributes','fb_lang_atts');

/*
 //disabled so that the site is HTML5-compliant, and it's not needed for social plugins any more
 function fb_lang_atts($lang) {
	return ' xmlns:fb="http://ogp.me/ns/fb#" xmlns:og="http://ogp.me/ns#" '. $lang;
}*/

function fb_init($app_id, $args = array()) {
	$locale = fb_get_locale();

	$defaults = array(
		'appId'=>$app_id,
		'channelUrl'=>home_url('?fb-channel-file=1'),
		'status'=>true, 
		'cookie'=>true, 
		'xfbml'=>true,
		'oauth'=>true,
	);
	
	$args = wp_parse_args($args, $defaults);

	echo '<div id="fb-root"></div>
	<script type="text/javascript">
		window.fbAsyncInit = function() {
			FB.init(' .  json_encode($args) . ');
			' . do_action('fb_async_init') . '
		};
		(function(d){
				 var js, id = "facebook-jssdk"; if (d.getElementById(id)) {return;}
				 js = d.createElement("script"); js.id = id; js.async = true;
				 js.src = "//connect.facebook.net/' .  $locale . '/all.js";
				 d.getElementsByTagName("head")[0].appendChild(js);
		 }(document));
	</script>';
}

function fb_channel_file() {
	if (!empty($_GET['fb-channel-file'])) {
		$cache_expire = 60 * 60 * 24 * 365;
		header("Pragma: public");
		header("Cache-Control: max-age=".$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/' . fb_get_locale() . '/all.js"></script>';
		exit;
	}
}

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

?>