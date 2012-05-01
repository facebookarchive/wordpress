<?php
add_action('wp_head','fb_add_og_protocol');

function fb_add_og_protocol() {
	global $post;
	
	$meta_tags = array();
	
	$options = get_option('fb_options');
	
	$meta_tags['og:type'] = 'article';
  $meta_tags['og:site_name'] = esc_attr(get_bloginfo('name'));
  $meta_tags['og:image'] = get_header_image();
	$meta_tags['og:url'] = get_permalink();
  $meta_tags['og:title'] = get_the_title();
  $meta_tags['og:description'] = get_bloginfo('description', 'display'); 
  $meta_tags['article:published_time'] = get_the_date('c'); 
  $meta_tags['article:modified_time'] = get_the_date('c'); 
  $meta_tags['article:expiration_time'] = get_the_date('c'); 
  $meta_tags['article:author'] = get_author_posts_url(get_the_author_meta('ID'));
  $meta_tags['article:section'] = '';
  $meta_tags['article:tag'] = '';
	
	$meta_tags['og:site_name'] = get_bloginfo("name");
	
	if (!empty($options["app_id"])) $meta_tags['fb:app_id'] = esc_attr($options["app_id"]);
	$meta_tags['og:locale'] = fb_get_locale();
	
	$meta_tags = apply_filters('fb_meta_tags', $meta_tags, $post);
	
	foreach ($meta_tags as $prop => $content) {
		echo "<meta property='$prop' content='$content' />\n";
	}
}

?>