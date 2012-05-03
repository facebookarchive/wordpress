<?php

// gets comments from FB for the post, based on the auto-published post
function sfc_getcomm_check($comments, $id) {
	$fbpagepost = get_post_meta($id,'_fb_post_id_app',true);
	$fbprofpost = get_post_meta($id,'_fb_post_id_profile',true);
	
	if (!$fbpagepost && !$fbprofpost) return $comments;

	$options = get_option('sfc_options');
	
	if ($fbpagepost || $fbprofpost) {
		if ( false === ( $newcomms = get_transient('sfcgetcomm-'.$id) ) ) {
			$newcomms = array();
			
			// get comments from the app or page post
			if ($fbpagepost && !empty($options['fanpage'])) {
			
				$token = $options['page_access_token'];
				
				$fbresp = sfc_remote($fbpagepost, 'comments', array('access_token'=>$token));
				  
				if (!empty($fbresp['data'])) {
					foreach ($fbresp['data'] as $fbcomm) {
						$nc=null;
						$nc->comment_ID = $fbcomm['id'];
						$nc->fbid = $fbcomm['from']['id'];
						$nc->comment_post_ID = $id;
						$nc->comment_author = $fbcomm['from']['name'];
						$nc->comment_author_email = '';
						$nc->comment_author_url = 'http://www.facebook.com/profile.php?id='.$fbcomm['from']['id'];
						$nc->comment_author_IP = '';
						$time = strtotime ($fbcomm['created_time']);
						$nc->comment_date = date('Y-m-d H:i:s', $time);
						$nc->comment_date_gmt = gmdate('Y-m-d H:i:s', $time);;
						$nc->comment_content = $fbcomm['message'];
						$nc->comment_karma = 0;
						$nc->comment_approved = 1;
						$nc->comment_agent = 'SFC/1.0 (WordPress; en-US) SFC-GetComments/1.0';
						$nc->comment_type = '';
						$nc->comment_parent = 0;
						$nc->user_id = 0;
						
						wp_cache_add($nc->comment_ID, $nc, 'comment');
						
						$newcomms[] = $nc;
					}
				}
			}
			
			// get comments from the profile post
			if ($fbprofpost) {
				$token = $options['access_token'];
				$fbresp = sfc_remote($fbprofpost, 'comments', array('access_token'=>$token));

				if (!empty($fbresp['data'])) {
					foreach ($fbresp['data'] as $fbcomm) {
						$nc=null;
						$nc->comment_ID = $fbcomm['id'];
						$nc->fbid = $fbcomm['from']['id'];
						$nc->comment_post_ID = $id;
						$nc->comment_author = $fbcomm['from']['name'];
						$nc->comment_author_email = '';
						$nc->comment_author_url = 'http://www.facebook.com/profile.php?id='.$fbcomm['from']['id'];
						$nc->comment_author_IP = '';
						$time = strtotime ($fbcomm['created_time']);
						$nc->comment_date = date('Y-m-d H:i:s', $time);
						$nc->comment_date_gmt = gmdate('Y-m-d H:i:s', $time);;
						$nc->comment_content = $fbcomm['message'];
						$nc->comment_karma = 0;
						$nc->comment_approved = 1;
						$nc->comment_agent = 'SFC/1.0 (WordPress; en-US) SFC-GetComments/1.0';
						$nc->comment_type = '';
						$nc->comment_parent = 0;
						$nc->user_id = 0;

						wp_cache_add($nc->comment_ID, $nc, 'comment');
												
						$newcomms[] = $nc;
					}
				}
			}
			
			set_transient('sfcgetcomm-'.$id, $newcomms, 6*60*60); // 6 hours seems reasonable
		}

		global $sfc_getcomm_counts;
		$sfc_getcomm_counts[$id] = count($newcomms);
		
		// build a new array based on the two existing arrays
		if ( !empty($newcomms) ) {
			$finalcomm=array();
			while ( !empty($comments) || !empty($newcomms) ) {
				if (empty($comments)) {
					$finalcomm = array_merge($finalcomm, $newcomms);
					$newcomms = array();
				}
				if (empty($newcomms)) {
					$finalcomm = array_merge($finalcomm, $comments);
					$comments = array();
				}
				if ( strtotime($comments[0]->comment_date) < strtotime($newcomms[0]->comment_date) ) {
					$finalcomm[] = array_shift($comments);
				} else {
					$finalcomm[] = array_shift($newcomms);
				}
			}
			
			$comments = $finalcomm;
		}
	}

	return $comments;
}
add_filter('comments_array', 'sfc_getcomm_check', 10, 2);

// fix the comment count to have the FB-imported comments
function sfc_getcomm_correct_count($count, $id) {
	global $sfc_getcomm_counts;
	if ( $sfc_getcomm_counts[$id] ) $count += $sfc_getcomm_counts[$id];
	
	return $count;
}
add_filter('get_comments_number','sfc_getcomm_correct_count', 10, 2);

// add a "facebook" class to FB-imported comments
function sfc_getcomm_class($classes) {
	if ( !empty($GLOBALS['comment']->fbid) ) {
		$classes[] = 'facebook';
	}

	return $classes;
}
add_filter('comment_class','sfc_getcomm_class', 10, 1);

// show FB avatars for FB-imported comments
function sfc_getcomm_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = false) {
	if ( !is_object($id_or_email) || !isset($id_or_email->fbid))
		 return $avatar;

	return "<img width='{$size}' height='{$size}' class='avatar avatar-{$size} fbavatar' src='http://graph.facebook.com/{$id_or_email->fbid}/picture?type=square' />";	
}
add_filter('get_avatar','sfc_getcomm_avatar', 10, 5);

// remove the reply link fron FB-imported comments
function sfc_getcomm_reply_link($link, $args, $comment, $post) {
	if (!empty($comment->fbid)) $link = '';
	return $link;
}
add_filter('comment_reply_link','sfc_getcomm_reply_link',10,4);