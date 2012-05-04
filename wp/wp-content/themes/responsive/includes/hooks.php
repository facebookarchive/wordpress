<?php
/**
 * Theme's Action Hooks
 *
 *
 * @file           hooks.php
 * @package        WordPress 
 * @subpackage     responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2011 ThemeID
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/includes/hooks.php
 * @link           http://codex.wordpress.org/Plugin_API/Hooks
 * @since          available since Release 1.0
 */
?>
<?php

/**
 * Just after opening <body> tag
 *
 * @see header.php
 */
function responsive_container() {
    do_action('responsive_container');
}

/**
 * Just after closing </div><!-- end of #container -->
 *
 * @see footer.php
 */
function responsive_container_end() {
    do_action('responsive_container_end');
}

/**
 * Just after opening <div id="container">
 *
 * @see header.php
 */
function responsive_header() {
    do_action('responsive_header');
}

/**
 * Just after opening <div id="header">
 *
 * @see header.php
 */
function responsive_in_header() {
    do_action('responsive_in_header');
}

/**
 * Just after closing </div><!-- end of #header -->
 *
 * @see header.php
 */
function responsive_header_end() {
    do_action('responsive_header_end');
}

/**
 * Just before opening <div id="wrapper">
 *
 * @see header.php
 */
function responsive_wrapper() {
    do_action('responsive_wrapper');
}

/**
 * Just after opening <div id="wrapper">
 *
 * @see header.php
 */
function responsive_in_wrapper() {
    do_action('responsive_in_wrapper');
}

/**
 * Just after closing </div><!-- end of #wrapper -->
 *
 * @see header.php
 */
function responsive_wrapper_end() {
    do_action('responsive_wrapper_end');
}

/**
 * Just before opening <div id="widgets">
 *
 * @see sidebar.php
 */
function responsive_widgets() {
    do_action('responsive_widgets');
}

/**
 * Just after closing </div><!-- end of #widgets -->
 *
 * @see sidebar.php
 */
function responsive_widgets_end() {
    do_action('responsive_widgets_end');
}

?>