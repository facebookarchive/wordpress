<?php

/**
 * Adds the Recent Activity Social Plugin as a WordPress Widget
 *
 * @since 1.0
 */
class Facebook_Activity_Feed_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-activity-feed', // Base ID
			__( 'Facebook Recent Activity', 'facebook' ), // Name
			array( 'description' => __( 'Displays the most interesting recent activity taking place on your site.', 'facebook' ), ) // Args
		);
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Activity_Feed_Widget', 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue color picker if present
	 *
	 * @since 1.1.9
	 * @uses wp_enqueue_script(), wp_enqueue_style()
	 * @param string $hook_suffix hook suffix passed with action
	 */
	public static function admin_enqueue_scripts( $hook_suffix = null ) {
		if ( $hook_suffix !== 'widgets.php' )
			return;

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
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

		if ( ! class_exists( 'Facebook_Activity_Feed' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-activity-feed.php' );

		$activity_feed = Facebook_Activity_Feed::fromArray( $instance );
		if ( ! $activity_feed )
			return;

		$activity_feed_html = $activity_feed->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
		if ( ! ( is_string( $activity_feed_html ) && $activity_feed_html ) )
			return;

		echo $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( $title )
			echo $before_title . esc_html( $title ) . $after_title;

		echo $activity_feed_html;

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

		if ( ! class_exists( 'Facebook_Activity_Feed' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-activity-feed.php' );

		$activity_feed = Facebook_Activity_Feed::fromArray( $new_instance );
		if ( $activity_feed ) {
			$activity_feed_options = $activity_feed->toHTMLDataArray();

			if ( isset( $activity_feed_options['header'] ) ) {
				if ( $activity_feed_options['header'] === 'false' )
					$activity_feed_options['header'] = false;
				else
					$activity_feed_options['header'] = true;
			}
			if ( isset( $activity_feed_options['border-color'] ) ) {
				$activity_feed_options['border_color'] = $activity_feed_options['border-color'];
				unset( $activity_feed_options['border-color'] );
			}
			if ( isset( $activity_feed_options['max-age'] ) ) {
				$activity_feed_options['max_age'] = absint( $activity_feed_options['max-age'] );
				unset( $activity_feed_options['max-age'] );
			}
			foreach( array( 'width', 'height' ) as $option ) {
				$activity_feed_options[$option] = absint( $activity_feed_options[$option] );
			}

			if ( isset( $activity_feed_options['recommendations'] ) ) {
				if ( $activity_feed_options['recommendations'] === 'false' )
					$activity_feed_options['recommendations'] = false;
				else
					$activity_feed_options['recommendations'] = true;
			}

			return array_merge( $instance, $activity_feed_options );
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
		$this->display_header( isset( $instance['header'] ) && ( $instance['header'] === true || $instance['header'] == '1' || $instance['header'] === 'true' ) );
		$this->display_recommendations( isset( $instance['recommendations'] ) && ( $instance['header'] === true || $instance['header'] == '1' || $instance['header'] === 'true' ) );
		$this->display_max_age( isset( $instance['max_age'] ) ? absint( $instance['max_age'] ) : 0 );
		$this->display_width( isset( $instance['width'] ) ? absint( $instance['width'] ) : 0 );
		$this->display_height( isset( $instance['height'] ) ? absint( $instance['height'] ) : 0 );
		$this->display_font( isset( $instance['font'] ) ? $instance['font'] : '' );
		echo '<p></p>';
		$this->display_colorscheme( isset( $instance['colorscheme'] ) ? $instance['colorscheme'] : '' );
		echo '<p></p>';
		$this->display_border_color( isset( $instance['border_color'] ) ? $instance['border_color'] : '' );
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
		echo ' value="1" /> ' . __( 'Include Facebook header', 'facebook' ) . '</label></p>';
	}

	/**
	 * Include recommended articles in recent activity in the bottom half
	 *
	 * @since 1.1
	 * @param bool $true_false
	 */
	public function display_recommendations( $true_false ) {
		echo '<p><label><input type="checkbox" id="' . $this->get_field_id( 'header' ) . '" name="' . $this->get_field_name( 'header' ) . '"';
		checked( $true_false );
		echo ' value="1" /> ' . __( 'Include recommendations', 'facebook' ) . '</label></p>';
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
	 * Choose a custom border color
	 * Note: we purposely do not set input[type=color] since an empty string is not a valid value for that field
	 *
	 * @since 1.1
	 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#color-state-(type=color) WHATWG input[type=color]
	 * @param string $existing_value stored value
	 */
	public function display_border_color( $existing_value = '' ) {
		$field_id = $this->get_field_id( 'border_color' );
		echo '<p><label for="' . $field_id . '">' . esc_html( __( 'Border color', 'facebook' ) ) . ':</label> <input class="color-picker-hex" type="text" size="8" maxlength="7" id="' . $field_id . '" name="' . $this->get_field_name( 'border_color' ) . '"';
		if ( $existing_value )
			echo ' value="' . esc_attr( $existing_value ) . '"';
		echo ' />';

		// include script element inline to trigger color picker on initial pageload as well as jQuery(.widget-content).html() on ajax save
		if ( wp_script_is( 'wp-color-picker', 'registered' ) ) {
			echo '</p><script type="text/javascript">if(jQuery){jQuery(function(){if(jQuery.fn.wpColorPicker){jQuery(' . json_encode( '#' . $field_id ) . ').wpColorPicker({defaultColor:"#000000"})}})}</script>';
		} else {
			if ( $existing_value )
				echo ' <span id="' . $field_id . '-span" style="background-color:' . esc_attr( $existing_value ) . ';min-width:2em">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			echo '</p><script type="text/javascript">if(jQuery){jQuery(function(){jQuery(' . json_encode( '#' . $field_id ) . ').on("change",function(){jQuery(' . json_encode( '#' . $field_id . '-span' ) . ').css("background-color",jQuery(this).val())})})}</script>';
		}
	}
}

?>