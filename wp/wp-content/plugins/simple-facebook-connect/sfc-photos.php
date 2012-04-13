<?php
// photos plugin

add_filter('media_upload_tabs','sfc_photos_upload_tab');
function sfc_photos_upload_tab($tabs) {
	$tabs['sfcphotos'] = 'Facebook Images';
	return $tabs;
}

add_action('media_upload_sfcphotos', 'sfc_photos_tab');
function sfc_photos_tab() {
	$errors = array();

	return wp_iframe( 'media_sfc_photos_form', $errors );
}

function media_sfc_photos_form($errors) {
	global $redir_tab, $type;

	$redir_tab = 'sfcphotos';
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);
	
	$user = sfc_cookie_parse();

	if (!isset($user['user_id'])) {
		?><p><?php _e("You don't appear to be logged into Facebook. Click the button below to login and grant photo access.",'sfc'); ?></p>
		<fb:login-button v="2" scope="offline_access,user_photos" onlogin="location.reload();"><fb:intl><?php _e('Connect with Facebook', 'sfc'); ?></fb:intl></fb:login-button><?php
	}
	
	if ( isset($_GET['send']) && !preg_match('/^[0-9]+$/i', $_GET['send'])) {
		// photo ids are bigints
		unset($_GET['send']);
	}
	
	if ( isset($_GET['send']) ){
		$send_id = $_GET['send'];
		
		$photo = sfc_photo_get_photo($send_id, $user['code']);
		
		$photo = apply_filters('sfc_photo_insert',$photo);
		
		list( $width, $height ) = image_constrain_size_for_editor($photo['width'], $photo['height'], 'large');
		
		$alt = '';
		if (!empty($photo['name'])) {
			$alt = esc_attr($photo['name']);
		}
		
		$html = "<a href='{$photo['link']}'><img src='{$photo['source']}' alt='{$alt}' width='{$width}' height='{$height}' class='size-full fb-image-{$photo['id']}'/></a>";

		if (!empty($photo['name'])) {
			$html = "[caption id='fb_attachment_{$photo['id']}' width='{$width}' caption='{$alt}']" . $html . '[/caption]';
		}
		
		return media_send_to_editor($html);
	}

	if (!empty($_GET['album']) && !preg_match('/^[0-9]+$/i', $_GET['album'])) {
		// album ids are bigints
		unset($_GET['album']);
	}
	
	if (!empty($_GET['album'])) {
		// show an album
		$album = $_GET['album'];

		if ( false === ( $photos = get_transient('sfcphotos-'.$album) ) ) {
			$photos = sfc_remote($album, 'photos', array('code'=>$user['code'], 'timeout' => 60, 'limit' => 0));

			if ($photos === false) {
				?><p><?php _e('Facebook is being really, really slow and not responding to requests in a reasonable period of time. Try again later.','sfc'); ?></p><?php	
				return;
			}

			// cache the data because Facebook's Graph API is slow as hell
			if (!empty($photos)) set_transient('sfcphotos-'.$album, $photos, 6*60*60); // 6 hours
		}
			
		if ( empty($photos['data']) ) {
			?><p><?php _e('This album appears to be empty','sfc'); ?></p><?php
			$link = admin_url("media-upload.php?post_id=$post_id&type=$type&tab=$redir_tab");
			echo "<p><a href='$link'>".__('Go Back','sfc')."</a></p>";
			return;
		}
		
		$photos = $photos['data'];

		$link = admin_url("media-upload.php?post_id=$post_id&type=$type&tab=$redir_tab");
		echo "<p><a href='$link'>".__('Go Back','sfc')."</a></p>";

		echo '<table><tr>'; 
		$i=1;
		foreach ($photos as $photo) {
			echo '<td>';

			$link = admin_url("media-upload.php?post_id=$post_id&type=$type&tab=$redir_tab&album={$album['id']}&send={$photo['id']}");

			echo "<p><a href='$link'><img src='{$photo['picture']}' /></a></p>";

			echo '</td>';

			if ($i%3 == 0) echo '</tr><tr>';
			$i++;
				}
		echo '</tr></table>';
		
		
	} else {

		if ( false === ( $albums = get_transient('sfcphotos-'.$user['user_id']) ) ) {
			$albums = sfc_remote($user['user_id'], 'albums', array('code'=>$user['code'], 'timeout' => 60, 'limit' => 0));
			
			if ($albums === false) {
				?><p><?php _e('Facebook is being really, really slow and not responding to requests in a reasonable period of time. Try again later.','sfc'); ?></p><?php	
				return;
			}

			// cache the data because Facebook's Graph API is slow as hell
			if (!empty($albums['data'])) set_transient('sfcphotos-'.$user['user_id'], $albums, 6*60*60); // 6 hours
		}

		if ( empty($albums['data']) ) {
			?><p><?php _e('Either you have no photo albums on Facebook, or you have not granted the site permission to access them. Either way, click the button below to login and grant access.','sfc'); ?></p>
			<fb:login-button v="2" scope="offline_access,user_photos" onlogin="location.reload();"><fb:intl><?php _e('Connect with Facebook', 'sfc'); ?></fb:intl></fb:login-button><?php
		} else {

			$albums = $albums['data'];

			echo '<table><tr>'; 
			$i=1;
			foreach ($albums as $album) {
				echo '<td>';

				$link = admin_url("media-upload.php?post_id=$post_id&type=$type&tab=$redir_tab&album={$album['id']}");
				// retrieve the cover image for the album
				if (false !== ($photo = sfc_photo_get_photo($album['cover_photo'], $user['code']) ) ) {
					echo "<p><a href='$link'><img src='{$photo['picture']}' /></a></p>";
				} else {
					// TODO cover not available
				}

				echo "<p><a href='$link'>{$album['name']}</a></p>";
				echo '</td>';

				if ($i%3 == 0) echo '</tr><tr>';
				$i++;
			}
			echo '</tr></table>';
		}
	}
?>
<?php
}

// generic function to get a photo's info from FB and store it in a transient cache (speed reasons)
function sfc_photo_get_photo($fbid, $code) {

	if (false === ($photo = get_transient('sfcphoto-'.$fbid) ) ) {
		$photo = sfc_remote( $fbid, '', array('code'=>$code) );
		if (!empty($photo['images'])) {
			set_transient('sfcphoto-'.$fbid, $photo, 60*60);
		} else {
			$photo = false;
		}
	}
	
	return $photo;
}
