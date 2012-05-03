<?php 
/**
 * Add breadcrumbs to the top of the content area. Uses the Breadcrumb NavXT plugin
*/
if ( function_exists( 'bcn_display' ) ) :
	function graphene_breadcrumb_navxt(){
		echo '<div class="breadcrumb">';
		bcn_display();
		echo '</div>';
	}
	add_action( 'graphene_top_content', 'graphene_breadcrumb_navxt' );
endif;


/**
 * Add 'nodate' class for bbPress user home
*/
if ( class_exists( 'bbPress' ) ) :
	function graphene_bbpress_post_class( $classes ){
		if ( bbp_is_user_home() )
			$classes[] = 'nodate';
			
		return $classes;
	}
	add_filter( 'post_class', 'graphene_bbpress_post_class' );
endif;
?>