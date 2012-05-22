<?php
/**
 * Check to see if the active admin has authenticated with Facebook
 * If not, display a warning message
 *
 * @since 1.0
 */
function fb_check_connected_accounts() {
	//get current wordpress user
	global $current_user;
	get_currentuserinfo();

	$options = get_option('fb_options');

	// check if we have enough info to handle the authFacebook function
	if ( ! $options || empty( $options['app_id'] ) || empty( $options['app_secret'] ) )
		return;

	//see if they have connected their account to facebook
	$fb_data = get_user_meta($current_user->ID, 'fb_data', true);

	//if no, show message prompting to connect
	if (empty($fb_data['uid']) && $options['social_publisher']['enabled']) {
		$fb_user = fb_get_current_user();

		if ($fb_user) {
			$fb_user_data = array('fb_uid' => $fb_user['id'], 'username' => $fb_user['username'], 'activation_time' => time());

			update_user_meta($current_user->ID, 'fb_data', $fb_user_data);
		}
		else {
			fb_admin_dialog( __('Facebook social publishing is enabled. <a href="#" onclick="authFacebook(); return false;">Link your Facebook account to your WordPress account</a> to get full functionality, including adding new Posts to your Timeline.', 'facebook' ), true);
		}
	}
	else {

	}
}
add_action('admin_notices', 'fb_check_connected_accounts');

/**
 * Gets and returns the current, active Facebook user
 *
 * @since 1.0
 */
function fb_get_current_user() {
	global $facebook;

	if ( ! isset( $facebook ) )
		return;

	try {
		$user = $facebook->api('/me', 'GET', array('ref' => 'fbwpp'));

		return $user;
	}
	catch (FacebookApiException $e) {
		//error_log('The Facebook user must be logged in.');
	}
}
?>