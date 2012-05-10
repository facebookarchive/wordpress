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
	$params = '';

	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}

	return '<div class="fb-like fb-social-plugin" ' . $params . ' ></div>';
}

function fb_like_button_automatic($content) {
	$options = get_option('fb_options');

	foreach($options['like'] as $param => $val) {
		$options['like']['data-' . $param] =  $val;
	}

	switch ($options['like']['position']) {
		case 'top':
			$content = fb_get_like_button($options['like']) . $content;
			break;
		case 'bottom':
			$content .= fb_get_like_button($options['like']);
			break;
		case 'both':
			$content = fb_get_like_button($options['like']) . $content;
			$content .= fb_get_like_button($options['like']);
			break;
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
			'Facebook Like Button', // Name
			array( 'description' => __( "Lets a user share your content with friends on Facebook.", 'text_domain' ), ) // Args
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
			echo $before_title . $instance['title'] . $after_title;

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
		return $new_instance;
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
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/like/',
									);

	$array['children'] = array(array('name' => 'send',
													'field_type' => 'checkbox',
													'help_text' => 'Include a send button.',
													),
										array('name' => 'show_faces',
													'field_type' => 'checkbox',
													'help_text' => 'Show profile pictures below the button.  Applicable to standard layout only.',
													),
										array('name' => 'layout',
													'field_type' => 'dropdown',
													'options' => array('standard', 'button_count', 'box_count'),
													'help_text' => 'Determines the size and amount of social context at the bottom.',
													),
										array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'action',
													'field_type' => 'dropdown',
													'options' => array('like', 'recommend'),
													'help_text' => 'The verb to display in the button.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the button.',
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'options' => array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'),
													'help_text' => 'The font of the button.',
													),
										);

	if ($placement == 'settings') {
		$array['children'][] = array('name' => 'position',
													'field_type' => 'dropdown',
													'options' => array('top', 'bottom', 'both'),
													'help_text' => 'Where the button will display on the page or post.',
													);
	}

	if ($placement == 'widget') {
		$array['children'][] = array('name' => 'href',
													'field_type' => 'text',
													'help_text' => 'The URL the Like button will point to.',
													);

		$array['children'][] = array('name' => 'title',
													'field_type' => 'text',
													'help_text' => 'The title above the button.',
													);
	}

	return $array;
}

?>