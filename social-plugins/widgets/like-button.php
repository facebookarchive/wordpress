<?php

/**
 * Adds the Like Button Social Plugin as a WordPress Widget
 *
 * @since 1.0
 */
class Facebook_Like_Button_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-like', // Base ID
			__( 'Facebook Like Button', 'facebook' ), // Name
			array(
				'classname' => 'widget_facebook_like',
				'description' => __( 'Lets a viewer share your content to his or her Facebook timeline.', 'facebook' ) ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @since 1.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		// identify which like button placement led to action
		if ( ! isset( $instance['ref'] ) )
			$instance['ref'] = 'widget';

		if ( ! class_exists( 'Facebook_Like_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-button.php' );

		$like_button = Facebook_Like_Button::fromArray( $instance );
		if ( ! $like_button )
			return;

		$like_button_html = $like_button->asHTML();
		if ( ! ( is_string( $like_button_html ) && $like_button_html ) )
			return;

		echo $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( $title )
			echo $before_title . esc_html( $title ) . $after_title;

		echo $like_button_html;

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @since 1.0
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

		foreach( array( 'share', 'show_faces' ) as $bool_option ) {
			if ( isset( $new_instance[ $bool_option ] ) )
				$new_instance[ $bool_option ] = true;
			else
				$new_instance[ $bool_option ] = false;
		}

		if ( ! class_exists( 'Facebook_Like_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-button.php' );

		$like_button = Facebook_Like_Button::fromArray( $new_instance );
		if ( $like_button ) {
			if ( ! class_exists( 'Facebook_Like_Button_Settings' ) )
				require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin/settings-like-button.php' );

			return array_merge( $instance, Facebook_Like_Button_Settings::html_data_to_options( $like_button->toHTMLDataArray() ) );
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @since 1.0
	 *
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'href' => '',
			'share' => false,
			'layout' => 'standard',
			'show_faces' => false,
			'width' => 0,
			'action' => 'like',
			'font' => '',
			'colorscheme' => 'light'
		) );

		$this->display_title( $instance['title'] );
		$this->display_href( $instance['href'] );

		if ( ! class_exists( 'Facebook_Like_Button_Settings' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin/settings-like-button.php' );

		$like_button_settings = new Facebook_Like_Button_Settings( $instance );

		echo '<div>';
		$like_button_settings->display_share( array(
			'id' => $this->get_field_id( 'share' ),
			'name' => $this->get_field_name( 'share' )
		) );
		echo '</div><p></p>';

		echo '<div>' . esc_html( __( 'Layout', 'facebook' ) ) . ': ';
		$like_button_settings->display_layout( array(
			'id' => $this->get_field_id( 'layout' ),
			'name' => $this->get_field_name( 'layout' )
		) );
		echo '</div><p></p>';

		echo '<div>';
		$like_button_settings->display_show_faces( array(
			'id' => $this->get_field_id( 'show_faces' ),
			'name' => $this->get_field_name( 'show_faces' )
		) );
		echo '</div><p></p>';

		echo '<div><label for="' . $this->get_field_id( 'width' ) . '">' . esc_html( __( 'Width', 'facebook' ) ) . '</label>: ';
		$like_button_settings->display_width( array(
			'id' => $this->get_field_id( 'width' ),
			'name' => $this->get_field_name( 'width' )
		) );
		echo '</div><p></p>';

		echo '<div>' . esc_html( __( 'Action', 'facebook' ) ) . ': ';
		$like_button_settings->display_action( array(
			'id' => $this->get_field_id( 'action' ),
			'name' => $this->get_field_name( 'action' )
		) );
		echo '</div><p></p>';

		echo '<div><label for="' . $this->get_field_id( 'font' ) . '">' . esc_html( __( 'Font', 'facebook' ) ) . '</label>: ';
		$like_button_settings->display_font( array(
			'id' => $this->get_field_id( 'font' ),
			'name' => $this->get_field_name( 'font' )
		) );
		echo '</div><p></p>';

		echo '<div style="line-height:2em">' . esc_html( __( 'Color scheme', 'facebook' ) ) . ': ';
		$like_button_settings->display_colorscheme( array(
			'id' => $this->get_field_id( 'colorscheme' ),
			'name' => $this->get_field_name( 'colorscheme' )
		) );
		echo '</div>';
	}

	/**
	 * Allow a publisher to customize the title displayed above the widget area.
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
	 * Customize the Like target.
	 *
	 * @since 1.1
	 *
	 * @param string $existing_value stored URL value
	 * @return void
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
