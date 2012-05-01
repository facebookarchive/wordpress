<?php // Do not delete these lines

if (!empty($_SERVER['SCRIPT_FILENAME']) AND 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die (__('Please do not load this page directly. Thanks!','atahualpa'));

if ( post_password_required() ) {
	_e('This post is password protected. Enter the password to view comments.','atahualpa');
	return;
}

global $bfa_ata;
// You can start editing below:
?>

<?php // If there are any comments
$bfa_page_comment_open = 0;  
if ( is_page() and ('open' == $post->comment_status)) {
	 $bfa_page_comment_open = 1; }
else {
	$bfa_page_comment_open = 0;} 

if ( have_comments() ) : ?>

	<a name="comments"></a><!-- named anchor for skip links -->
	<h3 id="comments"><?php // Comment Area Title
	comments_number(__('No comments yet to ', 'atahualpa'),
    __('1 comment to ', 'atahualpa'), __('% comments to ', 'atahualpa'));
	echo get_the_title(); ?></h3>

	<?php bfa_next_previous_comments_links('Above'); ?>

	<!-- Comment List -->
	<ul class="commentlist">
		
	<?php // Do this for every comment
	if ($bfa_ata['separate_trackbacks'] == "Yes") {

		wp_list_comments(array(
			'avatar_size'=>$bfa_ata['avatar_size'],
			'reply_text'=>__(' &middot; Reply','atahualpa'),
			'login_text'=>__('Log in to Reply','atahualpa'),
			'callback' => 'bfa_comments', 
			'type' => 'comment'
			));

		wp_list_comments(array(
			'avatar_size'=>$bfa_ata['avatar_size'],
			'reply_text'=>__(' &middot; Reply','atahualpa'),
			'login_text'=>__('Log in to Reply','atahualpa'),
			'callback' => 'bfa_comments', 
			'type' => 'pings'
			));

	} else {

		wp_list_comments(array(
			'avatar_size'=>$bfa_ata['avatar_size'],
			'reply_text'=>__(' &middot; Reply','atahualpa'),
			'login_text'=>__('Log in to Reply','atahualpa'),
			'callback' => 'bfa_comments', 
			'type' => 'all'
			));

	} ?>
	
	</ul>
	<!-- / Comment List -->

	<?php bfa_next_previous_comments_links('Below'); ?>

<?php else : // If there are NO comments  ?>

	<?php // If comments are open, but there are no comments:
if ( ('open' == $post->comment_status) ) : ?>
		<!-- .... -->

	<?php else : // If comments are closed: ?>

		<?php echo  $bfa_ata['comments_are_closed_text'] ; ?>

	<?php endif; ?>

<?php endif; // END of "If there are NO comments" ?>

<?php 
// Since 3.6.1: Configuring the new comment_form() function, 
// instead of using it with the default settings. See also http://codex.wordpress.org/Function_Reference/comment_form
?>

<?php
// These values aren't available else:
global $aria_req, $post_id, $required_text;
// author, email and url fields are set in a separate variable first:
$fields =  array(
	'author' => '<p><input class="text author" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' tabindex="1" />' . 
				'&nbsp;&nbsp;<label for="author"><strong>' . __( 'Name ' ,'atahualpa') . '</strong> ' . ( $req ? __('(required)','atahualpa') : '' ) . '</label></p>',
	'email'  => '<p><input class="text email" id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . '  tabindex="2" />' . 
				'&nbsp;&nbsp;<label for="email"><strong>' . __( 'Email' ,'atahualpa') . '</strong> ' . ( $req ? __('(will not be published) (required)','atahualpa') : '' ) . '</label></p>',
	'url'    => '<p><input class="text url" id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30"  tabindex="3" />' . 
				'&nbsp;&nbsp;<label for="url">' . __( 'Website','atahualpa' ) . '</label></p>'
); 

if ($bfa_ata['show_xhtml_tags'] == "Yes") {	
	$comment_notes_after = '
		<p class="thesetags clearfix">' . 
		sprintf(__('You can use %1$sthese HTML tags</a>','atahualpa'),
		'<a class="xhtmltags" href="#" onclick="return false;">') . '</p>
		<div class="xhtml-tags"><p><code>' . allowed_tags() . '
		</code></p></div>';
} else {
	$comment_notes_after = '';
}

// The rest is set here:
$comment_form_settings = array(
	'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
	'comment_field'        => '<p><textarea name="comment" id="comment" rows="10" cols="10" tabindex="4"></textarea></p>',
	'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'atahualpa' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
	'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'atahualpa' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
	'comment_notes_before' => '',
	'comment_notes_after'  => $comment_notes_after,
	'id_form'              => 'commentform',
	'id_submit'            => 'submit',
	'title_reply'          => __( 'Leave a Reply','atahualpa' ),
	'title_reply_to'       => __( 'Leave a Reply to %s','atahualpa' ),
	'cancel_reply_link'    => __( 'Cancel reply','atahualpa' ),
	'label_submit'         => __( 'Post Comment' ,'atahualpa')
);
?>

<?php // Using the new function comment_form() with the custom settings $comment_form_settings 
comment_form($comment_form_settings); 
?>