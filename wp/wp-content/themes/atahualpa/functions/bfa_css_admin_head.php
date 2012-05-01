<?php
function bfa_ata_admin_enqueue($hook) {
	
	$url = get_template_directory_uri() ;
	wp_enqueue_script( 'jscolor', "$url/options/jscolor/jscolor.js", false, '1.0.8' );
	wp_enqueue_script( 'ajaxupload', "$url/js/ajaxupload.js", false, '3.6' );
	wp_enqueue_script( 'mootools', "$url/options/mootools-for-textarea.js", false, '1.2.1' );
	wp_enqueue_script( 'uvumi-textarea', "$url/options/UvumiTextarea-compressed.js", array( 'mootools' ), '1.1.0' );
	wp_enqueue_script( 'ata-admin', "$url/options/ata-admin.js", array( 'jquery' ), '2011-06-10' );
	
	wp_enqueue_style( 'ata-admin', "$url/options/ata-admin.css", false, '2011-04-28' );
	wp_enqueue_style( 'uvumi-textarea', "$url/options/uvumi-textarea.css", false, '2011-04-28' );	
}


function bfa_add_stuff_admin_head() {
	global $templateURI, $homeURL;
	
	if ( isset($_GET['page'])) {
		if ( strpos( $_GET['page'], "atahualpa-" ) === 0 ) {
			// Create a WP nonce for the Ajax action later on
			$nonce = wp_create_nonce( 'reset_widget_areas' );
			$nonce2 = wp_create_nonce( 'delete_bfa_ata4' );
			$nonce3 = wp_create_nonce( 'import_settings' );
			echo "<script type='text/javascript'>var nonce = '$nonce'; var nonce2 = '$nonce2'; var nonce3 = '$nonce3';</script>";
		}
	}
}
?>