<?php
function fb_get_activity_feed($options = array()) {
	if ( ! class_exists( 'Facebook_Activity_Feed' ) )
		require_once( dirname(__FILE__) . '/class-facebook-activity-feed.php' );

	$activity_feed = Facebook_Activity_Feed::fromArray( $options );
	if ( ! $activity_feed )
		return '';

	$html = $activity_feed->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( is_string( $html ) && $html )
		return $html;

	return '';
}

function fb_get_activity_feed_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_activity_feed_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], null, $object);
}

function fb_get_activity_feed_fields_array($placement) {
	$array['children'] = array(
		array(
			'name' => 'width',
			'type' => 'text',
			'default' => '300',
			'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
			'sanitization_callback' => 'absint'
		),
		array(
			'name' => 'height',
			'type' => 'text',
			'default' => '300',
			'help_text' => __( 'The height of the plugin, in pixels.', 'facebook' ),
			'sanitization_callback' => 'absint'
		),
		array(
			'name' => 'colorscheme',
			'label' => __( 'Color scheme', 'facebook' ),
			'type' => 'dropdown',
			'default' => 'light',
			'options' => array(
				'light' => 'light',
				'dark' => 'dark'
			),
			'help_text' => __( 'The color scheme of the plugin.', 'facebook' )
		),
		array(
			'name' => 'border_color',
			'type' => 'text',
			'default' => '#aaa',
			'help_text' => __( 'The border color scheme of the plugin.', 'facebook' )
		),
		array(
			'name' => 'font',
			'type' => 'dropdown',
			'default' => 'lucida grande',
			'options' => array(
				'arial' => 'arial',
				'lucida grande' => 'lucida grande',
				'segoe ui' => 'segoe ui',
				'tahoma' => 'tahoma',
				'trebuchet ms' => 'trebuchet ms',
				'verdana' => 'verdana'
			),
			'help_text' => __( 'The font of the plugin.', 'facebook' )
		),
		array(
			'name' => 'recommendations',
			'type' => 'checkbox',
			'default' => false,
			'help_text' => __( 'Includes recommendations.', 'facebook' )
		)
	);

	if ($placement == 'widget') {
		$title_array = array(
			'name' => 'title',
			'type' => 'text',
			'help_text' => __( 'The title above the button.', 'facebook' )
		);
		$header_array = array(
			'name' => 'header',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Show the default Facebook title header.', 'facebook' )
		);

		array_unshift( $array['children'], $title_array, $header_array );
	}

	return $array;
}

include_once( dirname(__FILE__) . '/widgets/activity-feed.php' );

?>