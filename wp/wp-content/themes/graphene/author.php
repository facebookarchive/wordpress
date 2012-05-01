<?php
/**
 * The author template file.
 *
 * @package Graphene
 * @since Graphene 1.0
 */

get_header(); ?>

<?php 
	/* Queue the first post, that way we know who
	 * the author is when we try to get their name,
	 * URL, description, avatar, etc.
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	if ( have_posts() ) { the_post(); }
?>

	<?php
    /* Run the loop to output the posts.
     * If you want to overload this in a child theme then include a file
     * called loop-index.php and that will be used instead.
     */
     get_template_part( 'loop', 'author' );
    ?>
    
    <?php do_action( 'graphene_before_authorpostlist' ); ?>
    
    <h3 class="author-post-list"><?php _e("Author's posts listings", 'graphene'); ?></h3>
    <?php 
	/* Start the loop again to list all of the author's posts with excerpt */
	rewind_posts();
	
	while ( have_posts() ) {
		the_post();
		get_template_part( 'loop', 'archive' );
	}
	
	/* Posts navigation. */ 
    graphene_posts_nav();
	?>
    
    <?php do_action('graphene_after_authorpostlist'); ?>
            
<?php get_footer(); ?>