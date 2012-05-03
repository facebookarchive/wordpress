<?php
function bfa_ata_add_admin() {
	global $options, $bfa_ata;

	if ( isset($_GET['page'])) {
	   if ( $_GET['page'] == "atahualpa-options" ) {
		
			if ( isset($_REQUEST['action']) ) {
				if ( 'save' == $_REQUEST['action'] ) {
			
					foreach ($options as $value) {
						if ( $value['category'] == $_REQUEST['category'] ) {
							if ( isset($value['escape']) ) {
								if( isset( $_REQUEST[ $value['id'] ] ) ) 
									// Since 3.6.8 removed bfa_escape
									//$bfa_ata[ $value['id'] ] = stripslashes(bfa_escape($_REQUEST[ $value['id'] ]  )); 
									$bfa_ata[ $value['id'] ] =  stripslashes($_REQUEST[ $value['id'] ]); 
								else 
									unset ($bfa_ata[ $value['id'] ]); 
							} elseif ( isset($value['stripslashes']) ) { 
								if ($value['stripslashes'] == "no") {
									if( isset( $_REQUEST[ $value['id'] ] ) ) 
										$bfa_ata[ $value['id'] ] = $_REQUEST[ $value['id'] ]  ; 
									else 
										unset ($bfa_ata[ $value['id'] ]); 
								}	
							} else { 
								if( isset( $_REQUEST[ $value['id'] ] ) ) 
									$bfa_ata[ $value['id'] ] = stripslashes($_REQUEST[ $value['id'] ]  ); 
								else 
									unset ($bfa_ata[ $value['id'] ]); 
							} 
						}
					} 
					update_option('bfa_ata4', $bfa_ata);	
					header("Location: themes.php?page=atahualpa-options&saved=true");
					die;

				} else if( 'reset' == $_REQUEST['action'] ) {
				
					if ("reset-all" == $_REQUEST['category']) {
						delete_option('bfa_ata4');
					} else {
						foreach ($options as $value) {
							if ( $value['category'] == $_REQUEST['category'] ) 
								$bfa_ata[ $value['id'] ] = $value['std'];
						}
						update_option('bfa_ata4', $bfa_ata);
					}
					
					header("Location: themes.php?page=atahualpa-options&reset=true");
					die;
				}
			}
			
		}
	}

	$atapage = add_theme_page("Atahualpa Options", "Atahualpa Theme Options", 'edit_theme_options', 'atahualpa-options', 'bfa_ata_admin');	
	// Since 3.6.8:
	add_action( "admin_print_styles-$atapage", 'bfa_ata_admin_enqueue' );
}
?>