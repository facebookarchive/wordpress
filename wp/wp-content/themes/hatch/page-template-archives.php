<?php
/**
 * Template Name: Archives
 *
 * A custom page template for displaying blog archives.
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

							<h2><?php _e( 'Archives by category', 'hatch' ); ?></h2>

							<ul class="xoxo category-archives">
								<?php wp_list_categories( array( 'feed' => __( 'RSS', 'hatch' ), 'show_count' => true, 'use_desc_for_title' => false, 'title_li' => false ) ); ?>
							</ul><!-- .xoxo .category-archives -->

							<h2><?php _e( 'Archives by month', 'hatch' ); ?></h2>

							<ul class="xoxo monthly-archives">
								<?php wp_get_archives( array( 'show_post_count' => true, 'type' => 'monthly' ) ); ?>
							</ul><!-- .xoxo .monthly-archives -->

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