<?php

/**
 * Adds the Recommendations Social Plugin as a WordPress Widget
 */
class Facebook_Recommendations_Widget extends WP_Widget {

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
			echo $before_title . esc_html( $instance['title'] ) . $after_title;

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
			if ( ! empty( $field['sanitization_callback'] ) && function_exists( $field['sanitization_callback'] ) ) 
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

?>