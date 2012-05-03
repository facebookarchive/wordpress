<?php

/**
 * This is the function to display the custom user field in the user admin page
*/
function graphene_show_custom_user_fields($user){ global $current_user; ?>
	<h3><?php _e('Graphene-specific User Profile Information', 'graphene'); ?></h3>
    <p><?php _e('The settings defined here are used only with the Graphene theme.', 'graphene'); ?></p>
    <table class="form-table">
    	<tr>
        	<th>
            	<label for="author_imgurl"><?php _e('Author profile image URL', 'graphene'); ?></label><br />
                <small><?php _e("You can specify the image to be displayed as the author's profile image in the Author's page. If no URL is defined here, the author's <a href=\"http://www.gravatar.com\">Gravatar</a> will be used.", 'graphene'); ?></small>
            </th>
            <td>
            	<input type="text" name="author_imgurl" id="author_imgurl" value="<?php echo esc_attr( get_user_meta( $user->ID, 'graphene_author_imgurl', true ) ); ?>" size="50" /><br />
                <span class="description"><?php _e("Please enter the full URL (including <code>http://</code>) to your profile image.", 'graphene'); ?><br /> <?php _e("<strong>Important: </strong>Image width must be less than or equal to <strong>150px</strong>.", 'graphene'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}
// Hook the function to add extra field to the user profile page
add_action('show_user_profile', 'graphene_show_custom_user_fields');
add_action('edit_user_profile', 'graphene_show_custom_user_fields');


/**
 * This is the function to save the custom user fields we defined above
*/
function graphene_save_custom_user_fields( $user_id ){
	
	if ( ! current_user_can('edit_user', $user_id))
		return false;
	
	// Updates the custom field and save it as a user meta
	update_user_meta( $user_id, 'graphene_author_imgurl', $_POST['author_imgurl'] );
}
// Hook the update function
add_action('personal_options_update', 'graphene_save_custom_user_fields' );
add_action('edit_user_profile_update', 'graphene_save_custom_user_fields' );
?>