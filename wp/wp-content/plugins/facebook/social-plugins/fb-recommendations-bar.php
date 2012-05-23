<?php
function fb_get_recommendations_bar($options = array()) {
	$params = fb_build_social_plugin_params($options);

	return '<div class="fb-recommendations-bar fb-social-plugin" ' . $params . '></div>';
}

function fb_recommendations_bar_automatic($content) {
	if (!is_home()) {
		$options = get_option('fb_options');

		$content .= fb_get_recommendations_bar($options['recommendations_bar']);
	}

	return $content;
}


function fb_get_recommendations_bar_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_recommendations_bar_fields_array();

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_recommendations_bar_fields_array() {
	$array['parent'] = array('name' => 'recommendations_bar',
									'field_type' => 'checkbox',
									'help_text' => __( 'Click to learn more.', 'facebook' ),
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/recommendationsbar/',
									);

	$array['children'] = array(array('name' => 'trigger',
													'field_type' => 'text',
													'default' => '50',
													'help_text' => __( 'This specifies the percent of the page the user must scroll down before the plugin is expanded.', 'facebook' ),
													),
										array('name' => 'read_time',
													'field_type' => 'text',
													'default' => '20',
													'help_text' => __( 'The number of seconds the plugin will wait until it expands.', 'facebook' ),
													),
										array('name' => 'action',
													'field_type' => 'dropdown',
													'default' => 'like',
													'options' => array('like' => 'like', 'recommend' => 'recommend'),
													'help_text' => __( 'The verb to display in the button.', 'facebook' ),
													),
										array('name' => 'side',
													'field_type' => 'dropdown',
													'default' => 'right',
													'options' => array('left' => 'left', 'right' => 'right'),
													'help_text' => __( 'The side of the window that the plugin will display.', 'facebook' ),
													),
										);

	return $array;
}

?>