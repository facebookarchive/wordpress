<?php
function fb_insights_page() {
	$options = get_option('fb_options');

	if (!empty($options["app_id"])) {
		echo '<script>window.location = ' . json_encode( 'https://www.facebook.com/insights/?' . http_build_query( array( 'sk' => 'ao_' . $options['app_id'] ) ) ) . ';</script>';
	}
}


function fb_hide_wp_comments() {
	wp_enqueue_style( 'fb_hide_wp_comments', plugins_url( 'style/hide-wp-comments.css', dirname(__FILE__)), array(), '1.0' );
}

function fb_set_wp_comment_status ( $posts ) {
	if ( ! empty( $posts ) && is_singular() ) {
		$posts[0]->comment_status = 'open';
		$posts[0]->post_status = 'open';
	}
	return $posts;
}

function fb_close_wp_comments($comments) {
	global $post;
	//echo $post->post_date;
	$options = get_option('fb_options');
	$options['fb_plugin_activation_time'] = 1;
	update_option( 'fb_options', $options );
	$options = get_option('fb_options');
	return null;
}

function fb_publish_to_feed()
{
	$post = array(
		"ID" => 238,
		"post_author" => 1,
		"post_date" => "2012-07-31 00:54:27",
		"post_date_gmt" => "0000-00-00 00:00:00"
	);
}


function fb_wp_comment_form_unfiltered_html_nonce() {
	?>
	<script src="//connect.facebook.net/en_US/all.js"></script>

	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=311654765594138";
		fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		<script type="text/javascript">
		
		$(window).load(function() {
			// Handler for .ready() called.
			FB.getLoginStatus(function(response) {
				if (response.status === 'connected' || response.status === 'not_authorized') {
					// the user is logged in and has authenticated your
					// app, and response.authResponse supplies
					// the user's ID, a valid access token, a signed
					// request, and the time the access token 
					// and signed request each expire
					var uid = response.authResponse.userID;
					var accessToken = response.authResponse.accessToken;
					var oFormObject = document.forms['commentform'];
					
					FB.api('/me', function(response) {
						$('input[name=author]').val("<a href>" + response.name + "</a>");
						$('input[name=email]').val(response.email);
					});

					//hide these because they will be populated via fb sdk provided information
					$(".comment-form-author").hide();
					$(".comment-form-email").hide();
					$(".comment-form-url").hide();
				} /*else if (response.status === 'not_authorized') {
					// the user is logged in to Facebook, 
					// but has not authenticated your app
					alert("not authorized");
					var oFormObject = document.forms['commentform'];
					alert("You're logged in, but need to authorize your app.");
				} */else {
					// the user isn't logged in to Facebook.
				}
			});
		});			


		function afterLogin() {
			window.location.reload();
		}
		
		$('.comment-form-comment').prepend('<div class="fb-login-button" data-scope="email" data-show-faces="true" data-width="800" data-max-rows="1" style="top:0px;margin-top:10px;" onlogin:"afterLogin();"></div>');
		
		</script>	

		<?php
		global $post;

		$post_id = 0;
		if ( !empty($post) )
			$post_id = $post->ID;

		if ( current_user_can( 'unfiltered_html' ) ) {
			wp_nonce_field( 'unfiltered-html-comment_' . $post_id, '_wp_unfiltered_html_comment_disabled', false );
			echo "<script>(function(){if(window===window.parent){document.getElementById('_wp_unfiltered_html_comment_disabled').name='_wp_unfiltered_html_comment';}})();</script>\n";
		}

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
					if ( ! is_home() && ( 'default' == $show_indiv || empty( $show_indiv ) ) && isset( $options['comments']['show_on'] ) ) {
						if ( ( is_page() && ( $options['comments']['show_on'] == 'all pages' || $options['comments']['show_on'] == 'all posts and pages' ) )
							or ( is_single() &&  ( $options['comments']['show_on'] == 'all posts' || $options['comments']['show_on'] == 'all posts and pages' ) ) )
						{
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
'type' => 'dropdown',
'default' => 'all posts and pages',
'options' => array('all posts' => 'all posts', 'all pages' => 'all pages', 'all posts and pages' => 'all posts and pages', 'individual posts and pages' => 'individual posts and pages' ),
'help_text' => __( 'Whether the plugin will appear on all posts or pages by default. If "individual posts and pages" is selected, you must explicitly set each post and page to display the plugin.', 'facebook' ),
),
array('name' => 'retroactive_override',
'label' => 'Show retroactively',
'type' => 'checkbox',
'help_text' => 'The color scheme of the plugin.',
)
);

return $array;
}

?>
