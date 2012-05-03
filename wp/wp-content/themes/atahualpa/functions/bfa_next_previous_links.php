<?php
/* Check if several pages exist to avoid empty
   next/prev navigation on multi post pages */
function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1) ? TRUE : FALSE;
}

/* Next/Previous PAGE Links (on multi post pages)
   in next_posts_link "next" means older posts
   Available parameters for $location: Top, Bottom. Default: Top */
function bfa_next_previous_page_links($location = "Top") {

	global $bfa_ata, $homeURL;

	if ( !is_single() AND !is_page() AND
    strpos($bfa_ata['location_multi_next_prev'],$location) !== FALSE AND
    
    // don't display on WP Email pages
    intval(get_query_var('email')) != 1 AND
    
    // display only if next/prev links actually exist
    show_posts_nav() ) {

		if ( function_exists('wp_pagenavi') ) {

			echo '<div class="wp-pagenavi-navigation">'; wp_pagenavi();
			echo '</div>';

		} else {

			if($bfa_ata['home_multi_next_prev'] != '') { 
				ob_start();
					echo '<div class="home"><a href="' . $homeURL . '/">' . 
					$bfa_ata['home_multi_next_prev'] . '</a></div>'; 
					$nav_home_div_on = ob_get_contents(); 
				ob_end_clean();
				
				// for WP 2.5 and newer
				if ( function_exists('is_front_page') ) {

					// make sure this is the real homepage and not a subsequent page
					if ( is_front_page() AND !is_paged() ) {
						$nav_home_add = ""; $nav_home_div = ""; 
					} else {
						$nav_home_add = '-home';
						$nav_home_div = $nav_home_div_on; 
					}
				} 
				
				/* For WP 2.3 and older: Make sure this is the real homepage
				   and not a subsequent page */
				elseif ( is_home() AND !is_paged() ) {
					$nav_home_add = ""; $nav_home_div = "";
				} else { 
					$nav_home_add = '-home'; 
					$nav_home_div = $nav_home_div_on; 
				}	
			} else {
					$nav_home_add = ''; 
					$nav_home_div = ''; 
			}
			
			echo '<div class="clearfix navigation-'.strtolower($location).'">
			<div class="older' . $nav_home_add . '">';

			$bfa_ata['next_prev_orientation'] == 'Older Left, Newer Right' ? 
			next_posts_link($bfa_ata['multi_next_prev_older']) : 
			previous_posts_link($bfa_ata['multi_next_prev_newer']);

			echo ' &nbsp;</div>' . $nav_home_div . '<div class="newer' .
            $nav_home_add . '">&nbsp; ';

			$bfa_ata['next_prev_orientation'] == 'Older Left, Newer Right' ? 
			previous_posts_link($bfa_ata['multi_next_prev_newer']) : 
			next_posts_link($bfa_ata['multi_next_prev_older']);

			echo '</div></div>';
		}
	} 						
}

/* Next/Previous POST Links (on single post pages)
   in next_post_link "next" means newer posts
   Available parameters for $location: Top, Middle, Bottom. Default: Top  */
function bfa_next_previous_post_links($location = "Top") {

global $bfa_ata, $homeURL;

	if ( is_single() AND strpos($bfa_ata['location_single_next_prev'],$location) !== FALSE AND
	
    // don't display on WP Email pages
    intval(get_query_var('email')) != 1 )  {

		echo '<div class="clearfix navigation-'.strtolower($location).'">
		<div class="older' . ($bfa_ata['home_single_next_prev'] != '' ?
        '-home' : '') . '">';

		if ($bfa_ata['next_prev_orientation'] == 'Older Left, Newer Right') {
			if($bfa_ata['single_next_prev_same_cat'] == "Yes") {
				previous_post_link($bfa_ata['single_next_prev_older'], '%title', TRUE);
			} else { 
				previous_post_link($bfa_ata['single_next_prev_older']);
			}
		} else {
			if($bfa_ata['single_next_prev_same_cat'] == "Yes") {
				next_post_link($bfa_ata['single_next_prev_newer'], '%title', TRUE);
			} else { 
				next_post_link($bfa_ata['single_next_prev_newer']);
			}
		}
		
		echo ' &nbsp;</div>';
		if ($bfa_ata['home_single_next_prev'] != '') { 
			echo '<div class="home"><a href="' . $homeURL . '/">' .
			$bfa_ata['home_single_next_prev'] . '</a></div>';
		}
		echo '<div class="newer';
		if ($bfa_ata['home_single_next_prev'] != '') {
			echo '-home';
		}
		echo '">&nbsp; ';

		if ($bfa_ata['next_prev_orientation'] == 'Older Left, Newer Right') {
			if($bfa_ata['single_next_prev_same_cat'] == "Yes") {
				next_post_link($bfa_ata['single_next_prev_newer'], '%title', TRUE);
			} else { 
				next_post_link($bfa_ata['single_next_prev_newer']);
			}
		} else {
			if($bfa_ata['single_next_prev_same_cat'] == "Yes") {
				previous_post_link($bfa_ata['single_next_prev_older'], '%title', TRUE);
			} else { 
				previous_post_link($bfa_ata['single_next_prev_older']);
			}
		}

		echo '</div></div>';
	}
}

/* Next/Previous Comments Links.
   In next_comments_link "next" means newer.
   If navigation above comments is set: */
function bfa_next_previous_comments_links($location = "Above") {

	global $bfa_ata;

	if ( strpos($bfa_ata['location_comments_next_prev'],$location) !== FALSE ) {

		// if any navigation links exist, paginated or next/previous:
		if ( get_comment_pages_count() > 1 ) {

			// Overall navigation container
			echo '<div class="clearfix navigation-comments-'.strtolower($location).'">';

			if ( $bfa_ata['next_prev_comments_pagination'] == "Yes" ) {

				// paginated links
				paginate_comments_links(array(
				'prev_text' => $bfa_ata['comments_next_prev_older'],
				'next_text' => $bfa_ata['comments_next_prev_newer'],
				));

			} else {

				// next/previous links
				echo '<div class="older">';

				$bfa_ata['next_prev_orientation'] == 'Older Left, Newer Right' ?
				previous_comments_link($bfa_ata['comments_next_prev_older']) :
				next_comments_link($bfa_ata['comments_next_prev_newer']);

				echo ' &nbsp;</div><div class="newer">&nbsp; ';

				$bfa_ata['next_prev_orientation'] == 'Older Left, Newer Right' ?
				next_comments_link($bfa_ata['comments_next_prev_newer']) :
				previous_comments_link($bfa_ata['comments_next_prev_older']);

				echo '</div></div>';

			}
			
			echo '</div>';
		}
	}
}
?>