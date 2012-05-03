<?php
/*
Adjust output of new WP3 menu system for Ruthsarian RMenu CSS.
This adds the CSS class 'rMenu-expand' to parent LI's
*/  
function bfa_new_wp3_menus($theme_location = "menu1", $alignment = "left") { 

	$before_menu = ''; $after_menu = '';
	
	/*
	if ( $theme_location == "menu1" ) $menu_id = "rmenu2-page";
	if ( $theme_location == "menu2" ) $menu_id = "rmenu-cat";
	*/
	if ( $theme_location == "menu1" ) $menu_id = "rmenu2";
	if ( $theme_location == "menu2" ) $menu_id = "rmenu";
	
	$menu_class = "clearfix rMenu-hor rMenu";
	if ( $alignment == "right" ) $menu_class .= " rMenu-hRight";
	if ( $alignment == "center" ) { 
		if ( $theme_location == "menu1" ) 
			$before_menu = '<div id="bfa_page_menu"><table cellpadding="0" cellspacing="0" style="margin: 0 auto"><tr><td align="center">';
		if ( $theme_location == "menu2" ) 
			$before_menu = '<div id="bfa_cat_menu"><table cellpadding="0" cellspacing="0" style="margin: 0 auto"><tr><td align="center">';
		$after_menu = '</td></tr></table></div>';
	} 

	ob_start();
	
	wp_nav_menu( array( 
		'theme_location' => $theme_location, 
		'container' => 'div', 
		'container_id' => $theme_location,
		'menu_class' => $menu_class,
		'menu_id' => $menu_id,
		'link_before' => '<span>',
		'link_after' => '</span>'
		) );
		
	$newmenu = ob_get_contents(); 

	ob_end_clean();

	$newmenu = preg_replace("/<li (.*?)class=\"(.*?)\">(.*?)\n(.*?)<ul class=\"/i","<li \\1 class=\"rMenu-expand \\2\">\\3\n\\4<ul class=\"rMenu-ver ",$newmenu);
		
	return $before_menu . $newmenu . $after_menu;
		
}
?>