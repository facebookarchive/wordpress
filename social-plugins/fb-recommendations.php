<?php
function fb_get_recommendations_box($options = array()) {
	if (empty($options['header'])) {
		$options['header'] = 'false';
	}

	$params = fb_build_social_plugin_params($options);

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
			echo $before_title . esc_attr($instance['title']) . $after_title;

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
		$return_instance = $old_instance;
		
		$fields = fb_get_recommendations_box_fields_array('widget');
		
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
		fb_get_recommendations_box_fields('widget', $this);
	}
}


function fb_get_recommendations_box_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_recommendations_box_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], null, $object);
}

function fb_get_recommendations_box_fields_array($placement) {
	$array['children'] = array(array('name' => 'width',
													'type' => 'text',
													'default' => '300',
													'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
													'sanitization_callback' => 'intval',
													),
										array('name' => 'height',
													'type' => 'text',
													'default' => '300',
													'help_text' => __( 'The height of the plugin, in pixels.', 'facebook' ),
													'sanitization_callback' => 'intval',
													),
										array('name' => 'colorscheme',
													'label' => 'Color scheme',
													'type' => 'dropdown',
													'default' => 'light',
													'options' => array('light' => 'light', 'dark' => 'dark'),
													'help_text' => __( 'The color scheme of the plugin.', 'facebook' ),
													),
										array('name' => 'border_color',
													'type' => 'text',
													'default' => '#aaa',
													'help_text' => __( 'The border color scheme of the plugin (hex or text value).', 'facebook' ),
													),
										array('name' => 'font',
													'type' => 'dropdown',
													'default' => 'lucida grande',
													'options' => array('arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana'),
													'help_text' => __( 'The font of the plugin.', 'facebook' ),
													),
										);

	if ($placement == 'widget') {
		$title_array = array('name' => 'title',
													'type' => 'text',
													'help_text' => 'The title above the button.',
													);
		$header_array = array('name' => 'header',
													'type' => 'checkbox',
													'default' => true,
													'help_text' => 'Show the default Facebook title header.',
													);

		array_unshift($array['children'], $title_array, $header_array);
	}

	return $array;
}
?>