<?php
global $suffusion_custom_types_options;
$suffusion_custom_types_options = array(
	array("name" => "Custom Types",
		"type" => "sub-section-2",
		"category" => "custom-types",
		"parent" => "root"
	),

	array("name" => "Custom Types",
		"type" => "sub-section-3",
		"category" => "placeholder",
		"buttons" => 'no-buttons',
		"parent" => "custom-types"
	),

	array("name" => "I Have Moved!!",
		"desc" => "With Version 4.0.0 the options for Custom Post Types are no longer included with the theme. You can download the 
			<a href='http://wordpress.org/extend/plugins/suffusion-custom-post-types/'>Suffusion Custom Post Types</a> plugin.
			Even if you don't use the plugin, post types and taxonomies that you previously saved with the theme will continue to operate fine.",
		"parent" => "placeholder",
		"type" => "blurb"),
);
?>