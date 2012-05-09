<?php
function fb_get_recommendations_box($options = array()) {
	$params = '';

	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}

	return '<div class="fb-recommendations" ' . $params . '></div>';
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
			'Facebook Recommendations', // Name
			array( 'description' => __( "Shows personalized recommendations to your users.", 'text_domain' ), ) // Args
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

		//$options = array('data-href' => $instance['url']);
		
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
		$instance = array();
		
		$fields = fb_get_recommendations_box_fields_array();
		
		foreach ($fields['children'] as $field) {
			if (isset($new_instance[$field['name']])) {
				$instance[$field['name']] = $new_instance[$field['name']];
			}
		}

		return $instance;
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
	$fields_array = fb_get_recommendations_box_fields_array();
	
	fb_construct_fields($placement, $fields_array['children'], null, $object);
}

function fb_get_recommendations_box_fields_array() {
	$array['children'] = array(array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'height',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'border_color',
													'field_type' => 'text',
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'options' => array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'),
													'help_text' => 'The font of the plugin.',
													),
										);
	
	return $array;
}
?>