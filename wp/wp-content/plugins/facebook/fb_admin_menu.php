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
	//register our settings
	register_setting( 'fb-settings-group', 'enable_like' );
	register_setting( 'fb-settings-group', 'enable_comments' );
	register_setting( 'fb-settings-group', 'enable_recommendations' );
}

function fb_settings_page() {
?>
<div class="wrap">
<h2>Facebook</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'fb-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable Like button <a href="https://developers.facebook.com/docs/reference/plugins/like/" target="_new" title="The Like button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user's friends' News Feed with a link back to your website. Click to learn more.">[?]</a></th>
        <td><input type="checkbox" name="enable_like" value="true" <?php checked(TRUE, (bool) get_option('enable_like'));  print get_option('enable_like');?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Enable Comments box <a href="https://developers.facebook.com/docs/reference/plugins/comments/" target="_new" title="Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution. Click to learn more.">[?]</a></th>
        <td><input type="checkbox" name="enable_comments" value="true" <?php checked(TRUE, (bool) get_option('enable_comments'));  print get_option('enable_comments');?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Enable Recommendations bar <a href="https://developers.facebook.com/docs/reference/plugins/recommendationsbar/" target="_new" title="The Recommendations Bar allows users to like content, get recommendations, and share what they’re reading with their friends.">[?]</a></th>
        <td><input type="checkbox" name="enable_recommendations" value="true" <?php checked(TRUE, (bool) get_option('enable_recommendations'));  print get_option('enable_recommendations');?> /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>