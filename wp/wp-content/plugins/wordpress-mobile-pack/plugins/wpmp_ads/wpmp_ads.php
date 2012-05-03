<?php

/*
$Id: wpmp_ads.php 258240 2010-06-28 16:21:14Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_ads/wpmp_ads.php $

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
Plugin Name: Mobile Ads
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: Provides a widget (intended to be used on a mobile theme) that displays <a href='http://www.google.com/adsense/www/mobile/' target='_blank'>Google</a> or <a href='http://www.admob.com' target='_blank'>AdMob</a> ads. This plugin is tested with WordPress 2.5, 2.6, 2.7 and 2.8.
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/


add_action('init', 'wpmp_ads_init');

function wpmp_ads_init() {
  wp_register_sidebar_widget('wpmp_ads_widget', __('Mobile Ads', 'wpmp'), 'wpmp_ads_widget',
    array('classname' => 'wpmp_ads_widget', 'description' => __( "Displays AdMob or Google mobile ads", 'wpmp'))
  );
  wp_register_widget_control('wpmp_ads_widget', __('Mobile Ads', 'wpmp'), 'wpmp_ads_widget_control');
}

function wpmp_ads_activate() {
  foreach(array(
    'wpmp_ads_title'=>__('Mobile ads', 'wpmp'),
    'wpmp_ads_provider'=>'none',
    'wpmp_ads_publisher_id'=>'',
    'wpmp_ads_desktop_disable'=>'true',
  ) as $name=>$value) {
    if (get_option($name)=='') {
      update_option($name, $value);
    }
  }
}

function wpmp_ads_deactivate() {}

function wpmp_ads_widget($args) {
  if(get_option('wpmp_ads_desktop_disable') &&
    function_exists('wpmp_switcher_outcome') &&
    wpmp_switcher_outcome() == WPMP_SWITCHER_DESKTOP_PAGE
  ) {
    return;
  }
	if (($provider = get_option('wpmp_ads_provider'))!='' && ($publisher_id = get_option('wpmp_ads_publisher_id'))!='') {
    extract($args);
    $buffer = $before_widget;
    if (($title = get_option('wpmp_ads_title'))=='') {
      $title = __("Mobile ads", 'wpmp');
    }

    //nice to see them in accordions
    $before_title = str_replace('class="collapsed"', 'class="expanded"', $before_title);
    $after_title = str_replace('style="display: none;"', 'style="display: block;"', $after_title);

    $buffer .= $before_title . $title . $after_title;
    if(strpos($provider, '_')!==false) {
      $provider = explode('_', $provider, 2);
      $format = $provider[1];
      $provider = $provider[0];
    }
    if (function_exists($function = "wpmp_ads_insertion_$provider")) {
      if(($ad =call_user_func($function, $publisher_id, $format))!='') {
        print $buffer;
        print "<ul><li>$ad</li></ul>";
      	print $after_widget;
      }
    }
	}
}

function wpmp_ads_widget_control() {
  if($_POST['wpmp_ads']) {
    wpmp_ads_widget_options_write();
  }
  include('wpmp_ads_widget_admin.php');
}

function wpmp_ads_widget_options_write() {
  foreach(array(
    'wpmp_ads_title'=>false,
    'wpmp_ads_provider'=>false,
    'wpmp_ads_publisher_id'=>false,
    'wpmp_ads_desktop_disable'=>true
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
}

function wpmp_ads_option($option, $onchange='', $class='', $style='') {
  switch ($option) {
    case 'wpmp_ads_title':
    case 'wpmp_ads_publisher_id':
      return wpmp_ads_option_text(
        $option, $onchange, $class, $style
      );

    case 'wpmp_ads_provider':
      return wpmp_ads_option_dropdown(
        $option,
        array(
          "none"=>__("None", 'wpmp'),
          "admob"=>__("AdMob", 'wpmp'),
          "google_mobile_single"=>__("Google (single ad)", 'wpmp'),
          "google_mobile_double"=>__("Google (double ads)", 'wpmp'),
        ),
        $onchange
      );

    case 'wpmp_ads_desktop_disable':
      return wpmp_ads_option_checkbox(
        $option, $onchange
      );

  }
}

function wpmp_ads_option_text($option, $onchange='', $class='', $style='') {
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
function wpmp_ads_option_dropdown($option, $options, $onchange='') {
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
function wpmp_ads_option_checkbox($option, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '"';
  }
  $checkbox = '<input type="checkbox" id="' . $option . '" name="' . $option . '" value="true" ' . (get_option($option)==='true'?'checked="true"':'') . ' ' . $onchange . ' />';
  return $checkbox;
}


function wpmp_ads_insertion_admob($publisher_id, $format='') {
  $html = '';
  $ua = urlencode(getenv("HTTP_USER_AGENT"));
  $ip = urlencode($_SERVER['REMOTE_ADDR']);
  $url = "http://ads.admob.com/ad_source.php?s=$publisher_id&u=$ua&i=$ip";
  if($ip == "127.0.0.1") {
    $url .= "&m=test";
  }
  $response = wpmp_ads_http($url);
  $link = explode("><", $response);
  if (sizeof($link) == 2) {
    $ad_text = $link[0];
    $ad_link = $link[1];
    if (isset($ad_link) && ($ad_link !='')) {
      $html .= '<a href="'. $ad_link .'">'. $ad_text . '</a>';
    }
  }
  return $html;
}
function wpmp_ads_insertion_google($publisher_id, $format='mobile_single') {
  //'color_border'=>'FFFFFF',
  //'color_bg'=>'FFFFFF',
  //'color_link'=>'333333',
  //'color_text'=>'666666',
  //'color_url'=>'333399',
  $params = array(
    'ad_type'=>'text_image',
    'channel'=>'',
    'client'=>$publisher_id,
    'format'=>$format,
    'https'=>$_SERVER['HTTPS'],
    'host'=>$_SERVER['HTTP_HOST'],
    'ip'=>$_SERVER['REMOTE_ADDR'],
    'markup'=>'xhtml',
    'oe'=>'utf8',
    'output'=>'xhtml',
    'ref'=>$_SERVER['HTTP_REFERER'],
    'url'=>$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    'useragent'=>$_SERVER['HTTP_USER_AGENT']
  );
  $screen_res = $_SERVER['HTTP_UA_PIXELS'];
  $delimiter = 'x';
  if ($screen_res == '') {
    $screen_res = $_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS'];
    $delimiter = ',';
  }
  $res_array = explode($delimiter, $screen_res);
  if (sizeof($res_array) == 2) {
    $params['u_w'] = $res_array[0];
    $params['u_h'] = $res_array[1];
  }
  $dcmguid = $_SERVER['HTTP_X_DCMGUID'];
  if ($dcmguid != '') {
    $params['dcmguid'] = $dcmguid;
  }
  $google_dt = time();
  $url = 'http://pagead2.googlesyndication.com/pagead/ads?';
  $google_scheme = ($params['https'] == 'on') ? 'https://' : 'http://';
  foreach ($params as $param => $value) {
    if ($param == 'client') {
      $url .= '&client=' . urlencode("ca-mb-$value");
    } elseif (strpos($param, 'color_') === 0) {
      $color_array = split(',', $value);
      $url .= '&' . $param . '=' . $color_array[$google_dt % sizeof($color_array)];
    } elseif ((strpos($param, 'host') === 0) || (strpos($param, 'url') === 0)) {
      $url .= '&' . $param . '=' . urlencode($google_scheme . $value);
    } else {
      $url .= '&' . $param . '=' . urlencode($value);
    }
  }
  $url .= '&dt=' . round(1000 * array_sum(explode(' ', microtime())));
  $html = wpmp_ads_http($url);
  if (substr($html, 0, 15) == "<!-- google_afm" && substr($html, -3) == "-->") {
    $html = "";
  }
  return $html;
}


function wpmp_ads_http($url) {
  $html = "";
  if($handle = @fopen($url, 'r')) {
    while (!feof($handle)) {
      $html .= fread($handle, 8192);
    }
    fclose($handle);
  } elseif ($handle = @curl_init($url)) {
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    $html = curl_exec($handle);
    curl_close($handle);
  }
  return $html;
}

?>
