<?php
/**
 * Version Control
 *
 *
 * @file           version.php
 * @package        WordPress 
 * @subpackage     responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2011 ThemeID
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/includes/version.php
 * @link           N/A
 * @since          available since Release 1.0
 */
?>
<?php
$theme_data = get_theme_data(STYLESHEETPATH . '/style.css');
define('responsive_current_theme', $theme_name = $theme_data['Name']);

function responsive_template_data() {

    $theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
    $responsive_template_name = $theme_data['Name'];
    $responsive_template_version = $theme_data['Version'];

    echo '<!-- We need this for debugging -->' . "\n";
    echo '<meta name="template" content="' . $responsive_template_name . ' ' . $responsive_template_version . '" />' . "\n";
}

add_action('wp_head', 'responsive_template_data');

function responsive_theme_data() {
    if (is_child_theme()) {
        $theme_data = get_theme_data(STYLESHEETPATH . '/style.css');
        $responsive_theme_name = $theme_data['Name'];
        $responsive_theme_version = $theme_data['Version'];

        echo '<meta name="theme" content="' . $responsive_theme_name . ' ' . $responsive_theme_version . '" />' . "\n";
    }
}

add_action('wp_head', 'responsive_theme_data');