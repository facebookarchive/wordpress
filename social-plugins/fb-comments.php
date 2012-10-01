<?php
function fb_insights_page() {
	$options = get_option('fb_options');

	if (!empty($options["app_id"])) {
		echo '<script>window.location = ' . json_encode( 'https://www.facebook.com/insights/?' . http_build_query( array( 'sk' => 'ao_' . $options['app_id'] ) ) ) . ';</script>';
	}
}


function fb_hide_wp_comments() {
	wp_enqueue_style( 'fb_hide_wp_comments', plugins_url( 'style/hide-wp-comments.min.css', dirname(__FILE__)), array(), '1.0' );
}

function fb_hide_wp_comments_homepage() {
	return '';
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
	if (isset($options['href']) == '') {
		$options['href'] = get_permalink();
	}

	$params = fb_build_social_plugin_params($options);

	$output = fb_get_fb_comments_seo();
	$output .= '<div class="fb-comments fb-social-plugin" ' . $params . '></div>';

	return $output;
}

function fb_get_comments_count() {
		return '<iframe src="' . ( is_ssl() ? 'https' : 'http' ) . '://www.facebook.com/plugins/comments.php?' . http_build_query( array( 'href' => get_permalink(), 'permalink' => 1 ) ) . '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:130px; height:16px;" allowTransparency="true"></iframe>';
}

function fb_comments_automatic($content) {
	global $post;

	if ( isset ( $post ) ) {
		if ( comments_open( get_the_ID() ) && post_type_supports( get_post_type(), 'comments' ) ) {
			$options = get_option('fb_options');
			$show_indiv = get_post_meta( $post->ID, 'fb_social_plugin_settings_box_comments', true );
			if ( ! is_home() && ( 'default' == $show_indiv || empty( $show_indiv ) ) && isset( $options['comments']['show_on'] ) && isset( $options['comments']['show_on'][$post->post_type] ) ) {
				foreach( $options['comments'] as $param => $val ) {
					$param = str_replace( '_', '-', $param );

					$params[$param] = $val;
				}

				$content .= fb_get_comments( $params );
			} else if ( 'show' == $show_indiv || ( ( ! isset( $options['comments']['show_on'] ) ) && ( 'default' == $show_indiv || empty( $show_indiv ) ) ) ) {
				foreach( $options['comments'] as $param => $val ) {
					$param = str_replace( '_', '-', $param );

					$params[$param] = $val;
				}

				$content .= fb_get_comments( $params );
			}
			//elseif ( 'no' == $show_indiv ) {
			//}
		}
	}

	return $content;
}

function fb_get_fb_comments_seo() {
	global $facebook;
	global $post;

	if ( isset( $post ) ) {
		$url = get_permalink();

		if ( false === ( $comments = get_transient( 'fb_comments_' . $post->ID ) ) ) {
			try {
				$comments = $facebook->api('/comments', array('ids' => $url));
			}
				catch (WP_FacebookApiException $e) {
			}

			set_transient( 'fb_comments_' . $post->ID, $comments, 60*15 );
		}

		if ( ! isset( $comments[$url] ) )
			return '';

		$output = '<noscript><ol class="commentlist">';
		foreach ($comments[$url]['comments']['data'] as $key => $comment_info) {
			$unix_timestamp = strtotime($comment_info['created_time']);
			$output .= '<li id="' . esc_attr( 'comment-' . $key ) . '">
				<p><a href="' . esc_url( 'http://www.facebook.com/' . $comment_info['from']['id'], array( 'http', 'https' ) ) . '">' . esc_html( $comment_info['from']['name'] ) . '</a>:</p>
				<p class="metadata">' . date('F jS, Y', $unix_timestamp) . ' at ' . date('g:i a', $unix_timestamp) . '</p>
				' . $comment_info['message'] . '
				</li>';
			if (isset($comment_info['comments']) && isset($comment_info['comments']['data'])) {
				foreach ($comment_info['comments']['data'] as $second_key => $comment_info) {
					$unix_timestamp = strtotime($comment_info['created_time']);
					$output .= '<li id="' . esc_attr( 'comment-' . $key . '-' . $second_key ) . '">
						<p><a href="' . esc_url( 'http://www.facebook.com/' . $comment_info['from']['id'], array( 'http', 'https' ) ) . '">' . esc_html( $comment_info['from']['name'] ) . '</a>:</p>
						<p class="metadata">' . date('F jS, Y', $unix_timestamp) . ' at ' . date('g:i a', $unix_timestamp) . '</p>
						' . $comment_info['message'] . '
						</li>';
				}
			}
		}
		$output .= '</ol></noscript>';
	}

	return $output;
}


function fb_get_comments_fields($placement = 'settings', $object = null) {
	$fields_array = fb_get_comments_fields_array();

	fb_construct_fields($placement, $fields_array['children'], $fields_array['parent'], $object);
}

function fb_get_comments_fields_array() {
	$array['parent'] = array(
		'name' => 'comments',
		'type' => 'checkbox',
		'label' => __( 'Comments', 'facebook' ),
		'description' => __( 'The Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution.', 'facebook' ),
		'help_link' => 'https://developers.facebook.com/docs/reference/plugins/comments/',
		'image' => plugins_url( '/images/settings_comments.png', dirname(__FILE__) )
	);
	$post_types = get_post_types(array('public' => true));
	$array['children'] = array(
		array(
			'name' => 'num_posts',
			'label' => __( 'Number of posts', 'facebook' ),
			'type' => 'text',
			'default' => 20,
			'help_text' => __( 'The number of posts to display by default.', 'facebook' )
		),
		array(
			'name' => 'width',
			'type' => 'text',
			'default' => '470',
			'help_text' => __( 'The width of the plugin, in pixels.', 'facebook' )
		),
		array(
			'name' => 'colorscheme',
			'label' => __( 'Color scheme', 'facebook' ),
			'type' => 'dropdown',
			'default' => 'light',
			'options' => array(
				'light' => 'light',
				'dark' => 'dark'
			),
			'help_text' => __( 'The color scheme of the plugin.', 'facebook' )
		),
		array(
			'name' => 'show_on',
			'type' => 'checkbox',
			'default' => array_fill_keys( array_keys($post_types) , 'true' ),
			'options' => $post_types,
			'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' ),
		),
		array(
			'name' => 'homepage_comments',
			'label' => __( 'Show comment counts on the homepage', 'facebook' ),
			'type' => 'checkbox',
			'default' => 'true',
			'help_text' => __( 'Whether the plugin will display a comment count for each post on the homepage.', 'facebook' ),
		)
	);

	return $array;
}

?>
