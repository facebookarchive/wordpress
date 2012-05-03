<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Graphene
 * @since Graphene 1.0
 */
global $graphene_settings;
get_header(); ?>

	<?php
	
	/* Check if the user selects specific category for the front page */
	if ( is_home() && $graphene_settings['frontpage_posts_cats'] && ! in_array( 'disabled', $graphene_settings['frontpage_posts_cats'] ) ) {
		global $wp_query;
		$cats = $graphene_settings['frontpage_posts_cats'];
		
		// Display the sticky posts first
		$stickies = get_option('sticky_posts');
		if ( get_query_var( 'paged' ) < 2 && ( $stickies ) ) {
			$args = array( 'category__in' => $cats, 'post__in' => $stickies );
			query_posts( apply_filters( 'graphene_frontpage_posts_cats_sticky_args', $args ) );
			$wp_query->is_home = true;
			$wp_query->is_category = false;
			$wp_query->is_archive = false;
		
			if ( have_posts() ){
				get_template_part( 'loop', 'index' );
			}
			
			wp_reset_query();
		}
		
		// Display the rest of the posts from the category
		$args = array( 'category__in' => $cats, 'paged' => get_query_var( 'paged' ), 'post__not_in' => $stickies );
		query_posts( apply_filters( 'graphene_frontpage_posts_cats_args', $args ) );
		$wp_query->is_home = true;
		$wp_query->is_category = false;
		$wp_query->is_archive = false;
	}
	
	do_action('graphene_index_pre_loop');
	
    /* Run the loop to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-index.php and that will be used instead.
	*/
	if ( ! ( is_home() && $graphene_settings['frontpage_posts_cats'] && ! have_posts() ) ) {
		while ( have_posts() ) {
			the_post(); 
		  	get_template_part( 'loop', 'index' );
	   	}
	}
	
	/* Posts navigation. */ 
    graphene_posts_nav();
    ?>
            
<?php get_footer(); ?>