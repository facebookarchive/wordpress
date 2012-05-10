<?php
function fb_get_send_button($options = array()) {
	$params = '';

	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}

	$params .= 'data-ref="wp" ';

	return '<div class="fb-send fb-social-plugin" ' . $params . '></div>';
}

function fb_send_button_automatic($content) {
	$options = get_option('fb_options');

	foreach($options['send'] as $param => $val) {
		$param = str_replace('_', '-', $param);

		$options['send']['data-' . $param] =  $val;
	}

	$content .= fb_get_send_button($options['send']);

	return $content;
}


/**
 * Adds the Send Button Social Plugin as a WordPress Widget
 */
class Facebook_Send_Button extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
	 		'fb_send', // Base ID
			'Facebook Send Button', // Name
			array( 'description' => __( "The Send Button allows users to easily send content to their friends. People will have the option to send your URL in a message to their Facebook friends, to the group wall of one of their Facebook groups, and as an email to any email address.", 'text_domain' ), ) // Args
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
			echo $before_title . $instance['title'] . $after_title;

		echo fb_get_send_button($instance);
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
		return $new_instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		fb_get_send_fields('widget', $this);
	}
}


function fb_get_send_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_send_fields_array($placement);

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_send_fields_array($placement) {
	$array['parent'] = array('name' => 'send',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/send/',
									);

	$array['children'] = array(array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'options' => array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'),
													'help_text' => 'The font of the plugin.',
													),
										);

	if ($placement == 'settings') {
		$array['children'][] = array('name' => 'position',
													'field_type' => 'dropdown',
													'options' => array('top', 'bottom', 'both'),
													'help_text' => 'Where the button will display on the page or post.',
													);
	}

	if ($placement == 'widget') {
		$array['children'][] = array('name' => 'href',
													'field_type' => 'text',
													'help_text' => 'The URL the Like button will point to.',
													);

		$array['children'][] = array('name' => 'title',
													'field_type' => 'text',
													'help_text' => 'The title above the button.',
													);
	}

	return $array;
}

?>