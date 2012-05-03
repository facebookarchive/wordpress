<?php
	// control's types
	// ---------------
	$control_types = array(
		0 => 'textarea',
		1 => 'input',		
		2 => 'select',		
		3 => 'select-custom',
		4 => 'multi-select',
		5 => 'upload',
		6 => 'sort-item'
	);
	
	// sections controls
	// -----------------
	$sections_controls = array(
		0 => 'General Options',
		1 => 'Front Page Settings',
		2 => 'Site Statistics Settings'
	);
	
	// Preset boolean
	$boolean_var[] = array( "yes", "Yes" );
	$boolean_var[] = array( "no", "No" );
	
	// Numbers
	$num_data = array ('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19');

	
	// controls
	// --------
	$tcontrols = array();
	
	$tcontrols[] = array (
			'name' => 'nattywp_custom_logo',					// id for control
			'title' => 'Custom Logo',				// Title
			'section' => $sections_controls[0],			// Section -> see $sections_controls array
			'type' => $control_types[5],				// Type -> see $control_types array	
			'desc' => 'Upload custom logo, or specify the image address. Start with http://',
			'default' => ''		
		);	
	$tcontrols[] = array (
			'name' => 't_home_name',
			'title' => 'Home Link Text',
			'section' => $sections_controls[0],
			'type' => $control_types[1],
			'desc' => 'Enter a Home link text',
			'default' => 'Home'
		);				
	$tcontrols[] = array (
			'name' => 't_meta_desc',					// id for control
			'title' => 'Meta Description',				// Title
			'section' => $sections_controls[0],			// Section -> see $sections_controls array
			'type' => $control_types[0],				// Type -> see $control_types array	
			'desc' => 'Enter a blurb about your site here, and it will show up on the &lt;meta name=&quot;description&quot;&gt; tag. Useful for SEO.',
			'default' => ''		
		);	

	$tcontrols[] = array (
			'name' => 't_cufon_replace',
			'title' => 'Enable Cufon font replacement',
			'section' => $sections_controls[0],
			'type' => $control_types[2],
			'mode' => 'bool',
			'desc' => 'Cufon performs text replacement on web pages, using the canvas element to render fancy typefaces.',
			'default' => 'yes'
		);	
	$tcontrols[] = array (
			'name' => 't_show_post',
			'title' => 'Show Fullposts?',
			'section' => $sections_controls[0],
			'type' => $control_types[2],
			'mode' => 'bool',
			'desc' => 'Show fullposts instead of post summary?',
			'default' => 'yes'
		);
	$tcontrols[] = array (
			'name' => 'nattywp_custom_favicon',
			'title' => 'Custom Favicon.ico upload',
			'section' => $sections_controls[0],
			'type' => $control_types[5],
			'btn-name' => 'Upload .ICO file',
			'desc' => 'Use <a href="http://www.faviconer.com" target="_blank">Faviconer.com</a> to create unique favicon.ico image.',
			'default' => ''
	);	
		
	$tcontrols[] = array (
			'name' => 't_show_slideshow',
			'title' => 'Header Area Settings',
			'section' => $sections_controls[1],
			'type' => $control_types[3],			
			'associated' => array(
          'Turn Off Header section' => 'hide',
          'Display Header Image' => 'yes',
          'Display Page Slideshow' => 'no'
       ),
			'desc' => 'This option helps you to control your header area. Select: <br/>
			<strong>Turn Off Header section</strong> - to completely remove header area from display.<br/>
			<strong>Header Image</strong> - to display <a href="?page=custom-header">Uploaded Header</a>.<br/>
			<strong>Page Slideshow</strong> - to create a Page-based slider with Featured images.',
			'default' => 'yes'
		);			 
	$tcontrols[] = array (
			'name' => 't_scroll_pages',
			'name_get' => 't_scroll_pages[]',
			'title' => 'Homepage Scrolling pages',
			'section' => $sections_controls[1],
			'type' => $control_types[6],	
			'desc' => '<strong>Note:</strong> Please make sure that you selected <i>"Display Page Slideshow"</i> option under <strong>Header Area Settings</strong>.<br/> To add pages click on the "Plus" icon at the right side. To order pages click and move item at the left side.',
			'default' => 'no'
		);		
	$tcontrols[] = array (
			'name' => 't_slide_effect',
			'title' => 'Slideshow effect',
			'section' => $sections_controls[1],
			'type' => $control_types[3],			
			'associated' => array(
			'blindX' => 'blindX',
			'blindY' => 'blindY',
			'blindZ' => 'blindZ',
			'cover' => 'cover',
			'curtainX' => 'curtainX',
			'curtainY' => 'curtainY',
      'fade' => 'fade',
      'fadeZoom' => 'fadeZoom',
      'growX' => 'growX',
      'growY' => 'growY',
      'none' => 'none',
      'scrollUp' => 'scrollUp',
      'scrollDown' => 'scrollDown',
			'scrollLeft' => 'scrollLeft',
      'scrollRight' => 'scrollRight',
      'scrollHorz' => 'scrollHorz',
      'scrollVert' => 'scrollVert',
      'shuffle' => 'shuffle',
      'slideX' => 'slideX',
      'slideY' => 'slideY',
      'toss' => 'toss',
      'turnUp' => 'turnUp',
      'turnDown' => 'turnDown',
      'turnLeft' => 'turnLeft',
      'turnRight' => 'turnRight',
      'uncover' => 'uncover',
      'wipe' => 'wipe',
      'zoom' => 'zoom'
	),
			'desc' => 'Select transition effect for homepage slideshow.',
			'default' => 'fade'
		);		
$tcontrols[] = array (
			'name' => 't_timeout',
			'title' => 'Timeout',
			'section' => $sections_controls[1],
			'type' => $control_types[1],
			'desc' => 'Milliseconds between slide transitions (0 to disable auto advance)',
			'mode' => 'dimensions',
			'default' => '6000'		
		);	
  $tcontrols[] = array (
			'name' => 't_slide_speed',
			'title' => 'Slide speed',
			'section' => $sections_controls[1],
			'type' => $control_types[1],
			'mode' => 'dimensions',
			'desc' => 'Speed of the transition (in milliseconds)',
			'default' => '1000'		
		);
		
	
	$tcontrols[] = array (
			'name' => 't_twitterurl',				// id for control
			'title' => 'Twitter URL',				// Title
			'section' => $sections_controls[2],			// Section -> see $sections_controls array
			'type' => $control_types[1],				// Type -> see $control_types array	
			'desc' => 'Link to your twitter page. Start with http://',
			'default' => ''		
		);
	$tcontrols[] = array (
			'name' => 't_feedburnerurl',				// id for control
			'title' => 'Feedburner URL',				// Title
			'section' => $sections_controls[2],			// Section -> see $sections_controls array
			'type' => $control_types[1],				// Type -> see $control_types array	
			'desc' => '<a href="http://feedburner.google.com" target="_blank">Feedburner</a> URL. This will replace RSS feed link. Start with http://',
			'default' => ''		
		);
	$tcontrols[] = array (
			'name' => 't_tracking',					// id for control
			'title' => 'Tracking Code',					// Title
			'section' => $sections_controls[2],			// Section -> see $sections_controls array
			'type' => $control_types[0],				// Type -> see $control_types array	
			'desc' => 'Put your tracking code here and manage your website statistics',
			'default' => ''		
		);	
?>