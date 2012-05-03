<?php

/*
$Id: wordpress-mobile-pack.php 258240 2010-06-28 16:21:14Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/wordpress-mobile-pack.php $

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
Plugin Name: WordPress Mobile Pack
Plugin URI: http://wordpress.org/extend/plugins/wordpress-mobile-pack/
Description: <strong>The WordPress Mobile Pack is a complete toolkit to help mobilize your WordPress site and blog.</strong> It includes a <a href='themes.php?page=wpmp_switcher_admin'>mobile switcher</a>, <a href='themes.php?page=wpmp_theme_widget_admin'>filtered widgets</a>, and content adaptation for mobile device characteristics. Activating this plugin will also install a selection of mobile <a href='themes.php?page=wpmp_theme_theme_admin'>themes</a> by <a href='http://ribot.co.uk'>ribot</a>, a top UK mobile design team, and Forum Nokia. These adapt to different families of devices, such as Nokia and WebKit browsers (including Android, iPhone and Palm). If <a href='options-general.php?page=wpmp_mpexo_admin'>enabled</a>, your site will be listed on <a href='http://www.mpexo.com'>mpexo</a>, a directory of mobile-friendly blogs. Also check out <a href='http://wordpress.org/extend/plugins/wordpress-mobile-pack/' target='_blank'>the documentation</a> and <a href='http://www.wordpress.org/tags/wordpress-mobile-pack' target='_blank'>the forums</a>. If you like the plugin, please rate us on the <a href='http://wordpress.org/extend/plugins/wordpress-mobile-pack/'>WordPress directory</a>. And if you don't, let us know how we can improve it!
Version: 1.2.4
Author: James Pearce & friends
Author URI: http://www.assembla.com/spaces/wordpress-mobile-pack
*/

define('WPMP_VERSION', '1.2.4');

// you could disable sub-plugins here
global $wpmp_plugins;
$wpmp_plugins = array(
  "wpmp_switcher",
  "wpmp_barcode",
  "wpmp_ads",
  "wpmp_deviceatlas",
  "wpmp_transcoder",
  "wpmp_analytics",
  "wpmp_mpexo",
);

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL')) {
  define('WP_CONTENT_URL', get_option('siteurl' . '/wp-content'));
}
if (!defined('WP_CONTENT_DIR')) {
  define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
if (!defined('WP_PLUGIN_URL')) {
  define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
}
if (!defined('WP_PLUGIN_DIR')) {
  define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}

if(!$warning=get_option('wpmp_warning')) {
  foreach($wpmp_plugins as $wpmp_plugin) {
    if (file_exists($wpmp_plugin_file = dirname(__FILE__) . "/plugins/$wpmp_plugin/$wpmp_plugin.php")) {
      include_once($wpmp_plugin_file);
    }
  }
}

register_activation_hook('wordpress-mobile-pack/wordpress-mobile-pack.php', 'wordpress_mobile_pack_activate');
register_deactivation_hook('wordpress-mobile-pack/wordpress-mobile-pack.php', 'wordpress_mobile_pack_deactivate');

add_action('init', 'wordpress_mobile_pack_init');
add_action('admin_notices', 'wordpress_mobile_pack_admin_notices');
add_action('admin_menu', 'wordpress_mobile_pack_admin_menu');
add_action('send_headers', 'wordpress_mobile_pack_send_headers');
add_filter('get_the_generator_xhtml', 'wordpress_mobile_pack_generator');
add_filter('get_the_generator_html', 'wordpress_mobile_pack_generator');

add_filter('plugin_action_links', 'wordpress_mobile_pack_plugin_action_links', 10, 3);


function wordpress_mobile_pack_init() {
  $plugin_dir = basename(dirname(__FILE__));
  load_plugin_textdomain('wpmp', 'wp-content/plugins/wordpress-mobile-pack', 'wordpress-mobile-pack');
}


function wordpress_mobile_pack_send_headers($wp) {
  @header("X-Mobilized-By: WordPress Mobile Pack " . WPMP_VERSION);
}
function wordpress_mobile_pack_generator($generator) {
  return '<meta name="generator" content="WordPress ' . get_bloginfo( 'version' ) . ', fitted with the WordPress Mobile Pack ' . WPMP_VERSION . '" />';
}


function wordpress_mobile_pack_plugin_action_links($action_links, $plugin_file, $plugin_info) {
  $this_file = basename(__FILE__);
  if(substr($plugin_file, -strlen($this_file))==$this_file) {
    $new_action_links = array(
      "<a href='themes.php?page=wpmp_switcher_admin'>Switcher</a>",
      "<a href='themes.php?page=wpmp_theme_theme_admin'>Themes</a> ",
      "<br /><a href='themes.php?page=wpmp_theme_widget_admin'>Widgets</a>",
      "<a href='edit.php?page=wpmp_analytics_admin'>Analytics</a> ",
      "<br /><a href='options-general.php?page=wpmp_mpexo_admin'>mpexo</a>",
    );
    foreach($action_links as $action_link) {
      if (stripos($action_link, '>Edit<')===false) {
        if (stripos($action_link, '>Deactivate<')!==false) {
          #$new_action_links[] = '<br />' . $action_link;
          $new_action_links[] = $action_link;
        } else {
          $new_action_links[] = $action_link;
        }
      }
    }
    return $new_action_links;
  }
  return $action_links;
}

function wordpress_mobile_pack_admin_notices() {
  if($warning=get_option('wpmp_warning')) {
    print "<div class='error'><p><strong style='color:#770000'>";
    print __("Critical WordPress Mobile Pack Issue", 'wpmp');
    print "</strong></p><p>$warning</p><p><small>(";
    print __('Deactivate and re-activate the WordPress Mobile Pack once resolved.', 'wpmp');
    print ")</small></p></div>";
  }
  if($flash=get_option('wpmp_flash')) {
    print "<div class='error'><p><strong style='color:#770000'>";
    print __('Important WordPress Mobile Pack Notice', 'wpmp');
    print "</strong></p><p>$flash</p></div>";
    update_option('wpmp_flash', '');
  }
}

function wordpress_mobile_pack_admin_menu() {
  if (isset($_POST['wordpress_mobile_pack_force_copy_theme'])){  //user has forced theme upgrade
    update_option('wpmp_warning', '');
    update_option('wpmp_flash', '');
    wordpress_mobile_pack_directory_copy_themes(dirname(__FILE__) . "/themes", get_theme_root(), false);
    wp_redirect('plugins.php');
    #$redirect = explode("?", $_SERVER['REQUEST_URI']);
    #wp_redirect($redirect[0]);
  }
}

function wordpress_mobile_pack_activate() {
  update_option('wpmp_warning', '');
  update_option('wpmp_flash', '');
  if (wordpress_mobile_pack_readiness_audit()) {
    wordpress_mobile_pack_directory_copy_themes(dirname(__FILE__) . "/themes", get_theme_root());
    wordpress_mobile_pack_hook('activate');
  }
}

function wordpress_mobile_pack_readiness_audit() {
  $ready = true;
  $why_not = array();

  if (version_compare(PHP_VERSION, '6.0.0', '>=')) {
    $ready = false;
    $why_not[] = '<strong>' . __('PHP version not supported.', 'wpmp') . '</strong> ' . sprintf(__('PHP versions 6 and greater are not yet supported by this plugin, and you have version %s', 'wpmp'), PHP_VERSION);
  }

  $cache_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'wpmp_transcoder' . DIRECTORY_SEPARATOR . 'c';
  $cache_does = '';
  if (!file_exists($cache_dir)) {
  	$cache_does = __("That directory does not exist.", 'wpmp');
  } elseif (!is_writable($cache_dir)) {
  	$cache_does = __("That directory is not writable.", 'wpmp');
  } elseif (!is_executable($cache_dir) && DIRECTORY_SEPARATOR=='/') {
  	$cache_does = __("That directory is not executable.", 'wpmp');
  }
  if($cache_does!='') {
    $ready = false;
    $why_not[] = sprintf(__('<strong>Not able to cache images</strong> to %s.', 'wpmp'), $cache_dir) . ' ' . $cache_does . ' ' . __('Please ensure that the web server has write- and execute-access to it.', 'wpmp');
  }

  $theme_dir = str_replace('/', DIRECTORY_SEPARATOR, get_theme_root());
  $theme_does = '';
  if (!file_exists($theme_dir)) {
  	$theme_does = __("That directory does not exist.", 'wpmp');
  } elseif (!is_writable($theme_dir)) {
  	$theme_does = __("That directory is not writable.", 'wpmp');
  } elseif (!is_executable($theme_dir) && DIRECTORY_SEPARATOR=='/') {
  	$theme_does = __("That directory is not executable.", 'wpmp');
  }
  if($theme_does!='') {
    $ready = false;
    $why_not[] = sprintf(__('<strong>Not able to install theme files</strong> to %s.', 'wpmp'), $theme_dir) . ' ' . $theme_does . ' ' . __('Please ensure that the web server has write- and execute-access to it.', 'wpmp');
  }

  if (!$ready) {
    update_option('wpmp_warning', join("<hr />", $why_not));
  }
  return $ready;
}


function wordpress_mobile_pack_directory_copy_themes($source_dir, $destination_dir, $benign=true) {
  if(file_exists($destination_dir)) {
  	$dir_does = '';
	  if (!is_writable($destination_dir)) {
	  	$dir_does = "That directory is not writable.";
	  } elseif (!is_executable($destination_dir) && DIRECTORY_SEPARATOR=='/') {
	  	$dir_does = "That directory is not executable.";
	  }
	  if($dir_does!='') {
      update_option('wpmp_warning', sprintf(__('<strong>Could not install theme files</strong> to ', 'wpmp'), $destination_dir) . ' ' . $dir_does . ' ' . __('Please ensure that the web server has write- and execute-access to it.', 'wpmp'));
      return;
    }
  } elseif (!is_dir($destination_dir)) {
    if ($destination_dir[0] != ".") {
	    mkdir($destination_dir);
	  }
  }

  $dir_handle = opendir($source_dir);
  while($source_file = readdir($dir_handle)) {
    if ($source_file[0] == ".") {
      continue;
    }
    if (file_exists($destination_child = "$destination_dir/$source_file") && $benign) {
      update_option('wpmp_flash',
                    __("<strong>Existing Mobile Pack theme files were found</strong>, but they were not overwritten by the plugin activation.", 'wpmp') .
                    "</p><p>" .
                    sprintf(__("You are advised to upgrade your Mobile Pack theme files to version %s", 'wpmp'), WPMP_VERSION) .
                    "</p><p>" .
                    __("(<strong>NB</strong>: take precautions if you have manually edited any existing Mobile Pack theme files - your changes will now need to be re-applied.)", 'wpmp') .
                    "</p><br /><form method='post' action='" . $_SERVER['REQUEST_URI'] . "'>".
                    "<input type='submit' name='wordpress_mobile_pack_force_copy_theme' value='" .
                    __('Yes, please - upgrade all my themes for me (recommended)', 'wpmp') .
                    "' />&nbsp;&nbsp;".
                    "<input type='submit' value='" .
                    __('No, thanks - leave my themes as they are', 'wpmp') .
                    "' />".
                    "</form><p>");
      continue;
    }
    if (is_dir($source_child = "$source_dir/$source_file")) {
      wordpress_mobile_pack_directory_copy_themes($source_child, $destination_child, $benign);
      continue;
    }

    if (file_exists($destination_child) && !is_writable($destination_child)) {
      update_option('wpmp_warning', sprintf(__('<strong>Could not install file</strong> to %s.', 'wpmp'), $destination_child) . ' ' . __('Please ensure that the web server has write- access to that file.', 'wpmp'));
      continue;
    }
    copy($source_child, $destination_child);
  }
  closedir($dir_handle);
}

function wordpress_mobile_pack_deactivate() {
  wordpress_mobile_pack_hook('deactivate');
}

function wordpress_mobile_pack_hook($action) {
  global $wpmp_plugins;
  foreach($wpmp_plugins as $wpmp_plugin) {
    if (function_exists($function = $wpmp_plugin . "_" . $action)) {
      call_user_func($function);
    }
  }
}




?>
