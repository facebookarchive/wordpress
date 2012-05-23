<?php
add_action( 'init','fb_friend_page_autocomplete' );
add_filter( 'the_content', 'fb_social_publisher_mentioning_output', 30 );

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
			$pages = $facebook->api( '/search', 'GET', array( 'access_token' => '', 'q' => $_GET['q'], 'type' => 'page', 'fields' => 'picture,name,id,likes', 'ref' => 'fbwpp' ) );

			if ( isset($pages['data']) ) {
				foreach($pages['data'] as $page) {
					if (isset($page['name']) && isset($page['picture']) && isset($page['id']) && isset($page['likes'])) {
						$pages_clean[$page['name']] = array($page['picture'], $page['name'], $page['id'], $page['likes']);
					}
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

		if (!empty($results)) {
			foreach ($results as $result) {
				echo '<img src="' . $result[1][0] . '" width="25" height="25"> &nbsp;' . $result[1][1] . ' (' . fb_short_number($result[1][3]) . ' likes) <span style="display: none;">(' . $result[1][2] . ')</span>' . "\n";
			}
		}

		exit;
	}
}

function fb_short_number($num) {
	if($num>1000000) return round(($num/1000000),0).'m';
	else if($num>1000) return round(($num/1000),0).'k';

	return number_format($num);
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

add_action( 'add_meta_boxes', 'fb_add_page_mention_box' );
add_action( 'save_post', 'fb_add_page_mention_box_save' );

function fb_add_page_mention_box() {
		add_meta_box(
				'fb_page_mention_box_id',
			  __( 'Mention Facebook Pages', 'fb_page_mention_box_id_textdomain', 'facebook' ),
				'fb_add_page_mention_box_content',
				'post',
				'side'
		);
		add_meta_box(
				'fb_page_mention_box_id',
			  __( 'Mention Facebook Pages', 'fb_page_mention_box_id_textdomain', 'facebook' ),
				'fb_add_page_mention_box_content',
				'page',
				'side'
		);
}

function fb_add_page_mention_box_content( $post ) {
	wp_enqueue_script('suggest');

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_page_mention_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_page_mention_box_autocomplete">';
			 _e("Page's Name", 'fb_page_mention_box_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="suggest-pages" autocomplete="off" name="fb_page_mention_box_autocomplete" value="" size="44" placeholder="Type to find a page." />';
	echo '<label for="fb_page_mention_box_message">';
			 _e("Message", 'fb_page_mention_box_message_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="pages-mention-message" name="fb_page_mention_box_message" value="" size="44" placeholder="Write something..." />';
	echo '<p>This will post to the Timeline of each Facebook Page mentioned.</p>';
}

function fb_add_page_mention_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( empty($_POST['fb_page_mention_box_noncename']) || !wp_verify_nonce( $_POST['fb_page_mention_box_noncename'], plugin_basename( __FILE__ ) ) )
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

	$autocomplete_data = $_POST['fb_page_mention_box_autocomplete'];

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

	add_post_meta($post_id, 'fb_mentioned_pages', $pages_details_meta, true);

	add_post_meta($post_id, 'fb_mentioned_pages_message', $_POST['fb_page_mention_box_message'], true);
}

add_action( 'add_meta_boxes', 'fb_add_friend_mention_box' );
add_action( 'save_post', 'fb_add_friend_mention_box_save' );

function fb_add_friend_mention_box() {
		add_meta_box(
				'fb_friend_mention_box_id',
			  __( 'Mention Facebook Friends', 'facebook' ),
				'fb_add_friend_mention_box_content',
				'post',
				'side'
		);
		add_meta_box(
				'fb_friend_mention_box_id',
			  __( 'Mention Facebook Friends', 'facebook' ),
				'fb_add_friend_mention_box_content',
				'page',
				'side'
		);
}

function fb_add_friend_mention_box_content( $post ) {
	wp_enqueue_script('suggest');

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_friend_mention_box_noncename' );

	// The actual fields for data entry
	echo '<label for="fb_friend_mention_box_autocomplete">';
			 _e("Friend's Name", 'fb_friend_mention_box_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="suggest-friends" autocomplete="off" name="fb_friend_mention_box_autocomplete" value="" size="44" placeholder="Type to find a friend." />';
	echo '<label for="fb_friend_mention_box_message">';
			 _e("Message", 'fb_friend_mention_box_message_textdomain' );
	echo '</label> ';
	echo '<input type="text" class="widefat" id="friends-mention-message" name="fb_friend_mention_box_message" value="" size="44" placeholder="Write something..." />';
	echo '<p>This will post to the Timeline of each Facebook friend mentioned.</p>';
}

function fb_add_friend_mention_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( empty($_POST['fb_friend_mention_box_noncename']) || !wp_verify_nonce( $_POST['fb_friend_mention_box_noncename'], plugin_basename( __FILE__ ) ) )
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

	$autocomplete_data = $_POST['fb_friend_mention_box_autocomplete'];

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

	add_post_meta($post_id, 'fb_mentioned_friends', $friends_details_meta, true);

	add_post_meta($post_id, 'fb_mentioned_friends_message', $_POST['fb_friend_mention_box_message'], true);
}



function fb_social_publisher_mentioning_output($content) {
	global $post;

	$options = get_option('fb_options');

	$fb_mentioned_pages   = get_post_meta($post->ID, 'fb_mentioned_pages', true);
	$fb_mentioned_friends = get_post_meta($post->ID, 'fb_mentioned_friends', true);

	$mentions_entities = '';

	if (!empty($fb_mentioned_friends)){
		foreach( $fb_mentioned_friends as $fb_mentioned_friend ) {
			$mentions_entities .= '<a href="http://www.facebook.com/' . $fb_mentioned_friend['id'] . '"><img src="http://graph.facebook.com/' . $fb_mentioned_friend['id'] . '/picture" width="16" height="16"> ' . $fb_mentioned_friend['name'] . '</a> ';
		}
	}

	if (!empty($fb_mentioned_pages)){
		foreach( $fb_mentioned_pages as $fb_mentioned_page ) {
			$mentions_entities .= '<a href="http://www.facebook.com/' . $fb_mentioned_page['id'] . '"><img src="http://graph.facebook.com/' . $fb_mentioned_page['id'] . '/picture" width="16" height="16"> ' . $fb_mentioned_page['name'] . '</a> ';
		}
	}

	if ($mentions_entities) {
		$mentions = '<div class="fb-mentions entry-meta">' . $mentions_entities . 'mentioned in this post.</div>';

		$new_content = '';

		switch ($options['social_publisher']['mentions_position']) {
			case 'top':
				$new_content = $mentions . $content;
				break;
			case 'bottom':
				$new_content = $content . $mentions;
				break;
			case 'both':
				$new_content = $mentions . $content;
				$new_content .= $mentions;
				break;
			default:
				$new_content = $content;
		}

		if ( empty( $options['social_publisher']['mentions_show_on_homepage'] ) && is_singular() ) {
			$content = $new_content;
		}
		elseif ( isset($options['social_publisher']['mentions_show_on_homepage']) ) {
			$content = $new_content;
		}
	}

	return $content;
}