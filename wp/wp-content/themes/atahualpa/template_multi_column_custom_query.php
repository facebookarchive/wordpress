<?php
/*
Template Name: JUGGLEDAD's multi column/custom query
Version: 1.4

= 1.4 = 
* added option to control the number of full posts vrs excerpts to show
= 1.3 = 
* added even some more error checking
= 1.2 = 
* added some more error checking
= 1.1 =
* added error checking
= 1.0 = 
* original release

LICENSE:

   "JUGGLEDAD's multi column/custom query" is a template for the Atahualpa theme 
   Copyright (C) 2011 Paul M Woodard, The User's Guru (www.theusersguru.com)

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   For a copy of the GNU General Public License go to 'www.gnu.org/licenses'
   
*/
 ?>
 
 <?php 

list($bfa_ata, $cols, $left_col, $left_col2, $right_col, $right_col2, $bfa_ata['h_blogtitle'], $bfa_ata['h_posttitle']) = bfa_get_options();
global $bfa_pagetemplate_name, $bfa_pagetemplate_full_post_count, $bfa_ata_postcount;

get_header(); 
extract($bfa_ata); 

    $bfa_ata_postcount = 0;
	$mccq_page = new WP_Query('page_id=' . $post->ID); /* save the page id */
	$mccq_page->the_post();

	$number_of_columns = 1;
	$posts_above_the_column = 0;
	$mccq_args = 'post_status=publish';
	$show_page_title = 'no';
	$show_page_text = 'no';

	$custom_fields = get_post_custom($post->ID);
	$bfa_pagetemplate_name = 'template_multi_column_custom_query.php';
	$number_of_full_posts = 0;
// -------------------------------------------------------------------------
// get the custom query
// -------------------------------------------------------------------------
	$my_custom_field = $custom_fields['custom_query'];
	if (is_array($my_custom_field)) { 
		foreach ( $my_custom_field as $key => $value ) {
			if ($key == 'custom_query') {$mccq_args= $value;} 
		}
	}

// -------------------------------------------------------------------------
// get the show_page_title
// -------------------------------------------------------------------------
	$my_custom_field = $custom_fields['show_page_title'];
	if (is_array($my_custom_field)) { 
		foreach ( $my_custom_field as $key => $value ) {
			if ($key == 'show_page_title') {
				$show_page_title = strtolower($value);
				if (($show_page_title != 'yes') AND ($show_page_title != 'no')) { ?>
<br><strong><font color="Crimson">Warning:</font> your value for 'show_page_title' on page '<?php the_title(); ?>' must be 'yes' or 'no' - please edit the page and set the value correctly</strong><br>

				<?php }
			} 
		}
	}

// -------------------------------------------------------------------------
// get the number_of_full_posts
// -------------------------------------------------------------------------
	$my_custom_field = $custom_fields['number_of_full_posts'];
	if (is_array($my_custom_field)) { 
		foreach ( $my_custom_field as $key => $value ) {
			if ($key == 'number_of_full_posts') {
				$number_of_full_posts = strtolower($value);
				if ((!is_numeric($number_of_full_posts)) OR ($number_of_full_posts < 0)) { ?>
<br><strong><font color="Crimson">Warning:</font> your value for 'number_of_full_posts' on page '<?php the_title(); ?>' is not numeric or is a negative number - please edit the page and set the value correctly</strong><br>
				<?php }
			} 
		}
	}
	$bfa_pagetemplate_full_post_count = $number_of_full_posts;

// -------------------------------------------------------------------------
// get the show_page_text
// -------------------------------------------------------------------------
	$my_custom_field = $custom_fields['show_page_text'];
	if (is_array($my_custom_field)) { 
		foreach ( $my_custom_field as $key => $value ) {
			if ($key == 'show_page_text') {
				$show_page_text = strtolower($value);
				if (($show_page_text != 'yes') AND ($show_page_text != 'no')) { ?>
<br><strong><font color="Crimson">Warning:</font> your value for 'show_page_text'  on page '<?php the_title(); ?>' must be 'yes' or 'no' - please edit the page and set the value correctly</strong><br>
				<?php }
			} 
		}
	}

// -------------------------------------------------------------------------
// get the posts_above_the_column
// -------------------------------------------------------------------------
	$my_custom_field = $custom_fields['posts_above_the_column'];
	if (is_array($my_custom_field)) { 
		foreach ( $my_custom_field as $key => $value ) {
			if ($key == 'posts_above_the_column') {
				$posts_above_the_column = $value;
				if ((!is_numeric($posts_above_the_column)) OR ($posts_above_the_column < 1)) { ?>
<br><strong><font color="Crimson">Warning:</font> your value for 'posts_above_the_column' on page '<?php the_title(); ?>' is not numeric or is a negative number - please edit the page and set the value correctly</strong><br>
				<?php }
			} 
		}
	}
	
// -------------------------------------------------------------------------
// get the number_of_columns
// -------------------------------------------------------------------------
	$my_custom_field = $custom_fields['number_of_columns'];
	if (is_array($my_custom_field)) { 
		foreach ( $my_custom_field as $key => $value ) {
			if ($key == 'number_of_columns') {
				$number_of_columns = $value;
				if ((!is_numeric($number_of_columns)) OR ($number_of_columns < 1)) { ?>
<br><strong><font color="Crimson">Warning:</font> your value for 'number_of_columns' on page '<?php the_title(); ?>' is not numeric or is less than 1 - please edit the page and set the value correctly</strong><br>
				<?php }
				
			} 
		}
	}


//	wp_reset_query();
	$paged = get_query_var('paged');
	if (!$paged) {
		$paged = get_query_var('page');
		if (!$paged) {
			$paged = 0;
		}
	}
// -------------------------------------------------------------------------
// Should we show the page title?
// -------------------------------------------------------------------------
	if ($show_page_title == 'yes') {  
		 bfa_post_kicker('<div class="post-kicker">','</div>'); 
		 bfa_post_headline('<div class="post-headline">','</div>'); 
		 bfa_post_byline('<div class="post-byline">','</div>'); 
	}

// -------------------------------------------------------------------------
// Should we show the page text?
// -------------------------------------------------------------------------
	if ($show_page_text=='yes' ) {
		bfa_post_bodycopy('<div class="post-bodycopy clearfix">','</div>');
	}
	
	$mccq_args= $mccq_args."&paged=$paged";
?>

<?php 
/* =================================== 
   THIS SECTION WILL PROCESS THE POSTS 
   =================================== */
?>

<?php	query_posts($mccq_args); ?>

<?php /* If there are any posts: */
if (have_posts()) : $bfa_ata_postcount = 0; /* Postcount needed for option "XX first posts full posts, rest excerpts" */ ?>

	<?php // Deactivated since 3.6.5
	# include 'bfa://content_above_loop'; 
	// Uses the following static code instead: ?>
	<?php bfa_next_previous_page_links('Top'); // For MULTI post pages if activated at ATO -> Next/Previous Navigation:  ?>
	<?php if( is_category() AND function_exists('page2cat_output')) { page2cat_output($cat); } // For the plugin Page2Cat http://wordpress.org/extend/plugins/page2cat/ ?>

    <table cellpadding="0" cellspacing="0" border="0">
    <?php $column_cnt = 1; ?>
    
	<?php while (have_posts()) : the_post(); $bfa_ata_postcount++; ?>
	
  	<?php if ($column_cnt == 1) { echo "<tr>"; } 

        if ($bfa_ata_postcount <= $posts_above_the_column) { ?>
	   	     	<td id="mccq-header" colspan="<?php echo $number_of_columns;?>"> <?php 
			} else { ?>
        		<td class="mccq_column<?php echo $column_cnt;?>" style="vertical-align: top"> <?php 
        	} ?>

		<?php // Deactivated since 3.6.5
		#include 'bfa://content_inside_loop'; 
		// Uses the following static code instead: ?>
		<?php bfa_next_previous_post_links('Top'); // For SINGLE post pages if activated at ATO -> Next/Previous Navigation  ?>
		<?php /* Post Container starts here */
		if ( function_exists('post_class') ) { ?>
		<div <?php if ( is_page() ) { post_class('post'); } else { post_class(); } ?> id="post-<?php the_ID(); ?>">
		<?php } else { ?>
		<div class="<?php echo ( is_page() ? 'page ' : '' ) . 'post" id="post-'; the_ID(); ?>">
		<?php } ?>
		<?php bfa_post_kicker('<div class="post-kicker">','</div>'); ?>
		<?php bfa_post_headline('<div class="post-headline">','</div>'); ?>
		<?php bfa_post_byline('<div class="post-byline">','</div>'); ?>
		<?php bfa_post_bodycopy('<div class="post-bodycopy clearfix">','</div>'); ?>
		<?php bfa_post_pagination('<p class="post-pagination"><strong>'.__('Pages:','atahualpa').'</strong>','</p>'); ?>
		<?php bfa_post_footer('<div class="post-footer">','</div>'); ?>
		</div><!-- / Post -->	
						
        </td>
        <?php if ($bfa_ata_postcount <= $posts_above_the_column) { ?>
	   	     	</tr> <?php
	   	     	$column_cnt=0; 
			} ?>
       
        <?php $column_cnt++;
        if ($column_cnt > $number_of_columns) {
        	echo "</tr>";
            $column_cnt=1; 
        } ?>
        
	<?php endwhile; ?>

	</table>

	<?php // Deactivated since 3.6.5
	# include 'bfa://content_below_loop'; 
	// Uses the following static code instead: ?>
	<?php bfa_next_previous_post_links('Middle'); // Displayed on SINGLE post pages if activated at ATO -> Next/Previous Navigation: ?>
	<?php bfa_get_comments(); // Load Comments template (on single post pages, and static pages, if set on options page): ?>
	<?php bfa_next_previous_post_links('Bottom'); // Displayed on SINGLE post pages if activated at ATO -> Next/Previous Navigation: ?>
	<?php bfa_archives_page('<div class="archives-page">','</div>'); // Archives Pages. Displayed on a specific static page, if configured at ATO -> Archives Pages: ?>
	<?php bfa_next_previous_page_links('Bottom'); // Displayed on MULTI post pages if activated at ATO -> Next/Previous Navigation: ?>

<?php /* END of: If there are any posts */
else : /* If there are no posts: */ ?>

<?php // Deactivated since 3.6.5
#include 'bfa://content_not_found'; 
// Uses the following static code instead: ?>
<h2><?php _e('Not Found','atahualpa'); ?></h2>
<p><?php _e("Sorry, but you are looking for something that isn't here.","atahualpa"); ?></p>

<?php endif; /* END of: If there are no posts */ ?>
<?php $wp_query = $mccq_page;  /* reset the page id */ ?>

<?php get_footer(); ?>