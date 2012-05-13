<?php
add_action( 'wp_head','fb_add_og_protocol' );

/**
 * Recursively build RDFa <meta> elements used for Open Graph protocol
 *
 * @since 1.0
 * @param string $property whitespace separated list of CURIEs placed in a property attribute
 * @param mixed content attribute value for the given property. use an array for array property values or structured properties
 */
function fb_output_og_protocol( $property, $content ) {
	if ( empty( $property ) || empty( $content ) )
		return;

	// array of property values or structured property
	if ( is_array( $content ) ) {
		foreach( $content as $structured_property => $content_value ) {
			// handle numeric keys from regular arrays
			// account for the special structured property of url which is equivalent to the root tag and sets up the structure
			if ( ! is_string( $structured_property ) || $structured_property === 'url' )
				fb_output_og_protocol( $property, $content_value );
			else
				fb_output_og_protocol( $property . ':' . $structured_property, $content_value );
		}
	} else {
		echo "<meta property=\"$property\" content=\"" . esc_attr( $content ) . "\" />\n";
	}
}

/**
 * Add Open Graph protocol markup to <head>
 *
 * @since 1.0
 */
function fb_add_og_protocol() {
	global $post;

	$meta_tags = array(
		'http://ogp.me/ns#locale' => fb_get_locale(),
		'http://ogp.me/ns#site_name' => get_bloginfo( 'name' ),
		'http://ogp.me/ns#type' => 'website'
	);

	if ( is_home() || is_front_page() ) {
		$meta_tags['http://ogp.me/ns#description'] = get_bloginfo( 'description' );
	} else if ( is_single() ) {
		$post_type = get_post_type();
		$meta_tags['http://ogp.me/ns#type'] = 'article';
		$meta_tags['http://ogp.me/ns#url'] = apply_filters( 'rel_canonical', get_permalink() );
		if ( post_type_supports( $post_type, 'title' ) )
			$meta_tags['http://ogp.me/ns#title'] = get_the_title();
		if ( post_type_supports( $post_type, 'excerpt' ) )
			$meta_tags['http://ogp.me/ns#description'] = apply_filters( 'the_excerpt', get_the_excerpt() );
		$meta_tags['http://ogp.me/ns/article#published_time'] = get_the_date('c');
		$meta_tags['http://ogp.me/ns/article#modified_time'] = get_the_modified_date('c');
		if ( is_multi_author() && post_type_supports( $post_type, 'author' ) )
			$meta_tags['http://ogp.me/ns/article#author'] = get_author_posts_url( get_the_author_meta('ID') );

		// add the first category as a section. all other categories as tags
		$cat_ids = get_the_category();
		if ( ! empty( $cat_ids ) ) {
			$cat = get_category( $cat_ids[0] );
			if ( ! empty( $cat ) )
				$meta_tags['http://ogp.me/ns/article#section'] = $cat->name;

			//output the rest of the categories as tags
			unset( $cat_ids[0] );
			$meta_tags['http://ogp.me/ns/article#tag'] = array();
			if ( ! empty( $cat_ids ) ) {
				foreach( $cat_ids as $cat_id ) {
					$cat = get_category( $cat_id );
					$meta_tags['http://ogp.me/ns/article#tag'][] = $cat->name;
					unset( $cat );
				}
			}
		}

		// add tags. treat tags as lower priority than multiple categories
		$tags = get_the_tags();
		if ( $tags ) {
			if ( ! array_key_exists( 'http://ogp.me/ns/article#tag', $meta_tags ) )
				$meta_tags['http://ogp.me/ns/article#tag'] = array();
			foreach ( $tags as $tag ) {
				$meta_tags['http://ogp.me/ns/article#tag'][] = $tag->name;
			}
		}

		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( $post_type, 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			list( $post_thumbnail_url, $post_thumbnail_width, $post_thumbnail_height ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
			if ( ! empty( $post_thumbnail_url ) ) {
				$image = array( 'url' => $post_thumbnail_url );

				if ( ! empty( $post_thumbnail_width ) )
					$image['width'] = absint( $post_thumbnail_width );

				if ( ! empty($post_thumbnail_height) )
					$image['height'] = absint( $post_thumbnail_height );
				$meta_tags['http://ogp.me/ns#image'] = array( $image );
			}
		}
	}

	$options = get_option( 'fb_options' );
	if ( ! empty( $options['app_id'] ) )
		$meta_tags['http://ogp.me/ns/fb#app_id'] = $options['app_id'];

	$meta_tags = apply_filters( 'fb_meta_tags', $meta_tags, $post );

	foreach ( $meta_tags as $property => $content ) {
		fb_output_og_protocol( $property, $content );
	}
}

?>