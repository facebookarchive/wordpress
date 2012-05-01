<?php
/**
* @version		v.1.0
* @copyright	Copyright (C) 2008 NattyWP. All rights reserved.
* @author		Dave Miller
*/
// Feedburner Subscription Widget

function feedburnerWidget($args)
{
	extract( $args, EXTR_SKIP );
	$settings = get_option("widget_feedburnerwidget");

	$id = $settings['id'];
	$title = $settings['title'];

?>

			<?php echo $before_widget; 
			echo $before_title . $title . $after_title;
			?>
				
				<form name="feedburnerform" class="search" id="searchforma" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $id; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
					<div class="field">
					<input type="text" name="email" value="<?php _e('Enter your e-mail address','nattywp'); ?>" onfocus="if (this.value == '<?php _e('Enter your e-mail address','nattywp'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Enter your e-mail address','nattywp'); ?>';}" />
                     </div>
					<input type="hidden" value="<?php echo $id; ?>" name="uri"/>
					<input type="hidden" name="loc" value="en_US"/>
					<input type="submit" value="<?php _e('Subscribe','nattywp'); ?>" />
					
				</form>
               
				
			<div style="clear:both;"></div>
			<?php echo $after_widget; ?>
<?php
}

function feedburnerWidgetAdmin() {

	$settings = get_option("widget_feedburnerwidget");

	// check if anything's been sent
	if (isset($_POST['update_feedburner'])) {
		$settings['id'] = strip_tags(stripslashes($_POST['feedburner_id']));
		$settings['title'] = strip_tags(stripslashes($_POST['feedburner_title']));

		update_option("widget_feedburnerwidget", $settings);
	}

	echo '<p>
			<label for="feedburner_title">Title:
			<input id="feedburner_title" name="feedburner_title" type="text" class="widefat" value="'.$settings['title'].'" /></label></p>';
	echo '<p>
			<label for="feedburner_id">Your Feedburner ID:
			<input id="feedburner_id" name="feedburner_id" type="text" class="widefat" value="'.$settings['id'].'" /></label></p>';			
	echo '<input type="hidden" id="update_feedburner" name="update_feedburner" value="1" />';

}

function feedburnerWidget_register(){
	wp_register_sidebar_widget('feedburner-1','NattyWP Feedburner Subscription', 'feedburnerWidget');
	wp_register_widget_control('feedburner-1','NattyWP Feedburner Subscription', 'feedburnerWidgetAdmin', 100, 200);
}

add_action('widgets_init', 'feedburnerWidget_register');

?>