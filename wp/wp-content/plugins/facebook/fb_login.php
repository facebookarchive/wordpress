<?php
global $fb_db_version;
$fb_db_version = 1;

function fb_install() {
	global $wpdb;
	global $fb_db_version;

	$table_name = $wpdb->prefix . "fb_users";
	 
	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		time int(11) NOT NULL,
		fb_uid bigint(20) NOT NULL,
		wp_uid bigint(20) NOT NULL,
		fb_username varchar(255) NOT NULL,
		UNIQUE KEY id (id)
		);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
 
	add_option("fb_db_version", $fb_db_version);
}

register_activation_hook(__FILE__,'fb_install');

function fb_check_connected_accounts() {
	global $wpdb;
	$table_name = $wpdb->prefix . "fb_users";
	
	//get current wordpress user
	global $current_user;
	get_currentuserinfo();
	
	//see if they have connected their account to facebook
	$fb_uid = $wpdb->get_var($wpdb->prepare("SELECT fb_uid FROM $table_name WHERE wp_uid = %d", $current_user->ID));
	
	//if no, show message prompting to connect
	if (empty($fb_uid)) {
		$fb_user = fb_get_current_user();
		
		if ($fb_user) {
			$rows_affected = $wpdb->insert( $table_name, array( 'time' => time(), 'fb_uid' => $fb_user['id'], 'wp_uid' => $current_user->ID, 'fb_username' => $fb_user['username'] ) );
		}
		else {
			fb_admin_dialog( __('The Facebook plugin is enabled.  <a href="#" onclick="authFacebook(); return false;">Link your Facebook account to your WordPress account</a> to get full Facebook functionality.  [TODO]Click here to learn more.', 'facebook' ), true);
		}
	}
	else {
		
	}
}

add_action('admin_notices', 'fb_check_connected_accounts');

function fb_get_current_user() {
	global $facebook;
	
	try {
		$user = $facebook->api('/me');
		
		return $user;
	}
	catch (FacebookApiException $e) {
		//error_log('The Facebook user must be logged in.');
	}
}




?>