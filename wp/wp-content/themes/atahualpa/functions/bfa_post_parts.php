<?php
function bfa_post_kicker($before = '<div class="post-kicker">', $after = '</div>') 
{
	global $bfa_ata;
	
    // don't display on WP Email pages
    if(intval(get_query_var('email')) != 1) {
    	
    	if( (is_home() AND $bfa_ata['post_kicker_home'] != "") OR
    	(is_page() AND $bfa_ata['post_kicker_page'] != "") OR
    	(is_single() AND $bfa_ata['post_kicker_single'] != "") OR
    	( (is_archive() OR is_search() OR is_author() OR is_tag()) AND $bfa_ata['post_kicker_multi'] != "") ) {
    		
			echo $before;
			
			if ( is_home() ) 		$kickertype = 'post_kicker_home'; 
			elseif ( is_page() ) 	$kickertype = 'post_kicker_page'; 
			elseif ( is_single() ) 	$kickertype = 'post_kicker_single'; 
			else 					$kickertype = 'post_kicker_multi'; 
			
			echo postinfo($bfa_ata[$kickertype]);
			
			echo $after;		
    	}
    }
}

function bfa_post_headline($before = '<div class="post-headline">', $after = '</div>') 
{
	global $bfa_ata, $post;

//  Case 1 - 'Use Post / Page Options' is no, then just use the post/page title
	if ($bfa_ata['page_post_options'] == 'No') {
		echo $before; 
		?><h<?php echo $bfa_ata['h_posttitle']; ?>><?php 
			if ( is_single() OR is_page() ) {
				the_title(); ?></h<?php echo $bfa_ata['h_posttitle']; ?>><?php
			} else { ?>
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to ','atahualpa') . the_title_attribute('echo=1') ?>">
				<?php the_title(); ?></a></h<?php echo $bfa_ata['h_posttitle']; ?>><?php
			}
		echo $after;
		return;
	}
//  Case 2 - 'Use Post / Page Options' is Yes, then use the BFA title
	if ( is_single() OR is_page() ) {
		$bfa_ata_body_title = get_post_meta($post->ID, 'bfa_ata_body_title', true);
		$bfa_ata_display_body_title = get_post_meta($post->ID, 'bfa_ata_display_body_title', true);
		$bfa_ata_body_title_multi = get_post_meta($post->ID, 'bfa_ata_body_title_multi', true);
	} else {
		$bfa_ata_body_title_multi = get_post_meta($post->ID, 'bfa_ata_body_title_multi', true);
	}
	
	// Since 3.6.1: Display a link to the full post if there is no post title and the post is too short
	// for a read more link.
	
	// some plugins hook into 'the_title()' so we only want it called once. But it must also be called 
	// when using the bfa_ata titles so we use a dummy call:
	//		$bfa_toss = the_title('','',false); 
	// in those cases
	$bfa_temp_title = get_the_title();
	if ( $bfa_temp_title == '' ) { ?>
		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link','atahualpa')?>">Permalink</a><?php 
		
	} elseif ( (!is_single() AND !is_page()) OR $bfa_ata_display_body_title == '' ) {
		
		echo $before; ?>
		<h<?php echo $bfa_ata['h_posttitle']; ?>><?php 
			
		if( !is_single() AND !is_page() ) { ?>
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to ','atahualpa') .  the_title_attribute('echo=1') ?>"><?php 
		} 

		if ( (is_single() OR is_page()) AND $bfa_ata_body_title != "" ) {
			echo htmlentities($bfa_ata_body_title,ENT_QUOTES,'UTF-8');
			$bfa_toss = the_title('','',false);
		} else {
			if ( $bfa_ata_body_title_multi != '' ) {
				echo htmlentities($bfa_ata_body_title_multi,ENT_QUOTES,'UTF-8');  
				$bfa_toss = the_title('','',false);
			} else 
				echo $bfa_temp_title; 
		}

		if ( !is_single() AND !is_page() ) { ?></a><?php } ?></h<?php echo $bfa_ata['h_posttitle']; ?>>
		<?php echo $after;
	}
}




function bfa_post_byline($before = '<div class="post-byline">', $after = '</div>') 
{
	global $bfa_ata, $post;
	
    // don't display on WP Email pages
    if(intval(get_query_var('email')) != 1) {
    	
    	if( (is_home() AND $bfa_ata['post_byline_home'] != "") OR
    	(is_page() AND $bfa_ata['post_byline_page'] != "") OR
    	(is_single() AND $bfa_ata['post_byline_single'] != "") OR
    	( (is_archive() OR is_search() OR is_author() OR is_tag()) AND $bfa_ata['post_byline_multi'] != "") ) {
    		
    		echo $before;

			if ( is_home() ) 		$bylinetype = 'post_byline_home'; 		
			elseif ( is_page() ) 	$bylinetype = 'post_byline_page'; 
			elseif ( is_single() )	$bylinetype = 'post_byline_single'; 
			else 					$bylinetype = 'post_byline_multi'; 
			
			echo postinfo($bfa_ata[$bylinetype]);
					
    		echo $after;
    	}
    }
}

						
function bfa_post_bodycopy($before = '<div class="post-bodycopy clearfix">', $after = '</div>') 
{
	global $bfa_ata, $post, $bfa_pagetemplate_name, $bfa_pagetemplate_full_post_count, $bfa_ata_postcount;

	$do_full_post = 0;
	echo $before; 
	if ((is_home()     AND $bfa_ata['excerpts_home'] == "Full Posts") 
	OR  (is_home()     AND !is_paged() AND $bfa_ata_postcount <= $bfa_ata['full_posts_homepage']) 
	OR  (is_category() AND $bfa_ata['excerpts_category'] == "Full Posts") 
	OR  (is_date()     AND $bfa_ata['excerpts_archive'] == "Full Posts") 
	OR  (is_tag()      AND $bfa_ata['excerpts_tag'] == "Full Posts") 
	OR  (is_search()   AND $bfa_ata['excerpts_search'] == "Full Posts") 
	OR  (is_author()   AND $bfa_ata['excerpts_author'] == "Full Posts") 
	OR   is_single() 
	OR   is_page() 
	) { $do_full_post = 1; }
	
	if (bfa_is_pagetemplate_active($bfa_pagetemplate_name)) {
		if ($bfa_ata_postcount <= $bfa_pagetemplate_full_post_count) 
			{ $do_full_post = 1; }
		else 
			{ $do_full_post = 0; }
	}
	
	if ($do_full_post == 1) {
		$bfa_ata_more_tag_final = str_replace("%post-title%", the_title('', '', false), $bfa_ata['more_tag']);
		the_content($bfa_ata_more_tag_final); 
	} else { 
		if (function_exists('the_post_thumbnail') AND !function_exists('tfe_get_image')) {
		     if(has_post_thumbnail()): ?>
                <a href="<?php the_permalink() ?>"> <?php the_post_thumbnail(); ?></a>
				<?php endif;
		}
		the_excerpt(); 
	} 
	echo $after;
}

						
function bfa_post_pagination($before = '<p class="post-pagination"><strong>Pages:', $after = '</strong></p>') 
{
	global $bfa_ata, $bfa_ata_postcount;
	
	if ((is_home()     AND $bfa_ata['excerpts_home'] == "Full Posts") 
	OR  (is_home()     AND !is_paged() AND $bfa_ata_postcount <= $bfa_ata['full_posts_homepage']) 
	OR 	(is_category() AND $bfa_ata['excerpts_category'] == "Full Posts") 
	OR 	(is_date()     AND $bfa_ata['excerpts_archive'] == "Full Posts") 
	OR 	(is_tag()      AND $bfa_ata['excerpts_tag'] == "Full Posts") 
	OR 	(is_search()   AND $bfa_ata['excerpts_search'] == "Full Posts") 
	OR 	(is_author()   AND $bfa_ata['excerpts_author'] == "Full Posts") 
	OR 	is_single() 
	OR is_page() ) {
		wp_link_pages('before='.$before.'&after='.$after.'&next_or_number=number'); 
	} 
}


function bfa_archives_page($before = '<div class="archives-page">', $after = '</div>') 
{
	global $bfa_ata, $wp_query, $templateURI;
	$current_page_id = $wp_query->get_queried_object_id();
	
	if ( is_page() AND $current_page_id == $bfa_ata['archives_page_id'] ) { 
		
		echo $before;				
		if ( $bfa_ata['archives_date_show'] == "Yes" ) { ?>
			<h3><?php echo $bfa_ata['archives_date_title']; ?></h3>
			<ul>
			<?php wp_get_archives('type=' . $bfa_ata['archives_date_type'] . '&show_post_count=' . 
			($bfa_ata['archives_date_count'] == "Yes" ? '1' : '0') . ($bfa_ata['archives_date_limit'] != "" ? '&limit=' . 
			$bfa_ata['archives_date_limit'] : '')); ?>
			</ul>
		<?php } 						
		if ( $bfa_ata['archives_category_show'] == "Yes" ) { ?>
			<h3><?php echo $bfa_ata['archives_category_title']; ?></h3>
			<ul>
			<?php wp_list_categories('title_li=&orderby=' . $bfa_ata['archives_category_orderby'] . 
			'&order=' . $bfa_ata['archives_category_order'] . 
			'&show_count=' . ($bfa_ata['archives_category_count'] == "Yes" ? '1' : '0') . 
			'&depth=' . $bfa_ata['archives_category_depth'] . 
			($bfa_ata['archives_category_feed'] == "Yes" ? '&feed_image=' . $templateURI . 
			'/images/icons/feed.gif' : '')); ?>
			</ul>
		<?php } 
		echo $after;
	}
}


function bfa_post_footer($before = '<div class="post-footer">', $after = '</div>') 
{
	global $bfa_ata, $post;
	
    // don't display on WP Email pages
    if(intval(get_query_var('email')) != 1) {
    	
    	if( (is_home() AND $bfa_ata['post_footer_home'] != "") OR
    	(is_page() AND $bfa_ata['post_footer_page'] != "") OR
    	(is_single() AND $bfa_ata['post_footer_single'] != "") OR
    	( (is_archive() OR is_search() OR is_author() OR is_tag()) AND $bfa_ata['post_footer_multi'] != "") ) {
    		
    		echo $before;
			
			if ( is_home() ) 			$footertype = 'post_footer_home'; 
			elseif ( is_page() ) 		$footertype = 'post_footer_page'; 
			elseif ( is_single() )		$footertype = 'post_footer_single'; 
			else 						$footertype = 'post_footer_multi'; 
			
			echo postinfo($bfa_ata[$footertype]);
					
    		echo $after;
    	}
    }
}


function bfa_get_comments() 
{
	global $bfa_ata;
    
	// Load Comments template (on single post pages, and "Page" pages, if set on options page)
	if ( is_single() OR ( is_page() AND $bfa_ata['comments_on_pages'] == "Yes") ) {
		
		// don't display on WP-Email pages
		if( intval(get_query_var('email')) != 1 ) {
			
			if ( function_exists('paged_comments') ) {
				// If plugin "Paged Comments" is activated, for WP 2.6 and older
				paged_comments_template(); 
			} else {
				// This will load either legacy comments template (for WP 2.6 and older) or the new standard comments template (for WP 2.7 and newer)
				comments_template(); 
			}
		}
	}
}
?>