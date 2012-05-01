<?php
$natty_themename = 'delicate';
$natty_current = '3.5.3';

$natty_manualurl = 'http://support.nattywp.com/index.php?act=kb';

$natty_functions_path = TEMPLATEPATH . '/functions/';
$natty_include_path = TEMPLATEPATH . '/include/';
$natty_license_path = TEMPLATEPATH . '/license/';

require_once ($natty_include_path . 'settings-color.php');
require_once ($natty_include_path . 'settings-theme.php');
require_once ($natty_include_path . 'settings-comments.php');

require_once ($natty_functions_path . 'core-init.php');

require_once ($natty_include_path . 'hooks.php');
require_once ($natty_include_path . 'sidebar-init.php');
require_once ($natty_include_path . 'widgets/flickr.php');
require_once ($natty_include_path . 'widgets/feedburner.php');
require_once ($natty_include_path . 'widgets/twitter.php');

require_once ($natty_license_path . 'license.php');
?>