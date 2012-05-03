<div class="post clearfix post_404">
    <div class="entry clearfix">
        <h2><?php _e( 'Error 404 - Page Not Found', 'graphene' ); ?></h2>
        <div class="entry-content clearfix">
            <p><?php _e( "Sorry, I've looked everywhere but I can't find the page you're looking for.", 'graphene' ); ?></p>
            <p><?php _e( "If you follow the link from another website, I may have removed or renamed the page some time ago. You may want to try searching for the page:", 'graphene' ); ?></p>
            
            <?php get_search_form(); ?>
        </div>
    </div>
</div>
<div class="post clearfix post_404_search">
	<div class="entry clearfix"> 
	<h2><?php _e( 'Suggested results', 'graphene' ); ?></h2>   
        <div class="entry-content clearfix">
        <p>
        <?php /* translators: %s is the search term */ ?>
        <?php printf( __( "I've done a courtesy search for the term %s for you. See if you can find what you're looking for in the list below.", 'graphene' ), '<code>' . get_search_query() . '</code>' ); ?>
        </p>
        <?php if ( have_posts() ) : ?>    
            <ul class="search-404-results">
            <?php while ( have_posts() ) : the_post(); ?>
                <li class="clearfix">
                    <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink Link to %s', 'graphene' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a></h3>
                     <?php the_excerpt(); ?>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>
	<?php /* Posts navigation. See functions.php for the function definition */ ?>
    <?php graphene_posts_nav(); ?>
<?php else : ?>
			<p><?php _e("<strong>Sorry, couldn't find anything.</strong> Try searching for alternative terms using the search form above.", 'graphene' ); ?></p>
    	</div>
    </div>
</div>
<?php endif; ?>