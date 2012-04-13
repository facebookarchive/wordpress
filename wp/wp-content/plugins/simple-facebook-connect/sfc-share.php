<?php	
/**
 * Simple share button
 *
 * See http://wiki.developers.facebook.com/index.php/Fb:share-button for more info
 *
 * @param string $type box_count, button_count, button, icon, or icon_link
 * @param int $post_id An optional post ID.
 */
function get_sfc_share_button($type = '', $id = 0) {
	if (empty($type)) {
		$options = get_option('sfc_options');
		$type = $options['share_type'];
	}
		
	//return "<a name='fb_share' type='{$type}' share_url='".get_permalink($id)."'></a>";
	return '<span class="fb_share"><fb:like href="'.get_permalink($id).'" layout="'.$type.'"></fb:like></span>';
}

function sfc_share_button($type = '', $id = 0) {
	echo get_sfc_share_button($type,$id);
}

/**
 * Simple share button as a shortcode
 *
 * See http://wiki.developers.facebook.com/index.php/Fb:share-button for more info
 *
 * Example use: [fb-share type="button"] or [fb-share id="123"]
 */
function sfc_share_shortcode($atts) {
	$options = get_option('sfc_options');
	extract(shortcode_atts(array(
		'type' => $options['share_type'],
		'id' => 0,
	), $atts));

	return get_sfc_share_button($type,$id);
}

add_shortcode('fb-share', 'sfc_share_shortcode');
add_shortcode('fbshare', 'sfc_share_shortcode'); // FB Foundations Share uses this shortcode. This is compatible with it.

function sfc_share_button_automatic($content) {
	global $post;
	$post_types = apply_filters('sfc_share_post_types', get_post_types( array('public' => true) ) );
	if ( !in_array($post->post_type, $post_types) ) return $content;
	
	// exclude bbPress post types
	if ( function_exists('bbp_is_custom_post_type') && bbp_is_custom_post_type() ) return $content;

	$options = get_option('sfc_options');
	$button = get_sfc_share_button();
	switch ($options['share_position']) {
		case "before":
			$content = $button . $content;
			break;
		case "after":
			$content = $content . $button;
			break;
		case "both":
			$content = $button . $content . $button;
			break;
		case "manual":
		default:
			break;
	}
	return $content;
}
add_filter('the_content', 'sfc_share_button_automatic', 30);

// add the admin sections to the sfc page
add_action('admin_init', 'sfc_share_admin_init');
function sfc_share_admin_init() {
	add_settings_section('sfc_share', __('Share Button Settings', 'sfc'), 'sfc_share_section_callback', 'sfc');
	add_settings_field('sfc_share_position', __('Share Button Position', 'sfc'), 'sfc_share_position', 'sfc', 'sfc_share');
	add_settings_field('sfc_share_type', __('Share Button Type', 'sfc'), 'sfc_share_type', 'sfc', 'sfc_share');
}

function sfc_share_section_callback() {
	echo '<p>'.__('Facebook no longer supports the Share button. Therefore it has been replaced with the similar versions of the Like button.', 'sfc').'</p>';
}

function sfc_share_position() {
	$options = get_option('sfc_options');
	if (!isset($options['share_position'])) $options['share_position'] = 'manual';
	?>
	<p><label><input type="radio" name="sfc_options[share_position]" value="before" <?php checked('before', $options['share_position']); ?> /> <?php _e('Before the content of your post', 'sfc'); ?></label></p>
	<p><label><input type="radio" name="sfc_options[share_position]" value="after" <?php checked('after', $options['share_position']); ?> /> <?php _e('After the content of your post', 'sfc'); ?></label></p>
	<p><label><input type="radio" name="sfc_options[share_position]" value="both" <?php checked('both', $options['share_position']); ?> /> <?php _e('Before AND After the content of your post', 'sfc'); ?></label></p>
	<p><label><input type="radio" name="sfc_options[share_position]" value="manual" <?php checked('manual', $options['share_position']); ?> /> <?php _e('Manually add the button to your theme or posts (use the sfc_share_button function in your theme, or the [fb-share] shortcode in your posts)', 'sfc'); ?></label></p>
<?php 
}

function sfc_share_type() {
	$options = get_option('sfc_options');
	if (!isset($options['share_type'])) $options['share_type'] = 'box_count';
	?>
	<table><tr><td style="width:140px;">
	<div class="sfc_share_type_selector">
	<select name="sfc_options[share_type]" id="sfc_select_share_type">
	<option value="button_count" <?php selected('button_count', $options['share_type']); ?>><?php _e('Button Count', 'sfc'); ?></option>
	<option value="box_count" <?php selected('box_count', $options['share_type']); ?>><?php _e('Box Count', 'sfc'); ?></option>
	</select>
	</td><td>
	<div id="sfc_share_type_preview" style="float:right;"><?php _e('Preview:', 'sfc'); ?>
	<img id="sfc_share_type_preview_image" src="<?php echo plugins_url('/images/'.$options['share_type'].'.png', __FILE__); ?>" />
	</div>
	</td></tr></table>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#sfc_select_share_type").change(function() {
			var selected = jQuery("#sfc_select_share_type").val();
			jQuery("#sfc_share_type_preview_image").attr('src',"<?php echo plugins_url('/images/', __FILE__); ?>"+selected+".png");
		});
	});
	</script>
<?php 
}

add_filter('sfc_validate_options','sfc_share_validate_options');
function sfc_share_validate_options($input) {
	if (!in_array($input['share_position'], array('before', 'after', 'both', 'manual'))) {
			$input['share_position'] = 'manual';
	}
	if (!in_array($input['share_type'], array('button_count', 'box_count'))) {
			$input['share_type'] = 'box_count';
	}
	return $input;
}
