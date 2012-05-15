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

	if ($placement == 'widget') {
		$children_output = '';

		$instance = $object->get_settings();

		foreach ($children as $child) {
			$object_id = $object->number;

			if (isset($instance[$object_id][$child['name']])) {
				$child_value = $instance[$object_id][$child['name']];
			}
			elseif (isset($child['default'])) {
				$child_value = $child['default'];
			}
			else {
				$child_value = '';
			}

			$help_link = '';

			if (!isset($child['help_link'])) {
				$help_link = '<a href="#" target="_new" title="' . $child['help_text'] . '" onclick="return false;" style="color: #aaa; text-decoration: none;">[?]</a> &nbsp;';
			}
			else {
				$help_link = '<a href="' . $child['help_link'] . '" target="_new" title="' . $child['help_text'] . '" style=" text-decoration: none;">[?]</a> &nbsp;';
			}

			$children_output .= '<p>';
			switch ($child['field_type']) {
				case 'dropdown':
					$children_output .= $help_link . ' <label for="' . $object->get_field_id( $child['name'] ) . '">' . ucwords(str_replace("_", " ", $child['name'])) . '</label> &nbsp;';

					$children_output .= '<select name="' . $object->get_field_name( $child['name'] ) . '" id="' . $object->get_field_id( $child['name'] ) . '">';

					if (isset($child_value)) {
						foreach ($child['options'] as $key => $val) {
							$children_output .= '<option value="' . $key . '" ' . selected( $child_value, $key, false ) . '>' . $val . '</option>';
						}
					}
					else {
						foreach ($child['options'] as $key => $val) {
							$children_output .= '<option value="' . $key . '">' . $val . '</option>';
						}
					}

					$children_output .= '</select>';

					break;
				case 'checkbox':
					$children_output .= $help_link . '<input class="checkbox" id="' . $object->get_field_id( $child['name'] ) . '" name="' . $object->get_field_name( $child['name'] ) . '" type="checkbox" value="true"' . checked($child_value, 'true', false) . '>';
					$children_output .= ' <label for="' . $object->get_field_id( $child['name'] ) . '">' . ucwords(str_replace('_', ' ', $child['name'])) . '</label><br>';

					break;
				case 'text':
					$text_field_value = '';

					$children_output .= $help_link . ' <label for="' . $object->get_field_id( $child['name'] ) . '">' . ucwords(str_replace('_', ' ', $child['name'])) . '</label> &nbsp;';

					$children_output .= '<input id="' . $object->get_field_id( $child['name'] ) . '" name="' . $object->get_field_name( $child['name'] ) . '" type="text" value="' . $child_value . '">';

					break;
				case 'disabled_text':
					$text_field_value = '';

					$children_output .= $help_link . ' <label for="' . $object->get_field_id( $child['name'] ) . '">' . ucwords(str_replace('_', ' ', $child['name'])) . '</label> &nbsp;';

					$children_output .= $child['disabled_text'];

					break;
			}

			$children_output .= '</p>';

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
					$child_value = $options[$parent['name']][$child['name']];
				}
				elseif (isset($child['default'])) {
					$child_value = $child['default'];
				}
				else {
					$child_value = '';
				}
			}
			else {
				if (isset($options[$child['name']])) {
					$child_value = $options[$child['name']];
				}
				elseif (isset($child['default'])) {
					$child_value = $child['default'];
				}
				else {
					$child_value = '';
				}
			}

			switch ($child['field_type']) {
				case 'dropdown':
					$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
							<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
							<td>' . $help_link . '&nbsp;';

					$children_output .= '<select name="fb_options' . $parent_js_array . '[' . $child['name'] . ']">';

					if (isset($child_value)) {
						foreach ($child['options'] as $key => $val) {
							$children_output .= '<option value="' . $key . '" ' . selected( $child_value, $key, false ) . '>' . $val . '</option>';
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
							<td>' . $help_link . '&nbsp; <input type="checkbox" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="true"' . checked(isset($child_value), 1, false) . '></td>
							</tr>';
					break;
				case 'text':
					$text_field_value = '';

					if (isset($child_value)) {
						$text_field_value = $child_value;
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