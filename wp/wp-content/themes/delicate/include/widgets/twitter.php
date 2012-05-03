<?php 
/**
* @version		v.1.0
* @copyright	Copyright (C) 2008 NattyWP. All rights reserved.
* @author		Dave Miller
*/
// Twitter Widget

function twitterWidget($args) {
	extract( $args, EXTR_SKIP );
	$settings = get_option("widget_twitterwidget");
	$account = $settings['account'];  
	$title = $settings['title']; 
	$show = $settings['show']; 
	
	echo $before_widget; ?>
	
		<div id="twitter">
		<?php echo $before_title . $title . $after_title; ?>
		<ul id="twitter_update_list"><li></li></ul>
		<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
		<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/<?php echo $account; ?>.json?callback=twitterCallback2&amp;count=<?php echo $show; ?>"></script>
        <div class="dasheddivider"></div>
        <p align="right"><a href="http://www.twitter.com/<?php echo $account; ?>/" class="rightlink png_crop"><?php _e('Follow us on Twitter','nattywp'); ?></a></p>
		</div>


	<?php
	echo $after_widget;

}

function twitterWidgetAdmin() {
		$settings = get_option('widget_twitterwidget');	
		if ( !is_array($settings) )
			$settings = array('account'=>'nattywp', 'title'=>'Twitter Updates', 'show'=>'3');

        // form posted?
		if (isset($_POST['Twitter-submit'])) {

			// Remember to sanitize and format use input appropriately.
			$settings['account'] = strip_tags(stripslashes($_POST['Twitter-account']));
			$settings['title'] = strip_tags(stripslashes($_POST['Twitter-title']));
			$settings['show'] = strip_tags(stripslashes($_POST['Twitter-show']));
			update_option('widget_twitterwidget', $settings);
		}

		// Get options for form fields to show
		$account = htmlspecialchars($settings['account'], ENT_QUOTES);
		$title = htmlspecialchars($settings['title'], ENT_QUOTES);
		$show = htmlspecialchars($settings['show'], ENT_QUOTES);

		// The form fields
		echo '<p>
				<label for="Twitter-account">' . __('Account:', 'nattywp') . '
				<input style="width: 200px;" id="Twitter-account" name="Twitter-account" type="text" value="'.$account.'" />
				</label></p>';
		echo '<p>
				<label for="Twitter-title">' . __('Title:', 'nattywp') . '
				<input style="width: 200px;" id="Twitter-title" name="Twitter-title" type="text" value="'.$title.'" />
				</label></p>';
		echo '<p>
				<label for="Twitter-show">' . __('Show:', 'nattywp') . '
				<input style="width: 200px;" id="Twitter-show" name="Twitter-show" type="text" value="'.$show.'" />
				</label></p>';
		echo '<input type="hidden" id="Twitter-submit" name="Twitter-submit" value="1" />';

}

function twitterWidget_register(){
	wp_register_sidebar_widget('twitter-1', 'NattyWP Twitter', 'twitterWidget');
	wp_register_widget_control('twitter-1', 'NattyWP Twitter', 'twitterWidgetAdmin', 300, 200);
}

add_action('widgets_init', 'twitterWidget_register');