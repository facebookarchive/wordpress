<?php

function footer_page_links($matches) {
	$page_id = $matches[1];
	$page_data = get_page($page_id, ARRAY_A);
	$page_title = $page_data['post_title'];
	$page_url = get_permalink($page_id);
	return '<a href="' . $page_url . '">' . $page_title . '</a>'; 
}


function bfa_footer() {

	global $bfa_ata, $post, $homeURL;
	$footer_content = $bfa_ata['footer_style_content'];
	
	// page links
	if ( strpos($footer_content,'%page') !== FALSE ) {
		$footer_content = preg_replace_callback("|%page-(.*?)%|","footer_page_links",$footer_content);
	}

	// home link
	if ( strpos($footer_content,'%home%') !== FALSE ) {
		ob_start();
			echo '<a href="' . $homeURL . '/">'; bloginfo('name'); echo '</a>';
			$footer_home_link = ob_get_contents(); 
		ob_end_clean();	
		$footer_content = str_replace("%home%", $footer_home_link, $footer_content);
	}

	// login/logout link
	if ( strpos($footer_content,'%loginout%') !== FALSE ) {
		
		ob_start(); 
			wp_loginout(); 
			$loginout_link = ob_get_contents(); 
		ob_end_clean();
		
		if ( $bfa_ata['nofollow'] == "Yes" ) { 
			$loginout_link = str_replace(' href=', ' rel="nofollow" href=', $loginout_link); 
		}
		
		$footer_content = str_replace("%loginout%",  $loginout_link, $footer_content);
	}

	// register link
	if ( strpos($footer_content,'%register%') !== FALSE ) {
		
		ob_start(); 
			wp_register(); 
			$register_link = ob_get_contents(); 
		ob_end_clean();
		
		$register_link = str_replace( array('<li>', '</li>'), '', $register_link);
		
		if ( $bfa_ata['nofollow'] == "Yes" ) { 
			$register_link = str_replace(' href=', ' rel="nofollow" href=', $register_link); 
		}
		
		$footer_content = str_replace("%register%",  $register_link, $footer_content);	
	}

	/* LEGACY up to Atahualpa 3.2 admin link and register link were two different tags, 
	now they're combined into one tag %register% mimicking the wp_register() function */
	if ( strpos($footer_content,'%admin%') !== FALSE ) {
		
		ob_start(); 
			wp_register(); 
			$admin_link = ob_get_contents(); 
		ob_end_clean();
		
		$admin_link = str_replace( array('<li>', '</li>'), '', $admin_link);
		
		if ( $bfa_ata['nofollow'] == "Yes" ) { 
			$admin_link = str_replace(' href=', ' rel="nofollow" href=', $admin_link); 
		}
		
		$footer_content = str_replace("%admin%",  $admin_link, $footer_content);		
	}

	// RSS link
	if ( strpos($footer_content,'%rss%') !== FALSE ) {
		ob_start();
			bloginfo('rss2_url');
			$footer_rss2_url = ob_get_contents(); 
		ob_end_clean();
		$footer_content = str_replace("%rss%", $footer_rss2_url, $footer_content);
	}

	// Comments RSS link
	if ( strpos($footer_content,'%comments-rss%') !== FALSE ) {
		ob_start();
			bloginfo('comments_rss2_url');
			$footer_comments_rss2_url = ob_get_contents(); 
		ob_end_clean();
		$footer_content = str_replace("%comments-rss%", $footer_comments_rss2_url, $footer_content);
	}

	// Current Year
	$footer_content = str_replace("%current-year%",  date('Y'), $footer_content);

		// Parse widget areas:
	$result = bfa_parse_widget_areas( footer_output($footer_content) );
	
	return $result;

}
?>