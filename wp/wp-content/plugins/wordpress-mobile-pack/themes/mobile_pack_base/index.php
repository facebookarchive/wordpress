<?php

/*
$Id: index.php 237580 2010-05-04 23:32:07Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/themes/mobile_pack_base/index.php $

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

get_header();

$wpmp_title = '';
$wpmp_archives = false;
$wpmp_not_found = false;
if (isset($_GET['archives']) && ($archives = $_GET['archives'])!='') {
  $wpmp_title = __("Blog archives", 'wpmp');
  $wpmp_archives = true;
} elseif (have_posts()) {
  $post = $posts[0];
  if (is_search()) {
    $wpmp_title = __("Search results", 'wpmp');
  } elseif (is_tag()) {
    $wpmp_title = sprintf(__("Archive for the '%s' tag", 'wpmp'), single_tag_title('', false));
  } elseif (is_category()) {
    $wpmp_title = sprintf(__("Archive for the '%s'", 'wpmp'), single_cat_title('', false));
  } elseif (is_day()) {
    $wpmp_title = sprintf(__("Archive for %s", 'wpmp'), get_the_time('F jS, Y'));
  } elseif (is_month()) {
    $wpmp_title = sprintf(__("Archive for %s", 'wpmp'), get_the_time('F, Y'));
  } elseif (is_year()) {
    $wpmp_title = sprintf(__("Archive for %s", 'wpmp'), get_the_time('Y'));
  } elseif (is_author()) {
    $wpmp_title = __("Author archive", 'wpmp');
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
    $wpmp_title = __("Blog archives", 'wpmp');
  } elseif (!is_single() && !is_page()) {
    $wpmp_title = __("Recent posts", 'wpmp');
  }
} else {
  $wpmp_title = __("Page not found", 'wpmp');
  $wpmp_not_found = true;
}


print "<div id='wrapper'><div id='content'>";
if ($wpmp_title!='') {
  print "<h1>$wpmp_title</h1>";
}


if ($wpmp_not_found) {
  print "<p>" . __("Use the menu to navigate the site, or search for a keyword:", 'wpmp') . "</p>";
  include (TEMPLATEPATH . "/searchform.php");

} elseif ($wpmp_archives) {
  if ($archives=='category') {
    print "<h2>" . __("Archives by category", 'wpmp') . "</h2>";
    $links = array();
    foreach(get_categories() as $category) {
      $links[] = "<a href='" . get_category_link( $category->term_id ) . "'>$category->name</a>";
    }
    $links = implode(', ', $links);
  } elseif ($archives=='tag') {
    print "<h2>" . __("Archives by tag", 'wpmp') . "</h2>";
    $links = array();
    foreach(get_tags() as $tag) {
      $links[] = "<a href='" . get_tag_link( $tag->term_id ) . "'>$tag->name</a> ($tag->count)";
    }
    $links = implode(', ', $links);
  } elseif ($archives=='week' || $archives=='month' || $archives=='year') {
    print "<h2>" . sprintf(__("Archives by %s", 'wpmp'), $archives) . "</h2>";
    $links = " ";
    wp_get_archives(array('type'=>$archives.'ly', 'show_post_count'=>true));
  }
  if($links) {
    print "<p>$links</p>";
  } else {
    print "<p>" . __("No archives found. Use the menu to navigate the site, or search for a keyword:", 'wpmp') . "</p>";
    include (TEMPLATEPATH . "/searchform.php");
  }

} else {

  global $more;
  $more=(is_single() || is_page())?1:0;

  if (file_exists($wpmp_include = wpmp_theme_group_file('index.php'))) {
    include_once($wpmp_include);
  } else {

    while (have_posts()) {
      the_post();
      print '<div class="post" id="post-' . get_the_ID() . '">';
      if(is_single() || is_page()) {
        print '<h1>' . get_the_title() . '</h1>';
        wpmp_theme_post_single();
      } else {
        print '<h2><a href="'; the_permalink(); print '" rel="bookmark">' . get_the_title() . '</a></h2>';
        wpmp_theme_post_summary();
      }
    }
    if(!is_single() && !is_page()) {
      print '<p class="navigation">';
      next_posts_link(__('Older', 'wpmp'));
      print ' ';
      previous_posts_link(__('Newer', 'wpmp'));
      print '</p>';
    }

  }
}

function wpmp_theme_post_single() {
  wpmp_theme_post(true);
  print '<p class="metadata">'; previous_post_link(__('Previous post:', 'wpmp') . ' %link'); print '<br />'; next_post_link(__('Next post:', 'wpmp') . ' %link'); print '</p>';
  if(!function_exists('wpmp_transcoder_is_last_page') || wpmp_transcoder_is_last_page()) {
    global $post;
    if (!$post->comment_status=='open') {
      print '<p class="metadata">' . __('Comments are closed for this post.', 'wpmp') . '</p>';
      print '</div>';
    } else {
      print '</div>';
      comments_template();
    }
  }
}

function wpmp_theme_post_summary() {
  wpmp_theme_post();
  print '</div>';
}

function wpmp_theme_post($single = false) {
  global $wpmp_summary_first;
  if (!isset($wpmp_summary_first)) {
    $wpmp_summary_first=true;
  }
  $summary = get_option('wpmp_theme_post_summary');
  $metadata = get_option('wpmp_theme_post_summary_metadata')=='true';
  if ($single || $metadata) {
    print '<p class="metadata">'. get_the_time('F jS, Y') . ' by ' . get_the_author() . '</p>';
  }
  if ($single || ($summary!='none' && ($summary!='firstteaser' || $wpmp_summary_first))) {
    print '<div class="entry">';
    the_content(__('Read more', 'wpmp'));
    print '</div>';
    $wpmp_summary_first = false;
  }
  if ($single || $metadata) {
    print '<p class="metadata">' . __('Posted in ', 'wpmp');
    the_category(', ');
    edit_post_link('Edit', ' | ', '');
    print ' | ';
    comments_popup_link('No comments', '1 comment', '% comments');
    print '</p>';
  }
}

?>

  </div>
  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
