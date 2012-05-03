<?php

/*
$Id: comments.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/themes/mobile_pack_base/comments.php $

Copyright (c) 2009 James Pearce & friends, portions mTLD Top Level Domain Limited, ribot, Forum Nokia

Online support: http://wordpress.org/extend/plugins/wordpress-mobile-pack/

This file is part of the WordPress Mobile Pack.

The WordPress Mobile Pack is Licensed under the Apache License, Version 2.0
(the "License"); you may not use this file except in compliance with the
License.

You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed
under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
CONDITIONS OF ANY KIND, either express or implied. See the License for the
specific language governing permissions and limitations under the License.
*/

?>

<?php if (basename($_SERVER['SCRIPT_FILENAME'])=='comments.php') { die(); } ?>

<?php if (!empty($post->post_password) && $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { ?>
  <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'wpmp'); ?></p>
  <?php return; ?>
<?php } ?>

<?php
  if (file_exists($wpmp_include = wpmp_theme_group_file('comments.php'))) {
    include_once($wpmp_include);
  } else {
    if ($comments) {
      print '<h3 id="comments">'; comments_number('No comments', '1 comment', '% comments' ); _e(' on this post.', 'wpmp') . '</h3>';
      wpmp_theme_comment_list($comments);
    }
    if ($post->comment_status == 'open') {
      print '<h3 id="respond">' . __('Leave a comment', 'wpmp') . '</h3>';
      wpmp_theme_comment_form($user_ID, $user_identity, $req, $comment_author, $comment_author_url, $id, $post);
    }
  }
?>


<?php
  function wpmp_theme_comment_list($comments) {
    global $comment; //ouch
    ?>
    <ol class="commentlist">
      <?php foreach ($comments as $comment) { ?>
        <li>
          <a name="#comment-<?php comment_ID($comment->comment_ID) ?>"></a>
          <p><?php comment_author_link($comment->comment_ID) ?>:</p>
          <?php if ($comment->comment_approved == '0') { ?>
            <em><?php _e('Your comment is awaiting moderation.', 'wpmp'); ?></em>
          <?php } ?>
          <p class="metadata"><?php comment_date('F jS, Y') ?> at <?php comment_time() ?> <?php edit_comment_link('Edit','',''); ?></p>
          <?php comment_text() ?>
        </li>
      <?php } ?>
    </ol>
  <?php
  }
?>

<?php
  function wpmp_theme_comment_form($user_ID, $user_identity, $req, $comment_author, $comment_author_url, $id, $post) {
    ?>
    <?php if ( get_option('comment_registration') && !$user_ID ) { ?>
      <p>
        <?php printf(__('You must be <a%s>logged in</a> to post a comment.', 'wpmp'), ' href="' . get_option('siteurl') . '/wp-login.php?redirect_to=' . get_permalink($post->ID)); ?>
      </p>
    <?php } else { ?>
      <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
        <?php if ( $user_ID ) { ?>
          <p>
            <?php _e('Logged in as', 'wpmp'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.
            <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout"><?php _e('Logout', 'wpmp'); ?></a></p>
        <?php } else { ?>
          <p>
            <label for="author"><?php _e('Name', 'wpmp'); ?> <?php if ($req) {_e("(required)", 'wpmp');} ?></label>
            <br />
            <input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" />
          </p>
          <p>
            <label for="email"><?php _e('Mail', 'wpmp'); ?> (<?php if ($req) {_e("required, but ", 'wpmp');} ?><?php _e("not published", 'wpmp'); ?>)</label>
            <br />
            <input type="text" name="email" id="email" value="<?php print empty($comment_author_email)?"":$comment_author_email; ?>" />
          </p>
          <p>
            <label for="url"><?php _e('Website', 'wpmp'); ?></label>
            <br />
            <input type="text" name="url" id="url" value="<?php print empty($comment_author_url)?"http://":$comment_author_url; ?>"/>
          </p>
        <?php } ?>
        <p>
          <label for="comment"><?php _e('Comment', 'wpmp'); ?></label>
          <br />
          <textarea name="comment" id="comment" rows="3"></textarea>
        </p>
        <p>
          <input class="button" name="submit" type="submit" id="submit" value="<?php _e('Submit comment', 'wpmp'); ?>" />
          <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
        </p>
        <?php do_action('comment_form', $post->ID); ?>
      </form>
    <?php
    }
  }
?>