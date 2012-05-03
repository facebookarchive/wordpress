<?php
/*
 * This is the main code for the SFC Base system. It's included by the main "Simple Facebook Connect" plugin.
 */

// Load the textdomain
add_action('init', 'sfc_load_textdomain');
function sfc_load_textdomain() {
	load_plugin_textdomain('sfc', false, dirname(plugin_basename(__FILE__)));
}

global $sfc_plugin_list;
$sfc_plugin_list = array(
	'plugin_login'=>'sfc-login.php',
	'plugin_like'=>'sfc-like.php',
	'plugin_publish'=>'sfc-publish.php',
	'plugin_widgets'=>'sfc-widgets.php',
	'plugin_comments'=>'sfc-comments.php',
	'plugin_getcomm'=>'sfc-getcomm.php',
	'plugin_register'=>'sfc-register.php',
	'plugin_share'=>'sfc-share.php',
	'plugin_photos'=>'sfc-photos.php',
);

global $sfc_plugin_descriptions;
$sfc_plugin_descriptions = array(
	'plugin_login'		=>__('Login with Facebook','sfc'),
	'plugin_register'	=>__('User registration (must also enable Login)','sfc'),
	'plugin_like'		=>__('Like Button','sfc'),
	'plugin_share'		=>__('Share Button','sfc'),
	'plugin_publish'	=>__('Publisher (send posts to Facebook)','sfc'),
	'plugin_widgets'	=>__('Sidebar widgets (enables all widgets, use the ones you want)','sfc'),
	'plugin_comments'	=>__('Allow FB Login to Comment (for non-registered users)','sfc'),
	'plugin_getcomm'	=>__('Integrate FB Comments (needs automatic publishing enabled)','sfc'),
	'plugin_photos'		=>__('Photo Posting (integrate FB Photo Albums into the Media display)','sfc'),
	
);

// load all the subplugins
add_action('plugins_loaded','sfc_plugin_loader');
function sfc_plugin_loader() {
	global $sfc_plugin_list;
	$options = get_option('sfc_options');
	if (!empty($options)) foreach ($options as $key=>$value) {
		if ($value === 'enable' && array_key_exists($key, $sfc_plugin_list)) {
			include_once($sfc_plugin_list[$key]);
		}
	}
}

// fix up the html tag to have the FBML extensions
add_filter('language_attributes','sfc_lang_atts');
function sfc_lang_atts($lang) {
    return ' xmlns:fb="http://ogp.me/ns/fb#" xmlns:og="http://ogp.me/ns#" '.$lang;
}

// basic XFBML load into footer
add_action('wp_footer','sfc_add_base_js',20); // 20, to put it at the end of the footer insertions. sub-plugins should use 30 for their code
function sfc_add_base_js($args=array()) {
	$options = get_option('sfc_options');
	sfc_load_api($options['appid'],$args);
};

function sfc_get_locale() {
	// allow locale overrides using SFC_LOCALE define in the wp-config.php file
	if ( defined( 'SFC_LOCALE' ) ) {
		$locale = SFC_LOCALE;
	} else {
		// validate that they're using a valid locale string
		$sfc_valid_fb_locales = array(
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
		
		// TODO make a locale conversion list, perhaps?
		
		// check to see if the locale is a valid FB one, if not, use en_US as a fallback
		if ( !in_array($locale, $sfc_valid_fb_locales) ) {
			$locale = 'en_US';
		}
	}
	
	return $locale;
}

function sfc_load_api($appid, $args=array()) {
	$locale = sfc_get_locale();

	$defaults = array(
		'appId'=>$appid,
		'channelUrl'=>home_url('?sfc-channel-file=1'),
		'status'=>true, 
		'cookie'=>true, 
		'xfbml'=>true,
		'oauth'=>true,
	);
	
	$args = wp_parse_args($args,$defaults);
?>
<div id="fb-root"></div>
<script type="text/javascript">
  window.fbAsyncInit = function() {
    FB.init(<?php echo json_encode($args); ?>);
    <?php do_action('sfc_async_init'); // do any other actions sub-plugins might need to do here ?>
  };
  (function(d){
       var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
       js = d.createElement('script'); js.id = id; js.async = true;
       js.src = "//connect.facebook.net/<?php echo $locale; ?>/all.js";
       d.getElementsByTagName('head')[0].appendChild(js);
   }(document));     
</script>
<?php
}

add_action('init','sfc_channel_file');
function sfc_channel_file() {
	if (!empty($_GET['sfc-channel-file'])) {
		$cache_expire = 60*60*24*365;
		header("Pragma: public");
		header("Cache-Control: max-age=".$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/'.sfc_get_locale().'/all.js"></script>';
		exit;
	}
}

// add the admin settings and such
add_action('admin_init', 'sfc_admin_init',9); // 9 to force it first, subplugins should use default
function sfc_admin_init(){
	$options = get_option('sfc_options');
	if (empty($options['app_secret']) || empty($options['appid'])) {
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".sprintf(__('Simple Facebook Connect needs configuration information on its <a href="%s">settings</a> page.', 'sfc'), admin_url('options-general.php?page=sfc'))."</p></div>';" ) );
	} else {
		add_action('admin_print_footer_scripts','sfc_add_base_js',20);
	}
	wp_enqueue_script('jquery');
	register_setting( 'sfc_options', 'sfc_options', 'sfc_options_validate' );
	add_settings_section('sfc_main', __('Main Settings', 'sfc'), 'sfc_section_text', 'sfc');
	if (!defined('SFC_APP_ID')) add_settings_field('sfc_appid', __('Facebook Application ID', 'sfc'), 'sfc_setting_appid', 'sfc', 'sfc_main');
	if (!defined('SFC_APP_SECRET')) add_settings_field('sfc_app_secret', __('Facebook Application Secret', 'sfc'), 'sfc_setting_app_secret', 'sfc', 'sfc_main');
	if (!defined('SFC_FANPAGE')) add_settings_field('sfc_fanpage', __('Facebook Fan Page', 'sfc'), 'sfc_setting_fanpage', 'sfc', 'sfc_main');

	add_settings_section('sfc_plugins', __('SFC Modules', 'sfc'), 'sfc_plugins_text', 'sfc');
	add_settings_field('sfc_subplugins', __('Modules', 'sfc'), 'sfc_subplugins', 'sfc', 'sfc_plugins');
	
	add_settings_section('sfc_meta', __('Facebook Metadata', 'sfc'), 'sfc_meta_text', 'sfc');
	add_settings_field('sfc_default_image', __('Default Image', 'sfc'), 'sfc_default_image', 'sfc', 'sfc_meta');
	add_settings_field('sfc_default_description', __('Default Description', 'sfc'), 'sfc_default_description', 'sfc', 'sfc_meta');
}

// include the help stuff
include 'sfc-help.php';

// add the admin options page
add_action('admin_menu', 'sfc_admin_add_page');
function sfc_admin_add_page() {
	global $sfc_options_page;
	$sfc_options_page = add_options_page(__('Simple Facebook Connect', 'sfc'), __('Simple Facebook Connect', 'sfc'), 'manage_options', 'sfc', 'sfc_options_page');
	add_action("load-$sfc_options_page", 'sfc_plugin_help');
}

// display the admin options page
function sfc_options_page() {
?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Simple Facebook Connect', 'sfc'); ?></h2>
	<p><?php _e('Options relating to the Simple Facebook Connect plugins.', 'sfc'); ?> </p>
	<form method="post" action="options.php">
	<?php settings_fields('sfc_options'); ?>
	<table><tr><td>
	<?php do_settings_sections('sfc'); ?>
	</td><td style='vertical-align:top;'>
	<div id='sfc-about' style='width:20em; float:right; background: #ffc; border: 1px solid #333; margin: 2px; padding: 5px'>
			<h3 align='center'><?php _e('About the Author', 'sfc'); ?></h3>
		<p><a href="http://ottopress.com/blog/wordpress-plugins/simple-facebook-connect/">Simple Facebook Connect</a> is developed and maintained by <a href="http://ottodestruct.com">Otto</a>.</p>
			<p>He blogs at <a href="http://ottodestruct.com">Nothing To See Here</a> and <a href="http://ottopress.com">Otto on WordPress</a>, posts photos on <a href="http://www.flickr.com/photos/otto42/">Flickr</a>, and chats on <a href="http://twitter.com/otto42">Twitter</a>.</p>
			<p>You can follow his site on either <a href="https://www.facebook.com/pages/Nothing-to-See-Here/241409175928000">Facebook</a> or <a href="http://twitter.com/ottodestruct">Twitter</a>, if you like.</p>
			<p>If you'd like to <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom">buy him a beer</a>, then he'd be perfectly happy to drink it.</p>
		</div>
<?php /*
	<div style='width:20em; float:right; background: #fff; border: 1px solid #333; margin: 2px; padding: 5px'>
		<h3 align='center'><?php _e('Facebook Platform Status', 'sfc'); ?></h3>
		<?php @wp_widget_rss_output('http://www.facebook.com/feeds/api_messages.php',array('show_date' => 1, 'items' => 10) ); ?>
	</div>
*/ ?>
	</td></tr></table>
	<?php submit_button(); ?>
	</form>
	</div>

<?php
}

function sfc_section_text() {
	$options = get_option('sfc_options');
	if (empty($options['app_secret']) || empty($options['appid'])) {
?>
<p><?php _e('To connect your site to Facebook, you will need a Facebook Application.
If you have already created one, please insert your Application Secret and Application ID below.', 'sfc'); ?></p>
<p><strong><?php _e('Can\'t find your key?', 'sfc'); ?></strong></p>
<ol>
<li><?php _e('Get a list of your applications from here: <a target="_blank" href="https://developers.facebook.com/apps">Facebook Application List</a>', 'sfc'); ?></li>
<li><?php _e('Select the application you want, then copy and paste the Application Secret and Application ID from there.', 'sfc'); ?></li>
</ol>

<p><strong><?php _e('Haven\'t created an application yet?', 'sfc'); ?></strong> <?php _e('Don\'t worry, it\'s easy!', 'sfc'); ?></p>
<ol>
<li><?php _e('Go to this link to create your application: <a target="_blank" href="https://developers.facebook.com/apps">Facebook Application Setup</a>', 'sfc'); ?></li>
<li><?php $home = home_url('/'); _e("After creating the application, put <strong>{$home}</strong> in as the Site URL in the Website section.", 'sfc'); ?></li>
<li><?php _e('You can get the API information from the application on the
<a target="_blank" href="https://developers.facebook.com/apps">Facebook Application List</a> page.', 'sfc'); ?></li>
<li><?php _e('Select the application you created, then copy and paste the Application Secret and Application ID from there.', 'sfc'); ?></li>
</ol>
<?php
	}
}

// this will override all the main options if they are pre-defined
function sfc_override_options($options) {
	if (defined('SFC_APP_SECRET')) $options['app_secret'] = SFC_APP_SECRET;
	if (defined('SFC_APP_ID')) $options['appid'] = SFC_APP_ID;
	if (defined('SFC_FANPAGE')) $options['fanpage'] = SFC_FANPAGE;
	return $options;
}
add_filter('option_sfc_options', 'sfc_override_options');

function sfc_setting_app_secret() {
	if (defined('SFC_APP_SECRET')) return;
	$options = get_option('sfc_options');
	echo "<input type='text' id='sfcappsecret' name='sfc_options[app_secret]' value='{$options['app_secret']}' size='40' /> ";
	_e('(required)', 'sfc');
}

function sfc_setting_appid() {
	if (defined('SFC_APP_ID')) return;
	$options = get_option('sfc_options');
	echo "<input type='text' id='sfcappid' name='sfc_options[appid]' value='{$options['appid']}' size='40' /> ";
	_e('(required)', 'sfc');
}

function sfc_setting_fanpage() {
	if (defined('SFC_FANPAGE')) return;
	$options = get_option('sfc_options'); ?>

<p><?php _e('If you use a Fan Page for your site, you can fill in the ID number of the Fan Page here. To get the ID number, go to the Fan Page on Facebook,
find the "Edit Page" link, and click it. The Fan Page ID number will be in the URL of the Edit page.', 'sfc'); ?></p>
<?php
	echo "<input type='text' id='sfcfanpage' name='sfc_options[fanpage]' value='{$options['fanpage']}' size='40' /> (optional)";
}

function sfc_plugins_text() {
?>
<p><?php _e('SFC is a modular system. Click the checkboxes by the sub-plugins of SFC that you want to use. All of these are optional.', 'sfc'); ?></p>
<?php
}

function sfc_subplugins() {
	global $sfc_plugin_descriptions;
	$options = get_option('sfc_options');
	if ($options['appid']) {
	
		foreach($sfc_plugin_descriptions as $key=>$val) {
		?>
		<p><label><input type="checkbox" name="sfc_options[<?php echo $key; ?>]" value="enable" <?php @checked('enable', $options[$key]); ?> /> <?php echo $val; ?></label></p>
		<?php
		}
	do_action('sfc_subplugins');
	}
}

function sfc_meta_text() {
?>
<p><?php _e('SFC automatically populates your site with OpenGraph meta tags for Facebook and other sites to use for things like sharing and publishing.', 'sfc'); ?></p>
<?php
}

function sfc_default_image() {
	$options = get_option('sfc_options');
	?>
	<p><label><?php _e('SFC will automatically choose images from your content if they are available. When they are not available, you can specify the URL to a default image to use here.','sfc'); ?><br />
	<input type="text" name="sfc_options[default_image]" value="<?php echo esc_url($options['default_image']); ?>" size="80" placeholder="http://example.com/path/to/image.jpg"/></label></p>
	<?php
}

function sfc_default_description() {
	$options = get_option('sfc_options');
	?>
	<p><label><?php _e('SFC will automatically create descriptions for single post pages based on the excerpt of the content. For other pages, you can put in a default description here.','sfc'); ?><br />
	<textarea cols="80" rows="3" name="sfc_options[default_description]"><?php echo esc_textarea($options['default_description']); ?></textarea></label></p>
	<?php
}

// validate our options
function sfc_options_validate($input) {
	if (!defined('SFC_APP_SECRET')) {
		// secrets are 32 bytes long and made of hex values
		$input['app_secret'] = trim($input['app_secret']);
		if(! preg_match('/^[a-f0-9]{32}$/i', $input['app_secret'])) {
		  $input['app_secret'] = '';
		}
	}

	if (!defined('SFC_APP_ID')) {
		// app ids are big integers
		$input['appid'] = trim($input['appid']);
		if(! preg_match('/^[0-9]+$/i', $input['appid'])) {
		  $input['appid'] = '';
		}
	}

	if (!defined('SFC_FANPAGE')) {
		// fanpage ids are big integers
		$input['fanpage'] = trim($input['fanpage']);
		if(! preg_match('/^[0-9]+$/i', $input['fanpage'])) {
		  $input['fanpage'] = '';
		}
	}

	$input = apply_filters('sfc_validate_options',$input); // filter to let sub-plugins validate their options too
	return $input;
}

// the cookie is signed using our application secret, so it's unfakable as long as you don't give away the secret
function sfc_cookie_parse() {
	$options = get_option('sfc_options');
	$args = array();
	
	if (!empty($_COOKIE['fbsr_'. $options['appid']])) {
		if (list($encoded_sig, $payload) = explode('.', $_COOKIE['fbsr_'. $options['appid']], 2) ) {
			$sig = sfc_base64_url_decode($encoded_sig);  
			if (hash_hmac('sha256', $payload, $options['app_secret'], true) == $sig) {
				$args = json_decode(sfc_base64_url_decode($payload), true);
			}
		}
	}
	
	return $args;
}

// this is not a hack or a dangerous function.. the base64 decode is required because Facebook is sending back base64 encoded data in the signed_request bits. 
// See http://developers.facebook.com/docs/authentication/signed_request/ for more info
function sfc_base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}


// this function checks if the current FB user is a fan of your page.
// Returns true if they are, false otherwise.
function sfc_is_fan($pageid='0') {
	$user = sfc_cookie_parse();
	if (!isset($user['user_id'])) {
		return false; // user isn't "connected", so we don't know who they are, so we can't check to see if they're a fan
	}

	$options = get_option('sfc_options');

	if ($pageid == '0') {
		if (!empty($options['fanpage'])) $pageid = $options['fanpage'];
		else $pageid = $options['appid'];
	}

	if (!empty($options['fanpage'])) $token = $options['page_access_token'];
	else $token = $options['app_access_token'];

	$fbresp = sfc_remote($user['user_id'], "likes/{$pageid}", array('access_token'=>$token));

	if ( isset( $fbresp['data'][0]['name'] ) ) {
		return true;
	} else {
		return false;
	}
}

function sfc_remote($obj, $connection='', $args=array(), $type = 'GET') {

	// save the access tokens for later use in the same request
	static $saved_access_tokens;
	
	if (empty($args['access_token']) && isset($saved_access_tokens[$obj]) && $saved_access_tokens[$obj] = $obj) {
		$args['access_token'] = $saved_access_tokens[$obj];
	}
	
	$options = get_option('sfc_options');
	
	// get the access token
	if (empty($args['access_token']) && !empty($args['code'])) {
		$resp = wp_remote_get("https://graph.facebook.com/oauth/access_token?client_id={$options['appid']}&redirect_uri=&client_secret={$options['app_secret']}&code={$args['code']}");	
		if (!is_wp_error($resp) && 200 == wp_remote_retrieve_response_code( $resp )) {
			$args['access_token'] = str_replace('access_token=','',$resp['body']);
			$saved_access_tokens[$obj] = $args['access_token'];
		} else {
			return false;
		}
	}
	
	$type = strtoupper($type);
	
	if (empty($obj)) return null;
		
	$url = 'https://graph.facebook.com/'. $obj;
	if (!empty($connection)) $url .= '/'.$connection;
	if ($type == 'GET') $url .= '?'.http_build_query($args);
	$args['sslverify']=0;

	if ($type == 'POST') {
		$data = wp_remote_post($url, $args);
	} else if ($type == 'GET') {
		$data = wp_remote_get($url, $args);
	} 
	
	if ($data && !is_wp_error($data)) {
		$resp = json_decode($data['body'],true);
		return $resp;
	}
	
	return false;
}

// code to create a pretty excerpt given a post object
function sfc_base_make_excerpt($post) { 
	
	if ( !empty($post->post_excerpt) ) 
		$text = $post->post_excerpt;
	else 
		$text = $post->post_content;
	
	$text = strip_shortcodes( $text );

	// filter the excerpt or content, but without texturizing
	if ( empty($post->post_excerpt) ) {
		remove_filter( 'the_content', 'wptexturize' );
		$text = apply_filters('the_content', $text);
		add_filter( 'the_content', 'wptexturize' );
	} else {
		remove_filter( 'the_excerpt', 'wptexturize' );
		$text = apply_filters('the_excerpt', $text);
		add_filter( 'the_excerpt', 'wptexturize' );
	}

	$text = str_replace(']]>', ']]&gt;', $text);
	$text = wp_strip_all_tags($text);
	$text = str_replace(array("\r\n","\r","\n"),' ',$text);

	$excerpt_more = apply_filters('excerpt_more', '[...]');
	$excerpt_more = html_entity_decode($excerpt_more, ENT_QUOTES, 'UTF-8');
	$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	$text = htmlspecialchars_decode($text);
	
	$max = min(1000,apply_filters('sfc_excerpt_length',1000));
	$max -= strlen ($excerpt_more) + 1;
	$max -= strlen ('</fb:intl>') * 2 - 1;

	if ($max<1) return ''; // nothing to send
	
	if (strlen($text) >= $max) {
		$text = substr($text, 0, $max);
		$words = explode(' ', $text);
		array_pop ($words);
		array_push ($words, $excerpt_more);
		$text = implode(' ', $words);
	}

	return $text;
}


// add the media handlers
include 'sfc-media.php';

// add meta tags for *everything*
add_action('wp_head','sfc_base_meta');
function sfc_base_meta() {
	global $post;
	
	$fbmeta = array();
	
	$options = get_option('sfc_options');
	// exclude bbPress post types 
	if ( function_exists('bbp_is_custom_post_type') && bbp_is_custom_post_type() ) return;

	$excerpt = '';
	if (is_singular()) {
	
		global $wp_the_query;
		if ( $id = $wp_the_query->get_queried_object_id() ) {
			$post = get_post( $id );
		}
		
		// get the content from the main post on the page
		$content = sfc_base_make_excerpt($post);

		$title = get_the_title($post->ID);
		$title = strip_tags($title);
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
		$title = htmlspecialchars_decode($title);
		
		$permalink = get_permalink();
		
		$fbmeta['og:type'] = 'article';
		$fbmeta['og:title'] = esc_attr($title);
		$fbmeta['og:url'] = esc_url($permalink);
		$fbmeta['og:description'] = esc_attr($content);

	} else { // non singular pages need images and descriptions too
		if (!empty($options['default_image'])) {
			$fbmeta['og:image'][] = $options['default_image'];
		}
		if (!empty($options['default_description'])) { 
			$fbmeta['og:description'] = esc_attr($options['default_description']);
		}
	}
		
	if (is_home()) {
		$fbmeta['og:type'] = 'blog';
		$fbmeta['og:title'] = get_bloginfo("name");
		$fbmeta['og:url'] = esc_url(get_bloginfo("url"));
	}
	
	// stuff on all pages
	$fbmeta['og:site_name'] = get_bloginfo("name");
	if (!empty($options["appid"])) $fbmeta['fb:app_id'] = esc_attr($options["appid"]);
	$fbmeta['og:locale'] = sfc_get_locale();
	
	$fbmeta = apply_filters('sfc_base_meta',$fbmeta, $post);
	
	foreach ($fbmeta as $prop=>$content) {
		if (is_array($content)) {
			foreach ($content as $item) {
				echo "<meta property='{$prop}' content='{$item}' />\n";
				if ($prop == 'og:image') echo "<link rel='image_src' href='{$item}' />\n";
			}
		} else {
			echo "<meta property='{$prop}' content='{$content}' />\n";
			if ($prop == 'og:image') echo "<link rel='image_src' href='{$content}' />\n";
		}
	}
}

// finds a item from an array in a string
if (!function_exists('straipos')) :
function straipos($haystack,$array,$offset=0)
{
   $occ = array();
   for ($i = 0;$i<sizeof($array);$i++)
   {
       $pos = strpos($haystack,$array[$i],$offset);
       if (is_bool($pos)) continue;
       $occ[$pos] = $i;
   }
   if (sizeof($occ)<1) return false;
   ksort($occ);
   reset($occ);
   list($key,$value) = each($occ);
   return array($key,$value);
}
endif;

function sfc_pointer_enqueue( $hook_suffix ) {
	global $sfc_options_page;
	if ( $hook_suffix != $sfc_options_page ) return;
	
	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

	if ( ! in_array( 'sfc-help', $dismissed ) ) {
		$enqueue = true;
		add_action( 'admin_print_footer_scripts', '_sfc_pointer' );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}
}
add_action( 'admin_enqueue_scripts', 'sfc_pointer_enqueue' );

function _sfc_pointer() {
	$pointer_content  = '<h3>' . __('Help is available!', 'sfc') . '</h3>';
	$pointer_content .= '<p>' . __('Make sure to check the Help dropdown box for information on installing and using Simple Facebook Connect.','sfc') . '</p>';
?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready( function($) {
	$('#contextual-help-link-wrap').pointer({
		content: '<?php echo $pointer_content; ?>',
		position: {
			edge:  'top',
			align: 'right'
		},
		pointerClass: 'sfc-help-pointer',
		close: function() {
			$.post( ajaxurl, {
					pointer: 'sfc-help',
				//	_ajax_nonce: $('#_ajax_nonce').val(),
					action: 'dismiss-wp-pointer'
			});
		}
	}).pointer('open');
	
	$(window).resize(function() {
		if ( $('.sfc-help-pointer').is(":visible") ) $('#contextual-help-link-wrap').pointer('reposition');
	});
	
	$('#contextual-help-link-wrap').click( function () {
		setTimeout( function () {
			$('#contextual-help-link-wrap').pointer('reposition');
		}, 1000);
	});
});
//]]>
</script>
<style>
.sfc-help-pointer .wp-pointer-arrow {
	right:10px;
	left:auto;
}
</style>
<?php
}
