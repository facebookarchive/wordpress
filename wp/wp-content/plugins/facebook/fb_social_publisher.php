<?php

//hook into new posts action

//publish OG action for new post

//publish to fan page, if defined


function fb_post_to_fb_page($post_id) {
	global $facebook;

	$options = get_option('fb_options');

	$fan_page_fb_id = $options['social_publisher']['publish_to_fan_page'];

	if ( ! isset( $facebook ) )
		return;

	try {
		$publish = $facebook->api('/' . $fan_page_fb_id . '/feed', 'POST', array('from' => $fan_page_fb_id, 'source' => get_permalink($post_id)));

		return $user;
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e));
	}
}


function fb_get_social_publisher_fields() {
	$parent = array('name' => 'social_publisher',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
									);

	$children = array(array('name' => 'publish_to_authors_facebook_profile',
													'field_type' => 'dropdown',
													'options' => array('standard', 'button_count', 'box_count'),
													'help_text' => 'Determines the size and amount of social context at the bottom.',
													),
										array('name' => 'publish_to_fan_page',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										);

	fb_construct_fields('settings', $children, $parent);
}

//add_action( 'transition_post_status', 'fb_publish_later',10,3);
function fb_publish_later($new_status, $old_status, $post) {
	// check that the new status is "publish" and that the old status was not "publish"
	if ($new_status == 'publish' && $old_status != 'publish') {
		// only publish "public" post types
		$post_types = get_post_types( array('public' => true), 'objects' );
		foreach ( $post_types as $post_type ) {
			if ( $post->post_type == $post_type->name ) {
				fb_post_to_fb_page($post->ID);

				break;
			}
		}
	}
}

?>