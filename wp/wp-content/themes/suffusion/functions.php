<?php
/**
 * Core functions file for the theme. Includes other key files.
 *
 * @package Suffusion
 * @subpackage Functions
 */

if (!defined('SUFFUSION_THEME_VERSION')) {
	define('SUFFUSION_THEME_VERSION', '4.1.5.b4');
}

require_once(get_template_directory().'/functions/framework.php');
$suffusion_framework = new Suffusion_Framework();

add_action("after_setup_theme", "suffusion_theme_setup");

/**
 * Initializing action. If you are creating a child theme and you want to override some of Suffusion's actions/filters etc you
 * can add your own action to the hook "after_setup_theme", with a priority > 10 if you want your actions to be executed after
 * Suffusion and with priority < 10 if you want your actions executed before.
 *
 * @return void
 */
function suffusion_theme_setup() {
	global $pagenow, $suffusion_unified_options, $suffusion;
	suffusion_add_theme_supports();
	suffusion_include_files();
	suffusion_setup_standard_actions_and_filters();
	suffusion_setup_custom_actions_and_filters();
	suffusion_setup_skin();
	foreach ($suffusion_unified_options as $option => $value) {
		global $$option;
		$$option = $value;
	}

	$suffusion = new Suffusion();
	$suffusion->init();

	if(is_admin() && isset($_GET['activated']) && $pagenow = 'themes.php') {
		header('Location: '.admin_url().'themes.php?page=suffusion-options-manager&now-active=true' );
	}
}

/**
 * Define global variables for custom post types.
 *
 * @return void
 */
function suffusion_set_custom_post_type_globals() {
	global $suffusion_post_type_labels, $suffusion_post_type_args, $suffusion_post_type_supports;

	if (!isset($suffusion_post_type_labels)) {
		$suffusion_post_type_labels = array(
			array('name' => 'name', 'type' => 'text', 'desc' => 'Name (e.g. Books)', 'std' => '', 'reqd' => true),
			array('name' => 'singular_name', 'type' => 'text', 'desc' => 'Singular Name (e.g. Book)', 'std' => '', 'reqd' => true),
			array('name' => 'add_new', 'type' => 'text', 'desc' => 'Text for "Add New" (e.g. Add New)', 'std' => ''),
			array('name' => 'add_new_item', 'type' => 'text', 'desc' => 'Text for "Add New Item" (e.g. Add New Book)', 'std' => ''),
			array('name' => 'edit_item', 'type' => 'text', 'desc' => 'Text for "Edit Item" (e.g. Edit Book)', 'std' => ''),
			array('name' => 'new_item', 'type' => 'text', 'desc' => 'Text for "New Item" (e.g. New Book)', 'std' => ''),
			array('name' => 'view_item', 'type' => 'text', 'desc' => 'Text for "View Item" (e.g. View Book)', 'std' => ''),
			array('name' => 'search_items', 'type' => 'text', 'desc' => 'Text for "Search Items" (e.g. Search Books)', 'std' => ''),
			array('name' => 'not_found', 'type' => 'text', 'desc' => 'Text for "Not found" (e.g. No Books Found)', 'std' => ''),
			array('name' => 'not_found_in_trash', 'type' => 'text', 'desc' => 'Text for "Not found in Trash" (e.g. No Books Found in Trash)', 'std' => ''),
			array('name' => 'parent_item_colon', 'type' => 'text', 'desc' => 'Parent Text with a colon (e.g. Book Series:)', 'std' => ''),
		);
	}

	if (!isset($suffusion_post_type_args)) {
		$suffusion_post_type_args = array(
			array('name' => 'public', 'desc' => 'Public', 'type' => 'checkbox', 'default' => true),
			array('name' => 'publicly_queryable', 'desc' => 'Publicly Queriable', 'type' => 'checkbox', 'default' => true),
			array('name' => 'show_ui', 'desc' => 'Show UI', 'type' => 'checkbox', 'default' => true),
			array('name' => 'query_var', 'desc' => 'Query Variable', 'type' => 'checkbox', 'default' => true),
			array('name' => 'rewrite', 'desc' => 'Rewrite', 'type' => 'checkbox', 'default' => true),
			array('name' => 'hierarchical', 'desc' => 'Hierarchical', 'type' => 'checkbox', 'default' => true),
			array('name' => 'has_archive', 'desc' => 'Archives allowed', 'type' => 'checkbox', 'default' => true),
			array('name' => 'exclude_from_search', 'desc' => 'Exclude from Search', 'type' => 'checkbox', 'default' => true),
			array('name' => 'show_in_nav_menus', 'desc' => 'Show in Navigation menus', 'type' => 'checkbox', 'default' => true),
			array('name' => 'menu_position', 'desc' => 'Menu Position', 'type' => 'text', 'default' => null),
		);
	}

	if (!isset($suffusion_post_type_supports)) {
		$suffusion_post_type_supports = array(
			array('name' => 'title', 'desc' => 'Title', 'type' => 'checkbox', 'default' => false),
			array('name' => 'editor', 'desc' => 'Editor', 'type' => 'checkbox', 'default' => false),
			array('name' => 'author', 'desc' => 'Author', 'type' => 'checkbox', 'default' => false),
			array('name' => 'thumbnail', 'desc' => 'Thumbnail', 'type' => 'checkbox', 'default' => false),
			array('name' => 'excerpt', 'desc' => 'Excerpt', 'type' => 'checkbox', 'default' => false),
			array('name' => 'trackbacks', 'desc' => 'Trackbacks', 'type' => 'checkbox', 'default' => false),
			array('name' => 'custom-fields', 'desc' => 'Custom Fields', 'type' => 'checkbox', 'default' => false),
			array('name' => 'comments', 'desc' => 'Comments', 'type' => 'checkbox', 'default' => false),
			array('name' => 'revisions', 'desc' => 'Revisions', 'type' => 'checkbox', 'default' => false),
			array('name' => 'page-attributes', 'desc' => 'Page Attributes', 'type' => 'checkbox', 'default' => false),
		);
	}
}

/**
 * Define global variables for custom taxonomy setup.
 * 
 * @return void
 */
function suffusion_set_custom_taxonomy_globals() {
	global $suffusion_taxonomy_labels, $suffusion_taxonomy_args;

	if (!isset($suffusion_taxonomy_labels)) {
		$suffusion_taxonomy_labels = array(
			array('name' => 'name', 'type' => 'text', 'desc' => 'Name (e.g. Genres)', 'std' => '', 'reqd' => true),
			array('name' => 'singular_name', 'type' => 'text', 'desc' => 'Singular Name (e.g. Genre)', 'std' => '', 'reqd' => true),
			array('name' => 'search_items', 'type' => 'text', 'desc' => 'Text for "Search Items" (e.g. Search Genres)', 'std' => ''),
			array('name' => 'popular_items', 'type' => 'text', 'desc' => 'Text for "Popular Items" (e.g. Popular Genres)', 'std' => ''),
			array('name' => 'all_items', 'type' => 'text', 'desc' => 'Text for "All Items" (e.g. All Genres)', 'std' => ''),
			array('name' => 'parent_item', 'type' => 'text', 'desc' => 'Parent Item (e.g. Parent Genre)', 'std' => ''),
			array('name' => 'parent_item_colon', 'type' => 'text', 'desc' => 'Parent Item Colon (e.g. Parent Genre:)', 'std' => ''),
			array('name' => 'edit_item', 'type' => 'text', 'desc' => 'Text for "Edit Item" (e.g. Edit Genre)', 'std' => ''),
			array('name' => 'update_item', 'type' => 'text', 'desc' => 'Text for "Update Item" (e.g. Update Genre)', 'std' => ''),
			array('name' => 'add_new_item', 'type' => 'text', 'desc' => 'Text for "Add New Item" (e.g. Add New Genre)', 'std' => ''),
			array('name' => 'new_item_name', 'type' => 'text', 'desc' => 'Text for "New Item Name" (e.g. New Genre Name)', 'std' => ''),
		);
	}

	if (!isset($suffusion_taxonomy_args)) {
		$suffusion_taxonomy_args = array(
			array('name' => 'public', 'desc' => 'Public', 'type' => 'checkbox', 'default' => true),
			array('name' => 'show_ui', 'desc' => 'Show UI', 'type' => 'checkbox', 'default' => true),
			array('name' => 'show_tagcloud', 'desc' => 'Show in Tagcloud widget', 'type' => 'checkbox', 'default' => true),
			array('name' => 'hierarchical', 'desc' => 'Hierarchical', 'type' => 'checkbox', 'default' => true),
			array('name' => 'rewrite', 'desc' => 'Rewrite', 'type' => 'checkbox', 'default' => true),
		);
	}
}

/**
 * Add support for various theme functions
 * @return void
 */
function suffusion_add_theme_supports() {
	add_theme_support('post-thumbnails');
	add_theme_support('menus');
	add_theme_support('automatic-feed-links');

	// Adding post formats, so that users can assign formats to posts. They will be styled in a later release.
	add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

	//Suffusion-specific
	add_theme_support('mega-menus');

	// Support for page excerpts
	add_post_type_support('page', 'excerpt');

	// Additional options for Suffusion. This shows the meta box below posts
	add_theme_support('suffusion-additional-options');

	// Support for native custom headers and backgrounds
	add_custom_background();
}

/**
 * Include other core files. Some files like theme-options.php are included only on demand, because they are too
 * heavy for every load.
 *
 * @return void
 */
function suffusion_include_files() {
	global $suffusion_unified_options, $suffusion_options, $suffusion_interactive_text_fields;
	$template_path = get_template_directory();

	require_once (get_template_directory() . "/admin/theme-definitions.php");

	$suffusion_unified_options = suffusion_get_unified_options();
	if (function_exists('icl_t')) {
		$suffusion_interactive_text_fields = suffusion_get_interactive_text_fields();
	}

	include_once ($template_path . '/widgets/suffusion-widgets.php');
	$widgets = new Suffusion_Widgets();
	$widgets->init();

	require_once ($template_path . "/suffusion.php");
	include_once ($template_path . "/functions/wpml-integration.php");
}

/**
 * Adds actions and filters to standard action hooks and filter hooks.
 * 
 * @return void
 */
function suffusion_setup_standard_actions_and_filters() {
	add_action('init', 'suffusion_register_jquery');
	add_action('init', 'suffusion_register_custom_types');
	add_action('init', 'suffusion_register_menus');

	// Make sure that the generated CSS is written out to the right option at the end of the load.
	add_action('wp_loaded', 'suffusion_update_generated_css', 11);

	// Required for WP-MS, 3.0+
	add_action('before_signup_form', 'suffusion_pad_signup_form_start');
	add_action('after_signup_form', 'suffusion_pad_signup_form_end');

	if (!is_admin()) {
		add_action('wp_print_styles', 'suffusion_disable_plugin_styles');
	}

	////// FILTERS - The callbacks are in filters.php
	add_filter('extra_theme_headers', 'suffusion_extra_theme_headers');

	add_filter('user_contactmethods', 'suffusion_add_user_contact_methods');

	add_filter('widget_text', 'do_shortcode');

	if (current_theme_supports('mega-menus')) {
		add_filter('walker_nav_menu_start_el', 'suffusion_mega_menu_walker', 10, 4);
	}

	if (!is_admin()) {
		add_action('wp_enqueue_scripts', 'suffusion_enqueue_styles');
		add_action('wp_enqueue_scripts', 'suffusion_enqueue_scripts');

		add_action('wp_head', 'suffusion_add_header_contents');
		add_action('wp_head', 'suffusion_print_direct_styles', 11);
		add_action('wp_head', 'suffusion_print_direct_scripts', 11);
		add_action('wp_head', 'suffusion_create_analytics_contents', 30);
		remove_action('wp_head', 'wp_generator');

		add_action('wp_footer', 'suffusion_add_footer_contents');

		add_action('template_redirect', 'suffusion_custom_css_display');

		add_filter('get_pages', 'suffusion_replace_page_with_alt_title');
		add_filter('wp_list_pages', 'suffusion_js_for_unlinked_pages');
		add_filter('wp_list_pages', 'suffusion_remove_a_title_attribute');
		add_filter('wp_list_categories', 'suffusion_remove_a_title_attribute');
		add_filter('wp_list_bookmarks', 'suffusion_remove_a_title_attribute');

		add_filter('query_vars', 'suffusion_new_vars');
		add_filter('page_link', 'suffusion_unlink_page', 10, 2);

		add_filter('the_content_more_link', 'suffusion_set_more_link');
		add_filter('the_content', 'suffusion_pages_link', 8);
		add_filter('excerpt_more', 'suffusion_excerpt_more_replace');
		add_filter('the_excerpt', 'suffusion_excerpt_more_append');

		add_filter('comment_reply_link', 'suffusion_hide_reply_link_for_pings', 10, 4);
		add_filter('get_comments_number', 'suffusion_filter_trk_ping_from_count');
		add_filter('get_comments_pagenum_link', 'suffusion_append_comment_type');

		add_filter('style_loader_tag', 'suffusion_filter_rounded_corners_css', 10, 2);
		add_filter('style_loader_tag', 'suffusion_filter_ie_css', 10, 2);
		add_filter('post_class', 'suffusion_extra_post_classes');

		add_filter('nav_menu_css_class', 'suffusion_mm_nav_css', 10, 3);

		add_filter('body_class', 'suffusion_get_width_classes', 10, 2);

		add_filter('pre_get_posts', 'suffusion_custom_taxonomy_contents');
	}
}

/**
 * Adds actions and filters to custom action hooks and filter hooks.
 * 
 * @return void
 */
function suffusion_setup_custom_actions_and_filters() {
	///// ACTIONS
	add_action('suffusion_document_header', 'suffusion_include_ie7_compatibility_mode');
	add_action('suffusion_document_header', 'suffusion_set_title');
	add_action('suffusion_document_header', 'suffusion_include_meta');
	add_action('suffusion_document_header', 'suffusion_include_favicon');
	add_action('suffusion_document_header', 'suffusion_include_default_feed');

	add_action('suffusion_before_begin_wrapper', 'suffusion_display_open_header');

	add_action('suffusion_after_begin_wrapper', 'suffusion_display_closed_header');
	add_action('suffusion_after_begin_wrapper', 'suffusion_print_widget_area_below_header');

	add_action('suffusion_page_header', 'suffusion_display_header');
	add_action('suffusion_page_header', 'suffusion_display_main_navigation');

	add_action('suffusion_before_begin_content', 'suffusion_build_breadcrumb');
	add_action('suffusion_before_begin_content', 'suffusion_featured_posts');
	add_action('suffusion_after_begin_content', 'suffusion_template_specific_header');

	add_action('suffusion_content', 'suffusion_excerpt_or_content');

	add_action('suffusion_after_begin_post', 'suffusion_print_post_page_title');
	add_action('suffusion_after_begin_post', 'suffusion_print_post_format_icon');
	add_action('suffusion_after_begin_post', 'suffusion_print_post_updated_information');
	add_action('suffusion_after_begin_post', 'suffusion_print_line_byline_top');

	add_action('suffusion_after_content', 'suffusion_meta_pullout');

	add_action('suffusion_before_end_post', 'suffusion_author_information');
	add_action('suffusion_before_end_post', 'suffusion_post_footer');
	add_action('suffusion_before_end_post', 'suffusion_print_line_byline_bottom');

	add_action('suffusion_before_end_content', 'suffusion_pagination');

	// Print sidebars
	add_action('suffusion_before_end_container', 'suffusion_print_left_sidebars');
	add_action('suffusion_before_end_container', 'suffusion_print_right_sidebars');

	add_action('suffusion_after_end_container', 'suffusion_print_widget_area_above_footer');

	add_action('suffusion_page_footer', 'suffusion_display_footer');

	add_action('suffusion_document_footer', 'suffusion_include_custom_js');

	add_action('suffusion_skin_setup_photonique', 'suffusion_skin_setup_photonique');

	///// FILTERS
	add_filter('suffusion_can_display_attachment', 'suffusion_filter_attachment_display', 10, 4);
	add_filter('suffusion_left_sidebar_count', 'suffusion_get_sidebar_count_for_view', 10, 3);
	add_filter('suffusion_right_sidebar_count', 'suffusion_get_sidebar_count_for_view', 10, 3);

	add_filter('suffusion_after_comment_form', 'suffusion_allowed_html_tags');
}

function suffusion_setup_skin() {
	global $suffusion_theme_name;
	if (isset($suffusion_theme_name)) {
		do_action("suffusion_skin_setup_{$suffusion_theme_name}");
	}
}

function suffusion_skin_setup_photonique() {
	add_action('wp_enqueue_scripts', 'suffusion_enqueue_skin_scripts');
}

function suffusion_enqueue_skin_scripts() {
	global $suffusion_theme_name;
	if (isset($suffusion_theme_name) && $suffusion_theme_name == 'photonique') {
		wp_enqueue_style('suffusion-photonique-fonts', "http://fonts.googleapis.com/css?family=Quattrocento", array(), null);
	}
}

function suffusion_admin_check_integer($val) {
	if (substr($val, 0, 1) == '-') {
		$val = substr($val, 1);
	}
	return (preg_match('/^\d*$/', $val) == 1);
}

function suffusion_admin_get_size_from_field($val, $default, $allow_blank = true, $offset = 0) {
	$ret = $default;
	if (substr(trim($val), -2) == "px") {
		$test_str = trim(substr(trim($val), 0, strlen(trim($val)) - 2));
		if (suffusion_admin_check_integer($test_str)) {
			$test_str = (int)$test_str;
			$test_str = $test_str + $offset;
			$ret = $test_str."px";
		}
	}
	else if (suffusion_admin_check_integer(trim($val))) {
		if (!$allow_blank) {
			if (trim($val) != '') {
				$test_str = (int)trim($val);
				$test_str = $test_str + $offset;
				$ret = $test_str."px";
			}
		}
		else {
			$test_str = (int)trim($val);
			$test_str = $test_str + $offset;
			$ret = $test_str."px";
		}
	}
	return $ret;
}

function suffusion_get_numeric_size_from_field($val, $default) {
	$ret = $default;
	if (substr(trim($val), -2) == "px") {
		$test_str = trim(substr(trim($val), 0, strlen(trim($val)) - 2));
		if (suffusion_admin_check_integer($test_str)) {
			$ret = (int)$test_str;
		}
	}
	else if (suffusion_admin_check_integer(trim($val))) {
		$ret = (int)(trim($val));
	}
	return $ret;
}

function suffusion_tab_array_prepositions() {
    global $suffusion_sidebar_tabs;
    $ret = array();
    foreach ($suffusion_sidebar_tabs as $key => $value) {
        $ret[$key] = $value['title'];
    }
    return $ret;
}

function suffusion_entity_prepositions($entity_type = 'nav') {
	if ($entity_type == 'nav') {
		$ret = array('pages' => 'Pages', 'categories' => 'Categories', 'links' => 'Links');
		$menus = wp_get_nav_menus();
		if ($menus == null) {
			$menus = array();
		}

		foreach ($menus as $menu) {
			$ret["menu-".$menu->term_id] = $menu->name;
		}
	}
	else if ($entity_type == 'nr') {
		$ret = array('current' => 'Currently Reading', 'unread' => 'Not Yet Read', 'completed' => 'Completed');
	}
	else if ($entity_type == 'mag-layout') {
		$ret = array('headlines' => 'Headlines', 'excerpts' => 'Excerpts', 'categories' => 'Categories');
	}
	else if ($entity_type == 'thumb-mag-excerpt' || $entity_type == 'thumb-excerpt' || $entity_type == 'thumb-mag-headline' || $entity_type == 'thumb-tiles') {
		$ret = array('native' => 'Native WP 3.0 featured image', 'custom-thumb' => 'Image specified through custom thumbnail field',
			'attachment' => 'Image attached to the post', 'embedded' => 'Embedded URL in post', 'custom-featured' => 'Image specified through custom Featured Image field');
	}
	else if ($entity_type == 'thumb-featured') {
		$ret = array('custom-featured' => 'Image specified through custom Featured Image field', 'native' => 'Native WP 3.0 featured image', 'custom-thumb' => 'Image specified through custom thumbnail field',
			'attachment' => 'Image attached to the post', 'embedded' => 'Embedded URL in post');
	}
	else if ($entity_type == 'sitemap') {
		global $suffusion_sitemap_entities;
		$ret = array();
		foreach ($suffusion_sitemap_entities as $entity => $entity_options) {
			$ret[$entity] = $entity_options['title'];
		}
	}
    return $ret;
}

function suffusion_get_unified_options() {
	global $suffusion_unified_options, $suffusion_default_theme_name;
	$suffusion_unified_options = get_option('suffusion_options');
	if (!isset($suffusion_unified_options) || !is_array($suffusion_unified_options)) {
		// Regenerate the options
		$suffusion_unified_options = suffusion_default_options();
		$suffusion_unified_options['theme-version'] = SUFFUSION_THEME_VERSION;
		$suffusion_unified_options['option-date'] = date(get_option('date_format').' '.get_option('time_format'));
		$save = true;
	}
	else if (!isset($suffusion_unified_options['theme-version']) ||
			 (isset($suffusion_unified_options['theme-version']) && $suffusion_unified_options['theme-version'] != SUFFUSION_THEME_VERSION) ||
			 !isset($suffusion_unified_options['option-date'])) {
		$default_options = suffusion_default_options();
		$suffusion_unified_options = array_merge($default_options, $suffusion_unified_options);
		$suffusion_unified_options['theme-version'] = SUFFUSION_THEME_VERSION;
		$suffusion_unified_options['option-date'] = date(get_option('date_format').' '.get_option('time_format'));
		$save = true;
	}

	$template_path = get_template_directory();
	$stylesheet_path = get_stylesheet_directory();
	$suffusion_theme_name = suffusion_get_theme_name();
	if ($suffusion_theme_name == 'root') {
		$skin = $suffusion_default_theme_name;
	}
	else {
		$skin = $suffusion_theme_name;
	}

	if (file_exists($stylesheet_path . "/skins/$skin/settings.php")) {
		include_once($stylesheet_path . "/skins/$skin/settings.php");
	}
	else if (file_exists($template_path . "/skins/$skin/settings.php")) {
		include_once($template_path . "/skins/$skin/settings.php");
	}

	if (isset($skin_settings) && is_array($skin_settings)) {
		foreach ($skin_settings as $key => $value) {
			if (!isset($suffusion_unified_options[$key]) || (isset($suffusion_unified_options[$key]) && $suffusion_unified_options[$key] == 'theme')) {
				$suffusion_unified_options[$key] = $skin_settings[$key];
			}
		}
	}

	if (isset($save)) {
		update_option('suffusion_options', $suffusion_unified_options);
	}

	return $suffusion_unified_options;
}

if (!function_exists('suffusion_get_memory_usage')) {
	/**
	 * Returns the total memory usage for the script at any point.
	 *
	 * @param bool $echo Echoes the value if set to true
	 * @return string
	 */
	function suffusion_get_memory_usage($echo = true) {
		$ret = "";
		if (function_exists('memory_get_usage')) {
			$mem = memory_get_usage();
			$unit = "B";
			if ($mem > 1024) {
				$mem = round($mem / 1024);
				$unit = "KB";
				if ($mem > 1024) {
					$mem = round($mem / 1024);
					$unit = "MB";
				}
			}
			$ret = $mem . $unit;
			if ($echo) {
				echo $ret;
			}
		}
		return $ret;
	}
}

/**
 * Returns the name of the skin being used.
 *
 * @return string
 */
function suffusion_get_theme_name() {
    $suffusion_options = get_option('suffusion_options');
    if (!isset($suffusion_options['suf_color_scheme']) || $suffusion_options['suf_color_scheme'] === FALSE || $suffusion_options['suf_color_scheme'] == null || !isset($suffusion_options['suf_color_scheme'])) {
        $theme_name = 'root';
    }
    else {
        $theme_name = $suffusion_options['suf_color_scheme'];
    }
    return $theme_name;
}

function suffusion_get_template_prefixes() {
	$template_prefixes = array('1l-sidebar.php' => '_1l', '1r-sidebar.php' => '_1r', '1l1r-sidebar.php' => '_1l1r', '2l-sidebars.php' => '_2l', '2r-sidebars.php' => '_2r');
	$template_prefixes = apply_filters('suffusion_filter_template_prefixes', $template_prefixes);
	return $template_prefixes;
}

function suffusion_get_template_sidebars() {
	$template_sb = array('1l-sidebar.php' => 1, '1r-sidebar.php' => 1, '1l1r-sidebar.php' => 2, '2l-sidebars.php' => 2, '2r-sidebars.php' => 2);
	$template_sb = apply_filters('suffusion_filter_template_sidebars', $template_sb);
	return $template_sb;
}

function suffusion_new_vars($public_query_vars) {
	$public_query_vars[] = 'suffusion-css';
	return $public_query_vars;
}

/**
 * Core function to generate the custom CSS. This is used by custom-styles.php to print out the stylesheet, if CSS auto-generation
 * is switched off.
 *
 * @param bool $echo
 * @return string
 * @since 3.7.4
 */
function suffusion_generate_all_custom_styles($echo = false) {
	global $suf_size_options, $suf_sidebar_count, $suf_minify_css;
	$suffusion_custom_css_string = "";

	$template_path = get_template_directory();
	include_once ($template_path . '/suffusion-css-helper.php');
	include_once ($template_path . '/suffusion-css-generator.php');

	$suffusion_css_generator = new Suffusion_CSS_Generator(date(get_option('date_format').' '.get_option('time_format')));

	$suffusion_custom_css_string .= "/* ".$suffusion_css_generator->get_creation_date()." */";
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_body_settings();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_wrapper_settings();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_post_bg_settings();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_body_font_settings();

	$suffusion_template_prefixes = suffusion_get_template_prefixes();
	$suffusion_template_sidebars = suffusion_get_template_sidebars();
	foreach ($suffusion_template_prefixes as $template => $prefix) {
		$sb_count = $suffusion_template_sidebars[$template];
		$suffusion_template_widths = $suffusion_css_generator->get_widths_for_template($prefix, $sb_count, $template);
		$template_class = '.page-template-'.str_replace('.', '-', $template);
		$suffusion_custom_css_string .= $suffusion_css_generator->get_template_specific_classes($template_class, $suffusion_template_widths);
	}

	if ($suf_size_options == "custom") {
		$suffusion_template_widths = $suffusion_css_generator->get_widths_for_template(false, $suf_sidebar_count);
	}
	else {
		// We still need to get the array of widths for the sidebars.
		$suffusion_template_widths = $suffusion_css_generator->get_automatic_widths(1000, $suf_sidebar_count, false);
	}

	// The default settings:
	$suffusion_custom_css_string .= $suffusion_css_generator->get_template_specific_classes('', $suffusion_template_widths);

	// For the no-sidebars.php template (uses the same widths as computed for the default settings):
	$suffusion_custom_css_string .= $suffusion_css_generator->get_zero_sidebars_template_widths();

	$suffusion_custom_css_string .= $suffusion_css_generator->get_mag_template_widths($suffusion_template_widths);

	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_date_box_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_byline_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_header_settings();

	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_tbrh_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_wabh_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_waaf_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_featured_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_emphasis_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_layout_template_css();

	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_tiled_layout_css($suffusion_template_widths);
	$suffusion_custom_css_string .= $suffusion_css_generator->get_finalized_header_footer_nav_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_nr_css($suffusion_template_widths);

	$suffusion_custom_css_string .= $suffusion_css_generator->get_navigation_bar_custom_css('nav');
	$suffusion_custom_css_string .= $suffusion_css_generator->get_navigation_bar_custom_css('nav-top');

	$post_formats = array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat');
	$suffusion_custom_css_string .= $suffusion_css_generator->get_pullout_css('post');
	foreach ($post_formats as $format) {
		$suffusion_custom_css_string .= $suffusion_css_generator->get_pullout_css('post', $format);
	}
	$suffusion_custom_css_string .= $suffusion_css_generator->get_pullout_css('page');

	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_miscellaneous_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_custom_sidebar_settings_css();

	$suffusion_custom_css_string .= $suffusion_css_generator->get_typography_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_icon_set_css();
	$suffusion_custom_css_string .= $suffusion_css_generator->get_post_format_widths_css();

	if ($suf_minify_css == 'minify') {
		$suffusion_custom_css_string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $suffusion_custom_css_string);
		/* remove tabs, spaces, newlines, etc. */
		$suffusion_custom_css_string = str_replace(array("\r\n", "\r", "\n", "\t"), '', $suffusion_custom_css_string);
		$suffusion_custom_css_string = str_replace(array('  ', '   ', '    ', '     '), ' ', $suffusion_custom_css_string);
		$suffusion_custom_css_string = str_replace(array(": ", " :"), ':', $suffusion_custom_css_string);
		$suffusion_custom_css_string = str_replace(array(" {", "{ "), '{', $suffusion_custom_css_string);
		$suffusion_custom_css_string = str_replace(';}','}', $suffusion_custom_css_string);
		$suffusion_custom_css_string = str_replace(', ', ',', $suffusion_custom_css_string);
		$suffusion_custom_css_string = str_replace('; ', ';', $suffusion_custom_css_string);
	}

	if ($echo) {
		echo $suffusion_custom_css_string;
	}

	return $suffusion_custom_css_string;
}

function suffusion_get_interactive_text_fields() {
	global $suffusion_inbuilt_options;
	$field_titles = get_option('suffusion_options_field_titles');
	if (isset($field_titles) && is_array($field_titles)) {
		$theme_version = $field_titles['theme-version'];
	}

	if ((isset($theme_version) && $theme_version != SUFFUSION_THEME_VERSION) || (!isset($theme_version))) {
		$field_titles = array();
		include_once (get_template_directory() . "/admin/theme-options.php");
		foreach ($suffusion_inbuilt_options as $option) {
			if (isset($option['id'])) {
				$field_titles[$option['id']] = isset($option['name']) ? $option['name'] : '';
			}
		}
		$field_titles['theme-version'] = SUFFUSION_THEME_VERSION;
		if (current_user_can('edit_theme_options')) {
			update_option('suffusion_options_field_titles', $field_titles);
		}
	}
	return $field_titles;
}

/**
 * Updates the generated CSS upon saving.
 *
 * @return mixed|string|void
 */
function suffusion_update_generated_css() {
	global $suffusion_unified_options;

	if (!isset($suffusion_unified_options['option-date'])) {
		$suffusion_unified_options = suffusion_get_unified_options();
	}
	foreach ($suffusion_unified_options as $option => $value) {
		global $$option;
		$$option = $value;
	}

	$custom_css = get_option('suffusion_generated_css');

	if (!isset($custom_css) || (isset($custom_css) && !is_array($custom_css)) ||
		(is_array($custom_css) && !isset($custom_css['theme-version'])) ||
		(is_array($custom_css) && isset($custom_css['theme-version']) && $custom_css['theme-version'] != SUFFUSION_THEME_VERSION) ||
		(is_array($custom_css) && !isset($custom_css['option-date'])) ||
		(is_array($custom_css) && isset($custom_css['option-date']) && $custom_css['option-date'] != $suffusion_unified_options['option-date'])) {
			$custom_css = array();
			$custom_css['css'] = suffusion_generate_all_custom_styles();
			$custom_css['option-date'] = $suffusion_unified_options['option-date'];
			$custom_css['theme-version'] = SUFFUSION_THEME_VERSION;
			update_option('suffusion_generated_css', $custom_css);
	}

	return $custom_css;
}

function suffusion_is_sidebar_empty($index) {
	$sidebars = wp_get_sidebars_widgets();
	if ((!isset($sidebars['sidebar-'.$index]) || (isset($sidebars['sidebar-'.$index]) && $sidebars['sidebar-'.$index] == null) || (isset($sidebars['sidebar-'.$index]) && is_array($sidebars['sidebar-'.$index]) && count($sidebars['sidebar-'.$index]) == 0)) &&
			(!isset($sidebars[$index]) || (isset($sidebars[$index]) && $sidebars[$index] == null) || (isset($sidebars[$index]) && is_array($sidebars[$index]) && count($sidebars[$index]) == 0))) {
		return true;
	}
	return false;
}

function suffusion_sidebar_widget_count($index) {
	$sidebars = wp_get_sidebars_widgets();
	if (!isset($sidebars['sidebar-'.$index]) || $sidebars['sidebar-'.$index] == null || (is_array($sidebars['sidebar-'.$index]) && count($sidebars['sidebar-'.$index]) == 0)) {
		return 0;
	}
	else if (is_array($sidebars['sidebar-'.$index])){
		return count($sidebars['sidebar-'.$index]);
	}
	if (!isset($sidebars[$index]) || $sidebars[$index] == null || (is_array($sidebars[$index]) && count($sidebars[$index]) == 0)) {
		return 0;
	}
	else {
		return count($sidebars[$index]);
	}
}

function suffusion_register_jquery() {
	global $suf_featured_use_lite;
	if ($suf_featured_use_lite == 'lite') {
		wp_register_script('suffusion-jquery-cycle', get_template_directory_uri() . '/scripts/jquery.cycle.lite.min.js', array('jquery'), null);
	}
	else {
		wp_register_script('suffusion-jquery-cycle', get_template_directory_uri() . '/scripts/jquery.cycle.all.min.js', array('jquery'), null);
	}
}

/**
 * Registers the Custom Post Types set through the theme. This function predates CPT plugins, but is now being deprecated with version 4.0.0.
 * If this function determines the existence of the "Suffusion Custom Post Types" plugin, it quits and lets the plugin handle things. Otherwise it just
 * registers the post types. It doesn't display the UI in the back-end.
 *
 * @return mixed
 */
function suffusion_register_custom_types() {
	if (class_exists('Suffusion_Custom_Post_Types')) {
		return;
	}

	$suffusion_post_types = get_option('suffusion_post_types');
	$suffusion_taxonomies = get_option('suffusion_taxonomies');
	if (is_array($suffusion_post_types) || is_array($suffusion_taxonomies)) {
		require_once(get_template_directory().'/functions/custom-post-types.php');
	}
}

/**
 * Function to support meus from the Menu dashboard.
 * Strictly speaking this is not required. You could select these same menus from the Main Navigation Bar Setup or Top Navigation Bar Setup.
 *
 * @return void
 */
function suffusion_register_menus() {
	register_nav_menu('top', 'Navigation Bar Above Header');
	register_nav_menu('main', 'Navigation Bar Below Header');
}

function suffusion_add_user_contact_methods($contact_methods) {
    global $suf_uprof_networks, $suffusion_social_networks;
    if (trim($suf_uprof_networks) != '') {
        $networks = explode(',', $suf_uprof_networks);
        foreach ($networks as $network) {
            $display = $suffusion_social_networks[$network];
            $contact_methods[$network] = $display;
        }
    }
    return $contact_methods;
}

/**
 * Support for a custom tag in the style.css header.
 *
 * @param $headers
 * @return array
 */
function suffusion_extra_theme_headers($headers) {
	if (!in_array('Announcements Feed', $headers)) {
		$headers[] = 'Announcements Feed';
	}

	return $headers;
}

function suffusion_pad_signup_form_start() {
?>
<div id="main-col">
<?php
}

function suffusion_pad_signup_form_end() {
?>
</div><!-- #main-col -->
<?php
}

/**
 * Parses all the options, then picks out the default values for them. This is a heavy operation, hence it is executed only while saving.
 *
 * @return array
 */
function suffusion_default_options() {
	global $suffusion_inbuilt_options;
	if (!isset($suffusion_inbuilt_options) || !is_array($suffusion_inbuilt_options)) {
		require_once(get_template_directory().'/admin/theme-options.php');
	}

	$default_options = array();
	foreach ($suffusion_inbuilt_options as $value) {
		if (isset($value['id']) && isset($value['type']) && $value['type'] != 'button' && isset($value['std'])) {
			$default_options[$value['id']] = suffusion_flatten_option($value);
		}
	}
	return $default_options;
}

/**
 * Converts an option's default value to a string. This is useful for options where the default is stored as an array.
 *
 * @param $option
 * @return string
 */
function suffusion_flatten_option($option) {
	if (!is_array($option) || !isset($option['type']) || !isset($option['id']) || !isset($option['std'])) {
		return '';
	}
	switch ($option['type']) {
		case 'sortable-list':
			if (is_array($option['std'])) {
				return implode(',', array_keys($option['std']));
			}
			return $option['std'];

		case 'multi-select':
			if (is_array($option['std'])) {
				return implode(',', $option['std']);
			}
			return $option['std'];

		case 'border':
			if (is_array($option['std'])) {
				$default_txt = "";
				foreach ($option['std'] as $edge => $edge_val) {
					$default_txt .= $edge.'::';
					foreach ($edge_val as $opt => $opt_val) {
						$default_txt .= $opt . "=" . $opt_val . ";";
					}
					$default_txt .= "||";
				}
				return $default_txt;
			}
			return $option['std'];

		case 'background':
			if (is_array($option['std'])) {
				$default_txt = "";
				foreach ($option['std'] as $opt => $opt_val) {
					$default_txt .= $opt."=".$opt_val.";";
				}
				return $default_txt;
			}
			return $option['std'];

		case 'font':
			if (is_array($option['std'])) {
				$default_txt = "";
				foreach ($option['std'] as $opt => $opt_val) {
					$default_txt .= $opt."=".stripslashes($opt_val).";";
				}
				return $default_txt;
			}
			return $option['std'];

		default:
			return $option['std'];
	}
}

/**
 * Returns an indented array of pages, based on parent and child pages. This is used in the admin menus.
 *
 * @return array
 */
function suffusion_get_formatted_page_array() {
	global $suffusion_pages_array;
	if (isset($suffusion_pages_array) && $suffusion_pages_array != null) {
		return $suffusion_pages_array;
	}
	$ret = array();
	$pages = get_pages('sort_column=menu_order');
    if ($pages != null) {
        foreach ($pages as $page) {
            if (is_null($suffusion_pages_array)) {
	            $ret[$page->ID] = array ("title" => $page->post_title, "depth" => count(get_ancestors($page->ID, 'page')));
            }
        }
    }
	if ($suffusion_pages_array == null) {
		$suffusion_pages_array = $ret;
		return $ret;
	}
	else {
		return $suffusion_pages_array;
	}
}

function suffusion_get_formatted_category_array() {
	global $suffusion_categories_array;
	$ret = array();
	$args = array("type" => "post",
		"orderby" => "name",
		"hide_empty" => false,
	);
	$categories = get_categories($args);
	if ($categories == null) { $categories = array(); }
	foreach ($categories as $category) {
		if ($suffusion_categories_array == null) {
			$ret[$category->cat_ID] = array("title" => $category->cat_name);
		}
	}
	if ($suffusion_categories_array == null) {
		$suffusion_categories_array = $ret;
		return $suffusion_categories_array;
	}
	else {
		return $suffusion_categories_array;
	}
}

function suffusion_get_formatted_link_array() {
	global $link_array;
	$ret = array();
	$args = array(
		"order" => "ASC",
		"orderby" => 'name',
	);
	$links = get_bookmarks($args);
	if ($links == null) {
		$links = array();
	}
	foreach ($links as $link) {
		if ($link_array == null) {
			$ret[$link->link_id] = array("title" => $link->link_name);
		}
	}
	if ($link_array == null) {
		$link_array = $ret;
		return $link_array;
	}
	else {
		return $link_array;
	}
}

function suffusion_get_formatted_wp_menu_array() {
	global $menu_array;
	$ret = array();

	$menus = wp_get_nav_menus();
	if ($menus == null) {
		$menus = array();
	}

	foreach ($menus as $menu) {
		if ($menu_array == null) {
			$ret[$menu->term_id] = array("title" => $menu->name);
		}
	}

	if ($menu_array == null) {
		$menu_array = $ret;
		return $menu_array;
	}
	else {
		return $menu_array;
	}
}

function suffusion_get_formatted_options_array($prefix, $options_array) {
	$ret = array();
    foreach ($options_array as $option_key => $option_value) {
        $ret[$option_key] = array('title' => $option_value, 'depth' => 1);
    }
    return $ret;
}

function suffusion_get_associative_array($stored_value) {
	if (!is_array($stored_value)) {
		$converted = explode('||', $stored_value);
		$stored_value = array();
		foreach ($converted as $converted_string) {
			$converted_pairs = explode('::', $converted_string);
			$index = '';
			$inner_ctr = 0;
			$pair_array = array();
			foreach ($converted_pairs as $pairs_string) {
				$inner_ctr++;
				if ($inner_ctr == 1) {
					$index = $pairs_string;
					continue;
				}
				if (trim($pairs_string) != '') {
					$pairs = explode(';', $pairs_string);
					foreach ($pairs as $pair) {
						$name_value = explode('=', $pair);
						if (count($name_value) <= 1) {
							continue;
						}
						$pair_array[$name_value[0]] = $name_value[1];
					}
				}
			}
			$stored_value[$index] = $pair_array;
		}
	}
	return $stored_value;
}

/**
 * Based on the Image Rotator script by Matt Mullenweg > http://photomatt.net
 * Inspired by Dan Benjamin > http://hiveware.com/imagerotator.php
 * Latest version always at: http://photomatt.net/scripts/randomimage
 *
 * Make the folder the relative path to the images, like "../img" or "random/images/".
 *
 * Modifications by Sayontan Sinha, to dynamically pass the folder for images.
 * This cannot exist as a standalone file, because it loads outside the context of WP, so variables such as folder names cannot be fetched by the file automatically.
 *
 * @param $folder
 * @return string
 */
function suffusion_get_rotating_image($folder) {
	// Space seperated list of extensions, you probably won't have to change this.
	$exts = 'jpg jpeg png gif';

	$files = array(); $i = -1; // Initialize some variables
//	if ('' == $folder) $folder = './';
	$content_folder = WP_CONTENT_DIR."/".$folder;

	$handle = opendir($content_folder);
	$exts = explode(' ', $exts);
	while (false !== ($file = readdir($handle))) {
		foreach($exts as $ext) { // for each extension check the extension
			if (preg_match('/\.'.$ext.'$/i', $file, $test)) { // faster than ereg, case insensitive
				$files[] = $file; // it's good
				++$i;
			}
		}
	}
	closedir($handle); // We're not using it anymore
	mt_srand((double)microtime()*1000000); // seed for PHP < 4.2
	$rand = mt_rand(0, $i); // $i was incremented as we went along
	return WP_CONTENT_URL."/".$folder."/".$files[$rand];
}


/**
 * Returns an array of public custom post types. The name of the post type is the key and the label name is the value.
 *
 * @param bool $built_in
 * @return array
 */
function suffusion_get_custom_post_types($built_in = false) {
	$ret = array();
	if ($built_in) {
		$post_types = get_post_types(array('public' => true, '_builtin' => true), 'objects');
		foreach ($post_types as $post_type) {
			if ($post_type->name != 'attachment') {
				$ret[$post_type->name] = $post_type->labels->name." (".$post_type->name.")";
			}
		}
	}

	$post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
	foreach ($post_types as $post_type) {
		$ret[$post_type->name] = $post_type->labels->name." (".$post_type->name.")";
	}
	return $ret;
}
