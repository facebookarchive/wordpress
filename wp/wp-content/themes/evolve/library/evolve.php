<?php
/**
 *
 * @package WPevolve
 * @subpackage Functions
 */

/**
 * class WPevolve Main class loads all includes, adds/removes filters.
 * 
 * @since 0.1
 */
class WPevolve {
	
	/**
	 * init() Initialisation method which calls all other methods in turn.
	 *
	 * @since 0.1
	 */
	function init() {		
		$theme = new WPevolve;
		
		$theme->enviroment();
		$theme->evolve();
		$theme->extentions();
		$theme->defaults();
		$theme->ready();
		
		do_action( 'evolve_init' );
	}
	
	/**
	 * enviroment() defines WP evolve directory constants
	 *
	 * @since 0.2.3
	 */
	function enviroment() {	
		define( 'EVLTHEMELIB', get_template_directory() . '/library' ); // Shortcut to point to the /library/ dir
		define( 'EVLTHEMECORE', EVLTHEMELIB . '/functions/' ); // Shortcut to point to the /functions/ dir
		define( 'EVLTHEMEMORE', EVLTHEMELIB . '/extensions/' ); // Shortcut to point to the /extensions/ dir
		define( 'EVLTHEMEMEDIA', EVLTHEMELIB . '/media' ); // Shortcut to point to the /media/ URI
		define( 'EVLTHEMECSS', EVLTHEMEMEDIA . '/css' );
		define( 'EVLTHEMEIMAGES', EVLTHEMEMEDIA . '/images' );
		define( 'EVLTHEMEJS', EVLTHEMEMEDIA . '/js' );
		
		// URI shortcuts
		define( 'EVLTHEME', get_template_directory_uri(), true );
		define( 'EVLLIBRARY', EVLTHEME . '/library', true ); // Shortcut to point to the /library/ URI
		
		if ( STYLESHEETPATH !== get_template_directory() ) define( 'EVLMEDIA', get_stylesheet_directory_uri(), true ); // Shortcut to point to the /media/ URI
		else define( 'EVLMEDIA', EVLLIBRARY . '/media', true ); // Shortcut to point to the /media/ URI
		
		define( 'EVLCSS', EVLMEDIA . '/css', true );
		define( 'EVLIMAGES', EVLMEDIA . '/images', true );
		define( 'EVLJS', EVLMEDIA . '/js', true );

		do_action( 'enviroment' ); // Available action: load_enviroment
	}
	
	/**
	 * evolve() includes all the core functions for WP evolve
	 *
	 * @since 0.2.3
	 */
	function evolve() {    
		require_once( EVLTHEMECORE . '/hooks.php' ); // load the WP evolve Hook System
		require_once( EVLTHEMECORE . '/functions.php' ); // load evolve functions
		require_once( EVLTHEMECORE . '/comments.php' ); // load comment functions
		require_once( EVLTHEMECORE . '/widgets.php' ); // load Widget functions
	}
	
	/**
	 * extentions() includes all extentions if they exist
	 *
	 * @since 0.2.3
	 */
	function extentions() {
		evlinclude_all( EVLTHEMEMORE );
	}
	
	/**
	 * defaults() connects WP evolve default behavior to their respective action
	 *
	 * @since 0.2.3
	 */
	function defaults() {
		add_filter( 'the_generator', 'remove_generator_link', 1 ); // remove_generator_link() Removes generator link - Credits: (http://www.plaintxt.org)
		add_filter( 'wp_page_menu', 'evolve_menu_ulclass' ); // adds a .nav class to the ul wp_page_menu generates
		add_action( 'init', 'evolve_media' ); // evolve_media() loads scripts and styles
	}
	
	/**
	 * ready() includes user's theme.php if it exists, calls the evolve_init action, includes all pluggable functions and registers widgets
	 *
	 * @since 0.2.3
	 */
	function ready() {
		if ( file_exists( EVLTHEMEMEDIA . '/custom-functions.php' ) ) include_once( EVLTHEMEMEDIA . '/custom-functions.php' ); // include custom-functions.php if that file exist
		require_once( EVLTHEMECORE . '/pluggable.php' ); // load pluggable functions
		do_action( 'evolve_init' ); // Available action: evolve_init
	}
} // end of WPevolve;
?>