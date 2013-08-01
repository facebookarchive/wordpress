<?php

/**
 * Adds the Recommendations Social Plugin as a WordPress Widget
 *
 * @since 1.0
 */
class Facebook_Recommendations_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-recommendations', // Base ID
			__( 'Facebook Recommendations', 'facebook' ), // Name
			array( 'description' => __( 'Shows personalized recommendations to your users.', 'facebook' ) ) // Args
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

		if ( ! class_exists( 'Facebook_Recommendations_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-recommendations-box.php' );

		if ( empty( $instance['ref'] ) )
			$instance['ref'] = 'recommendations-box-widget';

		$box = Facebook_Recommendations_Box::fromArray( $instance );
		if ( ! $box )
			return;

		$box_html = $box->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
		if ( ! ( is_string( $box_html ) && $box_html ) )
			return;

		echo $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( $title )
			echo $before_title . esc_html( $title ) . $after_title;

		echo $box_html;

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
		$new_instance = (array) $new_instance;

		if ( ! empty( $new_instance['title'] ) )
			$instance['title'] = strip_tags( $new_instance['title'] );

		if ( isset( $new_instance['header'] ) )
			$new_instance['header'] = true;
		else
			$new_instance['header'] = false;

		foreach( array( 'width', 'height', 'max_age' ) as $option ) {
			if ( isset( $new_instance[ $option ] ) )
				$new_instance[ $option ] = absint( $new_instance[ $option ] );
		}

		if ( ! class_exists( 'Facebook_Recommendations_Box' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/class-facebook-recommendations-box.php' );

		$box = Facebook_Recommendations_Box::fromArray( $new_instance );
		if ( $box ) {
			$box_options = $box->toHTMLDataArray();

			if ( isset( $box_options['header'] ) ) {
				if ( $box_options['header'] === 'true' )
					$box_options['header'] = true;
				else if ( $box_options['header'] === 'false' )
					$box_options['header'] = false;
			}

			if ( isset( $box_options['max-age'] ) ) {
				$box_options['max_age'] = absint( $box_options['max-age'] );
				unset( $box_options['max-age'] );
			}

			foreach( array( 'width', 'height' ) as $option ) {
				if ( isset( $box_options[ $option ] ) )
					$box_options[ $option ] = absint( $box_options[ $option ] );
			}

			return array_merge( $instance, $box_options );
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
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'max_age' => 0,
			'width' => 0,
			'height' => 0,
			'font' => '',
			'colorscheme' => 'light'
		) );
		$this->display_title( $instance['title'] );
		$this->display_header( isset( $instance['header'] ) && ( $instance['header'] === true || $instance['header'] == '1' || $instance['header'] === 'true' ) );
		$this->display_max_age( absint( $instance['max_age'] ) );
		$this->display_width( absint( $instance['width'] ) );
		$this->display_height( absint( $instance['height'] ) );
		$this->display_font( $instance['font'] );
		echo '<p></p>';
		$this->display_colorscheme( $instance['colorscheme'] );
	}

	/**
	 * Allow a publisher to customize the title displayed above the widget area
	 * e.g. Things we hope you will like
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
	 * Show the Facebook header
	 * Works best when you don't set your own widget title
	 *
	 * @since 1.1
	 * @param bool $true_false
	 */
	public function display_header( $true_false ) {
		echo '<p><label><input type="checkbox" id="' . $this->get_field_id( 'header' ) . '" name="' . $this->get_field_name( 'header' ) . '"';
		checked( $true_false );
		echo ' value="1" /> ' . esc_html( __( 'Include Facebook header', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Specify the width of the recommendations box in whole pixels
	 *
	 * @since 1.1
	 * @param int $existing_value previously stored value
	 */
	public function display_width( $existing_value = 300 ) {
		if ( $existing_value < 200 )
			$existing_value = 300;
		echo '<p><label>' . esc_html( __( 'Width' ) ) . ': ' . '<input type="number" name="' . $this->get_field_name( 'width' ) . '" id="' . $this->get_field_id( 'width' ) . '" size="5" min="200" step="1" value="' . $existing_value . '" /></label></p>';
	}

	/**
	 * Specify the height of the recommendations box in whole pixels
	 *
	 * @since 1.1
	 * @param int $existing_value previously stored value
	 */
	public function display_height( $existing_value = 300 ) {
		if ( $existing_value < 200 )
			$existing_value = 300;
		echo '<p><label>' . esc_html( __( 'Height' ) ) . ': ' . '<input type="number" name="' . $this->get_field_name( 'height' ) . '" id="' . $this->get_field_id( 'height' ) . '" size="5" min="200" step="1" value="' . $existing_value . '" /></label></p>';
	}

	/**
	 * Choose a font
	 *
	 * @since 1.1
	 * @param string $existing_value stored font value
	 */
	public function display_font( $existing_value = '' ) {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin/settings-social-plugin.php' );

		echo '<label>' . esc_html( __( 'Font', 'facebook') ) . ': <select id="' . $this->get_field_id( 'font' ) . '" name="' . $this->get_field_name( 'font' ) . '">' . Facebook_Social_Plugin_Settings::font_choices( $existing_value ) . '</select></label>';
	}

	/**
	 * Choose a light or dark color scheme
	 *
	 * @since 1.1
	 * @param string $existing_value saved colorscheme value
	 */
	public function display_colorscheme( $existing_value = 'light' ) {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin/settings-social-plugin.php' );

		$color_schemes = Facebook_Social_Plugin_Settings::color_scheme_choices( $this->get_field_name( 'colorscheme' ), $existing_value );
		if ( $color_schemes )
			echo '<fieldset id="' . $this->get_field_id( 'colorscheme' ) . '">' . esc_html( __( 'Color scheme', 'facebook' ) ) . ': ' . $color_schemes . '</fieldset>';
	}

	/**
	 * Limit articles displayed in recommendations box to last N days where N is a number between 0 (no limit) and 180.
	 *
	 * @since 1.1
	 * @param int $existing_value stored value
	 */
	public function display_max_age( $existing_value = 0 ) {
		echo '<p><label>' . esc_html( __( 'Maximum age', 'facebook' ) ) . ': ';
		echo '<input type="number" size="3" maxlength="3" min="0" max="180" step="1" id="' . $this->get_field_id( 'max_age' ) . '" name="' . $this->get_field_name( 'max_age' ) . '" value="' . $existing_value . '" /> ' . esc_html( _n( 'day old', 'days old', $existing_value, 'facebook' ) );

		// days === 0 can be confusing. clarify
		if ( $existing_value === 0 )
			echo ' ' . esc_html( __( '(no limit)', 'facebook' ) );

		echo '</label></p>';

		echo '<p class="description">' . esc_html( __( 'Limit recommendations to articles authored within the last N days.', 'facebook' ) ) . ' ' . esc_html( sprintf( __( 'Reset this value to %s for no date-based limits.', 'facebook' ), '"0"' ) ) . '</p>';
	}
}

?>