<?php 
if (function_exists('add_theme_support'))
add_theme_support('automatic-feed-links');
	
// if we can't find theme installed lets go ahead and install all the options that run template.  This should run only one more time for all our existing users, then they will just be getting the upgrade function if it exists.

if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
	add_action('admin_head','natty_option_setup');	
	header( 'Location: '.admin_url().'themes.php?page=nattywp_home&pageaction=activated' ) ;	
}

function natty_option_setup() {
  global $controls, $tcontrols, $natty_themename, $preset_styles;
  
  if (!get_option($natty_themename.'nfinstalled')) {
		add_option($natty_themename.'nfinstalled');	
		$pop = array( 'recent_comments_num'   => 5,
                  'popular_posts_num'  => 7);	

		add_option('widget_popularwidget', $pop);
		
		for ($i = 0; $i < count($tcontrols); $i++) {
			$framework_settings[$tcontrols[$i]['name']] = $tcontrols[$i]['default'];
		}
		add_option($natty_themename.'_settings', $framework_settings);
				
		for ($i = 0; $i < count($controls); $i++) {     
      $framework_color_settings['paramspresetStyle'] = 'style0';
			$framework_color_settings[$controls[$i]['name']] = $preset_styles[0][$i];
		}	
		add_option($natty_themename.'_color_settings', $framework_color_settings);	
		
		// Add options for backup
		add_option($natty_themename.'_settings_back');
    add_option($natty_themename.'_color_settings_back');	
    
    // Add option for custom logo
    add_option('nattywp_custom_logo');
    add_option('nattywp_custom_favicon');
  }
}

function natty_option_backup() {}
// Here we handle upgrading our users with new options and such.  If nhinstalled is in the DB but the version they are running is lower than our current version, trigger this event.
?>