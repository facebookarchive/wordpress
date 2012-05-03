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

?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head profile="http://gmpg.org/xfn/11">
    <link href="<?php print wpmp_theme_group_uri(); ?>/styles/reset.css" rel="stylesheet" type="text/css" />
    <?php if (get_bloginfo('stylesheet_url') != $base_style = wpmp_theme_base_style()) { ?>
      <link href="<?php print $base_style ?>" rel="stylesheet" type="text/css" />
    <?php } ?>
    <link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php print wpmp_theme_group_uri(); ?>/styles/baseStyles.css" rel="stylesheet" type="text/css" />
    <?php if (get_bloginfo('stylesheet_url') != $wpmp_base_style = wpmp_theme_base_style()) { ?>
      <link href="<?php print $wpmp_base_style ?>.nokia.css" rel="stylesheet" type="text/css" />
    <?php } ?>
    <link href="<?php bloginfo('stylesheet_url'); ?>.nokia.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php print wpmp_theme_group_uri(); ?>/scripts/templates.js"></script>
    <script type="text/javascript">
      function init() {
        var myStyleTweaks = new StyleTweaker();
        myStyleTweaks.add("Series60/5.0", "<?php print wpmp_theme_group_uri(); ?>/styles/tweaks/S605th.css");
        myStyleTweaks.add("Series60/3.2", "<?php print wpmp_theme_group_uri(); ?>/styles/tweaks/S603rdFP2.css");
        myStyleTweaks.add("AppleWebKit/420+", "<?php print wpmp_theme_group_uri(); ?>/styles/tweaks/S406th.css");
        myStyleTweaks.add("N900", "<?php print wpmp_theme_group_uri(); ?>/styles/tweaks/maemo.css");
        myStyleTweaks.add("Firefox/3.0a1 Tablet browser 0.3.7", "<?php print wpmp_theme_group_uri(); ?>/styles/tweaks/maemo.css");
        myStyleTweaks.add("Opera Mini/4", "<?php print wpmp_theme_group_uri(); ?>/styles/tweaks/operamini.css");
        myStyleTweaks.tweak();
      }
      addEvent("onload",init);
    </script>