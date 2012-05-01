<?php 
function framework_version(){
	global $natty_themename, $natty_current, $post;
  $natty_framework_version = "2.1.5";
	
    echo '<meta name="template" content="'.$natty_themename.' '. $natty_current .'" />' ."\n";
    echo '<meta name="generator" content="NattyWP Framework Version '. $natty_framework_version .'" />' ."\n";
	
	if (is_single() || is_page()) {
		$custom_description = get_post_meta($post->ID, 'natty_description', true);
		$custom_keywords = get_post_meta($post->ID, 'natty_keywords', true);
		if (strlen($custom_keywords))
			echo '<meta name="keywords" content="' . trim(wptexturize(strip_tags(stripslashes($custom_keywords)))) . '" />'."\n";
			
		if (strlen($custom_description))
			echo '<meta name="description" content="' . trim(wptexturize(strip_tags(stripslashes($custom_description)))) . '" />'."\n";
		else {
			if (t_get_option('t_meta_desc') != '') {
				echo '<meta name="description" content="'.t_get_option('t_meta_desc').'" />'."\n";
			} else { 
				echo '<meta name="description" content="'.get_bloginfo('description').' " />'."\n";
			}	
		}		
	} else {
		if (t_get_option('t_meta_desc') != '') {
			echo '<meta name="description" content="'.t_get_option('t_meta_desc').'" />'."\n";
		} else { 
			echo '<meta name="description" content="'.get_bloginfo('description').' " />'."\n";
		}
	}
	
  $t_favico = get_option( "nattywp_custom_favicon" );
	if( $t_favico == '' || $t_favico == 'no') {
    echo '<link href="'.get_template_directory_uri().'/images/icons/favicon.ico" rel="shortcut icon" type="image/x-icon" />'."\n";
	} else {
    echo '<link href="'.$t_favico.'" rel="shortcut icon" type="image/x-icon" />'."\n";
	}	
	
	//Localization
	load_theme_textdomain('nattywp');	
	
	// Shortcodes
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/functions/css/shortcodes.css" media="screen" />';
	echo '<script type="text/javascript" src="'.get_bloginfo('template_directory').'/functions/js/shortcode.js"></script>';
}
add_action('wp_head','framework_version');


// offset and pagination
function my_post_limit($limit) {
	global $paged, $myOffset;
	if (empty($paged)) {
			$paged = 1;
	}
	$postperpage = intval(get_option('posts_per_page'));
	$pgstrt = ((intval($paged) -1) * $postperpage) + $myOffset . ', ';
	$limit = 'LIMIT '.$pgstrt.$postperpage;
	return $limit;
}

function t_get_option($option='', $echo='') {
  global $natty_themename;
  $natty_options = get_option($natty_themename.'_settings');
  if (isset($natty_options[$option])){
    $framework_options = $natty_options[$option];
  } else {$framework_options = 'no';}
  if ($echo == '1') {
    echo $framework_options;    
  } else {
    return $framework_options;
  }
}

function t_get_coption($option='', $echo='') {
  global $natty_themename;
  $natty_coptions = get_option($natty_themename.'_color_settings');
  $framework_coptions = $natty_coptions[$option];
  if ($echo == '1') {
    echo $framework_coptions;    
  } else {
    return $framework_coptions;
  }
}

function t_get_logo ($before = '', $after = '', $t_logo ='', $link = true) {	  
	$output = '';
	$t_custom_logo = get_option( "nattywp_custom_logo" );
	if ($t_custom_logo == '') {
    if ($t_logo == ''){$t_logo = t_get_option( "t_logo" );}
    if ($t_logo != "") {       
        $output .= $before;
        if($link == true) {
        $output .= '<a href="'. home_url() .'"><img src="'. get_template_directory_uri() .'/images/logo/'. $t_logo .'" border="0" class="png" alt="'. get_bloginfo('name') .'" /></a>';
        } else {
        $output .= '<img src="'. get_template_directory_uri() .'/images/logo/'. $t_logo .'" border="0" class="png" alt="'. get_bloginfo('name') .'" />';
        }
        $output .= $after; 
    } 
	} else {
    $output .= $before; 
    if($link == true) {
    $output .= '<a href="'. home_url() .'"><img src="'. $t_custom_logo .'" border="0" class="png" alt="'. get_bloginfo('name') .'" /></a>';
    } else {
      $output .= '<img src="'. $t_custom_logo .'" border="0" class="png" alt="'. get_bloginfo('name') .'" />';
    }
    $output .= $after; 
	}
	echo $output;
}


function t_most_commented( $no_posts = 15, $before = '<li>', $after = '</li>', $show_pass_post = false, $duration = '', $echo = true ) {
	global $wpdb;

	if ( !($posts = wp_cache_get('t_most_commented')) ) {
		$request = "SELECT ID, post_title, comment_count FROM $wpdb->posts WHERE post_status = 'publish'";
		if ( !$show_pass_post ) $request .= " AND post_password =''";
		if ( is_int($duration) ) $request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
		if ( !is_int($no_posts) ) $no_posts = 5;
		$request .= " ORDER BY comment_count DESC LIMIT $no_posts";

		$posts = $wpdb->get_results($request);

		wp_cache_set('t_most_commented', $posts, '', 1800);
	}

	if ( $echo ) {
		if ( !empty($posts) ) {
			foreach ($posts as $post) {
				$post_title = apply_filters('the_title', $post->post_title);
				$comment_count = $post->comment_count;
				$permalink = get_permalink($post->ID);
				$t_most_commented .= $before . '<a href="' . $permalink . '" title="' . $post_title.'">' . $post_title . '</a> (' . $comment_count.')' . $after;
			}
		} else {
			$t_most_commented .= $before . "None found" . $after;
		}

		echo $t_most_commented;
	} else {
		return $posts;
	}
}

function natty_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
  if (function_exists('wp_pagenavi')) { wp_pagenavi(); } 
  else {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);		
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);		
		}
		$fromwhere = $matches[1];
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class='nav'>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">'. __('&laquo; First', 'nattywp').'</a>';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='on'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo '<a href="'.get_pagenum_link($max_page).'">'. __('Last &raquo;', 'nattywp').'</a>';
			}
			echo "</div> $after";
		}
	}
	}	
}


function my_excerpt_length($length) {
return 20; }

function custom_excerpt($text, $excerpt_length = 15) {
	
	$text = str_replace(']]>', ']]>', $text);
	$text = strip_tags($text);
	$words = explode(' ', $text, $excerpt_length + 1);
	if (count($words) > $excerpt_length) {
		array_pop($words);
		array_push($words, '[...]');
		$text = implode(' ', $words);
	}

	return apply_filters('the_excerpt', $text);
}

function trimes($cont){
$excerpt_length = 55;
	$words = explode(' ', $cont, $excerpt_length + 1);	
		if (count($words) > $excerpt_length) {
			array_pop($words);
			array_push($words, '...');
			$text = implode(' ', $words);
		} else {
			$text = $cont;
		}
return $text;
}

function t_show_popular($popular_num = 7, $before = '', $after = '') {
	global $wpdb;

	$now = gmdate("Y-m-d H:i:s",time());
	$lastmonth = gmdate("Y-m-d H:i:s",gmmktime(date("H"), date("i"), date("s"), date("m")-1,date("d"),date("Y")));
	$popularposts = "SELECT ID, post_title, post_content, post_date, comment_count, COUNT($wpdb->comments.comment_post_ID) AS 'popular' FROM $wpdb->posts, $wpdb->comments WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish' AND post_date < '$now' AND comment_status = 'open' GROUP BY $wpdb->comments.comment_post_ID ORDER BY popular DESC LIMIT ".$popular_num ."";
	$myposts = $wpdb->get_results($popularposts);
	$popular = '';
	if($myposts){
		foreach($myposts as $postas){
			$post_title = stripslashes($postas->post_title);
			$post_date = stripslashes($postas->post_date);
			$comments = stripslashes($postas->comment_count);
			$cont = $postas->post_content;
			//$text = apply_filters('the_excerpt', $cont);
			$guid = get_permalink($postas->ID);
			
			//the_post($post->ID);
			$popular .= $before;
			$popular .= '<a href="'.$guid.'" title="'.$post_title.'">'.$post_title.'</a> ('.$comments.')';
			$popular .= $after;
		}
	} echo $popular;
} 


function t_show_comments($comments_num = 7,  $before = '', $after = '') {
	global $wpdb;
		$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
				comment_post_ID, comment_author, comment_date, comment_approved,
				comment_type,comment_author_url,
				SUBSTRING(comment_content,1,80) AS com_excerpt
				FROM $wpdb->comments
				LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
				$wpdb->posts.ID)
				WHERE comment_approved = '1' AND comment_type = '' AND
				post_password = ''
				ORDER BY comment_date DESC
				LIMIT ".$comments_num ."";
		$comments = $wpdb->get_results($sql);
		$output = $pre_HTML;
		foreach ($comments as $comment) {	
			$output .= $before;
			$output .= strip_tags($comment->comment_author).' &ndash; <a href="'.get_permalink($comment->ID).'#comment-'.$comment->comment_ID.'">'.strip_tags($comment->com_excerpt).'</a>';
			$output .= $after;
		}
		$output .= $post_HTML;
		echo $output;			
} 

function t_show_catwithdesc() {
	$getcats = get_categories('hierarchical=0&hide_empty=1');
				foreach($getcats as $thecat) {
				echo '<li><a href="'.get_category_link($thecat->term_id).'" title="View Posts in &quot;'.$thecat->name.'&quot;: '.$thecat->description.'">'.$thecat->name.'</a>'.$thecat->description.'</li>';
				}
}

function t_show_pagemenu($exclude='') {		
	if (t_get_option('t_show_pages') == 'yes'){
			t_show_pag();
	} else {		
			t_show_cat();
	}
}

function t_show_pag($before= '', $after= '') {
     $exclude = t_get_option('t_exclude_pages');     
			if ($exclude == 'no' || $exclude[0] == 'no' || $exclude[0] == ''){
				$exclude = '';
			} else {
				if(is_array($exclude)) {$exclude = join(',', $exclude );}
			}		
		$pages = wp_list_pages('sort_column=menu_order&title_li=&echo=0&depth=0&exclude='. $exclude);
		$pages = preg_replace('%<a ([^>]+)>%U','<a $1><span>', $pages);
		$pages = str_replace('</a>','</span></a>', $pages);
		echo $before . $pages . $after;
}

function t_show_cat($before= '', $after= '', $desc=false) {
   $exclude = t_get_option('t_exclude_cats');
			if ($exclude == 'no' || $exclude[0] == 'no' || $exclude[0] == ''){
				$exclude = '';
			} else {
				if(is_array($exclude)) {$exclude = join(',', $exclude );}
			}			
		if ($desc == false) {
      $categories = wp_list_categories('sort_column=menu_order&depth=3&echo=0&title_li=&exclude='. $exclude ); 
      $categories = preg_replace('%<a ([^>]+)>%U','<a $1><span>', $categories);
      $categories = str_replace('</a>','</span></a>', $categories);
		} else {
      $getcats = get_categories('hierarchical=0&hide_empty=1');
			foreach($getcats as $thecat) {
        $categories.= '<li><a href="'.get_category_link($thecat->term_id).'" title="'.sprintf( __( "View all posts under %s", 'nattywp' ), $thecat->name ).'"><span>'.$thecat->name.'</span>';
        $categories.= !empty($thecat->description) ? '<span class="desc">'.$thecat->description.'</span>' : '';
        $categories.= '</a></li>';
				}
		}
		echo $before . $categories . $after;
}
?>