<?php
/**
 * The Sidebar for display in the content page.
 * Is only called by header.php or footer.php if the sidebar is needed, so no checking is required!
 *
 * @package Graphene
 * @since graphene 1.0
 */
global $graphene_settings;
?>	
    
<div id="sidebar1" <?php graphene_grid( 'sidebar', 1, 5, 4 ); ?>>

	<?php do_action('graphene_before_sidebar1'); ?>

    <?php 	/* Widgetized sidebar, if supported. */
    if ( ! is_front_page() && is_active_sidebar( 'sidebar-widget-area' ) ) { // Not home, display normal sidebar if active
		dynamic_sidebar('sidebar-widget-area');
	} elseif ( is_front_page() && !$graphene_settings['alt_home_sidebar'] && is_active_sidebar( 'sidebar-widget-area' ) ) { // Home, but alternate sidebar disabled, display normal sidebar if active
		dynamic_sidebar('sidebar-widget-area');
	} elseif ( is_front_page() && $graphene_settings['alt_home_sidebar'] && is_active_sidebar( 'home-sidebar-widget-area' ) ) { // Home, alternate sidebar enabled, display alternate sidebar if active
		dynamic_sidebar( 'home-sidebar-widget-area' );
	} else {
		
		/* Display notice to logged in users if there is no active widget in the sidebar */
		if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ){
			if ( is_front_page() && $graphene_settings['alt_home_sidebar'] ){
				$sidebar_name = __( 'Front Page Sidebar Widget Area', 'graphene' );
			} else {
				$sidebar_name = __( 'Sidebar Widget Area', 'graphene' );
			}
			graphene_sidebar_notice( $sidebar_name );
		}
	} 
	?>
    
    <?php wp_meta(); ?>
    
    <?php do_action('graphene_after_sidebar1'); ?>

</div><!-- #sidebar1 -->