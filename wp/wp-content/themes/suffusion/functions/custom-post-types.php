<?php
/**
 * Registers any custom post types defined in the theme prior to 4.0.0. Since Custom Post Types have been moved out of the
 * theme to their own plugin, this code is there only for backwards compatibility.
 *
 * @package Suffusion
 * @subpackage Functions
 */

global $suffusion_post_type_labels, $suffusion_post_type_args, $suffusion_post_type_supports, $suffusion_taxonomy_labels, $suffusion_taxonomy_args;
$suffusion_post_types = get_option('suffusion_post_types');
$suffusion_taxonomies = get_option('suffusion_taxonomies');
if (is_array($suffusion_post_types)) {
	foreach ($suffusion_post_types as $post_type) {
		$args = array();
		$labels = array();
		$supports = array();
		foreach ($suffusion_post_type_labels as $label) {
			if (isset($post_type['labels'][$label['name']]) && $post_type['labels'][$label['name']] != '') {
				$labels[$label['name']] = $post_type['labels'][$label['name']];
			}
		}
		foreach ($suffusion_post_type_supports as $support) {
			if (isset($post_type['supports'][$support['name']])) {
				if ($post_type['supports'][$support['name']] == '1') {
					$supports[] = $support['name'];
				}
			}
		}
		foreach ($suffusion_post_type_args as $arg) {
			if (isset($post_type['args'][$arg['name']])) {
				if ($arg['type'] == 'checkbox' && $post_type['args'][$arg['name']] == '1' && $arg['name'] == 'has_archive') {
					$args[$arg['name']] = $post_type['post_type'];
				}
				else if ($arg['type'] == 'checkbox' && $post_type['args'][$arg['name']] == '1') {
					$args[$arg['name']] = true;
				}
				else if ($arg['type'] != 'checkbox') {
					$args[$arg['name']] = $post_type['args'][$arg['name']];
				}
			}
		}
		$args['labels'] = $labels;
		$args['supports'] = $supports;
		register_post_type($post_type['post_type'], $args);
	}
}

if (is_array($suffusion_taxonomies)) {
	foreach ($suffusion_taxonomies as $taxonomy) {
		$labels = array();
		$args = array();
		foreach ($suffusion_taxonomy_labels as $label) {
			if (isset($taxonomy['labels'][$label['name']]) && $taxonomy['labels'][$label['name']] != '') {
				$labels[$label['name']] = $taxonomy['labels'][$label['name']];
			}
		}
		foreach ($suffusion_taxonomy_args as $arg) {
			if (isset($taxonomy['args'][$arg['name']])) {
				if ($arg['type'] == 'checkbox' && $taxonomy['args'][$arg['name']] == '1') {
					$args[$arg['name']] = true;
				}
				else if ($arg['type'] != 'checkbox') {
					$args[$arg['name']] = $taxonomy['args'][$arg['name']];
				}
			}
		}
		$args['labels'] = $labels;
		$object_type_str = $taxonomy['object_type'];
		$object_type_array = explode(',',$object_type_str);
		$object_types = array();
		foreach ($object_type_array as $object_type) {
			if (post_type_exists(trim($object_type))) {
				$object_types[] = trim($object_type);
			}
		}
		register_taxonomy($taxonomy['taxonomy'], $object_types, $args);
	}
}
