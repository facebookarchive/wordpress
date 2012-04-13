<?php
/**
 * @package Facebook
 * @version 0.1a
 */
/*
Plugin Name: Facebook
Plugin URI: [TODO]
Description: [TODO]
Author: Facebook: Matt Kelly (matthwk), James Pearce (jamesgpearce)
Version: 0.1a
Author URI: http://developers.facebook.com/
License: [TODO]
*/

require_once('fb_admin_menu.php');
require_once('fb_social_plugins.php');

//wp_enqueue_script('fb_js_sdk', plugins_url('/js/fb.js', __FILE__));
//add_action ( 'get_footer', 'fb_js_init' );

add_action ( 'wp_head', 'fb_add_og_protocol' );

function fb_add_og_protocol() {
	print '<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
     <meta property="og:type"                 content="article"> 
     <meta property="og:site_name"            content="' . esc_attr(get_bloginfo( 'name' )) . '">
     <meta property="og:image"                content="' . get_header_image() .'">
     <meta property="og:title"                content="' . wp_title( '|', true, 'right' ) . '">
     <meta property="og:description"          content="' . bloginfo( 'description' ) . '"> 
     <meta property="article:published_time"  content="DateTime"> 
     <meta property="article:modified_time"   content="DateTime"> 
     <meta property="article:expiration_time" content="DateTime">
     <meta property="article:author"          content="URL to Author object">
     <meta property="article:section"         content="Section of article">
     <meta property="article:tag"             content="Keyword">';
		 
}

function fb_js_init() {
	print '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>';
}
?>