<?php
/**
 * Graphene functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, graphene_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists() ) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *  
 *     remove_filter( 'filter_hook', 'callback_function' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Graphene
 * @since Graphene 1.0
 */
 
 
/**
 * Before we do anything, let's get the mobile extension's init file if it exists
*/
$mobile_path = dirname( dirname( __FILE__ ) ) . '/graphene-mobile/includes/theme-plugin.php';
if ( file_exists( $mobile_path ) ) { include( $mobile_path ); }


/**
 * Load the various theme files
*/
require( 'admin/options-init.php' );		// Theme options and admin interface setup
require( 'includes/theme-scripts.php' );	// Theme stylesheets and scripts
require( 'includes/theme-utils.php' );		// Theme utilities
require( 'includes/theme-head.php' );		// Functions for output into the HTML <head> element
require( 'includes/theme-menu.php' );		// Functions for navigation menus
require( 'includes/theme-loop.php' );		// Functions for posts/pages loops
require( 'includes/theme-comments.php' );	// Functions for comments
require( 'includes/theme-widgets.php' );	// Functions for custom widgets
require( 'includes/theme-slider.php' );		// Functions for the slider
require( 'includes/theme-panes.php' );		// Functions for the homepage panes
require( 'includes/theme-plugins.php' );	// Native plugins support
require( 'includes/theme-shortcodes.php' );	// Theme shortcodes
require( 'includes/theme-functions.php' );	// Other functions that are not categorised above
require( 'includes/theme-setup.php' );		// Theme setup
?>