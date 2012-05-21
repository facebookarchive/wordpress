<?php
require_once( $facebook_plugin_directory . '/fb_social_publisher_tagging.php');

$options = get_option('fb_options');

if ( $options['social_publisher']['publish_to_authors_facebook_timeline'] ) {
		add_action( 'add_meta_boxes', 'fb_add_author_message_box' );
		add_action( 'save_post', 'fb_add_author_message_box_save' );
}

function fb_add_author_message_box() {
		add_meta_box(
				'fb_author_message_box_id',
				__( 'Facebook Status on Author\'s Timeline', 'facebook' ),
				'fb_add_author_message_box_content',
				'post'
		);
		add_meta_box(
				'fb_author_message_box_id',
				__( 'Facebook Message on Author\'s Timeline', 'fb_add_author_message_box_textdomain' ),
				'fb_add_author_message_box_content',
				'page'
		);
}

function fb_add_author_message_box_content( $post ) {
		// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_author_message_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_author_message_box_message">';
			 _e("Message", 'fb_author_message_box_message_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="friends-tag-message" name="fb_author_message_box_message" value="" size="44" />';
	echo '<p>This message will show as part of the post on the Author\'s Facebook Timeline.</p>';
}

function fb_add_author_message_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( empty($_POST['fb_author_message_box_noncename']) || !wp_verify_nonce( $_POST['fb_author_message_box_noncename'], plugin_basename( __FILE__ ) ) )
			return;


	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	}
	else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	// OK, we're authenticated: we need to find and save the data

	add_post_meta($post_id, 'fb_author_message', $_POST['fb_author_message_box_message'], true);
}










if ( $options['social_publisher']['publish_to_fan_page'] ) {
		add_action( 'add_meta_boxes', 'fb_add_fan_page_message_box' );
		add_action( 'save_post', 'fb_add_fan_page_message_box_save' );
}

function fb_add_fan_page_message_box() {
		add_meta_box(
				'fb_fan_page_message_box_id',
				__( 'Facebook Status on Fan Page\'s Timeline', 'facebook' ),
				'fb_add_fan_page_message_box_content',
				'post'
		);
		add_meta_box(
				'fb_fan_page_message_box_id',
				__( 'Facebook Message on Fan Page\'s Timeline', 'fb_add_fan_page_message_box_textdomain' ),
				'fb_add_fan_page_message_box_content',
				'page'
		);
}

function fb_add_fan_page_message_box_content( $post ) {
		// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_fan_page_message_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_fan_page_message_box_message">';
			 _e("Message", 'fb_fan_page_message_box_message_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="friends-tag-message" name="fb_fan_page_message_box_message" value="" size="44" />';
	echo '<p>This message will show as part of the post on the Fan Page\'s Facebook Timeline.</p>';
}

function fb_add_fan_page_message_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( empty($_POST['fb_fan_page_message_box_noncename']) || !wp_verify_nonce( $_POST['fb_fan_page_message_box_noncename'], plugin_basename( __FILE__ ) ) )
			return;


	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	}
	else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	// OK, we're authenticated: we need to find and save the data

	add_post_meta($post_id, 'fb_fan_page_message', $_POST['fb_fan_page_message_box_message'], true);
}




function fb_post_to_fb_page($post_id) {
	global $facebook;

	$options = get_option('fb_options');

	$app_id = $options["app_id"];

	preg_match_all("/(.*?)@@!!(.*?)$/s", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);

	error_log(var_export($fan_page_info,1));
	list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

	$fan_page_message = get_post_meta($post_id, 'fb_fan_page_message', true);

	if ($post_thumbnail_url == null) {
		$args = array('access_token' => $fan_page_info[0][2],
									'from' => $fan_page_info[0][1],
									'link' => apply_filters( 'rel_canonical', get_permalink()),
									'name' => get_the_title(),
									'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
									'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
									'message' => $fan_page_message,
									);
	}
	else {
		$args = array('access_token' => $fan_page_info[0][2],
									'from' => $fan_page_info[0][1],
									'link' => apply_filters( 'rel_canonical', get_permalink()),
									'picture' => $post_thumbnail_url,
									'name' => get_the_title(),
									'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
									'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
									'message' => $fan_page_message,
									);
	}

	$args['ref'] = 'fbwpp';

	if ( ! isset( $facebook ) )
		return;

	try {
		$publish_result = $facebook->api('/' . $fan_page_info[0][1] . '/feed', 'POST', $args);

		add_post_meta($post_id, 'fb_fan_page_post_id', $publish_result['id'], true);
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}
}

function fb_post_to_author_fb_timeline($post_id) {
	global $post;
	global $facebook;

	if ( ! isset( $facebook ) )
		return;

	$options = get_option('fb_options');
	$fb_tagged_friends = get_post_meta($post_id, 'fb_tagged_friends', true);

	//$tags = '';

	list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

	$tagged_friends_message = get_post_meta($post_id, 'fb_tagged_friends_message', true);

	foreach($fb_tagged_friends as $friend) {

		try {
			if ($post_thumbnail_url == null) {
				$args = array('link' => apply_filters( 'rel_canonical', get_permalink()),
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $tagged_friends_message,
											);
			}
			else {
				$args = array('link' => apply_filters( 'rel_canonical', get_permalink()),
											'picture' => $post_thumbnail_url,
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $tagged_friends_message,
											);
			}

			$args['ref'] = 'fbwpp';

			$publish_result = $facebook->api('/' . $friend['id'] . '/feed', 'POST', $args);
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e,1));
		}

		//$tags .= $friend['id'] . ",";
	}
	//error_log(var_export($tags,1));

	$fb_tagged_pages = get_post_meta($post_id, 'fb_tagged_pages', true);

	$tagged_pages_message = get_post_meta($post_id, 'fb_tagged_pages_message', true);

	//$tags = '';

	foreach($fb_tagged_pages as $page) {
		try {
			if ($post_thumbnail_url == null) {
				$args = array('link' => apply_filters( 'rel_canonical', get_permalink()),
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $tagged_pages_message,

											);
			}
			else {
				$args = array('link' => apply_filters( 'rel_canonical', get_permalink()),
											'picture' => $post_thumbnail_url,
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $tagged_pages_message,
											);
			}

			$args['ref'] = 'fbwpp';

			$publish_result = $facebook->api('/' . $page['id'] . '/feed', 'POST', $args);
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e,1));
		}

		//$tags .= $page['id'] . ",";
	}
	//error_log(var_export($tags,1));

	$author_message = get_post_meta($post_id, 'fb_author_message', true);

	try {
		$publish = $facebook->api('/me/' . $options["app_namespace"] . ':publish', 'POST', array('message' => $author_message, 'article' => get_permalink($post_id)));
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}
}


function fb_get_social_publisher_fields() {
	global $facebook;

	$accounts = fb_get_user_pages();

	$accounts_options = array();

	foreach($accounts as $account) {
		if (isset($account['name'])) {
			$account_options_key = $account['id'] . "@@!!" . $account['access_token'];
			$accounts_options[$account_options_key] = $account['name'];
		}
	}

	$parent = array('name' => 'social_publisher',
									'field_type' => 'checkbox',
									'help_text' => __( 'Click to learn more.', 'facebook' ),
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
									);

	if (empty($accounts_options)) {
		$fan_page_option = array('name' => 'publish_to_fan_page',
													'field_type' => 'disabled_text',
													'disabled_text' => '<a href="#" onclick="authFacebook(); return false;">Link your Facebook account to your WordPress account</a>',
													'help_text' => __( 'New posts will be published to this Facebook Page.', 'facebook' ),
													);
	}
	else {
		$fan_page_option = array('name' => 'publish_to_fan_page',
													'field_type' => 'dropdown',
													'options' => $accounts_options,
													'help_text' => __( 'New posts will be publish to this Facebook Page.', 'facebook' ),
													);
	}

	$children = array(array('name' => 'publish_to_authors_facebook_timeline',
													'field_type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'Publish new posts to the author\'s Facebook Timeline and allow tagging friends.', 'facebook' ),
													),
										$fan_page_option
										);

	fb_construct_fields('settings', $children, $parent);
}

add_action( 'transition_post_status', 'fb_publish_later',10,3);
function fb_publish_later($new_status, $old_status, $post) {
	// check that the new status is "publish" and that the old status was not "publish"
	if ($new_status == 'publish' && $old_status != 'publish') {
		// only publish "public" post types
		$post_types = get_post_types( array('public' => true), 'objects' );
		foreach ( $post_types as $post_type ) {
			if ( $post->post_type == $post_type->name ) {
				fb_post_to_fb_page($post->ID);

				fb_post_to_author_fb_timeline($post->ID);

				break;
			}
		}
	}
}

add_action('delete_post', 'fb_delete_social_posts', 10);

function fb_delete_social_posts( $post_id ) {
	global $facebook;

	if ( ! isset( $facebook ) || ! is_object( $facebook ) || ! method_exists( $facebook, 'api' ) )
		return;

	$fb_page_post_id = get_post_meta($post_id, 'fb_fan_page_post_id', true);

	try {
		$delete_result = $facebook->api('/' . $fb_page_post_id, 'DELETE', array('ref' => 'fbwpp'));
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}

	/*$fb_timeline_post_id = get_post_meta($post_id, 'fb_timeline_post_id', true);

	try {
		$delete_result = $facebook->api('/' . $fb_page_post_id, 'DELETE');
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}*/
}

?>