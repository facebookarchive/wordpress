<?php
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Like_Button" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recent_Activity" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recommendations" );'));

function fb_apply_filters() {
	$options = get_option('fb_options');
	
	if (isset($options['enable_recommendations_bar'])) {
		add_filter('the_content',    'fb_recommendations_bar_automatic', 30);
	}
	
	if (isset($options['enable_like'])) {
		add_filter('the_content',    'fb_like_button_automatic', 30);
	}
	
	if (isset($options['enable_comments'])) {
		add_filter('the_content',    'fb_comments_automatic', 30);
		add_filter('comments_array', 'fb_close_wp_comments');
		add_filter('the_posts',      'fb_set_wp_comment_status');
		add_filter('comments_open',  'fb_close_wp_comments', 10, 2);
		add_filter('pings_open',     'fb_close_wp_comments', 10, 2);
		add_action('wp_footer',      'fb_hide_wp_comments', 30);
	}
}
fb_apply_filters();

/**
 * Display the Like button.
 * More info at https://developers.facebook.com/docs/reference/plugins/like/
 *
 * @param array $enable_send      Enable send button (bool).
 * @param array $layout_style     Layout style, 'standard', 'button_count', or 'box_count'.
 * @param array $width            Width of button area.
 * @param array $show_faces       Show photos of friends that have like the URL.
 * @param array $verb_to_display  Verb to display, 'like' or 'recommend'.
 * @param array $color_scheme     Color scheme, 'light' or 'dark'.
 * @param array $font             Font, 'arial', 'lucida grande', 'segoe ui', 'tahoma', trebuchet ms', 'verdana'.
 * @param array $url              Optional. If not provided, current URL used.
 */

function fb_get_like_button($enable_send = true, $layout_style = 'standard', $width = 450, $show_faces = true, $verb_to_display = 'like', $color_scheme = 'light', $font = 'arial', $url = '') {
	return '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';
}

function fb_like_button_automatic($content) {
	$content .= fb_get_like_button();
	
	return $content;
}

/**
 * Adds the Like Button Social Plugin as a WordPress Widget
 */
class Facebook_Like_Button extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_like', // Base ID
			'Facebook_Like_Button', // Name
			array( 'description' => __( "The Like button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user's friends' News Feed with a link back to your website.", 'text_domain' ), ) // Args
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
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
		echo fb_get_like_button();
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
		$instance['title'] = strip_tags( $new_instance['title'] );

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
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Like ' . esc_attr(get_bloginfo('name')) . ' on Facebook', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}

function fb_get_recommendations_bar($trigger = '', $read_time = '', $verb_to_display = '', $side = '', $domain = '', $url ='') {
	return '<div class="fb-recommendations-bar"></div>';
}

function fb_recommendations_bar_automatic($content) {
	$content .= fb_get_recommendations_bar();
	
	return $content;
}

/*
wp_insert_comment

<noscript></noscript>
*/

function fb_hide_wp_comments($blah) {
	print "<script>document.getElementById('comments').style.display = 'none';</script>";
}

function fb_set_wp_comment_status ( $posts ) {
			if ( ! empty( $posts ) && is_singular() ) {
				$posts[0]->comment_status = 'open';
				$posts[0]->post_status = 'open';
			}
			return $posts;
		}

function fb_close_wp_comments($comments) {
	
	return null;
}

function fb_get_comments($url = '', $num_posts = '2', $width = '470', $color_scheme = 'light') {
	if ($url == '') {
		
	}
	
	return '<div class="fb-comments"></div>';
}

function fb_comments_automatic($content) {
	if (!is_home()) {
		$content .= fb_get_comments();
	}
	
	return $content;
}







function fb_get_recent_activity($options = array()) {
	return '<div class="fb-activity" data-width="300" data-height="300" data-header="true" data-recommendations="false"></div>';
}

/**
 * Adds the Recent Activity Social Plugin as a WordPress Widget
 */
class Facebook_Recent_Activity extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_recent_activity', // Base ID
			'Facebook_Recent_Activity', // Name
			array( 'description' => __( "The Activity Feed plugin displays the most interesting recent activity taking place on your site.", 'text_domain' ), ) // Args
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
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
		echo fb_get_recent_activity();
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
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	/*
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Like ' . esc_attr(get_bloginfo('name')) . ' on Facebook', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}*/

}






function fb_get_recommendations($options = array()) {
	return '<div class="fb-recommendations" data-width="300" data-height="300" data-header="true"></div>';
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
			'Facebook_Recommendations', // Name
			array( 'description' => __( "The Recommendations Box shows personalized recommendations to your users.", 'text_domain' ), ) // Args
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
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
		echo fb_get_recommendations();
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
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	/*
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Like ' . esc_attr(get_bloginfo('name')) . ' on Facebook', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}*/

}

?>