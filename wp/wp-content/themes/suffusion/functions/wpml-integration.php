<?php 
/**
 * File for integration with WPML admin string translation. This has been copied from docs/theme-integration/wpml-integration.php in the WPML
 * installation folder, as per instructions for WPML (http://wpml.org/documentation/support/creating-multilingual-wordpress-themes/).
 *
 * @since version 3.7.4
 */

/**
 * HOME URL
 * USAGE: replace references to the blog home url such as:
 *  - get_option('home')
 *  - bloginfo('home')
 *  - bloginfo('url')
 *  - get_bloginfo('url')
 *  - etc...
 * with wpml_get_home_url()
 * IMPORTANT: Most themes also add a trailing slash (/) to the URL. This function already includes it, so don't add the slash when using it.
 *
 * @return mixed|string
 */
function wpml_get_home_url() {
	if (function_exists('icl_get_home_url')) {
		return icl_get_home_url();
	}
	else {
		return rtrim(get_bloginfo('url'), '/') . '/';
	}
}


// LANGUAGE SELECTOR
// USAGE place this on the single.php, page.php, index.php etc... - inside the loop
// function wpml_content_languages($args)
// args: skip_missing, before, after
function wpml_content_languages($args = '') {
	parse_str($args);
	if (function_exists('icl_get_languages')) {
		$languages = icl_get_languages($args);
		if (1 < count($languages)) {
			echo isset($before) ? $before : __('This post is also available in: ', 'sitepress');
			foreach ($languages as $l) {
				if (!$l['active']) $langs[] = '<a href="' . $l['url'] . '">' . $l['translated_name'] . '</a>';
			}
			echo join(', ', $langs);
			echo isset($after) ? $after : '';
		}
	}
}


/**
 * Links to specific elements. This retrieves the link to a translated page/post/tag/category etc.
 *
 * @param  $element_id
 * @param string $element_type
 * @param string $link_text
 * @param array $optional_parameters
 * @param string $anchor
 * @param bool $echoit
 * @return string
 */
function wpml_link_to_element($element_id, $element_type = 'post', $link_text = '', $optional_parameters = array(), $anchor = '', $echoit = true) {
	if (!function_exists('icl_link_to_element')) {
		switch ($element_type) {
			case 'post':
			case 'page':
				$ret = '<a href="' . get_permalink($element_id) . '">';
				if ($anchor) {
					$ret .= $anchor;
				}
				else {
					$ret .= get_the_title($element_id);
				}
				$ret .= '<a>';
				break;
			case 'tag':
			case 'post_tag':
				$tag = get_term_by('id', $element_id, 'tag', ARRAY_A);
				$ret = '<a href="' . get_tag_link($element_id) . '">' . $tag->name . '</a>';
			case 'category':
				$ret = '<a href="' . get_tag_link($element_id) . '">' . get_the_category_by_ID($element_id) . '</a>';
			default:
				$ret = '';
		}
		if ($echoit) {
			echo $ret;
		}
		else {
			return $ret;
		}
	}
	else {
		return icl_link_to_element($element_id, $element_type, $link_text, $optional_parameters, $anchor, $echoit);
	}
}

/**
 * Languages links to display in the footer
 *
 * @param int $skip_missing
 * @param string $div_id
 * @return void
 */
function wpml_languages_list($skip_missing = 0, $div_id = "footer_language_list") {
	if (function_exists('icl_get_languages')) {
		$languages = icl_get_languages('skip_missing=' . intval($skip_missing));
		if (!empty($languages)) {
			echo '<div id="' . $div_id . '"><ul>';
			foreach ($languages as $l) {
				echo '<li>';
				if (!$l['active']) echo '<a href="' . $l['url'] . '">';
				echo '<img src="' . $l['country_flag_url'] . '" alt="' . $l['language_code'] . '" />';
				if (!$l['active']) echo '</a>';
				if (!$l['active']) echo '<a href="' . $l['url'] . '">';
				echo $l['native_name'];
				if (!$l['active']) echo ' (' . $l['translated_name'] . ')';
				if (!$l['active']) echo '</a>';
				echo '</li>';
			}
			echo '</ul></div>';
		}
	}
}

/**
 * Drop-down language selector
 *
 * @return void
 */
function wpml_languages_selector() {
	do_action('icl_language_selector');
}

/**
 * Displays the translated Admin string. This is used in conjunction with wpml_register_string, where wpml_register_string enables and interactive
 * admin back-end string to be translated and wpml_t lets the same be displayed on the front-end
 *
 * @param  $context
 * @param  $name
 * @param  $original_value
 * @return bool
 */
function wpml_t($context, $name, $original_value) {
	if (function_exists('icl_t')) {
		return icl_t($context, $name, $original_value);
	}
	else {
		return $original_value;
	}
}

/**
 * Registers a string for back-end translation. E.g. If you are configuring your back-end to display "Your Name" instead of "Name" in the
 * comment form, this function gives you the ability to translate "Your Name" from the WPML -> String Translation screen.
 *
 * @param  $context
 * @param  $name
 * @param  $value
 * @return void
 */
function wpml_register_string($context, $name, $value) {
	if (function_exists('icl_register_string') && trim($value)) {
		icl_register_string($context, $name, $value);
	}
}

function wpml_get_object_id($element_id, $element_type = 'post', $return_original_if_missing = false, $ulanguage_code = null) {
	if (function_exists('icl_object_id')) {
		return icl_object_id($element_id, $element_type, $return_original_if_missing, $ulanguage_code);
	}
	else {
		return $element_id;
	}
}

/**
 * Returns the default untranslated link if no translation is available.
 *  
 * @param  $anchor
 * @return string
 */
function wpml_default_link($anchor) {
	global $sitepress;
	$qv = false;

	if (is_single()) {
		$qv = 'p=' . get_the_ID();
	}
	elseif (is_page()) {
		$qv = 'page_id=' . get_the_ID();
	}
	elseif (is_tag()) {
		$tag = &get_term(intval(get_query_var('tag_id')), 'post_tag', OBJECT, 'display');
		$qv = 'tag=' . $tag->slug;
	}
	elseif (is_category()) {
		$qv = 'cat=' . get_query_var('cat');
	}
	elseif (is_year()) {
		$qv = 'year=' . get_query_var('year');
	}
	elseif (is_month()) {
		$qv = 'm=' . get_query_var('year') . sprintf('%02d', get_query_var('monthnum'));
	}
	elseif (is_day()) {
		$qv = 'm=' . get_query_var('year') . sprintf('%02d', get_query_var('monthnum')) . sprintf('%02d', get_query_var('day'));
	}
	elseif (is_search()) {
		$qv = 's=' . get_query_var('s');
	}
	elseif (is_tax()) {
		$qv = get_query_var('taxonomy') . '=' . get_query_var('term');
	}

	if (false !== strpos(wpml_get_home_url(), '?')) {
		$url_glue = '&';
	}
	else {
		$url_glue = '?';
	}

	if ($qv) {
		$link = '<a href="' . $sitepress->language_url($sitepress->get_default_language()) . $url_glue . $qv . '" rel="nofollow">' . $anchor . '</a>';
	}
	else {
		$link = '';
	}

	return $link;
}

?>