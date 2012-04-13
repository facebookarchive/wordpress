<?php

/*
 * Activity Feed
 */
function get_sfc_activity_feed($args) {
	$args = wp_parse_args($args, array(
		'header'=>'true',
		'site'=>'',
		'bordercolor'=>'000000',
		'width'=>'260',
		'height'=>'400',
		'font'=>'lucida+grande',
		'colorscheme'=>'light',
		'recommendations'=>'false'));
	extract($args);

	if ( empty($site) ) $site = home_url('/');

	// handle parsing full url into domain and path for XFBML code
	$url = parse_url($site);
	if ( !empty( $url['host'] ) ) {
		$host = $url['host'];

		if ( !empty( $url['path'] ) && $url['path'] != '/' ) $path = "filter='{$url['path']}'";
		else $path = '';
	} else {
		$host = $site;
		$path = '';
	}
	
	return "<fb:activity site='{$host}' {$path} width='{$width}' height='{$height}' header='{$header}' colorscheme='{$colorscheme}' font='{$font}' border_color='{$bordercolor}' recommendations='{$recommendations}'></fb:activity>";
}

function sfc_activity_feed($args='') {
	echo get_sfc_activity_feed($args);
}

function sfc_activity_feed_shortcode($atts) {
	$args = shortcode_atts(array(
		'header'=>'true',
		'site'=>'',
		'bordercolor'=>'000000',
		'width'=>'260',
		'height'=>'400',
		'font'=>'lucida+grande',
		'colorscheme'=>'light'), $atts);

	return get_sfc_activity_feed($args);
}
add_shortcode('fb-activity', 'sfc_activity_feed_shortcode');

class SFC_Activity_Feed_Widget extends WP_Widget {
	function SFC_Activity_Feed_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-activity-feed', 'description' => __('Facebook Activity Feed', 'sfc'));
		$this->WP_Widget('sfc-activity', __('Facebook Activity Feed (SFC)', 'sfc'), $widget_ops);
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		sfc_activity_feed($instance);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'width'=>260, 'height'=>400, 'bordercolor'=>'000000', 'font'=>'lucida+grande', 'colorscheme'=>'light') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = intval($new_instance['width']);
		$instance['height'] = intval($new_instance['height']);
		$instance['bordercolor'] = strip_tags($new_instance['bordercolor']);
		$instance['colorscheme'] = strip_tags($new_instance['colorscheme']);
		$instance['font'] = strip_tags($new_instance['font']);
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'width'=>260, 'height'=>400, 'bordercolor'=>'000000', 'font'=>'lucida+grande', 'colorscheme'=>'light' ) );
		$title = strip_tags($instance['title']);
		$width = intval($instance['width']);
		$height = intval($instance['height']);
		$bordercolor = strip_tags($instance['bordercolor']);
		if (empty($bordercolor)) $bordercolor = '000000';
		$colorscheme = strip_tags($instance['colorscheme']);
		$font = strip_tags($instance['font']);
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width of the widget in pixels:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height of the widget in pixels:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('bordercolor'); ?>"><?php _e('Border color:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('bordercolor'); ?>" name="<?php echo $this->get_field_name('bordercolor'); ?>" type="text" value="<?php echo $bordercolor; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('colorscheme'); ?>"><?php _e('Color scheme:', 'sfc'); ?>
<select name="<?php echo $this->get_field_name('colorscheme'); ?>" id="<?php echo $this->get_field_id('colorscheme'); ?>">
<option value="light" <?php selected('light', $colorscheme); ?>><?php _e('light', 'sfc'); ?></option>
<option value="dark" <?php selected('dark', $colorscheme); ?>><?php _e('dark', 'sfc'); ?></option>
</select>
</label></p>
<p><label for="<?php echo $this->get_field_id('font'); ?>"><?php _e('Font:', 'sfc'); ?>
<select name="<?php echo $this->get_field_name('font'); ?>" id="<?php echo $this->get_field_id('font'); ?>">
<option value="arial" <?php selected('arial', $font); ?>>arial</option>
<option value="lucide+grande" <?php selected('lucide+grande', $font); ?>>lucide grande</option>
<option value="segoe+ui" <?php selected('segoe+ui', $font); ?>>segoe ui</option>
<option value="tahoma" <?php selected('tahoma', $font); ?>>tahoma</option>
<option value="trebuchet+ms" <?php selected('trebuchet+ms', $font); ?>>trebuchet ms</option>
<option value="verdana" <?php selected('verdana', $font); ?>>verdana</option>
</select>
</label></p>
		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_Activity_Feed_Widget");'));


/*
 * Recommendations box
 */
function get_sfc_recommendations($args) {
	$args = wp_parse_args($args, array(
		'header'=>'true',
		'site'=>'',
		'bordercolor'=>'000000',
		'width'=>'260',
		'height'=>'400',
		'font'=>'lucida+grande',
		'colorscheme'=>'light',
		'recommendations'=>'false'));
	extract($args);

	if (empty($site)) $site = home_url('/');

	return "<fb:recommendations site='{$site}' width='{$width}' height='{$height}' header='{$header}' colorscheme='{$colorscheme}' font='{$font}' border_color='{$bordercolor}'></fb:recommendations>";
}

function sfc_recommendations($args='') {
	echo get_sfc_recommendations($args);
}

function sfc_recommendations_shortcode($atts) {
	$args = shortcode_atts(array(
		'header'=>'true',
		'site'=>'',
		'bordercolor'=>'000000',
		'width'=>'260',
		'height'=>'400',
		'font'=>'lucida+grande',
		'colorscheme'=>'light'), $atts);

	return get_sfc_recommendations($args);
}
add_shortcode('fb-recommend', 'sfc_recommendations_shortcode');

class SFC_Recommendations_Widget extends WP_Widget {
	function SFC_Recommendations_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-recommendations', 'description' => __('Facebook Recommendations', 'sfc'));
		$this->WP_Widget('sfc-recommendations', __('Facebook Recommendations (SFC)', 'sfc'), $widget_ops);
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		sfc_recommendations($instance);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'width'=>260, 'height'=>400, 'bordercolor'=>'000000', 'font'=>'lucida+grande', 'colorscheme'=>'light') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = intval($new_instance['width']);
		$instance['height'] = intval($new_instance['height']);
		$instance['bordercolor'] = strip_tags($new_instance['bordercolor']);
		$instance['colorscheme'] = strip_tags($new_instance['colorscheme']);
		$instance['font'] = strip_tags($new_instance['font']);
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'width'=>260, 'height'=>400, 'bordercolor'=>'000000', 'font'=>'lucida+grande', 'colorscheme'=>'light' ) );
		$title = strip_tags($instance['title']);
		$width = intval($instance['width']);
		$height = intval($instance['height']);
		$bordercolor = strip_tags($instance['bordercolor']);
		if (empty($bordercolor)) $bordercolor = '000000';
		$colorscheme = strip_tags($instance['colorscheme']);
		$font = strip_tags($instance['font']);
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width of the widget in pixels:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height of the widget in pixels:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('bordercolor'); ?>"><?php _e('Border color:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('bordercolor'); ?>" name="<?php echo $this->get_field_name('bordercolor'); ?>" type="text" value="<?php echo $bordercolor; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('colorscheme'); ?>"><?php _e('Color scheme:', 'sfc'); ?>
<select name="<?php echo $this->get_field_name('colorscheme'); ?>" id="<?php echo $this->get_field_id('colorscheme'); ?>">
<option value="light" <?php selected('light', $colorscheme); ?>><?php _e('light', 'sfc'); ?></option>
<option value="dark" <?php selected('dark', $colorscheme); ?>><?php _e('dark', 'sfc'); ?></option>
</select>
</label></p>
<p><label for="<?php echo $this->get_field_id('font'); ?>"><?php _e('Font:', 'sfc'); ?>
<select name="<?php echo $this->get_field_name('font'); ?>" id="<?php echo $this->get_field_id('font'); ?>">
<option value="arial" <?php selected('arial', $font); ?>>arial</option>
<option value="lucide+grande" <?php selected('lucide+grande', $font); ?>>lucide grande</option>
<option value="segoe+ui" <?php selected('segoe+ui', $font); ?>>segoe ui</option>
<option value="tahoma" <?php selected('tahoma', $font); ?>>tahoma</option>
<option value="trebuchet+ms" <?php selected('trebuchet+ms', $font); ?>>trebuchet ms</option>
<option value="verdana" <?php selected('verdana', $font); ?>>verdana</option>
</select>
</label></p>
		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_Recommendations_Widget");'));


/*
 * Fan Box
 */


// fast check for sfc-fanbox-css request on plugin load
if (array_key_exists('sfc-fanbox-css', $_GET) && !empty($_GET['sfc-fanbox-css'])) { 
	$options = get_option('sfc_options');
	header( 'Content-Type: text/css; charset=utf-8' );
	echo $options['fanbox_css'];
	exit; // stop normal WordPress execution
}

function get_sfc_fanbox($args='') {
	$options = get_option('sfc_options');
	$args = wp_parse_args($args, array(
		'stream' => 1,
		'connections' => 10,
		'colorscheme' => 'light', // light or dark
		'width' => 200,
		'height' => 0,
		'logobar' => 1
		));
	extract($args);

	if ($options['fanpage']) $id = $options['fanpage'];
	else $id = $options['appid'];

	$retvar = '<fb:fan profile_id="'.$id.'" logobar="'.$logobar.'" stream="'.$stream.'" connections="'.$connections.'" width="'.$width.'" colorscheme="'.$colorscheme.'"';
	if ($options['fanbox_css']) {
		$retvar .= ' css="'.get_bloginfo('url').'/?sfc-fanbox-css='.$options['fanbox_time'].'"';
	}
	if ($height) $retvar .= ' height="'.$height.'"';
	$retvar .= '></fb:fan>';

	//<fb:like-box profile_id="185550966885" width="242" connections="9" stream="false" header="false"></fb:like-box>

	return $retvar;
}

function sfc_fanbox($args='') {
	echo get_sfc_fanbox($args);
}

// Shortcode for putting it into pages or posts directly
function sfc_fanbox_shortcode($atts) {
	$args = shortcode_atts(array(
		'stream' => 1,
		'connections' => 10,
		'colorscheme' => 'light', // light or dark
		'width' => 200,
		'height' => 0,
		'logobar' => 1,
	), $atts);

	return get_sfc_fanbox($args);
}
add_shortcode('fb-fanbox', 'sfc_fanbox_shortcode');

class SFC_Fan_Box_Widget extends WP_Widget {
	function SFC_Fan_Box_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-fanbox', 'description' => __('Facebook Fan Box', 'sfc'));
		$this->WP_Widget('sfc-fanbox', __('Facebook Fan Box (SFC)', 'sfc'), $widget_ops);
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$instance['stream'] = isset($instance['stream']) ? $instance['stream'] : 1;
		$instance['logobar'] = isset($instance['logobar']) ? $instance['logobar'] : 1;
		$instance['connections'] = intval($instance['connections']);
		$instance['width'] = intval($instance['width']);
		$instance['height'] = intval($instance['height']);
		?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php sfc_fanbox($instance); ?>
		<?php echo $after_widget; ?>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'connections' => '0', 'logobar'=> 0, 'stream' => 0, 'width'=>200, 'height'=>0) );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['connections'] = intval($new_instance['connections']);
		$instance['width'] = intval($new_instance['width']);
		$instance['height'] = intval($new_instance['height']);
		$instance['stream'] = $new_instance['stream'] ? 1 : 0;
		$instance['logobar'] = $new_instance['logobar'] ? 1 : 0;
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'connections' => '0', 'logobar'=> 0, 'stream' => 0, 'width'=>200, 'height'=>0) );
		$title = strip_tags($instance['title']);
		$connections = intval($instance['connections']);
		$width = intval($instance['width']);
		$height = intval($instance['height']);
		$stream = $instance['stream'] ? true : false;
		$logobar = $instance['logobar'] ? true : false;
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('logobar'); ?>"><?php _e('Show Facebook Logo Bar?', 'sfc'); ?>
<input class="checkbox" id="<?php echo $this->get_field_id('logobar'); ?>" name="<?php echo $this->get_field_name('logobar'); ?>" type="checkbox" <?php checked($logobar, true); ?> />
</label></p>
<p><label for="<?php echo $this->get_field_id('stream'); ?>"><?php _e('Show Stream Stories? ', 'sfc'); ?>
<input class="checkbox" id="<?php echo $this->get_field_id('stream'); ?>" name="<?php echo $this->get_field_name('stream'); ?>" type="checkbox" <?php checked($stream, true); ?> />
</label></p>
<p><label for="<?php echo $this->get_field_id('connections'); ?>"><?php _e('Number of Fans to Show:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" type="text" value="<?php echo $connections; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width of the widget in pixels:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height of the widget in pixels (0 for automatic):', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
</label></p>
		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_Fan_Box_Widget");'));

// add the admin sections to the sfc page
add_action('admin_init', 'sfc_fanbox_admin_init');
function sfc_fanbox_admin_init() {
	add_settings_section('sfc_fanbox', __('Fan Box Settings', 'sfc'), 'sfc_fanbox_section_callback', 'sfc');
	add_settings_field('sfc_fanbox_css', __('Fanbox Custom CSS', 'sfc'), 'sfc_fanbox_css_callback', 'sfc', 'sfc_fanbox');
}

function sfc_fanbox_section_callback() {
	echo '<p>'.__('Use this area to add any custom CSS you like to the Facebook Fan Box display.', 'sfc').'</p>';
}

function sfc_fanbox_css_callback() {
	$options = get_option('sfc_options');
	if (!isset($options['fanbox_css'])) $options['fanbox_css'] = '';
	/* good default CSS to use:

	.connect_widget .connect_widget_facebook_logo_menubar {
	}
	.fan_box .full_widget .connect_top {
	}
	.fan_box .full_widget .page_stream {
	}
	.fan_box .full_widget .connections {
	}
	*/

	?>
	<p><label><textarea name="sfc_options[fanbox_css]" class="large-text code" rows="10"><?php echo $options['fanbox_css']; ?></textarea></label></p>
<?php
}

add_filter('sfc_validate_options','sfc_fanbox_validate_options');
function sfc_fanbox_validate_options($input) {
	$input['fanbox_css'] = strip_tags($input['fanbox_css']);
	$input['fanbox_time'] = time();
	return $input;
}


/*
 * User Status
 */

class SFC_User_Status_Widget extends WP_Widget {
	function SFC_User_Status_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-status', 'description' => __('Facebook User Status (needs user profile number)', 'sfc') );
		$this->WP_Widget('sfc-userstatus', __('Facebook Status (SFC)', 'sfc'), $widget_ops);
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		
		$statuses = get_transient($this->get_field_id('statuses'));

		if ( $statuses === false || !empty( $statuses['error'] ) ) {
			$statuses = sfc_remote($instance['profileid'], 'statuses', array('access_token'=>$instance['access_token']));
			set_transient($this->get_field_id('statuses'), $statuses, 60*60); // 1 hour cache
		}
		
		if (!empty($statuses) && !empty($statuses['data'][0]['message']))
			$status = "<a href='http://www.facebook.com/{$statuses['data'][0]['from']['id']}/posts/{$statuses['data'][0]['id']}'>{$statuses['data'][0]['message']}</a>";

		echo $before_widget;
		
		if ( $title ) 
			echo $before_title . $title . $after_title;
		
		echo $status;
		
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$profileid = esc_attr($instance['profileid']);
		$access_token = esc_attr($instance['access_token']);
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('profileid'); ?>"><?php _e('Facebook User Profile Number:', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('profileid'); ?>" name="<?php echo $this->get_field_name('profileid'); ?>" type="text" value="<?php echo $profileid; ?>" />
</label></p>
<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="hidden" value="<?php echo $access_token; ?>" />

<p><?php _e('You have to grant permissions for the widget to get your Status updates'); ?></p>
<fb:login-button v="2" scope="read_stream" onlogin="sfcUserStatusSetup('<?php echo 'widget-'.$this->id_base.'-'.$this->number; ?>')">Grant Permissions</fb:login-button>
<script type="text/javascript">
	// Refresh the DOM
	FB.XFBML.parse();
</script>
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_User_Status_Widget");'));

add_action('admin_footer-widgets.php','sfc_userstatus_widget_script');

function sfc_userstatus_widget_script() {
?>
<script type="text/javascript">
function sfcUserStatusSetup(base) {
	FB.getLoginStatus(function(response) {
		if (response.authResponse) {
			jQuery('#'+base+'-profileid').val(response.authResponse.userID);
			jQuery('#'+base+'-access_token').val(response.authResponse.accessToken);
		} else {
			jQuery('#'+base+'-profileid').val('');
			jQuery('#'+base+'-access_token').val('');
		}
	});
}
</script>
<?php
}
/**
* Chicklet
*/

define('SFC_FANCOUNT_CACHE',60*60); // 1 hour caching

// checks for sfc on activation
function sfc_chicklet($id = 0) {
	$options = get_option('sfc_options');

	if ($id == 0) {
		if ($options['fanpage']) $id = $options['fanpage'];
		else $id = $options['appid'];
	}

	$sfc_chicklet_fancount = get_transient('sfc_chicklet_fancount');

	if (!isset($sfc_chicklet_fancount[$id])) {
		$data = wp_remote_get("http://api.facebook.com/method/fql.query?format=json&query=select+page_url,fan_count+from+page+where+page_id={$id}");
		if (!is_wp_error($data)) {
			$resp = json_decode($data['body'],true);
			if (!empty($resp)) {
				$sfc_chicklet_fancount[$id] = array_shift($resp);
				set_transient('sfc_chicklet_fancount',$sfc_chicklet_fancount,SFC_FANCOUNT_CACHE);
			}
		}
	}

	if (!empty($sfc_chicklet_fancount[$id])) {
		$fancount = $sfc_chicklet_fancount[$id]['fan_count'];
		$pageurl = $sfc_chicklet_fancount[$id]['page_url'];
	}
	
	global $sfc_chicklet_no_style;
	if (!$sfc_chicklet_no_style) {
?>
<style>
.fanBoxChicklet {
width:88px;
height:17px;
overflow:auto;
background-color:#94bfbf;
border-top: 1px solid #cefdfd;
border-left: 1px solid #cefdfd;
border-right: 1px solid #5f8586;
border-bottom: 1px solid #5f8586;
font: 11px/normal monospace, courier new, sans-serif;
color:#59564f;
margin: 0;
padding: 0;
text-align:right;
}

.fanBoxChicklet .quantity {
width:auto;
height:13px;
min-width:40px;
background-color:#cefdfd;
border-top: 1px solid #8a8a8a;
border-left: 1px solid #8a8a8a;
border-right: 1px solid #fefffe;
border-bottom: 1px solid #fefffe;
padding: 2px;
float: left;
text-align: center;
overflow: hidden;
margin:1px 5px 0 0;
padding:0;
}

.fanBoxChicklet .readerCaption {
width:auto;
float: left;
text-align: center;
vertical-align: middle;
margin: 2px 0 0 0;
padding: 0;
}

.fanBoxChicklet .feedCountLink {
color:#59564f;
text-decoration:none;
margin:0;
padding:0;
}

.fanBoxBy {
width:88px;
height:9px;
font: 9px/normal monospace, courier new, sans-serif;
color:#59564f;
}
</style>
<?php } ?>
<div class="fanBoxChicklet fanBoxChicklet-<?php echo $id; ?>">
<p class="quantity"><?php echo $fancount; ?></p>
<p class="readerCaption"><a href="<?php echo $pageurl; ?>" class="feedCountLink" target="_blank"><?php _e('Fans', 'sfc'); ?></a></p>
</div>
<div class="fanBoxBy"><?php _e('ON FACEBOOK', 'sfc'); ?></div>
<?php
}


class SFC_Chicklet_Widget extends WP_Widget {
	function SFC_Chicklet_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-chicklet', 'description' => __('Facebook Chicklet', 'sfc'));
		$this->WP_Widget('sfc-chicklet', __('Facebook Chicklet (SFC)', 'sfc'), $widget_ops);
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php sfc_chicklet(); ?>
		<?php echo $after_widget; ?>
		<?php
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_Chicklet_Widget");'));


/**
* Live Stream Widget
*/

function get_sfc_livestream($args='') {
	$options = get_option('sfc_options');
	$args = wp_parse_args($args, array(
		'width' => '200',
		'height' => '400',
		'xid' => '',
		'always_post_to_friends'=>'false',
		'event_app_id' => $options['appid'],
		));
	extract($args);

	return "<fb:live-stream event_app_id='{$event_app_id}' width='{$width}' height='{$height}' xid='{$xid}' always_post_to_friends='{$always_post_to_friends}'></fb:live-stream>";
}

function sfc_livestream($args='') {
	echo get_sfc_livestream($args);
}

function sfc_live_stream_shortcode($atts) {
	$options = get_option('sfc_options');
	$args = shortcode_atts(array(
		'width' => '200',
		'height' => '400',
		'xid' => '',
		'always_post_to_friends'=>'false',
		'event_app_id' => $options['appid'],
	), $atts);

	return get_sfc_livestream($args);
}
add_shortcode('fb-livestream', 'sfc_live_stream_shortcode');

class SFC_Live_Stream_Widget extends WP_Widget {
	function SFC_Live_Stream_Widget() {
		$widget_ops = array('classname' => 'widget_sfc-livestream', 'description' => __('Facebook Live Stream', 'sfc'));
		$this->WP_Widget('sfc-livestream', __('Facebook Live Stream (SFC)', 'sfc'), $widget_ops);
	}

	function widget($args, $instance) {
		$options = get_option('sfc_options');
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$width = intval($instance['width']);
		$height = intval($instance['height']);
		?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php sfc_livestream($instance); ?>
		<?php echo $after_widget; ?>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'width'=>200, 'height'=>400 ) );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = intval($new_instance['width']);
		if ($instance['width'] < 200) $instance['width'] = 200;
		$instance['height'] = intval($new_instance['height']);
		if ($instance['height'] < 400) $instance['height'] = 400;
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'width'=>200, 'height'=>400 ) );
		$title = strip_tags($instance['title']);
		$width = intval($instance['width']);
		$height = intval($instance['height']);
		$stream = $instance['stream'] ? true : false;
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> 
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width of the widget in pixels (minimum 200):', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height of the widget in pixels (minimum 400):', 'sfc'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
</label></p>

		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("SFC_Live_Stream_Widget");'));
