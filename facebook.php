<?php
/**
 * @package Facebook
 * @version 1.0.3
 */
/*
Plugin Name: Facebook
Plugin URI: http://wordpress.org/extend/plugins/facebook/
Description: Facebook for WordPress. Make your site deeply social in just a couple of clicks.
Author: Facebook
Author URI: https://developers.facebook.com/wordpress/
Version: 1.0.3
License: GPL2
License URI: license.txt
Domain Path: /lang/
*/

global $fb_ver;
$fb_ver = '1.0.2';

$facebook_plugin_directory = dirname(__FILE__);

// Load the textdomain for translations
add_action('init', 'fb_load_textdomain');
function fb_load_textdomain() {
	load_plugin_textdomain( 'facebook', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

// include the Facebook PHP SDK
if ( ! class_exists( 'Facebook_WP' ) )
	require_once( $facebook_plugin_directory . '/includes/facebook-php-sdk/class-facebook-wp.php' );

require_once( $facebook_plugin_directory . '/fb-core.php' );
require_once( $facebook_plugin_directory . '/fb-admin-menu.php');
require_once( $facebook_plugin_directory . '/fb-open-graph.php');
require_once( $facebook_plugin_directory . '/social-plugins/fb-social-plugins.php');
require_once( $facebook_plugin_directory . '/fb-login.php' );
require_once( $facebook_plugin_directory . '/fb-social-publisher.php' );
require_once( $facebook_plugin_directory . '/fb-wp-helpers.php' );
require_once( $facebook_plugin_directory . '/social-plugins/fb_comment_to_wpdb.php' );

unset( $facebook_plugin_directory );

register_uninstall_hook( __FILE__, 'fb_uninstall' );

function fb_uninstall() {
    
    $meta_keys = array('state', 'code', 'access_token', 'user_id', 'fb_data');
    
    foreach ( $meta_keys as $meta_key ) {
        delete_user_meta( get_current_user_id(), $meta_key );    
    }
    
    delete_option( 'fb_options' );
    delete_option( 'fb_flush_rewrite_rules' );
}

