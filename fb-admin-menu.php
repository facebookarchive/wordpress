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

function fb_insights_page() {
	$options = get_option('fb_options');

	if ( ! empty( $options['app_id'] ))
		echo '<script type="text/javascript">window.location=' . json_encode( 'https://www.facebook.com/insights/?' . http_build_query( array( 'sk' => 'ao_' . $options['app_id'] ) ) ) . ';</script>';
}

/**
 * Function to check if the wordpress user has plugins that may conflict
 * with the Facebook plugin (due to Open Graph). 
 */
function fb_notify_user_of_plugin_conflicts() {
	$og_conflicting_plugins = apply_filters( 'fb_conflicting_plugins', array(
		'http://wordpress.org/extend/plugins/facebook/',
		'http://wordpress.org/extend/plugins/opengraph/',
		'http://yoast.com/wordpress/seo/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpseoplugin',
		'http://wordbooker.tty.org.uk',
		'http://ottopress.com/wordpress-plugins/simple-facebook-connect/',
		'http://www.whiletrue.it',
		'http://aaroncollegeman.com/sharepress'
	) );

	// allow for short circuit
	if ( ! is_array( $og_conflicting_plugins ) || empty( $og_conflicting_plugins ) )
		return;

	//fetch activated plugins
	$plugins_list = get_option( 'active_plugins', array() ); 

	$conflicting_plugins = array();

	// iterate through activated plugins, checking if they are in the list of conflict plugins
	foreach ( $plugins_list as $val ) {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $val );
		if ( ! ( array_key_exists( 'PluginURI', $plugin_data ) && array_key_exists( 'Name', $plugin_data ) ) )
			continue;

		$plugin_uri = $plugin_data['PluginURI'];

		if( $plugin_uri === 'http://wordpress.org/extend/plugins/facebook/' )
			continue;

		if( in_array( $plugin_uri, $og_conflicting_plugins, true ) )
			$conflicting_plugins[] = $plugin_data['Name'];
	}

	//if there are more than 1 plugins relying on Open Graph, warn the user on this plugins page
	if ( ! empty( $conflicting_plugins ) ) {
		fb_admin_dialog( sprintf( __( 'You have plugins installed that could potentially conflict with the Facebook plugin. Please consider disabling the following plugins on the %s:', 'facebook' ) . '<br />' . implode( ', ', $conflicting_plugins ), '<a href="plugins.php">' . esc_html( __( 'Plugins Settings page', 'facebook' ) ) . '</a>' ), true);
	}
}
add_action( 'fb_notify_plugin_conflicts', 'fb_notify_user_of_plugin_conflicts' );

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
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_style( 'fb_admin', plugins_url( 'style/style-admin' . $suffix . '.css', __FILE__), array(), '1.0' );
	wp_enqueue_style( 'fb_loopj', plugins_url( 'scripts/loopj-jquery-tokeninput/styles/token-input-facebook' . $suffix . '.css', __FILE__ ), array(), '1.6.0' );
	wp_enqueue_style( 'tipsy', plugins_url( 'style/tipsy' . $suffix . '.css', __FILE__), array(), '1.0.0a' );
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
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
	wp_enqueue_script( 'fb_admin', plugins_url( 'scripts/fb-admin' . $suffix . '.js', __FILE__ ), array(), '1.0', true );
	wp_enqueue_script( 'fb_loopj', plugins_url( 'scripts/loopj-jquery-tokeninput/jquery.tokeninput' . $suffix . '.js', __FILE__ ), array('jquery'), '1.6.0', true );
	wp_enqueue_script( 'tipsy', plugins_url( 'scripts/jquery.tipsy' . $suffix . '.js', __FILE__ ), array('jquery'), '1.0.0a', true );

	wp_localize_script( 'fb_admin', 'FBNonce', array(
		// URL to wp-admin/admin-ajax.php to process the request
		'ajaxurl' => admin_url( 'admin-ajax.php' ),

		// generate a nonce with a unique ID "myajax-post-comment-nonce"
		// so that you can check it later when an AJAX request is sent
		'autocompleteNonce' => wp_create_nonce( 'fb_autocomplete_nonce' ),
	) );
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
		'enabled' => 'true',
		'send' => 'true',
		'layout' => 'button_count',
		'action' => 'like',
		'colorscheme' => 'light',
		'font' => 'arial',
		'position' => 'both',
		'ref' => 'wp',
		'href' => 'http://developers.facebook.com/wordpress'
	);

	?>
	<div class="wrap">
		<div class="facebook-logo"></div>
		<h2><?php echo esc_html( sprintf( __( '%s for WordPress', 'facebook' ), 'Facebook' ) ) . ' ' . fb_get_like_button($like_button_options); ?></h2>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'fb_options' );

			if ( ! isset( $facebook ) ) {
				echo '<h2>' . esc_html( __( 'Step 1: Create an App', 'facebook' ) ) . '</h2>';
				echo '<p><strong>' . sprintf( esc_html( __( 'If you already have a Facebook app for this website, skip to %s.', 'facebook' ) ), '<a href="#step-2">' . esc_html( __( 'Step 2', 'facebook' ) ) . '</a>' ) . '</strong></p><br>';
				echo '<p>' . sprintf( esc_html( __( 'If you don\'t already have an app for this website, go to %s and click the "Create New App" button. You\'ll see a dialog like the one below. Fill this in and click "Continue".', 'facebook' ) ), '<a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a>' );
				echo '<p><img src="' . plugins_url( 'images/nux_create_app.png', __FILE__ ) . '"></p>';
				echo '<p>' . esc_html( __( 'Here are for some recommendations for filling this form out.', 'facebook' ) ) . '</p>';

				echo '<b>' . esc_html( __( 'App Name:', 'facebook' ) ) . '</b> ' . esc_html( get_bloginfo('name') ) . '<br />';
				echo '<b>' . esc_html( __( 'App Namespace:', 'facebook' ) ) . '</b> ' . esc_html( strtolower(str_replace( ' ', '-', get_bloginfo('name') ) ) ) . '<br />';

				echo '<h2 id="step-2">' . esc_html( __( 'Step 2: Set up the App', 'facebook' ) ) . '</h2>';
				echo sprintf( esc_html( __( 'Next, set up your app so that it looks like the settings below. Make sure you set your app\'s icon and image, too.	If you already have an app and skipped Step 1, you can view your app settings by going to %s', 'facebook' ) ), '<a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a>.</p>' );

				echo '<p>' . esc_html( __( 'Here are for some recommendations for filling this form out, based on where this plugin is installed.', 'facebook' ) ) . '</p>';
				echo '<b> App Domains: </b>' . esc_html( parse_url( home_url('/'), PHP_URL_HOST ) ) . '<br />';
				echo '<b> Site URL and Mobile Web URL: </b>' . esc_html( get_bloginfo( 'wpurl' ) ) . '<br />';

				echo '<p><img src="' . esc_url( plugins_url( 'images/nux_app_settings.png', __FILE__ ) ) . '" style="border: 1px solid #ccc; margin: 5px; padding: 5px;"></p>';

				echo '<h2>' . esc_html( __( 'Step 3: WordPress settings', 'facebook' ) ) . '</h2>';
				echo '<p>' . esc_html( __( 'Now, based on what you entered in Step 2, fill in the settings below and Save. Once saved, additional options will appear on this page.', 'facebook' ) ) . '</p>';
				fb_get_main_settings_fields();
			} else {
				echo '<h2>' . esc_html( __( 'Main Settings', 'facebook' ) ) . '</h2>';

				$options = get_option('fb_options');
				if ( empty($options['app_id']) || empty($options['app_secret']) )
					echo '<p>' . sprintf( esc_html( __( 'Get your App ID, Secret, and Namespace at %s.', 'facebook' ) ) . '<strong>' . esc_html( __( 'If you already have a Facebook app for this website, it\'s important that you use the same information below.', 'facebook' ) ) . '</strong>', '<a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a>' ) . '</p>';
				else
					echo '<p><a href="' . esc_url( 'https://developers.facebook.com/apps/' . $options['app_id'] ) . '">' . esc_html( __( 'Edit app settings on Facebook', 'facebook' ) ) . '</a></p>';
				fb_get_main_settings_fields();

				echo '<h2>' . esc_html( __( 'Post and Page Settings', 'facebook' ) ) . '</h2>';

				echo '<p>' . esc_html( __( 'These settings affect Pages and Posts only.', 'facebook' ) ) . ' ' . sprintf( esc_html( __( 'Additional Social Plugins are also available in the %s.', 'facebook' ) ), '<a href="widgets.php">' . esc_html( __( 'Widgets settings', 'facebook' ) ) . '</a>' );

				do_action( 'fb_notify_plugin_conflicts' );
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

function fb_insights_admin( $appid = 0 ) {
	echo '<img src="http://www.facebook.com/impression.php?' . http_build_query( array( 'plugin' => 'wordpress', 'payload' => json_encode( fb_get_settings($appid) ) ) ) . '" width="1" height="1" alt=" " />';
}

function fb_get_debug_output($appid = 0) {
	$bloginfo = get_bloginfo('version');

	$debug = fb_get_settings($appid);

	$debug['wp_ver'] = $bloginfo;

	echo '<a href="#" id="debug-output-link" onclick="fbShowDebugInfo(); return false">' . esc_html( __( 'debug info', 'facebook' ) ) . '</a><div id="debug-output">' . esc_html( json_encode($debug) ) . '</div>';
}

function fb_get_settings($appid) {
	global $fb_ver;

	$options = get_option('fb_options');

	if ( ! $appid )
		$appid = $options['app_id'];

	if ( ! empty( $options['social_publisher']['publish_to_fan_page'] ) )
		preg_match_all("/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['social_publisher']['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER);

	$options['social_publisher']['publish_to_fan_page'] = array();
	if ( ! empty( $fan_page_info[0] ) ) {
		$options['social_publisher']['publish_to_fan_page']['page_name'] = $fan_page_info[0][1];
		$options['social_publisher']['publish_to_fan_page']['page_id'] = $fan_page_info[0][2];
	}

	$enabled_options = array();
	if ( isset( $options ) ) {
		if ( isset( $options['social_publisher'] ) )
			$enabled_options['social_publisher'] = $options['social_publisher'];

		if ( isset( $options['recommendations_bar'] ) )
			$enabled_options['recommendations_bar'] = $options['recommendations_bar'];

		if ( isset( $options['subscribe'] ) )
			$enabled_options['subscribe'] = $options['subscribe'];

		if ( isset( $options['comments'] ) )
			$enabled_options['comments'] = $options['comments'];

		if ( isset( $options['send'] ) )
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

	$payload = array_merge( $fb_sidebar_widgets, $payload, $enabled_options );

	return $payload;
}

/**
 * Gets the main settings
 *
 * @since 1.0
 */
function fb_get_main_settings_fields() {
	$children = array(
		array(
			'name' => 'app_id',
			'label' => __( 'App ID', 'facebook' ),
			'type' => 'text',
			'help_text' => __( 'Your App ID.', 'facebook' ),
			'required' => true
		),
		array(
			'name' => 'app_secret',
			'type' => 'text',
			'help_text' => __( 'Your App Secret.', 'facebook' ),
			'required' => true
		),
		array(
			'name' => 'app_namespace',
			'type' => 'text',
			'help_text' => __( 'Your App Namespace.', 'facebook' )
		)
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
		 'Facebook',
		 'Facebook',
		 'moderate_comments',
		 'fb_comments',
		 'fb_settings_page'
	);
	add_submenu_page(
		 'facebook-settings',
		 _x( 'Insights', 'Facebook insights stats tool', 'facebook' ),
		 _x( 'Insights', 'Facebook insights stats tool', 'facebook' ),
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
			$label = __( 'App ID', 'facebook' );
			if (fb_options_validate_present($value, $label)) {
				$value = fb_options_validate_integer($value, $label);
			}
			break;
		case 'app_secret':
			$label = __( 'App secret', 'facebook' );
			if (fb_options_validate_present($value, $label)) {
				$value = fb_options_validate_hex($value, $label);
			}
			break;
		case 'app_namespace':
			$label = __( 'App namespace', 'facebook' );
			$value = fb_options_validate_namespace($value, $label);
			break;
		case 'social_publisher':
			$label_prefix = __( 'The Social Publisher\'s', 'facebook' );
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'recommendations_bar':
			$label_prefix = __( 'The Recommendations Bar\'s', 'facebook' );
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'like':
			$label_prefix = __( 'The Like Button\'s', 'facebook' );
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'subscribe':
			$label_prefix = __( 'The Subscribe Button\'s', 'facebook' );
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'send':
			$label_prefix = __( 'The Send Button\'s', 'facebook' );
			$value = fb_options_validate_plugin($value, $label_prefix);
			break;
		case 'comments':
			$label_prefix = __( 'The Comments Box\'s', 'facebook' );
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
		add_settings_error('fb_options', '', sprintf( __( '%s must be present', 'facebook' ), $label ) );
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
		add_settings_error('fb_options', '', sprintf( __( '%s has been converted to an integer', 'facebook' ), $label ) );
	}
	return $value;
}

function fb_options_validate_hex($value, $label, $sanitize=true) {
	if ($sanitize) {
		$value = sanitize_text_field( $value );
	}
	if (!preg_match('/^[0-9a-f]+$/i', $value)) {
		$value = preg_replace('/[^0-9a-f]/', '', strtolower($value));
		add_settings_error('fb_options', '', sprintf( __( '%s has been converted to a hex string', 'facebook' ), $label ) );
	}
	return $value;
}

function fb_options_validate_namespace($value, $label, $sanitize=true) {
	if ($sanitize) {
		$value = sanitize_text_field( $value );
	}
	if ($value != '' && !preg_match('/^[-_a-z]+$/', $value)) {
		$value = preg_replace('/[^-_a-z]/', '', strtolower($value));
		add_settings_error('fb_options', '', sprintf( __( '%s has been converted to contain only lowercase letters, dashes and underscores', 'facebook' ), $label ) );
	}
	return $value;
}

function fb_options_validate_plugin($array, $label_prefix, $sanitize=true) {
	// TODO desperately needs to be driven from plugin definitions
	if ($sanitize) {
		$array = fb_sanitize_options($array);
	}
	if (!isset($array['enabled']) || !$array['enabled']) {
		return $array;
	}
	foreach($array as $key=>$value) {
		$label = '';
		switch ($key) {
			case 'trigger':
				$label = sprintf( __( '%s trigger', 'facebook' ), $label_prefix );
				break;
			case 'read_time':
				$label = sprintf( __( '%s read time', 'facebook' ), $label_prefix );
				break;
			case 'width':
				$label = sprintf( __( '%s width', 'facebook' ), $label_prefix );
				break;
			case 'num_posts':
				$label = sprintf( __( '%s number of posts', 'facebook' ), $label_prefix );
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
