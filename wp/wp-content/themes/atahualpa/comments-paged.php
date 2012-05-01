<?php // Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments-paged.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die (__('Please do not load this page directly. Thanks!','atahualpa'));

if (!empty($post->post_password)) { // if there's a password
	if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','atahualpa'); ?></p>
		<?php
		return;
	}
}

/* This variable is for alternating comment background */
$oddcomment = 'class="alt clearfix" ';

global $bfa_ata;
// You can start editing below: 
?>

<?php // If there are any comments
if ($comments) : ?>

	<a name="comments"></a><!-- named anchor for skip links -->
	<h3 id="comments"><?php // Comment Area Title 
	comments_number(__('No comments yet to ', 'atahualpa'), __('1 comment to ', 'atahualpa'), 
	__('% comments to ', 'atahualpa')); echo get_the_title(); ?></h3>
	
	<!-- Comment page numbers -->
	<?php if ($paged_comments->pager->num_pages() > 1): ?>
	<p class="comment-page-numbers"><?php _e('Pages:','atahualpa'); ?> <?php paged_comments_print_pages(); ?></p>
	<?php endif; ?>
	<!-- End comment page numbers -->
	
	<!-- Comment List -->
	<ol class="commentlist">
		

		<?php // Do this for every comment -->
		foreach ($comments as $comment) : 
		if ( ($bfa_ata['separate_trackbacks'] == "Yes" AND get_comment_type() == 'comment') 
		OR $bfa_ata['separate_trackbacks'] == "No" ) { ?>

		<li <?php if ( $bfa_ata['author_highlight'] == "Yes" AND $comment->comment_author_email == get_the_author_meta('email') ) { 
				echo 'class="authorcomment clearfix" '; 
			} else { 
				echo $oddcomment; 
			} ?> id="comment-<?php comment_ID() ?>">
			
			<?php // GRAVATAR
			if (get_comment_type() == 'comment') {
			if ($bfa_ata['avatar_size'] != 0 AND $bfa_ata['avatar_size'] != "") {
			if (function_exists('get_avatar')) {
			echo get_avatar($comment -> comment_author_email, $size=$bfa_ata['avatar_size']);} 
			# if this WP version has no gravatars, use the theme's custom gravatar function:
			else { if(!empty($comment -> comment_author_email)) {
				$md5 = md5($comment -> comment_author_email);
				$default = urlencode(get_template_directory_uri() . '/images/no-gravatar.gif');
				echo '<img class="avatar" src="http://www.gravatar.com/avatar.php?gravatar_id='.$md5.'&size='.$bfa_ata['avatar_size'].'&default='.$default.' alt="'. __('Gravatar','atahualpa') .'" />';
				}
			}
			}
			}
			?>
		
			<div class="comment-number"><a href="<?php echo paged_comments_url('comment-'.get_comment_ID()); ?>" title=""><?php echo $comment_number; $comment_number += $comment_delta; ?></a></div>
			
			<span class="authorname"><?php // Comment Author
			comment_author_link() ?></span>
			
			<?php // Awaiting Moderation Text
			if ($comment->comment_approved == '0') : 
			_e('Your comment is awaiting moderation.','atahualpa'); 
			endif; ?>
		
			<br />
			
			<span class="commentdate">
			<?php // Comment Date and Time
			printf(__('%1$s at %2$s','atahualpa'), get_comment_date(__('F jS, Y','atahualpa')),  get_comment_time()) ?>
			</span>
			
			<?php // Comment Text
			comment_text() ?>
			
			<?php // Edit Comment Link
			edit_comment_link(__('Edit','atahualpa'),'<span class="editcomment">','</span>'); ?>
			

		</li>

		<?php $oddcomment = ( $oddcomment == 'class="clearfix" ' ) ? 'class="alt clearfix" ' : 'class="clearfix" '; 
		
		}
		endforeach; 
		// END of "Do this for every comment "
		?>
		
		<?php // Do this for every trackback / pingpack 
		if ($bfa_ata['separate_trackbacks'] == "Yes") {
		foreach ($comments as $comment) : 
		if ( get_comment_type() != 'comment') { ?>

		<li <?php echo $oddcomment; ?>id="comment-<?php comment_ID() ?>">
					
			<div class="comment-number"><a href="<?php echo paged_comments_url('comment-'.get_comment_ID()); ?>" title=""><?php echo $comment_number; $comment_number += $comment_delta; ?></a></div>
			
			<?php // Comment Author
			comment_author_link() ?>:
			
			<br />
			
			<?php // Comment Date and Time
			printf(__('%1$s at %2$s','atahualpa'), get_comment_date(__('F jS, Y','atahualpa')),  get_comment_time()) ?>
			
			<?php // Edit Comment Link
			edit_comment_link(__('Edit','atahualpa'),'&nbsp;&nbsp;',''); ?>
			
			<?php // Comment Text
			comment_text() ?>

		</li>

		<?php $oddcomment = ( empty( $oddcomment ) ) ? 'class="alt" ' : ''; 
		
		}
		endforeach; 
		}
		// END of "Do this for every trackback / pingback "
		?>
	
	</ol>
	<!-- / Comment List -->

	<!-- Comment page numbers -->
	<?php if ($paged_comments->pager->num_pages() > 1): ?>
	<p class="comment-page-numbers"><?php _e('Pages:','atahualpa'); ?> <?php paged_comments_print_pages(); ?></p>
	<?php endif; ?>
	<!-- End comment page numbers -->
	
<?php // END of "If there ARE any comments"
else : ?>

	<?php // If comments are open, but there are no comments:
	if ('open' == $post->comment_status) : ?>
	
		<!-- .... -->

	<?php // If comments are closed:
	else : ?>
	
		<p><?php _e('Comments are closed.','atahualpa'); ?></p>
		
	<?php endif; ?>

<?php // END of "If there are NO comments"
endif; ?>

<?php // If comments are open
if ('open' == $post->comment_status) : ?>

	<div id="respond">
	
	<a name="commentform"></a><!-- named anchor for skip links -->
	<h3 class="reply"><?php _e('Leave a Reply','atahualpa'); ?></h3>
		
	<?php // If Login is required and User is not logged in 
	if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p><?php printf(__('You must be %slogged in</a> to post a comment.', 'atahualpa'), '<a href="' . 
	get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode(get_permalink()) . '">')?></p>

	<?php // If Login is not required, or User is logged in 
	else : ?>
		
		<!-- Comment Form -->
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

			<?php // If User is logged in
			if ( $user_ID ) : ?>
			<p>
			<?php printf(__('Logged in as %s.', 'atahualpa'), '<a href="' . get_option('siteurl') . 
			'/wp-admin/profile.php">' . $user_identity . '</a>')?> 
			<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="
			<?php _e('Log out of this account','atahualpa'); ?>"><?php _e('Logout &raquo;','atahualpa'); ?></a>
			</p>

			<?php // If User is not logged in: Display the form fields "Name", "Email", "URL"
			else : ?>
			<p>
			<input class="text author" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="30" tabindex="1" />
			<label for="<?php _e('author','atahualpa'); ?>"><?php _e('Name ','atahualpa'); 
			if ($req) _e('(required)','atahualpa'); ?></label>
			</p>
			<p>
			<input class="text email" type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="30" tabindex="2" />
			<label for="<?php _e('email','atahualpa'); ?>"><?php _e('Mail (will not be published) ','atahualpa'); 
			if ($req) _e('(required)','atahualpa'); ?></label>
			</p>
			<p>
			<input class="text url" type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="30" tabindex="3" />
			<label for="<?php _e('url','atahualpa'); ?>"><?php _e('Website','atahualpa'); ?></label>
			</p>
			<?php endif; ?>
	
		<!-- Display Quicktags or allowed XHTML Tags -->
		<?php if (function_exists('lmbbox_comment_quicktags_display')) { echo "<p>"; lmbbox_comment_quicktags_display(); echo "</p>"; } 
		else { if ($bfa_ata['show_xhtml_tags'] == "Yes") { ?>
		<p class="thesetags clearfix"><?php printf(__('You can use %1$sthese HTML tags</a>','atahualpa'), '<a class="xhtmltags" href="#" onclick="return false;">'); ?></p>
		<div class="xhtml-tags"><p><code><?php echo allowed_tags(); ?></code></p></div>
		<?php } } ?>
	
		<!-- Comment Textarea -->
		<p><textarea name="comment" id="comment" rows="10" cols="10" tabindex="4"></textarea></p>
		<?php do_action('comment_form', $post->ID); ?>
		
		<!-- Submit -->
		<p><input name="submit" type="submit" class="button" id="submit" tabindex="5" value="<?php _e('Submit Comment','atahualpa'); ?>" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
		</p>
		
		</form>
		</div><!-- / respond -->
		<!-- / Comment Form -->

	<?php endif; ?>
	<!-- / If Login is not required, or User is logged in -->
	
<?php endif; ?>
<!-- If comments are open -->