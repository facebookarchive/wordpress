<?php
/**
 * Adds the Send Button Social Plugin as a WordPress Widget
 *
 * @since 1.0
 */
class Facebook_Send_Button_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-send', // Base ID
			__( 'Facebook Send Button', 'facebook' ), // Name
			array( 'description' => __( 'The Send Button allows users to easily send content to their friends.', 'facebook' ), ) // Args
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

		if ( ! isset( $instance['ref'] ) )
			$instance['ref'] = 'widget';

		if ( ! function_exists( 'facebook_get_send_button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins.php' );
		$send_button_html = facebook_get_send_button( $instance );

		if ( ! ( is_string( $send_button_html ) && $send_button_html ) )
			return;

		echo $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( $title )
			echo $before_title . esc_html( $title ) . $after_title;

		echo $send_button_html;

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

		if ( ! empty( $new_instance['title'] ) )
			$instance['title'] = strip_tags( $new_instance['title'] );

		if ( ! class_exists( 'Facebook_Send_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-send-button.php' );

		$send_button = Facebook_Send_Button::fromArray( $new_instance );
		if ( $send_button ) {
			$send_button_options = $send_button->toHTMLDataArray();
			foreach( $send_button_options as $key => $value ) {
				$instance[ str_replace( '-', '_', $key ) ] = $value;
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
		$this->display_title( isset( $instance['title'] ) ? $instance['title'] : '' );
		$this->display_href( isset( $instance['href'] ) ? $instance['href'] : '' );

		if ( ! class_exists( 'Facebook_Send_Button_Settings' ) )
			require_once( dirname( dirname( dirname(__FILE__) ) ) . '/admin/settings-send-button.php' );

		$send_button_settings = new Facebook_Send_Button_Settings( $instance );

		echo '<div><label for="' . $this->get_field_id( 'font' ) . '">' . esc_html( __( 'Font', 'facebook' ) ) . '</label>: ';
		$send_button_settings->display_font( array(
			'id' => $this->get_field_id( 'font' ),
			'name' => $this->get_field_name( 'font' )
		) );
		echo '</div><p></p>';

		echo '<div style="line-height:2em">' . esc_html( __( 'Color scheme', 'facebook' ) ) . ': ';
		$send_button_settings->display_colorscheme( array(
			'id' => $this->get_field_id( 'colorscheme' ),
			'name' => $this->get_field_name( 'colorscheme' )
		) );
		echo '</div>';
	}

	/**
	 * Allow a publisher to customize the title displayed above the widget area
	 * e.g. Like us on Facebook!
	 *
	 * @since 1.1
	 * @param string $existing_value saved title
	 */
	public function display_title( $existing_value = '' ) {
		echo '<p><label>' . esc_html( __( 'Title', 'facebook' ) ) . ': ';
		echo '<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat"';
		if ( $existing_value )
			echo ' value="' . esc_attr( $existing_value ) . '"';
		echo ' /></label></p>';
	}

	/**
	 * Customize the Like target
	 *
	 * @since 1.1
	 * @param string $existing_value stored URL value
	 */
	public function display_href( $existing_value = '' ) {
		echo '<p><label>URL: <input type="url" id="' . $this->get_field_id( 'href' ) . '" name="' . $this->get_field_name( 'href' ) . '" class="widefat"';
		if ( $existing_value )
			echo ' value="' . esc_url( $existing_value, array( 'http', 'https' ) ) . '"';
		echo ' /></label></p>';

		echo '<p class="description">' . esc_html( __( 'Default: URL of the displayed page', 'facebook' ) ) . '</p>';
	}
}
?>