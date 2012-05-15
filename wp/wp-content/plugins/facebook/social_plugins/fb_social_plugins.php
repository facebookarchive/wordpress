<?php

/**
 * Add social plugins through filters
 * Individual social plugin files contain both administrative setting fields and display code
 */
function fb_apply_filters() {
	$options = get_option('fb_options');

	/*if (isset($options['recommendations_bar'])) {
		add_filter('the_content', 'fb_recommendations_bar_automatic', 30);
		require_once('fb_recommendations_bar.php');
	}
	add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recommendations" );'));
	require_once('fb_recommendations.php');

	require_once('fb_activity_feed.php');
	add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Activity_Feed" );'));
	*/

	require_once( dirname(__FILE__) . '/fb_like.php' );
	if ( array_key_exists( 'like', $options ) && array_key_exists( 'enabled', $options['like'] ) && $options['like']['enabled'] ) {
		add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Like_Button" );') );
		add_filter( 'the_content', 'fb_like_button_automatic', 30 );
	}

	require_once( dirname(__FILE__) . '/fb_send.php' );
	if ( array_key_exists( 'send', $options ) && array_key_exists( 'enabled', $options['send'] ) && $options['send']['enabled'] ) {
		add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Send_Button" );') );
		add_filter( 'the_content', 'fb_send_button_automatic', 30 );
	}

	require_once( dirname(__FILE__) . '/fb_subscribe.php' );
	if ( array_key_exists( 'subscribe', $options ) && array_key_exists( 'enabled', $options['subscribe'] ) && $options['subscribe']['enabled'] ) {
		add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Subscribe_Button" );') );
		add_filter( 'the_content', 'fb_subscribe_button_automatic', 30 );
	}

	require_once( dirname(__FILE__) . '/fb_comments.php' );
	if ( array_key_exists( 'comments', $options ) && array_key_exists( 'enabled', $options['comments'] ) && $options['comments']['enabled'] ) {
		add_filter( 'the_content', 'fb_comments_automatic', 30 );
		add_filter( 'comments_array', 'fb_close_wp_comments' );
		add_filter( 'the_posts', 'fb_set_wp_comment_status' );
		//add_filter( 'comments_open', 'fb_close_wp_comments', 10, 2 );
		//add_filter( 'pings_open', 'fb_close_wp_comments', 10, 2 );
		add_action( 'wp_footer', 'fb_hide_wp_comments', 30 );
	}
}
add_action( 'init', 'fb_apply_filters' );
?>