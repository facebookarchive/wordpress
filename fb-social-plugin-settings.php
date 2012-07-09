<?php
require_once( $facebook_plugin_directory . '/fb-wp-helpers.php');

$options = get_option('fb_options');

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
				__( 'Social Plugin Settings', 'facebook' ),
				'fb_add_social_plugin_settings_box_content',
				'post'
		);
		add_meta_box(
				'fb_social_plugin_settings_box_id',
				__( 'Social Plugin Settings', 'facebook' ),
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
	echo '<table>';
	foreach ( $features as $feature ) {
		if ( $options[ $feature ]['enabled'] ) {
			$value = get_post_meta($post->ID,"fb_social_plugin_settings_box_$feature",true);
			echo '<tr><td>' . fb_option_name( $feature ) . "</td> <td><label><input type = \"radio\" name=\"fb_social_plugin_settings_box_$feature\" value=\"default\" "
				. ( $value == 'default' || empty($value) ? 'checked="checked" ' : '' ) . "/>Default</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"yes\" "
				. ( $value == 'yes' ? 'checked="checked" ' : '' ) . "/>Yes</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"no\" "
				. ( $value == 'no'  ? 'checked="checked" ' : '' ) . "/>No</label></td> </tr>" ;
		}
	}
	echo '</table>';
}

function fb_add_social_plugin_settings_box_save( $post_id ) {
	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	foreach ( $features as $feature ) {
		$index = "fb_social_plugin_settings_box_$feature";
		switch ( $_POST[ $index ]) {
			case 'default':
				update_post_meta( $post_id, $index, 'default' );
				break;
			case 'yes':
				update_post_meta( $post_id, $index, 'yes' );
				break;
			case 'no':
				update_post_meta( $post_id, $index, 'no' );
				break;
		}
	}
}

?>