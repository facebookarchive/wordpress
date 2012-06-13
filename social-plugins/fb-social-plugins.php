<?php

require_once( dirname(__FILE__) . '/fb-activity-feed.php');
require_once( dirname(__FILE__) . '/fb-recommendations.php');
require_once( dirname(__FILE__) . '/fb-like.php' );
require_once( dirname(__FILE__) . '/fb-send.php' );
require_once( dirname(__FILE__) . '/fb-subscribe.php' );
require_once( dirname(__FILE__) . '/fb-comments.php' );
require_once( dirname(__FILE__) . '/fb-recommendations-bar.php' );

add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Subscribe_Button" );'), 100 );
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Send_Button" );'), 100 );
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Like_Button" );') );
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recommendations" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Activity_Feed" );'));

/**
 * Add social plugins through filters
 * Individual social plugin files contain both administrative setting fields and display code
 */
function fb_apply_filters() {
	$options = get_option('fb_options');

	if ( ! is_array( $options ) )
		return;


	if ( array_key_exists( 'recommendations_bar', $options ) && array_key_exists( 'enabled', $options['recommendations_bar'] ) && $options['recommendations_bar']['enabled'] ) {
		add_filter('the_content', 'fb_recommendations_bar_automatic', 30);
	}

	if ( array_key_exists( 'like', $options ) && array_key_exists( 'enabled', $options['like'] ) && $options['like']['enabled'] ) {
		add_filter( 'the_content', 'fb_like_button_automatic', 30 );
	}

	if ( array_key_exists( 'send', $options ) && array_key_exists( 'enabled', $options['send'] ) && $options['send']['enabled'] ) {
		add_filter( 'the_content', 'fb_send_button_automatic', 30 );
	}

	if ( array_key_exists( 'subscribe', $options ) && array_key_exists( 'enabled', $options['subscribe'] ) && $options['subscribe']['enabled'] ) {
		add_filter( 'the_content', 'fb_subscribe_button_automatic', 30 );
	}

	if ( array_key_exists( 'comments', $options ) && array_key_exists( 'enabled', $options['comments'] ) && $options['comments']['enabled'] ) {
		add_filter( 'the_content', 'fb_comments_automatic', 30 );
		add_filter( 'comments_array', 'fb_close_wp_comments' );
		add_filter( 'the_posts', 'fb_set_wp_comment_status' );
		add_action( 'wp_enqueue_scripts', 'fb_hide_wp_comments' );
		add_filter( 'comments_number', 'fb_get_comments_count' );
	}
}
add_action( 'init', 'fb_apply_filters' );

function fb_build_social_plugin_params($options) {
	$params = '';

	foreach ($options as $option => $value) {
		$option = str_replace('_', '-', $option);

		$params .= 'data-' . $option . '="' . esc_attr($value) . '" ';
	}

	$params .= 'data-ref="wp" ';

	return $params;
}
?>