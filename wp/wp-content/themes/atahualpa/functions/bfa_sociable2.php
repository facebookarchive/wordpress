<?php
function sociable_html2($display=Array()) {
	global $sociable_known_sites, $sociablepluginpath, $wp_query; 

	$active_sites = get_option('sociable_active_sites');
	$html = "";
	$imagepath = $sociablepluginpath.'images/';

	// if no sites are specified, display all active
	// have to check $active_sites has content because WP
	// won't save an empty array as an option
	if (empty($display) and $active_sites)
		$display = $active_sites;
	// if no sites are active, display nothing
	if (empty($display))
		return "";

	// Load the post's data
	$blogname 	= urlencode(get_bloginfo('name')." ".get_bloginfo('description'));
	$post 		= $wp_query->post;
	
	$excerpt	= $post->post_excerpt;
	if ($excerpt == "") {
		$excerpt = urlencode(substr(strip_tags($post->post_content),0,250));
	}
	$excerpt	= str_replace('+','%20',$excerpt);
	
	$permalink 	= urlencode(get_permalink($post->ID));
	
	$title 		= urlencode($post->post_title);
	$title 		= str_replace('+','%20',$title);
	
	$rss 		= urlencode(get_bloginfo('ref_url'));

	$html .= "\n<span class=\"sociable\">\n";
	$html .= "<ul>\n";

	foreach($display as $sitename) {
		// if they specify an unknown or inactive site, ignore it
		if (!in_array($sitename, $active_sites))
			continue;

		$site = $sociable_known_sites[$sitename];

		$url = $site['url'];
		$url = str_replace('PERMALINK', $permalink, $url);
		$url = str_replace('TITLE', $title, $url);
		$url = str_replace('RSS', $rss, $url);
		$url = str_replace('BLOGNAME', $blogname, $url);
		$url = str_replace('EXCERPT', $excerpt, $url);

		if (isset($site['description']) && $site['description'] != "") {
			$description = $site['description'];
		} else {
			$description = $sitename;
		}
		$link = "<li>";		
		$link .= "<a rel=\"nofollow\"";
		if (get_option('sociable_usetargetblank')) {
			$link .= " target=\"_blank\"";
		}
		$link .= " href=\"$url\" title=\"$description\">";
		$link .= "<img src=\"$imagepath{$site['favicon']}\" title=\"$description\" alt=\"$description\" class=\"sociable-hovers";
		if ($site['class'])
			$link .= " sociable_{$site['class']}";
		$link .= "\" />";
		$link .= "</a></li>";
		
		$html .= "\t".apply_filters('sociable_link',$link)."\n";
	}

	$html .= "</ul>\n</span>\n";

	return $html;
}
?>