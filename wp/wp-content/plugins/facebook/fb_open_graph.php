<?php
add_action( 'wp_head','fb_add_og_protocol' );

function fb_add_og_protocol() {
	global $post;

	// change me when you are ready for more than just posts
	if ( ! is_single() )
		return;

	$meta_tags = array();

	$meta_tags['http://ogp.me/ns#locale'] = fb_get_locale();
	$meta_tags['http://ogp.me/ns#site_name'] = get_bloginfo( 'name' );
	$meta_tags['http://ogp.me/ns#type'] = 'article';
	$meta_tags['http://ogp.me/ns#url'] = get_permalink();
	$meta_tags['http://ogp.me/ns#title'] = get_the_title();
	$meta_tags['http://ogp.me/ns#description'] = get_bloginfo('description', 'display');
	$meta_tags['http://ogp.me/ns/article#published_time'] = get_the_date('c'); 
	$meta_tags['http://ogp.me/ns/article#modified_time'] = get_the_modified_date('c'); 
	$meta_tags['http://ogp.me/ns/article#author'] = get_author_posts_url( get_the_author_meta('ID') );

	$cat_ids = get_the_category();
	if ( ! empty( $cat_ids ) ) {
		$cat = get_category( $cat_ids[0] );
		if ( ! empty( $cat ) )
			$meta_tags['http://ogp.me/ns/article#section'] = $cat->name;

		/*
		TODO: output the rest of the categories as tags
		unset( $cat_ids[0] );
		if ( ! empty( $cat_ids ) ) {
			foreach( $cat_ids as $cat_id ) {
				$cat = get_category( $cat_id );
				$article->addTag( $cat->name );
				unset( $cat );
			}
		} */
	}

	// TODO: add tags. treat tags as lower priority than multiple categories
	// $meta_tags['http://ogp.me/ns/article#tag'] = '';

	// does theme support post thumbnails?
	if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
		list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		if ( ! empty( $post_thumbnail_url ) ) {
			$meta_tags['http://ogp.me/ns#image'] = $post_thumbnail_url;

			if ( ! empty($post_thumbnail_width) )
				$meta_tags['http://ogp.me/ns#image:width'] = $post_thumbnail_width;

			if ( ! empty($post_thumbnail_height) )
				$meta_tags['http://ogp.me/ns#image:height'] = $post_thumbnail_height;
		}
	}

	$options = get_option( 'fb_options' );
	if ( ! empty( $options['app_id'] ) )
		$meta_tags['http://ogp.me/ns/fb#app_id'] = $options['app_id'];
	
	$meta_tags = apply_filters( 'fb_meta_tags', $meta_tags, $post );
	
	foreach ( $meta_tags as $property => $content ) {
		if ( $content )
			echo "<meta property=\"$property\" content=\"" . esc_attr( $content ) . "\" />\n";
	}
}

?>