<?php 
/**
 * Adds the content panes in the homepage. The homepage panes are only displayed if using a static
 * front page, before the comments. It is also recommended that the comments section is disabled 
 * for the page used as the static front page.
*/
function graphene_homepage_panes(){
	global $graphene_settings, $graphene_defaults;
	
	// Get the number of panes to display
	if ( $graphene_settings['show_post_type'] == 'latest-posts' || $graphene_settings['show_post_type'] == 'cat-latest-posts' ){
		$pane_count = $graphene_settings['homepage_panes_count'];
	} elseif ( $graphene_settings['show_post_type'] == 'posts' ) {
		$pane_count = count(explode( ',', $graphene_settings['homepage_panes_posts']) );
	}
	
	// Build the common WP_Query() parameter first
	$args = array( 
				  'orderby' 			=> 'date',
				  'order' 				=> 'DESC',
				  'post_type' 			=> array( 'post', 'page' ),
				  'posts_per_page'		=> $pane_count,
				  'ignore_sticky_posts' => 1,
				 );
	
	// args specific to latest posts
	if ( $graphene_settings['show_post_type'] == 'latest-posts' ){
		$args_merge = array(
							'post_type' => array( 'post' ),
							);
		$args = array_merge( $args, $args_merge );
	}
	
	// args specific to latest posts by category
	if ($graphene_settings['show_post_type'] == 'cat-latest-posts' ){
		$args_merge = array(
							'category__in' => $graphene_settings['homepage_panes_cat'],
							);
		$args = array_merge( $args, $args_merge );
	}
	
	// args specific to posts/pages
	if ( $graphene_settings['show_post_type'] == 'posts' ){
		
         $post_ids = $graphene_settings['homepage_panes_posts'];
         $post_ids = preg_split("/[\s]*[,][\s]*/", $post_ids, -1, PREG_SPLIT_NO_EMPTY); // post_ids are comma seperated, the query needs a array                        
          
		$args_merge = array(	
							'post__in' => $post_ids,
							);
		$args = array_merge( $args, $args_merge );
	}
	
	// Get the posts to display as homepage panes
	$panes = new WP_Query( apply_filters( 'graphene_homepage_panes_args', $args ) );
	
	$count = 0;
	?>
    
    <?php do_action( 'graphene_before_homepage_panes' ); ?>
    
    <div class="homepage_panes">
	
	<?php while ( $panes->have_posts() ) : $panes->the_post(); 
		$count++;
		$alpha = $omega = false;
		if ( $count % 2 ){
			$alpha = true;
		} else {
			$omega = true;
		}
	?>
		<div <?php graphene_grid( 'homepage_pane clearfix', 8, 5, 4, $alpha, $omega ); ?> id="homepage-pane-<?php the_ID(); ?>">
        	<?php do_action( 'graphene_homepage_pane_top' ); ?>
        
        	<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'graphene' ), esc_attr( get_the_title() ) ); ?>">
        	<?php /* Get the post's image */ 
			if ( has_post_thumbnail( get_the_ID() ) ) {
				the_post_thumbnail( 'graphene-homepage-pane' );
			} else {
				echo graphene_get_post_image( get_the_ID(), 'graphene-homepage-pane', 'excerpt' );
			}
			?>
            </a>
            
            <?php /* The post title */ ?>
            <h3 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'graphene' ), esc_attr( get_the_title() ) ); ?>"><?php the_title(); ?></a></h3>
            
            <?php /* The post excerpt */ ?>
            <div class="post-excerpt">
            	<?php 
					the_excerpt();
					
					do_action( 'graphene_homepage_pane_content' );
				?>
            </div>
            
            <?php /* Read more button */ ?>
            <p class="post-comments">
            	<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'graphene' ), esc_attr( get_the_title() ) ); ?>" class="block-button"><?php _e( 'Read more', 'graphene' ); ?></a>
            </p>
            
            <?php do_action( 'graphene_homepage_pane_bottom' ); ?>
            
        </div>
    <?php endwhile; wp_reset_postdata(); ?>
	</div>
	
	<?php
	do_action( 'graphene_after_homepage_panes' );
}

/* Helper function to control when the homepage panes should be displayed. */
function graphene_display_homepage_panes(){
	global $graphene_settings;
	if ( get_option( 'show_on_front' ) == 'page' && ! $graphene_settings['disable_homepage_panes'] && is_front_page() ) {
		graphene_homepage_panes();
	}	
}
add_action( 'graphene_bottom_content', 'graphene_display_homepage_panes' );
?>