<?php
/**
 * The tag archive template file
 *
 * @package Graphene
 * @since Graphene 1.1.5
 */
get_header();
?>

<h1 class="page-title archive-title">
    <?php
        printf(__('Tag Archive: <span>%s</span>', 'graphene'), single_tag_title('', false));
    ?>
</h1>
<?php
    /* Run the loop for the tag archive to output the posts
     * If you want to overload this in a child theme then include a file
     * called loop-tag.php and that will be used instead.
     */
    while ( have_posts() ) {
		the_post(); 
		get_template_part( 'loop', 'tag' );
	}
	
	/* Posts navigation. */ 
    graphene_posts_nav();
?>

<?php get_footer(); ?>