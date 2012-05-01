<?php

// This adds classes to all, and an ID to most <BODY> tags
function bodyclasses() {

	global $post;
	
	if ( is_home() ) { 
		$body_classes = "body-homepage";
		if ( is_paged() ) {
			$body_id = "body-homepage-" . $paged; $body_classes .= " body-homepage-paged body-paged"; 
		}
		else 
			$body_id = "body-homepage"; 
	}
	
	if ( function_exists('is_front_page') ) {
		if ( is_front_page() ) 
			$body_id = "body-frontpage"; 
	}
		
	if ( is_page() ) 
		$body_id = "body-page-" . $post->ID; $body_classes .=" body-page"; 
		
	if ( is_single() ) 
		$body_id = "body-post-" . $post->ID; $body_classes .=" body-post"; 
	
	if ( is_category() ) {
		$body_classes .= " category";
		if ( is_paged() ) {
			$body_id = "body-cat-" . intval( get_query_var('cat') ) . "-" . 
			$paged; $body_classes .=" body-category-paged body-paged"; 
		}
		else 
			$body_id = "body-cat-" . intval( get_query_var('cat') ); 
	}

	if ( is_year() OR is_month() ) 
		$body_classes .= " body-archive" . single_month_title(' ', false);
	
	if ( is_search() ) 
		$body_classes .= " body-search";
	
	if ( is_404() ) 
		$body_classes .= " body-error";
	
	if ( is_date() ) $body_classes .= " body-date";

	if ( is_author() ) 
		$body_classes .= " body-author";
	
	if ( function_exists('is_tag') ) {
		if ( is_tag() ) 
			$body_classes .= " body-tag";
	}

	if ( is_page() OR is_single() ) {
		$custom_fields = get_post_custom($post->ID);
		if ($my_custom_field = $custom_fields['bfa_body_class']) {
			foreach ( $my_custom_field as $key => $value )
			$body_classes .= " $value";
		}
	}
	
	$body_classes = trim($body_classes);
	echo ( ($body_id != '') ? " id=\"$body_id\"" : '') . " class=\"$body_classes\"";

}
?>