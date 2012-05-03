<?php

/*
$Id: wpmp_theme_theme_admin.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/themes/mobile_pack_base/wpmp_theme_theme_admin.php $

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

?><div class="wrap">
  <h2>
    <?php _e('Mobile Theme', 'wpmp') ?>
    <p style='font-size:small;font-style:italic;margin:0'>
      <?php _e('Part of the WordPress Mobile Pack', 'wpmp'); ?>
    </p>
  </h2>
  <p>
    <?php printf(__('<strong>Select the mobile theme itself on the <a%s>switcher settings</a> page.</strong> The page below allows you to further configure its behaviour.', 'wpmp'), ' href="themes.php?page=wpmp_switcher_admin"'); ?>
  </p>
  <form method="post" action="">
    <table class="form-table">
      <tr>
        <th><?php _e('Enable Nokia templates', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_theme_option('wpmp_theme_nokia_templates'); ?>
          <br />
          <?php _e('Check this to provide additional optimisations for users of Nokia handsets. It also enables a rich theme for WebKit-based mobile browsers, such as the Apple iPhone and Palm Pre.', 'wpmp'); ?>
        </td>
      </tr>
      <tr>
        <th><?php _e('Check mobile status', 'wpmp'); ?></th>
        <td>
          <a style='font-weight:bold;font-size:13px' target='_blank' href='http://ready.mobi/results.jsp?uri=<?php print urlencode(get_option('home')); ?>'>Launch ready.mobi</a>
          <br />
          <?php _e('Click this link to check that the front page of your site is ready for mobile users. NB: this will only work for externally-visible sites.', 'wpmp'); ?>
        </td>
      </tr>
      <tr>
        <th><?php _e('Show home link in menu', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_theme_option('wpmp_theme_home_link_in_menu'); ?>
          <br />
          <?php _e("Unselect this if you are using a dedicated page for the blog home. It prevents 'Home' appearing twice in the menu.", 'wpmp'); ?>
        </td>
      </tr>
      <tr>
        <th><?php _e('Number of posts', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_theme_option('wpmp_theme_post_count'); ?>
          <br />
          <?php _e('This constrains the length of a list of posts (such as on the home page or in an archive). Consider the consequences these settings may have on page size for limited mobile devices.', 'wpmp'); ?>
        </td>
      </tr>
      <tr>
        <th><?php _e('Lists of posts show', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_theme_option('wpmp_theme_post_summary', 'wpmpThemeSummary()'); ?>
          <br />
            <?php print wpmp_theme_option('wpmp_theme_post_summary_metadata'); ?> <strong><?php _e("Display metadata for posts (author, tags, etc)", 'wpmp'); ?></strong>
          <br />
          <?php _e('These settings apply when your site is displaying a list of posts.', 'wpmp'); ?>
        </td>
      </tr>
      <tr class='wpmp_teaser'>
        <th><?php _e('Teaser length', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_theme_option('wpmp_theme_teaser_length'); ?>
          <br />
          <?php _e("The mobile theme will display teasers of this length (or use each post's 'more' break, if present - whichever is shorter).", 'wpmp'); ?>
        </td>
      </tr>
      <tr>
        <th><?php _e('Number of widget items', 'wpmp'); ?></th>
        <td>
          <?php print wpmp_theme_option('wpmp_theme_widget_list_count'); ?>
          <br />
          <?php _e("For 3 standard widgets 'Archives', 'Categories', and 'Tag cloud' (which are often lengthy), this will shorten their number of items to the given length. Where necessary, a link will be provided to the full list.", 'wpmp'); ?>
        </td>
      </tr>
      <?php if(function_exists('wpmp_transcoder_purge_cache')) { ?>
        <tr>
          <th><?php _e('Remove media', 'wpmp'); ?></th>
          <td>
            <?php print wpmp_theme_option('wpmp_theme_transcoder_remove_media'); ?>
            <br />
            <?php _e('This will remove interactivity and media elements (such as script, Flash, movies, and embedded frames) from your posts and pages.', 'wpmp'); ?>
          </td>
        </tr>
        <tr>
          <th><?php _e('Partition large pages', 'wpmp'); ?></th>
          <td>
            <?php print wpmp_theme_option('wpmp_theme_transcoder_partition_pages'); ?>
            <br />
            <?php _e('This will break large blog posts or pages into smaller pieces more suitable for mobile devices.', 'wpmp'); ?>
          </td>
        </tr>
        <tr>
          <th><?php _e('Shrink images', 'wpmp'); ?></th>
          <td>
            <?php print wpmp_theme_option('wpmp_theme_transcoder_shrink_images'); ?>
            <br />
            <?php _e('This will shrink large images within posts or pages to fit on smaller screens.', 'wpmp'); ?>
            <br /><br />
            <?php print wpmp_theme_option('wpmp_theme_transcoder_clear_cache_now'); ?> <strong><?php _e("Clear cache now", 'wpmp'); ?></strong>
            <br />
            <?php _e('Size-adjusted images are cached locally for performance. If an existing original image has changed, you may need to clear this cache to have it update for mobile users.', 'wpmp'); ?>
          </td>
        </tr>
        <tr>
          <th><?php _e('Simplify styling', 'wpmp'); ?></th>
          <td>
            <?php print wpmp_theme_option('wpmp_theme_transcoder_simplify_styling'); ?>
            <br />
            <?php _e('This will remove styling elements from your posts and pages to ensure mobile compatibility.', 'wpmp'); ?>
          </td>
        </tr>
      <?php } ?>
    </table>

    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Save Changes', 'wpmp'); ?>" />
    </p>
  </form>
</div>

<script>
  var wpmp_pale = 0.3;
  var wpmp_speed = 'slow';
  function wpmpThemeSummary(speed) {
    if (speed==null) {speed=wpmp_speed;}
    var value = jQuery("#wpmp_theme_post_summary").val();
    jQuery(".wpmp_teaser").children().fadeTo(speed, value.indexOf("teaser")>-1 ? 1 : wpmp_pale);
  }
  wpmpThemeSummary(-1);
</script>
