<?php

/*
$Id: wpmp_barcode_widget_admin.php 195195 2010-01-19 04:11:37Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_barcode/wpmp_barcode_widget_admin.php $

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
  <label for="wpmp_barcode_title"><?php _e('Title:', 'wpmp'); ?></label>
  <?php print wpmp_barcode_option('wpmp_barcode_title', '', 'widefat'); ?>
</p>
<p>
  <label for="wpmp_barcode_link"><?php _e('Link:', 'wpmp'); ?></label>
  <?php print wpmp_barcode_option('wpmp_barcode_link', '', 'widefat'); ?>
  <br /><?php _e('If you leave this blank, the URL in the barcode will be dynamic, and will be the mobile equivalent of the actual page the user is on.', 'wpmp'); ?>
</p>
<p>
  <label for="wpmp_barcode_size"><?php _e('Size:', 'wpmp'); ?></label>
  <br />
  <?php print wpmp_barcode_option('wpmp_barcode_size', '', 'widefat', 'width:23%'); ?>px
</p>
<p>
  <?php print wpmp_barcode_option('wpmp_barcode_help'); ?>
  <label for="wpmp_barcode_help"><?php _e('Show explanation', 'wpmp'); ?></label>
</p>
<p>
  <?php print wpmp_barcode_option('wpmp_barcode_reader_list'); ?>
  <label for="wpmp_barcode_reader_list"><?php _e('Show list of readers', 'wpmp'); ?></label>
</p>
<input type="hidden" id="wpmp_barcode" name="wpmp_barcode" value="1" />
