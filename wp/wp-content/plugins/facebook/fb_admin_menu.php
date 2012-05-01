<?php
// create custom plugin settings menu
add_action('admin_menu', 'fb_create_menu');

function fb_create_menu() {

	//create new top-level menu
	add_menu_page('Facebook Plugin Settings', 'Facebook', 'administrator', __FILE__, 'fb_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	register_setting( 'fb-settings-group', 'fb_options', 'fb_options_validate');
}

function fb_settings_page() {
	$options = get_option('fb_options');
?>
<div class="wrap">
<h2>Facebook for WordPress Settings</h2>

<form method="post" action="options.php">
	<?php settings_fields( 'fb-settings-group' ); ?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row">App ID</th>
		<td><a href="#" target="_new" title="TODO">[?]</a>&nbsp; <input type="text" name="fb_options[app_id]" value="<?php print $options['app_id'] ?>" size="40" /></td>
		</tr>
		
		<tr valign="top">
		<th scope="row">App Secret</th>
		<td><a href="#" target="_new" title="TODO">[?]</a>&nbsp; <input type="text" name="fb_options[app_secret]" value="<?php print $options['app_secret'] ?>" size="40" /></td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Enable Like Buttons on Posts</th>
		<td><a href="https://developers.facebook.com/docs/reference/plugins/like/" target="_new" title="The Like button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user's friends' News Feed with a link back to your website. Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_like]" value="true" <?php checked(TRUE, (bool) $options['enable_like']);  print $options['enable_like']; ?> /></td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Enable Comments on Posts</th>
		<td><a href="https://developers.facebook.com/docs/reference/plugins/comments/" target="_new" title="Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution. Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_comments]" value="true" <?php checked(TRUE, (bool) $options['enable_comments']);  print $options['enable_comments']; ?> /></td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Enable Recommendations Bar on Posts</th>
		<td><a href="https://developers.facebook.com/docs/reference/plugins/recommendationsbar/" target="_new" title="The Recommendations Bar allows users to like content, get recommendations, and share what they're reading with their friends.  Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_recommendations_bar]" value="true" <?php checked(TRUE, (bool) $options['enable_recommendations_bar']);  print $options['enable_recommendations_bar']; ?> /></td>
		</tr>
		
		<tr valign="top">
	</table>
	
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
</form>
</div>
<?php
}

// validate our options
function fb_options_validate($input) {
	if (!defined('FB_APP_SECRET')) {
		// secrets are 32 bytes long and made of hex values
		$input['app_secret'] = trim($input['app_secret']);
		if(! preg_match('/^[a-f0-9]{32}$/i', $input['app_secret'])) {
		  $input['app_secret'] = '';
		}
	}

	if (!defined('FB_APP_ID')) {
		// app ids are big integers
		$input['app_id'] = trim($input['app_id']);
		if(! preg_match('/^[0-9]+$/i', $input['app_id'])) {
		  $input['app_id'] = '';
		}
	}

	if (!defined('FB_FANPAGE')) {
		// fanpage ids are big integers
		$input['fanpage'] = trim($input['fanpage']);
		if(! preg_match('/^[0-9]+$/i', $input['fanpage'])) {
		  $input['fanpage'] = '';
		}
	}

	$input = apply_filters('fb_validate_options',$input); // filter to let sub-plugins validate their options too
	return $input;
}

?>