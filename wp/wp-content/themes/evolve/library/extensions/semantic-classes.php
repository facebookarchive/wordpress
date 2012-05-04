<?php
/**
 * Semantic Classes is made up of class-generating functions
 * that dynamically generate context sensitive classes and ids
 * to give unprecedented control over your layout options via CSS.
 * 
 * @package EvoLve
 * @subpackage Semantic_Markup
 */

/**
 * Functions:
 * semantic_title();
 * semantic_body();
 * semantic_entries();
 * semantic_comments();
 * semantic_last_class();
 */

/* Define the num val for 'alt' classes (in post DIV and comment LI) */
$semantic_post_alt = 1;
$semantic_comment_alt = 1;

/**
 * semantic_title() - Generates semantic classes for the <title> tag with extra SEO love.
 *
 * @todo refactor code
 * @since - 0.2
 * @filter semantic_title
 */
function semantic_title( $sep = '&mdash;' ) {
	if ( is_single() ) : wp_title( '&raquo;', true, 'right' ); bloginfo( 'name' );
	echo ( ' - ' );
	echo bloginfo( 'description' );
	
	elseif ( is_page() || is_paged() ) : wp_title( '&raquo;', true, 'right' ); 
	bloginfo( 'name' ); echo ( ' - ' );
	echo bloginfo( 'description' );
	
	elseif ( is_author() ) : wp_title( 'Archives for ', true, 'left' );
	echo ( ' &raquo; ' ); bloginfo( 'name' );
	echo ( ' - ' );
	echo bloginfo( 'description' );
	  
	elseif ( is_archive() ) : wp_title( 'Archives for ', true, 'left' );
	echo ( ' &raquo; ' ); bloginfo( 'name' );
	echo ( ' - ' );
	echo bloginfo( 'description' );
	
	elseif ( is_search() ) : wp_title('Search Results ', true, 'left' );
	echo ( ' &raquo; ' ); bloginfo( 'name' );
	echo ( ' - ' );
	echo bloginfo( 'description' );
	
	elseif ( is_404() ) : wp_title( '404 Error Page Not Found ', true, 'left' );
	echo ( ' &raquo; ' ); bloginfo( 'name' );
	echo ( ' - ');
	echo bloginfo( 'description' );
	
	else : wp_title( '&raquo', true, 'left' ); bloginfo( 'name' );
	echo ( ' - ' );
	echo bloginfo( 'description' );         
	endif;
}

/**
 * semantic_body() - Generates semantic classes for <body> element
 *
 * @since - 0.2
 * @filter semantic_body
 * @uses semantic_time()
 */
function semantic_body( $classes = array() ) {
	global $wp_query, $current_user;
	
	//$classes = get_body_class();
	
	// Starts the semantic markup array
	$sc = array( 'EvoLve' );
	
	// Generic semantic classes for what type of content is displayed
	is_front_page()  ? $classes[] = 'home'       : null; // For the front page, if set
	is_home()        ? $classes[] = 'blog'       : null; // For the blog posts page, if set
	is_singular()	 ? $classes[] = 'singular'   : null;
	is_single()		 ? $classes[] = 'single'     : null;
	is_archive()     ? $classes[] = 'archive'    : null; // For archive based templates: archive.php, author.php, category.php, tag.php
	is_date()        ? $classes[] = 'date'       : null;
	is_search()      ? $classes[] = 'search'     : null;
	is_paged()       ? $classes[] = 'paged'      : null;
	is_attachment()  ? $classes[] = 'attachment' : null;
	is_404()         ? $classes[] = 'error404'   : null;
	
	// Applies the time- and date-based classes (below) to BODY element
	
	// Special classes for BODY element when a single post
	if ( is_single() ) {
		$postID = $wp_query->post->ID;
		the_post();
		
		// Adds 'single' class and class with the post ID
		$sc[] = 'single postid-' . $postID;
		
		// Adds classes for the month, day, and hour when the post was published

	
		// Adds MIME-specific classes for attachments
		if ( is_attachment() ) {
			$mime_type = get_post_mime_type();
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$sc[] = 'attachmentid-' . $postID . ' attachment-' . str_replace( $mime_prefix, "", "$mime_type" );
		}
		
		// Adds category classes for each category on single posts
		if ( $cats = get_the_category() )
			foreach ( $cats as $cat )
				if ( $cat ) $classes[] = 's-category-' . $cat->slug;
				else $classes[] = 's-category-none';
	
		// Adds tag classes for each tags on single posts
		if ( $tags = get_the_tags() )
			foreach ( $tags as $tag )
				if ( $tag ) $classes[] = 's-tag-' . $tag->slug;
				else $classes[] = 's-tag-none';
	
		// Adds author class for the post author
		if ( the_author_meta('login') ) $s_author = sanitize_title_with_dashes( strtolower( the_author_meta('login') ) );
		else $s_author = 'none';
		$classes[] = 's-author-' . $s_author;
		rewind_posts();
	}
	
	// Author name classes for BODY on author archives
	elseif ( is_author() ) {
		$author = $wp_query->get_queried_object();
		$classes[] = 'author';
		if ( $author ) $classes[] = 'author-' . $author->user_nicename;
		else $classes[] = 'author-none';
	}
	
	// Category name classes for BODY on category archvies
	elseif ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		$classes[] = 'category';
		if ( $cat ) $classes[] = 'category-' . $cat->slug;
		else $classes[] = 'category-none';
	}
	
	// Tag name classes for BODY on tag archives
	elseif ( is_tag() ) {
		$tags = $wp_query->get_queried_object();
		$classes[] = 'tag';
		if ( $tags ) $classes[] = 'tag-' . $tags->slug;
		else $classes[] = 'tag-none';
	}
	
	// Page author for BODY on 'pages'
	elseif ( is_page() ) {
		$pageID = $wp_query->post->ID;
		$page_children = wp_list_pages( "child_of=$pageID&echo=0" );
		if ( !$pageID )
			$pageID = 0;
			$page_children = 0;
		
		the_post();
		
		$classes[] = 'page pageid-' . $pageID;
		// Checks to see if the page has children and/or is a child page; props to Adam
		if ( $page_children ) $classes[] = 'page-parent';
		if ( $wp_query->post->post_parent ) $classes[] = 'page-child parent-pageid-' . $wp_query->post->post_parent;
		if ( is_page_template() ) $classes[] = 'page-template page-template-' . str_replace( '.php', '-php', get_post_meta( $pageID, '_wp_page_template', true ) );
		
		rewind_posts();
	}
	
	// Page author for BODY on 'pages'
	elseif ( is_page() ) {
		$pageID = $wp_query->post->ID;
		$page_children = wp_list_pages( "child_of=$pageID&echo=0" );
		the_post();
		$sc[] = 'page pageid-' . $pageID;
		// Checks to see if the page has children and/or is a child page; props to Adam
		if ( $page_children )
				$sc[] = 'page-parent';
		if ( $wp_query->post->post_parent )
				$sc[] = 'page-child parent-pageid-' . $wp_query->post->post_parent;
		if ( is_page_template() ) // Hat tip to Ian, themeshaper.com
				$sc[] = 'page-template page-template-' . str_replace( '.php', '-php', get_post_meta( $pageID, '_wp_page_template', true ) );
		rewind_posts();
	}
	
	// Search classes for results or no results
	elseif ( is_search() ) {
		the_post();
		
		if ( have_posts() ) $sc[] = 'search-results';
		else $sc[] = 'search-no-results';
		
		rewind_posts();
	}
	
	// For when a visitor is logged in while browsing
	if ( $current_user->ID ) $sc[] = 'loggedin';
	
	// Paged classes; for 'page X' classes of index, single, etc.
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 ) {
		$sc[] = 'paged-' . $page;
		if ( is_single() ) $sc[] = 'single-paged-' . $page;
		elseif ( is_page() ) $sc[] = 'page-paged-' . $page;
		elseif ( is_category() ) $sc[] = 'category-paged-' . $page;
		elseif ( is_tag() ) $sc[] = 'tag-paged-' . $page;
		elseif ( is_date() ) $sc[] = 'date-paged-' . $page;
		elseif ( is_author() ) $sc[] = 'author-paged-' . $page;
		elseif ( is_search() ) $sc[] = 'search-paged-' . $page;
	}
	
	// A little browser detection shall we?
	$browser = $_SERVER[ 'HTTP_USER_AGENT' ];
	
	// Mac, PC ...or Linux?
	if ( preg_match( "/Mac/", $browser ) ) $classes[] = 'mac';
	elseif ( preg_match( "/Windows/", $browser ) ) $classes[] = 'windows';
	elseif ( preg_match( "/Linux/", $browser ) ) $classes[] = 'linux';
	else $classes[] = 'unknown-os';
	
	// Checks browsers in this order: Chrome, Safari, Opera, MSIE, FF
	// Then, get the browser's version number
	if ( preg_match( "/Chrome/", $browser ) ) {
		$classes[] = 'chrome';

		preg_match( "/Chrome\/(\d.\d)/si", $browser, $matches);
		$ch_version = 'ch' . str_replace( '.', '-', $matches[1] );      
		$classes[] = $ch_version;
	
	} elseif ( preg_match( "/Safari/", $browser ) ) {
		$classes[] = 'safari';
		
		preg_match( "/Version\/(\d.\d)/si", $browser, $matches);
		$sf_version = 'sf' . str_replace( '.', '-', $matches[1] );      
		$classes[] = $sf_version;
			
	} elseif ( preg_match( "/Opera/", $browser ) ) {
		$classes[] = 'opera';
		
		preg_match( "/Opera\/(\d.\d)/si", $browser, $matches);
		$op_version = 'op' . str_replace( '.', '-', $matches[1] );      
		$classes[] = $op_version;
			
	} elseif ( preg_match( "/MSIE/", $browser ) ) {
		$classes[] = 'msie';
		
		if( preg_match( "/MSIE 6.0/", $browser ) ) $classes[] = 'ie6';
		elseif ( preg_match( "/MSIE 7.0/", $browser ) ) $classes[] = 'ie7';
		elseif ( preg_match( "/MSIE 8.0/", $browser ) ) $classes[] = 'ie8';
			
	} elseif ( preg_match( "/Firefox/", $browser ) && preg_match( "/Gecko/", $browser ) ) {
		$classes[] = 'firefox';
		
		preg_match( "/Firefox\/(\d)/si", $browser, $matches);
		$ff_version = 'ff' . str_replace( '.', '-', $matches[1] );      
		$classes[] = $ff_version;
			
	} else $classes[] = 'unknown-browser';
	
	$classes = join( ' ', apply_filters( 'semantic_body',  $classes ) ); // Available filter: semantic_body
	$print = apply_filters( 'semantic_body_print', false ); // Available filter: semantic_body_print
	
	// And tada!
	if ( !$print ) echo $classes;
	else return $classes;
}

/**
 * semantic_entries() - Generates semantic classes for each post <div> element
 *
 * @since - 0.2
 * @filter semantic_entries
 * @uses semantic_time()
 */
function semantic_entries( $classes = array() ) {
	global $post, $semantic_post_alt, $entry_first_class;
	
	// Let WordPress do all the heavy lifting
	$classes[] = join( ' ', get_post_class() );
	
	// Gets 'alt' for every other post DIV, p[n] and post status
	$classes[] = "p$semantic_post_alt";
	$classes[] = $post->post_status;
	
	// add css class to first comment
	if( $entry_first_class == 0 )
		$classes[] = 'first-' . $post->post_type;
		$entry_first_class = 1;
	
	// Author for the post queried

	
	if ( get_the_category($post->ID) != null ) {
		$classes[] = 'cat';
	}
	
	// Tags for the post queried; if not tagged, use .untagged
	if ( get_the_tags($post->ID) == null ) $classes[] = 'untagged';
	else $classes[] = 'tag';

	// For password-protected posts
	if ( $post->post_password ) $classes[] = 'protected';

	// Applies the time- and date-based classes


	// If it's the other to the every, then add 'alt' class
	if ( ++$semantic_post_alt % 2 && !is_singular() ) $classes[] = 'alt';

	$classes = join( ' ', apply_filters( 'semantic_entries',  $classes ) ); // Available filter: semantic_entries
	$print = apply_filters( 'semantic_entries_print', false ); // Available filter: semantic_entries_print
	
	// And tada!
	if ( !$print ) echo $classes;
	else return $classes;
}

/**
 * semantic_comments() - Generates semantic classes for each comment <li> element
 *
 * @since - 0.2
 * @filter semantic_comments
 * @uses semantic_time()
 */
function semantic_comments( $classes = array() ) {
	global $comment, $post, $wpdb, $current_user, $comment_first_class, $semantic_comment_alt;
	
	// Collects the comment type (comment, trackback)
	$classes[] = get_comment_type();
	
	// add css class to first comment
	if( $comment_first_class == 0 )
		$classes[] = 'first-comment';
		$comment_first_class = 1;	
	
	// add css class to last comment
	if( $comment->comment_ID == semantic_last_class( 'comment' ) and !$comment_first_class ) $classes[] = 'last-comment';
			
	// Show commenter's capabilities
	if ( $comment->user_id > 0 && $user = get_userdata( $comment->user_id ) ) {
		$capabilities = $user->{$wpdb->prefix . 'capabilities'}; // hat tip to Justin Tadlock http://www.themehybrid.com
		
		if ( array_key_exists( 'administrator', $capabilities ) ) $classes[] = 'administrator administrator-' . $user->user_login;
		elseif ( array_key_exists( 'editor', $capabilities ) ) $classes[] = 'editor editor-' . $user->user_login;
		elseif ( array_key_exists( 'author', $capabilities ) ) $classes[] = 'author author-' . $user->user_login;
		elseif ( array_key_exists( 'contributor', $capabilities ) ) $classes[] = 'contributor contributor-' . $user->user_login;
		elseif ( array_key_exists( 'subscriber', $capabilities ) ) $classes[] = 'subscriber subscriber-' . $user->user_login;
		
		// For comment authors who are the author of the post
		if ( $post = get_post( $post_id ) )
			if ( $comment->user_id === $post->post_author ) $classes[] = 'entry-author entry-author-' . $user->user_login;
			
	} else $classes[] = 'reader reader-' . str_replace( ' ', '-', strtolower( $comment->comment_author ) );
	
	// http://microid.org
	$email = get_comment_author_email();
	$uri = get_comment_author_url();
	if ( !empty( $email ) && !empty( $uri ) ) {
		if ( preg_match( '/https:\/\//i', $uri ) ) $protocal = 'https';
		elseif ( preg_match( '/http:\/\//i', $uri ) ) $protocal = 'http';
		$microid = "microid-mailto+{$protocal}:sha1:" . sha1( sha1( 'mailto:' . $email ) . sha1( $uri ) );
		$classes[] = $microid;
	}
			
	// If it's the other to the every, then add 'alt' class; collects time- and date-based classes

	
	$classes = join( ' ', apply_filters( 'semantic_comments',  $classes ) ); // Available filter: semantic_comments
	$print = apply_filters( 'semantic_comments_print', false ); // Available filter: semantic_comments_print
	
	// And tada!
	if ( !$print ) echo $classes;
	else return $classes;
}

/**
 * semantic_last_class() - returns the ID for the last class.
 *
 * @since - 0.3
 */
function semantic_last_class( $type = NULL ){
	global $comment, $post, $wpdb;
	if ( !$type == 'comment' || !$type == 'post' )
		return;

	$post_id = $post->ID;
	
	// type can be post/comment (W.I.P.)
	if ( $type == 'comment' )
		$query = "SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post_id";
	
	if ($type) $get_id = $wpdb->get_results( $query, ARRAY_N );
	
	$last = end( $get_id );
	return $last[0];
}


/* Remember: Semantic Classes, like the Sandbox, is for play. (-_^) */
?>