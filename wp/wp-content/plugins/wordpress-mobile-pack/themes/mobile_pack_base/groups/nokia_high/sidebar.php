<?php

/*
$Id: sidebar.php 180811 2009-12-08 06:13:51Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/themes/mobile_pack_base/sidebar.php $

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

global $wp_registered_sidebars;
foreach($wp_registered_sidebars as $key=>$sidebar) {
  $wp_registered_sidebars[$key]['before_widget']='';
  $wp_registered_sidebars[$key]['after_widget']='</dd>';
  $wp_registered_sidebars[$key]['before_title']='<dt class="collapsed"><span></span>';
  $wp_registered_sidebars[$key]['after_title']='</dt><dd style="display: none;">';
}

$before_sidebar = '<dl id="accordion_widgets" class="list-accordion"><script type="text/javascript">addEvent("onload", function() {var accordion_widget = new AccordionList("accordion_widgets");});</script>';
$after_sidebar = '</dl>';

?>
