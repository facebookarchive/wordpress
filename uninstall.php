<?php
/**
 * Remove data written by the Facebook plugin for WordPress after an administrative user clicks "Delete" from the plugin management page in wp-admin.
 *
 * @since 1.0.3
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

foreach ( array('state', 'code', 'access_token', 'user_id', 'fb_data') as $meta_key ) {
	delete_user_meta( get_current_user_id(), $meta_key );
}

delete_option( 'fb_options' );
delete_option( 'fb_flush_rewrite_rules' );
?>