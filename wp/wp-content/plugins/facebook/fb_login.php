<?php
function fb_check_connected_accounts() {
	//get current wordpress user
	global $current_user;
	get_currentuserinfo();

	//see if they have connected their account to facebook
	$fb_data = get_user_meta($current_user->ID, 'fb_data', true);

	//if no, show message prompting to connect
	if (empty($fb_data['uid'])) {
		$fb_user = fb_get_current_user();

		if ($fb_user) {
			$fb_user_data = array('fb_uid' => $fb_user['id'], 'username' => $fb_user['username'], 'activation_time' => time());

			update_user_meta($current_user->ID, 'fb_data', $fb_user_data);
		}
		else {
			fb_admin_dialog( __('The Facebook plugin is enabled. <a href="#" onclick="authFacebook(); return false;">Link your Facebook account to your WordPress account</a> to get full Facebook functionality.', 'facebook' ), true);
		}
	}
	else {

	}
}
add_action('admin_notices', 'fb_check_connected_accounts');

function fb_get_current_user() {
	global $facebook;

	if ( ! isset( $facebook ) )
		return;

	try {
		$user = $facebook->api('/me');

		return $user;
	}
	catch (FacebookApiException $e) {
		//error_log('The Facebook user must be logged in.');
	}
}
?>