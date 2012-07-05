<?php
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
	}
	else {
		echo "<meta property=\"$property\" content=\"" . esc_attr( $content ) . "\" />\n";
	}
}

function fb_strip_and_format_desc( $post ) {
	
	$desc_no_html = "";
	$desc_no_html = strip_shortcodes( $desc_no_html ); // Strip shortcodes first in case there is HTML inside the shortcode
        $desc_no_html = wp_strip_all_tags( $desc_no_html ); // Strip all html
        $desc_no_html = trim( $desc_no_html ); // Trim the final string, we may have stripped everything out of the post so this will make the value empty if that's the case

	// Check if empty, may be that the strip functions above made excerpt empty, doubhtful but we want to be 100% sure.
	if( empty($desc_no_html) ) {
		$desc_no_html = $post->post_content; // Start over, this time with the post_content
		$desc_no_html = strip_shortcodes( $desc_no_html ); // Strip shortcodes first in case there is HTML inside the shortcode
		$desc_no_html = str_replace(']]>', ']]&gt;', $desc_no_html); // Angelo Recommendation, if for some reason ]]> happens to be in the_content, rare but We've seen it happen
		$desc_no_html = wp_strip_all_tags($desc_no_html);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$desc_no_html = wp_trim_words( $desc_no_html, $excerpt_length, $excerpt_more );
		$desc_no_html = trim($desc_no_html); // Trim the final string, we may have stripped everything out of the post so this will make the value empty if that's the case
	}
	
	$desc_no_html = str_replace( array( "\r\n", "\r", "\n" ), ' ',$desc_no_html); // I take it Facebook doesn't like new lines?
	return $desc_no_html;
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
		$meta_tags['http://ogp.me/ns#title'] = get_bloginfo( 'name' );
		$meta_tags['http://ogp.me/ns#description'] = get_bloginfo( 'description' );
	} else if ( is_single() ) {
		$post_type = get_post_type();
		$meta_tags['http://ogp.me/ns#type'] = 'article';
		$meta_tags['http://ogp.me/ns#url'] = apply_filters( 'rel_canonical', get_permalink() );
		if ( post_type_supports( $post_type, 'title' ) )
			$meta_tags['http://ogp.me/ns#title'] = get_the_title();
		if ( post_type_supports( $post_type, 'excerpt' ) ) {
			// thanks to Angelo Mandato (http://wordpress.org/support/topic/plugin-facebook-plugin-conflicts-with-powerpress?replies=16)
			// Strip and format the wordpress way, but don't apply any other filters which adds junk that ends up getitng stripped back out
			if ( !post_password_required($post) ) {
				// First lets get the post excerpt (shouldn't have any html, but anyone can enter anything...)
				$meta_tags['http://ogp.me/ns#description'] = fb_strip_and_format_desc ( $post );
			}
		}
		
		$meta_tags['http://ogp.me/ns/article#published_time'] = get_the_date('c');
		$meta_tags['http://ogp.me/ns/article#modified_time'] = get_the_modified_date('c');
		
		if ( post_type_supports( $post_type, 'author' ) && isset( $post->post_author ) )
			$meta_tags['http://ogp.me/ns/article#author'] = get_author_posts_url( $post->post_author );

		// add the first category as a section. all other categories as tags
		$cat_ids = get_the_category();
		
		if ( ! empty( $cat_ids ) ) {
			$cat = get_category( $cat_ids[0] );
			
			if ( ! empty( $cat ) )
				$meta_tags['http://ogp.me/ns/article#section'] = $cat->name;

			//output the rest of the categories as tags
			unset( $cat_ids[0] );
			
			if ( ! empty( $cat_ids ) ) {
				$meta_tags['http://ogp.me/ns/article#tag'] = array();
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
	else if ( is_author() && isset( $post->post_author ) ) {
		$meta_tags['http://ogp.me/ns#type'] = 'profile';
		$meta_tags['http://ogp.me/ns/profile#first_name'] = get_the_author_meta( 'first_name', $post->post_author );
		$meta_tags['http://ogp.me/ns/profile#last_name'] = get_the_author_meta( 'last_name', $post->post_author );
		if ( is_multi_author() )
			$meta_tags['http://ogp.me/ns/profile#username'] = get_the_author_meta( 'login', $post->post_author );
	}
	else if ( is_page() ) {
		$meta_tags['http://ogp.me/ns#type'] = 'article';
		$meta_tags['http://ogp.me/ns#title'] = get_the_title();
		$meta_tags['http://ogp.me/ns#url'] = apply_filters( 'rel_canonical', get_permalink() );
	}

	$options = get_option( 'fb_options' );
	
	if ( ! empty( $options['app_id'] ) )
		$meta_tags['http://ogp.me/ns/fb#app_id'] = $options['app_id'];

	$meta_tags = apply_filters( 'fb_meta_tags', $meta_tags, $post );

	foreach ( $meta_tags as $property => $content ) {
		fb_output_og_protocol( $property, $content );
	}
}

add_action( 'wp_head', 'fb_add_og_protocol' );
?>
