<?php
/**
 * Display the Like button.
 * More info at https://developers.facebook.com/docs/reference/plugins/like/
 *
 * @param array $enable_send      Enable send button (bool).
 * @param array $layout_style     Layout style, 'standard', 'button_count', or 'box_count'.
 * @param array $width            Width of button area.
 * @param array $show_faces       Show photos of friends that have like the URL.
 * @param array $verb_to_display  Verb to display, 'like' or 'recommend'.
 * @param array $color_scheme     Color scheme, 'light' or 'dark'.
 * @param array $font             Font, 'arial', 'lucida grande', 'segoe ui', 'tahoma', trebuchet ms', 'verdana'.
 * @param array $url              Optional. If not provided, current URL used.
 */

function fb_get_like_button($options = array()) {
	$params = fb_build_social_plugin_params($options);
	
	return '<div class="fb-like fb-social-plugin" ' . $params . ' ></div>';
}

function fb_like_button_automatic($content) {
	$options = get_option('fb_options');
	
	global $post;
	
	if ( isset ( $post ) ) {
		if ( isset($options['like']['show_on_homepage']) ) {
			$options['like']['href'] = get_permalink($post->ID);
		}
		
		$new_content = '';
	
		switch ($options['like']['position']) {
			case 'top':
				$new_content = fb_get_like_button($options['like']) . $content;
				break;
			case 'bottom':
				$new_content = $content . fb_get_like_button($options['like']);
				break;
			case 'both':
				$new_content = fb_get_like_button($options['like']) . $content;
				$new_content .= fb_get_like_button($options['like']);
				break;
		}
	
		$show_indiv = get_post_meta( $post->ID, 'fb_social_plugin_settings_box_like', true );
		
		if ( is_home() && isset ( $options['like']['show_on_homepage'] ) ) {
			$content = $new_content;
		}
		elseif ( ( 'default' == $show_indiv || empty( $show_indiv ) ) && isset ( $options['like']['show_on'] ) ) {		
			if ( is_page() && ( $options['like']['show_on'] == 'all pages' || $options['like']['show_on'] == 'all posts and pages' ) )
				$content = $new_content;
			elseif ( is_single() && ( $options['like']['show_on'] == 'all posts' || $options['like']['show_on'] == 'all posts and pages' ) )
				$content = $new_content;
		}
		elseif ( 'show' == $show_indiv ) {
			$content = $new_content;
		}
	}

	return $content;
}

/**
 * Adds the Like Button Social Plugin as a WordPress Widget
 */
class Facebook_Like_Button extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_like', // Base ID
			__( 'Facebook Like Button', 'facebook' ), // Name
			array( 'description' => __( 'Lets a user share your content with friends on Facebook.', 'facebook' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		if ( ! empty( $instance['title'] ) )
			echo $before_title . esc_attr($instance['title']) . $after_title;

		echo fb_get_like_button($instance);
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$return_instance = $old_instance;
		
		$fields = fb_get_like_fields_array('widget');
		
		foreach( $fields['children'] as $field ) {
			$unsafe_value = ( isset( $new_instance[$field['name']] ) ) ? $new_instance[$field['name']] : '';
			if ( !empty( $field['sanitization_callback'] ) && function_exists( $field['sanitization_callback'] ) ) 
				$return_instance[$field['name']] = $field['sanitization_callback']( $unsafe_value );
			else
				$return_instance[$field['name']] = sanitize_text_field( $unsafe_value );
		}
		return $return_instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		fb_get_like_fields('widget', $this);
	}
}

function fb_get_like_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_like_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_like_fields_array($placement) {
	$array['parent'] = array('name' => 'like',
														'label' => 'Like Button',
														'image' => plugins_url( '/images/settings_like_button.png', dirname(__FILE__)),
														'description' => 'The Like Button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user\'s friends\' News Feed with a link back to your website.',
														'help_link' => 'https://developers.facebook.com/docs/reference/plugins/like/',
														);

	$array['children'] = array(array('name' => 'send',
													'type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'Include a send button.', 'facebook' ),
													),
										array('name' => 'show_faces',
													'type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'Show profile pictures below the button.  Applicable to standard layout only.', 'facebook' ),
													),
										array('name' => 'layout',
													'type' => 'dropdown',
													'default' => 'standard',
													'options' => array('standard' => 'standard', 'button_count' => 'button_count', 'box_count' => 'box_count'),
													'help_text' => __( 'Determines the size and amount of social context at the bottom.', 'facebook' ),
													),
										array('name' => 'width',
													'type' => 'text',
													'default' => '450',
													'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
													'sanitization_callback' => 'intval',
													),
										array('name' => 'action',
													'type' => 'dropdown',
													'default' => 'like',
													'options' => array('like' => 'like', 'recommend' => 'recommend'),
													'help_text' => __( 'The verb to display in the button.', 'facebook' ),
													),
										array('name' => 'colorscheme',
													'label' => 'Color scheme',
													'type' => 'dropdown',
													'default' => 'light',
													'options' => array('light' => 'light', 'dark' => 'dark'),
													'help_text' => __( 'The color scheme of the button.', 'facebook' ),
													),
										array('name' => 'font',
													'type' => 'dropdown',
													'default' => 'lucida grande',
													'options' => array('arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana'),
													'help_text' => __( 'The font of the button.', 'facebook' ),
													),
										);

	if ($placement == 'settings') {
		$array['children'][] = array('name' => 'position',
													'type' => 'dropdown',
													'default' => 'both',
													'options' => array('top' => 'top', 'bottom' => 'bottom', 'both' => 'both'),
													'help_text' => __( 'Where the button will display on the page or post.', 'facebook' ),
													);
		$array['children'][] = array('name' => 'show_on',
													'type' => 'dropdown',
													'default' => 'all posts and pages',
													'options' => array('all posts' => 'all posts', 'all pages' => 'all pages', 'all posts and pages' => 'all posts and pages', 'individual posts and pages' => 'individual posts and pages' ),
													'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' ),
													);
		$array['children'][] = array('name' => 'show_on_homepage',
													'type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'If the plugin should appear on the homepage as part of the Post previews.  If unchecked, the plugin will only display on the Post itself.', 'facebook' ),
													);
	}

	if ($placement == 'widget') {
		$title_array = array('name' => 'title',
													'type' => 'text',
													'help_text' => __( 'The title above the button.', 'facebook' ),
													);
		$text_array = array('name' => 'href',
													'label' => 'URL',
													'type' => 'text',
													'default' => get_site_url(),
													'help_text' => __( 'The URL the Like button will point to.', 'facebook' ),
													'sanitization_callback' => 'esc_url_raw',
													);

		array_unshift($array['children'], $title_array, $text_array);
	}

	return $array;
}

?>
