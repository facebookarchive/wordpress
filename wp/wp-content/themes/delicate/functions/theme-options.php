<?php 
require ($natty_include_path . 'settings-theme.php');

// get pages	
$pn_pages = get_pages('');
	foreach ( $pn_pages as $pag ) {
		$pn_pag[] = array( $pag->ID, $pag->post_title );
	}	

// get categories	
$pn_categories = get_categories('hide_empty=0');
	foreach ( $pn_categories as $cat ) {
		$pn_cat[] = array( $cat->cat_ID, $cat->cat_name );
	}		
	
// Entries count
	for ($i = 0; $i < count($num_data); $i++) {
		$nd = $num_data[$i];
		$ph_entries[] = array("$nd", "$nd");
	}

	// get images
	for ($i = 0; $i < count($tcontrols); $i++) {
		if($tcontrols[$i]['type'] == 'select-custom') {
			if (!isset($tcontrols[$i]['associated'])) {
				$img_dir = opendir($tcontrols[$i]['server-path']);
				while (false !== ($bg_folder = readdir($img_dir))) {
					if( $bg_folder != "." && $bg_folder != ".." ) {
						$pn_images[$i][] = array($bg_folder, $bg_folder);
					}
				}		
				closedir($img_dir);
			}
			else {
				while (list($key, $val) = each($tcontrols[$i]['associated'])) {
					$pn_images[$i][] = array($val, $key);
				}
			}
		}
	}
	

?>
<div class="frame-nav">
   <ul>
      <li><a href="#nat-wellcome">Welcome</a></li>
      <?php 	for ($i = 0; $i < count($sections_controls); $i++) {
        echo '<li><a href="#'.$i.'">'.$sections_controls[$i].'</a></li>';
      } ?>
   </ul>
</div>

<div class="main-content">
<div id="nat-wellcome" class="tab">
  <?php 


  echo '<img class="alignright border" alt="theme" style="padding: 0px 0px 10px 10px;" src="'.get_bloginfo('template_directory').'/screenshot.png" />';
  t_display_license();
  ?>
<h3>Colors</h3>
<p>To make it super easy to customize your theme, we have added color control module. You can find and set these up under <strong>"Appearance" > "Color Options"</strong></p>

<h3>Widgets</h3>
<p>We have added a number of additional widgets to provide this theme with advanced tools. You can install and configure them under <strong>"Appearance" > "Widgets"</strong></p>

<?php t_show_shortcodes(); ?>
<br/>
<p>Find more information about this <a href="<?php echo $natty_manualurl; ?>">theme configuration</a> in the docs. </p>
</div>
  
<?php 
	for ($i = 0; $i < count($sections_controls); $i++) {
?>

<div id="<?php echo $i; ?>" class="tab">
<?php 
		for ($j = 0; $j < count($tcontrols); $j++) {
			if ($tcontrols[$j]['section'] == $sections_controls[$i]) {
        if(isset($tcontrols[$j]['mode']) && $tcontrols[$j]['mode'] == 'dimensions') {
           nat_th_array($tcontrols[$j]['title']);	
        } else { nat_th($tcontrols[$j]['title']);}	
						
				switch ($tcontrols[$j]['type']) {
					case 'textarea':					
						ih_input( $tcontrols[$j]['name'], 'textarea', '', stripslashes(t_get_option($tcontrols[$j]['name'])));
						nat_hit($tcontrols[$j]['desc']);							
						break;
					case 'input':
						ih_input( $tcontrols[$j]['name'], 'text', '', t_get_option($tcontrols[$j]['name']));							
						nat_hit($tcontrols[$j]['desc']);
						break;			
					case 'select':
						if($tcontrols[$j]['mode'] == 'pages') {
							ih_select($tcontrols[$j]['name'], $pn_pag, t_get_option($tcontrols[$j]['name']), "" ); 
						} elseif ($tcontrols[$j]['mode'] == 'cats')  {
							ih_select($tcontrols[$j]['name'], $pn_cat, t_get_option($tcontrols[$j]['name']), "" ); 
						} elseif ($tcontrols[$j]['mode'] == 'bool'){
							ih_select($tcontrols[$j]['name'], $boolean_var, t_get_option($tcontrols[$j]['name']), ''); 	
						} else {
							ih_select($tcontrols[$j]['name'], $ph_entries, t_get_option($tcontrols[$j]['name']), "" ); 
						}
						nat_hit($tcontrols[$j]['desc']);
						break;
					case 'select-custom':
						ih_select( $tcontrols[$j]['name'], $pn_images[$j], t_get_option($tcontrols[$j]['name']), '');
						nat_hit($tcontrols[$j]['desc']);
						break;
					case 'multi-select':						
							if($tcontrols[$j]['mode'] == 'pages') {		
								if ($tcontrols[$j]['set'] == 'exclude'){ $pn_pag[] = array ("", "--------------------"); $pn_pag[] = array ("no", "DO NOT EXCLUDE");}
								if ($tcontrols[$j]['sort'] == 'menu_order'){ 
									ih_mselect( $tcontrols[$j]['name_get'], $pn_sortpag ,t_get_option( $tcontrols[$j]['name'] ), "",'');
								} else {
									ih_mselect( $tcontrols[$j]['name_get'], $pn_pag ,t_get_option( $tcontrols[$j]['name'] ), "",'');
								}
							} else {
								if ($tcontrols[$j]['set'] == 'exclude') {$pn_cat[] = array ("", "--------------------"); $pn_cat[] = array ("no", "DO NOT EXCLUDE");}
								ih_mselect( $tcontrols[$j]['name_get'], $pn_cat ,t_get_option( $tcontrols[$j]['name'] ), "", '');
							};			
						nat_hit($tcontrols[$j]['desc']);			
						break;	
					case 'upload':
            if (!isset($tcontrols[$j]['btn-name']))
              $tcontrols[$j]['btn-name'] = 'Upload Image';
						nat_upload($tcontrols[$j]['name'], stripslashes(get_option($tcontrols[$j]['name'])), $tcontrols[$j]['btn-name']);
						nat_hit($tcontrols[$j]['desc']);	
						break;			
					case 'sort-item':
						nat_sort($tcontrols[$j]['name_get'], $pn_pag, t_get_option($tcontrols[$j]['name']), "",$tcontrols[$j]['desc']);
						break;								
				}
				nat_endth();
			} // END if
		}
?>
</div>
<?php } ?>
</div><!-- main-content -->