<?php
function fb_admin_dialog($message, $error = false) {
		echo '<div ' . ( $error ? 'id="facebook_warning" ' : '') . 'class="updated fade' . '"><p><strong>'. $message . '</strong></p></div>';
}

function fb_construct_fields($placement, $children, $parent = null, $object = null) {
	$options = get_option('fb_options');

	if ($placement == 'widget') {
		$children_fields = fb_construct_fields_children('widget', $children, null, $object);

		echo $children_fields['output'];
	}
	else if ($placement == 'settings') {
		$children_fields = fb_construct_fields_children('settings', $children, $parent);

		echo '<table class="form-table">
						<tbody>';

		if ($parent) {
			echo '	<tr valign="top">
								<th scope="row"><strong>Enable</strong></th>
								<td><a href="' . $parent['help_link'] . '" target="_new" title="' . $parent['help_text'] . '" style=" text-decoration: none;">[?]</a>&nbsp; <input type="checkbox" name="fb_options[' . $parent['name'] . '][enabled]" value="true" id="' . $parent['name'] . '" ' . checked(isset($options[$parent['name']]['enabled']), 1, false) . ' onclick="toggleOptions(\'' . $parent['name'] . '\', [\'' . implode("','", $children_fields['names']) . '\'])"></td>
								</tr>';
		}

		echo $children_fields['output'];

		echo '</tbody>
					</table>';
	}
}

function fb_construct_fields_children($placement, $children, $parent = null, $object = null) {
	$return = '';
	$checkbox = '<input class="checkbox"';

	if ( $placement == 'widget' ) {
		$children_output = '';

		$instance = $object->get_settings();

		foreach ( $children as $child ) {
			$object_id = $object->number;

			$name = $child['name'];
		  $name_label = isset($child['label']) ? $child['label'] : ucfirst(str_replace("_", " ", $name));
			$name_field_id = $object->get_field_id( $name );
			$name_field_name = $object->get_field_name( $name );
			$help = $child['help_text'];

			$value = '';
			if ( isset($instance[$object_id][$name]) ) {
				$value = $instance[$object_id][$name];
			}
			elseif ( isset($child['default']) && !$parent['enable'] ) {
				$value = $child['default'];
			}
			$value = esc_attr($value);

			$help_link = '';
			if (!isset($child['help_link'])) {
				$help_link = '<a href="#" target="_new" title="' . $help . '" onclick="return false;" style="color: #aaa; text-decoration: none;">[?]</a>';
			}
			else {
				$help_link = '<a href="' . $child['help_link'] . '" target="_new" title="' . $help . '" style=" text-decoration: none;">[?]</a>';
			}

			$label = '<label for="' . $name_field_id . '">' . $name_label . '</label>';

			$children_output .= '<p>';
			switch ($child['field_type']) {

				case 'dropdown':
					$children_output .= "$label: $help_link<br />";
					$children_output .= '<select name="' . $name_field_name . '" id="' . $name_field_id . '" class="widefat">';
					foreach ($child['options'] as $key => $val) {
						$children_output .= '<option value="' . $key . '" ' . selected( $value, $key, false ) . '>' . ucfirst(str_replace("_", " ", $val)) . '</option>';
					}
					$children_output .= "</select>";
					break;

				case 'checkbox':
					$children_output .= $checkbox . ' id="' . $name_field_id . '" name="' . $name_field_name . '" type="checkbox" value="true"' . checked($value, 'true', false) . '>';
					$children_output .= " $label $help_link<br/>";
					break;

				case 'text':
					$text_field_value = '';
					$children_output .= "$label: $help_link<br />";
					$children_output .= '<input id="' . $name_field_id . '" name="' . $name_field_name . '" type="text" value="' . $value . '" class="widefat">';
					break;

				case 'disabled_text':
					$text_field_value = '';
					$children_output .= "$help_link $label &nbsp;";
					$children_output .= $child['disabled_text'];
					break;
			}
			$children_output .= '</p>';

			$children_output = str_replace('<br/></p><p>$checkbox', '<br/>$checkbox', $children_output);
			// Tighly group checkboxes.

			$return['output'] = $children_output;
		}
	}
	elseif ($placement == 'settings') {
		$options = get_option('fb_options');

		print '<!--';
		print_r($options);
		print '-->';

		$display = ' style="display: none" ';

		if ($parent) {
			if (isset($options[$parent['name']]['enabled']) && $options[$parent['name']]['enabled'] == 'true') {
				$display = '';
			}
		}
		else {
			$display = '';
		}

		$children_output = '';

		foreach ($children as $child) {
			$help_link = '';

			if (!isset($child['help_link'])) {
				$help_link = '<a href="#" target="_new" title="' . $child['help_text'] . '" onclick="return false;" style="color: #aaa; text-decoration: none;">[?]</a>';
			}
			else {
				$help_link = '<a href="' . $child['help_link'] . '" target="_new" title="' . $child['help_text'] . '" style="text-decoration: none;">[?]</a>';
			}

			$parent_js_array = '';

			if ($parent) {
				$parent_js_array = '[' . $parent['name'] . ']';

				if (isset($options[$parent['name']][$child['name']])) {
					$value = $options[$parent['name']][$child['name']];
				}
				elseif (isset($child['default']) && empty($options[$parent['name']]['enabled'])) {
					$value = $child['default'];
				}
				else {
					$value = '';
				}
			}
			else {
				if (isset($options[$child['name']])) {
					$value = $options[$child['name']];
				}
				elseif (isset($child['default'])) {
					$value = $child['default'];
				}
				else {
					$value = '';
				}
			}

			switch ($child['field_type']) {
				case 'dropdown':
					$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
							<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
							<td>' . $help_link . '&nbsp;';

					$children_output .= '<select name="fb_options' . $parent_js_array . '[' . $child['name'] . ']">';

					if (isset($value)) {
						foreach ($child['options'] as $key => $val) {
							$children_output .= '<option value="' . $key . '" ' . selected( $value, $key, false ) . '>' . $val . '</option>';
						}
					}
					else {
						foreach ($child['options'] as $key => $val) {
							$children_output .= '<option value="' . $key . '">' . $val . '</option>';
						}
					}

					$children_output .= '</select></td></tr>';

					break;
				case 'checkbox':
					$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
							<th scope="row">' . ucwords(str_replace('_', ' ', $child['name'])) . '</th>
							<td>' . $help_link . '&nbsp; <input type="checkbox" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="true"' . checked($value, 'true', false) . '></td>
							</tr>';
					break;
				case 'text':
					$text_field_value = '';

					if (isset($value)) {
						$text_field_value = $value;
					}

					$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
							<th scope="row">' . ucwords(str_replace('_', ' ', $child['name'])) . '</th>
							<td>' . $help_link . '&nbsp; <input type="text" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="' . $text_field_value . '"></td>
							</tr>';
					break;
				case 'disabled_text':
					$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
							<th scope="row">' . ucwords(str_replace('_', ' ', $child['name'])) . '</th>
							<td>' . $help_link . '&nbsp; ' . $child['disabled_text'] . '</td>
							</tr>';
					break;
			}

			if ($parent['name']) {
				$children_names[] = $parent['name'] . '_' . $child['name'];
			}
			else {
				$children_names[] = $child['name'];
			}
		}

		$return['output'] = $children_output;
		$return['names'] = $children_names;
	}

	return $return;
}
?>