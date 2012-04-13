<?php
/*
Plugin Name: Simple Facebook Connect
Plugin URI: http://ottopress.com/wordpress-plugins/simple-facebook-connect/
Description: Makes it easy for your site to use Facebook Connect, in a wholly modular way.
Author: Otto
Version: 1.3
Author URI: http://ottodestruct.com
License: GPL2

    Copyright 2009-2011  Samuel Wood  (email : otto@ottodestruct.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/
function sfc_version() {
	return '1.3';
}

// prevent parsing errors on PHP 4 or old WP installs
if ( !version_compare(PHP_VERSION, '5', '<') && version_compare( $wp_version, '3.2.999', '>' ) ) {
	include 'sfc-base.php';
} else {
	add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".__('Simple Facebook Connect requires PHP 5 and WordPress 3.3 to function. Please upgrade or deactivate the SFC plugin.', 'sfc') ."</p></div>';" ) );
}

// plugin row links
add_filter('plugin_row_meta', 'sfc_donate_link', 10, 2);
function sfc_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$links[] = '<a href="'.admin_url('options-general.php?page=sfc').'">'.__('Settings', 'sfc').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom">'.__('Donate', 'sfc').'</a>';
	}
	return $links;
}

// action links
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sfc_settings_link', 10, 1);
function sfc_settings_link($links) {
	$links[] = '<a href="'.admin_url('options-general.php?page=sfc').'">'.__('Settings', 'sfc').'</a>';
	return $links;
}

