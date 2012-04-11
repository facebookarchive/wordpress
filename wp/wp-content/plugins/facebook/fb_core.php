<?php
/**
 * @package Facebook
 * @version 0.1a
 */
/*
Plugin Name: Facebook
Plugin URI: [TODO]
Description: [TODO]
Author: Facebook, Matt Kelly
Version: 0.1a
Author URI: http://developers.facebook.com/
License: [TODO]
*/

require_once('fb_admin_menu.php');
require_once('fb_social_plugins.php');

//wp_enqueue_script('fb_js_sdk', plugins_url('/js/fb.js', __FILE__));

add_action ( 'wp_loaded', 'fb_js_init' );

function fb_js_init() {
	print '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=413161128711224";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>';
}
?>