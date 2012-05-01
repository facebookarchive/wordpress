<?php
	require ($natty_include_path . 'settings-color.php');
	
	// preset styles	
	for ($i = 0; $i < count($preset_styles); $i++) {
		$ih_scheme[] = array('style'.$i, 'Style'.$i);
	}
	$ih_scheme[] = array('custom', 'Custom');
	
	// font-sizes
	for ($i = 0; $i < count($font_sizes); $i++) {
		$fs = $font_sizes[$i];
		$ih_font[] = array("$fs", $fs.'px');
	}
	
	// get images
	for ($i = 0; $i < count($controls); $i++) {
		if($controls[$i]['type'] == 'background-image') {
			if (!isset($controls[$i]['associated'])) {
				$img_dir = opendir($controls[$i]['server-path']);
				while (false !== ($bg_folder = readdir($img_dir))) {
					if( $bg_folder != "." && $bg_folder != ".." ) {
						$fn_images[$i][] = array($bg_folder, $bg_folder);
					}
				}		
				closedir($img_dir);
			}
			else {
				while (list($key, $val) = each($controls[$i]['associated'])) {
					$fn_images[$i][] = array($val, $key);
				}
			}
		}
	}
?>
<div class="main-content">
  <h3 class="hndle">Color Settings</h3>
  
  <div class="preview-box">
<?php
	require (TEMPLATEPATH . '/include/template-mini.php');
?>
</div>

    <div class="inside">
			<div class="form-item">
<?php
	nat_th("Preset Color scheme");
	ih_select("paramspresetStyle", $ih_scheme, t_get_coption("paramspresetStyle"), "", "kDefaults()");
	nat_endth();
?>
			</div>
<?php
	for ($i = 0; $i < count($sections_controls); $i++) {
?>
			<h3><?php echo $sections_controls[$i] ?></h3>
			<div class="form-item">
<?php
		for ($j = 0; $j < count($controls); $j++) {
			if ($controls[$j]['section'] == $sections_controls[$i]) {
				switch ($controls[$j]['type']) {
					case 'background-image':
						nat_th($controls[$j]['title']);
						ih_select($controls[$j]['name'], $fn_images[$j], t_get_coption($controls[$j]['name']), '');
						nat_endth();
						break;
					case 'background-color':
						nat_th($controls[$j]['title']);
						ih_input($controls[$j]['name'], 'text', '', t_get_coption($controls[$j]['name']));
						nat_endth();
						break;
					case 'color':
						nat_th($controls[$j]['title']);
						ih_input($controls[$j]['name'], 'text', '', t_get_coption($controls[$j]['name']));
						nat_endth();
						break;
					case 'color:hover':
						nat_th($controls[$j]['title']);
						ih_input($controls[$j]['name'], 'text', '', t_get_coption($controls[$j]['name']));
						nat_endth();
						break;
					case 'font-size':
						nat_th($controls[$j]['title']);
						ih_select($controls[$j]['name'], $ih_font, t_get_coption($controls[$j]['name']), '');
						nat_endth();
						break;
				}
			}
		}
?>
			</div>
			<br /> 
<?php
	}
?></div>


<div class="clear"></div>
</div>