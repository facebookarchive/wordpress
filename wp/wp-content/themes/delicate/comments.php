<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','nattywp'); ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>
	<h2><?php comments_number(__('No Responses','nattywp'), __('One Response','nattywp'), __('% Responses','nattywp'));?></h2>
	<div class="inner-text">				

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>		
			<div class="navigation">
				<div class="alignleft"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'nattywp' ) ); ?></div>
				<div class="alignright"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'nattywp' ) ); ?></div>
			</div><div style="clear:both;"></div>
<?php endif; // check for comment navigation ?>			
            
            
			<ul class="commentlist">
			<?php wp_list_comments('style=ul&callback=natty_themecomment'); ?>
			</ul>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>			
			<div class="navigation">
				<div class="alignleft"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'nattywp' ) ); ?></div>
				<div class="alignright"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'nattywp' ) ); ?></div>
			</div>
<?php endif; // check for comment navigation ?>				
              <div style="clear:both;"></div><br /><br />
	</div>
 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"></p>

	<?php endif; ?>
<?php endif; ?>

<?php 
$defaults = array(
	'id_form'              => 'comment-form',
	'id_submit'            => 'submit',
	'title_reply'          => __( 'Leave a Reply', 'nattywp' ),
	'title_reply_to'       => __( 'Leave a Reply to %s', 'nattywp' ),
	'cancel_reply_link'    => __( 'Cancel reply', 'nattywp' ),
	'label_submit'         => __( 'Post Comment', 'nattywp' )
);

comment_form($defaults); ?>