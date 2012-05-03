<?php
/**
 * Search Template
 *
 * The search template is loaded when a visitor uses the search form to search for something
 * on the site.
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
					
					<?php if ( ( $counter % 2 ) == 0 ) { ?>
					
						<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?> last">
						
					<?php } else { ?>

						<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
						
					<?php } ?>

							<?php do_atomic( 'open_entry' ); // hatch_open_entry ?>

							<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

							<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-published] [entry-author]', 'hatch' ) . '</div>' ); ?>

							<div class="entry-summary">
								<?php the_excerpt(); ?>
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'hatch' ), 'after' => '</p>' ) ); ?>
							</div><!-- .entry-summary -->

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