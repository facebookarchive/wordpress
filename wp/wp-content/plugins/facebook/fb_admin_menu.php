<?php
// create custom plugin settings menu
add_action('admin_menu', 'fb_create_menu');

function fb_create_menu() {

	//create new top-level menu
	add_menu_page('Facebook Plugin Settings', 'Facebook', 'administrator', __FILE__, 'fb_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'fb_admin_menu_settings' );
}


// __return_false for no desc
function fb_admin_menu_settings() {
	register_setting( 'fb_options', 'fb_options', 'fb_options_validate');
	
	add_settings_section('fb_section_main', 'Main Settings', 'fb_section_main', 'fb_options' );
	add_settings_field('fb_field_app_id', 'App ID', 'fb_field_app_id', 'fb_options', 'fb_section_main');
	add_settings_field('fb_field_app_secret', 'App Secret', 'fb_field_app_secret', 'fb_options', 'fb_section_main');
	
	add_settings_section('fb_section_like_send_subscribe', 'Like, Send, Subscribe Buttons', 'fb_section_like_send_subscribe', 'fb_options' );
	add_settings_field('fb_field_like', 'Like Button on Posts', 'fb_field_like', 'fb_options', 'fb_section_like_send_subscribe');
	add_settings_field('fb_field_subscribe', 'Subscribe Button on Posts', 'fb_field_subscribe', 'fb_options', 'fb_section_like_send_subscribe');
	add_settings_field('fb_field_send', 'Send Button on Posts', 'fb_field_send', 'fb_options', 'fb_section_like_send_subscribe');
	
	add_settings_section('fb_section_comments', 'Comments', 'fb_section_comments', 'fb_options' );
	add_settings_field('fb_field_comments', 'Comments on Posts', 'fb_field_comments', 'fb_options', 'fb_section_comments');
	
	add_settings_section('fb_section_social_reader', 'Social Reader', 'fb_section_social_reader', 'fb_options' );
	add_settings_field('fb_field_recommendations_bar', 'Recommendations Bar on Posts', 'fb_field_recommendations_bar', 'fb_options', 'fb_section_social_reader');
	
	add_settings_section('fb_section_social_publisher', 'Social Publisher', 'fb_section_social_publisher', 'fb_options' );
	add_settings_field('fb_field_og_publish', 'Open Graph Publish for new Posts', 'fb_field_og_publish', 'fb_options', 'fb_section_social_publisher');
	add_settings_field('fb_field_posts_to_fb_page', 'Send new Posts to Facebook Page', 'fb_field_posts_to_fb_page', 'fb_options', 'fb_section_social_publisher');
}



function fb_settings_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Facebook for WordPress Settings</h2>
		<p>The official Facebook for WordPress plugin.</p>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'fb_options' );
			do_settings_sections( 'fb_options' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}



// validate our options
function fb_options_validate($input) {
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
	return $input;
}




function fb_section_main() {
	echo '<p></p>';
}

function fb_field_app_id() {
	$options = get_option('otto_options');
	echo "<input id='otto_first_field' name='otto_options[first_field]' size='40' type='text' value='{$options['first_field']}' />";
}

function fb_field_app_secret() {
	$options = get_option('otto_options');
	echo "<input id='otto_second_field' name='otto_options[second_field]' size='40' type='text' value='{$options['second_field']}' />";
}




function fb_section_like_send_subscribe() {
	echo '<p></p>';
}

function fb_field_like() {
	$options = get_option('otto_options');
	echo "<input id='otto_third_field' name='otto_options[third_field]' size='40' type='text' value='{$options['third_field']}' />";
}

function fb_field_subscribe() {
	$options = get_option('otto_options');
	echo "<input id='otto_fourth_field' name='otto_options[fourth_field]' size='40' type='text' value='{$options['fourth_field']}' />";
}

function fb_field_send() {
	$options = get_option('otto_options');
	echo "<input id='otto_fourth_field' name='otto_options[fourth_field]' size='40' type='text' value='{$options['fourth_field']}' />";
}





function fb_section_comments() {
	echo '<p></p>';
}

function fb_field_comments() {
	$options = get_option('otto_options');
	echo "<input id='otto_third_field' name='otto_options[third_field]' size='40' type='text' value='{$options['third_field']}' />";
}




function fb_section_social_reader() {
	echo '<p></p>';
}

function fb_field_recommendations_bar() {
	$options = get_option('otto_options');
	echo "<input id='otto_third_field' name='otto_options[third_field]' size='40' type='text' value='{$options['third_field']}' />";
}



function fb_section_social_publisher() {
	echo '<p></p>';
}


function fb_field_og_publish() {
	$options = get_option('otto_options');
	echo "<input id='otto_third_field' name='otto_options[third_field]' size='40' type='text' value='{$options['third_field']}' />";
}
function fb_field_posts_to_fb_page() {
	$options = get_option('otto_options');
	echo "<input id='otto_third_field' name='otto_options[third_field]' size='40' type='text' value='{$options['third_field']}' />";
}
?>