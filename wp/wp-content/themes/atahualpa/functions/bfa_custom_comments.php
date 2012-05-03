<?php
function bfa_comments($comment, $args, $depth) {

global $bfa_ata;

   $GLOBALS['comment'] = $comment; ?>
		<li <?php comment_class($class='clearfix') ?> id="comment-<?php comment_ID(); ?>">
		<div id="div-comment-<?php comment_ID(); ?>" class="clearfix comment-container<?php 
		$comment = get_comment($comment_id);
		if ( $post = get_post($post_id) ) {
			if ( $comment->user_id === $post->post_author )
				echo ' bypostauthor';
		} ?>">
		<div class="comment-author vcard">
		<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
		<span class="authorname"><?php comment_author_link(); ?></span>
		</div>
		<?php if ($comment->comment_approved == '0') : ?>
		<em><?php echo $bfa_ata['comment_moderation_text']; ?></em><br />
		<?php endif; ?>
		<div class="comment-meta commentmetadata">
		<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php printf(__('%1$s at %2$s','atahualpa'), get_comment_date(),  get_comment_time()) ?></a>
        <?php echo comment_reply_link(array('before' => '<span class="comment-reply-link">', 'after' => '</span>', 'reply_text' => $bfa_ata['comment_reply_link_text'], 'depth' => $depth, 'max_depth' => $args['max_depth'] ));  ?>
		<?php edit_comment_link($bfa_ata['comment_edit_link_text'],'<span class="comment-edit-link">','</span>') ?> 
		</div>
		<?php comment_text() ?>
		</div>
<?php } ?>