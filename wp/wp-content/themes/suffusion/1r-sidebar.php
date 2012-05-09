<?php
/**
 * Template Name: 1 Right Sidebar (Deprecated)
 *
 * @package Suffusion
 * @subpackage Templates
 */

get_header();
?>

<div id="main-col">
<?php
suffusion_page_navigation();
suffusion_before_begin_content();
?>
	<div id="content">
<?php
global $post;
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$original_post = $post;
?>
		<article <?php post_class('fix'); ?> id="post-<?php the_ID(); ?>">
			<?php suffusion_after_begin_post(); ?>
			<div class="entry-container fix">
				<div class="entry fix">
					<?php suffusion_content(); ?>
				</div><!--/entry -->
			<?php
				// Due to the inclusion of Ad Hoc Widgets the global variable $post might have got changed. We will reset it to the original value.
				$post = $original_post;
				suffusion_after_content();
			?>
			</div><!-- .entry-container -->
			<?php suffusion_before_end_post(); ?>

			<?php comments_template(); ?>
		</article><!--/post -->
<?php
	}
}
?>
	</div><!-- #content -->
</div><!-- #main-col -->
<?php get_footer(); ?>
