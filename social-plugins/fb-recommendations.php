<?php
function fb_get_recommendations_box($options = array()) {
	if ( ! class_exists( 'Facebook_Recommendations_Box' ) )
		require_once( dirname(__FILE__) . '/class-facebook-recommendations-box.php' );

	$box = Facebook_Recommendations_Box::fromArray( $options );
	if ( ! $box )
		return '';

	$html = $box->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( is_string( $html ) && $html )
		return $html;

	return '';
}

function fb_get_recommendations_box_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_recommendations_box_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], null, $object);
}

function fb_get_recommendations_box_fields_array($placement) {
	$array['children'] = array(
		array(
			'name' => 'width',
			'type' => 'text',
			'default' => '300',
			'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
			'sanitization_callback' => 'intval'
		),
		array(
			'name' => 'height',
			'type' => 'text',
			'default' => '300',
			'help_text' => __( 'The height of the plugin, in pixels.', 'facebook' ),
			'sanitization_callback' => 'intval'
		),
		array(
			'name' => 'colorscheme',
			'label' => 'Color scheme',
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
			'help_text' => __( 'The border color scheme of the plugin (hex or text value).', 'facebook' )
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

		array_unshift($array['children'], $title_array, $header_array);
	}

	return $array;
}

include_once( dirname(__FILE__) . '/widgets/recommendations-box.php' );

?>