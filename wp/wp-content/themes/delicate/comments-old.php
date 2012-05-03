<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments">This post is password protected. Enter the password to view comments.</p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'alt';
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
	<h2><?php comments_number('No Responses', '1 Response', '% Responses' );?></h2>
	<div class="inner-text">
	<ul class="commentlist">	
	<?php foreach ($comments as $comment) : ?>
		<!-- comment -->
		
		
	<li class="comment even thread-even depth-1">
				<div>
					<div class="comment-author vcard">
								<?php echo get_avatar( $comment, $size = '32' ); ?>	
								<cite class="fn"><?php comment_author_link() ?></cite> <span class="says">says:</span>		
					</div>
			
					<div class="comment-meta commentmetadata"><a href="<?php the_permalink() ?>#comment-<?php comment_ID() ?>"><?php comment_date('l, jS F Y') ?> at <?php comment_time() ?></a>&nbsp;&nbsp;</div>
			
					<?php comment_text() ?>
			
			<div class="reply">
         <?php edit_comment_link('(Edit)','&nbsp;&nbsp;',''); ?>
      </div>
					
							</div>
	</li>

					
				<!-- /comment -->	

	<?php /* Changes every other comment to a different class */
		if ('alt' == $oddcomment) $oddcomment = '';
		else $oddcomment = 'alt';
	?>

	<?php endforeach; /* end for each comment */ ?>
	</ul></div>

 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Comments are closed.</p>

	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<!-- comment-form -->
<div id="comment-form">
				
	<h1 class="heading">Leave a Comment</h1>

		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
		<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>

</div><!-- #commentform-->

<?php else : ?>

		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		
			<div>

			<?php if ( $user_ID ) : ?>

				<p><?php _e('Logged in as', 'nattywp'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', 'nattywp'); ?>"><?php _e('Logout', 'nattywp'); ?> &raquo;</a></p>

			<?php else : ?>

				<p>
					<label for="author">Your Name: <span class="required">Required</span></label>
				<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
				</p>
				<p>
					<label for="email">Email: <span class="required">Required, Hidden</span></label>
					<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
				</p>
				<p>
					<label for="url">Website:</label>
					<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
				</p>
		
<?php endif; ?>

				<p>
					<label for="message">Message:</label>
					<textarea name="comment" id="comment" cols="40" rows="10" tabindex="4"></textarea>
				</p>

				<p style="background:transparent;">
					<input name="submit" type="submit" id="submit" tabindex="5" value="Publish My Comment" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
				</p>

				<?php do_action('comment_form', $post->ID); ?>

			</div>
		
		</form>

</div><!-- #commentform -->

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
