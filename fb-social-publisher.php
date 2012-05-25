<?php
require_once( $facebook_plugin_directory . '/fb-social-publisher-mentioning.php');

if ( isset($options['social_publisher']['publish_to_authors_facebook_timeline']) ) {
		add_action( 'add_meta_boxes', 'fb_add_author_message_box' );
		add_action( 'save_post', 'fb_add_author_message_box_save' );
}

if ( $options['social_publisher']['publish_to_fan_page'] !== 'disabled' ) {
		add_action( 'add_meta_boxes', 'fb_add_fan_page_message_box' );
		add_action( 'save_post', 'fb_add_fan_page_message_box_save' );
}

/**
 * Add meta boxes for a custom Status that is used when posting to an Author's Timeline
 *
 * @since 1.0
 */
function fb_add_author_message_box() {
  global $post;
  
  if ($post->post_status == 'publish')  
    return;
  
		add_meta_box(
				'fb_author_message_box_id',
				__( 'Facebook Status on Your Timeline', 'facebook' ),
				'fb_add_author_message_box_content',
				'post'
		);
		add_meta_box(
				'fb_author_message_box_id',
				__( 'Facebook Status on Your Timeline', 'fb_add_author_message_box_textdomain' ),
				'fb_add_author_message_box_content',
				'page'
		);
}

/**
 * Add meta boxes for a custom Status that is used when posting to an Author's Timeline
 *
 * @since 1.0
 */
function fb_add_author_message_box_content( $post ) {
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_author_message_box_noncename' );

	// The actual fields for data entry
	/*
	echo '<label for="fb_author_message_box_message">';
			 _e("Message", 'fb_author_message_box_message_textdomain' );
	echo '</label> ';
	*/
	echo '<input type="text" class="widefat" id="friends-mention-message" name="fb_author_message_box_message" value="" size="44" placeholder="What\'s on your mind?" />';
	echo '<p class="howto">This message will show as part of the story on your Facebook Timeline.</p>';
}

/**
 * Save the custom Status, used when posting to an Author's Timeline
 *
 * @since 1.0
 */
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

/**
 * Add meta boxes for a custom Status that is used when posting to a Fan Page's Timeline
 *
 * @since 1.0
 */
function fb_add_fan_page_message_box() {
  global $post;
  
  $options = get_option('fb_options');
  
  preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/s", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);
  
  if ($post->post_status == 'publish')  
    return;
  
		add_meta_box(
				'fb_fan_page_message_box_id',
				__( 'Facebook Status on ' . $fan_page_info[0][1] . '\'s Timeline', 'fb_add_fan_page_message_box_textdomain' ),
				'fb_add_fan_page_message_box_content',
				'post'
		);
		add_meta_box(
				'fb_fan_page_message_box_id',
				__( 'Facebook Status on ' . $fan_page_info[0][1] . '\'s Timeline', 'fb_add_fan_page_message_box_textdomain' ),
				'fb_add_fan_page_message_box_content',
				'page'
		);
}

/**
 * Add meta boxes for a custom Status that is used when posting to a Fan Page's Timeline
 *
 * @since 1.0
 */
function fb_add_fan_page_message_box_content( $post ) {
  $options = get_option('fb_options');
  
  preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/s", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);
  
		// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_fan_page_message_box_noncename' );

	// The actual fields for data entry
	/*
	echo '<label for="fb_fan_page_message_box_message">';
			 _e("Message", 'fb_fan_page_message_box_message_textdomain' );
	echo '</label> ';
	*/
	echo '<input type="text" class="widefat" id="friends-mention-message" name="fb_fan_page_message_box_message" value="" size="44" placeholder="Write something..." />';
	echo '<p class="howto">This message will show as part of the story on ' . $fan_page_info[0][1] . '\'s Timeline.</p>';
}

/**
 * Save the custom Status, used when posting to an Fan Page's Timeline
 *
 * @since 1.0
 */
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

/**
 * Posts a published WordPress post to a Facebook Page's Timeline
 *
 * @since 1.0
 * @param int $post_id The post ID that will be posted
 */
function fb_post_to_fb_page($post_id) {
	global $facebook;

	$options = get_option('fb_options');

	if ($options['social_publisher']['publish_to_fan_page'] == 'disabled')
		return;

	preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/s", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);

	list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

	$fan_page_message = get_post_meta($post_id, 'fb_fan_page_message', true);

	if ($post_thumbnail_url == null) {
		$args = array('access_token' => $fan_page_info[0][3],
									'from' => $fan_page_info[0][2],
									'link' => apply_filters( 'rel_canonical', get_permalink()),
									'name' => get_the_title(),
									'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
									'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
									'message' => $fan_page_message,
									);
	}
	else {
		$args = array('access_token' => $fan_page_info[0][3],
									'from' => $fan_page_info[0][2],
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
		$publish_result = $facebook->api('/' . $fan_page_info[0][2] . '/feed', 'POST', $args);

		add_post_meta($post_id, 'fb_fan_page_post_id', $publish_result['id'], true);
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}
}

/**
 * Posts an Open Graph action to an author's Facebook Timeline
 *
 * @since 1.0
 * @param int $post_id The post ID that will be posted
 */
function fb_post_to_author_fb_timeline($post_id) {
	global $post;
	global $facebook;

	if ( ! isset( $facebook ) )
		return;

	$options = get_option('fb_options');
	$fb_mentioned_friends = get_post_meta($post_id, 'fb_mentioned_friends', true);

	list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

	$mentioned_friends_message = get_post_meta($post_id, 'fb_mentioned_friends_message', true);

	$publish_ids_friends = array();

	foreach($fb_mentioned_friends as $friend) {

		try {
			if ($post_thumbnail_url == null) {
				$args = array('access_token' => $facebook->getApplicationAccessToken(),
                      'link' => apply_filters( 'rel_canonical', get_permalink()),
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $mentioned_friends_message,
											);
			}
			else {
				$args = array('access_token' => $facebook->getApplicationAccessToken(),
                      'link' => apply_filters( 'rel_canonical', get_permalink()),
											'picture' => $post_thumbnail_url,
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $mentioned_friends_message,
											);
			}

			$args['ref'] = 'fbwpp';

			$publish_result = $facebook->api('/' . $friend['id'] . '/feed', 'POST', $args);
      
			$publish_ids_friends[] = $publish_result['id'];

		}
		catch (FacebookApiException $e) {
			error_log(var_export($e,1));
		}
	}

	add_post_meta($post_id, 'fb_mentioned_friends_post_ids', $publish_ids_friends, true);


	$fb_mentioned_pages = get_post_meta($post_id, 'fb_mentioned_pages', true);

	$mentioned_pages_message = get_post_meta($post_id, 'fb_mentioned_pages_message', true);

	//$mentions = '';

	$publish_ids_pages = array();

	foreach($fb_mentioned_pages as $page) {
		try {
			if ($post_thumbnail_url == null) {
				$args = array('link' => apply_filters( 'rel_canonical', get_permalink()),
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $mentioned_pages_message,

											);
			}
			else {
				$args = array('link' => apply_filters( 'rel_canonical', get_permalink()),
											'picture' => $post_thumbnail_url,
											'name' => get_the_title(),
											'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'description' => apply_filters( 'the_excerpt', get_the_excerpt() ),
											'message' => $mentioned_pages_message,
											);
			}

			$args['ref'] = 'fbwpp';

			$publish_result = $facebook->api('/' . $page['id'] . '/feed', 'POST', $args);
      
			$publish_ids_pages[] = $publish_result['id'];
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e,1));
		}

		//$mentions .= $page['id'] . ",";
	}

	add_post_meta($post_id, 'fb_mentioned_pages_post_ids', $publish_ids_pages, true);

	$author_message = get_post_meta($post_id, 'fb_author_message', true);

	try {
		$publish_result = $facebook->api('/me/' . $options["app_namespace"] . ':publish', 'POST', array('message' => $author_message, 'article' => get_permalink($post_id)));
		
		add_post_meta($post_id, 'fb_author_post_id', $publish_result['id'], true);
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}
}


function fb_get_social_publisher_fields() {
	global $facebook;

	$accounts = fb_get_user_pages();

	$accounts_options = array('disabled' => '[Disabled]');

	foreach($accounts as $account) {
		if (isset($account['name']) && isset($account['category']) && $account['category'] != 'Application') {
			$account_options_key = $account['name'] . "@@!!" . $account['id'] . "@@!!" . $account['access_token'];
			$accounts_options[$account_options_key] = $account['name'];
		}
	}

	$parent = array('name' => 'social_publisher',
									'type' => 'checkbox',
									'label' => 'Social Publisher',
									'description' => 'Social Publisher allows you to publish to an Author\'s Facebook Timeline and Fan Page.  Authors can also mention Facebook friends and pages. ',
									'help_link' => 'http://developers.facebook.com/wordpress',
									'image' => plugins_url( 'images/settings_social_publisher.png', __FILE__)
									);

	if (empty($accounts_options)) {
		$fan_page_option = array('name' => 'publish_to_fan_page',
													'type' => 'disabled_text',
													'disabled_text' => '<a href="#" onclick="authFacebook(); return false;">Link your Facebook account to your WordPress account</a>',
													'help_text' => __( 'All new posts will be automatically published to this Facebook Page.', 'facebook' ),
													);
	}
	else {
		$fan_page_option = array('name' => 'publish_to_fan_page',
													'type' => 'dropdown',
													'options' => $accounts_options,
													'help_text' => __( 'New posts will be publish to this Facebook Page.', 'facebook' ),
													);
	}

	$children = array(array('name' => 'publish_to_authors_facebook_timeline',
													'label' => "Publish to author's Timeline",
													'type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'Publish new posts to the author\'s Facebook Timeline and allow mentioning friends.', 'facebook' ),
													),
										$fan_page_option,
										array('name' => 'mentions_show_on_homepage',
													'type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'Authors can mentions Facebook friends and pages in posts.  Enable this to show mentions on the homepage, as part of the post and page previews.', 'facebook' ),
													),
										array('name' => 'mentions_position',
													'type' => 'dropdown',
													'default' => 'both',
													'options' => array('top' => 'top', 'bottom' => 'bottom', 'both' => 'both'),
													'help_text' => __( 'Authors can mentions Facebook friends and pages in posts.  This controls where mentions will be displayed in the posts.', 'facebook' ),
													),
										);



	fb_construct_fields('settings', $children, $parent);
}

add_action( 'transition_post_status', 'fb_publish_later', 10, 3);
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

add_action('before_delete_post', 'fb_delete_social_posts', 10);

function fb_delete_social_posts( $post_id ) {
	global $facebook;

	if ( ! isset( $facebook ) || ! is_object( $facebook ) || ! method_exists( $facebook, 'api' ) )
		return;

	$fb_page_post_id = get_post_meta($post_id, 'fb_fan_page_post_id', true);

	if ($fb_page_post_id) {
		try {
			$delete_result = $facebook->api('/' . $fb_page_post_id, 'DELETE', array('ref' => 'fbwpp'));
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e,1));
		}
	}
	
	$fb_author_post_id = get_post_meta($post_id, 'fb_author_post_id', true);

	if ($fb_author_post_id) {
		try {
			$delete_result = $facebook->api('/' . $fb_author_post_id, 'DELETE', array('ref' => 'fbwpp'));
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e,1));
		}

	}
	
	$fb_mentioned_pages_post_ids = get_post_meta($post_id, 'fb_mentioned_pages_post_ids', true);

	if ($fb_mentioned_pages_post_ids) {
		foreach($fb_mentioned_pages_post_ids as $page_post_ids) {
			try {
					$delete_result = $facebook->api('/' . $page_post_ids, 'DELETE', array('ref' => 'fbwpp'));
			}
			catch (FacebookApiException $e) {
				error_log(var_export($e,1));
			}
		}
	}

	$fb_mentioned_friends_post_ids = get_post_meta($post_id, 'fb_mentioned_friends_post_ids', true);

	if ($fb_mentioned_friends_post_ids) {
		foreach($fb_mentioned_friends_post_ids as $page_post_ids) {
			try {
					$delete_result = $facebook->api('/' . $page_post_ids, 'DELETE', array('ref' => 'fbwpp'));
			}
			catch (FacebookApiException $e) {
				error_log(var_export($e,1));
			}
		}
	}
}

//TODO: currently, updating mentions don't work-- we should fix this

//add_action('post_updated', 'fb_update_social_posts', 10);

function fb_update_social_posts($post_ID, $post_after, $post_before) {
	//get post's meta for friends
	
	//loop through post's meta for friends
	
	//get post's meta for pages
}

?>