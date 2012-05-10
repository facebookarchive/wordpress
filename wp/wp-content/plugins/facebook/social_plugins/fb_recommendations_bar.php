<?php
function fb_get_recommendations_bar($options = array()) {
	$params = '';

	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}

	$params .= 'data-ref="wp" ';

	return '<div class="fb-recommendations-bar fb-social-plugin" ' . $params . '></div>';
}

function fb_recommendations_bar_automatic($content) {
	if (!is_home()) {
		$options = get_option('fb_options');

		foreach($options['recommendations_bar'] as $param => $val) {
			$param = str_replace('_', '-', $param);

			$options['recommendations_bar']['data-' . $param] =  $val;
		}

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
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/recommendationsbar/',
									);

	$array['children'] = array(array('name' => 'trigger',
													'field_type' => 'text',
													'default' => '50',
													'help_text' => 'This specifies the percent of the page the user must scroll down before the plugin is expanded.',
													),
										array('name' => 'read_time',
													'field_type' => 'text',
													'default' => '20',
													'help_text' => 'The number of seconds the plugin will wait until it expands.',
													),
										array('name' => 'action',
													'field_type' => 'dropdown',
													'default' => 'like',
													'options' => array('like', 'recommend'),
													'help_text' => 'The verb to display in the button.',
													),
										array('name' => 'side',
													'field_type' => 'dropdown',
													'default' => 'right',
													'options' => array('left', 'right'),
													'help_text' => 'The side of the window that the plugin will display.',
													),
										);

	return $array;
}

?>