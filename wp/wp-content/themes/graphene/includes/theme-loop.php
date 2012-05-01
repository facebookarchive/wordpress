<?php

/**
 * Function to display ads from adsense
*/
$adsense_adcount = 1;
$ad_limit = apply_filters( 'graphene_adsense_ads_limit', 3 );
if (!function_exists( 'graphene_adsense' ) ) :
	function graphene_adsense(){
		global $adsense_adcount, $ad_limit, $graphene_settings;
		
		if ( $graphene_settings['show_adsense'] && $adsense_adcount <= $ad_limit) : ?>
            <div class="post adsense_single clearfix" id="adsense-ad-<?php echo $adsense_adcount; ?>">
                <?php echo stripslashes( $graphene_settings['adsense_code']); ?>
            </div>
            <?php do_action( 'graphene_show_adsense' ); ?>
		<?php endif;
		
		$adsense_adcount++;
		
		do_action( 'graphene_adsense' );
	}
endif;


/**
 * Function to display the AddThis social sharing button
*/

if (!function_exists( 'graphene_addthis' ) ) :
	function graphene_addthis( $post_id = '' ){
		if ( ! $post_id ){
			global $post;
			$post_id = $post->ID;
			if ( ! $post_id ) return;
		}
		
		global $graphene_settings;
		
		// Get the local setting
		$show_addthis_local = ( get_post_meta( $post_id, '_graphene_show_addthis', true) ) ? get_post_meta( $post_id, '_graphene_show_addthis', true) : 'global';
		$show_addthis_global = $graphene_settings['show_addthis'];
		$show_addthis_page = $graphene_settings['show_addthis_page'];
		
		// Determine whether we should show AddThis or not
		if ( $show_addthis_local == 'show' )
			$show_addthis = true;
		elseif ( $show_addthis_local == 'hide' )
			$show_addthis = false;
		elseif ( $show_addthis_local == 'global' ){
			if ( ( $show_addthis_global && get_post_type() != 'page' ) || ( $show_addthis_global && $show_addthis_page ) )
				$show_addthis = true;
			else
				$show_addthis = false;
		}
		
		// Show the AddThis button
		if ( $show_addthis) {
			echo '<div class="add-this-right">';
			$html = stripslashes( $graphene_settings['addthis_code'] );
			$html = str_replace( '[#post-url]', esc_attr( get_permalink( $post_id ) ), $html );
			$html = str_replace( '[#post-title]', esc_attr( get_the_title( $post_id ) ), $html );
			$html = str_replace( '[#post-excerpt]', esc_attr( get_the_excerpt() ), $html );
			echo $html;
			echo '</div>';
			
			do_action( 'graphene_show_addthis' );
		}
		do_action( 'graphene_addthis' );
	}
endif;


/**
 * Returns a "Continue Reading" link for excerpts
 * Based on the function from the Twenty Ten theme
 *
 * @since Graphene 1.0.8
 * @return string "Continue Reading" link
 */
if (!function_exists( 'graphene_continue_reading_link' ) ) :
	function graphene_continue_reading_link() {
		global $in_slider;
		if (!is_page() && !$in_slider) {
			$more_link_text = __( 'Continue reading &raquo;', 'graphene' );
			return '</p><p><a class="more-link block-button" href="'.get_permalink().'">'.$more_link_text.'</a>';
		}
	}
endif;


/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and graphene_continue_reading_link().
 * Based on the function from Twenty Ten theme.
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Graphene 1.0.8
 * @return string An ellipsis
 */
function graphene_auto_excerpt_more( $more) {
	return apply_filters( 'graphene_auto_excerpt_more', ' &hellip; '.graphene_continue_reading_link() );
}
add_filter( 'excerpt_more', 'graphene_auto_excerpt_more' );


/**
 * Add the Read More link to manual excerpts
 *
 * @since Graphene 1.1.3
*/
function graphene_manual_excerpt_more( $text){
	global $in_slider;
	if (has_excerpt() && !$in_slider){
		$text = explode( '</p>', $text);
		$text[count( $text)-2] .= graphene_continue_reading_link();
		$text = implode( '</p>', $text);
	}
	return $text;
}
if ( $graphene_settings['show_excerpt_more']) {
	add_filter( 'the_excerpt', 'graphene_manual_excerpt_more' );
}


/**
 * Generates the posts navigation links
*/
if (!function_exists( 'graphene_posts_nav' ) ) :
	function graphene_posts_nav(){ 
		$query = $GLOBALS['wp_query'];
		
		if (function_exists( 'wp_pagenavi' ) ) :  ?>
			<div class="post-nav clearfix">
				<?php wp_pagenavi(); ?>
            </div>
        <?php 
		
		elseif ( $query->max_num_pages > 1) : ?>
            <div class="post-nav clearfix">
                <?php if (!is_search() ) : ?>
                    <p id="previous"><?php next_posts_link(__( 'Older posts &laquo;', 'graphene' ) ) ?></p>
                    <p id="next-post"><?php previous_posts_link(__( '&raquo; Newer posts', 'graphene' ) ) ?></p>
                <?php else : ?>
                    <p id="next-post"><?php next_posts_link(__( 'Next page &raquo;', 'graphene' ) ) ?></p>
                    <p id="previous"><?php previous_posts_link(__( '&laquo; Previous page', 'graphene' ) ) ?></p>
                <?php endif; ?>
            </div>
         
	<?php
		endif;
	}
endif;


/**
 * Generates the post navigation links
*/
if ( ! function_exists( 'graphene_post_nav' ) ) :

function graphene_post_nav(){
	if ( is_singular() ) :
	?>
	<div class="post-nav clearfix">
		<p class="previous"><?php previous_post_link(); ?></p>
		<p class="next-post"><?php next_post_link(); ?></p>
		<?php do_action( 'graphene_post_nav' ); ?>
	</div>
	<?php
	endif;
}

endif;


/**
 * Control the excerpt length
*/
function graphene_modify_excerpt_length( $length ) {
	global $graphene_settings;
	/*
	$column_mode = graphene_column_mode();
	if ( $graphene_settings['slider_display_style'] == 'bgimage-excerpt' ){
		if ( strpos( $column_mode, 'three_col' ) === 0)
			return 24;
		if ( strpos( $column_mode, 'two_col' ) === 0)
			return 40;
		if ( $column_mode == 'one_column' )
			return 55;
	}
	*/
	
	return apply_filters( 'graphene_modify_excerpt_length', $graphene_settings['excerpt_length'] );
}
add_filter( 'excerpt_length', 'graphene_modify_excerpt_length' );


/**
 * Set the excerpt length
 *
 * @param int $length Excerpt length
 *
 * @package Graphene
 * @since 1.6
*/
function graphene_set_excerpt_length( $length ){
	if ( ! $length ) return;
	global $graphene_settings;
	$graphene_settings['excerpt_length'] = $length;
}


/**
 * Reset the excerpt length
 *
 * @package Graphene
 * @since 1.6
*/
function graphene_reset_excerpt_length(){
	global $graphene_settings, $graphene_defaults;
	$graphene_settings['excerpt_length'] = $graphene_defaults['excerpt_length'];
}


/**
 * This function gets the first image (as ordered in the post's media gallery) attached to
 * the current post. It outputs the complete <img> tag, with height and width attributes.
 * The function returns the thumbnail of the original image, linked to the post's 
 * permalink. Returns FALSE if the current post has no image.
 *
 * This function requires the post ID to get the image from to be supplied as the
 * argument. If no post ID is supplied, it outputs an error message. An optional argument
 * size can be used to determine the size of the image to be used.
 *
 * Based on code snippets by John Crenshaw 
 * (http://www.rlmseo.com/blog/get-images-attached-to-post/)
 *
 * @package Graphene
 * @since Graphene 1.1
*/
if ( ! function_exists( 'graphene_get_post_image' ) ) :
	function graphene_get_post_image( $post_id = NULL, $size = 'thumbnail', $context = '', $urlonly = false ){
		
		/* Display error message if no post ID is supplied */
		if ( $post_id == NULL ){
			_e( '<strong>ERROR: You must supply the post ID to get the image from as an argument when calling the graphene_get_post_image() function.</strong>', 'graphene' );
			return;
		}
		
		/* Get the images */
		$images = get_children( array( 
								'post_type' 		=> 'attachment',
								'post_mime_type' 	=> 'image',
								'post_parent' 	 	=> $post_id,
								'orderby'			=> 'menu_order',
								'order'				=> 'ASC',
								'numberposts'		=> 1,
									 ), ARRAY_A );
		
		$html = '';
		
		/* Returns generic image if there is no image to show */
		if ( empty( $images ) && $context != 'excerpt' && ! $urlonly ) {
			$html .= apply_filters( 'graphene_generic_slider_img', '' ); // For backward compatibility
			$html .= apply_filters( 'graphene_generic_post_img', '' );
		}
		
		/* Build the <img> tag if there is an image */
		foreach ( $images as $image ){
			if (!$urlonly) {
				if ( $context == 'excerpt' ) {$html .= '<div class="excerpt-thumb">';};
				$html .= '<a href="'.get_permalink( $post_id).'">';
				$html .= wp_get_attachment_image( $image['ID'], $size);
				$html .= '</a>';
				if ( $context == 'excerpt' ) {$html .= '</div>';};
			} else {
				$html = wp_get_attachment_image_src( $image['ID'], $size);
			}
		}
		
		/* Returns the image HTMl */
		return $html;
}
endif;


/**
 * Improves the WordPress default excerpt output. This function will retain HTML tags inside the excerpt.
 * Based on codes by Aaron Russell at http://www.aaronrussell.co.uk/blog/improving-wordpress-the_excerpt/
*/
function graphene_improved_excerpt( $text ){
	global $graphene_settings, $post;
	
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content( '' );
		$text = strip_shortcodes( $text );
		$text = apply_filters( 'the_content', $text);
		$text = str_replace( ']]>', ']]&gt;', $text);
		
		/* Remove unwanted JS code */
		$text = preg_replace( '@<script[^>]*?>.*?</script>@si', '', $text);
		
		/* Strip HTML tags, but allow certain tags */
		$text = strip_tags( $text, $graphene_settings['excerpt_html_tags']);

		$excerpt_length = apply_filters( 'excerpt_length', 55);
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count( $words) > $excerpt_length ) {
			array_pop( $words);
			$text = implode( ' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode( ' ', $words);
		}
	}
	
	// Try to balance the HTML tags
	$text = force_balance_tags( $text );
	
	return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt);
}

/**
 * Only use the custom excerpt trimming function if user decides to retain html tags.
*/
if ( $graphene_settings['excerpt_html_tags'] ) {
	remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
	add_filter( 'get_the_excerpt', 'graphene_improved_excerpt' );
}


/**
 * Determine if date should be displayed. Returns true if it should, or false otherwise.
*/
if ( ! function_exists( 'graphene_should_show_date' ) ) :

function graphene_should_show_date(){
	
	// Check post type
	$allowed_posttypes = apply_filters( 'graphene_date_display_posttype', array( 'post' ) );
	if ( ! in_array( get_post_type(), $allowed_posttypes ) )
		return false;
	
	// Check per-post settings
	global $post;
	$post_setting = get_post_meta( $post->ID, '_graphene_post_date_display', true );
	if ( $post_setting == 'hide' )
		return false;
		
	// Check global setting
	global $graphene_settings;
	if ( $graphene_settings['post_date_display'] == 'hidden' )
		return false;
	
	return true;
}

endif;


/**
 * This functions adds additional classes to the post element. The additional classes
 * are added by filtering the WordPress post_class() function.
*/
function graphene_post_class( $classes ){
    global $graphene_settings;
    
	if ( in_array( $graphene_settings['post_date_display'], array( 'hidden', 'text' ) ) || ! graphene_should_show_date() ) {
		$classes[] = 'nodate';
	}
	
	// $classes = array_merge( $classes, graphene_get_grid( '', 16, 11, 8, true ) );
		
    // Prints the body class
    return $classes;
}
add_filter( 'post_class', 'graphene_post_class' );


/**
 * Allows post queries to sort the results by the order specified in the post__in parameter. 
 * Just set the orderby parameter to post__in!
 *
 * Based on the Sort Query by Post In plugin by Jake Goldman (http://www.get10up.com)
*/
add_filter( 'posts_orderby', 'graphene_sort_query_by_post_in', 10, 2 );

function graphene_sort_query_by_post_in( $sortby, $thequery ) {
	if ( ! empty( $thequery->query['post__in'] ) && isset( $thequery->query['orderby'] ) && $thequery->query['orderby'] == 'post__in' )
		$sortby = "find_in_set(ID, '" . implode( ',', $thequery->query['post__in'] ) . "')";
	
	return $sortby;
}


/**
 * Displays the date. Must be used inside the loop.
 *
 * Accepts 1 argument, $style, which is the style of date to display, which is either 'icon'
 * or 'inline'.
*/
function graphene_post_date( $style = 'icon' ){
	global $graphene_settings;
	
	if ( $style == 'icon' ) :
	?>
    	<div class="date updated alpha <?php if ( $graphene_settings['post_date_display'] == 'icon_plus_year' ) echo 'with-year'; ?>">
        	<span class="value-title" title="<?php the_time( 'Y-m-d\TH:i' ); ?>" />
            <p class="default_date">
            	<span class="month"><?php the_time( 'M' ); ?></span>
                <span class="day"><?php the_time( 'd' ) ?></span>
                <?php if ( $graphene_settings['post_date_display'] == 'icon_plus_year' ) : ?>
	                <span class="year"><?php the_time( 'Y' ); ?></span>
                <?php endif; ?>
            </p>
            <?php do_action( 'graphene_post_date' ); ?>
        </div>
    <?php
	endif;
	
	if ( $style == 'inline' ) :
	?>
    	<p class="post-date-inline updated">
        	<span class="value-title" title="<?php the_time( 'Y-m-d\TH:i' ); ?>"></span>
            <abbr class="published" title="<?php the_date( 'c' ); ?>"><?php the_time( get_option( 'date_format' ) ); ?></abbr>
            <?php do_action( 'graphene_post_date' ); ?>
        </p>
    <?php
	endif;
}


/**
 * Displays the print button
*/
function graphene_print_button( $post_type ){
	?>
    <p class="print">
        <a href="javascript:print();" title="<?php esc_attr_e( sprintf( __('Print this %s', 'graphene' ), strtolower( $post_type->labels->singular_name ) ) ); ?>">
            <span><?php printf( __('Print this %s', 'graphene' ), $post_type->labels->singular_name ); ?></span>
        </a>
    </p>
    <?php
}


/**
 * Add .first-p class to the first <p> element in a text block
 *
 * @param string $text A text block
 * @return string $text The text block with the .first-p class added to the first <p> element
 *
 * @package Graphene
 * @since 1.6
 */
function graphene_first_p( $text ){
	
	$text = preg_replace('/<p([^>]+)?>/', '<p$1 class="first-p">', $text , 1);
	
	return apply_filters( 'graphene_first_p', $text );
}
?>