<?php
/**
 * Hooks - WP evolve's hook system
 *
 * @package WPevolve
 * @subpackage WP_evolve
 */

/**
 * evolve_hook_before_html() short description.
 *
 * Long description.
 *
 * @since 0.3
 * @hook action evolve_hook_before_html
 */
function evolve_hook_before_html() {
	do_action( 'evolve_hook_before_html' );
}

/**
 * evolve_hook_after_html() short description.
 *
 * Long description.
 *
 * @since 0.3
 * @hook action evolve_hook_after_html
 */
function evolve_hook_after_html() {
	do_action( 'evolve_hook_after_html' );
}

/**
 * evolve_hook_comments() short description.
 *
 * Long description.
 *
 * @since 0.3
 * @hook action evolve_hook_loop
 */
function evolve_hook_comments( $callback = array('evolve_comment_author', 'evolve_comment_meta', 'evolve_comment_moderation', 'evolve_comment_text', 'evolve_comment_reply' ) ) {
	do_action( 'evolve_hook_comments_open' ); // Available action: evolve_comment_open
	do_action( 'evolve_hook_comments' );

	$callback = apply_filters( 'evolve_comments_callback', $callback ); // Available filter: evolve_comments_callback
	
	// If $callback is an array, loop through all callbacks and call those functions if they exist
	if ( is_array( $callback ) ) {
		foreach( $callback as $function ) {
			if ( function_exists( $function ) ) {
				call_user_func( $function );
			}
		}
	}
	
	// If $callback is a string, just call that function if it exist
	elseif ( is_string( $callback ) ) {
		if ( function_exists( $callback ) ) {
			call_user_func( $callback );
		}
	}
	do_action( 'evolve_hook_comments_close' ); // Available action: evolve_comment_close
}
?>