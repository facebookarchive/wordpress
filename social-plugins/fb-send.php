<?php
function fb_get_send_button($options = array()) {
	$params = fb_build_social_plugin_params($options);

	return '<div class="fb-send fb-social-plugin" ' . $params . '></div>';
}

function fb_send_button_automatic($content) {
	$options = get_option('fb_options');

	$new_content = '';

	switch ($options['send']['position']) {
		case 'top':
			$new_content = fb_get_send_button($options['send']) . $content;
			break;
		case 'bottom':
			$new_content = $content . fb_get_send_button($options['send']);
			break;
		case 'both':
			$new_content = fb_get_send_button($options['send']) . $content;
			$new_content .= fb_get_send_button($options['send']);
			break;
	}

	if ( empty( $options['send']['show_on_homepage'] ) && is_singular() ) {
		$content = $new_content;
	}
	elseif ( isset($options['send']['show_on_homepage']) ) {
		$content = $new_content;
	}

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
			array( 'description' => __( 'The Send Button allows users to easily send content to their friends.', 'facebook' ), ) // Args
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
									'help_text' => __( 'Click to learn more.', 'facebook' ),
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/send/',
									);

	$array['children'] = array(array('name' => 'colorscheme',
													'label' => 'Color scheme',
													'field_type' => 'dropdown',
													'default' => 'light',
													'options' => array('light' => 'light', 'dark' => 'dark'),
													'help_text' => __( 'The color scheme of the plugin.', 'facebook' ),
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'default' => 'arial',
													'options' => array('arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana'),
													'help_text' => __( 'The font of the plugin.', 'facebook' ),
													),
										);

	if ($placement == 'settings') {
		$array['children'][] = array('name' => 'position',
													'field_type' => 'dropdown',
													'default' => 'both',
													'options' => array('top' => 'top', 'bottom' => 'bottom', 'both' => 'both'),
													'help_text' => __( 'Where the button will display on the page or post.', 'facebook' ),
													);
		$array['children'][] = array('name' => 'show_on_homepage',
													'field_type' => 'checkbox',
													'default' => true,
													'help_text' => __( 'If the plugin should appear on the homepage as part of the Post previews.  If unchecked, the plugin will only display on the Post itself.', 'facebook' ),
													);
	}

	if ($placement == 'widget') {
		$title_array = array('name' => 'title',
													'field_type' => 'text',
													'help_text' => __( 'The title above the button.', 'facebook' ),
													);
		$text_array = array('name' => 'href',
													'label' => 'URL',
													'field_type' => 'text',
													'default' => get_site_url(),
													'help_text' => __( 'The URL the Send button will point to.', 'facebook' ),
													);

		array_unshift($array['children'], $title_array, $text_array);
	}

	return $array;
}

?>