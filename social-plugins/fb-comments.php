<?php

function fb_hide_wp_comments() {
	wp_enqueue_style( 'fb_hide_wp_comments', plugins_url( 'style/hide-wp-comments.min.css', dirname(__FILE__) ), array(), '1.0' );
}

function fb_get_comments_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_comments_fields_array();

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_comments_fields_array() {
	$array['parent'] = array(
		'name' => 'comments',
		'type' => 'checkbox',
		'label' => __( 'Comments', 'facebook' ),
		'description' => __( 'The Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution.', 'facebook' ),
		'help_link' => 'https://developers.facebook.com/docs/reference/plugins/comments/',
		'image' => plugins_url( '/images/settings_comments.png', dirname(__FILE__) )
	);
	$post_types = get_post_types(array('public' => true));
	$array['children'] = array(
		array(
			'name' => 'num_posts',
			'label' => __( 'Number of posts', 'facebook' ),
			'type' => 'text',
			'default' => 20,
			'help_text' => __( 'The number of posts to display by default.', 'facebook' )
		),
		array(
			'name' => 'width',
			'type' => 'text',
			'default' => '470',
			'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' )
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
			'name' => 'show_on',
			'type' => 'checkbox',
			'default' => array_fill_keys( array_keys($post_types) , 'true' ),
			'options' => $post_types,
			'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' ),
		),
		array(
			'name' => 'homepage_comments',
			'label' => __( 'Show comment counts on the homepage', 'facebook' ),
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Whether the plugin will display a comment count for each post on the homepage.', 'facebook' ),
		)
	);

	return $array;
}

?>
