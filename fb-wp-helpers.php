<?php
function fb_admin_dialog($message, $error = false) {
	if ( $error )
		$class = 'error';
	else
		$class = 'updated';

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
			echo '<p class="description">' . esc_html( $parent['description'] ) . ' <a href="' . esc_url( $parent['help_link'] ) . '" target="_blank" title="' . esc_attr( $parent['description'] ) . '">' . esc_html( __( 'Read more', 'facebook') ) . '</a></p>';
		} else {
			$enabled = true;
			echo '<div>';
		}

		echo '<table class="form-table" id="' . esc_attr( $parent['name'] ) . '_table" style="display:' . ( $enabled ? 'block':'none' ) . '">
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
	} else if ($place == 'settings') {
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

	if ( ! isset($label) ) {
		$label = trim(
			ucfirst(
				str_replace(
					array('_', ']'), ' ',
					array_pop(
						explode('[', $name)
					)
				)
			)
		);
	}
	$label = '<label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label>';

	if ( isset($help_link) )
		$help = '<a href="' . esc_url( $help_link, array( 'http', 'https' ) ) . '" target="_blank" title="' . esc_html( $help_text ) . '" class="wp_help_link">[?]</a>';
	else
		$help = '<span title="' . esc_attr( $help_text ) . '" class="wp_help_hover">[?]</span>';

	$widget = call_user_func( "fb_field_$type", $field, $place );

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

/**
 * Build an input HTML element of type=text based on field values and the intended display section
 *
 * @since 1.0
 * @param array $field associative array of field values
 * @param string $place allow special handling for widget display
 * @return string HTML input element
 */
function fb_field_text( $field, $place='settings' ) {
	$id_name = esc_attr( $field['name'] );
	$input = '<input type="text" id="' . $id_name . '" name="' . $id_name . '"';
	if ( $place === 'widget' )
		$input .= ' class="widefat"';
	if ( ! empty( $field['value'] ) )
		$input .= ' value="' . esc_attr( $field['value'] ) . '"';
	if ( array_key_exists( 'required', $field ) && $field['required'] )
		$input .= ' required';
	$input .= ' />';
	return $input;
}

/**
 * Build an input HTML element of type=checkbox based on field values and the intended display section
 *
 * @since 1.0
 * @param array $field associative array of field values
 * @param string $place allow special handling for widget display
 * @return string HTML input element
 */
function fb_field_checkbox( $field, $place='settings' ) {
	$onclick = '';
	if ( isset($field['onclick']) )
		$onclick = esc_attr( esc_js( $field['onclick'] ) );

	if (isset($field['options'])) {
		$items = array();
		foreach ($field['options'] as $option_value => $option_label) {
			if ( ! isset( $field['value'][$option_value] ) )
				$field['value'][$option_label] = '';

			$id_name = esc_attr( $field['name'] . "[$option_label]" );
			$item = '<label for="' . $id_name . '">' . esc_html( $option_label ) . '</label><input type="checkbox" class="multicheckbox" id="' . $id_name . '" name="' . $id_name . '" value="true"' . checked( $field['value'][$option_label], 'true', false );
			if ( $onclick )
				$item .= ' onclick="' . $onclick . '"';
			$item .= ' />';

			$items[] = $item;
			unset( $id_name );
			unset( $item );
		}
		return implode( '', $items );
	} else {
		$id_name = esc_attr( $field['name'] );
		$item = '<input type="checkbox" id="' . $id_name . '" name="' . $id_name . '" value="true"' . checked( $field['value'], 'true', false );
		if ( $onclick )
			$item .= ' onclick="' . $onclick . '"';
		$item .= ' />';
		return $item;
	}
}

/**
 * Build a select HTML element based on options provided in the field variable
 *
 * @since 1.0
 * @param array $field associative array of field values
 * @param string $place allow special handling for widget display
 * @return string HTML select element
 */
function fb_field_dropdown( $field, $place='settings' ) {
	$options = array();
	foreach ($field['options'] as $option_value => $option_label) {
		$options[] = '<option value="' . esc_attr( $option_value ) . '"' . selected($field['value'], $option_value, false) . '>' . esc_html( $option_label ) . '</option>';
	}

	if ( empty($options) )
		return '';

	$id_name = esc_attr( $field['name'] );
	$select = '<select id="' . $id_name . '" name="' . $id_name . '"';
	if ( $place === 'widget' )
		$select .= ' class="widefat"';
	$select .= '>';
	$select .= implode( '', $options );
	$select .= '</select>';

	return $select;
}

function fb_field_disabled_text( $field, $place='settings' ) {
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
			return __( 'App ID', 'facebook' );
			break;
		case 'app_secret':
			return __( 'App secret', 'facebook' );
			break;
		case 'app_namespace':
			return __( 'App namespace', 'facebook' );
			break;
		case 'social_publisher':
			return __( 'Social Publisher', 'facebook' );
			break;
		case 'recommendations_bar':
			return __( 'Recommendations Bar', 'facebook' );
			break;
		case 'like':
			return __( 'Like Button', 'facebook' );
			break;
		case 'subscribe':
			return __( 'Subscribe Button', 'facebook' );
			break;
		case 'send':
			return __( 'Send Button', 'facebook' );
			break;
		case 'comments':
			return __( 'Comments Box', 'facebook' );
			break;
		default:
			return '';
			break;
	}
}

function fb_sanitize_options($options_array) {
	foreach ($options_array as $key => $value) {
		if (is_array($value))
			$options_array[$key] = fb_sanitize_options($value);
		else
			$options_array[$key] = sanitize_text_field($value);
	}
	return $options_array;
}


?>
