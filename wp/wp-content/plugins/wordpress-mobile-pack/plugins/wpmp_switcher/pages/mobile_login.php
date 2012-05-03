<?php

/*
$Id: mobile_login.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_switcher/pages/mobile_login.php $

Copyright (c) 2009 James Pearce & friends, portions mTLD Top Level Domain Limited, ribot, Forum Nokia

Online support: http://wordpress.org/extend/plugins/wordpress-mobile-pack/

This file is part of the WordPress Mobile Pack.

The WordPress Mobile Pack is Licensed under the Apache License, Version 2.0
(the "License"); you may not use this file except in compliance with the
License.

You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed
under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
CONDITIONS OF ANY KIND, either express or implied. See the License for the
specific language governing permissions and limitations under the License.
*/

function wpmp_switcher_login_header($title, $message = '', $wp_error = '') {
	global $error;

	if ( empty($wp_error) )
		$wp_error = new WP_Error();

  include_once('mobile.php');
  wpmp_ms_mobile_top($title);

	if ( !empty( $message ) ) echo apply_filters('login_message', $message) . "\n";

	if ( !empty( $error ) ) {
		$wp_error->add('error', $error);
		unset($error);
	}

	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);
			foreach ( $wp_error->get_error_messages($code) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
		if ( !empty($errors) )
			echo '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
		if ( !empty($messages) )
			echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
	}
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$errors = new WP_Error();

if ( isset($_GET['key']) )
	$action = 'resetpass';

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

if ( defined('RELOCATE') ) { // Move flag is set
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	if ( dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) != get_option('siteurl') )
		update_option('siteurl', dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) );
}

setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
switch ($action) {

case 'logout' :

	wp_logout();

	$redirect_to = 'wp-login.php?loggedout=true';
	if ( isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = $_REQUEST['redirect_to'];

	wp_safe_redirect($redirect_to);
	exit();

break;

case 'login' :
default:
	if ( isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = $_REQUEST['redirect_to'];
	else
		$redirect_to = 'wp-admin/';

	$user = wp_signon();

	if ( !is_wp_error($user) ) {
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' ) )
			$redirect_to = get_option('siteurl') . '/wp-admin/profile.php';
		wp_safe_redirect($redirect_to);
		exit();
	}

	$errors = $user;
	if ( !empty($_GET['loggedout']) )
		$errors = new WP_Error();

	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', sprintf(__("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a%s>enable cookies</a> to use WordPress.", 'wpmp'), " href='http://www.google.com/cookies.html'"));

	if		( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )			$errors->add('loggedout', __('You are now logged out.', 'wpmp'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )	$errors->add('registerdiabled', __('User registration is currently not allowed.', 'wpmp'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )	$errors->add('confirm', __('Check your e-mail for the confirmation link.', 'wpmp'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )	$errors->add('newpass', __('Check your e-mail for your new password.', 'wpmp'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )	$errors->add('registered', __('Registration complete. Please check your e-mail.', 'wpmp'), 'message');

	wpmp_switcher_login_header(__('Login', 'wpmp'), '', $errors);
?>

<form name="loginform" id="loginform" action="wp-login.php" method="post">
<?php if ( !isset($_GET['checkemail']) || !in_array( $_GET['checkemail'], array('confirm', 'newpass') ) ) : ?>
	<p>
		<label><?php _e('Username', 'wpmp') ?><br />
		<input type="text" name="log" id="user_login" class="input" value="<?php echo attribute_escape(stripslashes(@$user_login)); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('Password', 'wpmp') ?><br />
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
	</p>
<?php do_action('login_form'); ?>
	<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> <?php _e('Remember Me', 'wpmp'); ?></label></p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="submit" value="<?php _e('Log In', 'wpmp'); ?>" tabindex="100" />
		<input type="hidden" name="redirect_to" value="<?php echo attribute_escape($redirect_to); ?>" />
		<input type="hidden" name="testcookie" value="1" />
	</p>
<?php else : ?>
	<p>&nbsp;</p>
<?php endif; ?>
</form>

<p id="backtoblog"><a href="<?php bloginfo('url'); ?>/" title="<?php _e('Are you lost?', 'wpmp') ?>"><?php print '&laquo; ' . sprintf(__('Back to %s', 'wpmp'), get_bloginfo('title', 'display' )); ?></a></p>

<?php
  wpmp_ms_mobile_bottom();
  break;
}

?>
