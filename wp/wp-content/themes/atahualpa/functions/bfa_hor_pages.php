<?php
function bfa_hor_pages($sort_order = "menu_order", $levels = "", $titles = "No", $exclude = "") { 
	
	global $bfa_ata;

	$list_pages_string = wp_list_pages('sort_column=' . $sort_order . '&title_li=&depth=' . $levels . '&exclude=' . trim(str_replace(" ", "", 
	$exclude)) . '&echo=0&link_before=<span>&link_after=</span>');
	
	$list_pages_string = preg_replace("/<ul class='children'/","<ul",$list_pages_string);

	if ( $bfa_ata['page_menu_1st_level_not_linked'] == "Yes" ) {
		$list_pages_string = preg_replace("/<li class=\"(.*?)><a href=\"(.*?)\"(.*?)\n<ul>/i","<li class=\"rMenu-expand \\1><a href=\"#\" onclick=\"return false\"\\3\n <ul class=\"rMenu-ver\">",$list_pages_string);
	} else {
		$list_pages_string = preg_replace("/<li class=\"(.*?)\n<ul>/i","<li class=\"rMenu-expand \\1\n <ul class=\"rMenu-ver\">",$list_pages_string);
	}
	
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t<ul>/i","<li class=\"rMenu-expand \\1\n\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	$list_pages_string = preg_replace("/<li class=\"(.*?)\n\t\t\t\t\t\t\t\t\t<ul>/i","<li class=\"rMenu-expand \\1\n\t\t\t\t\t\t\t\t\t <ul class=\"rMenu-ver\">",$list_pages_string);
	
	if ( $titles == "No" ) { 
		$list_pages_string = preg_replace("/title=\"(.*?)\"/i","",$list_pages_string);
	}
	
	return $list_pages_string;
	
}
?>