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
	<h2 class="comments-title"><?php echo esc_html( apply_filters( 'facebook_wp_comments_title', __( 'Comments' ) ) ); ?></h2>
	<ol class="comment-list"><?php
		/**
		 * List comments using the default WordPress comments output
		 * Allow parameter overrides through facebook_wp_list_comments filter
		 */
		wp_list_comments( apply_filters( 'facebook_wp_list_comments', array( 'style' => 'ol' ) ) ); ?></ol><?php

endif; // have_comments()

$_facebook_comments = Facebook_Comments::comments_box();
if ( $_facebook_comments )
	echo '<div id="respond">' . $_facebook_comments . '</div>';
unset( $_facebook_comments );
?>
</div>