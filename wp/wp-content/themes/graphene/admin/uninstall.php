<?php 
/* Check authorisation */
$authorised = true;
// Check nonce
if ( ! wp_verify_nonce( $_POST['graphene-uninstall'], 'graphene-uninstall' ) ) { 
	$authorised = false;
}
// Check permissions
if ( ! current_user_can( 'edit_theme_options' ) ) {
	$authorised = false;
}

// If the user is authorised, delete the theme's options from the database
if ( $authorised ) {

	delete_option( 'graphene_settings' );
	delete_transient( 'graphene-action-hooks-list' );
	delete_transient( 'graphene-action-hooks' );
	switch_theme( 'twentyten', 'twentyten' );
	wp_cache_flush(); ?>
	
    <div class="wrap">
    <h2><?php _e( 'Uninstall Graphene', 'graphene' ); ?></h2>
    <p><?php printf( __( 'Theme uninstalled. Redirecting to %s', 'graphene' ), '<a href="'.get_home_url().'/wp-admin/themes.php?activated=true">'.get_home_url().'/wp-admin/themes.php?activated=true</a>...' ); ?></p>
	<script type="text/javascript">
		window.location = '<?php echo get_home_url(); ?>/wp-admin/themes.php?activated=true';
	</script>;
	</div>
    
    <?php  
} else {
	wp_die( __( 'ERROR: You are not authorised to perform that operation', 'graphene' ));
}
?>