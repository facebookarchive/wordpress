<?php
/**
 * Post Template
 *
 * This is the default post template.  It is used when a more specific template can't be found to display
 * singular views of the 'post' post type.
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
						
						<div class="post-content">
							
							<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'single-thumbnail', 'link_to_post' => false, 'image_class' => 'featured', 'attachment' => false, 'width' => 640, 'height' => 360, 'default_image' => get_template_directory_uri() . '/images/single_image_placeholder.png' ) ); ?>													
							<div class="post-aside">								
							
								<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
								
								<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-published before="Date: "]', 'hatch' ) . '</div>' ); ?>
								
								<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-author before="Author: "]', 'hatch' ) . '</div>' ); ?>
								
								<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( 'Category: [entry-terms taxonomy="category"]', 'hatch' ) . '</div>' ); ?>
								
								<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">' . __( '[entry-terms taxonomy="post_tag" before="Tags: "]', 'hatch' ) . '</div>' ); ?>
														
								<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-edit-link]', 'hatch' ) . '</div>' ); ?>
								
								<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>
								
							</div>
						
							<div class="entry-content">
								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'hatch' ) ); ?>
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'hatch' ), 'after' => '</p>' ) ); ?>
							</div><!-- .entry-content -->

							<?php do_atomic( 'close_entry' ); // hatch_close_entry ?>
						
						</div><!-- .post-content -->

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // hatch_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // hatch_after_singular ?>

					<?php comments_template( '/comments.php', true ); // Loads the comments.php template. ?>

				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // hatch_close_content ?>

		

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // hatch_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>