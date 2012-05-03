<?php 
/**
* @version		v.1.0
* @copyright	Copyright (C) 2008 NattyWP. All rights reserved.
* @author		Dave Miller
*/
// Flickr Widget

function flickrWidget($args)
{
	extract( $args, EXTR_SKIP );
	$settings = get_option("widget_flickrwidget");
	$title = $settings['title'];
	$id = $settings['id'];
	$number = $settings['number'];	

	echo $before_widget;
?>
	
				<?php echo $before_title . $title . $after_title; ?>
				
				<div class="flickr-pic">	
					<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $number; ?>&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo $id; ?>"></script>        
				</div>
			<div style="clear:both;"></div>
<?php
	echo $after_widget;
}

function flickrWidgetAdmin() {

	$settings = get_option("widget_flickrwidget");

	// check if anything's been sent
	if (isset($_POST['update_flickr'])) {
		$settings['title'] = strip_tags(stripslashes($_POST['flickr_title']));
		$settings['id'] = strip_tags(stripslashes($_POST['flickr_id']));
		$settings['number'] = strip_tags(stripslashes($_POST['flickr_number']));

		update_option("widget_flickrwidget", $settings);
	}

	echo '<p>
			<label for="flickr_title">Title:
			<input id="flickr_title" name="flickr_title" type="text" class="widefat" value="'.$settings['title'].'" /></label></p>';
			
	echo '<p>
			<label for="flickr_id">Flickr ID (<a href="http://www.idgettr.com">idGettr</a>):
			<input id="flickr_id" name="flickr_id" type="text" class="widefat" value="'.$settings['id'].'" /></label></p>';
	echo '<p>
			<label for="flickr_number">Number of photos:
			<input id="flickr_number" name="flickr_number" type="text" class="widefat" value="'.$settings['number'].'" /></label></p>';
	echo '<input type="hidden" id="update_flickr" name="update_flickr" value="1" />';

}

function flickrWidget_register(){
	wp_register_sidebar_widget('flickr-1', 'NattyWP Flickr', 'flickrWidget');
	wp_register_widget_control('flickr-1', 'NattyWP Flickr', 'flickrWidgetAdmin', 300, 200);
}

add_action('widgets_init', 'flickrWidget_register');

?>