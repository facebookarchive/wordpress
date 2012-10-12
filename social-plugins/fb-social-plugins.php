<?php

require_once( dirname(__FILE__) . '/fb-activity-feed.php');
require_once( dirname(__FILE__) . '/fb-recommendations.php');
require_once( dirname(__FILE__) . '/fb-like.php' );
require_once( dirname(__FILE__) . '/fb-send.php' );
require_once( dirname(__FILE__) . '/fb-subscribe.php' );
require_once( dirname(__FILE__) . '/fb-comments.php' );
require_once( dirname(__FILE__) . '/fb-recommendations-bar.php' );

/**
 * Register our widgets for display in theme customization menus
 *
 * @since 1.0.3
 */
function fb_register_widgets() {
	foreach( array( 'Facebook_Subscribe_Button_Widget', 'Facebook_Send_Button_Widget', 'Facebook_Like_Button_Widget', 'Facebook_Recommendations_Widget', 'Facebook_Activity_Feed_Widget' ) as $widget_class ) {
		register_widget( $widget_class );
	}
}
add_action( 'widgets_init', 'fb_register_widgets' );

/**
 * Has the publisher enabled the given feature?
 *
 * @param string $feature Facebook feature slug as stored in options
 * @param array $options existing options
 */
function fb_feature_enabled( $feature, $options=array() ) {
	if ( ! is_string( $feature ) && $feature )
		return false;

	if ( ! is_array( $options ) || empty( $options ) ) {
		$options = get_option( 'fb_options' );
		if ( ! is_array( $options ) || empty( $options ) )
			return false;
	}

	if ( array_key_exists( $feature, $options ) && array_key_exists( 'enabled', $options[$feature] ) && $options[$feature]['enabled'] )
		return true;

	return false;
}

/**
 * Add social plugins through filters
 * Individual social plugin files contain both administrative setting fields and display code
 */
function fb_apply_filters() {
	if ( is_admin() )
		return;

	$options = get_option('fb_options');

	if ( ! is_array( $options ) )
		return;


	if ( fb_feature_enabled( 'recommendations', $options ) ) {
		add_filter( 'the_content', 'fb_recommendations_bar_automatic', 30 );
	}

	if ( fb_feature_enabled( 'like', $options ) ) {
		add_filter( 'the_content', 'fb_like_button_automatic', 30 );
	}

	if ( fb_feature_enabled( 'send', $options ) ) {
		add_filter( 'the_content', 'fb_send_button_automatic', 30 );
	}

	if ( fb_feature_enabled( 'subscribe', $options ) ) {
		add_filter( 'the_content', 'fb_subscribe_button_automatic', 30 );
	}

	if ( fb_feature_enabled( 'comments', $options ) ) {
		// only load comments class and features if enabled
		if ( ! class_exists( 'Facebook_Comments' ) )
			require_once( dirname(__FILE__) . '/class-facebook-comments.php' );

		add_filter( 'the_content', 'Facebook_Comments::maybe_render', 30 );
		add_action( 'wp_head', 'Facebook_Comments::css_hide_comments', 0 );
		add_filter( 'comments_array', create_function( '', 'return null;' ) );
		add_filter( 'comments_open', create_function( '', 'return true;' ) );
	if ( isset($options['comments']['homepage_comments']['enabled']) ) {
		add_filter( 'comments_number', 'Facebook_Comments::comments_count_xfbml' );

		// short-circuit special template behavior for comment count = 0
		// prevents linking to #respond anchor which leads nowhere
		add_filter( 'get_comments_number', create_function('', 'return -1;') );
	} else {
		add_filter( 'comments_number', create_function( '', 'return "";' ) );
	}
	}
}
add_action( 'init', 'fb_apply_filters' );

/**
 * Should the Facebook plugin display a Facebook social plugin given the current view context?
 *
 * @since 1.0.3
 * @param string $social_plugin_type the type of social plugin. should match the array key of options array
 * @return bool true if plugin should be shown in current context; else false
 */
function fb_show_social_plugin( $social_plugin_type ) {
	global $post;

	if ( ! isset( $post ) || ! is_string( $social_plugin_type ) || ! $social_plugin_type )
		return false;

	$options = fb_load_social_plugin_options( $social_plugin_type );
	if ( empty( $options ) )
		return false;

	if ( ( is_front_page() || is_home() ) && ! array_key_exists( 'show_on_homepage', $options ) ) {
		// are we on the homepage without the show on homepage option?
		return false;
	} else {
		$post_override = get_post_meta( $post->ID, 'fb_social_plugin_settings_box_' . $social_plugin_type, true );
		if ( ! $post_override || $post_override === 'default' ) {
			if ( ! array_key_exists( 'show_on', $options ) || ! is_array( $options['show_on'] ) )
				return false;

			if ( ! array_key_exists( get_post_type( $post ), $options['show_on'] ) )
				return false;
		}
		unset( $post_override );
	}

	return true;
}

/**
 * Load the subset of plugin options specific to a social plugin
 *
 * @since 1.0.3
 * @param string $social_plugin_type the type of social plugin. should match the array key of options array
 * @return array associative array of plugin options
 */
function fb_load_social_plugin_options( $social_plugin_type ) {
	if ( ! is_string( $social_plugin_type ) )
		return array();

	$options = get_option('fb_options');

	if ( ! ( is_array( $options ) && array_key_exists( $social_plugin_type, $options ) && is_array( $options[$social_plugin_type] ) ) )
		return array();

	return $options[$social_plugin_type];
}

function fb_build_social_plugin_params($options, $plugin = '' ) {
	$params = '';

	if ( 'like' == $plugin ) {
		if ( ! isset( $options['send'] ) || empty( $options['send'] ) ) {
			$params .= 'data-send="false" ';
		}
		if ( ! isset( $options['show_faces'] ) || empty( $options['show_faces'] ) ) {
			$params .= 'data-show-faces="false" ';
		}
	}

	foreach ($options as $option => $value) {
		$option = str_replace('_', '-', $option);

		$params .= 'data-' . $option . '="' . esc_attr($value) . '" ';
	}

	if ( ! array_key_exists( 'ref', $options ) )
		$params .= 'data-ref="wp" ';

	return rtrim( $params, ' ' );
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

	$post_types = get_post_types(array('public' => true));
	unset($post_types['attachment']);
	$post_types = array_values($post_types);
	foreach ( $post_types as $post_type ) {
		add_meta_box(
			'fb_social_plugin_settings_box_id',
			__( 'Facebook Social Plugins', 'facebook' ),
			'fb_add_social_plugin_settings_box_content',
			$post_type
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
	echo '<table><p>' . esc_html( __( 'Change the settings below to show or hide particular Social Plugins.', 'facebook' ) ) . '</p>';
	foreach ( $features as $feature ) {
		if ( isset ( $options[ $feature ]['enabled'] ) ) {
			$value = get_post_meta($post->ID,"fb_social_plugin_settings_box_$feature",true);
			echo '<tr><td>' . fb_option_name( $feature ) . '</td><td><label><input type="radio" name="fb_social_plugin_settings_box_' . $feature . '" value="default" '
				. ( $value == 'default' || empty($value) ? 'checked="checked" ' : '' ) . ' />' . esc_html( __( 'Default', 'facebook' ) ) . ' (' . esc_html( isset($options[$feature]['show_on']) && isset($options[$feature]['show_on'][$post->post_type]) ? _x( 'Show', 'action: show what was hidden', 'facebook' ) : _x( 'Hide', 'verb: hide from view', 'facebook' ) ) . ')</label></td> <td><label><input type="radio" name="fb_social_plugin_settings_box_' . $feature . '" value="show" '
				. ( $value == 'show' ? 'checked="checked" ' : '' ) . ' />' . esc_html( _x( 'Show', 'action: show what was hidden', 'facebook' ) ) . '</label></td><td><label><input type="radio" name="fb_social_plugin_settings_box_' . $feature . '" value="hide" '
				. ( $value == 'hide'  ? 'checked="checked" ' : '' ) . ' />' . esc_html( _x( 'Hide', 'verb: hide from view', 'facebook' ) ) . '</label></td></tr>' ;
		}
	}
	echo '</table><p class="howto">'. esc_html( sprintf( __( 'If \'%s\' is selected, the Social Plugin will appear based on the global setting set on the Facebook Settings page.', 'facebook' ), 'Default' ) ) . ' ' . esc_html( sprintf( __( 'If you choose "%1$s" or "%2$s", the Social Plugin will ignore the global setting for this %3$s.', 'facebook' ), _x( 'Show', 'action: show what was hidden', 'facebook' ), _x( 'Hide', 'verb: hide from view', 'facebook' ), $post->post_type ) ) . '</p>';
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
