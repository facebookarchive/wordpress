<?php
add_action( 'init', 'fb_comment_rule' );
add_action( 'query_vars', 'fb_filter_comment_query_vars' );
add_action( 'template_redirect', 'fb_handle_save_comment' );

function fb_insights_page() {
	$options = get_option('fb_options');

	if (!empty($options["app_id"])) {
		echo '<script>window.location = ' . json_encode( 'https://www.facebook.com/insights/?' . http_build_query( array( 'sk' => 'ao_' . $options['app_id'] ) ) ) . ';</script>';
	}
}


function fb_hide_wp_comments() {
	wp_enqueue_style( 'fb_hide_wp_comments', plugins_url( 'style/hide-wp-comments.css', dirname(__FILE__)), array(), '1.0' );
}

function fb_hide_wp_comments_homepage() {
	return '';
}

/*Handling of saving comments coming from fb social plugin, into the WP DB*/
function fb_comment_rule() {
	add_rewrite_rule( '^fb-save-comment/?', 'index.php?fb-save-comment=true', 'top' );
}


function fb_filter_comment_query_vars( $query_vars ) {
	$query_vars[] = 'fb-save-comment';
	return $query_vars;
}

//Call back function that will get the data from ajax 
function fb_handle_save_comment() {
	global $facebook;
	//check if this query var is true, and if so access fb comment id
	if ( get_query_var( 'fb-save-comment' ) ) {
		$fb_comment = $_REQUEST['fb_comment'];
		require_once('facebook-php-sdk/facebook.php');
		
		$facebook = new Facebook_WP_Extend(array(
		  'appId'  => '311654765594138',
		  'secret' => '776fca0b17fef9df33e026268a847e81',
		));
		
		error_log($facebook->getAccessToken() . "access token");
		
		//logic to store this comment into the 
		$user = $facebook->getUser();
		$fields = $facebook->api('/me/?fields=name,email,picture');
		add_comment_meta($comment_id, 'fb_uid', $user);
		add_comment_meta($comment_id, 'name', $fields['name']);		
		add_comment_meta($comment_id, 'email', $fields['email']);
		add_comment_meta($comment_id, 'avatar', $fields['picture']);

		$comment_post_ID = get_the_ID();
		$comment_author = $fields['name'];
		$comment_author_email = $fields['email'];
		$comment_author_url = "http://facebook.com/" . $user;
		$comment_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)';
		error_log($fb_comment);
		$comment_content = $fb_comment;
		$comment_author_IP = "127.0.0.1";
		error_log($comment_post_ID);
		$commentdata = array(
			'comment_post_ID' => 273,
			'comment_author' => $comment_author,
			'comment_author_email' => $comment_author_email,
			'comment_author_url' => $comment_author_url,
			'comment_content' => $comment_content,
			'comment_type' => '',
			'comment_parent' => 0,
			'user_id' => 1,
			'comment_author_IP' => '127.0.0.1',
			'comment_agent' => $comment_agent,
			'comment_date' => $time,
			'comment_approved' => 1,
		);

		//use wp new comment, not insert because new comment does sanity checks!
		$new_comments_id = wp_new_comment( $commentdata );
		die();
	}
}
function fb_save_comment_link() {
	echo "JUNKJUNKJUNK";
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
				}
			}
			elseif ( 'show' == $show_indiv || ( ( ! isset( $options['comments']['show_on'] ) ) && ( 'default' == $show_indiv || empty( $show_indiv ) ) ) ) {
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
	$array['parent'] = array('name' => 'comments',
									'type' => 'checkbox',
									'label' => 'Comments',
									'description' => 'The Comments Box is a social plugin that enables user commenting on your site. Features include moderation tools and distribution.',
									'help_link' => 'https://developers.facebook.com/docs/reference/plugins/comments/',
									'image' => plugins_url( '/images/settings_comments.png', dirname(__FILE__))
									);
    $post_types = get_post_types(array('public' => true));
	$array['children'] = array(array('name' => 'num_posts',
													'label' => 'Number of posts',
													'type' => 'text',
													'default' => 20,
													'help_text' => 'The number of posts to display by default.',
													),
										array('name' => 'width',
													'type' => 'text',
													'default' => '470',
													'help_text' => 'The width of the plugin, in pixels.',
													),
										array('name' => 'colorscheme',
													'label' => 'Color scheme',
													'type' => 'dropdown',
													'default' => 'light',
													'options' => array('light' => 'light', 'dark' => 'dark'),
													'help_text' => 'The color scheme of the plugin.',
													),
										array('name' => 'show_on',
													'type' => 'checkbox',
													'default' => array_fill_keys(array_keys($post_types) , 'true'),
													'options' => $post_types,
													'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' ),
                         ),
                    array('name' => 'homepage_comments',
                          'label' => 'Show comment counts on the homepage',
                          'type' => 'checkbox',
                          'default' => 'true',
                          'help_text' => __('Whether the plugin will display a comment count for each post on the homepage.'),
                         )
										);

	return $array;
}

?>
