<?php
/*
wp_insert_comment

<noscript></noscript>
*/

function fb_hide_wp_comments() {
	print "<script>document.getElementById('comments').style.display = 'none';</script>";
}

function fb_set_wp_comment_status ( $posts ) {
	if ( ! empty( $posts ) && is_singular() ) {
		$posts[0]->comment_status = 'open';
		$posts[0]->post_status = 'open';
	}
	return $posts;
}

function fb_close_wp_comments($comments) {
	return null;
}

function fb_get_comments($options = array()) {
	if (isset($options['data-href']) == '') {
		$options['data-href'] = get_permalink();
	}
	
	$params = '';
	
	foreach ($options as $option => $value) {
		$params .= $option . '="' . $value . '" ';
	}
	
	return '<div class="fb-comments" ' . $params . '></div>';
}

function fb_comments_automatic($content) {
	if (!is_home()) {
		$options = get_option('fb_options');
		
		foreach($options['comments'] as $param => $val) {
			$param = str_replace('_', '-', $param);
				
			$options['comments']['data-' . $param] =  $val;
		}
		
		$content .= fb_get_comments($options['comments']);
	}
	
	return $content;
}
?>