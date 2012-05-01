<?php
/**
 * Set the settings for options presets
*/

/* Check authorisation */
$authorised = true;

if (isset($_POST['graphene-preset'])){ 
	if (!wp_verify_nonce($_POST['graphene-preset'], 'graphene-preset')) {$authorised = false;} // Check nonce
	if (!current_user_can('edit_theme_options')){$authorised = false;} // Check permissions

} else {$authorised = false;}

if ($authorised) {
	global $graphene_settings, $graphene_defaults;
			
	/* Apply the website preset */
	if ($_POST['graphene_options_preset'] == 'website') {
		
		$graphene_preset_website = array(
			'slider_display_style' => 'bgimage-excerpt',
			'show_post_type' => 'latest-posts',
			'homepage_panes_count' => 4,
			'comments_setting' => 'disabled_pages',			
			'hide_post_author' => true,
			'post_date_display' => 'icon_no_year',
			'print_css' => true,
			'print_button' => true,
		);
		
		$graphene_preset_website = array_merge( $graphene_defaults, $graphene_preset_website );
		update_option( 'graphene_settings', $graphene_preset_website );
		
		add_settings_error( 'graphene_options', 2, __( 'The "Normal website" settings preset has been applied.', 'graphene' ), 'updated');
	
	/* Reset the options */	
	} elseif ( $_POST['graphene_options_preset'] == 'reset' ) {
		delete_option( 'graphene_settings' );
		add_settings_error('graphene_options', 2, __( 'Settings have been reset.', 'graphene' ), 'updated');
	}
	
	// Update the global settings variable
	$graphene_settings = array_merge( $graphene_defaults, get_option( 'graphene_settings', array() ) );

} else {
	wp_die( __( 'ERROR: You are not authorised to perform that operation', 'graphene' ) );
}
?>