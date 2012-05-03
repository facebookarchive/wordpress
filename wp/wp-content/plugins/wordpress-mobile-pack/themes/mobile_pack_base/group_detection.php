<?php

/*
$Id: group_detection.php 134485 2009-07-12 17:04:19Z jamesgpearce $

$URL: http://plugins.svn.wordpress.org/wordpress-mobile-pack/trunk/plugins/wpmp_switcher/lite_detection.php $

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

function group_detection() {
  $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
  if (
  	strpos($user_agent, 'series60') !== false ||
  	strpos($user_agent, 'maemo') !== false ||
  	strpos($user_agent, 'webkit') !== false
  ) {
    return 'nokia_high';
  }
  if (strpos($user_agent, 'series40') !== false) {
    return 'nokia_mid';
  }
  if (strpos($user_agent, 'nokia') !== false) {
    return 'nokia_low';
  }
  return '';
}

?>
