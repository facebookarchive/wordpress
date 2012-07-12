<?php
add_action( 'init', 'fb_init' );
add_action( 'admin_notices', 'fb_install_warning' );
add_action( 'admin_notices', 'fb_ssl_warning' );
//add_action( 'admin_notices', 'fb_rate_message' );
add_action( 'wp_enqueue_scripts', 'fb_style' );

/**
 * Display an admin-facing warning if the current user hasn't authenticated with Facebook yet
 *
 * @since 1.0
 */
function fb_install_warning() {
	$options = get_option('fb_options');

	$page = (isset($_GET['page']) ? $_GET['page'] : null);

	if ((empty($options['app_id']) || empty($options['app_secret'])) && $page != 'facebook-settings' && current_user_can( 'manage_options' ) ) {
		fb_admin_dialog( sprintf( __('You must %sconfigure the plugin%s to enable Facebook for WordPress.', 'facebook' ), '<a href="admin.php?page=facebook-settings">', '</a>' ), true);
	}
}

/**
 * Display an admin-facing warning if openSSL is not installed properly
 *
 * @since 1.0.2
 */
function fb_ssl_warning() {
	$options = get_option( 'fb_options' );

	$page = (isset($_GET['page']) ? $_GET['page'] : null);

	if ( ! wp_http_supports( array( 'ssl' => true ) )  && current_user_can( 'manage_options' ) ) {
		$msg = 'SSL must be enabled on your server for Facebook Social Publisher to work.';
		if ( $options['social_publisher']['enabled'] ) {
			unset($options['social_publisher']['enabled']);
			update_option( 'fb_options', $options );
			$msg .= ' As a result, Social Publisher has been disabled.';
		}
		fb_admin_dialog( __( $msg, 'facebook' ), true );
	}
}

/**
 * Display an admin-facing message to rate the plugin
 *
 * @since 1.0
 */
/*
function fb_rate_message() {
	$options = get_option('fb_options');
	
  global $current_user;
  
	$user_id = $current_user->ID;
	
	$page = (isset($_GET['page']) ? $_GET['page'] : null);

	if ( !empty($options['app_id']) && !empty( $options['app_secret'] ) && current_user_can( 'publish_posts' ) && !get_user_meta( $user_id, 'fb_rate_message_ignore_notice', true )
      && ( !empty( $options['social_publisher'] ) || !empty( $options['like_button'] ) || !empty( $options['subscribe_button'] ) || !empty( $options['send_button'] ) || !empty( $options['comments'] ) || !empty( $options['recommendations_bar'] ) ) ) {
    $like_button_options = array(
       "enabled" => "true", 
       "send" => "true", 
       "layout" => "button_count", 
       "action" => "like", 
       "colorscheme" => "light", 
       "font" => "arial", 
       "position" => "both", 
       "ref" => "wp",
       "href" => "http://developers.facebook.com/wordpress",
    );
    
		fb_admin_dialog( sprintf( __( '%1$sEnjoying the Facebook plugin? Please like it, %2$srate it,  and mark it as working%3$s! Having a problem? %4$sReport it%5$s. &nbsp;|&nbsp; %6$sDismiss%7$s' ), fb_get_like_button($like_button_options), '<a href="http://wordpress.org/extend/plugins/facebook/" target="_blank">', '</a>', '<a href="http://wordpress.org/support/plugin/facebook" target="_blank">', '</a>', '<a href="' . get_admin_url() . '?fb_rate_message_ignore=1' . '">', '</a>' ), false);
	}
}

add_action('admin_init', 'fb_rate_message_ignore');
function fb_rate_message_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	
	if ( isset($_GET['fb_rate_message_ignore']) && '1' == $_GET['fb_rate_message_ignore'] ) {
		fb_update_user_meta($user_id, 'fb_rate_message_ignore_notice', 'true');
	}
}
*/

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
	global $facebook;
	
	$options = get_option( 'fb_options' );

	if ( empty( $options['app_id'] ) )
		return;
	
	// appId and secret are required by BaseFacebook
	if ( ( ! empty( $options['app_id'] ) && ! empty( $options['app_secret'] ) ) ) {
		$facebook = new Facebook_WP_Extend(array(
			'appId'  => $options['app_id'],
			'secret' => $options['app_secret'],
		));
	}

	add_action( 'wp_head', 'fb_js_sdk_setup' );
	add_action( 'admin_head', 'fb_js_sdk_setup' );

}


/**
 * Expose the cross-domain channel needed to make Facebook Platform API calls
 *
 * @since 1.0
 */
add_action( 'init', 'fb_register_rewrite_rule' );
function fb_register_rewrite_rule() {
	add_rewrite_rule( '^fb-channel-file/?', 'index.php?fb-channel-file=true', 'top' );
}

add_action( 'query_vars', 'fb_filter_query_vars' );
function fb_filter_query_vars( $query_vars ) {
	$query_vars[] = 'fb-channel-file';
	return $query_vars;
}

add_action( 'template_redirect', 'fb_handle_channel_file' );
function fb_handle_channel_file() {
	if ( get_query_var( 'fb-channel-file' ) ) {
		$cache_expire = 60 * 60 * 24 * 365;
		header('Pragma: public');
		header('Cache-Control: max-age='.$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/en/all.js"></script>';
		die();
	}
}

add_action( 'admin_init', 'fb_flush_rewrite_rules' );
function fb_flush_rewrite_rules() {
	if ( !get_option( 'fb_flush_rewrite_rules' ) ) {
		flush_rewrite_rules( false );
		update_option( 'fb_flush_rewrite_rules', 1 );
	}
}

function fb_channel_file_link() {
	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() )
		echo home_url( '/fb-channel-file/' );
	else
		echo add_query_arg( 'fb-channel-file', 'true', home_url() );
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

	return apply_filters('fb_locale', $locale); // filter the locale in case somebody has a weird case and needs to change it
}

/**
 * Set styles for elements like Social Plugins
 *
 * @since 1.0
 */
function fb_style() {
	wp_enqueue_style( 'fb', plugins_url( 'style/style.css', __FILE__), array(), '1.0' );
}
