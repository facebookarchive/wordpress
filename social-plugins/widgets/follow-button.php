<?php
/**
 * Adds the Follow Button Social Plugin as a WordPress Widget
 *
 * @since 1.0
 */
class Facebook_Follow_Button_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-follow', // Base ID
			__( 'Facebook Follow Button', 'facebook' ), // Name
			array( 'description' => __( 'Lets a user follow your public updates on Facebook.', 'facebook' ) ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @since 1.0
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		// no follow target. fail early
		if ( empty( $instance['href'] ) )
			return;

		extract( $args );

		if ( ! isset( $instance['ref'] ) )
			$instance['ref'] = 'widget';

		if ( ! function_exists( 'facebook_get_follow_button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins.php' );

		$follow_button_html = facebook_get_follow_button( $instance );
		if ( ! ( is_string( $follow_button_html ) && $follow_button_html ) )
			return;

		echo $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( $title )
			echo $before_title . $title . $after_title;

		echo $follow_button_html;

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 1.0
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
		$new_instance = (array) $new_instance;

		if ( ! empty( $new_instance['title'] ) )
			$instance['title'] = strip_tags( $new_instance['title'] );

		if ( isset( $new_instance['show_faces'] ) )
			$new_instance['show_faces'] = true;
		else
			$new_instance['show_faces'] = false;

		if ( isset( $new_instance['width'] ) )
			$new_instance['width'] = absint( $new_instance['width'] );

		if ( ! class_exists( 'Facebook_Follow_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-follow-button.php' );

		$follow_button = Facebook_Follow_Button::fromArray( $new_instance );
		if ( $follow_button ) {
			if ( ! class_exists( 'Facebook_Follow_Button_Settings' ) )
				require_once( dirname( dirname( dirname(__FILE__) ) ) . '/admin/settings-follow-button.php' );

			return array_merge( $instance, Facebook_Follow_Button_Settings::html_data_to_options( $follow_button->toHTMLDataArray() ) );
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @since 1.0
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'href' => '',
			'layout' => 'standard',
			'show_faces' => false,
			'colorscheme' => 'light',
			'font' => '',
			'width' => 0
		) );

		$this->display_title( isset( $instance['title'] ) ? $instance['title'] : '' );
		$this->display_href( isset( $instance['href'] ) ? $instance['href'] : '' );

		if ( ! class_exists( 'Facebook_Follow_Button_Settings' ) )
			require_once( dirname( dirname( dirname(__FILE__) ) ) . '/admin/settings-follow-button.php' );

		$follow_button_settings = new Facebook_Follow_Button_Settings( $instance );

		echo '<div>' . esc_html( __( 'Layout', 'facebook' ) ) . ': ';
		$follow_button_settings->display_layout( array(
			'id' => $this->get_field_id( 'layout' ),
			'name' => $this->get_field_name( 'layout' )
		) );
		echo '</div><p></p>';

		echo '<div>';
		$follow_button_settings->display_show_faces( array(
			'id' => $this->get_field_id( 'show_faces' ),
			'name' => $this->get_field_name( 'show_faces' )
		) );
		echo '</div><p></p>';

		echo '<div><label for="' . $this->get_field_id( 'width' ) . '">' . esc_html( __( 'Width', 'facebook' ) ) . '</label>: ';
		$follow_button_settings->display_width( array(
			'id' => $this->get_field_id( 'width' ),
			'name' => $this->get_field_name( 'width' )
		) );
		echo '</div><p></p>';

		echo '<div><label for="' . $this->get_field_id( 'font' ) . '">' . esc_html( __( 'Font', 'facebook' ) ) . '</label>: ';
		$follow_button_settings->display_font( array(
			'id' => $this->get_field_id( 'font' ),
			'name' => $this->get_field_name( 'font' )
		) );
		echo '</div><p></p>';

		echo '<div style="line-height:2em">' . esc_html( __( 'Color scheme', 'facebook' ) ) . ': ';
		$follow_button_settings->display_colorscheme( array(
			'id' => $this->get_field_id( 'colorscheme' ),
			'name' => $this->get_field_name( 'colorscheme' )
		) );
		echo '</div>';
	}

	/**
	 * Allow a publisher to customize the title displayed above the widget area
	 *
	 * e.g. Like us on Facebook!
	 *
	 * @since 1.1
	 *
	 * @param string $existing_value saved title
	 * @return void
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
	 *
	 * @param string $existing_value stored URL value
	 * @return void
	 */
	public function display_href( $existing_value = '' ) {
		echo '<p><label>URL: <input type="url" id="' . $this->get_field_id( 'href' ) . '" name="' . $this->get_field_name( 'href' ) . '" class="widefat" required';
		if ( $existing_value )
			echo ' value="' . esc_url( $existing_value, array( 'http', 'https' ) ) . '"';
		echo ' /></label></p>';

		echo '<p class="description">' . esc_html( __( 'Must be a Facebook URL', 'facebook' ) ) . '</p>';
	}
}
?>
