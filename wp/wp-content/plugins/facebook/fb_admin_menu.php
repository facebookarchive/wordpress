<?php
// create custom plugin settings menu
add_action( 'admin_init', 'fb_admin_menu_settings' );
add_action('admin_menu', 'fb_create_menu');

function fb_create_menu() {
	//create new top-level menu
	$page = add_menu_page('Facebook Plugin Settings', 'Facebook', 'administrator', __FILE__, 'fb_settings_page', plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_print_styles-' . $page, 'fb_admin_style');
	add_action( 'admin_enqueue_scripts', 'fb_admin_scripts' );
}

function fb_admin_style() {
	wp_enqueue_style('fb_admin');
}


function fb_admin_scripts( $hook_suffix ) {
	wp_register_script( 'fb_admin', plugins_url('/fb_admin.js', __FILE__) );
	wp_enqueue_script( 'fb_admin' );
}

// __return_false for no desc
function fb_admin_menu_settings() {
	$options = get_option('fb_options');
	
	wp_register_style('fb_admin', plugins_url('style_admin.css', __FILE__));
	
	register_setting( 'fb_options', 'fb_options', 'fb_options_validate');
	
	add_settings_section('fb_section_main', 'Main Settings', 'fb_section_main', 'fb_options' );
	add_settings_field('fb_field_app_id', 'App ID', 'fb_field_app_id', 'fb_options', 'fb_section_main');
	add_settings_field('fb_field_app_secret', 'App Secret', 'fb_field_app_secret', 'fb_options', 'fb_section_main');
	add_settings_field('fb_field_enable_fb', 'Enable Facebook for WordPress', 'fb_field_enable_fb', 'fb_options', 'fb_section_main');
}

function fb_settings_page() {
	?>
	<div class="wrap">
		<div class="facebook-logo"></div>
		<h2>Facebook for WordPress Settings</h2>
		<p>The official Facebook for WordPress plugin.</p>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'fb_options' );
			
			print "<h3>Main Settings</h3>
			<p></p>";
			fb_get_main_settings_fields();
			
			print "<h3>Like Button</h3>
			<p>The Like button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user's friends' News Feed with a link back to your website.</p>";
			fb_get_like_fields();
			
			print '<h3>Subscribe Button</h3>
			<p>The Subscribe button lets a user subscribe to your public updates on Facebook.</p>';
			fb_get_subscribe_fields();
			
			print '<h3>Send Button</h3>
			<p>The Send Button allows users to easily send content to their friends. People will have the option to send your URL in a message to their Facebook friends, to the group wall of one of their Facebook groups, and as an email to any email address.</p>';
			fb_get_send_fields();
			
			print '<h3>Comments</h3>
			<p>Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution.</p>';
			fb_get_comments_fields();
			
			print '<h3>Recommendation Bar</h3>
			<p>The Recommendations Bar allows users to like content, get recommendations, and share what they\'re reading with their friends.</p>';
			fb_get_recommendations_bar_fields();
			
			print '<h3>Social Publisher</h3>';
			fb_get_social_publisher_fields();
			
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// validate our options
function fb_options_validate($input) {
	/*
	if (!defined('FB_APP_SECRET')) {
		// secrets are 32 bytes long and made of hex values
		$input['app_secret'] = trim($input['app_secret']);
		if(! preg_match('/^[a-f0-9]{32}$/i', $input['app_secret'])) {
		  $input['app_secret'] = '';
		}
	}

	if (!defined('FB_APP_ID')) {
		// app ids are big integers
		$input['app_id'] = trim($input['app_id']);
		if(! preg_match('/^[0-9]+$/i', $input['app_id'])) {
		  $input['app_id'] = '';
		}
	}

	if (!defined('FB_FANPAGE')) {
		// fanpage ids are big integers
		$input['fanpage'] = trim($input['fanpage']);
		if(! preg_match('/^[0-9]+$/i', $input['fanpage'])) {
		  $input['fanpage'] = '';
		}
	}

	$input = apply_filters('fb_validate_options',$input); // filter to let sub-plugins validate their options too
	*/
	return $input;
}

function fb_section_main() {
	echo '<p></p>';
}

function fb_field_app_id() {
	$options = get_option('fb_options');
	echo '<a href="#" target="_new" title="TODO">[?]</a>&nbsp; <input type="text" name="fb_options[app_id]" value="' . $options['app_id'] . '" size="40" />';
}

function fb_field_app_secret() {
	$options = get_option('fb_options');
	echo '<a href="#" target="_new" title="TODO">[?]</a>&nbsp; <input type="text" name="fb_options[app_secret]" value="' . $options['app_secret'] . '" size="40" />';
}

function fb_field_enable_fb() {
	$options = get_option('fb_options');
	
	echo '<input type="checkbox" name="fb_options[enable_fb]" value="true" ' . checked(isset($options['enable_fb']), 1, false) . '" />';
}

function fb_construct_fields($placement, $children, $parent = null) {
	$options = get_option('fb_options');
	
	if ($placement == 'widget') {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Like ' . esc_attr(get_bloginfo('name')) . ' on Facebook', 'text_domain' );
		}
		
		$children_fields = fb_construct_fields_children($placement, $children, $parent);
		
		/*echo '<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>';*/
		}
	else if ($placement == 'settings') {
		$children_fields = fb_construct_fields_children($placement, $children, $parent);
		
		echo '<table class="form-table">
						<tbody>';
		
		if ($parent) {
			echo '	<tr valign="top">
								<th scope="row"><strong>Enable</strong></th>
								<td><a href="' . $parent['help_link'] . '" target="_new" title="' . $parent['help_text'] . '" style=" text-decoration: none;">[?]</a>&nbsp; <input type="checkbox" name="fb_options[' . $parent['name'] . '][enabled]" value="true" id="' . $parent['name'] . '" ' . checked(isset($options[$parent['name']]), 1, false) . ' onclick="toggleOptions(\'' . $parent['name'] . '\', [\'' . implode("','", $children_fields['names']) . '\'])"></td>
								</tr>';
		}
			echo $children_fields['output'];
			
			echo '</tbody>
						</table>';
	}
}

function fb_construct_fields_children($placement, $children, $parent = null) {
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
			$help_link = '<a href="' . $child['help_link'] . '" target="_new" title="' . $child['help_text'] . '" style=" text-decoration: none;">[?]</a>';
		}
		
		$parent_js_array = '';
		
		if ($parent) {
			$parent_js_array = '[' . $parent['name'] . ']';
			
			if (isset($options[$parent['name']][$child['name']])) {
				$child_value = $options[$parent['name']][$child['name']];
			}
		}
		else {
			if (isset($options[$child['name']])) {
				$child_value = $options[$child['name']];
			}
		}
		
		switch ($child['field_type']) {
			case 'dropdown':
				$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
						<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
						<td>' . $help_link . '&nbsp;';
				
				$children_output .= '<select name="fb_options' . $parent_js_array . '[' . $child['name'] . ']">';
				
				if (isset($child_value)) {
					foreach ($child['options'] as $option) {
						$children_output .= '<option value="' . $option . '" ' . selected( $child_value, $option, false ) . '>' . $option . '</option>';
					}
				}
				else {
					foreach ($child['options'] as $option) {
						$children_output .= '<option value="' . $option . '">' . $option . '</option>';
					}
				}
				
				$children_output .= '</select></td></tr>';
				
				break;
			case 'checkbox':
				$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
						<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
						<td>' . $help_link . '&nbsp; <input type="checkbox" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="true"' . checked(isset($child_value), 1, false) . '></td>
						</tr>';
				break;
			case 'text':
				$text_field_value = '';
				
				if (isset($child_value)) {
					$text_field_value = $child_value;
				}
				
				$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
						<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
						<td>' . $help_link . '&nbsp; <input type="text" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="' . $text_field_value . '"></td>
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
	
	return $return;
}

function fb_get_main_settings_fields() {
	$children = array(array('name' => 'app_id',
													'field_type' => 'text',
													'help_text' => 'Your app id.',
													),
										array('name' => 'app_secret',
													'field_type' => 'text',
													'help_text' => 'Your app secret.',
													),
										);
	
	fb_construct_fields('settings', $children);
}

function fb_get_like_fields() {
	$parent = array('name' => 'like',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/like/',
									);
	
	$children = array(array('name' => 'send',
													'field_type' => 'checkbox',
													'help_text' => 'Include a send button.',
													),
										array('name' => 'layout',
													'field_type' => 'dropdown',
													'options' => array('standard', 'button_count', 'box_count'),
													'help_text' => 'Determines the size and amount of social context at the bottom.',
													),
										array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'show_faces',
													'field_type' => 'checkbox',
													'help_text' => 'Show profile pictures below the button.  Applicable to standard layout only.',
													),
										array('name' => 'position',
													'field_type' => 'dropdown',
													'options' => array('top', 'bottom', 'both'),
													'help_text' => 'Where the button will display on the page or post.',
													),
										array('name' => 'action',
													'field_type' => 'dropdown',
													'options' => array('like', 'recommend'),
													'help_text' => 'The verb to display in the button.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the button.',
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'options' => array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'),
													'help_text' => 'The font of the button.',
													),
										);
	
	fb_construct_fields('settings', $children, $parent);
}

function fb_get_subscribe_fields() {
	$parent = array('name' => 'subscribe',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
									);
	
	$children = array(array('name' => 'layout',
													'field_type' => 'dropdown',
													'options' => array('standard', 'button_count', 'box_count'),
													'help_text' => 'Determines the size and amount of social context at the bottom.',
													),
										array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'show_faces',
													'field_type' => 'checkbox',
													'help_text' => 'Show profile pictures below the button.  Applicable to standard layout only.',
													),
										array('name' => 'colorscheme',
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
	
	fb_construct_fields('settings', $children, $parent);
}


function fb_get_send_fields() {
	$parent = array('name' => 'send',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/send/',
									);
	
	$children = array(array('name' => 'colorscheme',
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
	
	fb_construct_fields('settings', $children, $parent);
}




function fb_get_comments_fields() {
	$parent = array('name' => 'comments',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/comments/',
									);
	
	$children = array(array('name' => 'num_posts',
													'field_type' => 'text',
													'help_text' => 'The number of posts to display by default.',
													),
										array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										);
	
	fb_construct_fields('settings', $children, $parent);
}

function fb_get_recommendations_bar_fields() {
	$parent = array('name' => 'recommendations_bar',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/recommendationsbar/',
									);
	
	$children = array(array('name' => 'trigger',
													'field_type' => 'text',
													'help_text' => 'This specifies the percent of the page the user must scroll down before the plugin is expanded.',
													),
										array('name' => 'read_time',
													'field_type' => 'text',
													'help_text' => 'The number of seconds the plugin will wait until it expands.',
													),
										array('name' => 'action',
													'field_type' => 'dropdown',
													'options' => array('like', 'recommend'),
													'help_text' => 'The verb to display in the button.',
													),
										array('name' => 'side',
													'field_type' => 'dropdown',
													'options' => array('left', 'right'),	
													'help_text' => 'The side of the window that the plugin will display.',
													),
										);
	
	fb_construct_fields('settings', $children, $parent);
}

function fb_get_social_publisher_fields() {
	$parent = array('name' => 'social_publisher',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/subscribe/',
									);
	
	$children = array(array('name' => 'publish_to_authors_facebook_profile',
													'field_type' => 'dropdown',
													'options' => array('standard', 'button_count', 'box_count'),
													'help_text' => 'Determines the size and amount of social context at the bottom.',
													),
										array('name' => 'publish_to_fan_page',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										);
	
	fb_construct_fields('settings', $children, $parent);
}

function fb_get_recommendations_box_fields() {
	$children = array(array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'height',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'border_color',
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
	
	fb_construct_fields('settings', $children);
}

function fb_get_activity_feed_fields() {
	$children = array(array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'height',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'border_color',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'font',
													'field_type' => 'dropdown',
													'options' => array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'),
													'help_text' => 'The font of the plugin.',
													),
										array('name' => 'recommendations',
													'field_type' => 'checkbox',
													'help_text' => 'Includes recommendations.',
													),
										);
	
	fb_construct_fields('settings', $children);
}


?>