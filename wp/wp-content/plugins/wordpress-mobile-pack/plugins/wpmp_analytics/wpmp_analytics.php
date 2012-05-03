<?php

/*
$Id: wpmp_switcher.php 191227 2010-01-07 20:45:22Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_switcher/wpmp_switcher.php $

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
Plugin Name: Mobile Analytics
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: Provides simple local mobile analytics and hooks to external providers
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/

add_action('init', 'wpmp_analytics_init');
add_action('admin_menu', 'wpmp_analytics_admin_menu');
add_action('wp_footer', 'wpmp_analytics_wp_footer');

function wpmp_analytics_init() {
  if(($provider_id=get_option('wpmp_analytics_provider_id'))=='') {
    return;
  }
  switch (get_option('wpmp_analytics_provider')) {
    case 'percent':
      include_once('lib/percent_mobile.php');
      break;
  }
}

function wpmp_analytics_activate() {
  foreach(array(
    'wpmp_analytics_provider'=>'percent',
    'wpmp_analytics_provider_id'=>'',
  ) as $name=>$value) {
    if (get_option($name)=='') {
      update_option($name, $value);
    }
  }
}

function wpmp_analytics_wp_footer() {
  if(($provider_id=get_option('wpmp_analytics_provider_id'))=='') {
    return;
  }
  print "<span id='wpmp_analytics'>";
  switch (get_option('wpmp_analytics_provider')) {
    case 'percent':
      percent_mobile_track($provider_id);
      break;
  }
  print "</span>";
}

function wpmp_analytics_admin_menu() {
	add_management_page(__('Mobile Analytics', 'wpmp'), __('Mobile Analytics', 'wpmp'), 3, 'wpmp_analytics_admin', 'wpmp_analytics_admin');

}
function wpmp_analytics_admin() {
  if(sizeof($_POST)>0) {
    print '<div id="message" class="updated fade"><p><strong>' . wpmp_analytics_options_write() . '</strong></p></div>';
    if(isset($_POST['wpmp_analytics_local_reset']) && $_POST['wpmp_analytics_local_reset']=='true') {
      if (wpmp_analytics_local_enabled()) {
        wpmp_switcher_hit_reset();
        print '<div id="message" class="updated fade"><p><strong>' . __('Hit counter reset.', 'wpmp') . '</strong></p></div>';
      }
    }
  }
  include_once('wpmp_analytics_admin.php');
}


function wpmp_analytics_options_write() {
  $message = __('Settings saved.', 'wpmp');
  foreach(array(
    'wpmp_analytics_provider'=>false,
    'wpmp_analytics_provider_id'=>false,
  ) as $option=>$checkbox) {
    if(isset($_POST[$option])){
      $value = $_POST[$option];
      if(!is_array($value)) {
  			$value = trim($value);
      }
			$value = stripslashes_deep($value);
      update_option($option, $value);
    } elseif ($checkbox) {
      update_option($option, 'false');
    }
  }
  return $message;
}

function wpmp_analytics_option($option, $onchange='', $class='', $style='') {
  switch ($option) {
    case 'wpmp_analytics_provider':
      return wpmp_analytics_option_dropdown(
        $option,
        array(
          'none'=>__('Disabled', 'wpmp'),
          'percent'=>__('PercentMobile', 'wpmp'),
        ),
        $onchange
      );
    case 'wpmp_analytics_provider_id':
      return wpmp_analytics_option_text(
        $option,
        $onchange,
        $class,
        $style
      );
    case 'wpmp_analytics_local_reset':
      return wpmp_analytics_option_checkbox(
        $option,
        $onchange
      );
  }
}


function wpmp_analytics_option_dropdown($option, $options, $onchange='') {
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

function wpmp_analytics_option_text($option, $onchange='', $class='', $style='') {
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

function wpmp_analytics_option_checkbox($option, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '"';
  }
  $checkbox = '<input type="checkbox" id="' . $option . '" name="' . $option . '" value="true" ' . (get_option($option)==='true'?'checked="true"':'') . ' ' . $onchange . ' />';
  return $checkbox;
}

function wpmp_analytics_local_enabled() {
  return function_exists('wpmp_switcher_hit_reset');
}
function wpmp_analytics_local_summary() {
  return wpmp_switcher_hit_summary();
}
?>
