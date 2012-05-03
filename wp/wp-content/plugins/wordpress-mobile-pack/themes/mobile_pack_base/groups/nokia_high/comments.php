<?php

/*
$Id: header.php 132044 2009-07-05 06:26:08Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/themes/mobile_pack_base/header.php $

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

  if ($comments || $post->comment_status == 'open') {
    print '<dl id="accordion_comments" class="list-accordion">';
    print '<script type="text/javascript">addEvent("onload", function() {var accordion_comments = new AccordionList("accordion_comments");});</script>';
    if ($comments) {
      print '<dt class="collapsed" id="comments"><span></span>'; comments_number('No comments', '1 comment', '% comments' ); print __(' on this post.', 'wpmp') . '</dt>';
      print "<dd>"; wpmp_theme_comment_list($comments); print '</dd>';
    }
    if ($post->comment_status == 'open') {
      print '<dt class="collapsed" id="respond"><span></span>' . __('Leave a comment', 'wpmp') . '</dt>';
      print "<dd>"; wpmp_theme_comment_form($user_ID, $user_identity, $req, $comment_author, $comment_author_url, $id, $post); print '</dd>';
    }
    print '</dl>';
  }

?>