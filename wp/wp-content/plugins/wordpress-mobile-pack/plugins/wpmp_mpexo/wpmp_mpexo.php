<?php

/*
$Id: wpmp_mpexo.php 180811 2009-12-08 06:13:51Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_mpexo/wpmp_mpexo.php $

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

/*
Plugin Name: mpexo client
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: Publishes information about this blog to the mpexo directory service. This plugin is tested with WordPress 2.5, 2.6, 2.7 and 2.8.
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/

add_action('init', 'wpmp_mpexo_init', 100);
add_action('shutdown', 'wpmp_mpexo_shutdown');
add_action('admin_menu', 'wpmp_mpexo_admin_menu');

function wpmp_mpexo_add_action_options_core() {
  add_action('update_option_siteurl', 'wpmp_mpexo_update_option_siteurl', 100, 2);
  add_action('update_option_blogname', 'wpmp_mpexo_update_option_blogname', 100, 2);
  add_action('update_option_blogdescription', 'wpmp_mpexo_update_option_blogdescription', 100, 0);
  add_action('update_option_admin_email', 'wpmp_mpexo_update_option_admin_email', 100, 0);
}
wpmp_mpexo_add_action_options_core();

function wpmp_mpexo_add_action_options_mpexo() {
  add_action('update_option_wpmp_mpexo_enabled_beta', 'wpmp_mpexo_update_option_wpmp_mpexo_enabled', 100, 2);
  add_action('update_option_wpmp_mpexo_description', 'wpmp_mpexo_update_option_blogdescription', 100, 0);
  add_action('update_option_wpmp_mpexo_description_custom', 'wpmp_mpexo_update_option_blogdescription', 100, 0);
  add_action('update_option_wpmp_mpexo_email', 'wpmp_mpexo_update_option_admin_email', 100, 0);
  add_action('update_option_wpmp_mpexo_classification', 'wpmp_mpexo_update_option_wpmp_mpexo_classification', 100, 2);
  add_action('update_option_wpmp_mpexo_content', 'wpmp_mpexo_update_option_wpmp_mpexo_content', 100, 2);
  add_action('update_option_wpmp_mpexo_popularity', 'wpmp_mpexo_update_option_wpmp_mpexo_popularity', 100, 2);
  add_action('update_option_wpmp_mpexo_diagnostics', 'wpmp_mpexo_update_option_wpmp_mpexo_diagnostics', 100, 2);

  add_action('add_option_wpmp_mpexo_enabled_beta', 'wpmp_mpexo_update_option_wpmp_mpexo_enabled', 100, 2);
  add_action('add_option_wpmp_mpexo_description', 'wpmp_mpexo_update_option_blogdescription', 100, 0);
  add_action('add_option_wpmp_mpexo_description_custom', 'wpmp_mpexo_update_option_blogdescription', 100, 0);
  add_action('add_option_wpmp_mpexo_email', 'wpmp_mpexo_update_option_admin_email', 100, 0);
  add_action('add_option_wpmp_mpexo_classification', 'wpmp_mpexo_update_option_wpmp_mpexo_classification', 100, 2);
  add_action('add_option_wpmp_mpexo_content', 'wpmp_mpexo_update_option_wpmp_mpexo_content', 100, 2);
  add_action('add_option_wpmp_mpexo_popularity', 'wpmp_mpexo_update_option_wpmp_mpexo_popularity', 100, 2);
  add_action('add_option_wpmp_mpexo_diagnostics', 'wpmp_mpexo_update_option_wpmp_mpexo_diagnostics', 100, 2);
}
wpmp_mpexo_add_action_options_mpexo();

function wpmp_mpexo_add_action_options_wpmp($call_now=false) {
  foreach(array(

    'wad'=>'wpmp_ads_desktop_disable',
    'wap'=>'wpmp_ads_provider',
    'wat'=>'wpmp_ads_title',

    'wbh'=>'wpmp_barcode_help',
    'wbl'=>'wpmp_barcode_link',
    'wbr'=>'wpmp_barcode_reader_list',
    'wbs'=>'wpmp_barcode_size',
    'wbt'=>'wpmp_barcode_title',

    'wsd'=>'wpmp_switcher_desktop_domains',
    'wse'=>'wpmp_switcher_detection',
    'wsf'=>'wpmp_switcher_footer_links',
    'wsm'=>'wpmp_switcher_mobile_domains',
    'wst'=>'wpmp_switcher_mobile_theme',
    'wss'=>'wpmp_switcher_mobile_theme_stylesheet',
    'wsp'=>'wpmp_switcher_mobile_theme_template',
    'wso'=>'wpmp_switcher_mode',

    'wth'=>'wpmp_theme_home_link_in_menu',
    'wtc'=>'wpmp_theme_post_count',
    'wts'=>'wpmp_theme_post_summary',
    'wtm'=>'wpmp_theme_post_summary_metadata',
    'wtl'=>'wpmp_theme_teaser_length',
    'wto'=>'wpmp_theme_widget_list_count',
    'wtr'=>'wpmp_theme_transcoder_remove_media',
    'wtp'=>'wpmp_theme_transcoder_partition_pages',
    'wti'=>'wpmp_theme_transcoder_shrink_images',
    'wtf'=>'wpmp_theme_transcoder_simplify_styling',
    'wtn'=>'wpmp_theme_nokia_templates',

    'wpt'=>'current_theme',

  ) as $key=>$option) {
    if($call_now) {
      wpmp_mpexo_add_to_payload($key, get_option($option));
    } else {
      $function = create_function('$was,$is', "wpmp_mpexo_add_to_payload('$key', \$is);");
      add_action("update_option_$option", $function, 100, 2);
    }
  }
}
wpmp_mpexo_add_action_options_wpmp();

function wpmp_mpexo_add_action_classification() {
  add_action('created_post_tag', 'wpmp_mpexo_update_tag');
  add_action('edited_post_tag', 'wpmp_mpexo_update_tag');
  add_action('delete_post_tag', 'wpmp_mpexo_delete_tag');

  add_action('created_category', 'wpmp_mpexo_update_category');
  add_action('edited_category', 'wpmp_mpexo_update_category');
  add_action('delete_category', 'wpmp_mpexo_delete_category');
}
wpmp_mpexo_add_action_classification();

function wpmp_mpexo_add_action_content() {
  add_action('publish_post', 'wpmp_mpexo_update_post', 10, 2);
  add_action('pending_post', 'wpmp_mpexo_delete_post', 10, 2);
  add_action('draft_post', 'wpmp_mpexo_delete_post', 10, 2);
  add_action('private_post', 'wpmp_mpexo_delete_post', 10, 2);

  add_action('publish_page', 'wpmp_mpexo_update_page', 10, 2);
  add_action('pending_page', 'wpmp_mpexo_delete_page', 10, 2);
  add_action('draft_page', 'wpmp_mpexo_delete_page', 10, 2);
  add_action('private_page', 'wpmp_mpexo_delete_page', 10, 2);

  add_action('delete_post', 'wpmp_mpexo_delete_post_or_page');
}
wpmp_mpexo_add_action_content();

function wpmp_mpexo_init() {
  if (isset($_GET['wpmp_mpexo_ping'])) {
    wpmp_mpexo_ping();
  }
  global $wpmp_mpexo_payload;
  $wpmp_mpexo_payload = array();
  global $wpmp_mpexo_payload_forced;
  $wpmp_mpexo_payload_forced = array();
  wp_register_sidebar_widget('wpmp_mpexo_widget', 'mpexo', 'wpmp_mpexo_widget',
    array('classname' => 'wpmp_mpexo_widget', 'description' => __( "A widget to show mpexo links for this blog", 'wpmp'))
  );
  wp_register_widget_control('wpmp_mpexo_widget', 'mpexo', 'wpmp_mpexo_widget_control');
}

function wpmp_mpexo_ping() {
  if ($_GET['wpmp_mpexo_ping']=='verify') {
    print "OK";
  }
  exit();
}

function wpmp_mpexo_activate() {
  foreach(array(
    'wpmp_mpexo_client_key'=>uniqid('', true),
    'wpmp_mpexo_description'=>'tagline',
    'wpmp_mpexo_description_custom'=>get_option('blogdescription'),
    'wpmp_mpexo_classification'=>'both',
    'wpmp_mpexo_content'=>'both',
    'wpmp_mpexo_popularity'=>'true',
    'wpmp_mpexo_diagnostics'=>'true',
    'wpmp_mpexo_email'=>'true',
    'wpmp_mpexo_enabled_beta'=>'false',
  ) as $name=>$value) {
    if (get_option($name)=='') {
      update_option($name, $value);
    }
  }
  wpmp_mpexo_update_tag(0);
  wpmp_mpexo_update_category(0);
  wpmp_mpexo_update_post(0);
  wpmp_mpexo_update_page(0);
}

function wpmp_mpexo_add_to_payload($option, $value="", $force=false) {
  if ($force) {
    global $wpmp_mpexo_payload_forced;
    $wpmp_mpexo_payload_forced[$option] = $value;
  } else {
    global $wpmp_mpexo_payload;
    $wpmp_mpexo_payload[$option] = $value;
  }
}

function wpmp_mpexo_update_option_siteurl($was, $is) {
  wpmp_mpexo_add_to_payload('siteurl', $is);
}

function wpmp_mpexo_update_option_blogname($was, $is) {
  wpmp_mpexo_add_to_payload('blogname', $is);
}

function wpmp_mpexo_update_option_blogdescription() {
  switch (get_option('wpmp_mpexo_description')) {
    case 'tagline':
      wpmp_mpexo_add_to_payload('description', get_option('blogdescription'));
      break;
    case 'custom':
      wpmp_mpexo_add_to_payload('description', get_option('wpmp_mpexo_description_custom'));
      break;
    default:
      wpmp_mpexo_add_to_payload('description', '');
  }
}
function wpmp_mpexo_update_option_admin_email() {
  if(get_option('wpmp_mpexo_email')=='true') {
    wpmp_mpexo_add_to_payload('email', get_option('admin_email'));
  } else {
    wpmp_mpexo_add_to_payload('email', 'Unknown');
  }
}
function wpmp_mpexo_update_option_wpmp_mpexo_enabled($was, $is) {
  wpmp_mpexo_add_to_payload('wpmp_mpexo_enabled', $is, true);
  wpmp_mpexo_delete_category(0);
  wpmp_mpexo_delete_tag(0);
  wpmp_mpexo_delete_post(0);
  wpmp_mpexo_delete_page(0);
  if($is=='true') {
    wpmp_mpexo_update_option_siteurl('', get_option('siteurl'));
    wpmp_mpexo_update_option_blogname('', get_option('blogname'));
    wpmp_mpexo_update_option_blogdescription();
    wpmp_mpexo_update_option_wpmp_mpexo_classification('none', get_option('wpmp_mpexo_classification'));
    wpmp_mpexo_update_option_wpmp_mpexo_content('none', get_option('wpmp_mpexo_content'));
    wpmp_mpexo_update_option_wpmp_mpexo_popularity('', get_option('wpmp_mpexo_popularity'));
    wpmp_mpexo_update_option_wpmp_mpexo_diagnostics('', get_option('wpmp_mpexo_diagnostics'));
    wpmp_mpexo_update_option_admin_email();
  }
}
function wpmp_mpexo_update_option_wpmp_mpexo_classification($was, $is) {
  wpmp_mpexo_add_to_payload('wpmp_mpexo_classification', $is);
  if (($was=='none'||$was=='categories')&&($is=='tags'||$is=='both')) {
    wpmp_mpexo_delete_tag(0);
    wpmp_mpexo_update_tag(0);
  }
  if (($was=='tags'||$was=='both')&&($is=='none'||$is=='categories')) {
    wpmp_mpexo_delete_tag(0);
  }
  if (($was=='none'||$was=='tags')&&($is=='categories'||$is=='both')) {
    wpmp_mpexo_delete_category(0);
    wpmp_mpexo_update_category(0);
  }
  if (($was=='categories'||$was=='both')&&($is=='none'||$is=='tags')) {
    wpmp_mpexo_delete_category(0);
  }
}
function wpmp_mpexo_update_option_wpmp_mpexo_content($was, $is) {
  wpmp_mpexo_add_to_payload('wpmp_mpexo_content', $is);
  if (($was=='none'||$was=='posts')&&($is=='pages'||$is=='both')) {
    wpmp_mpexo_delete_page(0);
    wpmp_mpexo_update_page(0);
  }
  if (($was=='pages'||$was=='both')&&($is=='none'||$is=='posts')) {
    wpmp_mpexo_delete_page(0);
  }
  if (($was=='none'||$was=='pages')&&($is=='posts'||$is=='both')) {
    wpmp_mpexo_delete_post(0);
    wpmp_mpexo_update_post(0);
  }
  if (($was=='posts'||$was=='both')&&($is=='none'||$is=='pages')) {
    wpmp_mpexo_delete_post(0);
  }
}
function wpmp_mpexo_update_option_wpmp_mpexo_popularity($was, $is) {
  wpmp_mpexo_add_to_payload('wpmp_mpexo_popularity', $is);
  if($is=='true' && function_exists('wpmp_switcher_hit_data')) {
    wpmp_mpexo_add_to_payload('wpmp_mpexo_popularity_data', wpmp_switcher_hit_data());
  }
}
function wpmp_mpexo_update_option_wpmp_mpexo_diagnostics($was, $is) {
  wpmp_mpexo_add_to_payload('wpmp_mpexo_diagnostics', $is);
  if($is=='true') {
    wpmp_mpexo_add_action_options_wpmp(true);
  }
}
function wpmp_mpexo_update_tag($tag_id) {
  if(get_option('wpmp_mpexo_classification')=='tags' || get_option('wpmp_mpexo_classification')=='both') {
    if ($tag_id==0) {
      foreach(get_tags('hide_empty=0') as $tag) {
        wpmp_mpexo_add_to_payload('t'.$tag->term_id, $tag->name);
      }
      wpmp_mpexo_update_post(0, null, 'tags');
    } else {
      $tag = get_tag($tag_id);
      wpmp_mpexo_add_to_payload('t'.$tag_id, $tag->name);
    }
  }
}

function wpmp_mpexo_delete_tag($tag_id) {
  if(get_option('wpmp_mpexo_classification')=='tags' || get_option('wpmp_mpexo_classification')=='both') {
    wpmp_mpexo_add_to_payload("td$tag_id");
  }
  if($tag_id==0) {
    wpmp_mpexo_add_to_payload("td0", "", true);
  }
}

function wpmp_mpexo_update_category($category_id) {
  if(get_option('wpmp_mpexo_classification')=='categories' || get_option('wpmp_mpexo_classification')=='both') {
    if ($category_id==0) {
      foreach(get_categories('hide_empty=0') as $category) {
        wpmp_mpexo_add_to_payload('c'.$category->term_id, $category->name);
      }
      wpmp_mpexo_update_post(0, null, 'categories');
    } else {
      $category = get_category($category_id);
      wpmp_mpexo_add_to_payload('c'.$category_id, $category->name);
    }
  }
}

function wpmp_mpexo_delete_category($category_id) {
  if(get_option('wpmp_mpexo_classification')=='categories' || get_option('wpmp_mpexo_classification')=='both') {
    wpmp_mpexo_add_to_payload("cd$category_id");
  }
  if($category_id==0) {
    wpmp_mpexo_add_to_payload("cd0", "", true);
  }
}

function wpmp_mpexo_update_post($post_id, $post=null, $just_classifications='') {
  if(get_option('wpmp_mpexo_content')=='posts' || get_option('wpmp_mpexo_content')=='both') {
    if ($post_id==0) {
      if(!($post_count=get_option('wpmp_theme_post_count'))) {
        $post_count = 5;
      }
      foreach(get_posts(array(
        'post_type'=>'post',
        'numberposts'=>$post_count
      )) as $post) {
        wpmp_mpexo_update_single_post_or_page($post, true, $just_classifications);
      }
    } else {
      wpmp_mpexo_update_single_post_or_page($post, true, $just_classifications);
    }
  }
}

function wpmp_mpexo_delete_post($post_id) {
  if(get_option('wpmp_mpexo_content')=='posts' || get_option('wpmp_mpexo_content')=='both') {
    wpmp_mpexo_add_to_payload("pd$post_id");
  }
  if($post_id==0) {
    wpmp_mpexo_add_to_payload("pd0", "", true);
  }
}

function wpmp_mpexo_update_page($page_id, $page=null) {
  if(get_option('wpmp_mpexo_content')=='pages' || get_option('wpmp_mpexo_content')=='both') {
    if ($page_id==0) {
      foreach(get_posts(array(
        'post_type'=>'page',
        'numberposts'=>0
      )) as $page) {
        wpmp_mpexo_update_single_post_or_page($page, false);
      }
    } else {
      wpmp_mpexo_update_single_post_or_page($page, false);
    }
  }
}

function wpmp_mpexo_delete_page($page_id) {
  if(get_option('wpmp_mpexo_content')=='pages' || get_option('wpmp_mpexo_content')=='both') {
    wpmp_mpexo_add_to_payload("gd$page_id");
  }
  if($page_id==0) {
    wpmp_mpexo_add_to_payload("gd0", "", true);
  }
}

function wpmp_mpexo_update_single_post_or_page($post_or_page, $is_post=true, $just_classifications=false) {
  $prefix = $is_post ? 'p' : 'g';
  if ($just_classifications=='') {
    wpmp_mpexo_add_to_payload($prefix.$post_or_page->ID, $post_or_page->post_title);
  }
  if($is_post) {
    if($just_classifications!='tags' && (get_option('wpmp_mpexo_classification')=='categories' || get_option('wpmp_mpexo_classification')=='both')) {
      $categories = array();
      foreach(wp_get_post_categories($post_or_page->ID) as $category) {
        $categories[]=$category;
      }
      wpmp_mpexo_add_to_payload("$prefix{$post_or_page->ID}c", join('.', $categories));
    }
    if($just_classifications!='categories' && (get_option('wpmp_mpexo_classification')=='tags' || get_option('wpmp_mpexo_classification')=='both')) {
      $tags = array();
      foreach(wp_get_post_tags($post_or_page->ID, array('fields'=>'ids')) as $tag) {
        $tags[]=$tag;
      }
      wpmp_mpexo_add_to_payload("$prefix{$post_or_page->ID}t", join('.', $tags));
    }
  }
}

function wpmp_mpexo_delete_post_or_page($post_or_page_id) {
  $post_or_page = get_post($post_or_page_id);
  switch($post_or_page->post_type) {
    case 'post':
      wpmp_mpexo_delete_post($post_or_page_id);
      break;
    case 'page':
      wpmp_mpexo_delete_page($post_or_page_id);
      break;
  }
}



function wpmp_mpexo_top_tags() {
  $top_tags = array();
  if (($o=get_option('wpmp_mpexo_classification'))=='tags' || $o=='both') {
    foreach(get_tags(array('hide_empty'=>0, 'orderby'=>'count', 'order'=>'DESC', 'number'=>5)) as $tag) {
      $top_tags[] = $tag->term_id;
    }
  }
  wpmp_mpexo_add_to_payload('tt', join('.', $top_tags));
}
function wpmp_mpexo_top_categories() {
  $top_categories = array();
  if (($o=get_option('wpmp_mpexo_classification'))=='categories' || $o=='both') {
    foreach(get_categories(array('hide_empty'=>0, 'orderby'=>'count', 'order'=>'DESC', 'number'=>5)) as $category) {
      $top_categories[] = $category->term_id;
    }
  }
  wpmp_mpexo_add_to_payload('ct', join('.', $top_categories));
}

function wpmp_mpexo_shutdown() {
  return wpmp_mpexo_send_payload();
}

function wpmp_mpexo_send_payload() {
  global $wpmp_mpexo_payload;
  global $wpmp_mpexo_payload_forced;
  if (get_option('wpmp_mpexo_enabled_beta')!='true') {
    $wpmp_mpexo_payload = $wpmp_mpexo_payload_forced;
  } else {
    if (!is_array($wpmp_mpexo_payload) || !is_array($wpmp_mpexo_payload_forced)) {
      $wpmp_mpexo_payload = array();
      $wpmp_mpexo_payload_forced = array();
    }
    $wpmp_mpexo_payload = array_merge($wpmp_mpexo_payload, $wpmp_mpexo_payload_forced);
  }
  $wpmp_mpexo_payload_forced = array();
  if (sizeof($wpmp_mpexo_payload)==0) {
    return true;
  }

  if (get_option('wpmp_mpexo_client_key')=='') {
     update_option('wpmp_mpexo_client_key', uniqid('', true)); //to be sure
  }

  $wpmp_mpexo_payload['siteurl'] = get_option('siteurl');
  $wpmp_mpexo_payload['wpmp_mpexo_client_key'] = get_option('wpmp_mpexo_client_key');
  $wpmp_mpexo_payload['wpmp_mpexo_server_key'] = get_option('wpmp_mpexo_server_key');


  if (get_option('wpmp_mpexo_enabled_beta')=='true') {
    wpmp_mpexo_top_tags();
    wpmp_mpexo_top_categories();
  }
  $wpmp_mpexo_payload['wpmp_mpexo_wp_version'] = $GLOBALS['wp_version'];
  $wpmp_mpexo_payload['wpmp_mpexo_wpmp_version'] = WPMP_VERSION;

  $url = 'http://www.mpexo.com/api';
  if(substr(get_option('siteurl'), -10)=='_dev.local') {
    $url = 'http://localhost:8081/api';
  }

  $query_aliases = array(
    'siteurl'=>'u',
    'wpmp_mpexo_client_key'=>'c',
    'wpmp_mpexo_server_key'=>'s',
  );

  $post_aliases = array(

    'blogname'=>'nn',
    'description'=>'nd',
    'email'=>'ne',

    'wpmp_mpexo_wp_version'=>'vw',
    'wpmp_mpexo_wpmp_version'=>'vp',

    'wpmp_mpexo_enabled'=>'me',
    'wpmp_mpexo_classification'=>'mc',
    'wpmp_mpexo_content'=>'mo',
    'wpmp_mpexo_popularity'=>'mp',
    'wpmp_mpexo_diagnostics'=>'mi',

    'wpmp_mpexo_popularity_data'=>'pd',

  );

  $query = array();
  $post = array();
  foreach($wpmp_mpexo_payload as $key=>$value) {
    $value = urlencode($value);
    if (isset($query_aliases[$key])) {
      $key = $query_aliases[$key];
      $query[] = ("$key=$value");
    } elseif (isset($post_aliases[$key])) {
      $key = $post_aliases[$key];
      $post[] = ("$key=$value");
    } else {
      $post[] = ("$key=$value");
    }
  }
  $wpmp_mpexo_payload = array();

  $url .= '?q=' . (sizeof($query) + sizeof($post)) . '&' . join('&', $query);
  $body = join('&', $post);
  $response = "";

  if($handle = @fopen($url, 'r', false, @stream_context_create(array(
    'http'=>array(
      'method' => 'POST',
      'content' => $body
    )
  )))) {
    while (!feof($handle)) {
      $response .= fread($handle, 8192);
    }
    fclose($handle);
  } elseif ($handle = @curl_init($url)) {
    $query[] = 'curl=1';
    $url .= '?q=' . (sizeof($query) + sizeof($post)) . '&' . join('&', $query);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($handle, CURLOPT_POST, TRUE);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
    $response = curl_exec($handle);
    curl_close($handle);
  }
  if($response=='OK') {
    return true;
  }
  if(substr($response, 0, 2)=='s=' && get_option('wpmp_mpexo_server_key')=='') {
    update_option('wpmp_mpexo_server_key', substr($response, 2));
    return true;
  }
  return false;
}




function wpmp_mpexo_admin_menu() {
  $state = '';
  if (get_option('wpmp_mpexo_enabled_beta')!='true') {
    $state = ' (disabled)';
  }
  if(sizeof($_POST)>0 && $_POST['wpmp_mpexo_enabled_beta']=='true') {
    $state = '';
  }
  add_options_page(__('mpexo', 'wpmp'), __("mpexo$state", 'wpmp'), 3, 'wpmp_mpexo_admin', 'wpmp_mpexo_admin');
}

function wpmp_mpexo_widget($args) {
  extract($args);
  print $before_widget;
  print $before_title . 'mpexo' . $after_title;
  print "<ul><li>";
  printf (__('This site is proudly listed as a mobile blog on <a%s>mpexo</a>.', 'wpmp'), ' href="http://www.mpexo.com"');
  print "</li></ul>";
  print $after_widget;
}

function wpmp_mpexo_widget_control() {
  if($_POST['wpmp_mpexo']) {
    wpmp_mpexo_options_write();
  }
  include('wpmp_mpexo_widget_admin.php');
}

function wpmp_mpexo_admin() {
  if(sizeof($_POST)>0) {
    print '<div id="message" class="updated fade"><p><strong>' . wpmp_mpexo_options_write() . '</strong></p></div>';
  }
  include_once('wpmp_mpexo_admin.php');
}

function wpmp_mpexo_options_write() {
  foreach(array(
    'wpmp_mpexo_enabled_beta'=>true,
    'wpmp_mpexo_description'=>false,
    'wpmp_mpexo_description_custom'=>false,
    'wpmp_mpexo_classification'=>false,
    'wpmp_mpexo_content'=>false,
    'wpmp_mpexo_popularity'=>true,
    'wpmp_mpexo_diagnostics'=>true,
    'wpmp_mpexo_email'=>true,
  ) as $option=>$checkbox) {
    if(isset($_POST[$option])){
      $value = $_POST[$option];
			$value = trim($value);
			$value = stripslashes_deep($value);
    } elseif ($checkbox) {
      $value = 'false';
    }
    update_option($option, $value);
  }
  if(wpmp_mpexo_shutdown()) {
    return __('Settings saved.', 'wpmp');
  }
  return __('<strong>Communications error:</strong> these settings have been saved locally and will be transmitted to mpexo when resubmitted.', 'wpmp');
}

function wpmp_mpexo_option($option, $onchange='', $class='', $style='') {
  switch ($option) {

    case 'wpmp_mpexo_description':
      return wpmp_mpexo_option_dropdown(
        $option,
        array(
          'none'=>__('None', 'wpmp'),
          'tagline'=>__('Tagline', 'wpmp'),
          'custom'=>__('Custom', 'wpmp'),
        ),
        $onchange
      );
    case 'wpmp_mpexo_classification':
      return wpmp_mpexo_option_dropdown(
        $option,
        array(
          'none'=>__('None', 'wpmp'),
          'tags'=>__('Tags only', 'wpmp'),
          'categories'=>__('Categories only', 'wpmp'),
          'both'=>__('Tags and Categories', 'wpmp'),
        ),
        $onchange
      );
    case 'wpmp_mpexo_content':
      return wpmp_mpexo_option_dropdown(
        $option,
        array(
          'none'=>__('None', 'wpmp'),
          'posts'=>__('Posts only', 'wpmp'),
          'pages'=>__('Pages only', 'wpmp'),
          'both'=>__('Posts and Pages', 'wpmp'),
        ),
        $onchange
      );

    case 'wpmp_mpexo_description_custom':
      return wpmp_mpexo_option_text(
        $option, $onchange, $class, $style
      );

    case 'wpmp_mpexo_enabled_beta':
    case 'wpmp_mpexo_popularity':
    case 'wpmp_mpexo_diagnostics':
    case 'wpmp_mpexo_email':
      return wpmp_mpexo_option_checkbox(
        $option, $onchange
      );
  }
}

function wpmp_mpexo_option_dropdown($option, $options, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '" onkeyup="' . attribute_escape($onchange) . '"';
  }
  $dropdown = "<select id='$option' name='$option' $onchange>";
  foreach($options as $value=>$description) {
    if(get_option($option)==$value) {
      $selected = ' selected="true"';
    } else {
      $selected = '';
    }
    $dropdown .= '<option value="' . attribute_escape($value) . '"' . $selected . '>' . __($description, 'wpmp') . '</option>';
  }
  $dropdown .= "</select>";
  return $dropdown;
}


function wpmp_mpexo_option_text($option, $onchange='', $class='', $style='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '" onkeyup="' . attribute_escape($onchange) . '"';
  }
  if ($class!='') {
    $class = 'class="' . attribute_escape($class) . '"';
  }
  if ($style!='') {
    $style = 'style="' . attribute_escape($style) . '"';
  }
  $text = '<input type="text" id="' . $option . '" name="' . $option . '" value="' . attribute_escape(get_option($option)) . '" ' . $onchange . ' ' . $class . ' ' . $style . '/>';
  return $text;
}

function wpmp_mpexo_option_checkbox($option, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '"';
  }
  $checkbox = '<input type="checkbox" id="' . $option . '" name="' . $option . '" value="true" ' . (get_option($option)==='true'?'checked="true"':'') . ' ' . $onchange . ' />';
  return $checkbox;
}



?>
