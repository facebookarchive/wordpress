<?php
/**
 * WordPress comments template for the Facebook plugin for WordPress.
 *
 * Integrate with the comments display area of WordPress themes by overriding the theme's default comments template with a Facebook plugin-specific comments template including existing WordPress comments, static markup wrapped in a <noscript> based on comments stored on Facebook servers, and Comments Box XFBML markup to be interpreted by the Facebook SDK for JavaScript. Attempt to match CSS-addressable elements of WordPress core themes and customization functions where possible but with Facebook plugin-specific filters to avoid unexpected behaviors.
 *
 * @since 1.3
 */


/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">
<?php if ( have_comments() ) :
	// remove links to reply to an existing comment in the WordPress comment system
	add_filter( 'comment_reply_link', array( 'Facebook_Loader', '__return_empty_string' ) );
	add_filter( 'post_comments_link', array( 'Facebook_Loader', '__return_empty_string' ) );
	add_filter( 'cancel_comment_reply_link', array( 'Facebook_Loader', '__return_empty_string' ) );
	add_filter( 'comment_id_fields', array( 'Facebook_Loader', '__return_empty_string' ) );
?>
	<h2 class="comments-title"><?php
/**
 * Customize the title shown above the comments area in the comments template
 *
 * @since 1.3
 * @param string Comments title.
 */
echo esc_html( apply_filters( 'facebook_wp_comments_title', __( 'Comments' ) ) );
?></h2><?php
	/**
	 * Customize the HTML element style of the static markup wrapper displaying WordPress comments.
	 *
	 * Some sites may have accepted WordPress plugins for a post before switching to the Facebook Comments Box social plugin. Display previous comments before displaying the
	 *
	 * @since 1.3
	 *
	 * @see wp_list_comments() for a full list of options to be passed to wp_list_comments
	 * @param array {
	 *     Comments options.
	 *
	 *     @type string 'style' Can be either 'div', 'ol', or 'ul', to display comments using divs, ordered, or unordered lists. Default 'ol'.
	 * }
	 */
	$_comment_options = apply_filters( 'facebook_wp_list_comments', array( 'style' => 'ol' ) );
	// correct filter if broken
	if ( ! is_array( $_comment_options ) )
		$_comment_options = array( 'style' => 'ol' );
	else if ( ! ( isset( $_comment_options['style'] ) ) && in_array( $_comment_options['style'], array( 'ol', 'ul', 'div' ), true ) )
		$_comment_options['style'] = 'ol';
	?>
	<<?php echo $_comment_options['style']; ?> class="comment-list"><?php
		/**
		 * List comments using the default WordPress comments output
		 * Allow parameter overrides through facebook_wp_list_comments filter
		 */
		wp_list_comments( $_comment_options ); ?></<?php echo $_comment_options['style']; ?>><?php
	unset( $_comment_options );
endif; // have_comments()

// generate Facebook Comments Box markup
$_facebook_comments = Facebook_Comments::comments_box();
if ( $_facebook_comments ) {
	/**
	 * Output content before the Facebook Comments Box display area
	 *
	 * @since 1.3
	 */
	do_action( 'facebook_comment_form_before' );
	echo '<div id="respond" class="comment-respond">';
	echo $_facebook_comments;
	echo '</div>';
	/**
	 * Output content after the Facebook Comments Box display area
	 *
	 * @since 1.3
	 */
	do_action( 'facebook_comment_form_after' );
}
unset( $_facebook_comments );
?>
</div>
