<?php
/**
 * Loads up all the widgets defined by Suffusion. This functionality will be released as a plugin in a future release.
 *
 * @package Suffusion
 * @subpackage Widgets
 */

if (!class_exists('Suffusion_Widgets')) {
	class Suffusion_Widgets {
		function Suffusion_Widgets() {
		}

		function init() {
			add_action("widgets_init", array(&$this, "load_widgets"));
		}

		function load_widgets() {
			$template_path = get_template_directory();
			include_once ($template_path . "/widget-areas.php");

			include_once ($template_path . '/widgets/suffusion-search.php');
			include_once ($template_path . '/widgets/suffusion-meta.php');
			include_once ($template_path . '/widgets/suffusion-twitter.php');
			include_once ($template_path . '/widgets/suffusion-query-posts.php');
			include_once ($template_path . '/widgets/suffusion-featured-posts.php');
			include_once ($template_path . '/widgets/suffusion-translator.php');
			include_once ($template_path . '/widgets/suffusion-subscription.php');
			include_once ($template_path . '/widgets/suffusion-flickr.php');
			include_once ($template_path . '/widgets/suffusion-query-users.php');
			include_once ($template_path . '/widgets/suffusion-child-pages.php');

			register_widget("Suffusion_Search");
			register_widget("Suffusion_Meta");
			register_widget("Suffusion_Follow_Twitter");
			register_widget("Suffusion_Category_Posts");
			register_widget("Suffusion_Featured_Posts");
			register_widget("Suffusion_Google_Translator");
			register_widget("Suffusion_Subscription");
			register_widget("Suffusion_Flickr");
			register_widget("Suffusion_Query_Users");
			register_widget("Suffusion_Child_Pages");
		}
	}
}
?>