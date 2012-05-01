<?php if (!empty($_SERVER['SCRIPT_FILENAME']) AND 'download.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not load this page directly.');
}
global $user_ID; 
if( $user_ID ) { 
	if( current_user_can('level_10') ) { 
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="ata-' . str_replace('.','', $_SERVER['SERVER_NAME']) . '-' . date('Y') . date('m') . date('d') . '.txt"');
		header('Content-Type: text/plain; charset=utf-8');
		// output the file
		// Since 3.5.2: Use JSON 
		echo json_encode( get_option('bfa_ata4') );
	}
}
die();
?>