<?php
/*
wp_insert_comment

<noscript></noscript>
*/

function fb_insights_page() {
	$options = get_option('fb_options');

	if (!empty($options["app_id"])) {
		echo '<script>window.location = "https://www.facebook.com/insights/?sk=ao_' . $options["app_id"] . '";</script>';
	}
}


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

	$params .= 'data-ref="wp" ';

	$output = fb_get_fb_comments_seo();
	$output .= '<div class="fb-comments fb-social-plugin" ' . $params . '></div>';

	return $output;
}

function fb_get_comments_count() {
		return '<iframe src="http://www.facebook.com/plugins/comments.php?href=' . get_permalink() . '&permalink=1" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:130px; height:16px;" allowTransparency="true"></iframe>';
}

function fb_comments_automatic($content) {
	if (!is_home()) {
		$options = get_option('fb_options');

		foreach($options['comments'] as $param => $val) {
			$param = str_replace('_', '-', $param);

			$options['comments']['data-' . $param] = $val;
		}

		$content .= fb_get_comments($options['comments']);
	}
	else {
		$content .= fb_get_comments_count();
	}

	return $content;
}

function fb_get_fb_comments_seo() {
	global $facebook;

	$url = get_permalink();

	try {
		$comments = $facebook->api('/comments', array('ids' => $url));
	}
	catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}

	$output = '<noscript><ol class="commentlist">';

	if (isset($comments[$url])) {
		foreach ($comments[$url]['comments']['data'] as $key => $comment_info) {
			$unix_timestamp = strtotime($comment_info['created_time']);
			$output .= '<li>
				<a name="#comment-' . $key  . '"></a>
				<p><a href="https://www.facebook.com/' . $comment_info['from']['id'] . '">' . $comment_info['from']['name'] . '</a>:</p>
				<p class="metadata">' . date('F jS, Y', $unix_timestamp) . ' at ' . date('g:i a', $unix_timestamp) . '</p>
				' . $comment_info['message'] . '
				</li>';
		}
	}

	$output .= '<ol class="commentlist"></noscript>';

	return $output;
}


function fb_get_comments_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_comments_fields_array();

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_comments_fields_array() {
	$array['parent'] = array('name' => 'comments',
									'field_type' => 'checkbox',
									'help_text' => 'Click to learn more.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/comments/',
									);

	$array['children'] = array(array('name' => 'num_posts',
													'field_type' => 'text',
													'help_text' => 'The number of posts to display by default.',
													),
										array('name' => 'width',
													'field_type' => 'text',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'colorscheme',
													'field_type' => 'dropdown',
													'options' => array('light', 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										);

	return $array;
}

?>