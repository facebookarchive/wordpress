<?php
function fb_admin_dialog($message, $error = false) {
	if ($error) {
		$class = 'error';
	}
	else {
		$class = 'updated';
	}
	
	echo '<div ' . ( $error ? 'id="facebook_warning" ' : '') . 'class="' . $class . ' fade' . '"><p>'. $message . '</p></div>';
}

function fb_construct_fields($placement, $children, $parent = null, $object = null) {
	$options = get_option('fb_options');

	if ($placement == 'widget') {
		echo fb_construct_fields_children('widget', $children, null, $object);
	}
	else if ($placement == 'settings') {

		if ($parent) {
			$enabled = isset($options[$parent['name']]['enabled']);
			if (isset($parent['image'])) {
				echo '<div class="fb_admin_image">';
				echo '<img src="' . esc_url( $parent['image'] ) . '"/>';
			} else {
				echo '<div>';
			}
			echo '<h3>';
			echo '<input type="checkbox" name="fb_options[' . esc_attr( $parent['name'] ). '][enabled]" value="true" id="' . esc_attr( $parent['name'] ) . '" ' . checked( $enabled, 1, false ) . ' onclick="toggleOptions(\'' . esc_js( $parent['name'] ) . '\', [\'' . esc_js( $parent['name'] ) . '_table\'])">';
			echo ' <label for="' . esc_attr($parent['name']) . '">' . esc_html( $parent['label'] ) . '</label></h3>';
			echo '<p class="description">' . esc_html( $parent['description'] ) . ' <a href="' . esc_url( $parent['help_link'] ) . '" target="_new" title="' . esc_attr( $parent['description'] ) . '">' . __( 'Read more', 'facebook') . '</a></p>';
		} else {
			$enabled = true;
			echo '<div>';
		}

		echo '<table class="form-table" id="' . esc_attr( $parent['name'] ) . '_table" style="display:' . ( $enabled ?'block':'none') . '">
						<tbody>';

		echo fb_construct_fields_children('settings', $children, $parent);

		echo '</tbody>
					</table>';
			echo '</div>';

	}
}

function fb_construct_fields_children($place, $fields, $parent = null, $object = null) {

	if ( $place == 'widget' ) {
		$options = $object->get_settings();
		$parent_name = $object->number;
	} elseif ($place == 'settings') {
		$options = get_option('fb_options');
		$parent_name = $parent['name'];
	}

	if ( $place == 'widget' ) {
		foreach ( $fields as $c => $field ) {
			$field['value'] = fb_array_default(
				$options, $parent_name, $field['name'], (
					empty($parent['name']['enabled']) ?
						fb_array_default($field, 'default', '') : ''
				)
			);
			$field['name'] = $object->get_field_name( $field['name'] );
			$fields[$c] = $field;
		}
	}
	elseif ($place == 'settings') {
		foreach ($fields as $c => $field) {
			if ($parent) {
				$value = fb_array_default(
					$options, $parent['name'], $field['name'], (
						empty($options[$parent['name']]['enabled']) ?
							fb_array_default($field, 'default', '') : ''
					)
				);
			}
			else {
				$value = fb_array_default(
					$options, $field['name'],
					fb_array_default($field, 'default', '')
				);
			}

			$parent_js_array = '';
			if ($parent) {
				$parent_js_array = '[' . $parent['name'] . ']';
			}

			$field['value'] = $value;
			$field['name'] = "fb_options$parent_js_array"."[" . $field['name'] ."]";
			$fields[$c] = $field;

		}
	}

	return fb_fields($fields, $place);
}

function fb_array_default() { // $array, $keys..., $default
	$keys = func_get_args();
	$array = array_shift($keys);
	$default = array_pop($keys);
	$key = array_shift($keys);
	if (!isset($array[$key])) {
		return $default;
	}
	$array = $array[$key];
	if (sizeof($keys)>0) {
		array_unshift($keys, $array);
		array_push($keys, $default);
		return call_user_func_array('fb_array_default', $keys);
	}
	return $array;
}

function fb_fields($fields, $place='settings') {
	$buffer = '';
	foreach ($fields as $field) {
		$buffer .= fb_field($field, $place);
	}
	return $buffer;
}

function fb_field($field, $place='settings') {
	extract($field);

	if (!isset($label)) {
		$label = trim(
			ucfirst(
				str_replace(
					array("_", "]"), " ",
					array_pop(
						explode('[', $name)
					)
				)
			)
		);
	}
	$label = sprintf(
		'<label for="%1$s">%2$s</label>',
		esc_attr($name),
		esc_html($label)
	);

	if (isset($help_link)) {
		$help = sprintf(
			'<a href="%s" target="_new" title="%s" class="wp_help_link">[?]</a>',
			esc_attr($help_link),
			esc_attr($help_text)
		);
	} else {
		$help = sprintf(
			'<span title="%s" class="wp_help_hover">[?]</span>',
			esc_attr($help_text)
		);
	}

	$widget = call_user_func("fb_field_$type", $field, $place);

	switch ($place) {
		case 'widget':
			if ($type=='checkbox') {
				$field_pattern = '<p>%3$s %1$s %2$s</p>';
			} else {
				$field_pattern = '<p>%1$s: %2$s<br />%3$s</p>';
			}
			break;
		case 'settings':
			$field_pattern = '<tr valign="top"><th scope="row">%1$s %2$s</th><td>%3$s</td></tr>';
			break;
	}

	return sprintf(
		$field_pattern,
		$label,
		$help,
		$widget
	);

}

function fb_field_text($field, $place='settings') {
	return sprintf(
		'<input type="text" id="%1$s" name="%1$s" value="%2$s" %3$s/>',
		esc_attr($field['name']),
		esc_attr($field['value']),
		$place=='widget' ? 'class="widefat"' : ""
	);
}

function fb_field_checkbox($field, $place='settings') {
	$onclick = '';	
		
	if (isset($field['onclick'])) {
		$onclick = $field['onclick'];
	}
		
	return sprintf(
		'<input type="checkbox" id="%1$s" name="%1$s" onclick="%2$s" value="true" %3$s />',
		esc_attr($field['name']),
		esc_js( $onclick ),
		checked($field['value'], 'true', false)
	);
}

function fb_field_dropdown($field, $place='settings') {
	$buffer = sprintf(
		'<select id="%1$s" name="%1$s" %2$s>',
		esc_attr($field['name']),
		$place=='widget' ? 'class="widefat"' : ""
	);

	foreach ($field['options'] as $option_value => $option_label) {
		$buffer .= sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr($option_value),
			selected($field['value'], $option_value, false),
			esc_html($option_label)
		);
	}

	$buffer .= '</select>';
	return $buffer;
}

function fb_field_disabled_text($field, $place='settings') {
	return $field['disabled_text'];
}

function fb_get_user_meta( $user_id, $meta_key, $single = false ) {
	$override = apply_filters( 'fb_get_user_meta', false, $user_id, $meta_key, $single );
	if ( false !== $override )
		return $override;

	return get_user_meta( $user_id, $meta_key, $single );
}

function fb_update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
	$override = apply_filters( 'fb_update_user_meta', false, $user_id, $meta_key, $meta_value, $prev_value );
	if ( false !== $override )
		return $override;

	return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
}

function fb_delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {
	$override = apply_filters( 'fb_delete_user_meta', false, $user_id, $meta_key, $meta_value );
	if ( false !== $override )
		return $override;

	return delete_user_meta( $user_id, $meta_key, $meta_value );
}


function fb_option_name($field){
	switch ($field) {
		case 'app_id':
			return 'App ID';
			break;
		case 'app_secret':
			return 'App secret';
			break;
		case 'app_namespace':
			return 'App namespace';
			break;
		case 'social_publisher':
			return "Social Publisher";
			break;
		case 'recommendations_bar':
			return "Recommendations Bar";
			break;
		case 'like':
			return "Like Button";
			break;
		case 'subscribe':
			return "Subscribe Button";
			break;
		case 'send':
			return "Send Button";
			break;
		case 'comments':
			return "Comments Box";
			break;
		default:
			return '';
			break;
	}
}

?>