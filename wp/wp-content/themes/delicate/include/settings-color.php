<?php
	// paths
	// -----
	$path_theme = get_template_directory_uri();
	$path_images = $path_theme.'/images/';
	$path_rainbow_images = $path_theme.'/functions/moorainbow/images/';
	
	// control's types
	// ---------------
	$control_types = array(
		0 => 'background-color',
		1 => 'background-image',
		2 => 'color',
		3 => 'color:hover',
		4 => 'font-size'
	);
	
	// sections controls
	// -----------------
	$sections_controls = array(
		0 => 'Manage your template color scheme.',
		1 => 'Text and Link styles.'
	);
	
	$root_block = 'background-image-div';
	
	// controls
	// --------
	$controls = array(
	/*$controls[] = array(
			'name' => 't_background',					// id for control
			'title' => 'Background image',				// Title
			'section' => $sections_controls[0],			// Section -> see $sections_controls array
			'type' => $control_types[1],				// Type -> see $control_types array	
			'path' => $path_images.'background/',
			'server-path' => TEMPLATEPATH.'/images/background/',
			'selector' => '#header',						// Selector(s) in template
			'selector-mini' => '#background-image-div'	// Selector(s) in preview
		),	*/
	$controls[] = array(
			'name' => 't_font_size',
			'title' => 'Font size',
			'section' => $sections_controls[0],
			'type' => $control_types[4],
			'selector' => 'body',
			'selector-mini' => '#background-image-div'
		),
		
	$controls[] = array(
			'name' => 't_text_color',
			'title' => 'Text color',
			'section' => $sections_controls[1], // Top navigation.
			'type' => $control_types[2],
			'selector' => '.post',
			'selector-mini' => '#test-text'
		),	
	$controls[] = array(
			'name' => 't_link_color',
			'title' => 'Link color',
			'section' => $sections_controls[1], // Top navigation.
			'type' => $control_types[2],
			'selector' => '.post a',
			'selector-mini' => '#test-text a'
		),
	$controls[] = array(
			'name' => 't_linkactive_color',
			'title' => 'Link hover (highlight) color',
			'section' => $sections_controls[1], // Top navigation.
			'type' => $control_types[3],
			'selector' => '.post a:hover',
			'selector-mini' => '#test-text a',
			'color-control' => 't_link_color'
		)	
	);
	
	// preset styles
	$preset_styles = array (
	$preset_styles[] = array(			
			'12',
			
			'4D4D4F',
			'0E73B8',
			'ff0505'
		)
	);
	
	// Preset font sizes
	$font_sizes = array('11','12','13','14','15','16');
?>