<?php
/**
 * Template Name: Three columns, sidebars on the right
 *
 * A custom page template with the main content on 
 * the left side and one sidebar on the right side.
 *
 * @package Graphene
 * @since Graphene 1.1.5
 */
 get_header(); ?>
 
    <?php
    /* Run the loop to output the pages.
	 * If you want to overload this in a child theme then include a file
	 * called loop-page.php and that will be used instead.
	*/
	the_post();
    get_template_part( 'loop', 'page' );
    ?>

<?php get_footer(); ?>