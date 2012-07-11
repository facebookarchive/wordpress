<?php
require_once( $facebook_plugin_directory . '/fb-social-publisher-mentioning.php');
require_once('fb-open-graph.php');

$options = get_option('fb_options');

if ( isset($options['social_publisher']) && isset($options['social_publisher']['publish_to_authors_facebook_timeline']) ) {
	add_action( 'add_meta_boxes', 'fb_add_author_message_box' );
	add_action( 'save_post', 'fb_add_author_message_box_save' );
}

if ( isset($options['social_publisher']) && isset($options['social_publisher']['publish_to_fan_page']) && $options['social_publisher']['publish_to_fan_page'] !== 'disabled' ) {
	add_action( 'add_meta_boxes', 'fb_add_fan_page_message_box' );
	add_action( 'save_post', 'fb_add_fan_page_message_box_save' );
}

/**
 * Add meta boxes for a custom Status that is used when posting to an Author's Timeline
 *5
 * @since 1.0
 */
function fb_add_author_message_box() {
	global $post;
	$options = get_option('fb_options');
  
	if ($post->post_status == 'publish')	
		return;
  
  if ( isset( $options['social_publisher']['enabled'] ) ) {
    add_meta_box(
      'fb_author_message_box_id',
      __( 'Facebook Status on Your Timeline', 'facebook' ),
      'fb_add_author_message_box_content',
      'post'
    );
    add_meta_box(
      'fb_author_message_box_id',
      __( 'Facebook Status on Your Timeline', 'facebook' ),
      'fb_add_author_message_box_content',
      'page'
    );
  }
}

/**
 * Add meta boxes for a custom Status that is used when posting to an Author's Timeline
 *
 * @since 1.0
 */
function fb_add_author_message_box_content( $post ) {
  global $facebook;
  
  if ( ! isset( $facebook ) )
		return;
  
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_author_message_box_noncename' );

	// The actual fields for data entry
	/*
	echo '<label for="fb_author_message_box_message">';
			 _ e("Message", 'facebook' );
	echo '</label> ';
	*/
  
  $fb_user = fb_get_current_user();
	
	if ( isset( $fb_user ) ) {
    $perms = $facebook->api('/me/permissions', 'GET', array('ref' => 'fbwpp'));
  }
	
	if ( isset ( $fb_user ) && isset($perms['data'][0]['manage_pages']) && isset($perms['data'][0]['publish_actions']) && isset($perms['data'][0]['publish_stream'])) {
		echo '<input type="text" class="widefat" id="friends-mention-message" name="fb_author_message_box_message" value="" size="44" placeholder="What\'s on your mind?" />';
    echo '<p class="howto">'. __('This message will show as part of the story on your Facebook Timeline.', 'facebook' ) .'</p>';
	}
	else {
		echo '<p>'.__('Facebook social publishing is enabled.', 'facebook') .'</p>';
		echo '<p>'.sprintf(__('<strong>%sLink your Facebook account to your WordPress account</a></strong> to get full functionality, including adding new Posts to your Timeline and mentioning friends Facebook Pages.', 'facebook'), '<a href="#" onclick="authFacebook(); return false;">' ) .'</p>';
	}
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
	update_post_meta($post_id, 'fb_author_message', sanitize_text_field($_POST['fb_author_message_box_message']) );
}

/**
 * Add meta boxes for a custom Status that is used when posting to a Fan Page's Timeline
 *
 * @since 1.0
 */
function fb_add_fan_page_message_box() {
	global $post;
	
	$options = get_option('fb_options');
	
	preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);
	
	if ($post->post_status == 'publish')	
		return;
	
  if ( isset( $options['social_publisher']['enabled'] ) && isset( $fan_page_info ) && isset( $fan_page_info[0] ) && isset( $fan_page_info[0][2] ) ) {
    add_meta_box(
      'fb_fan_page_message_box_id',
      sprintf( __( 'Facebook Status on %s\'s Timeline', 'facebook' ), $fan_page_info[0][1] ),
      'fb_add_fan_page_message_box_content',
      'post'
    );
    add_meta_box(
      'fb_fan_page_message_box_id',
      sprintf( __( 'Facebook Status on %s\'s Timeline', 'facebook' ), $fan_page_info[0][1] ),
      'fb_add_fan_page_message_box_content',
      'page'
    );
  }
}

/**
 * Add meta boxes for a custom Status that is used when posting to a Fan Page's Timeline
 *
 * @since 1.0
 */
function fb_add_fan_page_message_box_content( $post ) {
	$options = get_option('fb_options');
	
	preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);
	
		// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'fb_fan_page_message_box_noncename' );

	// The actual fields for data entry
	/*
	echo '<label for="fb_fan_page_message_box_message">';
			 _ e("Message", 'facebook' );
	echo '</label> ';
	*/
	echo '<input type="text" class="widefat" id="friends-mention-message" name="fb_fan_page_message_box_message" value="" size="44" placeholder="Write something..." />';
	echo '<p class="howto">' . sprintf( __( 'This message will show as part of the story on %s\'s Timeline.', 'facebook'), $fan_page_info[0][1] ) . '</p>';
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

	update_post_meta( $post_id, 'fb_fan_page_message', sanitize_text_field($_POST['fb_fan_page_message_box_message']) );
}

/**
 * Posts a published WordPress post to a Facebook Page's Timeline
 *
 * @since 1.0
 * @param int $post_id The post ID that will be posted
 */
function fb_post_to_fb_page($post_id) {
	global $facebook;
	global $post;
  
  // thanks to Tareq Hasan on http://wordpress.org/support/topic/plugin-facebook-bug-problems-when-publishing-to-a-page
  if ( isset ( $post_id ) ) {
    $post = get_post( $post_id );
    setup_postdata( $post );
  }
  
	$options = get_option('fb_options');

	if (!isset($options['social_publisher']) || !isset($options['social_publisher']['publish_to_fan_page']) || $options['social_publisher']['publish_to_fan_page'] == 'disabled')
		return;

	preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);
  
  if ( isset( $fan_page_info ) && isset( $fan_page_info[0] ) && isset( $fan_page_info[0][2] ) ) {
    // does current post type and the current theme support post thumbnails?
    if ( post_type_supports( $post->post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
      list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
    }
  
    $fan_page_message = get_post_meta($post_id, 'fb_fan_page_message', true);
    
    if ( !isset ( $post_thumbnail_url ) ) {
      $args = array('access_token' => $fan_page_info[0][3],
        'from' => $fan_page_info[0][2],
        'link' => apply_filters( 'rel_canonical', get_permalink()),
        'name' => get_the_title(),
        'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
        'description' => fb_strip_and_format_desc( $post ), 
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
        'description' => fb_strip_and_format_desc( $post ), 
        'message' => $fan_page_message,
      );
    }
  
    $args['ref'] = 'fbwpp';
  
    if ( ! isset( $facebook ) )
      return;
  
    $status_messages = array();
  
    try {
      $publish_result = $facebook->api('/' . $fan_page_info[0][2] . '/feed', 'POST', $args);
  
      update_post_meta($post_id, 'fb_fan_page_post_id', sanitize_text_field($publish_result['id']));
    }
    catch (WP_FacebookApiException $e) {
      $error_result = $e->getResult();
      
      if ($e->getCode() == 190) {
        $options['social_publisher']['publish_to_fan_page'] = false;
      
        update_option( 'fb_options', $options );
        
        $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to ' . $fan_page_info[0][1] . '\'s Timeline because the access token expired.  To reactivate publishing, visit the Facebook settings page and re-enable the "Publish to fan page" setting. Full error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true);
      }
      else {
        $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to ' . $fan_page_info[0][1] . '\'s Timeline. Error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true);
      }
    }
    
    if ( isset( $publish_result ) && isset( $publish_result['id'] ) ) {
      $status_messages[] = array( 'message' => sprintf( __( 'Posted to <a href="' . fb_get_permalink_from_feed_publish_id( sanitize_text_field( $publish_result['id'] ) ) . '" target="_blank">' . $fan_page_info[0][1] . '\'s Facebook Timeline</a>', true ) ), 'error' => false);
    }
    
    $existing_status_messages = get_post_meta($post_id, 'fb_status_messages', true);
    
    if ( !empty( $existing_status_messages ) ) {
      $status_messages = array_merge($existing_status_messages, $status_messages);
    }
    
    update_post_meta( $post->ID, 'fb_status_messages', $status_messages );
    add_filter( 'redirect_post_location', 'fb_add_new_post_location' );
  }
}



function fb_add_new_post_location( $loc ) {
	return add_query_arg( 'fb_message', 1, $loc ); 
}

add_action( 'admin_notices', 'fb_new_post_status_messages' );

function fb_new_post_status_messages() {
	if ( !empty($_GET['fb_message'] ) ) {
		global $post;
		
		if (isset ( $post ) ) {
			$fb_status_messages = get_post_meta( $post->ID, 'fb_status_messages', true );
			
			foreach ( $fb_status_messages as $fb_status_message ) {
				fb_admin_dialog( $fb_status_message['message'], $fb_status_message['error'] );
			}
		}
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

  $status_messages = array();
  
	if ( ! isset( $facebook ) )
		return;

	$options = get_option('fb_options');
	$fb_mentioned_friends = get_post_meta($post_id, 'fb_mentioned_friends', true);
	
	if ( !empty( $fb_mentioned_friends ) ) {
		
		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( $post->post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		}
		
		$mentioned_friends_message = get_post_meta($post_id, 'fb_mentioned_friends_message', true);

		$publish_ids_friends = array();
		
		$friends_posts = '';
		
		foreach($fb_mentioned_friends as $friend) {
			try {
				if ( !isset ( $post_thumbnail_url ) ) {
					$args = array(
						'link' => apply_filters( 'rel_canonical', get_permalink()),
						'name' => get_the_title(),
						'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
        					'description' => fb_strip_and_format_desc( $post ), 
						'message' => $mentioned_friends_message,
					);
				}
				else {
					$args = array(
						'link' => apply_filters( 'rel_canonical', get_permalink()),
						'picture' => $post_thumbnail_url,
						'name' => get_the_title(),
						'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
        					'description' => fb_strip_and_format_desc( $post ), 
						'message' => $mentioned_friends_message,
					);
				}

				$args['ref'] = 'fbwpp';
        
				$publish_result = $facebook->api('/' . $friend['id'] . '/feed', 'POST', $args);
				
				$publish_ids_friends[] = sanitize_text_field( $publish_result['id'] );
				
				$friends_posts .= '<a href="' . esc_url( fb_get_permalink_from_feed_publish_id( $publish_result['id'] ) ) . '" target="_blank"><img src="' . esc_url( 'http://graph.facebook.com/' . $friend['id'] . '/picture' ) . '" width="15"></a> ';
			}
			catch (WP_FacebookApiException $e) {
        $error_result = $e->getResult();
        
        if ($e->getCode() == 210) {
          $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to mentioned friend\'s Facebook Timeline. <img src="' . esc_url( 'http://graph.facebook.com/' . $friend['id'] . '/picture' ) . '" width="15"> Error: Page doesn\'t allow posts from other Facebook users. Full error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true );
        }
        else {
          $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to mentioned friend\'s Facebook Timeline. <img src="' . esc_url( 'http://graph.facebook.com/' . $friend['id'] . '/picture' ) . '" width="15"> Error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true );
        }
			}
		}
		
		update_post_meta($post_id, 'fb_mentioned_friends_post_ids', $publish_ids_friends);
		
		if ( !empty( $publish_ids_friends ) ) {
      $status_messages[] = array( 'message' => sprintf( __( 'Posted to mentioned friends\' Facebook Timelines. ' . $friends_posts ) ), 'error' => false );
		}
	}

	$fb_mentioned_pages = get_post_meta($post_id, 'fb_mentioned_pages', true);
  
  $pages_posts = '';
  
	if ( !empty( $fb_mentioned_pages ) ) {
	
		$mentioned_pages_message = get_post_meta($post_id, 'fb_mentioned_pages_message', true);

		$publish_ids_pages = array();

		foreach($fb_mentioned_pages as $page) {
			try {
				if ( !isset ( $post_thumbnail_url ) ) {
					$args = array(
						'link' => apply_filters( 'rel_canonical', get_permalink()),
						'name' => get_the_title(),
						'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
					        'description' => fb_strip_and_format_desc( $post ), 
						'message' => $mentioned_pages_message,
					);
				}
				else {
					$args = array(
						'link' => apply_filters( 'rel_canonical', get_permalink()),
						'picture' => $post_thumbnail_url,
						'name' => get_the_title(),
						'caption' => apply_filters( 'the_excerpt', get_the_excerpt() ),
					        'description' => fb_strip_and_format_desc( $post ), 
						'message' => $mentioned_pages_message,
					);
				}

				$args['ref'] = 'fbwpp';

				$publish_result = $facebook->api('/' . $page['id'] . '/feed', 'POST', $args);

				$publish_ids_pages[] = sanitize_text_field($publish_result['id']);
				
				$pages_posts .= '<a href="' . sanitize_text_field( fb_get_permalink_from_feed_publish_id ( $publish_result['id'] ) ) . '" target="_blank"><img src="http://graph.facebook.com/' . $page['id'] . '/picture" width="15" target="_blank"></a> ';

			}
			catch (WP_FacebookApiException $e) {
        $error_result = $e->getResult();
        
        if ($e->getCode() == 210) {
          $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to mentioned page\'s Facebook Timeline. <img src="http://graph.facebook.com/' . $page['id'] . '/picture" width="15"> Error: Page doesn\'t allow posts from other Facebook users. Full error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true );
        }
        else {
         $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to mentioned page\'s Facebook Timeline. <img src="http://graph.facebook.com/' . $page['id'] . '/picture" width="15"> Error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true );
        }
			}
		}

		update_post_meta($post_id, 'fb_mentioned_pages_post_ids', $publish_ids_pages);
		
		if ( !empty( $publish_ids_pages ) ) {
      $status_messages[] = array( 'message' => sprintf( __( 'Posted to mentioned pages\' Facebook Timelines. ' . $pages_posts ) ), 'error' => false );
		}
	}

  $fb_user = fb_get_current_user();
	
	if ( isset( $fb_user ) ) {
    $perms = $facebook->api('/me/permissions', 'GET', array('ref' => 'fbwpp'));
  }
  
  if ( isset ( $fb_user ) && isset($perms['data'][0]['manage_pages']) && isset($perms['data'][0]['publish_actions']) && isset($perms['data'][0]['publish_stream'])) {
    $author_message = get_post_meta($post_id, 'fb_author_message', true);
  
    try {
      //POST https://graph.facebook.com/me/news.reads?article=[article object URL]
      $publish_result = $facebook->api('/me/news.publishes', 'POST', array('message' => $author_message, 'article' => get_permalink($post_id)));
      
      update_post_meta($post_id, 'fb_author_post_id', sanitize_text_field($publish_result['id']));
      
    }
    catch (WP_FacebookApiException $e) {
      $error_result = $e->getResult();
      
      //Unset the option to publish to an author's Timeline, since the likely failure is because the admin didn't set up the proper OG action and object in their App Settings
      //if it's a token issue, it's because the Author hasn't auth'd the WP site yet, so don't unset the option (since that will turn it off for all authors)
      /*if ($e->getType() != 'OAuthException') {
        $options['social_publisher']['publish_to_authors_facebook_timeline'] = false;
      
        update_option( 'fb_options', $options );
      }*/
      
      $status_messages[] = array( 'message' => sprintf( __( 'Failed posting to your Facebook Timeline. Error: ' . json_encode ( $error_result['error'] ), true ) ), 'error' => true );
    }
    
  
    if ( isset( $publish_result ) && isset( $publish_result['id'] ) ) {
      $status_messages[] = array( 'message' => sprintf( __( 'Posted to <a href="http://www.facebook.com/' . sanitize_text_field( $publish_result['id'] ) . '" target="_blank">your Facebook Timeline</a>', false ) ), 'error' => false );
    }
  }
  
  $existing_status_messages = get_post_meta($post_id, 'fb_status_messages', true);
  
  if ( !empty( $existing_status_messages ) ) {
    $status_messages = array_merge($existing_status_messages, $status_messages);
  }
  
  update_post_meta( $post->ID, 'fb_status_messages', $status_messages );
  add_filter( 'redirect_post_location', 'fb_add_new_post_location' );
}

function fb_get_permalink_from_feed_publish_id( $id ) {
  preg_match_all("/(.*?)_(.*?)$/su", $id, $ids, PREG_SET_ORDER); 
  
  return 'http://www.facebook.com/' . $ids[0][1] . '/posts/' . $ids[0][2];
}

function fb_get_social_publisher_fields() {
	global $facebook;

	if ( ! isset( $facebook ) )
		return;
  
  $fan_page_option = array();
  
  if (!$facebook->getUser() ) {
    $fan_page_option = array(
			'name' => 'publish_to_fan_page',
			'type' => 'disabled_text',
			'disabled_text' => '<a href="#" onclick="authFacebook(); return false;">'.__('Link your Facebook account to your WordPress account to enable.','facebook').'</a>',
			'help_text' => __( 'All new posts will be automatically published to this Facebook Page.', 'facebook' ),
			);
  }
	else {
    $accounts = fb_get_user_pages();

    $accounts_options = array('disabled' => '[Disabled]');
    
    $options = get_option('fb_options');
  
    if (isset($options['social_publisher']) && isset($options['social_publisher']['publish_to_fan_page']) && $options['social_publisher']['publish_to_fan_page'] != 'disabled') {
      preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER); 
    }
  
    foreach($accounts as $account) {
      if (isset($account['name']) && isset($account['category']) && $account['category'] != 'Application') {
        $account_options_key = $account['name'] . "@@!!" . $account['id'] . "@@!!" . $account['access_token'];
        $accounts_options[$account_options_key] = $account['name'];
        
        if ( isset( $fan_page_info ) && isset( $fan_page_info[0] ) && isset( $fan_page_info[0][2] ) ) {
          if ($account['id'] == $fan_page_info[0][2]) {
            $options['social_publisher']['publish_to_fan_page'] = $account_options_key;
          
            update_option( 'fb_options', $options );
          }
        }
      }
    }
    
    $fan_page_option = array(
      'name' => 'publish_to_fan_page',
      'type' => 'dropdown',
      'options' => $accounts_options,
      'help_text' => __( 'New posts will be publish to this Facebook Page.', 'facebook' ),
      );
    
  }
	
	$parent = array(
		'name' => 'social_publisher',
		'type' => 'checkbox',
		'label' => __('Social Publisher', 'facebook'),
		'description' => __('Social Publisher allows you to publish to an Author\'s Facebook Timeline and Fan Page.	Authors can also mention Facebook friends and pages. ', 'facebook'),
		'help_link' => 'http://developers.facebook.com/wordpress',
		'image' => plugins_url( 'images/settings_social_publisher.png', __FILE__)
	);
  
	$children = array(
		array(
			'name' => 'publish_to_authors_facebook_timeline',
			'label' => "Publish to author's Timeline",
			'type' => 'checkbox',
			'default' => true,
			'onclick' => "window.open(\"http://developers.facebook.com/wordpress#author-og-setup\", \"Open Graph Setup\", \"fullscreen=no\");",
			'help_text' => __( 'Publish new posts to the author\'s Facebook Timeline and allow mentioning friends. You must setup Open Graph in your App Settings. Enable the feature to learn how.', 'facebook' ),
			'help_link' => 'http://developers.facebook.com/wordpress#author-og-setup',
		),
		$fan_page_option,
		array(
			'name' => 'mentions_show_on_homepage',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Authors can mentions Facebook friends and pages in posts.	Enable this to show mentions on the homepage, as part of the post and page previews.', 'facebook' ),
		),
		array(
			'name' => 'mentions_position',
			'type' => 'dropdown',
			'default' => 'both',
			'options' => array('top' => 'top', 'bottom' => 'bottom', 'both' => 'both'),
			'help_text' => __( 'Authors can mentions Facebook friends and pages in posts.	This controls where mentions will be displayed in the posts.', 'facebook' ),
		),
	);

	fb_construct_fields('settings', $children, $parent);
}

add_action( 'transition_post_status', 'fb_publish_later', 10, 3);
function fb_publish_later($new_status, $old_status, $post) {
  $options = get_option('fb_options');
  
	// check that the new status is "publish" and that the old status was not "publish"
	if ($new_status == 'publish' && $old_status != 'publish') {
		// only publish "public" post types
		$post_types = get_post_types( array('public' => true), 'objects' );
		foreach ( $post_types as $post_type ) {
			if ( $post->post_type == $post_type->name && isset( $options['social_publisher']['enabled'] ) ) {
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
		catch (WP_FacebookApiException $e) {
		}
	}
	
	$fb_author_post_id = get_post_meta($post_id, 'fb_author_post_id', true);

	if ($fb_author_post_id) {
		try {
			$delete_result = $facebook->api('/' . $fb_author_post_id, 'DELETE', array('ref' => 'fbwpp'));
		}
		catch (WP_FacebookApiException $e) {
		}

	}
	
	$fb_mentioned_pages_post_ids = get_post_meta($post_id, 'fb_mentioned_pages_post_ids', true);

	if ($fb_mentioned_pages_post_ids) {
		foreach($fb_mentioned_pages_post_ids as $page_post_ids) {
			try {
					$delete_result = $facebook->api('/' . $page_post_ids, 'DELETE', array('ref' => 'fbwpp'));
			}
			catch (WP_FacebookApiException $e) {
			}
		}
	}

	$fb_mentioned_friends_post_ids = get_post_meta($post_id, 'fb_mentioned_friends_post_ids', true);

	if ($fb_mentioned_friends_post_ids) {
		foreach($fb_mentioned_friends_post_ids as $page_post_ids) {
			try {
					$delete_result = $facebook->api('/' . $page_post_ids, 'DELETE', array('ref' => 'fbwpp'));
			}
			catch (WP_FacebookApiException $e) {
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

function fb_get_user_pages() {
	global $facebook;

	$accounts = array();

	if ( ! isset( $facebook ) )
		return $accounts;

	try {
		$accounts = $facebook->api('/me/accounts', 'GET', array('ref' => 'fbwpp'));
	}
	catch (WP_FacebookApiException $e) {
		return $accounts;
	}

	return $accounts['data'];
}

