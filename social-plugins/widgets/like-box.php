<?php
/**
 * Adds the Recommendations Social Plugin as a WordPress Widget
 *
 * @since 1.1.11
 */
class Facebook_Like_Box_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
	 		'facebook-like-box', // Base ID
			__( 'Facebook Like Box', 'facebook' ), // Name
			array( 'description' => _x( 'Highlight your Facebook Page content.', 'Improve the marketing of Facebook content by including on your site.', 'facebook' ) . ' ' . __( 'Encourage visitors to like your Facebook page.', 'facebook' ) ) // Args
		);
	}

	/**
	 * Test if a provided string is a URL to a Facebook page
	 *
	 * Sanitize the URL if valid
	 *
	 * @since 1.1.11
	 *
	 * @global wpdb $wpdb WordPress database class. sanitize FQL values.
	 * @global \Facebook_Loader $facebook_loader access Facebook application credentials
	 * @param string $url absolute URL
	 * @return string Facebook Page URL or empty string if the passed URL does not seem to be a Facebook Page URL
	 */
	public static function sanitize_facebook_page_url( $url ) {
		global $wpdb, $facebook_loader;

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
		$url_parts['path'] = ltrim( $url_parts['path'], '\/' );
		if ( ! $url_parts['path'] )
			return '';

		// attempt to normalize the URL through a Facebook request if an access token is present
		if ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() ) {
			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( $facebook_loader->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );

			// page without a username
			if ( strlen( $url_parts['path'] ) > 7 && substr_compare( $url_parts['path'], 'pages/', 0, 6 ) === 0 ) {
				$page_id = ltrim( substr( $url_parts['path'], strrpos( $url_parts['path'], '/' ) ), '\/' );
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
				$page_info = Facebook_WP_Extend::graph_api_with_app_access_token( '/fql', 'GET', array( 'q' => 'SELECT page_url FROM page WHERE ' . $where ) );
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
	 * @since 1.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 * @return void
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

		// set the booleans
		foreach ( array( 'stream', 'header', 'show_border', 'force_wall', 'show_faces' ) as $bool_option ) {
			if ( isset( $new_instance[ $bool_option ] ) )
				$new_instance[ $bool_option ] = true;
			else
				$new_instance[ $bool_option ] = false;
		}

		if ( isset( $new_instance['href'] ) ) {
			// sanitize if href has changed
			if ( ! isset( $old_instance['href'] ) || $old_instance['href'] !== $new_instance['href'] )
				$new_instance['href'] = self::sanitize_facebook_page_url( $new_instance['href'] );
		}

		foreach( array( 'width', 'height' ) as $option ) {
			if ( isset( $new_instance[ $option ] ) )
				$new_instance[ $option ] = absint( $new_instance[ $option ] );
		}

		if ( ! class_exists( 'Facebook_Like_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-box.php' );

		// include values when checkboxes not present
		$box = Facebook_Like_Box::fromArray( $new_instance );
		if ( $box ) {
			$box_options = $box->toHTMLDataArray();

			// convert dashes used to construct data-* attributes into underscore properties
			foreach( array( 'stream' => 'stream', 'header' => 'header', 'show-border' => 'show_border', 'force-wall' => 'force_wall', 'show-faces' => 'show_faces' ) as $data => $prop ) {
				if ( ! isset( $box_options[ $data ] ) )
					continue;

				if ( $box_options[ $data ] === 'true' )
					$box_options[ $data ] = true;
				else if ( $box_options[ $data ] === 'false' )
					$box_options[ $data ] = false;

				if ( $data !== $prop ) {
					$box_options[ $prop ] = $box_options[ $data ];
					unset( $box_options[ $data ] );
				}
			}

			// unsigned ints
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
	 * @since 1.0
	 *
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'header' => true,
			'show_border' => true,
			'href' => '',
			'stream' => true,
			'force_wall' => false,
			'show_faces' => true,
			'colorscheme' => 'light',
			'width' => 300,
			'height' => 0
		) );
		$this->display_title( $instance['title'] );
		$this->display_header( $instance['header'] === true || $instance['header'] === 'true' || $instance['header'] == '1' );
		$this->display_show_border( $instance['show_border'] === true || $instance['show_border'] === 'true' || $instance['show_border'] == '1' );
		$this->display_href( $instance['href'] );
		$this->display_stream( $instance['stream'] === true || $instance['stream'] === 'true' || $instance['stream'] == '1' );
		$this->display_force_wall( $instance['force_wall'] === true || $instance['force_wall'] === 'true' || $instance['force_wall'] == '1' );
		$this->display_show_faces( $instance['show_faces'] === true || $instance['show_faces'] === 'true' || $instance['show_faces'] == '1' );
		$this->display_colorscheme( $instance['colorscheme'] );
		$this->display_width( absint( $instance['width'] ) );
		$this->display_height( absint( $instance['height'] ) );
	}

	/**
	 * Allow a publisher to customize the title displayed above the widget area
	 *
	 * e.g. Like us!
	 *
	 * @since 1.1.11
	 *
	 * @param string $existing_value saved title
	 * @return void
	 */
	public function display_title( $existing_value = '' ) {
		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">' . esc_html( _x( 'Title', 'Section title or header', 'facebook' ) ) . '</label>: ';
		echo '<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat"';
		if ( $existing_value )
			echo ' value="' . esc_attr( $existing_value ) . '"';
		echo ' /></p>';
	}

	/**
	 * Show the Facebook header.
	 *
	 * Works best when you do not set your own widget title
	 *
	 * @since 1.1.11
	 *
	 * @param bool $true_false
	 * @return void
	 */
	public function display_header( $true_false ) {
		echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'header' ) . '" name="' . $this->get_field_name( 'header' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'header' ) . '">' . esc_html( __( 'Include Facebook header', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Show the social plugin border.
	 *
	 * Works best when you do not set your own widget title
	 *
	 * @since 1.5
	 *
	 * @param bool $true_false
	 * @return void
	 */
	public function display_show_border( $true_false ) {
		echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'show_border' ) . '" name="' . $this->get_field_name( 'show_border' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'show_border' ) . '">' . esc_html( __( 'Show a border', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Set the Like target.
	 *
	 * @since 1.1.11
	 *
	 * @param string $existing_value stored URL value
	 * @return void
	 */
	public function display_href( $existing_value = '' ) {
		echo '<p><label for="' . $this->get_field_id( 'href' ) . '">URL</label>: <input type="url" id="' . $this->get_field_id( 'href' ) . '" name="' . $this->get_field_name( 'href' ) . '" class="widefat" required';
		if ( $existing_value )
			echo ' value="' . esc_url( $existing_value, array( 'http', 'https' ) ) . '"';
		echo ' /><br />';

		echo '<small class="description">' . esc_html( __( 'Must be a Facebook Page URL', 'facebook' ) ) . '</small></p>';
	}

	/**
	 * Display a stream of latest posts from the Facebook Page's wall.
	 *
	 * @since 1.1.11
	 *
	 * @param bool $true_false enabled or disabled
	 * @return void
	 */
	public function display_stream( $true_false ) {
		echo '<p><input type="checkbox" id="' . $this->get_field_id( 'stream' ) . '" name="' . $this->get_field_name( 'stream' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'stream' ) . '">' . esc_html( __( 'Display the latest posts from your Facebook Page', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Always display posts from Page's stream, even if page is a Place and checkins available.
	 *
	 * @since 1.1.11
	 *
	 * @param bool $true_false
	 * @return void
	 */
	public function display_force_wall( $true_false ) {
		echo '<p><input type="checkbox" id="' . $this->get_field_id( 'force_wall' ) . '" name="' . $this->get_field_name( 'force_wall' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'force_wall' ) . '">' . esc_html( _x( "Places-specific: disable display of checkins from the current visitor's friends", 'Prefer Posts from a page over Checkins to the venue', 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Show faces of viewer's friends.
	 *
	 * @since 1.1.11
	 *
	 * @param bool $true_false enabled or disabled
	 * @return void
	 */
	public function display_show_faces( $true_false ) {
		echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id( 'show_faces' ) . '" name="' . $this->get_field_name( 'show_faces' ) . '" value="1"';
		checked( $true_false );
		echo ' /> <label for="' . $this->get_field_id( 'show_faces' ) . '">' . esc_html( __( "Show profile photos of the viewer's friends who have already liked the URL.", 'facebook' ) ) . '</label></p>';
	}

	/**
	 * Choose a light or dark color scheme.
	 *
	 * @since 1.1.11
	 *
	 * @param string $existing_value saved colorscheme value
	 * @return void
	 */
	public function display_colorscheme( $existing_value = 'light' ) {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin/settings-social-plugin.php' );

		$color_schemes = Facebook_Social_Plugin_Settings::color_scheme_choices( $this->get_field_name( 'colorscheme' ), $existing_value );
		if ( $color_schemes )
			echo '<div id="' . $this->get_field_id( 'colorscheme' ) . '">' . esc_html( __( 'Color scheme', 'facebook' ) ) . ': ' . $color_schemes . '</div>';
	}

	/**
	 * Specify the width of the recommendations box in whole pixels.
	 *
	 * @since 1.1.11
	 *
	 * @param int $existing_value previously stored value
	 * @return void
	 */
	public function display_width( $existing_value = 300 ) {
		if ( ! class_exists( 'Facebook_Like_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/class-facebook-like-box.php' );

		if ( ! is_int( $existing_value ) || $existing_value < Facebook_Like_Box::MIN_WIDTH )
			$existing_value = Facebook_Like_Box::MIN_WIDTH;

		echo '<p><label for="' . $this->get_field_id( 'width' ) . '">' . esc_html( __( 'Width' ) ) . '</label>: ' . '<input type="number" name="' . $this->get_field_name( 'width' ) . '" id="' . $this->get_field_id( 'width' ) . '" size="5" min="' . Facebook_Like_Box::MIN_WIDTH . '" step="1" value="' . $existing_value . '" /> ' . esc_attr( __( 'pixels', 'facebook' ) ) . '</p>';
	}

	/**
	 * Specify the height of the recommendations box in whole pixels.
	 *
	 * @since 1.1.11
	 *
	 * @param int $existing_value previously stored value
	 * @return void
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
