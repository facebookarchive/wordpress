<?php

/*
$Id: functions.php 250849 2010-06-11 05:51:54Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/themes/mobile_pack_base/functions.php $

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

include_once('functions_persist.php');

add_action('init', 'wpmp_theme_init_in_use');
add_filter('dynamic_sidebar_params', 'wpmp_theme_dynamic_sidebar_params');
add_action('pre_get_posts', 'wpmp_theme_pre_get_posts');
add_action('the_content', 'wpmp_theme_the_content');

function wpmp_theme_group() {
  global $wpmp_theme_group;
  if(!isset($wpmp_theme_group)) {
    if (get_option('wpmp_theme_nokia_templates')=='true') {
      $wp_mobile_pack_dir = WP_CONTENT_DIR . '/plugins/wordpress-mobile-pack';
      include_once($wp_mobile_pack_dir . '/themes/mobile_pack_base/group_detection.php');
      $wpmp_theme_group = group_detection();
    } else {
      $wpmp_theme_group = '';
    }
  }
  return $wpmp_theme_group;
}

function wpmp_theme_group_file($file='index.php') {
  return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . wpmp_theme_group() . DIRECTORY_SEPARATOR . $file;
}

function wpmp_theme_group_uri() {
  return get_theme_root_uri() . "/mobile_pack_base/groups/" . wpmp_theme_group();
}

function wpmp_theme_base_style() {
  return get_theme_root_uri() . '/mobile_pack_base/style.css';
}

function wpmp_theme_init_in_use() {
  global $wp_registered_sidebars;
  if(sizeof($wp_registered_sidebars)==0) {
    $sidebars_widgets = get_option('sidebars_widgets');
    if (is_array($sidebars_widgets)) {
      foreach($sidebars_widgets as $index=>$widgets) {
        if ($index!='wp_inactive_widgets') {
          register_sidebar(array(
            'id' => $index,
            'before_widget' => '<li>',
            'after_widget' => '</li>',
            'before_title' => '<h2>',
            'after_title' => '</h2>',
          ));
        }
      }
    }
  }

  global $wp_registered_widgets;
  foreach ($wp_registered_widgets as $index=>$widget) {
    if(function_exists($function = 'wpmp_theme_widget_' . strtolower(str_replace(' ', '_', $widget['name'])))) {
      $wp_registered_widgets[$index]['callback'] = $function;
    }
  }

  global $wpmp_theme_menu_location;
  if (function_exists('get_nav_menu_locations')) {
    $wpmp_theme_menu_locations = get_nav_menu_locations();
    if (is_array($wpmp_theme_menu_locations)) {
      $wpmp_theme_menu_locations = array_keys($wpmp_theme_menu_locations);
      if (sizeof($wpmp_theme_menu_locations)>0) {
        $wpmp_theme_menu_location = $wpmp_theme_menu_locations[0];
      }
    }
  }

}

function wpmp_theme_dynamic_sidebar_params($params) {
  global $wp_registered_widgets;
  $widget = $params[0]['widget_id'];
  $widgets = get_option('wpmp_theme_widget');
  if (!is_array($widgets) || array_search($widget, $widgets)===false) {
    $wp_registered_widgets[$widget]['callback'] = 'wpmp_theme_widget_removed';
  }
  return $params;
}
function wpmp_theme_widget_removed() {
}


function wpmp_theme_pre_get_posts($wp_query) {
  $wp_query->query_vars['posts_per_page'] = get_option('wpmp_theme_post_count');
  return $wp_query;
}

function wpmp_theme_the_content($content) {
  if (is_single() || is_page()) {
    wpmp_theme_transcode_content($content);
    return $content;
  }
  if(strpos(strtolower($content), 'class="more-link"')!==false) {
    #return $content;
  }
  $content = preg_replace("/\r/Usi", " ", $content);
  $content = preg_replace("/\<\/?p[^>]*\>/Usi", " ", $content);
  $content = preg_replace("/\<\/?br[^>]*\>/Usi", " ", $content);
  $content = preg_replace("/\n+/Usi", " ", $content);
  $content = preg_replace("/[\x20\x09]+/Usi", " ", $content);
  $content = strip_tags($content);
  $content = trim($content);
  $length = get_option('wpmp_theme_teaser_length');
  $suffix = false;
  if(strlen($content)>$length) {
    $content = substr($content, 0, $length);
    $content = substr($content, 0, strrpos($content, ' ')) . "...";
    $content = balanceTags($content, true);
    global $id;
    $suffix = true;
  }
	if(($pos=strpos($content, '['))!==false) {
		$content = substr($content, 0, $pos) . ' ' . __("Read more", 'wpmp');
	}
  if (substr($content, strlen(__("Read more", 'wpmp')))==__("Read more", 'wpmp')) {
    $content = substr($content, 0, -9);
    $suffix = true;
  }
  if ($suffix) {
    $content .= '<br /><a href="'. get_permalink() . '#more-'.$id.'" class="more-link">' . __('Read more', 'wpmp') . "</a>";
  }
  return $content;
}

function wpmp_theme_transcode_content(&$content) {
  if(get_option('wpmp_theme_transcoder_remove_media')=='true' && function_exists('wpmp_transcoder_remove_media')) {
    wpmp_transcoder_remove_media($content);
  }
  if(get_option('wpmp_theme_transcoder_partition_pages')=='true' && function_exists('wpmp_transcoder_partition_pages')) {
    wpmp_transcoder_partition_pages($content);
  }
  if(get_option('wpmp_theme_transcoder_shrink_images')=='true' && function_exists('wpmp_transcoder_shrink_images')) {
    wpmp_transcoder_shrink_images($content);
  }
  if(get_option('wpmp_theme_transcoder_simplify_styling')=='true' && function_exists('wpmp_transcoder_simplify_styling')) {
    wpmp_transcoder_simplify_styling($content);
  }
}

function wpmp_theme_widget_search($args, $widget_args=1) {
  extract($args);
  print $before_widget . $before_title . __('Search Site', 'wpmp') . $after_title;
  include (TEMPLATEPATH . "/searchform.php");
  print $after_widget;
}


function wpmp_theme_widget_archives($args, $widget_args=1) {
  extract($args);
  $options = get_option('widget_archives');
  $title = empty($options['title']) ? __('Archives', 'wpmp') : $options['title'];
  print $before_widget . $before_title . $title . $after_title . "<ul>";
  ob_start();
  wp_get_archives("type=monthly&show_post_count=1");
  $html = ob_get_contents();
  ob_end_clean();
  $content = wpmp_theme_widget_trim_list($html, "<li><a href='/?archives=month'>" . __('...more months', 'wpmp') . "</a></li>");
  if($content) {
    print $content;
  } else {
    print "<li>" . __('No archives', 'wpmp') . "</li>";
  }
  print "</ul>$after_widget";
}

function wpmp_theme_widget_categories($args, $widget_args=1) {
  extract($args, EXTR_SKIP);
  if (is_numeric($widget_args)) {
    $widget_args = array('number' => $widget_args);
  }
  $widget_args = wp_parse_args($widget_args, array('number'=>-1));
  extract($widget_args, EXTR_SKIP);
  $options = get_option('widget_categories');
  if (!isset($options[$number])) { return; }
  $title = empty($options[$number]['title']) ? __('Categories', 'wpmp') : $options[$number]['title'];
  print $before_widget . $before_title . $title . $after_title . "<ul>";
  ob_start();
  wp_list_categories("orderby=name&hierarchical=0&show_count=1&title_li=0");
  $html = ob_get_contents();
  ob_end_clean();
  print wpmp_theme_widget_trim_list($html, "<li><a href='/?archives=category'>" . __('...more categories', 'wpmp') . "</a></li>");
  print "</ul>$after_widget";
}

function wpmp_theme_widget_tag_cloud($args, $widget_args=1) {
  extract($args);
  $options = get_option('widget_tag_cloud');
  $title = empty($options['title']) ? __('Tags', 'wpmp') : $options['title'];
  $tags = get_tags();
  if(sizeof($tags)>0) {
    print $before_widget . $before_title . $title . $after_title . "<ul>";
    $limit = get_option('wpmp_theme_widget_list_count');
    foreach($tags as $tag) {
      if($limit==0) {
        print "<li><a href='/?archives=tag'>" . __('...more tags', 'wpmp') . "</a>";
        break;
      }
      $limit--;
      print "<li><a href='" . get_tag_link( $tag->term_id ) . "'>$tag->name</a> ($tag->count)</li>";
    }
    print "</ul>" . $after_widget;
  }
}

function wpmp_theme_widget_recent_comments($args, $widget_args=1) {
  ob_start();
  if (function_exists('wp_widget_recent_comments')) {
    wp_widget_recent_comments($args);
  } else {
    $widget = new WP_Widget_Recent_Comments();
    $widget->display_callback($args, $widget_args);
  }
  $original = ob_get_contents();
  ob_end_clean();
  $original = str_ireplace('<ul id="recentcomments"></ul>', '<ul id="recentcomments"><li>' . __('No comments', 'wpmp') . '</li></ul>', $original);
  $original = str_ireplace("&cpage", "&amp;cpage", $original);
  print $original;
}
function wpmp_theme_widget_calendar($args, $widget_args=1) {
  ob_start();ob_start(); //funny ob stack inside old widgets
  if (function_exists('wp_widget_calendar')) {
    wp_widget_calendar($args);
  } else {
    $widget = new WP_Widget_Calendar();
    $widget->display_callback($args, $widget_args);
  }
  $original = ob_get_contents();
  ob_end_clean();
  $original = ob_get_contents() . $original;
  ob_end_clean();
  if (stripos($original, '<div id="calendar_wrap"></div>')!==false) {
    return;
  }
  preg_match_all("/(^.*)\<caption\>(.*)\<\/caption\>.*\<thead\>(.*)\<\/thead\>.*\<tfoot\>(.*)\<\/tfoot\>.*\<tbody\>(.*)\<\/tbody\>(.*$)/Usi", $original, $parts);
  print str_replace("<h2>&nbsp;</h2>", "<h2>" . __('Calendar', 'wpmp') . "</h2>", $parts[1][0]) .
        "<tr><td colspan='7'>" . $parts[2][0] . "</td></tr>" .
        $parts[3][0] .$parts[5][0] . $parts[4][0] .
        $parts[6][0];
}

function wpmp_theme_widget_rss($args, $widget_args=1) {
  ob_start();
  if (function_exists('wp_widget_rss')) {
    wp_widget_rss($args, $widget_args);
  } else {
    $widget = new WP_Widget_RSS();
    $widget->display_callback($args, $widget_args);
  }
  $html = ob_get_contents();
  ob_end_clean();
  print preg_replace("/\<img.*\>/Usi", "", $html);
}
function wpmp_theme_widget_trim_list($html, $more='') {
  $return = '';
  preg_match_all("/\<li.*\>(.*)\<\/li/Usi", $html, $parts);
  for($p = 0; sizeof($parts[1])>0 && $p < get_option('wpmp_theme_widget_list_count'); $p++) {
    $return .= "<li>" . array_shift($parts[1]) . "</li>";
  }
  if(sizeof($parts[1])>0) {
    $return .= $more;
  }
  return $return;
}
?>
