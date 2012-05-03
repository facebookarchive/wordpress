<?php
/**
 * Comments - functions that deal with comments
 *
 * @package EvoLve
 * @subpackage Core
 */

/**
 * evolve_discussion_title()
 *
 * @since 0.3
 * @needsdoc
 * @filter evolve_many_comments, evolve_no_comments, evolve_one_comment, evolve_comments_number
 */
function evolve_discussion_title( $type = NULL, $echo = true ) {
	if ( !$type ) return;
  
  $discussion_title = '';

	$comment_count = evolve_count( 'comment', false );
	$ping_count = evolve_count( 'pings', false );

	switch( $type ) {
		case 'comment' :
			$count = $comment_count;
			$many  = apply_filters( 'evolve_many_comments', __('% Comments', 'evolve' )); // Available filter: evolve_many_comments
			$none  = apply_filters( 'evolve_no_comments', __('No Comments Yet', 'evolve' )); // Available filter: evolve_no_comments
			$one   = apply_filters( 'evolve_one_comment', __('1 Comment', 'evolve' )); // Available filter: evolve_one_comment
			break;
		case 'pings' :
			$count = $ping_count;
			$many  = apply_filters( 'evolve_many_pings', __('% Trackbacks', 'evolve' )); // Available filter: evolve_many_pings
			$none  = apply_filters( 'evolve_no_pings', __('', 'evolve' )); // Available filter: evolve_no_pings
			$one   = apply_filters( 'evolve_one_ping', __('1 Trackback', 'evolve' )); // Available filter: evolve_one_comment
			break;
	}
	
	if ( $count > 1 ) {
		$number = str_replace( '%', number_format_i18n( $count ), $many );
	} elseif ( $count == 1 ) {
		$number = $one;
	} else { // it must be one
		$number = $none;
	}
	
	// Now let's format this badboy.
	$tag = apply_filters( 'evolve_discussion_title_tag', (string) 'h3' ); // Available filter: evolve_discussion_title_tag
	
	if ( $number ) {
		$discussion_title  = '<'. $tag .' class="'. $type .'-title">';
		$discussion_title .= '<span class="'. $type .'-title-meta">' . $number . '</span>';
		$discussion_title .= '</'. $tag .'>';
	}
	$evolve_discussion_title = apply_filters( 'evolve_discussion_title', (string) $discussion_title ); // Available filter: evolve_discussion_title
	return ( $echo ) ? print( $evolve_discussion_title ) : $evolve_discussion_title;
}

/**
 * evolve_discussion_rss()
 *
 * @since 0.3
 * @needsdoc
 */
function evolve_discussion_rss() {
	global $id;
	$uri = get_post_comments_feed_link( $id );
  $text = "<span class=\"comment-feed-link\"><a title=\"Follow replies\" class=\"tipsytext follow-replies\" href=\"{$uri}\"></a></span>";
	echo $text;
}

/**
 * evolve_count()
 *
 * @since 0.3
 * @needsdoc
 */
function evolve_count( $type = NULL, $echo = true ) {
	if ( !$type ) return;
	global $wp_query;

	$comment_count = count( $wp_query->comments_by_type['comment'] );
	$ping_count = count( $wp_query->comments_by_type['trackback'] );
	
	switch ( $type ):
		case 'comment':
			return ( $echo ) ? print( $comment_count ) : $comment_count;
			break;
		case 'pings':
			return ( $echo ) ? print( $ping_count ) : $ping_count;
			break;
	endswitch;
}

/**
 * evolve_comment_author() short description
 *
 * @since 0.3
 * @todo needs filter
 */
function evolve_comment_author( $meta_format = '%avatar% %name%' ) {
	$meta_format = apply_filters( 'evolve_comment_author_meta_format', $meta_format ); // Available filter: evolve_comment_author_meta_format
	if ( ! $meta_format ) return;
	
	// No keywords to replace
	if ( strpos( $meta_format, '%' ) === false ) {
		echo $meta_format;
	} else {
		$open  = '<!--BEGIN .comment-author-->' . "\n";
		$open .= '<div class="comment-author vcard">' . "\n";
		$close  = "\n" . '<!--END .comment-author-->' . "\n";
		$close .= '</div>' . "\n";
		
		// separate the %keywords%
		$meta_array = preg_split( '/(%.+?%)/', $meta_format, -1, PREG_SPLIT_DELIM_CAPTURE );

		// parse through the keywords
		foreach ( $meta_array as $key => $str ) {
			switch ( $str ) {
				case '%avatar%':
					$meta_array[$key] = evolve_comment_avatar();
					break;

				case '%name%':
					$meta_array[$key] = evolve_comment_name();
					break;
			}
		}
		$output = join( '', $meta_array );
		if ( $output ) echo $open . $output . $close; // output the result
	}
}

/**
 * evolve_comment_meta() short description
 *
 * @since 0.3.1
 * @todo needs filter
 */
function evolve_comment_meta( $meta_format = '%date% at %time% | %link% %edit%' ) {	
	$meta_format = apply_filters( 'evolve_comment_meta_format', $meta_format ); // Available filter: evolve_comment_meta_format
	if ( ! $meta_format ) return;
	
	// No keywords to replace
	if ( strpos( $meta_format, '%' ) === false ) {
		echo $meta_format;
	} else {
		$open  = '<!--BEGIN .comment-meta-->' . "\n";
		$open .= '<div class="comment-meta">' . "\n";
		$close  = "\n" . '<!--END .comment-meta-->' . "\n";
		$close .= '</div>' . "\n";
		
		// separate the %keywords%
		$meta_array = preg_split( '/(%.+?%)/', $meta_format, -1, PREG_SPLIT_DELIM_CAPTURE );

		// parse through the keywords
		foreach ( $meta_array as $key => $str ) {
			switch ( $str ) {
				case '%date%':
					$meta_array[$key] = evolve_comment_date();
					break;

				case '%time%':
					$meta_array[$key] = evolve_comment_time();
					break;

				case '%link%':
					$meta_array[$key] = evolve_comment_link();
					break;
				
				case '%reply%':
					$meta_array[$key] = evolve_comment_reply( true );
					break;
					
				case '%edit%':
					$meta_array[$key] = evolve_comment_edit();
					break;
			}
		}
		$output = join( '', $meta_array );
		if ( $output ) echo $open . $output . $close; // output the result
	}
}

/**
 * evolve_comment_text() short description
 *
 * @since 0.3.1
 */
function evolve_comment_text() {
	echo "\n<!--BEGIN .comment-content-->\n";
	echo "<div class=\"comment-content\">\n";
	comment_text();
	echo "\n<!--END .comment-content-->\n";
	echo "</div>\n";
}

/**
 * evolve_comment_moderation() short description
 *
 * @since - 0.3.1
 */
function evolve_comment_moderation() {
	global $comment;
	if ( $comment->comment_approved == '0' ) echo '<p class="comment-unapproved moderation alert">Your comment is awaiting moderation</p>';
}

/**
 * evolve_comment_navigation() paged comments
 *
 * @since 0.3
 * @needsdoc
 * @todo add html comments?
 */
function evolve_comment_navigation() {
	$num = get_comments_number() + 1;
	
	$tag = apply_filters( 'evolve_comment_navigation_tag', (string) 'div' ); // Available filter: evolve_comment_navigation_tag
	$open = "<!--BEGIN .navigation-links-->\n";
	$open .= "<". $tag ." class=\"navigation-links comment-navigation\">\n";
	$close = "<!--END .navigation-links-->\n";
	$close .= "</". $tag .">\n";
	
	if ( $num > get_option( 'comments_per_page' ) ) {		
		$paged_links = paginate_comments_links( array(
			'type' => 'array',
			'echo' => false,
			'prev_text' => '&laquo; Previous Page',
			'next_text' => 'Next Page &raquo;' ) );
		
		if ( $paged_links ) $comment_navigation = $open . join( ' ', $paged_links ) . $close;
	}
	else {
		$comment_navigation = NULL;
	}
	echo apply_filters( 'evolve_comment_navigation', (string) $comment_navigation ); // Available filter: evolve_comment_navigation
}

/**
 * evolve_comments_callback() recreate the comment list
 *
 * @since 0.3
 * @needsdoc
 */
function evolve_comments_callback( $comment, $args, $depth ) {	
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $depth;
	$tag = apply_filters( 'evolve_comments_list_tag', (string) 'li' ); // Available filter: evolve_comments_list_tag
	?>
    
    <!--BEING .comment-->
	<<?php echo $tag; ?> class="<?php semantic_comments(); ?>" id="comment-<?php echo comment_ID(); ?>">
    	<?php evolve_hook_comments(); ?>
	<?php
}

/**
 * evolve_comments_endcallback() close the comment list
 *
 * @since 0.3
 * @needsdoc
 * @todo needs filter
 */
function evolve_comments_endcallback(){
	$tag = apply_filters( 'evolve_comments_list_tag', (string) 'li' ); // Available filter: evolve_comments_list_tag
	echo "<!--END .comment-->\n";
	echo "</". $tag .">\n";
	do_action( 'evolve_hook_inside_comments_loop' ); // Available action: evolve_hook_inside_comments_loop
}

/**
 * evolve_pings_callback() recreate the comment list
 *
 * @since 0.3
 * @needsdoc
 */
function evolve_pings_callback( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	$tag = apply_filters( 'evolve_pings_callback_tag', (string) 'li' ); // Available filter: evolve_pings_callback_tag
	$time = apply_filters( 'evolve_pings_callback_time', (string) ' on ' ); // Available filter: evolve_pings_callback_time
	$when = apply_filters( 'evolve_pings_callback_when', (string) ' at ' ); // Available filter: evolve_pings_callback_time
	?>
    <?php if ( $comment->comment_approved == '0' ) echo '<p class="ping-unapproved moderation alert">Your trackback is awaiting moderation.</p>\n'; ?>
    <!--BEING .pings-->
	<<?php echo $tag; ?> class="<?php echo semantic_comments(); ?>" id="ping-<?php echo $comment->comment_ID; ?>">
		<?php comment_author_link(); echo $time; ?><a class="trackback-time" href="<?php comment_link(); ?>"><?php comment_date(); echo $when; comment_time(); ?></a>
	<?php
}

/**
 * evolve_pings_endcallback() close the comment list
 *
 * @since 0.3
 * @needsdoc
 */
function evolve_pings_endcallback(){
	$tag = apply_filters( 'evolve_pings_callback_tag', (string) 'li' ); // Available filter: evolve_pings_callback_tag
	echo "<!--END .pings-list-->\n";
	echo "</". $tag .">\n";
	do_action( 'evolve_hook_inside_pings_list' ); // Available action: evolve_hook_inside_pings_list
}
?>