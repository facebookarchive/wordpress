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
		add_action( 'wp_enqueue_scripts', 'fb_hide_wp_comments', 0);
    if ( isset($options['comments']['homepage_comments']['enabled']) ) {
      add_filter( 'comments_number', 'fb_get_comments_count' );
    } else {
      add_filter( 'comments_number', 'fb_hide_wp_comments_homepage' );
    }
	}
}
add_action( 'init', 'fb_apply_filters' );

function fb_build_social_plugin_params($options, $plugin = '' ) {
	$params = '';
    
    if ( 'like' == $plugin ) {
        if ( ! isset( $options['send'] ) || empty( $options['send'] ) ) {
            $params .= 'data-send="false"';
        }
        if ( ! isset( $options['show_faces'] ) || empty( $options['show_faces'] ) ) {
            $params .= 'data-show-faces="false"';
        }
    }

	foreach ($options as $option => $value) {
		$option = str_replace('_', '-', $option);

		$params .= 'data-' . $option . '="' . esc_attr($value) . '" ';
    }
    
	$params .= 'data-ref="wp" ';

	return $params;
}

if ( true ) {
	add_action( 'add_meta_boxes', 'fb_add_social_plugin_settings_box' );
	add_action( 'save_post', 'fb_add_social_plugin_settings_box_save' );
}

/**
 * Add meta box for social plugin settings for individual posts and pages
 *
 * @since 1.0.2
 */
function fb_add_social_plugin_settings_box() {
	global $post;
	$options = get_option('fb_options');

	if ( true ) {
		add_meta_box(
				'fb_social_plugin_settings_box_id',
				__( 'Facebook Social Plugins', 'facebook' ),
				'fb_add_social_plugin_settings_box_content',
				'post'
		);
		add_meta_box(
				'fb_social_plugin_settings_box_id',
				__( 'Facebook Social Plugins', 'facebook' ),
				'fb_add_social_plugin_settings_box_content',
				'page'
		);
	}
}

/**
 * Add meta boxes for a custom Status that is used when posting to an Author's Timeline
 *
 * @since 1.0.2
 */
function fb_add_social_plugin_settings_box_content( $post ) {
	$options = get_option('fb_options');

	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	echo '<table><p>Change the settings below to show or hide particular Social Plugins. </p>';
	foreach ( $features as $feature ) {
		if ( isset ( $options[ $feature ]['enabled'] ) ) {
			$value = get_post_meta($post->ID,"fb_social_plugin_settings_box_$feature",true);
			echo '<tr><td>' . fb_option_name( $feature ) . "</td> <td><label><input type = \"radio\" name=\"fb_social_plugin_settings_box_$feature\" value=\"default\" "
				. ( $value == 'default' || empty($value) ? 'checked="checked" ' : '' ) . "/>Default (" . $options[$feature]['show_on'] . ")</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"show\" "
				. ( $value == 'show' ? 'checked="checked" ' : '' ) . "/>Show</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"hide\" "
				. ( $value == 'hide'  ? 'checked="checked" ' : '' ) . "/>Hide</label></td> </tr>" ;
		}
	}
	echo '</table><p class="howto"> If \'Default\' is selected, the Social Plugin will appear based on the global setting, set on the Facebook Settings page.  If you choose "Show" or "Hide", the Social Plugin will ignore the global setting for this ' . ( $post->post_type == 'page' ? 'page' : 'post' ) . '.</p>';
}

function fb_add_social_plugin_settings_box_save( $post_id ) {
	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	foreach ( $features as $feature ) {
		$index = "fb_social_plugin_settings_box_$feature";
		if ( isset( $_POST) && isset( $_POST[$index] )) {
			switch ( $_POST[ $index ]) {
				case 'default':
					update_post_meta( $post_id, $index, 'default' );
					break;
				case 'show':
					update_post_meta( $post_id, $index, 'show' );
					break;
				case 'hide':
					update_post_meta( $post_id, $index, 'hide' );
					break;
			}
		}
	}
}


/*
 function fb_add_social_plugin_settings_box_content( $post ) {
	$options = get_option('fb_options');

	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	echo '<table><p>Change the settings below to show or hide particular Social Plugins.</p>';
	foreach ( $features as $feature ) {
		if ( $options[$feature]['enabled'] ) {
			$value = get_post_meta( $post->ID, "fb_social_plugin_settings_box_$feature", true );

			if ( empty( $value ) ) {
				if ( $post->post_type == 'page' ) {
					if ( $options[$feature]['show_on'] == 'all posts and pages' || $options[$feature]['show_on'] == 'all pages' ) {
						$value = 'show';
					}
					else {
						$value = 'hide';
					}
				}
				else {
					if ( $options[$feature]['show_on'] == 'all posts and pages' || $options[$feature]['show_on'] == 'all posts' ) {
						$value = 'show';
					}
					else {
						$value = 'hide';
					}
				}
			}

			echo '<tr><td>' . fb_option_name( $feature ) . "</td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"show\" "
				. ( $value == 'show' ? 'checked="checked" ' : '' ) . "/>Show</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"hide\" "
				. ( $value == 'hide'  ? 'checked="checked" ' : '' ) . "/>Hide</label></td> </tr>" ;
		}
	}
	echo '</table>';
}

function fb_add_social_plugin_settings_box_save( $post_id ) {
	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	foreach ( $features as $feature ) {
		$index = "fb_social_plugin_settings_box_$feature";
		if ( isset( $_POST) && isset( $_POST[$index] )) {
			switch ( $_POST[$index]) {
				case 'show':
					update_post_meta( $post_id, $index, 'show' );
					break;
				case 'hide':
					update_post_meta( $post_id, $index, 'hide' );
					break;
			}
		}
	}
}
*/
?>
