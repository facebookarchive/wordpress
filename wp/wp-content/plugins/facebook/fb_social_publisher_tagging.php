<?php
add_action( 'init','fb_friend_page_autocomplete' );

function fb_friend_page_autocomplete() {
	if (!empty($_GET['fb-friends'])) {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		try {
			$friends = $facebook->api('/me/friends', 'GET', array('ref' => 'fbwpp'));

			foreach($friends['data'] as $friend) {
				$friends_clean[$friend['name']] = $friend['id'];
			}
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e, 1));
		}

		if (isset($_GET['q']) && isset($friends_clean)) {
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

		if (!empty($results)) {
			foreach ($results as $result) {
				echo '<img src="http://graph.facebook.com/' . $result[1] . '/picture/" width="25" height="25"> &nbsp;' . $result[0] . '<span style="display: none;">(' . $result[1] . ')</span>' . "\n";
			}
		}

		exit;
	}


	if (!empty($_GET['fb-pages'])) {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		try {
			$pages = $facebook->api( '/search', 'GET', array( 'q' => $_GET['q'], 'type' => 'page', 'fields' => 'picture,name,id,likes', 'ref' => 'fbwpp' ) );

			if ( isset($pages['data']) ) {
				foreach($pages['data'] as $page) {
					$pages_clean[$page['name']] = array($page['picture'], $page['name'], $page['id'], $page['likes']);
				}
			}
			else {
				echo 'Error returning results.';
				exit;
			}
		}
		catch (FacebookApiException $e) {
			error_log(var_export($e, 1));
		}

		if (isset($_GET['q'])) {
			$q = strtolower($_GET['q']);

			if ($q && isset($pages_clean)) {
				foreach ($pages_clean as $key => $value) {
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

		if (!empty($results)) {
			foreach ($results as $result) {
				echo '<img src="' . $result[1][0] . '" width="25" height="25"> &nbsp;' . $result[1][1] . '(' . number_format($result[1][3]) . ' likes) <span style="display: none;">(' . $result[1][2] . ')</span>' . "\n";
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
		$accounts = $facebook->api('/me/accounts', 'GET', array('ref' => 'fbwpp'));
	}
	catch (FacebookApiException $e) {
		error_log(var_export($e, 1));

		return $accounts;
	}

	return $accounts['data'];
}

add_action( 'add_meta_boxes', 'fb_add_page_tag_box' );
add_action( 'save_post', 'fb_add_page_tag_box_save' );

function fb_add_page_tag_box() {
		add_meta_box(
				'fb_page_tag_box_id',
			  __( 'Tag Facebook Pages', 'fb_page_tag_box_id_textdomain', 'facebook' ),
				'fb_add_page_tag_box_content',
				'post'
		);
		add_meta_box(
				'fb_page_tag_box_id',
			  __( 'Tag Facebook Pages', 'fb_page_tag_box_id_textdomain', 'facebook' ),
				'fb_add_page_tag_box_content',
				'page'
		);
}

function fb_add_page_tag_box_content( $post ) {
	wp_enqueue_script('suggest');

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_page_tag_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_page_tag_box_autocomplete">';
			 _e("Page Name", 'fb_page_tag_box_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="suggest-pages" autocomplete="off" name="fb_page_tag_box_autocomplete" value="" size="44" />';
	echo '<label for="fb_page_tag_box_message">';
			 _e("Message", 'fb_page_tag_box_message_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="pages-tag-message" name="fb_page_tag_box_message" value="" size="44" />';
	echo '<p>This will post the Timeline of each Facebook Page tagged.</p>';
}

function fb_add_page_tag_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( empty($_POST['fb_page_tag_box_noncename']) || !wp_verify_nonce( $_POST['fb_page_tag_box_noncename'], plugin_basename( __FILE__ ) ) )
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

	$autocomplete_data = $_POST['fb_page_tag_box_autocomplete'];

	preg_match_all(
		"/([A-Z].*?)\(.*?\((.*?)\)/s",
		$autocomplete_data,
		$page_details,
		PREG_SET_ORDER // formats data into an array of posts
	);

	// probably using add_post_meta(), update_post_meta(), or
	// a custom table (see Further Reading section below)

	$pages_details_meta = array();

	foreach($page_details as $page_detail) {
		$pages_details_meta[] = array('id' => $page_detail[2], 'name' => $page_detail[1]);
	}

	add_post_meta($post_id, 'fb_tagged_pages', $pages_details_meta, true);

	add_post_meta($post_id, 'fb_tagged_pages_message', $_POST['fb_page_tag_box_message'], true);
}

add_action( 'add_meta_boxes', 'fb_add_friend_tag_box' );
add_action( 'save_post', 'fb_add_friend_tag_box_save' );

function fb_add_friend_tag_box() {
		add_meta_box(
				'fb_friend_tag_box_id',
			  __( 'Tag Facebook Friends', 'facebook' ),
				'fb_add_friend_tag_box_content',
				'post'
		);
		add_meta_box(
				'fb_friend_tag_box_id',
			  __( 'Tag Facebook Friends', 'facebook' ),
				'fb_add_friend_tag_box_content',
				'page'
		);
}

function fb_add_friend_tag_box_content( $post ) {
	wp_enqueue_script('suggest');

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_friend_tag_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_friend_tag_box_autocomplete">';
			 _e("Friend's Name", 'fb_friend_tag_box_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="suggest-friends" autocomplete="off" name="fb_friend_tag_box_autocomplete" value="" size="44" />';
	echo '<label for="fb_friend_tag_box_message">';
			 _e("Message", 'fb_friend_tag_box_message_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="friends-tag-message" name="fb_friend_tag_box_message" value="" size="44" />';
	echo '<p>This will post the Timeline of each Facebook friend tagged.</p>';
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

	$autocomplete_data = $_POST['fb_friend_tag_box_autocomplete'];

	preg_match_all(
		"/([A-Z].*?)\((.*?)\)/s",
		$autocomplete_data,
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

	add_post_meta($post_id, 'fb_tagged_friends_message', $_POST['fb_friend_tag_box_message'], true);
}