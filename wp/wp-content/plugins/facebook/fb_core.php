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
add_action ( 'get_footer', 'fb_js_init' );

add_action ( 'wp_head', 'fb_add_og_protocol' );

function fb_add_og_protocol() {
	print '<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
     <meta property="og:type"                 content="article"> 
     <meta property="og:site_name"            content="' . esc_attr(get_bloginfo( 'name' )) . '">
     <meta property="og:image"                content="' . get_header_image() .'">
		 <meta property="og:url"                  content="' . get_permalink() .'">
     <meta property="og:title"                content="' . get_bloginfo( 'name', 'display' ) . '">
     <meta property="og:description"          content="' . get_bloginfo( 'description', 'display' ) . '"> 
     <meta property="article:published_time"  content="' . get_the_date( 'c' ) . '"> 
     <meta property="article:modified_time"   content="' . get_the_date( 'c' ) . '"> 
     <meta property="article:expiration_time" content="' . get_the_date( 'c' ) . '"> 
     <meta property="article:author"          content="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">
     <meta property="article:section"         content="">
     <meta property="article:tag"             content="">';
		 
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