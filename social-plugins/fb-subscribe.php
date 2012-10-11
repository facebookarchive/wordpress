<?php

function fb_get_subscribe_button( $options = array() ) {
	if ( ! class_exists( 'Facebook_Subscribe_Button' ) )
		require_once( dirname(__FILE__) . '/class-facebook-subscribe-button.php' );

	$subscribe_button = Facebook_Subscribe_Button::fromArray( $options );
	if ( ! $subscribe_button )
		return '';

	$html = $subscribe_button->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( is_string($html) && $html )
		return "\n" . $html . "\n";

	return '';
}

function fb_subscribe_button_automatic( $content ) {
	global $post;

	$social_plugin_type = 'subscribe';

	if ( ! fb_show_social_plugin( $social_plugin_type ) )
		return $content;

	$options = fb_load_social_plugin_options( $social_plugin_type );
	if ( empty( $options ) )
		return $content;

	$fb_data = fb_get_user_meta( get_the_author_meta( 'ID' ), 'fb_data', true );
	if ( $fb_data ) {
		if ( array_key_exists( 'username' , $fb_data ) ) // prefer username URLs
			$options['href'] = 'https://www.facebook.com/' . $fb_data['username'];
		else if ( array_key_exists( 'fb_uid', $fb_data ) ) // use profile uid if no username
			$options['href'] = 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $fb_data['fb_uid'] ) );
	}

	if ( ! array_key_exists( 'href', $options ) )
		return $content;

	if ( array_key_exists( 'position', $options ) ) {
		switch ($options['position']) {
			case 'top':
				return fb_get_subscribe_button( $options ) . $content;
				break;
			case 'bottom':
				return $content . fb_get_subscribe_button( $options );
				break;
			case 'both':
				$subscribe_button = fb_get_subscribe_button( $options );
				return $subscribe_button . $content . $subscribe_button;
				break;
		}
	}

	return $content;
}

function fb_get_subscribe_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_subscribe_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_subscribe_fields_array($placement) {
	$array['parent'] = array(
		'name' => 'subscribe',
		'label' => __( 'Subscribe Button', 'facebook' ),
		'description' => __( 'The Subscribe Button lets a user subscribe to your public updates on Facebook. Each WordPress author must authenticate with Facebook in order for the Subscribe button to appear on their pages and posts.', 'facebook' ),
		'type' => 'checkbox',
		'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
		'image' => plugins_url( '/images/settings_subscribe_button.png', dirname(__FILE__) )
	);

	$array['children'] = array(
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
			'name' => 'show_faces',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Show profile pictures below the button.  Applicable to standard layout only.', 'facebook' )
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

	if ($placement == 'settings') {
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
		$array['children'][] = array(
			'name' => 'show_on',
			'type' => 'checkbox',
			'default' => array_fill_keys( array_keys($post_types) , 'true' ),
			'options' => $post_types,
			'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' )
		);
		$array['children'][] = array(
			'name' => 'show_on_homepage',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'If the plugin should appear on the homepage as part of the Post previews.  If unchecked, the plugin will only display on the Post itself.', 'facebook' )
		);
	}

	if ( $placement == 'widget' ) {
		$title_array = array(
			'name' => 'title',
			'type' => 'text',
			'help_text' => __( 'The title above the button.', 'facebook' )
		);
		$text_array = array(
			'name' => 'href',
			'type' => 'text',
			'default' => get_site_url(),
			'help_text' => __( 'The URL the Subscribe button will point to.', 'facebook' )
		);

		array_unshift($array['children'], $title_array, $text_array);
	}

	return $array;
}

include_once( dirname(__FILE__) . '/widgets/subscribe-button.php' );

?>
