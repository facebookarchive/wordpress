<?php

// change this if you want to try to publish elsewhere (like to "links")
if (!defined('SFC_PUBLISH_ENDPOINT')) 
	define('SFC_PUBLISH_ENDPOINT','feed');

// add the meta boxes
add_action('admin_menu', 'sfc_publish_meta_box_add');
function sfc_publish_meta_box_add() {
	$post_types = apply_filters('sfc_publish_post_types', get_post_types( array('public' => true), 'objects' ) );
	foreach ( $post_types as $post_type ) {
		add_meta_box('sfc-publish-div', __('Facebook Publisher', 'sfc'), 'sfc_publish_meta_box', $post_type->name, 'side');
	}
}

// add the admin sections to the sfc page
add_action('admin_init', 'sfc_publish_admin_init');
function sfc_publish_admin_init() {
	add_settings_section('sfc_publish', __('Publish Settings', 'sfc'), 'sfc_publish_section_callback', 'sfc');
	add_settings_field('sfc_publish_flags', __('Automatic Publishing', 'sfc'), 'sfc_publish_auto_callback', 'sfc', 'sfc_publish');
	add_settings_field('sfc_publish_extended_permissions', __('Extended Permissions', 'sfc'), 'sfc_publish_extended_callback', 'sfc', 'sfc_publish');
	add_settings_field('sfc_publish_token_checks', __('Token Checks', 'sfc'), 'sfc_publish_token_checks', 'sfc', 'sfc_publish');
	wp_enqueue_script('jquery');
}

function sfc_publish_section_callback() {
	echo "<p>".__('Settings for the SFC-Publish plugin. The manual Facebook Publishing buttons can be found on the Edit Post or Edit Page screen, after you publish a post. If you can\'t find them, try scrolling down or seeing if you have the box disabled in the Options dropdown.', 'sfc')."</p>";
}

function sfc_publish_auto_callback() {
	$options = get_option('sfc_options');
	if (!isset($options['autopublish_app'])) $options['autopublish_app'] = false;
	if (!isset($options['autopublish_profile'])) $options['autopublish_profile'] = false;
	
	if (!empty($options['fanpage'])) {
		echo '<p><label>';
		_e('Automatically Publish to Facebook Fan Page', 'sfc');
		echo '<input type="checkbox" name="sfc_options[autopublish_app]" value="1" ';
		checked('1', $options['autopublish_app']);
		echo ' /></label></p>';
	}
	?>
	<p><label><?php _e('Automatically Publish to Facebook Profile:', 'sfc'); ?> <input type="checkbox" name="sfc_options[autopublish_profile]" value="1" <?php checked('1', $options['autopublish_profile']); ?> /></label></p>
<?php
}

function sfc_publish_extended_callback() {
	$options = get_option('sfc_options');

?><p><?php _e('In order for the SFC-Publish plugin to be able to publish your posts automatically, you must grant some "Extended Permissions"
to the plugin.', 'sfc'); ?></p>
<p><?php _e('To do so, click this button. This will also cause the page to refresh, in order to save the results.','sfc'); ?></p>
<input type="hidden" id="token" name="sfc_options[access_token]" value="<?php echo $options['access_token']; ?>" />
<script type="text/javascript">
function sfcPubToken() {
	FB.getLoginStatus(function(response) {
		if (response.authResponse.accessToken) {
			jQuery('#token').val(response.authResponse.accessToken);
			jQuery('#submit').click();
		}
	});
}
</script>
<fb:login-button scope="offline_access,publish_stream,manage_pages" onlogin="sfcPubToken();"><fb:intl>Grant SFC Permissions</fb:intl></fb:login-button>
<?php
}

function sfc_publish_token_checks() {
	$options = get_option('sfc_options');
	
	if ( !wp_http_supports(array(), "https://graph.facebook.com/") ) {
		echo '<p>';
		_e("<strong>Oops! There's a problem.</strong>", 'sfc');
		echo '</p><p>';
		_e("It seems that your server setup cannot support making SSL connections to other servers. Since Facebook requires 
		SSL to connect to their API servers, then SFC can't directly connect to Facebook on this server configuration. 
		This means that the plugin will not be able to retrieve access tokens properly, nor automatically publish anything 
		to page or user walls.",'sfc');
		echo '</p><p>';
		_e("To fix the problem, you need to ask your webhost to enable OpenSSL support or Curl support. With either of these enabled,
		this message will go away and all should be well.",'sfc');
		echo '</p>';
		return;
	}
?>
<p><?php _e('In order for SFC to be able to automatically publish to Facebook, it must retrieve and save "tokens" from Facebook. The status of each of these is given below.','sfc'); ?></p>
<ul>
<?php
	if (!empty($options['user']) && !empty($options['access_token'])) {
		?><li style="background-color: #c3ff88; border-color: #8dff1c;"><?php _e('User ID and Access Token found!', 'sfc'); ?></li><?php
	} else {
		?><li class="error"><?php _e('No User or Access Token found. Try re-saving this page.', 'sfc'); ?></li><?php
	}

	if (!empty($options['app_access_token'])) {
		?><li style="background-color: #c3ff88; border-color: #8dff1c;"><?php _e('Application Access Token found!', 'sfc'); ?></li><?php
	} else {
		?><li class="error"><?php _e('Application Access Token not found. Try re-saving this page.', 'sfc'); ?></li><?php
	}

	if (!empty($options['fanpage']) && !empty($options['page_access_token'])) {
		?><li style="background-color: #c3ff88; border-color: #8dff1c;"><?php _e('Fan Page Access Token found!', 'sfc'); ?></li><?php
	} else if (!empty($options['fanpage']) && empty($options['page_access_token'])) {
		?><li class="error"><?php _e('Fan Page Access Token not found. Try re-saving this page.', 'sfc'); ?></li><?php
	}

	?>
</ul>
<?php
}

function sfc_publish_meta_box( $post ) {
	$options = get_option('sfc_options');

	if ($post->post_status == 'private') {
		echo '<p>'.__('Why would you put private posts on Facebook, for all to see?', 'sfc').'</p>';
		return;
	}

	if ($post->post_status !== 'publish') {
		echo '<p>'.__('After publishing the post, you can send it to Facebook from here.', 'sfc').'</p>';
		return;
	}

	// look for the images/video to add with image_src
	$images = sfc_media_find_images($post);
	$video = sfc_media_find_video($post);
	if ( !empty( $video['og:image'] ) ) {
		array_unshift($images, $video['og:image'][0]);
	}
 
 	$feed['app_id'] = $options["appid"];
 	$feed['method'] = SFC_PUBLISH_ENDPOINT;
 	$feed['display'] = 'iframe';
 	$feed['scrape'] = 'true';
 	$permalink = apply_filters('sfc_publish_permalink',wp_get_shortlink($post->ID),$post->ID);
 	$feed['link'] = $permalink;
	if ($images) $feed['picture'] = $images[0];
	if ($video) $feed['source'] = $video['og:video'];
	
	$title = get_the_title($post->ID);
	$title = strip_tags($title);
	$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
	$title = htmlspecialchars_decode($title);

	$feed['name'] = $title;
	$feed['description'] = sfc_base_make_excerpt($post);
	$feed['caption'] = ' ';
	$actions[0]['name'] = 'Share';
	$actions[0]['link'] = 'http://www.facebook.com/share.php?u='.urlencode($permalink);
	
	$feed['actions'] = json_encode($actions);

	$attachment = apply_filters('sfc_publish_manual', $feed, $post);
	
	// personal publish
	$ui = $feed;
	
	$html = '<input type="button" class="button-primary" onclick="sfcPersonalPublish(); return false;" value="'. esc_attr(__('Publish to your Facebook Profile', 'sfc')).'" />';
	?>
	<script type="text/javascript">
	function sfcPersonalPublish() {
		FB.ui(<?php echo json_encode($ui); ?>);
	}

	<?php
	if (!empty($options['fanpage'])) {
		$ui['from'] = $options['fanpage'];
		$ui['to'] = $options['fanpage'];

	?>
	function sfcPublish() {
		FB.ui(<?php echo json_encode($ui); ?>);
	}
	<?php
		$html = '<input type="button" class="button-primary" onclick="sfcPublish(); return false;" value="'. esc_attr(__('Publish to Facebook Fan Page', 'sfc')).'" />'.$html;
	}
	?>

	function sfcShowPubButtons() {
		jQuery('#sfc-publish-buttons').html(<?php echo json_encode($html); ?>);
	}

	</script>
	<div id="sfc-publish-buttons"><p><?php _e('If you can see this, then there is some form of problem showing you the Facebook publishing buttons. This may be caused by a plugin conflict or some form of bad javascript on this page. Try reloading or disabling other plugins to find the source of the problem.', 'sfc'); ?></p></div>
	<?php

	add_action('sfc_async_init','sfc_publish_show_buttons');
}

function sfc_publish_show_buttons() {
?>
FB.getLoginStatus(function(response) {
	if (response.authResponse) {
		sfcShowPubButtons();
	} else {
		jQuery('#sfc-publish-buttons').html('<fb:login-button v="2" scope="offline_access,publish_stream,manage_pages" onlogin="sfcShowPubButtons();"><fb:intl><?php echo addslashes(__('Connect with Facebook', 'sfc')); ?></fb:intl></fb:login-button>');
		FB.XFBML.parse();
	}
});
<?php
}

// this function prevents edits to existing posts from auto-posting
add_action('transition_post_status','sfc_publish_auto_check',10,3);
function sfc_publish_auto_check($new, $old, $post) {
	if ($new == 'publish' && $old != 'publish') {
		$post_types = apply_filters('sfc_publish_post_types', get_post_types( array('public' => true), 'objects' ) );
		foreach ( $post_types as $post_type ) {
			if ( $post->post_type == $post_type->name ) {
				sfc_publish_automatic($post->ID, $post);
				break;
			}
		}
	}
}

function sfc_publish_automatic($id, $post) {

	// check to make sure post is published
	if ($post->post_status !== 'publish') return;

	// check options to see if we need to send to FB at all
	$options = get_option('sfc_options');
	if (!$options['autopublish_app'] && !$options['autopublish_profile'])
		return;

	// build the post to send to FB

	// look for the images/video to add with image_src
	$images = sfc_media_find_images($post);
	$video = sfc_media_find_video($post);
	if ( !empty( $video['og:image'] ) ) {
		array_unshift($images, $video['og:image'][0]);
	}
 
	// build the attachment
	$permalink = apply_filters('sfc_publish_permalink',wp_get_shortlink($post->ID),$post->ID);
	
	$title = get_the_title($post->ID);
	$title = strip_tags($title);
	$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
	$title = htmlspecialchars_decode($title);

	$attachment['name'] = $title;
	$attachment['message'] = $title;
	$attachment['link'] = $permalink;
	$attachment['scrape'] = 'true';
	$attachment['description'] = sfc_base_make_excerpt($post);
	$attachment['caption'] = ' ';
	
	if (!empty($images)) $attachment['picture'] = $images[0];
	if (!empty($video)) $attachment['source'] = $video['og:video'];

	// Actions
	$actions[0]['name'] = 'Share';
	$actions[0]['link'] = 'http://www.facebook.com/share.php?u='.urlencode($permalink);

	$attachment['actions'] = json_encode($actions);
	
	$attachment = apply_filters('sfc_publish_automatic', $attachment, $post);
	
	// publish to page
	if ( !empty($options['fanpage']) && $options['autopublish_app'] && !get_post_meta($id,'_fb_post_id_app',true) ) {

		$url = "https://graph.facebook.com/{$options['fanpage']}/".SFC_PUBLISH_ENDPOINT;
		$attachment['access_token'] = $options['page_access_token'];
		
		$data = wp_remote_post($url, array('body'=>$attachment));

		if (!is_wp_error($data)) {
			$resp = json_decode($data['body'],true);
			if ($resp['id']) update_post_meta($id,'_fb_post_id_app',$resp['id']);
		}
	}

	// publish to profile
	if ($options['autopublish_profile'] && !get_post_meta($id,'_fb_post_id_profile',true)) {

		$url = "https://graph.facebook.com/{$options['user']}/".SFC_PUBLISH_ENDPOINT;

		// check the cookie for an access token. If not found, try to use the stored one.
		$cookie = sfc_cookie_parse();
		if ($cookie['access_token']) $attachment['access_token'] = $cookie['access_token'];
		else $attachment['access_token'] = $options['access_token'];

		$data = wp_remote_post($url, array('body'=>$attachment));

		if (!is_wp_error($data)) {
			$resp = json_decode($data['body'],true);
			if ($resp['id']) update_post_meta($id,'_fb_post_id_profile',$resp['id']);
		}
	}
}

add_filter('sfc_validate_options','sfc_publish_validate_options');
function sfc_publish_validate_options($input) {
	$options = get_option('sfc_options');

	if (isset($input['autopublish_app']) && $input['autopublish_app'] != 1) $input['autopublish_app'] = 0;
	if (isset($input['autopublish_profile']) && $input['autopublish_profile'] != 1) $input['autopublish_profile'] = 0;

	unset($input['user']);
	unset($input['page_access_token']);
	unset($input['app_access_token']);

	// find the access token and save it if it's there
	$cookie = sfc_cookie_parse();
	if ($input['access_token']) {
		$input['user'] = $cookie['user_id'];

		// for fan pages, we need to go get their access token
		if ($input['fanpage']) {
			// connect to FB, find a list of the available Pages
			$data = wp_remote_get("https://graph.facebook.com/{$input['user']}/accounts?access_token={$input['access_token']}", array('sslverify'=>0));
			if (!is_wp_error($data)) {
				$pages = json_decode($data['body'],true);
				if ( is_array( $pages ) && isset( $pages['data'] ) ) foreach ($pages['data'] as $page) {
					if ($page['id'] == $input['fanpage']) {
						$input['page_access_token'] = $page['access_token'];
						break;
					}
				}
			}
		}

		// get application access token
		$data = wp_remote_get("https://graph.facebook.com/oauth/access_token?client_id={$input['appid']}&client_secret={$input['app_secret']}&type=client_cred", array('sslverify'=>0));
		if (!is_wp_error($data)) {
			$token = $data['body'];
			if (strpos($token,'access_token=') !== false) {
				$input['app_access_token'] = str_replace('access_token=','',$token);
			}
		}
	}

	return $input;
}

// fix crazy shortlink nonsense
add_filter('sfc_publish_permalink', 'sfc_publish_shortlink_fix', 10, 2);
function sfc_publish_shortlink_fix($link, $id) {
	if (empty($link)) $link = get_permalink($id);
	return $link;
}
