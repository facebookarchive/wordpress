<?php

require_once( dirname(__FILE__) . '/fb-activity-feed.php');
require_once( dirname(__FILE__) . '/fb-recommendations.php');
require_once( dirname(__FILE__) . '/fb-like.php' );
require_once( dirname(__FILE__) . '/fb-send.php' );
require_once( dirname(__FILE__) . '/fb-subscribe.php' );
require_once( dirname(__FILE__) . '/fb-comments.php' );
require_once( dirname(__FILE__) . '/fb-recommendations-bar.php' );

add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Subscribe_Button" );'), 100 );
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Send_Button" );'), 100 );
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Like_Button" );') );
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Recommendations" );'));
add_action( 'widgets_init', create_function('', 'register_widget( "Facebook_Activity_Feed" );'));

add_filter('get_comment_author', 'fb_get_comment_author_link');
add_filter('get_avatar', 'fb_get_avatar', 10, 5);

/**
 * Add social plugins through filters
 * Individual social plugin files contain both administrative setting fields and display code
 */
function fb_apply_filters() {
	$options = get_option('fb_options');

	if ( ! is_array( $options ) )
		return;


	if ( array_key_exists( 'recommendations_bar', $options ) && array_key_exists( 'enabled', $options['recommendations_bar'] ) && $options['recommendations_bar']['enabled'] ) {
		add_filter('the_content', 'fb_recommendations_bar_automatic', 30);
	}

	if ( array_key_exists( 'like', $options ) && array_key_exists( 'enabled', $options['like'] ) && $options['like']['enabled'] ) {
		add_filter( 'the_content', 'fb_like_button_automatic', 30 );
	}

	if ( array_key_exists( 'send', $options ) && array_key_exists( 'enabled', $options['send'] ) && $options['send']['enabled'] ) {
		add_filter( 'the_content', 'fb_send_button_automatic', 30 );
	}

	if ( array_key_exists( 'subscribe', $options ) && array_key_exists( 'enabled', $options['subscribe'] ) && $options['subscribe']['enabled'] ) {
		add_filter( 'the_content', 'fb_subscribe_button_automatic', 30 );
	}

	if ( array_key_exists( 'comments', $options ) && array_key_exists( 'enabled', $options['comments'] ) && $options['comments']['enabled'] ) {
		
		$options = get_option('fb_options');
				update_option( 'fb_options', $options );
				$options = get_option('fb_options');
				global $post;
		
		if( $options['comments']['comment_type'] == "WordPress Comments with Login with Facebook" ) {
					add_filter( 'the_posts', 'fb_set_wp_comment_status' );
					add_action( 'comment_form', 'fb_wp_comment_form_unfiltered_html_nonce');
					//add filter that will return the author 
					add_filter('comment_author_link', 'fb_get_comment_author_link');

					//add action that will add facebook specific meta data to the comment 
					add_action('comment_post', 'fb_add_meta_to_comment');
				}
				else if( $options['comments']['comment_type'] == "Facebook Comments Social Plugin" ) {
					
					?>
					<script src="//connect.facebook.net/en_US/all.js"></script>
					<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

					<script>
					(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=311654765594138";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
						</script>
						<script>
						FB.Event.subscribe('comment.create',
						function(response) {
							FB.api({method: 'fql.query', query:'SELECT text, post_fbid FROM comment WHERE object_id IN (SELECT comments_fbid FROM link_stat WHERE url = "' + document.URL + '") order by time'},function(comments){
								var comment_body = comments[comments.length-1].text;
								var url = document.URL;
								var post_id = url.substring(url.lastIndexOf('?p=') + 3);
								//alert(id);
								$.ajax({
									type: "POST",
									url: '?fb-save-comment=true',
									data: { fb_comment: comment_body, the_post_id : post_id }
								}).done(function( data ) { 
									//Data saved to WP DB for this FB social plugin Comment.
								});
							})
						}
					);
					</script>
					<?php

					echo add_query_arg( 'fb_save_comment', 'true', home_url() );
					add_filter( 'the_content', 'fb_comments_automatic', 30 );
					add_filter( 'comments_array', 'fb_close_wp_comments' );
					echo '<style type="text/css"> #respond, #commentform, #addcomment, #comment-form-wrap .entry-comments { display: none; } </style>';
				}
				else { //both
						add_filter( 'the_content', 'fb_comments_automatic', 30 );
						echo '<style type="text/css"> #respond, #commentform, #addcomment, #comment-form-wrap .entry-comments { display: none; } </style>';
				}
		
		if ( isset($options['comments']['homepage_comments']['enabled']) ) {
			add_filter( 'comments_number', 'fb_get_comments_count' );
		} else {
			add_filter( 'comments_number', 'fb_hide_wp_comments_homepage' );
		}
	}
}
add_action( 'init', 'fb_apply_filters' );

function fb_build_social_plugin_params($options, $plugin = '' ) {
	$params = '';

    if ( 'like' == $plugin ) {
        if ( ! isset( $options['send'] ) || empty( $options['send'] ) ) {
            $params .= 'data-send="false"';
        }
        if ( ! isset( $options['show_faces'] ) || empty( $options['show_faces'] ) ) {
            $params .= 'data-show-faces="false"';
        }
    }

	foreach ($options as $option => $value) {
		$option = str_replace('_', '-', $option);

		$params .= 'data-' . $option . '="' . esc_attr($value) . '" ';
    }

	$params .= 'data-ref="wp" ';

	return $params;
}

if ( true ) {
	add_action( 'add_meta_boxes', 'fb_add_social_plugin_settings_box' );
	add_action( 'save_post', 'fb_add_social_plugin_settings_box_save' );
}

/**
 * Add meta box for social plugin settings for individual posts and pages
 *
 * @since 1.0.2
 */
function fb_add_social_plugin_settings_box() {
	global $post;
	$options = get_option('fb_options');

    $post_types = get_post_types(array('public' => true));
    unset($post_types['attachment']);
    $post_types = array_values($post_types);
    foreach ( $post_types as $post_type ) {
		add_meta_box(
				'fb_social_plugin_settings_box_id',
				__( 'Facebook Social Plugins', 'facebook' ),
				'fb_add_social_plugin_settings_box_content',
				$post_type
		);
	}
}

/**
 * Add meta boxes for a custom Status that is used when posting to an Author's Timeline
 *
 * @since 1.0.2
 */
function fb_add_social_plugin_settings_box_content( $post ) {
	$options = get_option('fb_options');

	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	echo '<table><p>Change the settings below to show or hide particular Social Plugins. </p>';
	foreach ( $features as $feature ) {
		if ( isset ( $options[ $feature ]['enabled'] ) ) {
            $value = get_post_meta($post->ID,"fb_social_plugin_settings_box_$feature",true);
			echo '<tr><td>' . fb_option_name( $feature ) . "</td> <td><label><input type = \"radio\" name=\"fb_social_plugin_settings_box_$feature\" value=\"default\" "
				. ( $value == 'default' || empty($value) ? 'checked="checked" ' : '' ) . "/>Default (" . (isset($options[$feature]['show_on']) && isset($options[$feature]['show_on'][$post->post_type]) ? 'Show' : 'Hide') . ")</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"show\" "
				. ( $value == 'show' ? 'checked="checked" ' : '' ) . "/>Show</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"hide\" "
				. ( $value == 'hide'  ? 'checked="checked" ' : '' ) . "/>Hide</label></td> </tr>" ;
		}
    }
	echo '</table><p class="howto"> If \'Default\' is selected, the Social Plugin will appear based on the global setting, set on the Facebook Settings page.  If you choose "Show" or "Hide", the Social Plugin will ignore the global setting for this ' . $post->post_type . '.</p>';
}

function fb_add_social_plugin_settings_box_save( $post_id ) {
	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	foreach ( $features as $feature ) {
		$index = "fb_social_plugin_settings_box_$feature";
		if ( isset( $_POST) && isset( $_POST[$index] )) {
			switch ( $_POST[ $index ]) {
				case 'default':
					update_post_meta( $post_id, $index, 'default' );
					break;
				case 'show':
					update_post_meta( $post_id, $index, 'show' );
					break;
				case 'hide':
					update_post_meta( $post_id, $index, 'hide' );
					break;
			}
		}
	}
}


/*
 function fb_add_social_plugin_settings_box_content( $post ) {
	$options = get_option('fb_options');

	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	echo '<table><p>Change the settings below to show or hide particular Social Plugins.</p>';
	foreach ( $features as $feature ) {
		if ( $options[$feature]['enabled'] ) {
			$value = get_post_meta( $post->ID, "fb_social_plugin_settings_box_$feature", true );

			if ( empty( $value ) ) {
				if ( $post->post_type == 'page' ) {
					if ( $options[$feature]['show_on'] == 'all posts and pages' || $options[$feature]['show_on'] == 'all pages' ) {
						$value = 'show';
					}
					else {
						$value = 'hide';
					}
				}
				else {
					if ( $options[$feature]['show_on'] == 'all posts and pages' || $options[$feature]['show_on'] == 'all posts' ) {
						$value = 'show';
					}
					else {
						$value = 'hide';
					}
				}
			}

			echo '<tr><td>' . fb_option_name( $feature ) . "</td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"show\" "
				. ( $value == 'show' ? 'checked="checked" ' : '' ) . "/>Show</label></td> <td><label><input type=\"radio\" name=\"fb_social_plugin_settings_box_$feature\" value =\"hide\" "
				. ( $value == 'hide'  ? 'checked="checked" ' : '' ) . "/>Hide</label></td> </tr>" ;
		}
	}
	echo '</table>';
}

function fb_add_social_plugin_settings_box_save( $post_id ) {
	$features = array( 'like', 'subscribe', 'send', 'comments', 'recommendations_bar' );
	foreach ( $features as $feature ) {
		$index = "fb_social_plugin_settings_box_$feature";
		if ( isset( $_POST) && isset( $_POST[$index] )) {
			switch ( $_POST[$index]) {
				case 'show':
					update_post_meta( $post_id, $index, 'show' );
					break;
				case 'hide':
					update_post_meta( $post_id, $index, 'hide' );
					break;
			}
		}
	}
}
*/
?>
