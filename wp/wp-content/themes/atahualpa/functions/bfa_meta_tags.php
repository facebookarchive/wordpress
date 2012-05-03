<?php
function bfa_meta_tags() {
	global $bfa_ata, $post;
		
	ob_start();

	/* check to see if any of these SEO plugins is installed.
	   If yes, the Bytes For All SEO options will be deactivated,
	   no matter what the option "Use Bytes For All SEO options?" is set to. */

	// if "SEO Ultimate" Plugin (http://www.seodesignsolutions.com/wordpress-seo/) is installed
	if(class_exists('seo_ultimate') OR

	// if "All-In-One_SEO" Plugin (http://semperfiwebdesign.com) is installed
	class_exists('All_in_One_SEO_Pack'))
	{	?> <title><?php wp_title(''); ?></title> <?php }
	
	// if "WpSEO" Plugin (http://www.wpseo.de/) is installed
	elseif (class_exists('wpSEO') OR

	// if "HeadSpace2" Plugin (http://urbangiraffe.com/plugins/headspace2/) is installed
	class_exists('HeadSpace2_Admin') OR

	// if "SEO Title Tag" Plugin (http://www.netconcepts.com/seo-title-tag-plugin/) is installed
	function_exists('seo_title_tag_options_page') OR

	/* if "Another WordPress Meta Plugin"
	   (http://wp.uberdose.com/2006/11/04/another-wordpress-meta-plugin/) is installed */
	class_exists('Another_WordPress_Meta_Plugin') OR

	// if "Platinum_SEO_Pack" Plugin (http://techblissonline.com/platinum-seo-pack/) is installed
	class_exists('Platinum_SEO_Pack') OR

	/* if "HeadMeta" Plugin is installed
	   (http://dougal.gunters.org/blog/2004/06/17/my-first-wordpress-plugin-headmeta) */
	function_exists('headmeta') OR

	/* if "Improved Meta Description Snippets" Plugin is installed
	  (http://www.microkid.net/wordpress-plugins/improved-meta-description-snippets/) */
	function_exists('bas_improved_meta_descriptions') OR

	// if "Head META Description" Plugin (http://guff.szub.net/head-meta-description/) is installed
	function_exists('head_meta_desc') OR

	// if "Robots Meta" Plugin (http://yoast.com/wordpress/robots-meta/) is installed
	class_exists('RobotsMeta_Admin') OR

	// if "Quick META Keywords" Plugin (http://www.quickonlinetips.com/) is installed
	function_exists('quickkeywords') OR

	// if "Add Your Own Headers" Plugin (http://wp.uberdose.com/2007/03/30/add-your-own-headers/) is installed
	class_exists('Add_Your_Own_Headers') OR

	// if "SEO_Wordpress" Plugin (http://www.utheguru.com/seo_wordpress-wordpress-seo-plugin) is installed
	function_exists('SEO_wordpress') OR

	// if the option "Use Bytes For All SEO options?" is set to "No"
	$bfa_ata['use_bfa_seo'] == "No")

	{
	?>
	<title><?php wp_title('&laquo;', true, 'right'); ?><?php bloginfo('name'); ?></title>
	<?php
	} else { ?><title><?php

	if ( is_home() ) {

		bloginfo('name');

	} else {

		if ( is_single() OR is_page() ) {
			// post and page titles get their own filter from WP
			$bfa_meta_title = get_post_meta($post->ID, 'bfa_ata_meta_title', true);
			if ( $bfa_meta_title != '' ) {
				#$bfa_ata_page_title = $bfa_meta_title; 
				$bfa_ata_page_title = htmlentities($bfa_meta_title,ENT_QUOTES,'UTF-8');
			} else {
				$bfa_ata_page_title = single_post_title('', false);
			}
			
		} elseif ( is_category() ) {
			// cat titles don't get a filter, so htmlentities is required
			$bfa_ata_page_title = htmlentities(single_cat_title('', false),ENT_QUOTES,'UTF-8');

		} elseif ( function_exists('is_tag') AND is_tag() ) {
			// tag titles get their own filter from WP
			$bfa_ata_page_title = htmlentities(single_tag_title('', false),ENT_QUOTES,'UTF-8');

		} elseif ( is_search() ) {
			// no WP filter, htmlentities required
			$bfa_ata_page_title = htmlentities(wp_specialchars($s),ENT_QUOTES,'UTF-8');

		} elseif ( is_day() ) {
			$bfa_ata_page_title = htmlentities(get_the_time(__('l, F jS, Y','atahualpa')),ENT_QUOTES,'UTF-8');

		} elseif ( is_month() ) {
			$bfa_ata_page_title = htmlentities(get_the_time(__('F Y','atahualpa')),ENT_QUOTES,'UTF-8');

		} elseif ( is_year() ) {
			$bfa_ata_page_title = htmlentities(get_the_time('Y'),ENT_QUOTES,'UTF-8');
		}
	#	elseif ( is_author() ) { 
	#		$bfa_ata_page_title = htmlentities(the_author(),ENT_QUOTES); }   // this won't work

		elseif ( is_404() ) { 
			$bfa_ata_page_title = __('404 - Page not found','atahualpa');

		} else {
			$bfa_ata_page_title = htmlentities(wp_title('', false),ENT_QUOTES,'UTF-8');

		}

		switch ( $bfa_ata['title_separator_code'] ) {
			case 1: $bfa_ata_title_separator = " &#171; "; break;
			case 2: $bfa_ata_title_separator = " &#187; "; break;
			case 3: $bfa_ata_title_separator = " &#58; "; break;
			case 4: $bfa_ata_title_separator = "&#58; "; break;
			case 5: $bfa_ata_title_separator = " &#62; "; break;
			case 6: $bfa_ata_title_separator = " &#60; "; break;
			case 7: $bfa_ata_title_separator = " &#45; "; break;
			case 8: $bfa_ata_title_separator = " &#8249; "; break;
			case 9: $bfa_ata_title_separator = " &#8250; "; break;
			case 10: $bfa_ata_title_separator = " &#8226; "; break;
			case 11: $bfa_ata_title_separator = " &#183; "; break;
			case 12: $bfa_ata_title_separator = " &#151; "; break;
			case 13: $bfa_ata_title_separator = " &#124; "; 
		}

		/* 3 different styles for meta title tag: (1) Blog Title - Page Title,
		   (2) Page Title - Blog Title, (3) Page Title */

		if ( $bfa_ata['add_blogtitle'] == "Blog Title - Page Title" ) {
			bloginfo('name'); echo $bfa_ata_title_separator . $bfa_ata_page_title;

		} elseif ( $bfa_ata['add_blogtitle'] == "Page Title - Blog Title" ) {
			echo $bfa_ata_page_title . $bfa_ata_title_separator; bloginfo('name');

		} elseif ( $bfa_ata['add_blogtitle'] == "Page Title" ) {
		   echo $bfa_ata_page_title;

		}

	}

	?></title>
	<?php 

	// META DESCRIPTION & KEYWORDS for (only) the HOMEPAGE.
	if ( function_exists('is_front_page') ? is_front_page() : is_home() ) {
	#if ( is_home() ) {
		if ( trim($bfa_ata['homepage_meta_description'] ) != "" ) {
			echo "<meta name=\"description\" content=\"" .
			htmlentities($bfa_ata['homepage_meta_description'],ENT_QUOTES,'UTF-8') . "\" />\n";
		}
		if ( trim($bfa_ata['homepage_meta_keywords'] ) != "" ) {
			echo "<meta name=\"keywords\" content=\"" .
			htmlentities($bfa_ata['homepage_meta_keywords'],ENT_QUOTES,'UTF-8') . "\" />\n";
		}
	}

	// META DESCRIPTION & KEYWORDS Tag for single post pages and static pages:
	if ( is_single() OR is_page() ) {
		$bfa_meta_description = get_post_meta($post->ID, 'bfa_ata_meta_description', true);
		$bfa_meta_keywords = get_post_meta($post->ID, 'bfa_ata_meta_keywords', true);
		if ( $bfa_meta_description != '' ) {
			echo "<meta name=\"description\" content=\"" .
			htmlentities($bfa_meta_description,ENT_QUOTES,'UTF-8') . "\" />\n";
		}
		if ( $bfa_meta_keywords != '' ) {
			echo "<meta name=\"keywords\" content=\"" .
			htmlentities($bfa_meta_keywords,ENT_QUOTES,'UTF-8') . "\" />\n";
		}  
	}

	// META DESCRIPTION Tag for CATEGORY PAGES, if a category description exists:
	if ( is_category() AND strip_tags(trim(category_description())) != "" ) {
		/* the category description gets its own ASCII code filter from WP,
		   but <p> ... </p> tags will be included by WP, so we remove them here: */
		echo "<meta name=\"description\" content=\"" .
		htmlentities(strip_tags(trim(category_description())),ENT_QUOTES,'UTF-8') . "\" />\n";
	}


	/* prevent duplicate content by making archive pages noindex:
	   If it's a date, category or tag page: */
	if ( ($bfa_ata['archive_noindex'] == "Yes" AND is_date()) OR 
	($bfa_ata['cat_noindex'] == "Yes" AND is_category()) OR 
	($bfa_ata['tag_noindex'] == "Yes" AND is_tag()) ) {
		echo '<meta name="robots" content="noindex, follow" />'."\n";
	}
	}
	
	$bfametatags = ob_get_contents(); 
	ob_end_clean();
	
	echo $bfametatags;	
	return;
}
?>