<?php
function fb_get_recommendations_bar($options = array()) {
	$params = '';
	
	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}
	
	return '<div class="fb-like" ' . $params . '></div>';
	return '<div class="fb-recommendations-bar" ' . $params . '></div>';
}

function fb_recommendations_bar_automatic($content) {
	if (!is_home()) {
		$content .= fb_get_recommendations_bar();
	}
	
	return $content;
}
?>