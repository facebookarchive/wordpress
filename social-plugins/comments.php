<?php
/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">
<?php if ( have_comments() ) :
	add_filter( 'comment_reply_link', array( 'Facebook_Loader', '__return_empty_string' ) );
	add_filter( 'post_comments_link', array( 'Facebook_Loader', '__return_empty_string' ) );
?>
	<h2 class="comments-title"><?php echo esc_html( apply_filters( 'facebook_wp_comments_title', __( 'Comments' ) ) ); ?></h2><?php
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

$_facebook_comments = Facebook_Comments::comments_box();
if ( $_facebook_comments )
	echo '<div id="respond" class="comments-area">' . $_facebook_comments . '</div>';
unset( $_facebook_comments );
?>
</div>
