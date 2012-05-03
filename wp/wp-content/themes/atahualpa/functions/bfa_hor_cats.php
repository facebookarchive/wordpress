<?php
function bfa_hor_cats($sort_order = "ID", $order = "ASC", $levels = "", $titles = "No", $exclude = "") { 
	
	// allow option "order" only if Plugin "My category Order" is activated:
	if ( !function_exists('mycategoryorder') AND $sort_order == 'order' ) { 
		$sort_order = "ID"; 
	}
	
	$list_cat_string = wp_list_categories('orderby=' . $sort_order . '&order=' . $order . '&title_li=&depth=' . $levels . '&exclude=' . trim(str_replace(" ", "", $exclude)) . '&echo=0');
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n<ul class='children'>/i","<li class=\"rMenu-expand \\1\n <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	$list_cat_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t\t\t\t<ul class='children'>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t\t\t\t <ul class=\"rMenu-ver children\">",$list_cat_string);
	
	# Added in 3.2.1: Option to add Category Description to menu bar link text
	global $bfa_ata; 
	
	if ( $bfa_ata['add_descr_cat_menu_links'] == "Yes" ) { 
		$list_cat_string = preg_replace_callback("| title=\"(.*?)\">(.*?)</a>|","add_descr_cat_menu_links",$list_cat_string); 
	}
	
	if ( $titles == "No" ) { 
		$list_cat_string = preg_replace("/title=\"(.*?)\"/si","",$list_cat_string);
	}

	return $list_cat_string;
}



function add_descr_cat_menu_links($matches) {
	
	global $bfa_ata; 
	
	if ( strpos($matches[1],__('View all posts filed under', 'atahualpa')) !== FALSE ) {
		
		if ( $bfa_ata['default_cat_descr_text'] != '' ) { 
			$default_cat_descr = str_replace("%category%", $matches[2], $bfa_ata['default_cat_descr_text']);
			return '>'.$matches[2].'<br /><span class="cat-descr">'.$default_cat_descr.'</span></a>';
		} else {
			return '>'.$matches[2].'</a>';
		}
		
	} else {
		return '>'.$matches[2].'<br /><span class="cat-descr">'.$matches[1].'</span></a>';
	}
	
}
?>