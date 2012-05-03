<?php

/*
$Id: wpmp_barcode.php 258240 2010-06-28 16:21:14Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_barcode/wpmp_barcode.php $

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
Plugin Name: Mobile Barcode
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: Provides a widget (intended to be used on a desktop theme) that displays a 2D-barcode for navigating to the mobile site. This plugin is tested with WordPress 2.5, 2.6, 2.7 and 2.8.
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/


add_action('init', 'wpmp_barcode_init');

function wpmp_barcode_init() {
  wp_register_sidebar_widget('wpmp_barcode_widget', __('Mobile Barcode', 'wpmp'), 'wpmp_barcode_widget',
    array('classname' => 'wpmp_barcode_widget', 'description' => __( "A 2D-barcode used for navigating to a mobile URL", 'wpmp'))
  );
  wp_register_widget_control('wpmp_barcode_widget', __('Mobile Barcode', 'wpmp'), 'wpmp_barcode_widget_control');
}
function wpmp_barcode_activate() {
  foreach(array(
    'wpmp_barcode_title'=>__('Our mobile site', 'wpmp'),
    'wpmp_barcode_link'=>
      function_exists('wpmp_switcher_domains') ?
        "http://" . wpmp_switcher_domains('mobile', true) :
        ''
      ,
    'wpmp_barcode_size'=>'190',
    'wpmp_barcode_help'=>'true',
    'wpmp_barcode_reader_list'=>'true'
  ) as $name=>$value) {
    if (get_option($name)=='') {
      update_option($name, $value);
    }
  }
}

function wpmp_barcode_deactivate() {}

function wpmp_barcode_widget($args) {
  extract($args);
  print $before_widget;
  if (($title = get_option('wpmp_barcode_title'))=='') {
    $title = __("Our mobile site", 'wpmp');
  }
  print $before_title . $title . $after_title;
  $size = get_option('wpmp_barcode_size');
  if(!is_numeric($size) && $size < 64) {
    $size = 190;
  } else {
    $size = floor($size);
  }

	$link = get_option('wpmp_barcode_link');

	//If the user hasn't specified a URL in the widget admin panel
	if (trim($link)=='') {
		//Get the unique ID of this post http://codex.wordpress.org/Function_Reference/get_the_ID
		//We can use this to craft a shorter URL, thus making the QR code smaller and easier to scan.
		//http://example.com/?p=1234 rather than http://example.com/2010/01/05/some-title
		//Using http://codex.wordpress.org/Function_Reference/get_page
		$page_data = get_page( get_the_ID() );
		$link = $page_data->guid; //[guid] => (http://mydomain/?page_id={[ID]})

		//We need to add wpmp_switcher=true to force the mobile version if the switcher uses domain mapping.
		if (get_option('wpmp_switcher_mode') != 'browser' ) {
			$link .= "&wpmp_switcher=mobile";
		}
	}

  $url = "http://chart.apis.google.com/chart?chs=" .
         $size . "x" . $size .
         "&amp;cht=qr&amp;choe=UTF-8&amp;chl=" .
         urlencode($link);

  print "<img width='$size' height='$size' src='$url' alt='QR Code - scan to visit our mobile site' />";
  if(get_option('wpmp_barcode_help')=='true') {
    print "<p>";
    printf (__('This is a 2D-barcode containing the address of our <a %s>mobile site</a>.', 'wpmp'), "href='$link'");
    print __('If your mobile has a barcode reader, simply snap this bar code with the camera and launch the site.', 'wpmp');
    print "</p>";
  }
  if(get_option('wpmp_barcode_reader_list')=='true') {
    print "<p>";
    print __('Many companies provide barcode readers that you can install on your mobile, and all of the following are compatible with this format:', 'wpmp');
    print "</p>";
    include_once('barcode_reader_list.php');
    print "<ul>";
    foreach(wpmp_barcode_barcode_reader_list() as $name=>$url) {
      print "<li><a href='$url' target='_blank'>$name</a></li>";
    }
    print "</ul>";
  }
  print $after_widget;
}

function wpmp_barcode_widget_control() {
  if($_POST['wpmp_barcode']) {
    wpmp_barcode_widget_options_write();
  }
  include('wpmp_barcode_widget_admin.php');
}

function wpmp_barcode_widget_options_write() {
  foreach(array(
    'wpmp_barcode_title'=>false,
    'wpmp_barcode_link'=>false,
    'wpmp_barcode_size'=>false,
    'wpmp_barcode_help'=>true,
    'wpmp_barcode_reader_list'=>true,
  ) as $option=>$checkbox) {
    if(isset($_POST[$option])){
      $value = $_POST[$option];
			$value = trim($value);
			$value = stripslashes_deep($value);
      update_option($option, $value);
    } elseif ($checkbox) {
      update_option($option, 'false');
    }
  }
  if (!is_numeric(get_option('wpmp_barcode_size'))) {
    update_option('wpmp_barcode_size', '190');
  }
}

function wpmp_barcode_option($option, $onchange='', $class='', $style='') {
  switch ($option) {
    case 'wpmp_barcode_title':
    case 'wpmp_barcode_link':
    case 'wpmp_barcode_size':
      return wpmp_barcode_option_text(
        $option, $onchange, $class, $style
      );

    case 'wpmp_barcode_help':
    case 'wpmp_barcode_reader_list':
      return wpmp_barcode_option_checkbox(
        $option, $onchange
      );
  }
}

function wpmp_barcode_option_text($option, $onchange='', $class='', $style='') {
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

function wpmp_barcode_option_checkbox($option, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '"';
  }
  $checkbox = '<input type="checkbox" id="' . $option . '" name="' . $option . '" value="true" ' . (get_option($option)==='true'?'checked="true"':'') . ' ' . $onchange . ' />';
  return $checkbox;
}



?>
