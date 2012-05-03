<?php
/**
 * Index Template
 *
 * This is the default template.  It is used when a more specific template can't be found to display
 * posts.  It is unlikely that this template will ever be used, but there may be rare cases.
 *
 * @package Hatch
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // hatch_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // hatch_open_content ?>

		<div class="hfeed">

			<?php if ( have_posts() ) : ?>
			
				<?php $counter = 1; ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // hatch_before_entry ?>
					
					<?php if ( ( $counter % 4 ) == 0 ) { ?>
					
						<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?> last">
						
					<?php } else { ?>

						<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
						
					<?php } ?>

							<?php do_atomic( 'open_entry' ); // hatch_open_entry ?>

							<?php if ( current_theme_supports( 'get-the-image' ) ) {
										
										get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'archive-thumbnail', 'image_class' => 'featured', 'width' => 220, 'height' => 150, 'default_image' => get_template_directory_uri() . '/images/archive_image_placeholder.png' ) );
										
								} ?>					
										
								<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

							<?php do_atomic( 'close_entry' ); // hatch_close_entry ?>

						</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // hatch_after_entry ?>
					
					<?php $counter++; ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // hatch_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // hatch_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>