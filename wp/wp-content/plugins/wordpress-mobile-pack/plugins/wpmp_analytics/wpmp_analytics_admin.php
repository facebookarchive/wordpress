<?php

/*
$Id: wpmp_switcher_admin.php 188277 2009-12-30 21:30:27Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_switcher/wpmp_switcher_admin.php $

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

<div class="wrap">
  <h2>
    <?php _e('Mobile Analytics', 'wpmp') ?>
    <p style='font-size:small;font-style:italic;margin:0'>
      <?php _e('Part of the WordPress Mobile Pack', 'wpmp'); ?>
    </p>
  </h2>
  <form method="post" action="">
    <p>
      <?php _e('The Mobile Pack keeps a basic local tally of mobile traffic. However, we recommend you register with an external provider to obtain much richer statistics.', 'wpmp'); ?>
    </p>
    <table class="form-table">
      <?php if (wpmp_analytics_local_enabled()) { ?>
        <tr>
          <th><?php _e('Local analytics', 'wpmp'); ?></th>
          <td>
            <?php print wpmp_analytics_local_summary(); ?>
            <br />
            <?php print wpmp_analytics_option('wpmp_analytics_local_reset'); ?> <strong><?php _e("Reset counter", 'wpmp'); ?></strong>
          </td>
        </tr>
      <?php } ?>
      <tr>
        <th><?php _e('External analytics provider', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_analytics_option('wpmp_analytics_provider', 'wpmpProvider();'); ?>
          <br />
          <?php printf(__('Register with <a%s>PercentMobile</a> for more information, and to obtain a site tracking ID.', 'wpmp'), ' href="http://percentmobile.com" target="_blank"'); ?>
        </td>
      </tr>
      <tr class='wpmp_provider'>
        <th><?php _e('Site tracking ID', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_analytics_option('wpmp_analytics_provider_id', '', '', 'width:30em'); ?>
          <br />
          <?php _e("You receive this long numeric ID when you sign up to PercentMobile. e.g. '330497158886598752234180556567095576021'.", 'wpmp'); ?>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Save Changes', 'wpmp'); ?>" />
    </p>
  </form>
</div>

<script>
  var wpmp_pale = 0.3;
  var wpmp_speed = 'slow';
  function wpmpProvider(speed) {
    if (speed==null) {speed=wpmp_speed;}
    var value = jQuery("#wpmp_analytics_provider").val();
    jQuery(".wpmp_provider").children().fadeTo(speed, (value!='none') ? 1 : wpmp_pale);
  }
  wpmpProvider(-1);
</script>
