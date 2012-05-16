<?php

//hook into new posts action

//publish OG action for new post

//publish to fan page, if defined

add_action( 'init','fb_friend_autocomplete' );

function fb_friend_autocomplete() {
	if (!empty($_GET['fb-friends'])) {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		try {
			$friends = $facebook->api('/me/friends');

			foreach($friends['data'] as $friend) {
				$friends_clean[$friend['name']] = $friend['id'];
			}
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e, 1));
		}

		if (isset($_GET['q'])) {
			$q = strtolower($_GET['q']);

			if ($q) {
				foreach ($friends_clean as $key => $value) {
					if (strpos(strtolower($key), $q) !== false) {
						$results[] = array($key, $value);
					}
				}
			}
		}

		$output = 'autocomplete';
		if (isset($_GET['output'])) {
			$output = strtolower($_GET['output']);
		}

		if ($output === 'json') {
			echo json_encode($results);
		}
		else {
			if (!empty($results)) {
				foreach ($results as $result) {
					echo '<img src="http://graph.facebook.com/' . $result[1] . '/picture/" width="25" height="25"> &nbsp;' . $result[0] . '<span style="display: none;">(' . $result[1] . ')</span>' . "\n";
				}
			}
		}

		exit;
	}
}

function fb_get_user_pages() {
	global $facebook;

	$accounts = array();

	if ( ! isset( $facebook ) )
			return $accounts;

	try {
		$accounts = $facebook->api('/me/accounts');
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e, 1));

		return $accounts;
	}

	return $accounts['data'];
}

add_action( 'add_meta_boxes', 'fb_add_friend_tag_box' );
add_action( 'save_post', 'fb_add_friend_tag_box_save' );

function fb_add_friend_tag_box() {
		add_meta_box(
				'fb_friend_tag_box_id',
			  __( 'Tag Facebook Friends', 'fb_friend_tag_box_id_textdomain' ),
				'fb_add_friend_tag_box_content',
				'post'
		);
		add_meta_box(
				'fb_friend_tag_box_id',
				__( 'Tag Facebook Friends', 'fb_friend_tag_box_id_textdomain' ),
				'fb_add_friend_tag_box_content',
				'page'
		);
}

function fb_add_friend_tag_box_content( $post ) {
	wp_enqueue_script('suggest');

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_friend_tag_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_friend_tag_box_new_field">';
			 _e("", 'fb_friend_tag_box_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="suggest" autocomplete="off" name="fb_friend_tag_box_new_field" value="" size="25" />';
}

function fb_add_friend_tag_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( empty($_POST['fb_friend_tag_box_noncename']) || !wp_verify_nonce( $_POST['fb_friend_tag_box_noncename'], plugin_basename( __FILE__ ) ) )
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

	$data = $_POST['fb_friend_tag_box_new_field'];

	preg_match_all(
		"/([A-Z].*?)\((.*?)\)/s",
		$data,
		$friend_details,
		PREG_SET_ORDER // formats data into an array of posts
	);

	// probably using add_post_meta(), update_post_meta(), or
	// a custom table (see Further Reading section below)

	$friends_details_meta = array();

	foreach($friend_details as $friend_detail) {
		$friends_details_meta[] = array('id' => $friend_detail[2], 'name' => $friend_detail[1]);
	}

	add_post_meta($post_id, 'fb_tagged_friends', $friends_details_meta, true);
}

function fb_post_to_fb_page($post_id) {
	global $facebook;

	$options = get_option('fb_options');

	$app_id = $options["app_id"];

	preg_match_all("/(.*?)@@!!(.*?)$/s", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);

	error_log(var_export($fan_page_info,1));

	if ( ! isset( $facebook ) )
		return;

	try {
		$publish_result = $facebook->api('/' . $fan_page_info[0][1] . '/feed', 'POST', array('access_token' => $fan_page_info[0][2], 'from' => $fan_page_info[0][1], 'source' => get_permalink($post_id)));

		add_post_meta($post_id, 'fb_fan_page_post_id', $publish_result['id'], true);
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e,1));
	}
}

function fb_post_to_author_fb_timeline($post_id) {
	global $facebook;

	$options = get_option('fb_options');
	$fb_tagged_friends = get_post_meta($post_id, 'fb_tagged_friends', true);
	error_log(var_export($fb_tagged_friends,1));

	$tags = '';

	foreach($fb_tagged_friends as $friend) {
		error_log(var_export($friend,1));
		$tags .= $friend['id'] . ",";
	}
	error_log(var_export($tags,1));

	if ( ! isset( $facebook ) )
		return;

	try {
		$publish = $facebook->api('/me/' . $options["app_namespace"] . ':publish', 'POST', array('tags' => $tags, 'article' => get_permalink($post_id)));
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
		$account_options_key = $account['id'] . "@@!!" . $account['access_token'];
		$accounts_options[$account_options_key] = $account['name'];
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
													'help_text' => __( 'New posts will be publish to this Facebook Page.', 'facebook' ),
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
		$delete_result = $facebook->api('/' . $fb_page_post_id, 'DELETE');
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