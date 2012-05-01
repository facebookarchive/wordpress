<?php
// create custom plugin settings menu
add_action( 'admin_init', 'fb_admin_menu_settings' );
add_action('admin_menu', 'fb_create_menu');

function fb_create_menu() {
	//create new top-level menu
	$page = add_menu_page('Facebook Plugin Settings', 'Facebook', 'administrator', __FILE__, 'fb_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_print_styles-' . $page, 'fb_admin_style');
}

function fb_admin_style() {
		wp_enqueue_style('fb_admin');
}

// __return_false for no desc
function fb_admin_menu_settings() {
	wp_register_style('fb_admin', plugins_url('style_admin.css', __FILE__));
		
	register_setting( 'fb_options', 'fb_options', 'fb_options_validate');
	
	add_settings_section('fb_section_main', 'Main Settings', 'fb_section_main', 'fb_options' );
	add_settings_field('fb_field_app_id', 'App ID', 'fb_field_app_id', 'fb_options', 'fb_section_main');
	add_settings_field('fb_field_app_secret', 'App Secret', 'fb_field_app_secret', 'fb_options', 'fb_section_main');
	
	add_settings_section('fb_section_like_send_subscribe', 'Like, Send, Subscribe Buttons on Posts', 'fb_section_like_send_subscribe', 'fb_options' );
	add_settings_field('fb_field_like', 'Like Button', 'fb_field_like', 'fb_options', 'fb_section_like_send_subscribe');
	add_settings_field('fb_field_subscribe', 'Subscribe Button', 'fb_field_subscribe', 'fb_options', 'fb_section_like_send_subscribe');
	add_settings_field('fb_field_send', 'Send Button', 'fb_field_send', 'fb_options', 'fb_section_like_send_subscribe');
	
	add_settings_section('fb_section_comments', 'Comments on Posts', 'fb_section_comments', 'fb_options' );
	add_settings_field('fb_field_comments', 'Comments on Posts', 'fb_field_comments', 'fb_options', 'fb_section_comments');
	
	add_settings_section('fb_section_social_reader', 'Social Reader for Posts', 'fb_section_social_reader', 'fb_options' );
	add_settings_field('fb_field_recommendations_bar', 'Recommendations Bar', 'fb_field_recommendations_bar', 'fb_options', 'fb_section_social_reader');
	
	add_settings_section('fb_section_social_publisher', 'Social Publisher for Posts', 'fb_section_social_publisher', 'fb_options' );
	add_settings_field('fb_field_og_publish', 'Publish New Posts to User Profile', 'fb_field_og_publish', 'fb_options', 'fb_section_social_publisher');
	add_settings_field('fb_field_posts_to_fb_page', 'Publish New Posts to Facebook Page', 'fb_field_posts_to_fb_page', 'fb_options', 'fb_section_social_publisher');
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
			do_settings_sections( 'fb_options' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/*
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
*/



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






function fb_section_like_send_subscribe() {
	echo '<p></p>';
}

function fb_field_like() {
	$options = get_option('fb_options');
	echo '<a href="https://developers.facebook.com/docs/reference/plugins/like/" target="_new" title="The Like button lets a user share your content with friends on Facebook. When the user clicks the Like button on your site, a story appears in the user\'s friends\' News Feed with a link back to your website. Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_like]" value="true" checked="' . isset($options['enable_like']) . '" />';
}

function fb_field_subscribe() {
	$options = get_option('fb_options');
	echo '<a href="https://developers.facebook.com/docs/reference/plugins/subscribe/" target="_new" title="The Subscribe button lets a user subscribe to your public updates on Facebook. Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_subscribe]" value="true" checked="' . isset($options['enable_subscribe']) . '" />';
}

function fb_field_send() {
	$options = get_option('fb_options');
	echo '<a href="https://developers.facebook.com/docs/reference/plugins/send/" target="_new" title="The Send Button allows users to easily send content to their friends. People will have the option to send your URL in a message to their Facebook friends, to the group wall of one of their Facebook groups, and as an email to any email address. Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_send]" value="true" checked="' . isset($options['enable_send']) . '" />';
}





function fb_section_comments() {
	echo '<p></p>';
}

function fb_field_comments() {
	$options = get_option('fb_options');
	echo '<a href="https://developers.facebook.com/docs/reference/plugins/comments/" target="_new" title="Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution. Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_comments]" value="true" checked="' . isset($options['enable_comments']) . '" />';
}




function fb_section_social_reader() {
	echo '<p></p>';
}

function fb_field_recommendations_bar() {
	$options = get_option('fb_options');
	echo '<a href="https://developers.facebook.com/docs/reference/plugins/recommendationsbar/" target="_new" title="The Recommendations Bar allows users to like content, get recommendations, and share what they\'re reading with their friends.  Click to learn more.">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_recommendations_bar]" value="true" checked="' . isset($options['enable_recommendations_bar']) . '" />';
}



function fb_section_social_publisher() {
	echo '<p></p>';
}


function fb_field_og_publish() {
	$options = get_option('fb_options');
	echo '<a href="#" target="_new" title="TODO">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_og_publish]" value="true" checked="' . isset($options['enable_og_publish']) . '" />';
}
function fb_field_posts_to_fb_page() {
	$options = get_option('fb_options');
	echo '<a href="#" target="_new" title="TODO">[?]</a>&nbsp; <input type="checkbox" name="fb_options[enable_posts_to_fb_page]" value="true" checked="' . isset($options['enable_posts_to_fb_page']) . '" />';
}
?>