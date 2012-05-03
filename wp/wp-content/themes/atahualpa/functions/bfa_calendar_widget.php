<?php
function widget_calendar($args) {
	extract($args);
	$options = get_option('widget_calendar');
	$title = apply_filters('widget_title', $options['title']);
	echo $before_widget;
	if ( !empty($title) ) { echo $before_title . $title . $after_title; }
	echo '<div id="calendar_wrap">'; get_calendar(); echo '</div>'; 
	if ( !empty( $title ) ) { echo $after_widget; } else { echo "</div>"; }
}
	// unregister old / register new calendar widget
	$widget_ops = array('classname' => 'widget_calendar', 'description' => __("A calendar of your blog's posts","atahualpa") );
	wp_unregister_sidebar_widget('calendar', __('Calendar','atahualpa'), 'wp_widget_calendar', $widget_ops);
	wp_register_sidebar_widget('calendar', __('Calendar','atahualpa'), 'widget_calendar', $widget_ops);
?>