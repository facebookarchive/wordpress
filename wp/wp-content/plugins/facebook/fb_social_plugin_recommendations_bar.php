<?php
function fb_get_recommendations_bar($options = array()) {
	$params = '';
	
	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}
	
	return '<div class="fb-recommendations-bar" ' . $params . '></div>';
}

function fb_recommendations_bar_automatic($content) {
	if (!is_home()) {
		$options = get_option('fb_options');
		
		foreach($options['recommendations_bar'] as $param => $val) {
			$param = str_replace('_', '-', $param);
				
			$options['recommendations_bar']['data-' . $param] =  $val;
		}
		
		$content .= fb_get_recommendations_bar($options['recommendations_bar']);
	}
	
	return $content;
}
?>