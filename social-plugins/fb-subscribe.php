<?php
function fb_get_subscribe_button($options = array()) {
	return '<div class="fb-subscribe fb-social-plugin" ' . fb_build_social_plugin_params($options) . '></div>';
}

function fb_subscribe_button_automatic($content) {
	global $post;
	$options = get_option('fb_options');

	if ( isset ($post ) ) {
		if ( isset( $options['subscribe']['show_on_homepage'] ) ) {
			$options['subscribe']['href'] = get_permalink( $post->ID );
		}

		$fb_data = fb_get_user_meta( get_the_author_meta( 'ID' ), 'fb_data', true );

		if ( ! $fb_data )
			return $content;

		$options['subscribe']['href'] = 'http://www.facebook.com/' . $fb_data['username'];

		$new_content = '';

		if ( isset( $fb_data['username'] ) ) {
			switch ( $options['subscribe']['position'] ) {
				case 'top':
					$new_content = fb_get_subscribe_button( $options['subscribe'] ) . $content;
					break;
				case 'bottom':
					$new_content = $content . fb_get_subscribe_button( $options['subscribe'] );
					break;
				case 'both':
					$new_content = fb_get_subscribe_button( $options['subscribe'] ) . $content;
					$new_content .= fb_get_subscribe_button( $options['subscribe'] );
					break;
			}
		}

		$show_indiv = get_post_meta( $post->ID, 'fb_social_plugin_settings_box_subscribe', true );

		if ( is_home() && isset ( $options['subscribe']['show_on_homepage'] ) && isset( $options['subscribe']['show_on'] ) && isset( $options['subscribe']['show_on'][$post->post_type] ) ) {
			$content = $new_content;
		} elseif ( !is_home() && ( 'default' == $show_indiv || empty( $show_indiv ) ) && isset ( $options['subscribe']['show_on'] ) && isset( $options['subscribe']['show_on'][$post->post_type] ) ) {		
			$content = $new_content;
		} elseif ( !is_home() && ('show' == $show_indiv || ( ( ! isset( $options['subscribe']['show_on'] ) ) && ( 'default' == $show_indiv || empty( $show_indiv ) ) ) ) ) {
			$content = $new_content;
		}
	}

	return $content;
}


/**
 * Adds the Subscribe Button Social Plugin as a WordPress Widget
 */
class Facebook_Subscribe_Button extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_subscribe', // Base ID
			__( 'Facebook Subscribe Button', 'facebook' ), // Name
			array( 'description' => __( 'Lets a user subscribe to your public updates on Facebook.', 'facebook' ) ) // Args
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
			echo $before_title . esc_attr($instance['title']) . $after_title;

		if ($instance['href']) {
			echo fb_get_subscribe_button($instance);
		}

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

		$fields = fb_get_subscribe_fields_array('widget');

		foreach( $fields['children'] as $field ) {
			$unsafe_value = ( isset( $new_instance[$field['name']] ) ) ? $new_instance[$field['name']] : '';
			if ( !empty( $field['sanitization_callback'] ) && function_exists( $field['sanitization_callback'] ) ) 
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
		fb_get_subscribe_fields('widget', $this);
	}
}


function fb_get_subscribe_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_subscribe_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_subscribe_fields_array($placement) {
	$array['parent'] = array(
		'name' => 'subscribe',
		'label' => __( 'Subscribe Button', 'facebook' ),
		'description' => __( 'The Subscribe Button lets a user subscribe to your public updates on Facebook. Each WordPress author must authenticate with Facebook in order for the Subscribe button to appear on their pages and posts.', 'facebook' ),
		'type' => 'checkbox',
		'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
		'image' => plugins_url( '/images/settings_subscribe_button.png', dirname(__FILE__) )
	);

	$array['children'] = array(
		array(
			'name' => 'layout',
			'type' => 'dropdown',
			'default' => 'standard',
			'options' => array(
				'standard' => 'standard',
				'button_count' => 'button_count',
				'box_count' => 'box_count'
			),
			'help_text' => __( 'Determines the size and amount of social context at the bottom.', 'facebook' )
		),
		array(
			'name' => 'width',
			'type' => 'text',
			'default' => '450',
			'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' ),
			'sanitization_callback' => 'intval'
		),
		array(
			'name' => 'show_faces',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'Show profile pictures below the button.  Applicable to standard layout only.', 'facebook' )
		),
		array(
			'name' => 'colorscheme',
			'label' => __( 'Color scheme', 'facebook' ),
			'type' => 'dropdown',
			'default' => 'light',
			'options' => array(
				'light' => 'light',
				'dark' => 'dark'
			),
			'help_text' => __( 'The color scheme of the plugin.', 'facebook' )
		),
		array(
			'name' => 'font',
			'type' => 'dropdown',
			'default' => 'lucida grande',
			'options' => array(
				'arial' => 'arial',
				'lucida grande' => 'lucida grande',
				'segoe ui' => 'segoe ui',
				'tahoma' => 'tahoma',
				'trebuchet ms' => 'trebuchet ms',
				'verdana' => 'verdana'
			),
			'help_text' => __( 'The font of the plugin.', 'facebook' )
		)
	);

	if ($placement == 'settings') {
		$array['children'][] = array(
			'name' => 'position',
			'type' => 'dropdown',
			'default' => 'both',
			'options' => array(
				'top' => 'top',
				'bottom' => 'bottom',
				'both' => 'both'
			),
			'help_text' => __( 'Where the button will display on the page or post.', 'facebook' )
		);
		$post_types = get_post_types(array('public' => true));
		$array['children'][] = array(
			'name' => 'show_on',
			'type' => 'checkbox',
			'default' => array_fill_keys( array_keys($post_types) , 'true' ),
			'options' => $post_types,
			'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' )
		);
		$array['children'][] = array(
			'name' => 'show_on_homepage',
			'type' => 'checkbox',
			'default' => true,
			'help_text' => __( 'If the plugin should appear on the homepage as part of the Post previews.  If unchecked, the plugin will only display on the Post itself.', 'facebook' )
		);
	}

	if ( $placement == 'widget' ) {
		$title_array = array(
			'name' => 'title',
			'type' => 'text',
			'help_text' => __( 'The title above the button.', 'facebook' )
		);
		$text_array = array(
			'name' => 'href',
			'type' => 'text',
			'default' => get_site_url(),
			'help_text' => __( 'The URL the Subscribe button will point to.', 'facebook' )
		);

		array_unshift($array['children'], $title_array, $text_array);
	}

	return $array;
}

?>
