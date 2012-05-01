<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) AND 'upload.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not load this page directly.');
}
global $user_ID; 

if( $user_ID ) { 
	if( current_user_can('level_10') ) { 
		$import_options = bfa_file_get_contents($_FILES['userfile']['tmp_name']);
	
		// Since 3.5.2, use JSON 
		if ( json_decode($import_options) != NULL AND strpos($import_options, 'use_bfa_seo') !== FALSE ) {
			update_option('bfa_ata4', json_decode($import_options, TRUE));
			echo "<strong><span style='color:green'>Successfully imported. Reloading admin area in 2 seconds... </span><code>" . 
				basename($_FILES['userfile']['name']) . "</code></strong><br />";		
		
		// Probably not a valid settings file:
		} else {
			echo "<strong><span style='color:red'>Sorry, but </span><code>" . 
				basename($_FILES['userfile']['name']) . "</code> <span style='color:red'>doesn't appear 
				to be a valid Atahualpa Settings File.</span></strong>";
		}
		
	} else {
		die("<span style='color:green'>Only admins can import settings</span>");
	}
}
die();
?>