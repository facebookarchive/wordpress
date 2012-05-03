<?php
/**
 * Loop Nav Template
 *
 * This template is used to show your your next/previous post links on singular pages and
 * the next/previous posts links on the home/posts page and archive pages.
 *
 * @package Hatch
 * @subpackage Template
 */
?>

	<?php if ( is_attachment() ) : ?>

		<div class="loop-nav">
			<?php previous_post_link( '%link', '<span class="previous">' . __( '&larr; Return to entry', 'hatch' ) . '</span>' ); ?>
		</div><!-- .loop-nav -->

	<?php elseif ( is_singular( 'post' ) ) : ?>

		<div class="loop-nav">
			<?php previous_post_link( '<div class="previous">' . __( '%link', 'hatch' ) . '</div>', '&larr; %title' ); ?>
			<?php next_post_link( '<div class="next">' . __( '%link', 'hatch' ) . '</div>', '%title &rarr;' ); ?>
		</div><!-- .loop-nav -->

	<?php elseif ( !is_singular() && current_theme_supports( 'loop-pagination' ) ) : loop_pagination(); ?>

	<?php elseif ( !is_singular() && $nav = get_posts_nav_link( array( 'sep' => '', 'prelabel' => '<span class="previous">' . __( '&larr; Previous', 'hatch' ) . '</span>', 'nxtlabel' => '<span class="next">' . __( 'Next &rarr;', 'hatch' ) . '</span>' ) ) ) : ?>

		<div class="loop-nav">
			<?php echo $nav; ?>
		</div><!-- .loop-nav -->

	<?php endif; ?>