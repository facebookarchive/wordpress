<?php

/*
$Id: wpmp_ads_widget_admin.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_ads/wpmp_ads_widget_admin.php $

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

?>

<p>
  <label for="wpmp_ads_title"><?php _e('Title:', 'wpmp'); ?></label>
  <?php print wpmp_ads_option('wpmp_ads_title', '', 'widefat'); ?>
</p>
<p>
  <label for="wpmp_ads_provider"><?php _e('Provider:', 'wpmp'); ?></label>
  <?php print wpmp_ads_option('wpmp_ads_provider'); ?>
</p>
<p>
  <label for="wpmp_ads_publisher_id"><?php _e('Publisher ID:', 'wpmp'); ?></label>
  <br />
  <?php print wpmp_ads_option('wpmp_ads_publisher_id', '', 'widefat'); ?>
  <br /><?php _e("Examples: a14948dbe57548e (for AdMob) or pub-2709587966093607 (for Google)", 'wpmp'); ?>
</p>
<p>
  <?php printf(__("This widget should only be used on mobile themes. If you are using a theme from, or derived from, the WordPress Mobile Pack, you will need to enable this widget <a%s>here</a>.", 'wpmp'), " href='/wp-admin/themes.php?page=wpmp_theme_widget_admin' target='_blank'"); ?>
</p>
<p>
  <?php print wpmp_ads_option('wpmp_ads_desktop_disable'); ?>
  <label for="wpmp_ads_desktop_disable"><?php _e('Attempt to automatically disable for desktop themes (when switcher is running)', 'wpmp'); ?></label>
</p>
<p>
  <?php _e('Note also that this widget will be completely hidden if no ads are returned from the provider you have selected.', 'wpmp'); ?>
</p>
<input type="hidden" id="wpmp_ads" name="wpmp_ads" value="1" />
