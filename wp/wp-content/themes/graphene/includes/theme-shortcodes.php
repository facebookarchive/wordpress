<?php
/**
 * Shortcode handlers
 */
function warning_block_shortcode_handler( $atts, $content=null, $code="" ) {
    return '<div class="warning_block message-block">' . graphene_print_only_text( '<strong>Warning!</strong>' ) . graphene_first_p( do_shortcode( $content ) ) . '</div>';
}
add_shortcode( 'warning', 'warning_block_shortcode_handler' );

function error_block_shortcode_handler( $atts, $content=null, $code="" ) {
    return '<div class="error_block message-block">' . graphene_print_only_text( '<strong>Error!</strong>' ) . graphene_first_p( do_shortcode( $content ) ) . '</div>';
}
add_shortcode( 'error', 'error_block_shortcode_handler' );

function notice_block_shortcode_handler( $atts, $content=null, $code="" ) {
    return '<div class="notice_block message-block">' . graphene_print_only_text( '<strong>Notice</strong>' ) . graphene_first_p( do_shortcode( $content ) ) . '</div>';
}
add_shortcode( 'notice', 'notice_block_shortcode_handler' );

function important_block_shortcode_handler( $atts, $content=null, $code="" ) {
    return '<div class="important_block message-block">' . graphene_print_only_text( '<strong>Important!</strong>' ) . graphene_first_p( do_shortcode( $content ) ) . '</div>';
}
add_shortcode( 'important', 'important_block_shortcode_handler' );


/**
 * Hook the shortcode buttons to the TinyMCE editor
*/
class Graphene_Shortcodes_Buttons{
	
	function Graphene_Shortcodes_Buttons(){
		if ( current_user_can( 'edit_posts' ) &&  current_user_can( 'edit_pages' ) ) {	
			// add_filter( 'tiny_mce_version', array(&$this, 'tiny_mce_version' ) );
			add_filter( 'mce_external_plugins', array(&$this, 'graphene_add_plugin' ) );  
			add_filter( 'mce_buttons_2', array(&$this, 'graphene_register_button' ) );  
	   }
	}
	
	function graphene_register_button( $buttons){
		array_push( $buttons, "separator", "warning", "error", "notice", "important");
		return $buttons;
	}
	
	function graphene_add_plugin( $plugin_array){
		$plugin_array['grapheneshortcodes'] = get_template_directory_uri().'/js/mce-shortcodes.js';
		return $plugin_array; 
	}
}
add_action( 'init', 'Graphene_Shortcodes_Buttons' );

function Graphene_Shortcodes_Buttons(){
	global $Graphene_Shortcodes_Buttons;
	$Graphene_Shortcodes_Buttons = new Graphene_Shortcodes_Buttons();
}
?>