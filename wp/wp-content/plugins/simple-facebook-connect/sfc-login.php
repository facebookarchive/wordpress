<?php

// if you want people to be unable to disconnect their WP and FB accounts, set this to false in wp-config
if (!defined('SFC_ALLOW_DISCONNECT'))
	define('SFC_ALLOW_DISCONNECT',true);

// fix the reauth redirect problem
add_action('login_form_login','sfc_login_reauth_disable');
function sfc_login_reauth_disable() {
	$_REQUEST['reauth'] = false;
}

// add the section on the user profile page
add_action('profile_personal_options','sfc_login_profile_page');

function sfc_login_profile_page($profile) {
	$options = get_option('sfc_options');
?>
	<table class="form-table">
		<tr>
			<th><label><?php _e('Facebook Connect', 'sfc'); ?></label></th>
<?php
	$fbuid = get_user_meta($profile->ID, 'fbuid', true);
	if (empty($fbuid)) {
		?>
			<td><p><fb:login-button scope="email" v="2" size="large" onlogin="sfc_login_update_fbuid(0);"><fb:intl><?php _e('Connect this WordPress account to Facebook', 'sfc'); ?></fb:intl></fb:login-button></p></td>
		</tr>
	</table>
	<?php
	} else { ?>
		<td><p><?php _e('Connected as', 'sfc'); ?>
		<fb:profile-pic size="square" width="32" height="32" uid="<?php echo $fbuid; ?>" linked="true"></fb:profile-pic>
		<fb:name useyou="false" uid="<?php echo $fbuid; ?>"></fb:name>.
<?php if (SFC_ALLOW_DISCONNECT) { ?>
		<input type="button" class="button-primary" value="<?php _e('Disconnect this account from WordPress', 'sfc'); ?>" onclick="sfc_login_update_fbuid(1); return false;" />
<?php } ?>
		</p></td>
	<?php } ?>
	</tr>
	</table>
	<?php
}

add_action('admin_footer','sfc_login_update_js',30);
function sfc_login_update_js() {
	if (defined('IS_PROFILE_PAGE')) {
		?>
		<script type="text/javascript">
		function sfc_login_update_fbuid(disconnect) {
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			if (disconnect == 1) {
				var fbuid = 0;
			} else {
				var fbuid = 1; // it gets it from the cookie
			}
			var data = {
				action: 'update_fbuid',
				fbuid: fbuid
			}
			jQuery.post(ajax_url, data, function(response) {
				if (response == '1') {
					location.reload(true);
				}
			});
		}
		</script>
		<?php
	}
}

add_action('wp_ajax_update_fbuid', 'sfc_login_ajax_update_fbuid');
function sfc_login_ajax_update_fbuid() {
	$options = get_option('sfc_options');
	$user = wp_get_current_user();

	$fbuid = (int)($_POST['fbuid']);

	if ($fbuid) {
		// get the id from the cookie
		$cookie = sfc_cookie_parse();
		if (empty($cookie)) { echo 1; exit; }
		$fbuid = $cookie['user_id'];
	} else {
		if (!SFC_ALLOW_DISCONNECT) { echo 1; exit(); }
		$fbuid = 0;
	}

	update_usermeta($user->ID, 'fbuid', $fbuid);
	echo 1;
	exit();
}

add_action('login_form','sfc_login_add_login_button');
function sfc_login_add_login_button() {
	global $action;
	if ($action == 'login') echo '<p><fb:login-button v="2" scope="email,user_website" onlogin="window.location.reload();" /></p><br />';
}

// add the fb icon to the admin bar, showing you're connected via FB login
add_filter('admin_user_info_links','sfc_login_admin_header');
function sfc_login_admin_header($links) {
	$user = wp_get_current_user();
	$fbuid = get_user_meta($user->ID, 'fbuid', true);
	$icon = plugins_url('/images/fb-icon.png', __FILE__);
	if ($fbuid) $links[6]="<a href='http://www.facebook.com/profile.php?id=$fbuid'><img src='$icon' /> Facebook</a>";
	return $links;
}

// add the Facebook menu item to the admin bar (3.3+) 
add_action( 'add_admin_bar_menus', 'sfc_add_admin_bar' );
function sfc_add_admin_bar() {
	add_action( 'admin_bar_menu', 'sfc_admin_bar_my_account_menu', 11 );
}
function sfc_admin_bar_my_account_menu( $wp_admin_bar ) {
	$user = wp_get_current_user();
	$fbuid = get_user_meta($user->ID, 'fbuid', true);

	if ($fbuid) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-account',
			'id'     => 'facebook-profile',
			'title'  => __( 'Facebook Profile' ),
			'href' => "http://www.facebook.com/profile.php?id={$fbuid}",
			'meta'   => array(
				'class' => 'user-info-item',
			),
		) );
	}
}

// do the actual authentication
//
// note: Because of the way auth works in WP, sometimes you may appear to login
// with an incorrect username and password. This is because FB authentication
// worked even though normal auth didn't.
add_filter('authenticate','sfc_login_check',90);
function sfc_login_check($user) {
	if ( is_a($user, 'WP_User') ) { return $user; } // check if user is already logged in, skip FB stuff

	// check for the valid cookie
	$cookie = sfc_cookie_parse();
	if (empty($cookie)) return $user;

	// the cookie is signed using our secret, so if we get it back from sfc_cookie_parse, then it's authenticated. So just log the user in.
	$fbuid=$cookie['user_id'];
	
	if($fbuid) {
		global $wpdb;
		$user_id = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'fbuid' AND meta_value = %s", $fbuid) );

		if ($user_id) {
			$user = new WP_User($user_id);
		} else {
			$data = sfc_remote($fbuid, '', array(
				'fields'=>'email',
				'code'=>$cookie['code'],
			));

			if (!empty($data['email'])) {
				$user_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $data['email']) );
			}

			if ($user_id) {
				$user = new WP_User($user_id);
				update_usermeta($user->ID, 'fbuid', $fbuid); // connect the account so we don't have to query this again
			}

			if (!$user_id) {
				do_action('sfc_login_new_fb_user'); // TODO hook for creating new users if desired
				global $error;
				$error = '<strong>'.__('ERROR', 'sfc').'</strong>: '.__('Cannot log you in. There is no account on this site connected to that Facebook user identity.', 'sfc');
			}
		}
	}

	return $user;
}

// we have to change the logout to use a javascript redirect. No other way to make FB log out properly and stop giving us the cookie.
add_action('wp_logout','sfc_login_logout');
function sfc_login_logout() {
	$options = get_option('sfc_options');
	
	// check for FB cookies, if not found, do nothing
	$cookie = sfc_cookie_parse();
	if (empty($cookie)) return;
	
	// force remove the cookie, since FB can't be relied on to do it properly
	$domain = '.'.parse_url(home_url('/'), PHP_URL_HOST);
	setcookie('fbsr_' . $options['appid'], ' ', time() - 31536000, "/", $domain);
	
	// we have an FB login, log them out with a redirect
	add_action('sfc_async_init','sfc_login_logout_js');
?>
	<html><head></head><body>
	<?php sfc_add_base_js(array('cookie'=>false)); ?>
	</body></html>
<?php
exit;
}

// add logout code to async init
function sfc_login_logout_js() {
	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?loggedout=true';
?>
FB.getLoginStatus(function(response) {
	if (response.status === 'connected') {
		FB.logout(function(response) {
			window.location.href = '<?php echo $redirect_to; ?>';
		});
	} else {
		window.location.href = '<?php echo $redirect_to; ?>';
	}
});
<?php
}

add_action('login_footer','sfc_add_base_js',20);

add_action('admin_init', 'sfc_login_admin_init');
function sfc_login_admin_init() {
	add_settings_section('sfc_login', __('Login Settings', 'sfc'), 'sfc_login_section_callback', 'sfc');
	add_settings_field('sfc_login_avatars', __('Facebook Avatars', 'sfc'), 'sfc_login_avatar_callback', 'sfc', 'sfc_login');
}


function sfc_login_section_callback() {
	echo "<p>".__('Settings for the SFC-Login plugin. Users can connect their individual WP Logins to FB Logins on the Users->Your Profile screen.', 'sfc')."</p>";
}

function sfc_login_avatar_callback() {
	$options = get_option('sfc_options');
	if (!isset($options['login_avatars'])) $options['login_avatars'] = false;
	?>
	<p><input type="checkbox" name="sfc_options[login_avatars]" value="1" <?php checked('1', $options['login_avatars']); ?> /><label> <?php _e('Use Facebook Avatars in preference to Gravatars','sfc'); ?></label>	</p>
<?php
}

add_filter('sfc_validate_options','sfc_login_validate_options');
function sfc_login_validate_options($input) {
	if (isset($input['login_avatars']) && $input['login_avatars'] != 1) $input['login_avatars'] = 0;
	return $input;
}

// generate facebook avatar code for users who login with Facebook
add_filter('get_avatar','sfc_login_avatar', 10, 5);
function sfc_login_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = false) {
	$options = get_option('sfc_options');
	
	if ( !isset($options['login_avatars']) || $options['login_avatars'] != 1 ) return $avatar;
	
	// handle comments by registered users
	if ( is_object($id_or_email) && isset($id_or_email->user_id) && $id_or_email->user_id != 0) {
		$id_or_email = $id_or_email->user_id;	
	}

	// check to be sure this is for a user id
	if ( !is_numeric($id_or_email) ) return $avatar;

	$fbuid = get_user_meta( $id_or_email, 'fbuid', true );
	if ($fbuid) {
		// return the avatar code
		return "<img width='{$size}' height='{$size}' class='avatar avatar-{$size} fbavatar' src='http://graph.facebook.com/{$fbuid}/picture?type=square' />";
	}
	return $avatar;
}
