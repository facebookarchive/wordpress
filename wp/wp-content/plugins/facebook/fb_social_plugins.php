<?php
require_once('fb_social_plugin_like.php');
require_once('fb_social_plugin_send.php');
require_once('fb_social_plugin_subscribe.php');
require_once('fb_social_plugin_recent_activity.php');
require_once('fb_social_plugin_recommendations.php');
require_once('fb_social_plugin_recommendations_bar.php');
require_once('fb_social_plugin_comments.php');

add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Like_Button" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Send_Button" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Subscribe_Button" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recent_Activity" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recommendations" );'));

function fb_apply_filters() {
	$options = get_option('fb_options');
	
	if (isset($options['recommendations_bar'])) {
		
		add_filter('the_content', 'fb_recommendations_bar_automatic', 30);
	}
	
	if (isset($options['like'])) {
		add_filter('the_content', 'fb_like_button_automatic', 30);
	}
	
	if (isset($options['send'])) {
		add_filter('the_content', 'fb_send_button_automatic', 30);
	}
	
	if (isset($options['subscribe'])) {
		add_filter('the_content', 'fb_subscribe_button_automatic', 30);
	}
	
	if (isset($options['comments'])) {
		add_filter('the_content', 'fb_comments_automatic', 30);
		add_filter('comments_array', 'fb_close_wp_comments');
		add_filter('the_posts', 'fb_set_wp_comment_status');
		add_filter('comments_open', 'fb_close_wp_comments', 10, 2);
		add_filter('pings_open', 'fb_close_wp_comments', 10, 2);
		add_action('wp_footer', 'fb_hide_wp_comments', 30);
	}
}

fb_apply_filters();
?>