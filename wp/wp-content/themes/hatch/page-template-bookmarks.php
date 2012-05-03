<?php
/**
 * Template Name: Bookmarks
 *
 * A custom page template for displaying the site's bookmarks/links.
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

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // hatch_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // hatch_open_entry ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'hatch' ) ); ?>

							<?php $args = array(
								'title_li' => false,
								'title_before' => '<h2>',
								'title_after' => '</h2>',
								'category_before' => false,
								'category_after' => false,
								'categorize' => true,
								'show_description' => true,
								'between' => '<br />',
								'show_images' => false,
								'show_rating' => false,
							); ?>
							<?php wp_list_bookmarks( $args ); ?>

							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'hatch' ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->

						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>

						<?php do_atomic( 'close_entry' ); // hatch_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // hatch_after_entry ?>

					<?php do_atomic( 'after_singular' ); // hatch_after_singular ?>

				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // hatch_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // hatch_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>