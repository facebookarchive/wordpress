<?php
/**
 * The archive template file
 *
 * @package Graphene
 * @since Graphene 1.1.5
 */
get_header();
?>

<?php
/* Queue the first post, that way we know
 * what date we're dealing with (if that is the case).
 *
 * We reset this later so we can run the loop
 * properly with a call to rewind_posts().
 */
if ( have_posts() )
    the_post();
?>

<h1 class="page-title archive-title">
    <?php if ( is_day() ) : ?>
        <?php printf( __( 'Daily Archive: %s', 'graphene' ), '<span>' . get_the_date() . '</span>' ); ?>
    <?php elseif ( is_month() ) : ?>
        <?php printf( __( 'Monthly Archive: %s', 'graphene' ), 
		/* translators: F will be replaced with month, and Y will be replaced with year, so "F Y" in English would be replaced with something like "June 2008". */
		'<span>' . get_the_date( __( 'F Y', 'graphene' ) ) . '</span>' ); ?>
    <?php elseif ( is_year() ) : ?>
        <?php printf(__( 'Yearly Archive: %s', 'graphene' ), '<span>' . get_the_date( 'Y' ) . '</span>' ); ?>
    <?php elseif ( is_tax() ) : 
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$term_title = $term->name;
		
		$taxonomy = $term->taxonomy;
		$taxonomy = get_taxonomy( $taxonomy );
		$taxonomy = $taxonomy->labels->singular_name;
	
		printf( __('%1$s Archive: <span>%2$s</span>', 'graphene'), $taxonomy, $term_title ); 
	
	else : ?>
        <?php _e( 'Blog Archive', 'graphene' ); ?>
    <?php endif; ?>
</h1>
<?php
    /* Since we called the_post() above, we need to
     * rewind the loop back to the beginning that way
     * we can run the loop properly, in full.
     */
    rewind_posts();

    /* Run the loop for the archives page to output the posts.
     * If you want to overload this in a child theme then include a file
     * called loop-archives.php and that will be used instead.
     */
    while ( have_posts() ) {
		the_post(); 
		get_template_part( 'loop', 'archive' );
	}
	
	/* Posts navigation. */ 
    graphene_posts_nav();
?>

<?php get_footer(); ?>