<?php

/*
$Id: mobile.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_switcher/pages/mobile.php $

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

function wpmp_ms_mobile_top($title, $menu=array()) {
  print "<?xml version='1.0' encoding='UTF-8'?>";

  //defend against old, confused or custom mobile themes
  include_once(get_theme_root() . DIRECTORY_SEPARATOR . 'mobile_pack_base' . DIRECTORY_SEPARATOR . 'functions.php');
  if(!function_exists('wpmp_theme_group_file')) {
    function wpmp_theme_group_file($file='index.php') {
      return 'none';
    }
    function wpmp_theme_base_style() {
      return get_bloginfo('stylesheet_url');
    }
    function wpmp_theme_group() {
      return 'none';
    }
  }

  if (file_exists($wpmp_include = wpmp_theme_group_file('header.php'))) {
    include_once($wpmp_include);
  } else {
    ?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head profile="http://gmpg.org/xfn/11">
      <?php if (get_bloginfo('stylesheet_url') != wpmp_theme_base_style()) { ?>
        <link href="<?php print wpmp_theme_base_style() ?>" rel="stylesheet" type="text/css" />
      <?php } ?>
      <link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css" />
      <link href="<?php print get_theme_root_uri(); ?>/mobile_pack_base/style_structure.css" rel="stylesheet" type="text/css" />
    <?php
  }
  ?>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php bloginfo('name'); ?> <?php print $title; ?></title>
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
    <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_head(); ?>
  </head>
  <body class="<?php if($wpmp_theme_group = wpmp_theme_group()) {print $wpmp_theme_group;} else {print 'base';} ?>">
    <div id="wrap">
      <div id="header" style='height:auto'>
        <p><a href="<?php echo get_option('home'); ?>/"><strong><?php bloginfo('name'); ?></strong></a></p>
        <p><?php bloginfo('description'); ?></p>
      </div>
      <?php
        if($menu) {
          $base = get_option('home');
          print '<div id="menu"><ul class="breadcrumbs">';
          $page = $_SERVER['REQUEST_URI'];
          if(substr($page, -9)=="/wp-admin") {
            $page="$base/wp-admin/index.php";
          }
          foreach($menu as $name=>$link) {
            $item = '<li class="';
           if(strpos(strtolower($page), strtolower($link))!==false) {
              $item .= 'current_';
              $title = substr($name, ($name[0]=='_')?1:0);
            }
            if(substr($link, 0, 7)!="http://" && substr($link, 0, 8)!="https://") {
              $link = $base . $link;
            }
            $item .= 'page_item"><a href="' . $link . '" title="' . $name . '">' . __($name, 'wpmp') . '</a></li> ';
            if ($name[0]!='_') {
              print $item;
            }
          }
          print '</ul></div>';
        }
      ?>
      <div id="wrapper">
        <div id="content">
          <h1><?php print $title; ?></h1>
          <?php
          }



          function wpmp_ms_mobile_bottom() {
          ?>
        </div>
      </div>
        <div id="footer">
        <?php
          if (file_exists($wpmp_include = wpmp_theme_group_file('footer.php'))) {
            include_once($wpmp_include);
          } else {
            ?>
              <p><?php printf(__("Powered by the <a%s>WordPress Mobile Pack</a>", 'wpmp'), ' href="http://wordpress.org/extend/plugins/wordpress-mobile-pack/"');?> | <?php printf(__("Theme designed by <a%s>ribot</a>", 'wpmp'), ' href="http://ribot.co.uk"'); ?></p>
            <?php
          }
        ?>
        <?php wpmp_switcher_wp_footer(true); ?>
      </div>
    </div>
  </body>
</html>
<?php
}
?>
