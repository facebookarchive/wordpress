<?php

/**
 * Generate HTML for a send button based on passed options
 *
 * @param array $options customizations
 * @return string send button HTML for use with the JavaScript SDK
 */
function fb_get_send_button($options = array()) {
	if ( ! class_exists( 'Facebook_Send_Button' ) )
		require_once( dirname(__FILE__) . '/class-facebook-send-button.php' );

	$send_button = Facebook_Send_Button::fromArray( $options );
	if ( ! $send_button )
		return '';

	$html = $send_button->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( $html )
		return "\n" . $html . "\n";

	return '';
}

function fb_send_button_automatic( $content ) {
	global $post;

	$social_plugin_type = 'send';

	if ( ! fb_show_social_plugin( $social_plugin_type ) )
		return $content;

	$options = fb_load_social_plugin_options( $social_plugin_type );
	if ( empty( $options ) )
		return $content;
	
	if ( isset( $options['show_on_homepage'] ) )
		$options['href'] = get_permalink($post->ID);

	switch ( $options['position'] ) {
		case 'top':
			return fb_get_send_button( $options ) . $content;
			break;
		case 'bottom':
			return $content . fb_get_send_button( $options );
			break;
		case 'both':
			$send_button = fb_get_send_button( $options );
			return $send_button . $content . $send_button;
	}

	return $content;
}

function fb_get_send_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_send_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_send_fields_array($placement) {
	$array['parent'] = array(
		'name' => 'send',
		'type' => 'checkbox',
		'label' => __( 'Send Button', 'facebook' ),
		'description' => __( 'The Send Button allows users to easily send content to their friends. People will have the option to send your URL in a message to their Facebook friends, to the group wall of one of their Facebook groups, and as an email to any email address.', 'facebook' ),
		'help_link' => 'https://developers.facebook.com/docs/reference/plugins/send/',
		'image' => plugins_url( '/images/settings_send_button.png', dirname(__FILE__) )
	);

	$array['children'] = array(
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
			'help_text' => __( 'The font of the plugin.', 'facebook' ),
		)
	);

	if ($placement == 'settings') {
		$array['children'][] = array(
			'name' => 'position',
			'type' => 'dropdown',
			'default' => 'both',
			'options' => array('top' => 'top', 'bottom' => 'bottom', 'both' => 'both'),
			'help_text' => __( 'Where the button will display on the page or post.', 'facebook' )
		);

		$post_types = get_post_types(array('public' => true));
		$array['children'][] = array(
			'name' => 'show_on',
			'type' => 'checkbox',
			'default' => array_fill_keys( array_keys($post_types), 'true' ),
			'options' => $post_types,
			'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' )
		);
		$array['children'][] = array(
			'name' => 'show_on_homepage',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'If the plugin should appear on the homepage as part of the Post previews. If unchecked, the plugin will only display on the Post itself.', 'facebook' )
		);
	}

	if ($placement == 'widget') {
		$title_array = array(
			'name' => 'title',
			'type' => 'text',
			'help_text' => __( 'The title above the button.', 'facebook' )
		);
		$text_array = array(
			'name' => 'href',
			'label' => 'URL',
			'type' => 'text',
			'default' => get_site_url(),
			'help_text' => __( 'The URL the Send button will point to.', 'facebook' ),
			'sanitization_callback' => 'esc_url_raw'
		);

		array_unshift($array['children'], $title_array, $text_array);
	}

	return $array;
}

include_once( dirname(__FILE__) . '/widgets/send-button.php' );

?>
