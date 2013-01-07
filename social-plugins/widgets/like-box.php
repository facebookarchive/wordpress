<?php
/**
 * Adds the Recommendations Social Plugin as a WordPress Widget
 *
 * @since 1.1.11
 */
class Facebook_Like_Box_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-like-box', // Base ID
			__( 'Facebook Like Box', 'facebook' ), // Name
			array( 'description' => _x( 'Highlight your Facebook Page content.', 'Improve the marketing of Facebook content by including on your site.', 'facebook' ) . ' ' . __( 'Encourage visitors to like your Facebook page.', 'facebook' ) ) // Args
		);
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Like_Box_Widget', 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue color picker if present
	 *
	 * @since 1.1.11
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
	 * Test if a provided string is a URL to a Facebook page
	 * Sanitize the URL if valid
	 *
	 * @since 1.1.11
	 * @param string $url absolute URL
	 * @return string 
	 */
	public static function sanitize_facebook_page_url( $url ) {
		global $wpdb, $facebook;

		if ( ! is_string( $url ) || ! $url )
			return '';

		// check for basic URL validity
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( ! $url )
			return '';

		// is the provided URL a Facebook URL?
		try {
			$url_parts = parse_url( $url );
		} catch ( Exception $e ) {
			return '';
		}

		// does the provided string look like a Facebook URL?
		if ( ! ( is_array( $url_parts ) && isset( $url_parts['host'] ) && $url_parts['host'] === 'www.facebook.com' && ! empty( $url_parts['path'] ) ) )
			return '';

		// reject a Like Box URL pointing to the Facebook homepage
		$url_parts['path'] = ltrim( $url_parts['path'], '/' );
		if ( ! $url_parts['path'] )
			return '';

		// attempt to normalize the URL through a Facebook request if an access token is present
		if ( isset( $facebook ) ) {
			// page without a username
			if ( strlen( $url_parts['path'] ) > 7 && substr_compare( $url_parts['path'], 'pages/', 0, 6 ) === 0 ) {
				$page_id = substr( $url_parts['path'], strrpos( $url_parts['path'], '/' ) );
				if ( ! ( is_string( $page_id ) && $page_id && ctype_digit( $page_id ) ) )
					return '';
				$where = $wpdb->prepare( 'page_id=%s', $page_id );
				unset( $page_id );
			} else {
				// treat the link as a username
				$where = $wpdb->prepare( 'username=%s', $url_parts['path'] );
			}
			$where .= ' AND is_published=1';

			try {
				$page_info = $facebook->api( '/fql', array( 'q' => 'SELECT page_url FROM page WHERE ' . $where ) );
			} catch ( WP_FacebookApiException $e ) {
				break;
			}
			unset( $where );

			if ( isset( $page_info['data'][0]['page_url'] ) )
				return $page_info['data'][0]['page_url'];

			unset( $page_info );
		}

		return 'https://www.facebook.com/' . $url_parts['path'];
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
		// no Facebook Page target specified. fail early
		if ( empty( $instance['href'] ) )
			return;

		extract( $args );

		if ( ! isset( $instance['ref'] ) )
			$instance['ref'] = 'widget';

		if ( ! class_exists( 'Facebook_Like_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-box.php' );

		$box = Facebook_Like_Box::fromArray( $instance );
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

		if ( ! empty( $new_instance['title'] ) )
			$instance['title'] = strip_tags( $new_instance['title'] );

		if ( ! class_exists( 'Facebook_Like_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-box.php' );

		// include values when checkboxes not present
		$box = Facebook_Like_Box::fromArray( array_merge( array(
			'show_faces' => false,
			'stream' => false,
			'header' => false,
			'force_wall' => false
		),  $new_instance ) );
		if ( $box ) {
			$box_options = $box->toHTMLDataArray();

			if ( isset( $box_options['href'] ) ) {
				// sanitize if href has changed
				if ( ! isset( $old_instance['href'] ) || $old_instance['href'] !== $box_options['href'] )
					$box_options['href'] = self::sanitize_facebook_page_url( $box_options['href'] );
			}

			// convert the booleans
			foreach ( array( 'stream', 'header' ) as $bool_property ) {
				if ( isset( $box_options[ $bool_property ] ) ) {
					if ( $box_options[ $bool_property ] === 'false' )
						$box_options[ $bool_property ] = false;
					else
						$box_options[ $bool_property ] = true;
				}
			}

			// dashes to underscores
			if ( isset( $box_options['border-color'] ) ) {
				$box_options['border_color'] = $box_options['border-color'];
				unset( $box_options['border-color'] );
			}
			if ( isset( $box_options['max-age'] ) ) {
				$box_options['max_age'] = absint( $box_options['max-age'] );
				unset( $box_options['max-age'] );
			}

			// bool with dash
			if ( isset( $box_options['force-wall'] ) ) {
				if ( $box_options['force-wall'] === 'true' )
					$box_options['force_wall'] = true;
				else
					$box_options['force_wall'] = false;
				unset( $box_options['force-wall'] );
			}
			// bool with dash
			if ( isset( $box_options['show-faces'] ) ) {
				if ( $box_options['show-faces'] === 'false' )
					$box_options['show_faces'] = false;
				else
					$box_options['show_faces'] = true;
				unset( $box_options['show-faces'] );
			}

			if ( isset( $box_options['width'] ) ) {
				$box_options['width'] = absint( $box_options['width'] );
				if ( $box_options['width'] > 0 ) {
					// correct an invalid value to the closest allowed value
					if ( $box_options['width'] < Facebook_Like_Box::MIN_WIDTH )
						$box_options['width'] = Facebook_Like_Box::MIN_WIDTH;
				} else {
					unset( $box_options['width'] );
				}
			}

			if ( isset( $box_options['height'] ) ) {
				$box_options['height'] = absint( $box_options['height'] );
				// default is the same as minimum. remove invalid value
				if ( $box_options['height'] < Facebook_Like_Box::MIN_HEIGHT )
					unset( $box_options['height'] );
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
		$instance = wp_parse_args( $instance, array(
			'title' => '',
			'header' => true,
			'href' => '',
			'stream' => true,
			'force_wall' => false,
			'show_faces' => true,
			'colorscheme' => 'light',
			'border_color' => '',
			'width' => 300,
			'height' => 0
		) );
		$this->display_title( $instance['title'] );
		$this->display_header( $instance['header'] === true || $instance['header'] === 'true' || $instance['header'] == '1' );
		$this->display_href( $instance['href'] );
		$this->display_stream( $instance['stream'] === true || $instance['stream'] === 'true' || $instance['stream'] == '1' );
		$this->display_force_wall( $instance['force_wall'] === true || $instance['force_wall'] === 'true' || $instance['force_wall'] == '1' );
		$this->display_show_faces( $instance['show_faces'] === true || $instance['show_faces'] === 'true' || $instance['show_faces'] == '1' );
		$this->display_colorscheme( $instance['colorscheme'] );
		$this->display_border_color( $instance['border_color'] );
		$this->display_width( absint( $instance['width'] ) );
		$this->display_height( absint( $instance['height'] ) );
	}

	/**
	 * Allow a publisher to customize the title displayed above the widget area
	 * e.g. Like us!
	 *
	 * @since 1.1.11
	 * @param string $existing_value saved title
	 */
	public function display_title( $existing_value = '' ) {
		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">' . esc_html( _x( 'Title', 'Section title or header', 'facebook' ) ) . '</label>: ';
		echo '<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat"';
		if ( $existing_value )
			echo ' value="' . esc_attr( $existing_value ) . '"';
		echo ' /></p>';
	}

	/**
	 * Show the Facebook header
	 * Works best when you do not set your own widget title
	 *
	 * @since 1.1.11
	 * @param bool $true_false
	 */
	public function display_header( $true_false ) {
		echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'header' ) . '" name="' . $this->get_field_name( 'header' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'header' ) . '">' . esc_html( __( 'Include Facebook header', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Set the Like target
	 *
	 * @since 1.1.11
	 * @param string $existing_value stored URL value
	 */
	public function display_href( $existing_value = '' ) {
		echo '<p><label for="' . $this->get_field_id( 'href' ) . '">URL</label>: <input type="url" id="' . $this->get_field_id( 'href' ) . '" name="' . $this->get_field_name( 'href' ) . '" class="widefat" required';
		if ( $existing_value )
			echo ' value="' . esc_url( $existing_value, array( 'http', 'https' ) ) . '"';
		echo ' /><br />';

		echo '<small class="description">' . esc_html( __( 'Must be a Facebook Page URL', 'facebook' ) ) . '</small></p>';
	}

	/**
	 * Display a stream of latest posts from the Facebook Page's wall
	 *
	 * @since 1.1.11
	 * @param bool $true_false enabled or disabled
	 */
	public function display_stream( $true_false ) {
		echo '<p><input type="checkbox" id="' . $this->get_field_id( 'stream' ) . '" name="' . $this->get_field_name( 'stream' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'stream' ) . '">' . esc_html( __( 'Display the latest posts from your Facebook Page', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Always display posts from Page's stream, even if page is a Place and checkins available
	 *
	 * @since 1.1.11
	 * @param bool $true_false
	 */
	public function display_force_wall( $true_false ) {
		echo '<p><input type="checkbox" id="' . $this->get_field_id( 'force_wall' ) . '" name="' . $this->get_field_name( 'force_wall' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'force_wall' ) . '">' . esc_html( _x( "Places-specific: disable display of checkins from the current visitor's friends", 'Prefer Posts from a page over Checkins to the venue', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Show faces of viewer's friends
	 *
	 * @since 1.1.11
	 * @param bool $true_false enabled or disabled
	 */
	public function display_show_faces( $true_false ) {
		echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'show_faces' ) . '" name="' . $this->get_field_name( 'show_faces' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'show_faces' ) . '">' . esc_html( __( "Show profile photos of the viewer's friends who have already liked the URL.", 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Choose a light or dark color scheme
	 *
	 * @since 1.1.11
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
	 * @since 1.1.11
	 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#color-state-(type=color) WHATWG input[type=color]
	 * @param string $existing_value stored value
	 */
	public function display_border_color( $existing_value = '' ) {
		$field_id = $this->get_field_id( 'border_color' );
		echo '<p><label for="' . $field_id . '">' . esc_html( __( 'Border color', 'facebook' ) ) . '</label>: <input class="color-picker-hex" type="text" size="8" maxlength="7" id="' . $field_id . '" name="' . $this->get_field_name( 'border_color' ) . '"';
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

	/**
	 * Specify the width of the recommendations box in whole pixels
	 *
	 * @since 1.1.11
	 * @param int $existing_value previously stored value
	 */
	public function display_width( $existing_value = 300 ) {
		if ( ! class_exists( 'Facebook_Like_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-box.php' );

		if ( ! is_int( $existing_value ) || $existing_value < Facebook_Like_Box::MIN_WIDTH )
			$existing_value = Facebook_Like_Box::MIN_WIDTH;

		echo '<p><label for="' . $this->get_field_id( 'width' ) . '">' . esc_html( __( 'Width' ) ) . '</label>: ' . '<input type="number" name="' . $this->get_field_name( 'width' ) . '" id="' . $this->get_field_id( 'width' ) . '" size="5" min="' . Facebook_Like_Box::MIN_WIDTH . '" step="1" value="' . $existing_value . '" /> ' . esc_attr( __( 'pixels', 'facebook' ) ) . '</p>';
	}

	/**
	 * Specify the height of the recommendations box in whole pixels
	 *
	 * @since 1.1.11
	 * @param int $existing_value previously stored value
	 */
	public function display_height( $existing_value = 0 ) {
		if ( ! class_exists( 'Facebook_Like_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-box.php' );

		echo '<p><label for="' . $this->get_field_id( 'height' ) . '">' . esc_html( __( 'Height' ) ) . '</label>: ' . '<input type="number" name="' . $this->get_field_name( 'height' ) . '" id="' . $this->get_field_id( 'height' ) . '" size="5" min="' . Facebook_Like_Box::MIN_HEIGHT . '" step="1"';
		if ( is_int( $existing_value ) && $existing_value >= Facebook_Like_Box::MIN_HEIGHT )
			echo ' value="' . $existing_value . '"';
		echo ' /> ' . esc_html( __( 'pixels', 'facebook' ) ) . '</p>';
	}
}
?>