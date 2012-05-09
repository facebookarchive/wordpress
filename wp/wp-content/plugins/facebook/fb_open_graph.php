<?php
add_action( 'wp_head','fb_add_og_protocol' );

function fb_add_og_protocol() {
	global $post;

	// change me when you are ready for more than just posts
	if ( ! is_single() )
		return;

	$meta_tags = array();

	$options = get_option( 'fb_options' );

	$meta_tags['og:type'] = 'article';
	$meta_tags['og:site_name'] = get_bloginfo('name');
	$meta_tags['og:url'] = get_permalink();
	$meta_tags['og:title'] = get_the_title();
	$meta_tags['og:description'] = get_bloginfo('description', 'display'); 
	$meta_tags['article:published_time'] = get_the_date('c'); 
	$meta_tags['article:modified_time'] = get_the_modified_date('c'); 
	$meta_tags['article:author'] = get_author_posts_url( get_the_author_meta('ID') );
	$meta_tags['article:section'] = '';
	$meta_tags['article:tag'] = '';

	// does theme support post thumbnails?
	if ( function_exists( 'get_post_thumbnail_id' ) ) {
		list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		if ( ! empty( $post_thumbnail_url ) ) {
			$meta_tags['og:image'] = $post_thumbnail_url;

			if ( ! empty($post_thumbnail_width) )
				$meta_tags['og:image:width'] = $post_thumbnail_width;

			if ( ! empty($post_thumbnail_height) )
				$meta_tags['og:image:height'] = $post_thumbnail_height;
		}
	}

	$meta_tags['og:site_name'] = get_bloginfo( 'name' );
	
	if ( ! empty($options['app_id'] ) )
		$meta_tags['fb:app_id'] = $options['app_id'];
	$meta_tags['og:locale'] = fb_get_locale();
	
	$meta_tags = apply_filters( 'fb_meta_tags', $meta_tags, $post );
	
	foreach ($meta_tags as $property => $content) {
		if ( $content )
			echo "<meta property=\"$property\" content=\"" . esc_attr( $content ) . "\" />\n";
	}
}

?>