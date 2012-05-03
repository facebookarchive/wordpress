<?php
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

function fb_get_like_button($options = array()) {
	$params = '';
	
	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}
	
	return '<div class="fb-like" ' . $params . ' ></div>';
}

function fb_like_button_automatic($content) {
	$options = get_option('fb_options');
	
	foreach($options['like'] as $param => $val) {
		$options['like']['data-' . $param] =  $val;
	}
	
	switch ($options['like']['position']) {
		case 'top':
			$content = fb_get_like_button($options['like']) . $content;
			break;
		case 'bottom':
			$content .= fb_get_like_button($options['like']);
			break;
		case 'both':
			$content = fb_get_like_button($options['like']) . $content;
			$content .= fb_get_like_button($options['like']);
			break;
	}
	
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
		
		$options = array('data-href' => $instance['url']);	
		echo fb_get_like_button($options);
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
		$instance['url'] = strip_tags( $new_instance['url'] );

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
		if ( isset( $instance[ 'url' ] ) ) {
			$url = $instance[ 'url' ];
		}
		else {
			$url = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Facebook Page URL:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
		<p>Optional.  If you have a Page on Facebook that you want users to Like.  If you leave it blank, the user will like the current page that they're on.</p>
		</p>
		
		
		
		
		<?php
		//send button
		if ( isset( $instance[ 'send' ] ) ) {
			$send = $instance[ 'send' ];
		}
		else {
			$send = '';
		}
		?>
		<p>
		<input type="checkbox" id="<?php echo $this->get_field_id( 'send' ); ?>" name="<?php echo $this->get_field_name( 'send' ); ?>" value="true" <?php checked(TRUE, (bool) $send);  print $send; ?> />
		<label for="<?php echo $this->get_field_id( 'send' ); ?>"><?php _e( 'Enable send button' ); ?></label>
		</p>
		
		<?php
		
		//		<div class="fb-like" data-send="false" data-layout="button_count" data-width="454" data-show-faces="false" data-action="recommend" data-colorscheme="dark" data-font="segoe ui"></div>
		
		
		//layout style
		//width
		//show faces
		//verb to display
		//color scheme
		//font
		
		/*
		dropdown
		checkbox
		field
		*/
		
		
	}
}
?>