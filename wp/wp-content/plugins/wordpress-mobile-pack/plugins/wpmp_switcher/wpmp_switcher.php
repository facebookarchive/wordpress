<?php

/*
$Id: wpmp_switcher.php 258240 2010-06-28 16:21:14Z jamesgpearce $

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
Plugin Name: Mobile Switcher
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: Detects a mobile user accessing your site and switches theme accordingly. This plugin is tested with WordPress 2.5, 2.6, 2.7 and 2.8.
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/

define("WPMP_SWITCHER_COOKIE_VAR", "wpmp_switcher");
define("WPMP_SWITCHER_CGI_VAR", "wpmp_switcher");
define("WPMP_SWITCHER_NO_SWITCH", 0);
define("WPMP_SWITCHER_DESKTOP_PAGE", 1);
define("WPMP_SWITCHER_MOBILE_PAGE", 2);
define("WPMP_SWITCHER_REDIRECT_TO_MOBILE", 3);
define("WPMP_SWITCHER_REDIRECT_TO_DESKTOP", 4);
define("WPMP_SWITCHER_MOBILE_INTERSTITIAL", 5);
define("WPMP_SWITCHER_DESKTOP_INTERSTITIAL", 6);

if (file_exists($theme_functions_persist = str_replace('/', DIRECTORY_SEPARATOR, get_theme_root()) . DIRECTORY_SEPARATOR . 'mobile_pack_base' . DIRECTORY_SEPARATOR . 'functions_persist.php')) {
  include_once($theme_functions_persist);
}

add_action('init', 'wpmp_switcher_init');
add_action('admin_menu', 'wpmp_switcher_admin_menu');
add_action('wp_footer', 'wpmp_switcher_wp_footer');
add_filter('stylesheet', 'wpmp_switcher_stylesheet');
add_filter('template', 'wpmp_switcher_template');
add_filter('option_home', 'wpmp_switcher_option_home_siteurl');
add_filter('option_siteurl', 'wpmp_switcher_option_home_siteurl');

if (function_exists('add_cacheaction')) {
  // WP Super Cache integration
  if (isset($GLOBALS['wp_super_cache_debug']) && $GLOBALS['wp_super_cache_debug']) {
    wp_cache_debug("Adding hook for wpmp mobile detection", 5);
  }
  add_cacheaction('wp_cache_get_cookies_values', 'wpmp_switcher_wp_cache_check_mobile');
}

function wpmp_switcher_init() {
  wp_register_sidebar_widget('wpmp_switcher_widget_link', __('Mobile Switcher Link', 'wpmp'), 'wpmp_switcher_widget_link',
    array('classname' => 'wpmp_switcher_widget_link', 'description' => __( "A link that allows users to toggle between desktop and mobile sites (when a switcher mode is enabled)", 'wpmp'))
  );
  switch($switcher_outcome = wpmp_switcher_outcome()) {
    case WPMP_SWITCHER_NO_SWITCH:
      break;
    case WPMP_SWITCHER_DESKTOP_PAGE:
      wpmp_switcher_hit('desktop');
      wpmp_switcher_set_cookie('desktop');
      break;
    case WPMP_SWITCHER_MOBILE_PAGE:
      wpmp_switcher_hit('mobile');
      wpmp_switcher_set_cookie('mobile');
      if (strpos(strtolower($_SERVER['REQUEST_URI']), '/wp-login.php')!==false) {
        wpmp_switcher_mobile_login();
      }
      if (is_admin() || strtolower(substr($_SERVER['REQUEST_URI'], -9))=='/wp-admin') {
        wpmp_switcher_mobile_admin();
      }
      break;
    case WPMP_SWITCHER_DESKTOP_INTERSTITIAL:
      wpmp_switcher_desktop_interstitial();
      break;
    case WPMP_SWITCHER_MOBILE_INTERSTITIAL:
      wpmp_switcher_mobile_interstitial();
      break;
    case WPMP_SWITCHER_REDIRECT_TO_MOBILE:
      $target_url = "http://" . wpmp_switcher_domains('mobile', true) . wpmp_switcher_current_path_plus_cgi();
      header("Location: $target_url");
      exit;
    case WPMP_SWITCHER_REDIRECT_TO_DESKTOP:
      $target_url = "http://" . wpmp_switcher_domains('desktop', true) . wpmp_switcher_current_path_plus_cgi();
      header("Location: $target_url");
      exit;
  }
  if($switcher_outcome!=WPMP_SWITCHER_NO_SWITCH) {
    remove_filter('template_redirect', 'redirect_canonical');
  }
}
function wpmp_switcher_widget_link($args) {
  extract($args);
  if(get_option('wpmp_switcher_mode')=='none') {
    return;
  }
  print $before_widget . $before_title . __('Switch site', 'wpmp') . $after_title;
  switch (wpmp_switcher_outcome()) {
    case WPMP_SWITCHER_MOBILE_PAGE:
      print "<ul><li>" . wpmp_switcher_link('desktop', __('Switch to our desktop site', 'wpmp')) . "</li></ul>";
      break;
    case WPMP_SWITCHER_DESKTOP_PAGE:
      print "<ul><li>" . wpmp_switcher_link('mobile', __('Switch to our mobile site', 'wpmp')) . "</li></ul>";
      break;
  }
  print $after_widget;
}
function wpmp_switcher_activate() {
  $default_desktop_domain=wpmp_switcher_trim_domain(get_option('home'));
  $default_desktop_domains = array();
  $default_mobile_domains = array();

  $default_desktop_domains[] = $default_desktop_domain;
  if(($tld=substr($default_desktop_domain, 0, -4))==".com" || $tld==".org" || $tld==".net") {
    $default_mobile_domains[] = substr($default_desktop_domain, 0, -4) . ".mobi";
  }
  if(substr($default_desktop_domain, 0, 4)=="www.") {
    $default_desktop_domains[] = substr($default_desktop_domain, 4);
    $default_mobile_domains[] = "m." . substr($default_desktop_domain, 4);
  } else {
    $default_mobile_domains[] = "m." . $default_desktop_domain;
  }
  $default_theme = '';
  foreach(get_themes() as $name=>$theme) {
    if ($default_theme=='') {
      $default_theme = $theme;
    }
    if(strpos(strtolower($name), 'mobile')!==false) {
      $default_theme = $theme;
      break;
    }
  }
  foreach(array(
    'wpmp_switcher_mode'=>'browser',
    'wpmp_switcher_detection'=>'simple',
    'wpmp_switcher_desktop_domains'=>implode(", ", $default_desktop_domains),
    'wpmp_switcher_mobile_domains'=>implode(", ", $default_mobile_domains),
    'wpmp_switcher_mobile_theme'=>$default_theme['Name'],
    'wpmp_switcher_mobile_theme_stylesheet'=>$default_theme['Stylesheet'],
    'wpmp_switcher_mobile_theme_template'=>$default_theme['Template'],
    'wpmp_switcher_footer_links'=>'true',
    'wpmp_switcher_hits_desktop'=>'0',
    'wpmp_switcher_hits_mobile'=>'0',
    'wpmp_switcher_hits_start'=>microtime(true),
  ) as $name=>$value) {
    if (get_option($name)=='') {
      update_option($name, $value);
    }
  }
  //fixing incorrect settings from WP1.1.2 & earlier
  foreach(array('desktop', 'mobile') as $type) {
    $domains = strtolower(get_option('wpmp_switcher_' . $type . '_domains'));
    $domains = explode(",", $domains);
    $trimmed_domains = array();
    foreach($domains as $domain) {
      $trimmed_domains[] = wpmp_switcher_trim_domain($domain);
    }
    update_option('wpmp_switcher_' . $type . '_domains', join(', ', $trimmed_domains));
  }
}

function wpmp_switcher_trim_domain($domain) {
  $trimmed_domain = trim(strtolower($domain));
  if(substr($trimmed_domain, 0, 7) == 'http://') {
    $trimmed_domain = substr($trimmed_domain, 7);
  } elseif(substr($trimmed_domain, 0, 8) == 'https://') {
    $trimmed_domain = substr($trimmed_domain, 8);
  }
  $trimmed_domain = explode("/", "$trimmed_domain/");
  $trimmed_domain = $trimmed_domain[0];
  return $trimmed_domain;
}

function wpmp_switcher_deactivate() {
}


function wpmp_switcher_admin_menu() {
	add_theme_page(__('Mobile Switcher', 'wpmp'), __('Mobile Switcher', 'wpmp'), 3, 'wpmp_switcher_admin', 'wpmp_switcher_admin');
}
function wpmp_switcher_admin() {
  if(sizeof($_POST)>0) {
    print '<div id="message" class="updated fade"><p><strong>' . wpmp_switcher_options_write() . '</strong></p></div>';
  }
  include_once('wpmp_switcher_admin.php');
}

function wpmp_switcher_wp_footer($force=false) {
  if(!$force && (get_option('wpmp_switcher_mode')=='none' || get_option('wpmp_switcher_footer_links')!='true')) {
    return;
  }
  switch (wpmp_switcher_outcome()) {
    case WPMP_SWITCHER_MOBILE_PAGE:
      print "<p>" . wpmp_switcher_link('desktop', __('Switch to our desktop site', 'wpmp')) . "</p>";
      break;
    case WPMP_SWITCHER_DESKTOP_PAGE:
      print "<p>" . wpmp_switcher_link('mobile', __('Switch to our mobile site', 'wpmp')) . "</p>";
      break;
  }
}
function wpmp_switcher_stylesheet($stylesheet) {
  switch (wpmp_switcher_outcome()) {
    case WPMP_SWITCHER_MOBILE_PAGE:
    case WPMP_SWITCHER_MOBILE_INTERSTITIAL:
      if($mobile_stylesheet = get_option('wpmp_switcher_mobile_theme_stylesheet')) {
        return $mobile_stylesheet;
      }
  }
  return $stylesheet;
}

function wpmp_switcher_template($template) {
  switch (wpmp_switcher_outcome()) {
    case WPMP_SWITCHER_MOBILE_PAGE:
    case WPMP_SWITCHER_MOBILE_INTERSTITIAL:
      if($mobile_template = get_option('wpmp_switcher_mobile_theme_template')) {
        return $mobile_template;
      }
  }
  return $template;
}

function wpmp_switcher_option_home_siteurl($value) {
  switch (wpmp_switcher_outcome()) {
    case WPMP_SWITCHER_MOBILE_PAGE:
    case WPMP_SWITCHER_MOBILE_INTERSTITIAL:
      if(($scheme = substr($value, 0, 7))=="http://" || ($scheme = substr($value, 0, 8))=="https://") {
        $path = "";
        if(sizeof($parts=(explode('/', "$value", 4)))==4) {
          $path = '/' . array_pop($parts);
        }
        if (strpos(get_option('wpmp_switcher_mode'), 'domain')!==false){
          $domain = wpmp_switcher_domains('mobile', true);
        } else {
          $domain = $_SERVER['HTTP_HOST'];
        }
        return $scheme . $domain .  $path;
      }
  }
  return $value;
}

function wpmp_switcher_hit($type='desktop') {
  $current = get_option("wpmp_switcher_hits_$type");
  if(!is_numeric($current)) {
    wpmp_switcher_hit_reset();
    $current = '0';
  }
  if(function_exists('bcadd')) {
    $next = bcadd($current, '1');
  } else {
    $next = $current + 1;
  }
  update_option("wpmp_switcher_hits_$type", $next);
}
function wpmp_switcher_hit_reset() {
  update_option("wpmp_switcher_hits_desktop", 0);
  update_option("wpmp_switcher_hits_mobile", 0);
  update_option("wpmp_switcher_hits_start", microtime(true));
}
function wpmp_switcher_hit_data() {
  $desktop = get_option("wpmp_switcher_hits_desktop");
  $mobile = get_option("wpmp_switcher_hits_mobile");
  $duration = floor(microtime(true) - get_option("wpmp_switcher_hits_start"))+1;
  return "1.$desktop.$mobile.$duration";
}
function wpmp_switcher_hit_summary() {
  $desktop = get_option("wpmp_switcher_hits_desktop");
  $desktop_text = sprintf(__ngettext('one desktop hit', '%d desktop hits', wpmp_switcher_humanize_number($desktop), 'wpmp'), $desktop);
  $mobile = get_option("wpmp_switcher_hits_mobile");
  $mobile_text = sprintf(__ngettext('one mobile hit', '%d mobile hits', wpmp_switcher_humanize_number($mobile), 'wpmp'), $mobile);
  $duration = wpmp_switcher_humanize_delta(microtime(true) - get_option("wpmp_switcher_hits_start"));
  $percentage = round(100 * $mobile / ($desktop + $mobile), 1);
  return "<strong>" . sprintf(__('%d%%  of your traffic is currently from mobile users.', 'wpmp'), $percentage) . "</strong><br />" .
          sprintf(__('You\'ve had %1$s and %2$s in the last %3$s.', 'wpmp'), $desktop_text, $mobile_text, $duration);
}

function wpmp_switcher_humanize_number($number) {
  $number = $number * 1;
  $suffix = '';
  if ($number>(1000000000000)){
    $suffix=' ' . __('trillion', 'wpmp');
    $number = $number / (1000000000000);
  } elseif ($number>(1000000000)){
    $suffix=' ' . __('billion', 'wpmp');
    $number = $number / (1000000000);
  } elseif ($number>(1000000)){
    $suffix=' ' . __('million', 'wpmp');
    $number = $number / (1000000);
  }
  return round($number, 1) . $suffix;
}

function wpmp_switcher_humanize_delta($seconds) {
  $seconds = $seconds * 1;
  $suffix = ' ' . __('seconds', 'wpmp');
  if (($seconds)>60*60*24*365*2){
    $suffix=' ' . __('years', 'wpmp');
    $seconds = round($seconds / (60*60*24*365), 1);
  } elseif ($seconds>60*60*24*30*2){
    $suffix=' ' . __('months', 'wpmp');
    $seconds = round($seconds / (60*60*24*30), 0);
  } elseif ($seconds>60*60*24*7*2){
    $suffix=' ' . __('weeks', 'wpmp');
    $seconds = round($seconds / (60*60*24*7), 1);
  } elseif ($seconds>60*60*24*2){
    $suffix=' ' . __('days', 'wpmp');
    $seconds = round($seconds / (60*60*24), 1);
  } elseif ($seconds>60*60*2){
    $suffix=' ' . __('hours', 'wpmp');
    $seconds = round($seconds / (60*60), 1);
  } elseif ($seconds>60){
    $suffix=' ' . __('minutes', 'wpmp');
    $seconds = round($seconds / 60, 1);
  } else {
    $seconds = round($seconds, 1);
  }
  return $seconds . $suffix;
}

function wpmp_switcher_outcome() {
  global $wpmp_switcher_outcome;
  if(!isset($wpmp_switcher_outcome)) {
    $switcher_mode = get_option('wpmp_switcher_mode');
    if (wpmp_switcher_domains('desktop', true) == wpmp_switcher_domains('mobile', true)) {
      $switcher_mode = "browser";
    }
    $desktop_domain = wpmp_switcher_is_domain('desktop');
    $mobile_domain = wpmp_switcher_is_domain('mobile');
    if($desktop_domain==$mobile_domain) {
      $desktop_domain=!$desktop_domain;
    }
    $desktop_browser = wpmp_switcher_is_browser('desktop');
    $mobile_browser = wpmp_switcher_is_browser('mobile');
    if($desktop_browser==$mobile_browser) {
      $desktop_browser=!$desktop_browser;
    }
    $desktop_cookie = wpmp_switcher_is_cookie('desktop');
    $mobile_cookie = wpmp_switcher_is_cookie('mobile');
    $cgi = wpmp_switcher_is_cgi_parameter_present();
    $wpmp_switcher_outcome = wpmp_switcher_outcome_process($switcher_mode, $desktop_domain, $mobile_domain, $desktop_browser, $mobile_browser, $desktop_cookie, $mobile_cookie, $cgi);
  }
  return $wpmp_switcher_outcome;
}
function wpmp_switcher_outcome_process($switcher_mode, $desktop_domain, $mobile_domain, $desktop_browser, $mobile_browser, $desktop_cookie, $mobile_cookie, $cgi) {
  switch ($switcher_mode) {
    case 'browser':
      if ($cgi=='desktop' || $desktop_cookie) {
        return WPMP_SWITCHER_DESKTOP_PAGE;
      } elseif ($cgi=='mobile' || $mobile_cookie) {
        return WPMP_SWITCHER_MOBILE_PAGE;
      }
      return $mobile_browser ? WPMP_SWITCHER_MOBILE_PAGE : WPMP_SWITCHER_DESKTOP_PAGE;
    case 'domain':
      return $mobile_domain ? WPMP_SWITCHER_MOBILE_PAGE : WPMP_SWITCHER_DESKTOP_PAGE;
    case 'browserdomain':
      if ($desktop_domain) {
        if ($desktop_browser) {
          if ($mobile_cookie && !$cgi) {
            return WPMP_SWITCHER_REDIRECT_TO_MOBILE;
          } else {
            return WPMP_SWITCHER_DESKTOP_PAGE;
          }
        } else {
          if ($cgi || $desktop_cookie) {
            return WPMP_SWITCHER_DESKTOP_PAGE;
          } else {
            if ($mobile_cookie) {
              return WPMP_SWITCHER_REDIRECT_TO_MOBILE;
            } else {
              return WPMP_SWITCHER_MOBILE_INTERSTITIAL;
            }
          }
        }
      } else {
        if ($mobile_browser) {
          if ($desktop_cookie && !$cgi) {
            return WPMP_SWITCHER_REDIRECT_TO_DESKTOP;
          } else {
            return WPMP_SWITCHER_MOBILE_PAGE;
          }
        } else {
          if ($cgi || $mobile_cookie) {
            return WPMP_SWITCHER_MOBILE_PAGE;
          } else {
            if ($desktop_cookie) {
              return WPMP_SWITCHER_REDIRECT_TO_DESKTOP;
            } else {
              return WPMP_SWITCHER_DESKTOP_INTERSTITIAL;
            }
          }
        }
      }
    default:
      return WPMP_SWITCHER_NO_SWITCH;
  }
}

function wpmp_switcher_domains($type='desktop', $first_only=false) {
  if(get_option('wpmp_switcher_mode')=='browser'){
    $type = 'desktop';
  }
  $domains = strtolower(get_option('wpmp_switcher_' . $type . '_domains'));
  $domains = explode(",", $domains);
  $trimmed_domains = array();
  foreach($domains as $domain) {
    if($first_only) {
      return wpmp_switcher_trim_domain($domain);
    }
    $trimmed_domains[] = wpmp_switcher_trim_domain($domain);
  }
  return $trimmed_domains;
}
function wpmp_switcher_is_domain($type='desktop') {
  $this_domain = strtolower($_SERVER['HTTP_HOST']);
  $domains = wpmp_switcher_domains($type);
  foreach($domains as $domain) {
    if (substr($this_domain, -strlen($domain)) == $domain) {
      return true;
    }
  }
  return false;
}

function wpmp_switcher_is_browser($type='desktop') {
  return call_user_func('wpmp_switcher_is_' . $type . '_browser');
}
function wpmp_switcher_is_desktop_browser() {
  return !wpmp_switcher_is_mobile_browser();
}
function wpmp_switcher_is_mobile_browser() {
  global $wpmp_switcher_is_mobile_browser;
  if (!isset($wpmp_switcher_is_mobile_browser)) {
    if(get_option('wpmp_switcher_detection')=='deviceatlas' &&
       function_exists('wpmp_deviceatlas_enabled') &&
       wpmp_deviceatlas_enabled()
    ) {
      $wpmp_switcher_is_mobile_browser = (wpmp_deviceatlas_property("mobileDevice")==1);
    } else {
      include_once('lite_detection.php');
      $wpmp_switcher_is_mobile_browser = lite_detection();
    }
  }
  return $wpmp_switcher_is_mobile_browser;
}
function wpmp_switcher_is_cookie($type='desktop') {
  return (isset($_COOKIE[WPMP_SWITCHER_COOKIE_VAR]) && $_COOKIE[WPMP_SWITCHER_COOKIE_VAR] == $type);
}
function wpmp_switcher_is_cgi_parameter_present() {
  if(isset($_GET[WPMP_SWITCHER_CGI_VAR])) {
    return $_GET[WPMP_SWITCHER_CGI_VAR];
  }
  return false;
}



function wpmp_switcher_link($type, $label) {
  $cookie = WPMP_SWITCHER_COOKIE_VAR . "=$type;path=/;expires=Tue, 01-01-2030 00:00:00 GMT";
  $target_url = "http://" . wpmp_switcher_domains($type, true) . wpmp_switcher_current_path_plus_cgi('', $type);
  if ($target_url) {
    return "<a onclick='document.cookie=\"$cookie\";' href='$target_url'>$label</a>";
  }
}

function wpmp_switcher_current_path_plus_cgi($path='', $type='true') {
  if($path) {
    if(strpos(strtolower($path), 'http://')===0 || strpos(strtolower($path), 'https://')===0) {
      $path = explode("/", $path, 4);
      $path = '/' . array_pop($path);
    }
  } else {
    $path = $_SERVER['REQUEST_URI'];
  }
  $path = htmlentities($path);
  foreach(array("true", "desktop", "mobile") as $t) {
    $path = str_replace(WPMP_SWITCHER_CGI_VAR . "=$t&amp;", "", $path);
    $path = str_replace(WPMP_SWITCHER_CGI_VAR . "=$t&", "", $path);
    $path = str_replace("&amp;" . WPMP_SWITCHER_CGI_VAR . "=$t", "", $path);
    $path = str_replace("&" . WPMP_SWITCHER_CGI_VAR . "=$t", "", $path);
    $path = str_replace(WPMP_SWITCHER_CGI_VAR . "=$t", "", $path);
  } //surely there's a better way
  if (strpos($path, "?") === false) {
    return $path . "?" . WPMP_SWITCHER_CGI_VAR . "=$type";
  } elseif (substr($path, -1) == "?") {
    return $path . WPMP_SWITCHER_CGI_VAR . "=$type";
  }
  return $path . "&amp;" . WPMP_SWITCHER_CGI_VAR . "=$type";
}
function wpmp_switcher_set_cookie($type) {
  setcookie(WPMP_SWITCHER_COOKIE_VAR, $type, time()+60*60*24*365, '/');
}
function wpmp_switcher_interstitial($type) {
  return call_user_func('wpmp_switcher_' . $type . '_interstitial');
}
function wpmp_switcher_desktop_interstitial() {
  add_action('template_redirect', 'wpmp_switcher_template_redirect_desktop_insterstitial');
}
function wpmp_switcher_template_redirect_desktop_insterstitial() {
	include_once('pages/desktop_interstitial.php');
	exit;
}
function wpmp_switcher_mobile_interstitial() {
  add_action('template_redirect', 'wpmp_switcher_template_redirect_mobile_insterstitial');
}
function wpmp_switcher_template_redirect_mobile_insterstitial() {
	include_once('pages/mobile_interstitial.php');
	exit;
}
function wpmp_switcher_mobile_login() {
	include_once('pages/mobile_login.php');
	exit;
}
function wpmp_switcher_mobile_admin() {
	include_once('pages/mobile_admin.php');
	exit;
}
function wpmp_switcher_options_write() {
  $message = __('Settings saved.', 'wpmp');
  foreach(array(
    'wpmp_switcher_mode'=>false,
    'wpmp_switcher_detection'=>false,
    'wpmp_switcher_desktop_domains'=>false,
    'wpmp_switcher_mobile_domains'=>false,
    'wpmp_switcher_mobile_theme'=>false,
    'wpmp_switcher_footer_links'=>true,
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
  $option = 'wpmp_switcher_mobile_theme';
  $theme_data = get_theme(get_option($option));
  if(isset($theme_data['Stylesheet']) && isset($theme_data['Template'])) {
    update_option($option . "_stylesheet", $theme_data['Stylesheet']);
    update_option($option . "_template", $theme_data['Template']);
  }
  if (strpos(get_option('wpmp_switcher_mode'), 'none')===false) {
    foreach(array('wpmp_switcher_mobile_domains', 'wpmp_switcher_desktop_domains') as $option) {
      $trimmed_domains=array();
      foreach(split(",", get_option($option)) as $domain) {
        $domain = trim($domain);
        $trimmed_domain = wpmp_switcher_trim_domain($domain);
        if ($trimmed_domain!=$domain) {
          $message = __('You must provide clean domain names without any leading or trailing syntax. We fixed them for you.', 'wpmp');
        }
        $trimmed_domains[] = $trimmed_domain;
      }
      update_option($option, join(', ', $trimmed_domains));
    }
  }

  if (get_option('wpmp_switcher_desktop_domains')=='' || get_option('wpmp_switcher_mobile_domains')=='') {
    switch(get_option('wpmp_switcher_mode')) {
      case 'domain':
        update_option('wpmp_switcher_mode', 'none');
        $message = __('You must provide both desktop and mobile domains. Switching has been disabled.', 'wpmp');
        break;
      case 'browserdomain':
        update_option('wpmp_switcher_mode', 'browser');
        $message = __('You must provide both desktop and mobile domains. Switching has been changed to browser detection only.', 'wpmp');
        break;
    }
  }
  return $message;
}

function wpmp_switcher_option($option, $onchange='') {
  switch ($option) {
    case 'wpmp_switcher_mode':
      return wpmp_switcher_option_dropdown(
        $option,
        array(
          'none'=>__('Disabled', 'wpmp'),
          'browser'=>__('Browser detection', 'wpmp'),
          'domain'=>__('Domain mapping', 'wpmp'),
          'browserdomain'=>__('BOTH: browser detection and domain mapping', 'wpmp'),
        ),
        $onchange
      );

    case 'wpmp_switcher_mobile_theme':
      return wpmp_switcher_option_themes($option);

    case 'wpmp_switcher_detection':
      $options = array('simple'=>__('User-agent prefixes', 'wpmp'));
      if(function_exists('wpmp_deviceatlas_enabled') && wpmp_deviceatlas_enabled()) {
        $options['simple']=__('SIMPLE: User-agent prefixes', 'wpmp');
        $options['deviceatlas']=__('ADVANCED: DeviceAtlas recognition', 'wpmp');
      }
      return wpmp_switcher_option_dropdown(
        $option, $options, $onchange
      );
    case 'wpmp_switcher_desktop_domains':
    case 'wpmp_switcher_mobile_domains':
      return wpmp_switcher_option_text(
        $option,
        $onchange
      );

    case 'wpmp_switcher_footer_links':
      return wpmp_switcher_option_checkbox(
        $option,
        $onchange
      );
  }
}


function wpmp_switcher_option_dropdown($option, $options, $onchange='') {
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

function wpmp_switcher_option_text($option, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '" onkeyup="' . attribute_escape($onchange) . '"';
  }
  $text = '<input type="text" id="' . $option . '" name="' . $option . '" value="' . attribute_escape(get_option($option)) . '" ' . $onchange . '/>';
  return $text;
}

function wpmp_switcher_option_checkbox($option, $onchange='') {
  if ($onchange!='') {
    $onchange = 'onchange="' . attribute_escape($onchange) . '"';
  }
  $checkbox = '<input type="checkbox" id="' . $option . '" name="' . $option . '" value="true" ' . (get_option($option)==='true'?'checked="true"':'') . ' ' . $onchange . ' />';
  return $checkbox;
}

function wpmp_switcher_option_themes($option) {
  $mobile_themes = array();
  $non_mobile_themes = array();
  foreach(get_themes() as $name=>$theme) {
    if(strpos(strtolower($name), 'mobile')!==false) {
      $mobile_themes[$name] = $name;
    } else {
      $non_mobile_themes[$name] = $name;
    }
  }
  if(sizeof($mobile_themes)>0) {
    $mobile_themes[''] = '-------';
  }
  $options = array_merge($mobile_themes, $non_mobile_themes);
  return wpmp_switcher_option_dropdown($option, $options);
}

function wpmp_switcher_desktop_theme() {
  $info = current_theme_info();
  return $info->title;
}

function wpmp_switcher_wp_cache_check_mobile( $cache_key ) {
  if (!isset($_SERVER["HTTP_USER_AGENT"])) {
    return $cache_key;
  }

  $is_mobile = wpmp_switcher_is_mobile_browser();
  $mobile_group = '';
  $wp_mobile_pack_dir = WP_CONTENT_DIR . '/plugins/wordpress-mobile-pack';
  if ($is_mobile && is_file($wp_mobile_pack_dir . '/themes/mobile_pack_base/group_detection.php')) {
    include_once($wp_mobile_pack_dir . '/themes/mobile_pack_base/group_detection.php');
    $mobile_group = group_detection();
  }
  if (isset($GLOBALS['wp_super_cache_debug']) && $GLOBALS['wp_super_cache_debug']) {
    wp_cache_debug("Lite detection says is_mobile: {$is_mobile} and group: {$mobile_group} for User-Agent: " . $_SERVER[ "HTTP_USER_AGENT" ], 5);
  }

  $new_cache_key = $cache_key . $is_mobile . $mobile_group;
  // In the worst case we return the cache_key as it came in
  return $new_cache_key;
}

?>
