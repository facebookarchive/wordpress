<?php
/**
 * Register the stylesheets
*/
function graphene_register_styles(){
	global $graphene_settings;

	if ( ! is_admin() ){
		wp_register_style( 'graphene-stylesheet', get_stylesheet_uri(), array(), false, 'screen' );
		wp_register_style( 'graphene-stylesheet-rtl', get_template_directory_uri() . '/rtl.css', array(), false, 'screen' );
		wp_register_style( 'graphene-light-header', get_template_directory_uri() . '/style-light.css', array(), false, 'screen' );
		wp_register_style( 'graphene-print', get_template_directory_uri() . '/style-print.css', array(), false, 'print' );
		wp_register_style( 'graphene-bbpress', get_template_directory_uri() . '/style-bbpress.css', array(), false, 'screen' );
	}
	
	wp_register_style( 'jquery-ui-slider', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.custom.css', array(), false, 'screen' );
	
}
add_action( 'init', 'graphene_register_styles' );


/**
 * Print the stylesheets
*/
function graphene_enqueue_styles(){
	global $graphene_settings;

	if ( ! is_admin() ){
		wp_enqueue_style( 'graphene-stylesheet' );
		if ( is_rtl() ) wp_enqueue_style( 'graphene-stylesheet-rtl' );
		if ( $graphene_settings['light_header'] ) wp_enqueue_style( 'graphene-light-header' );
		if ( is_singular() && $graphene_settings['print_css'] ) wp_enqueue_style( 'graphene-print' );
		if ( class_exists( 'bbPress' ) ) wp_enqueue_style( 'graphene-bbpress' );
	}
	
}
add_action( 'wp_enqueue_scripts', 'graphene_enqueue_styles' );


/**
 * Register custom scripts that the theme uses
*/
function graphene_register_scripts(){
	global $graphene_settings;
	
	wp_register_script( 'graphene-jquery-tools', get_template_directory_uri() . '/js/jquery-tools-1.2.5.min.js', array( 'jquery' ), '', true);
	
	// Register scripts for older versions of WordPress
	if ( ! graphene_is_wp_version( '3.3' ) ){
		wp_register_script( 'jquery-ui-widget', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.widget.min.js', array( 'jquery-ui-core' ), '', true );
		wp_register_script( 'jquery-ui-mouse', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.mouse.min.js', array( 'jquery-ui-core' ), '', true );
		wp_register_script( 'jquery-ui-slider', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.slider.min.js', array( 'jquery-ui-widget', 'jquery-ui-mouse' ), '', true );
	}
}
add_action( 'init', 'graphene_register_scripts' );


/**
 * Print custom scripts that the theme uses
*/
function graphene_enqueue_scripts(){
	global $graphene_settings;
	
	if ( ! is_admin() ) { // Front-end only
		wp_enqueue_script( 'jquery' );
		
		if ( is_front_page() && ! $graphene_settings['slider_disable'] )
			wp_enqueue_script( 'graphene-jquery-tools' ); // jQuery Tools, required for slider
			
		if ( is_singular() && get_option( 'thread_comments' ) )
        	wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'graphene_enqueue_scripts' );
?>