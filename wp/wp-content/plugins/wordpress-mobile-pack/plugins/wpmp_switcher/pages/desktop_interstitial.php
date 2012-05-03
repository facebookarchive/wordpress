<?php

/*
$Id: desktop_interstitial.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_switcher/pages/desktop_interstitial.php $

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

?><html>
  <head>
    <title><?php bloginfo('name'); ?> - <?php _e("Select site", 'wpmp'); ?></title>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <?php wp_head(); ?>
  </head>
  <body>
    <h2><?php _e("Select site", 'wpmp'); ?></h2>
    <p><?php _e("You've requested the mobile site, but you appear to have a desktop browser.", 'wpmp'); ?></p>
    <p><?php print wpmp_switcher_link('desktop', __("Revert to the desktop site", 'wpmp')); ?></p>
    <p><?php print wpmp_switcher_link('mobile', __("Continue to our mobile site", 'wpmp')); ?></p>
  </body>
</html>
