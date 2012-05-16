<?php
function fb_get_recommendations_box($options = array()) {
	$params = '';

	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}

	if (empty($options['header'])) {
		$params .= 'data-header="false" ';
	}

	$params .= 'data-ref="wp" ';

	return '<div class="fb-recommendations fb-social-plugin" ' . $params . '></div>';
}

/**
 * Adds the Recommendations Social Plugin as a WordPress Widget
 */
class Facebook_Recommendations extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_recommendations', // Base ID
			__( 'Facebook Recommendations', 'facebook' ), // Name
			array( 'description' => __( 'Shows personalized recommendations to your users.', 'facebook' ), ) // Args
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

		echo fb_get_recommendations_box($instance);
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
		fb_get_recommendations_box_fields('widget', $this);
	}
}


function fb_get_recommendations_box_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_recommendations_box_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], null, $object);
}

function fb_get_recommendations_box_fields_array($placement) {
	$array['children'] = array(array('name' => 'width',
													'field_type' => 'text',
													'default' => '250',
													'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
													),
										array('name' => 'height',
													'field_type' => 'text',
													'default' => '450',
													'help_text' => __( 'The height of the plugin, in pixels.', 'facebook' ),
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'default' => 'light',
													'options' => array('light' => 'light', 'dark' => 'dark'),
													'help_text' => __( 'The color scheme of the plugin.', 'facebook' ),
													),
										array('name' => 'border_color',
													'field_type' => 'text',
													'default' => '#aaa',
													'help_text' => __( 'The border color scheme of the plugin (hex or text value).', 'facebook' ),
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'default' => 'arial',
													'options' => array('arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana'),
													'help_text' => __( 'The font of the plugin.', 'facebook' ),
													),
										);

	if ($placement == 'widget') {
		$title_array = array('name' => 'title',
													'field_type' => 'text',
													'help_text' => 'The title above the button.',
													);
		$header_array = array('name' => 'header',
													'field_type' => 'checkbox',
													'default' => true,
													'help_text' => 'Show the default Facebook title header.',
													);

		array_unshift($array['children'], $title_array, $header_array);
	}

	return $array;
}
?>