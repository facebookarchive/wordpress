<?php
// create custom plugin settings menu
add_action( 'admin_init', 'fb_admin_menu_settings' );
add_action( 'admin_menu', 'fb_create_menu' );
add_action( 'admin_menu', 'fb_add_settings_pages', 10 );

/**
 * Create the Settings menu in the admin control panel
 *
 * @since 1.0
 */
function fb_create_menu() {
	//create new top-level menu
	$page = add_menu_page( sprintf( __( '%s Plugin Settings', 'facebook' ), 'Facebook'), 'Facebook', 'manage_options', 'facebook-settings', 'fb_settings_page', plugins_url( 'images/icon-bw.png', __FILE__) );

	//call register settings function
	add_action( 'admin_print_styles-' . $page, 'fb_admin_style');
	add_action( 'admin_print_scripts-' . $page, 'fb_admin_scripts' );
}


function fb_notify_user_of_plugin_conflicts()
{
	$plugins_list = apply_filters( 'all_plugins', get_plugins() );

	//static array of potentially conflicting plugins
	$og_conflicting_plugins_static = array( "Facebook", "Hello Dolly", "Open Graph", "WordPress SEO by Yoast", "Pure Web Brilliant's Social Graph Control" );

	$num_conflicting = 0;
	$conflicting_plugins = array();
	foreach ( $plugins_list as $val ) {
		$plugin_name = $val['Name'];
		if($plugin_name == "Facebook") {
			continue;
		}
		
		if( in_array($plugin_name, $og_conflicting_plugins_static) ) {
			$num_conflicting += 1;
			array_push( $conflicting_plugins, $plugin_name );
		}
	}

	//if there are more than 1 plugins relying on Open Graph, warn the user on this plugins page
	if ( $num_conflicting >= 1 ) {
		echo "<h2>" . "The following plugins may conflict with Facebook: \n" . "</h2>";
		foreach( $conflicting_plugins as $val ) {
			echo $val;
		}
		echo "<br></br>Consider deactivating a few, so only one is activated at once\n";
		echo "<a href='plugins.php' aria-label='Plugins 0'>Disable Plugins Here</a>";
	}
	else {
		echo "No issues here!";
	}
}

/**
 * Link to settings from the plugin listing page
 *
 * @since 1.0
 * @param array $links links displayed under the plugin
 * @param string $file plugin main file path relative to plugin dir
 * @return array links array passed in, possibly with our settings link added
 */
function fb_plugin_action_links( $links, $file ) {
	if ( $file === plugin_basename( dirname(__FILE__) . '/facebook.php' ) )
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?' . http_build_query( array( 'page' => 'facebook-settings' ) ) ) . '">' . __( 'Settings' ) . '</a>';
	return $links;
}
// Customize plugins listing
add_filter( 'plugin_action_links', 'fb_plugin_action_links', 10, 2 );

/**
 * Add admin styles to the head
 *
 * @since 1.0
 */
function fb_admin_style() {
	wp_enqueue_style( 'fb_admin', plugins_url( 'style/style-admin.css', __FILE__), array(), '1.0' );
	wp_enqueue_style( 'fb_loopj', plugins_url( 'scripts/loopj-jquery-tokeninput/styles/token-input-facebook.css', __FILE__ ), array(), '1.0' );
}

/**
 * Gray icon swapped out with color icon onhover
 *
 * @since 1.0
 */
function fb_admin_menu_style() { ?>
<style type="text/css">
#toplevel_page_fb-hacks-fb-admin-menu img {
  display: none;
}
#toplevel_page_fb-hacks-fb-admin-menu .wp-menu-image {
  background-image: url(<?php echo esc_url( plugins_url( 'images/icon-bw.png', __FILE__ ) ); ?>);
  background-repeat: no-repeat;
  background-position: 6px 6px;
}
#toplevel_page_fb-hacks-fb-admin-menu .wp-menu-image:hover {
  background-image: url( <?php echo esc_url( plugins_url( 'images/icon.png', __FILE__ ) ); ?> );
}
</style><?php
}

/**
 * Add admin scripts to the head
 *
 * @since 1.0
 */
function fb_admin_scripts( $hook_suffix ) {
	wp_enqueue_script( 'fb_admin', plugins_url( 'scripts/fb-admin.js', __FILE__ ), array(), '1.0', true );
	wp_enqueue_script( 'fb_loopj', plugins_url( 'scripts/loopj-jquery-tokeninput/jquery.tokeninput.js', __FILE__ ), array(), '1.0', true );

  wp_localize_script( 'fb_admin', 'FBNonce', array(
    // URL to wp-admin/admin-ajax.php to process the request
    'ajaxurl' => admin_url( 'admin-ajax.php' ),

    // generate a nonce with a unique ID "myajax-post-comment-nonce"
    // so that you can check it later when an AJAX request is sent
    'autocompleteNonce' => wp_create_nonce( 'fb_autocomplete_nonce' ),
    )
  );
}

/**
 * Queue scripts and styles up
 *
 * @since 1.0
 */
function fb_admin_menu_settings() {
	register_setting( 'fb_options', 'fb_options', 'fb_options_validate' );

	//call register settings function
	add_action( 'admin_print_styles', 'fb_admin_style' );
	add_action( 'admin_print_scripts', 'fb_admin_scripts' );
}

/**
 * The settings page
 *
 * @since 1.0
 */
function fb_settings_page() {
	global $facebook;
  
	$like_button_options = array(
		"enabled" => "true", 
		"send" => "true", 
		"layout" => "button_count", 
		"action" => "like", 
		"colorscheme" => "light", 
		"font" => "arial", 
		"position" => "both", 
		"ref" => "wp",
		"href" => "http://developers.facebook.com/wordpress",
  );
	
	?>
	<div class="wrap">
		<div class="facebook-logo"></div>
		<h2><?php echo esc_html__( 'Facebook for WordPress', 'facebook' ) . ' ' . fb_get_like_button($like_button_options); ?></h2>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'fb_options' );

			if ( !isset( $facebook ) ) {
				echo '<h2>' . esc_html__( 'Step 1: Create an App', 'facebook' ) . '</h2>';
				echo '<p><strong>' . sprintf( esc_html( __( 'If you already have a Facebook app for this website, skip to %sStep 2%s.', 'facebook' ) ), '<a href="#step-2">', '</a>' ) . '</strong></p><br>';
				echo '<p>' . sprintf( esc_html( __( 'If you don\'t already have an app for this website, go to %s and click the "Create New App" button.	You\'ll see a dialog like the one below.	Fill this in and click "Continue".', 'facebook' ) ), '<a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a>' );
				echo '<p><img src="' . plugins_url( 'images/nux_create_app.png', __FILE__ ) . '"></p>';

				echo '<a name="step-2"></a><h2>' . esc_html__( 'Step 2: Set up the App', 'facebook' ) . '</h2>';
				echo sprintf( esc_html( __( 'Next, set up your app so that it looks like the settings below.	Make sure you set your app\'s icon and image, too.	If you already have an app and skipped Step 1, you can view your app settings by going to %s', 'facebook' ) ), '<a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a>.</p>' );
				echo '<p><img src="' . plugins_url( 'images/nux_app_settings.png', __FILE__ ) . '"></p>';

				echo '<h2>' . esc_html__( 'Step 3: WordPress settings', 'facebook' ) . '</h2>';
				echo '<p>' . esc_html__( 'Now, based on what you entered in Step 2, fill in the settings below and Save.	Once saved, additional options will appear on this page.', 'facebook' ) . '</p>';
				fb_get_main_settings_fields();
			}
			else {
				echo '<h2>' . esc_html__( 'Main Settings', 'facebook' ) . '</h2>';

				echo '<p>' . sprintf( esc_html( __( 'Get your App ID, Secret, and Namespace at %s. %sIf you already have a Facebook app for this website, it\'s important that you use the same information below%s.', 'facebook' ) ), '<a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a>', '<strong>', '</strong>' ) . '</p>';
				fb_get_main_settings_fields();

				echo '<h2>' . esc_html__( 'Post and Page Settings', 'facebook' ) . '</h2>';

				echo '<p>' . sprintf( esc_html( __( 'These settings affect Pages and Posts only.	Additional Social Plugins are also available in the %sWidgets settings%s.', 'facebook' ) ), '<a href="widgets.php">', '</a>' );

				
				fb_notify_user_of_plugin_conflicts();
				fb_get_social_publisher_fields();
				fb_get_like_fields();
				fb_get_subscribe_fields();
				fb_get_send_fields();
				fb_get_comments_fields();
				fb_get_recommendations_bar_fields();
			}

			submit_button();
      
      fb_get_debug_output();
      
			fb_insights_admin();
			?>
		</form>
	</div>
	<?php
}

function fb_insights_admin($appid = 0) {
  $payload = json_encode(fb_get_settings($appid));

	echo "<img src='http://www.facebook.com/impression.php?plugin=wordpress&payload=$payload'>";
}

function fb_get_debug_output($appid = 0) {
  $bloginfo = get_bloginfo('version');
  
  $debug = fb_get_settings($appid);

  $debug['wp_ver'] = $bloginfo;
  
  echo '<a href="#" id="debug-output-link" onclick="fbShowDebugInfo(); return false">debug info</a><div id="debug-output">' . json_encode($debug) . '</div>';
}

function fb_get_settings($appid) {
  global $fb_ver;
  
  $options = get_option('fb_options');

	if (!$appid) {
		$appid = $options['app_id'];
	}

 	if ( !empty( $options['social_publisher']['publish_to_fan_page'] ) )
		preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);

	$options['social_publisher']['publish_to_fan_page'] = array();
	if ( !empty( $fan_page_info[0] ) ) {
		$options['social_publisher']['publish_to_fan_page']['page_name'] = $fan_page_info[0][1];
		$options['social_publisher']['publish_to_fan_page']['page_id'] = $fan_page_info[0][2];
	}

	$enabled_options = array();

	if (isset($options) && isset($options['social_publisher'])){
		$enabled_options['social_publisher'] = $options['social_publisher'];
	}

	if (isset($options) && isset($options['recommendations_bar'])){
		$enabled_options['recommendations_bar'] = $options['recommendations_bar'];
	}

	if (isset($options) && isset($options['subscribe'])){
		$enabled_options['subscribe'] = $options['subscribe'];
	}

	if (isset($options) && isset($options['comments'])){
		$enabled_options['comments'] = $options['comments'];
	}

	if (isset($options) && isset($options['send'])){
		$enabled_options['send'] = $options['send'];
	}

	$sidebar_widgets = wp_get_sidebars_widgets();

	$fb_sidebar_widgets = array();

	$sidebars = array( 'sidebar-1', 'sidebar-2', 'sidebar-3', 'sidebar-4', 'sidebar-5' );

	foreach ($sidebars as $sidebar) {
		if (empty($sidebar_widgets[$sidebar])) {
			continue;
		}
		foreach($sidebar_widgets[$sidebar] as $key => $val) {
			if (strpos($val, 'fb_') !== false){
				$fb_sidebar_widgets[$sidebar][] = $val;
			}
		}
	}

	$payload = array( 'appid' => $appid, 'version' => $fb_ver, 'domain' => $_SERVER['HTTP_HOST'] );
  
  $payload = array_merge($fb_sidebar_widgets, $payload, $enabled_options);
  
	return $payload;
}

/**
 * Gets the main settings
 *
 * @since 1.0
 */
function fb_get_main_settings_fields() {
	$children = array(
		array('name' => 'app_id',
			'label' => 'App ID',
			'type' => 'text',
			'help_text' => __( 'Your App ID.', 'facebook' ),
			),
		array('name' => 'app_secret',
			'type' => 'text',
			'help_text' => __( 'Your App Secret.', 'facebook' ),
			),
		array('name' => 'app_namespace',
			'type' => 'text',
			'help_text' => __( 'Your App Namespace.', 'facebook' ),
			),
		);

	fb_construct_fields('settings', $children);
}

/**
 * Add Facebook options to other sub-menus
 *
 * @since 1.0
 */
function fb_add_settings_pages() {
	add_submenu_page(
		 'edit-comments.php',
		 _x('Facebook', 'admin page title', 'facebook'),
		 _x('Facebook', 'admin menu title', 'facebook'),
		 'moderate_comments',
		 'fb_comments',
		 'fb_settings_page'
	);
	add_submenu_page(
		 'facebook-settings',
		 _x('Insights', 'admin page title', 'facebook'),
		 _x('Insights', 'admin menu title', 'facebook'),
		 'publish_posts',
		 'fb_insights',
		 'fb_insights_page'
	);
}


function fb_options_validate($input) {
	// TODO wire this up to field definitions!
	$output = array();
	foreach ($input as $key=>$value) {
		switch ($key) {
		case 'app_id':
			$label = 'App ID';
			if (fb_options_validate_present($value, $label)) {
				$value = fb_options_validate_integer($value, $label);
			}
			break;
		case 'app_secret':
			$label = 'App secret';
			if (fb_options_validate_present($value, $label)) {
				$value = fb_options_validate_hex($value, $label);
			}
			break;
		case 'app_namespace':
			$label = 'App namespace';
			$value = fb_options_validate_namespace($value, $label);
			break;
		case 'social_publisher':
			$label_prefix = "The Social Publisher's";
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'recommendations_bar':
			$label_prefix = "The Recommendations Bar's";
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'like':
			$label_prefix = "The Like Button's";
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'subscribe':
			$label_prefix = "The Subscribe Button's";
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'send':
			$label_prefix = "The Send Button's";
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'comments':
			$label_prefix = "The Comments Box's";
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		default:
			$value = '';
			break;
		}
		$output[$key] = $value;
	}

	return $output;
}

function fb_options_validate_present($value, $label) {
	if ($value == '') {
		add_settings_error('fb_options', '', "$label must be present");
		return false;
	}
	return true;
}

function fb_options_validate_integer($value, $label, $sanitize=true) {
	if ($sanitize) {
		$value = sanitize_text_field( $value );
	}
	if (!preg_match('/^[0-9]+$/', $value)) {
		$value = preg_replace('/[^0-9]/', '', $value);
		add_settings_error('fb_options', '', "$label has been converted to an integer");
	}
	return $value;
}

function fb_options_validate_hex($value, $label, $sanitize=true) {
	if ($sanitize) {
		$value = sanitize_text_field( $value );
	}
	if (!preg_match('/^[0-9a-f]+$/i', $value)) {
		$value = preg_replace('/[^0-9a-f]/', '', strtolower($value));
		add_settings_error('fb_options', '', "$label has been converted to a hex string");
	}
	return $value;
}

function fb_options_validate_namespace($value, $label, $sanitize=true) {
	if ($sanitize) {
		$value = sanitize_text_field( $value );
	}
	if ($value != '' && !preg_match('/^[-_a-z]+$/', $value)) {
		$value = preg_replace('/[^-_a-z]/', '', strtolower($value));
		add_settings_error('fb_options', '', "$label has been converted to contain only lowercase letters, dashes and underscores");
	}
	return $value;
}

function fb_options_validate_plugin($array, $label_prefix, $sanitize=true) {
	// TODO desperately needs to be driven from plugin definitions
	if ($sanitize) {
		foreach($array as $key=>$value) {
			$array[$key] = sanitize_text_field( $value );
		}
	}
	if (!isset($array['enabled']) || !$array['enabled']) {
		return $array;
	}
	foreach($array as $key=>$value) {
		$label = '';
		switch ($key) {
			case 'trigger':
				$label = "$label_prefix trigger";
				break;
			case 'read_time':
				$label = "$label_prefix read time";
				break;
			case 'width':
				$label = "$label_prefix width";
				break;
			case 'num_posts':
				$label = "$label_prefix number of posts";
				break;
		}
		if ($label != '' && fb_options_validate_present($value, $label)) {
			$value = fb_options_validate_integer($value, $label);
		}
		$array[$key] = $value;
	}
	return $array;
}

?>