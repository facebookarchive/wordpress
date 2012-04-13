<?php

if (!get_option('users_can_register')) return;

add_action('login_enqueue_scripts','sfc_register_enqueue_scripts');
function sfc_register_enqueue_scripts() {
	wp_enqueue_script('jquery');
}

add_action('sfc_login_new_fb_user', 'sfc_register_redirect');
function sfc_register_redirect() {
	wp_redirect(site_url('wp-login.php?action=register', 'login'));
	exit;
}

remove_action('login_form','sfc_login_add_login_button');
add_action('login_form','sfc_register_add_login_button');
function sfc_register_add_login_button() {
	global $action;
	if ($action == 'login') echo '<p><fb:login-button v="2" registration-url="'.site_url('wp-login.php?action=register', 'login').'" scope="email,user_website" onlogin="window.location.reload();" /></p><br />';
}

add_action('register_form','sfc_register_form');
function sfc_register_form() {
	add_action('sfc_async_init', 'sfc_register_form_script');
	
	$fields = json_encode( apply_filters('sfc_register_fields',array(
		array('name'=>'name', 'view'=>'prefilled'),
		array('name'=>'username', 'description'=>'Choose a username', 'type'=>'text'),
		array('name'=>'email'),
		array('name'=>'captcha'),
		)
	) );
	?>
<fb:registration 
  fields='<?php echo $fields; ?>'
  redirect-uri="<?php echo apply_filters('sfc_register_redirect', site_url('wp-login.php?action=register', 'login') ); ?>"
  width="262"
  >
</fb:registration>
<?php
}

function sfc_register_form_script() {
?>
jQuery('#registerform p').hide();
jQuery('#reg_passmail').show();
<?php
}

add_action('register_form','sfc_add_base_js',20);

// catch the signed request
add_action('login_form_register','sfc_register_handle_signed_request'); 
function sfc_register_handle_signed_request() {
	global $wpdb;
	$options = get_option('sfc_options');
	if (!empty($_POST['signed_request'])) {
		list($encoded_sig, $payload) = explode('.', $_POST['signed_request'], 2); 

		// decode the data
		$sig = sfc_base64_url_decode($encoded_sig);
		$data = json_decode(sfc_base64_url_decode($payload), true);
		if (!isset($data['algorithm']) || strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			return;
		}

		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $options['app_secret'], true);
		if ($sig !== $expected_sig) {
			return;
		}
		if (isset($data['registration'])) {
			$info = $data['registration'];
			if (isset($info['username']) && isset($info['email'])) {
			
				// first check to see if this user already exists in the db
				$user_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $info['email']) );
				if ($user_id) {
					$fbuid = $data['user_id'];

					update_usermeta($user_id, 'fbuid', $fbuid); // connect the account so we don't have to query this again
					
					// redirect to admin and exit
					wp_redirect( add_query_arg( array('updated' => 'true'), self_admin_url( 'profile.php' ) ) );
					exit;
				} else {
					// new user, set the registration info
					$_POST['user_login'] = $info['username'];
					$_POST['user_email'] = $info['email'];
					do_action('sfc_register_request',$info);
				}
			}
		}
	}
}