<?php

/**
 * Display the classes for the body element.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function body_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function get_body_class( $class = '' ) {
	global $wp_query, $wpdb, $current_user;

	$classes = array();

	if ( is_rtl() )
		$classes[] = 'rtl';

	// For WP 2.3 and older:
	if ( function_exists('is_front_page') ) {
		if ( is_front_page() ) $classes[] = 'home';
	} elseif ( is_home() AND !is_paged() ) {
			$classes[] = 'home';
	}
	
	if ( is_home() )
		$classes[] = 'blog';
	if ( is_archive() )
		$classes[] = 'archive';
	if ( is_date() )
		$classes[] = 'date';
	if ( is_search() )
		$classes[] = 'search';
	if ( is_paged() )
		$classes[] = 'paged';
	if ( is_attachment() )
		$classes[] = 'attachment';
	if ( is_404() )
		$classes[] = 'error404';

	if ( is_single() ) {
		$wp_query->post = $wp_query->posts[0];
		setup_postdata($wp_query->post);

		$postID = $wp_query->post->ID;
		$classes[] = 'single postid-' . $postID;

		if ( is_attachment() ) {
			$mime_type = get_post_mime_type();
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$classes[] = 'attachmentid-' . $postID;
			$classes[] = 'attachment-' . str_replace($mime_prefix, '', $mime_type);
		}
	} elseif ( is_archive() ) {
		if ( is_author() ) {
			$author = $wp_query->get_queried_object();
			$classes[] = 'author';
			$classes[] = 'author-' . $author->user_id;
		} elseif ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$classes[] = 'category';
			$classes[] = 'category-' . $cat->cat_ID;
		} elseif ( is_tag() ) {
			$tags = $wp_query->get_queried_object();
			$classes[] = 'tag';
			$classes[] = 'tag-' . $tags->term_id;
		}
	} elseif ( is_page() ) {
		$classes[] = 'page';

		$wp_query->post = $wp_query->posts[0];
		setup_postdata($wp_query->post);

		$pageID = $wp_query->post->ID;

		$classes[] = 'page-id-' . $pageID;

		if ( $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' LIMIT 1", $pageID) ) )
			$classes[] = 'page-parent';

		if ( $wp_query->post->post_parent )
			$classes[] = 'page-child';
			$classes[] = 'parent-pageid-' . $wp_query->post->post_parent;

		if ( is_page_template() )
			$classes[] = 'page-template';
			$classes[] = 'page-template-' . str_replace( '.php', '-php', get_post_meta( $pageID, '_wp_page_template', true ) );
	} elseif ( is_search() ) {
		if ( !empty($wp_query->posts) )
			$classes[] = 'search-results';
		else
			$classes[] = 'search-no-results';
	}

	if ( is_user_logged_in() )
		$classes[] = 'logged-in';

	$page = $wp_query->get('page');

	if ( !$page || $page < 2)
		$page = $wp_query->get('paged');

	if ( $page && $page > 1 ) {
		$classes[] = 'paged-' . $page;

		if ( is_single() )
			$classes[] = 'single-paged-' . $page;
		elseif ( is_page() )
			$classes[] = 'page-paged-' . $page;
		elseif ( is_category() )
			$classes[] = 'category-paged-' . $page;
		elseif ( is_tag() )
			$classes[] = 'tag-paged-' . $page;
		elseif ( is_date() )
			$classes[] = 'date-paged-' . $page;
		elseif ( is_author() )
			$classes[] = 'author-paged-' . $page;
		elseif ( is_search() )
			$classes[] = 'search-paged-' . $page;
	}

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	return apply_filters('body_class', $classes, $class);
}

?>