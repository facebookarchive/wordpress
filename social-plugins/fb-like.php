<?php

/**
 * Generate HTML for a single Like Button
 *
 * @param array $options like button options
 * @return string HTML div for use with the JavaScript SDK
 */
function fb_get_like_button($options = array()) {
	if ( ! class_exists( 'Facebook_Like_Button' ) )
		require_once( dirname(__FILE__) . '/class-facebook-like-button.php' );

	$like_button = Facebook_Like_Button::fromArray( $options );
	if ( ! $like_button )
		return '';

	$html = $like_button->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( $html )
		return "\n" . $html . "\n";

	return '';
}

/**
 * Display one or more like buttons if current view context matches site preferences
 *
 * @param string $content existing content
 * @return string passed content with Like Button markup prepended, appended, or both.
 */
function fb_like_button_automatic( $content ) {
	global $post;

	$social_plugin_type = 'like';

	if ( ! fb_show_social_plugin( $social_plugin_type ) )
		return $content;

	$options = fb_load_social_plugin_options( $social_plugin_type );
	if ( empty( $options ) )
		return $content;

	if ( ! is_singular( get_post_type( $post ) ) )
		$options['href'] = apply_filters( 'fb_rel_canonical', get_permalink( $post->ID ) );

	if ( array_key_exists( 'position', $options ) ) {
		switch ($options['position']) {
			case 'top':
				return fb_get_like_button( $options ) . $content;
				break;
			case 'bottom':
				return $content . fb_get_like_button( $options );
				break;
			case 'both':
				$like_button = fb_get_like_button( $options );
				return $like_button . $content . $like_button;
				break;
		}
	}

	return $content;
}

function fb_get_like_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_like_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_like_fields_array($placement) {
	$array['parent'] = array(
		'name' => 'like',
		'label' => __( 'Like Button', 'facebook' ),
		'image' => plugins_url( '/images/settings_like_button.png', dirname(__FILE__) ),
		'description' => __( 'The Like Button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user\'s friends\' News Feed with a link back to your website.', 'facebook' ),
		'help_link' => 'https://developers.facebook.com/docs/reference/plugins/like/',
	);

	$array['children'] = array(
		array(
			'name' => 'send',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Include a send button.', 'facebook' )
		),
		array(
			'name' => 'show_faces',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Show profile pictures below the button. Applicable to standard layout only.', 'facebook' )
		),
		array(
			'name' => 'layout',
			'type' => 'dropdown',
			'default' => 'standard',
			'options' => array(
				'standard' => 'standard',
				'button_count' => 'button_count',
				'box_count' => 'box_count'
			),
			'help_text' => __( 'Determines the size and amount of social context at the bottom.', 'facebook' )
		),
		array(
			'name' => 'width',
			'type' => 'text',
			'default' => '450',
			'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
			'sanitization_callback' => 'intval'
		),
		array(
			'name' => 'action',
			'type' => 'dropdown',
			'default' => 'like',
			'options' => array(
				'like' => 'like',
				'recommend' => 'recommend'
			),
			'help_text' => __( 'The verb to display in the button.', 'facebook' )
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
			'help_text' => __( 'The color scheme of the button.', 'facebook' )
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
			'help_text' => __( 'The font of the button.', 'facebook' )
		)
	);

	if ( $placement == 'settings' ) {
		$array['children'][] = array(
			'name' => 'position',
			'type' => 'dropdown',
			'default' => 'both',
			'options' => array(
				'top' => 'top',
				'bottom' => 'bottom',
				'both' => 'both'
			),
			'help_text' => __( 'Where the button will display on the page or post.', 'facebook' )
		);
		$post_types = get_post_types(array('public' => true));
		//unset($post_types['attachment']);
		//$post_types = array_values($post_types);
		$array['children'][] = array(
			'name' => 'show_on',
			'type' => 'checkbox',
			'default' => array_fill_keys(array_keys($post_types) , 'true'),
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
			'help_text' => __( 'The URL the Like button will point to.', 'facebook' ),
			'sanitization_callback' => 'esc_url_raw'
		);

		array_unshift($array['children'], $title_array, $text_array);
	}

	return $array;
}

include_once( dirname(__FILE__) . '/widgets/like-button.php' );

?>
