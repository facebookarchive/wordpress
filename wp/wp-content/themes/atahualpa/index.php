<?php 
list($bfa_ata, $cols, $left_col, $left_col2, $right_col, $right_col2, $bfa_ata['h_blogtitle'], $bfa_ata['h_posttitle']) = bfa_get_options();
get_header(); 
extract($bfa_ata); 
global $bfa_ata_postcount;
?>

<?php /* If there are any posts: */
if (have_posts()) : $bfa_ata_postcount = 0; /* Postcount needed for option "XX first posts full posts, rest excerpts" */ ?>

	<?php // Deactivated since 3.6.5
	# include 'bfa://content_above_loop'; 
	// Uses the following static code instead: ?>
	<?php bfa_next_previous_page_links('Top'); // For MULTI post pages if activated at ATO -> Next/Previous Navigation:  ?>

	<?php while (have_posts()) : the_post(); $bfa_ata_postcount++; ?>
	
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
		<?php bfa_archives_page('<div class="archives-page">','</div>'); // Archives Pages. Displayed on a specific static page, if configured at ATO -> Archives Pages: ?>
		<?php bfa_post_footer('<div class="post-footer">','</div>'); ?>
		</div><!-- / Post -->	
						
	<?php endwhile; ?>

	<?php // Deactivated since 3.6.5
	# include 'bfa://content_below_loop'; 
	// Uses the following static code instead: ?>
	<?php bfa_next_previous_post_links('Middle'); // Displayed on SINGLE post pages if activated at ATO -> Next/Previous Navigation: ?>
	<?php bfa_get_comments(); // Load Comments template (on single post pages, and static pages, if set on options page): ?>
	<?php bfa_next_previous_post_links('Bottom'); // Displayed on SINGLE post pages if activated at ATO -> Next/Previous Navigation: ?>
	<?php bfa_next_previous_page_links('Bottom'); // Displayed on MULTI post pages if activated at ATO -> Next/Previous Navigation: ?>

<?php /* END of: If there are any posts */
else : /* If there are no posts: */ ?>

<?php // Deactivated since 3.6.5
#include 'bfa://content_not_found'; 
// Uses the following static code instead: ?>
<h2><?php _e('Not Found','atahualpa'); ?></h2>
<p><?php _e("Sorry, but you are looking for something that isn't here.","atahualpa"); ?></p>

<?php endif; /* END of: If there are no posts */ ?>

<?php get_footer(); ?>