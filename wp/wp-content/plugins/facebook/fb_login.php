<?php
global $fb_db_version;
$fb_db_version = 1;

function fb_install() {
	global $wpdb;
	global $fb_db_version;

	$table_name = $wpdb->prefix . "users";
	 
	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		time int(11) NOT NULL,
		fb_uid bigint(20) NOT NULL,
		wp_uid bigint(20) NOT NULL,
		fb_vanity varchar(255) NOT NULL,
		UNIQUE KEY id (id)
		);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
 
	add_option("fb_db_version", $fb_db_version);
}

register_activation_hook(__FILE__,'fb_install');

function fb_install_data() {
	global $wpdb;
	$welcome_name = "Mr. WordPress";
	$welcome_text = "Congratulations, you just completed the installation!";

	$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
}

register_activation_hook(__FILE__,'fb_install_data');

/*
if ( current_user_can('update_core') ) {
	$msg = sprintf( __('<a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> is available! <a href="%2$s">Please update now</a>.'), $cur->current, network_admin_url( 'update-core.php' ) );
} else {
	$msg = sprintf( __('<a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> is available! Please notify the site administrator.'), $cur->current );
}
	echo "<div class='update-nag'>$msg</div>";
add_action( 'admin_notices', 'update_nag', 3 );
*/

//get current user

//see if they have connected their account

//if no, show message prompting to connect

//once connected, associate accounts

//update subscribe to point to their vanity


?>