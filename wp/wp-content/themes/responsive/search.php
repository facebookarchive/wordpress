<?php
/**
 * Search Template
 *
 *
 * @file           search.php
 * @package        Responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2012 ThemeID
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/search.php
 * @link           http://codex.wordpress.org/Theme_Development#Search_Results_.28search.php.29
 * @since          available since Release 1.0
 */
?>
<?php get_header(); ?>

        <div id="content" class="grid col-620">
            <h6><?php _e('We found','responsive'); ?> 
			<?php
                $allsearch = &new WP_Query("s=$s&showposts=-1");
                $key = esc_html($s, 1);
                $count = $allsearch->post_count;
                _e(' &#8211; ', 'responsive');
                echo $count . ' ';
                _e('results for ', 'responsive');
                _e('<span class="post-search-terms">', 'responsive');
                echo $key;
                _e('</span><!-- end of .post-search-terms -->', 'responsive');
                wp_reset_query();
            ?>
            </h6>


<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
        
        <?php $options = get_option('responsive_theme_options'); ?>
		<?php if ($options['breadcrumb'] == 0): ?>
		<?php echo responsive_breadcrumb_lists(); ?>
        <?php endif; ?> 
          
            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'responsive'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h1>

                <div class="post-meta">
                <?php 
                    printf( __( '<span class="%1$s">Posted on</span> %2$s by %3$s', 'responsive' ),'meta-prep meta-prep-author',
		            sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
			            get_permalink(),
			            esc_attr( get_the_time() ),
			            get_the_date()
		            ),
		            sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			            get_author_posts_url( get_the_author_meta( 'ID' ) ),
			        sprintf( esc_attr__( 'View all posts by %s', 'responsive' ), get_the_author() ),
			            get_the_author()
		                )
			        );
		        ?>
				    <?php if ( comments_open() ) : ?>
                        <span class="comments-link">
                        <span class="mdash">&mdash;</span>
                    <?php comments_popup_link(__('No Comments &darr;', 'responsive'), __('1 Comment &darr;', 'responsive'), __('% Comments &darr;', 'responsive')); ?>
                        </span>
                    <?php endif; ?> 
                </div><!-- end of .post-meta -->
                                
                <div class="post-entry">
                    <?php the_content(__('Read more &#8250;', 'responsive')); ?>
                    
                    <?php if ( get_the_author_meta('description') != '' ) : ?>
                    
                    <div id="author-meta">
                    <?php if (function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '80' ); }?>
                        <div class="about-author"><?php _e('About','responsive'); ?> <?php the_author_posts_link(); ?></div>
                        <p><?php the_author_meta('description') ?></p>
                    </div><!-- end of #author-meta -->
                    
                    <?php endif; // no description, no author's meta ?>
                    
                    <?php wp_link_pages(array('before' => '<div class="pagination">' . __('Pages:', 'responsive'), 'after' => '</div>')); ?>
                </div><!-- end of .post-entry -->
                
                <div class="post-data">
				    <?php the_tags(__('Tagged with:', 'responsive') . ' ', ', ', '<br />'); ?> 
					<?php printf(__('Posted in %s', 'responsive'), get_the_category_list(', ')); ?> 
                </div><!-- end of .post-data -->             

            <div class="post-edit"><?php edit_post_link(__('Edit', 'responsive')); ?></div>             
            </div><!-- end of #post-<?php the_ID(); ?> -->
            
			<?php comments_template( '', true ); ?>
            
        <?php endwhile; ?> 

        <?php if (  $wp_query->max_num_pages > 1 ) : ?>
        <div class="navigation">
			<div class="previous"><?php next_posts_link( __( '&#8249; Older posts', 'responsive' ) ); ?></div>
            <div class="next"><?php previous_posts_link( __( 'Newer posts &#8250;', 'responsive' ) ); ?></div>
		</div><!-- end of .navigation -->
        <?php endif; ?>

	    <?php else : ?>

        <h1 class="title-404"><?php _e('404 &#8212; Fancy meeting you here!', 'responsive'); ?></h1>
        <p><?php _e('Don&#39;t panic, we&#39;ll get through this together. Let&#39;s explore our options here.', 'responsive'); ?></p>
        <h6><?php _e( 'You can return', 'responsive' ); ?> <a href="<?php echo home_url(); ?>/" title="<?php esc_attr_e( 'Home', 'responsive' ); ?>"><?php _e( '&#9166; Home', 'responsive' ); ?></a> <?php _e( 'or search for the page you were looking for', 'responsive' ); ?></h6>
        <?php get_search_form(); ?>

<?php endif; ?>  
      
        </div><!-- end of #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>