<?php
add_action( 'admin_init','fb_friend_page_autocomplete' );
add_filter( 'the_content', 'fb_social_publisher_mentioning_output', 30 );
add_action( 'wp_ajax_my_action', 'fb_friend_page_autocomplete' );

function fb_friend_page_autocomplete() {
	$output = array();
	
  $nonce = '';
  
  if (isset($_GET) && isset($_GET['autocompleteNonce']))
    $nonce = $_GET['autocompleteNonce'];
  
  // check to see if the submitted nonce matches with the
  // generated nonce we created earlier
  if ( wp_verify_nonce( $nonce, 'fb_autocomplete_nonce' ) ) {
    if (!empty($_GET['fb-friends'])) {
      global $facebook;
  
      if ( ! isset( $facebook ) )
        return;
  
      if ( false === ( $friends = get_transient( 'fb_friends_' . $facebook->getUser() ) ) ) {
        try {
          $friends = $facebook->api('/me/friends', 'GET', array('ref' => 'fbwpp'));
        }
        catch (WP_FacebookApiException $e) {
        }
        
        set_transient( 'fb_friends_' . $facebook->getUser(), $friends, 60*15 );
      }
  
      foreach($friends['data'] as $friend) {
        $friends_clean[$friend['name']] = $friend['id'];
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
        $count = 0;
        
        foreach ($results as $result) {
          $output[$count]['id'] = '[' . esc_attr($result[1]) . '|' . esc_attr($result[0]) . ']';
          $output[$count]['name'] = '<img src="' . esc_url( 'http://graph.facebook.com/' . $result[1] . '/picture/' ) . '" width="25" height="25"> &nbsp;' . esc_attr($result[0]);
          
          $count++;
        }
      }
      
      print json_encode($output);
      exit;
    }
  
  
    if (!empty($_GET['fb-pages'])) {
      global $facebook;
  
      if ( ! isset( $facebook ) )
        return;

      if ( false === ( $pages = get_transient( 'fb_pages_' . $_GET['q']) ) ) {
        try {
          $pages = $facebook->api( '/search', 'GET', array( 'access_token' => '', 'q' => $_GET['q'], 'type' => 'page', 'fields' => 'picture,name,id,likes', 'ref' => 'fbwpp' ) );
        }
        catch (WP_FacebookApiException $e) {
        }
        set_transient( 'fb_pages_' . $_GET['q'], $pages, 60*60 );
      }
  
  
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
        $count = 0;
        
        foreach ($results as $result) {
          $output[$count]['id'] = '[' . esc_attr($result[1][2]) . '|' . esc_attr($result[1][1]) . ']';
          $output[$count]['name'] = '<img src="' . esc_url($result[1][0]) . '" width="25" height="25"> &nbsp;' . esc_attr($result[1][1]) . ' (' . fb_short_number(esc_attr($result[1][3])) . ' likes)';
          
          $count++;
        }
      }
      
      print json_encode($output);
      exit;
    }
  }
}

function fb_short_number($num) {
	if($num>1000000) return round(($num/1000000),0).'m';
	else if($num>1000) return round(($num/1000),0).'k';

	return number_format_i18n($num);
}

add_action( 'add_meta_boxes', 'fb_add_page_mention_box' );
add_action( 'save_post', 'fb_add_page_mention_box_save' );

function fb_add_page_mention_box() {
	global $post;
	global $facebook;
	$options = get_option('fb_options');
  
	if ($post->post_status == 'publish')	
		return;
	
  if ( isset( $options['social_publisher']['enabled'] ) ) {
    add_meta_box(
        'fb_page_mention_box_id',
        __( 'Mention Facebook Pages', 'facebook' ),
        'fb_add_page_mention_box_content',
        'post',
        'side'
    );
    add_meta_box(
        'fb_page_mention_box_id',
        __( 'Mention Facebook Pages', 'facebook' ),
        'fb_add_page_mention_box_content',
        'page',
        'side'
    );
  }
}

function fb_add_page_mention_box_content( $post ) {
	wp_enqueue_script('suggest');

	global $facebook;

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_page_mention_box_noncename' );

	$fb_user = fb_get_current_user();
	
	if (isset ( $fb_user ) ) {
		$perms = $facebook->api('/me/permissions', 'GET', array('ref' => 'fbwpp'));
	}
	
	if ( isset ( $fb_user ) && isset($perms['data'][0]['manage_pages']) && isset($perms['data'][0]['publish_actions']) && isset($perms['data'][0]['publish_stream'])) {
		// The actual fields for data entry
		echo '<label for="fb_page_mention_box_autocomplete">';
		_e("Page's Name", 'facebook' );
		echo '</label> ';
		echo '<input type="text" class="widefat" id="suggest-pages" autocomplete="off" name="fb_page_mention_box_autocomplete" value="" size="44" placeholder="' . esc_attr__('Type to find a page.', 'facebook') . '" />';
		echo '<label for="fb_page_mention_box_message">';
		_e("Message", 'facebook' );
		echo '</label> ';
		echo '<input type="text" class="widefat" id="pages-mention-message" name="fb_page_mention_box_message" value="" size="44" placeholder="'.esc_attr__('Write something...').'" />';
		echo '<p class="howto">';
		if ( $post->post_type == 'page' ) {
			_e('This will add the page and message to the Timeline of each Facebook Page mentioned. They will also appear in the contents of the page.', 'facebook');
		} else {
			_e('This will add the post and message to the Timeline of each Facebook Page mentioned. They will also appear in the contents of the post.', 'facebook');
		}
		echo '</p>';
	}
	else {
		echo '<p>Facebook social publishing is enabled.</p><p><strong><a href="#" onclick="authFacebook(); return false;">Link your Facebook account to your WordPress account</a></strong> to get full functionality, including adding new Posts to your Timeline and mentioning friends Facebook Pages.</p>';
	}
}

function fb_add_page_mention_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

	$fb_user = fb_get_current_user();
	
	if (!$fb_user)
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
  if ( isset ( $_POST ) && isset( $_POST['fb_page_mention_box_autocomplete'] ) ) {
    $autocomplete_data = $_POST['fb_page_mention_box_autocomplete'];
    
    preg_match_all(
      "/\[(.*?)\|(.*?)\]/su",
      $autocomplete_data,
      $page_details,
      PREG_SET_ORDER // formats data into an array of posts
    );
  
    // probably using add_post_meta(), update_post_meta(), or
    // a custom table (see Further Reading section below)
  
    $pages_details_meta = array();
  
    foreach($page_details as $page_detail) {
      $pages_details_meta[] = array('id' => sanitize_text_field($page_detail[1]), 'name' => sanitize_text_field($page_detail[2]));
    }
  
    update_post_meta($post_id, 'fb_mentioned_pages', $pages_details_meta );
  
    update_post_meta($post_id, 'fb_mentioned_pages_message', sanitize_text_field($_POST['fb_page_mention_box_message']) );
  }
}

add_action( 'add_meta_boxes', 'fb_add_friend_mention_box' );
add_action( 'save_post', 'fb_add_friend_mention_box_save' );

function fb_add_friend_mention_box() {
	global $post;
	global $facebook;
	$options = get_option('fb_options');
  
	if ($post->post_status == 'publish')	
		return;
	
  if ( isset( $options['social_publisher']['enabled'] ) ) {
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
}

function fb_add_friend_mention_box_content( $post ) {
	global $facebook;
	
	wp_enqueue_script('suggest');

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_friend_mention_box_noncename' );
	
	$fb_user = fb_get_current_user();
	
	if ( isset( $fb_user ) ) {
    $perms = $facebook->api('/me/permissions', 'GET', array('ref' => 'fbwpp'));
  }
	
	if ( isset ( $fb_user ) && isset($perms['data'][0]['manage_pages']) && isset($perms['data'][0]['publish_actions']) && isset($perms['data'][0]['publish_stream'])) {
		// The actual fields for data entry
		echo '<label for="fb_friend_mention_box_autocomplete">';
		_e("Friend's Name", 'facebook' );
		echo '</label> ';
		echo '<input type="text" class="widefat" id="suggest-friends" autocomplete="off" name="fb_friend_mention_box_autocomplete" value="" size="44" placeholder="Type to find a friend." />';
		echo '<label for="fb_friend_mention_box_message">';
		_e("Message", 'facebook' );
		echo '</label> ';
		echo '<input type="text" class="widefat" id="friends-mention-message" name="fb_friend_mention_box_message" value="" size="44" placeholder="Write something..." />';

		echo '<p class="howto">';
		if ( $post->post_type == 'page' ) {
			_e('This will add the page and message to the Timeline of each friend mentioned. They will also appear in the contents of the page.', 'facebook');
		} else {
			_e('This will add the post and message to the Timeline of each friend mentioned. They will also appear in the contents of the post.', 'facebook');
		}
		echo '</p>';
	}
	else {
		echo '<p>'.__('Facebook social publishing is enabled.', 'facebook') .'</p>';
		echo '<p>'.sprintf(__('<strong>%sLink your Facebook account to your WordPress account</a></strong> to get full functionality, including adding new Posts to your Timeline and mentioning friends Facebook Pages.', 'facebook'), '<a href="#" onclick="authFacebook(); return false;">' ) .'</p>';
	}
}

function fb_add_friend_mention_box_save( $post_id ) {
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
	
	$fb_user = fb_get_current_user();
	
	if (!$fb_user)
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
  if ( isset ($_POST) && isset( $_POST['fb_page_mention_box_autocomplete'] ) ) {
    $autocomplete_data = $_POST['fb_friend_mention_box_autocomplete'];
  
    preg_match_all(
      "/\[(.*?)\|(.*?)\]/su",
      $autocomplete_data,
      $friend_details,
      PREG_SET_ORDER // formats data into an array of posts
    );
  
    // probably using add_post_meta(), update_post_meta(), or
    // a custom table (see Further Reading section below)
  
    $friends_details_meta = array();
  
    foreach($friend_details as $friend_detail) {
      $friends_details_meta[] = array('id' => sanitize_text_field($friend_detail[1]), 'name' => sanitize_text_field($friend_detail[2]));
    }
  
    update_post_meta($post_id, 'fb_mentioned_friends', $friends_details_meta);
  
    update_post_meta($post_id, 'fb_mentioned_friends_message', sanitize_text_field($_POST['fb_friend_mention_box_message']));
  }
}



function fb_social_publisher_mentioning_output($content) {
	global $post;

	$options = get_option('fb_options');
  
  if( $post ) {
    $fb_mentioned_pages	 = get_post_meta($post->ID, 'fb_mentioned_pages', true);
    $fb_mentioned_friends = get_post_meta($post->ID, 'fb_mentioned_friends', true);
  
    $mentions_entities = '';
  
    if (!empty($fb_mentioned_friends)){
      foreach( $fb_mentioned_friends as $fb_mentioned_friend ) {
        $mentions_entities .= '<a href="http://www.facebook.com/' . esc_attr($fb_mentioned_friend['id']) . '" title="'.sprintf(esc_attr__('Click to visit %s\'s profile on Facebook.','facebook'), esc_attr($fb_mentioned_friend['name'])) .'"><img src="http://graph.facebook.com/' . esc_attr($fb_mentioned_friend['id']) . '/picture" width="16" height="16"> ' . esc_html($fb_mentioned_friend['name']) . '</a> ';
      }
    }
  
    if (!empty($fb_mentioned_pages)){
      foreach( $fb_mentioned_pages as $fb_mentioned_page ) {
        $mentions_entities .= '<a href="http://www.facebook.com/' . esc_attr($fb_mentioned_page['id']) . '" title="'.sprintf(esc_attr__('Click to visit %s\'s profile on Facebook.','facebook'), esc_attr($fb_mentioned_page['name'])).'"><img src="http://graph.facebook.com/' . esc_attr($fb_mentioned_page['id']) . '/picture" width="16" height="16"> ' . esc_html($fb_mentioned_page['name']) . '</a> ';
      }
    }
  
    if ($mentions_entities) {
      $mentions = '<div class="fb-mentions entry-meta">' . $mentions_entities . 'mentioned.</div>';
  
      $new_content = '';
      
      if ( isset($options['social_publisher']) ) {
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
    }
  }
	

	return $content;
}