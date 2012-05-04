<?php
/**
 * Template: Sidebar.php
 *
 * @package EvoLve
 * @subpackage Template
 */
?>
        <!--BEGIN #secondary-2 .aside-->
        <div id="secondary-2" class="aside">
        
        
        
          <!-- AD Space 5 -->
  
  
    <?php $options = get_option('evolve');
     if (!empty($options['evl_space_5'])) { 
    
 $ad_space_5 = $options['evl_space_5']; 
 echo '<div style="text-align:center;margin-bottom:25px;overflow:hidden;">'.stripslashes($ad_space_5).'</div>';
 
 } 
?> 
        
        
        
			<?php	/* Widgetized Area */
					if ( !dynamic_sidebar( 'sidebar-2' )) : ?>



     <!--BEGIN #widget-pages-->
            
				<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Pages', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<ul>
					<?php wp_list_pages('title_li='); ?> 
				</ul>
          <?php evlwidget_after_widget(); ?> 
            <!--END #widget-pages-->
			

                         <!--BEGIN #widget-categories-->
          
				<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Categories', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<ul>
					<?php wp_list_categories( 'title_li=' ); ?>
				</ul>
                    <?php evlwidget_after_widget(); ?>    
                        <!--END #widget-categories-->
 
 
                            <!--BEGIN #widget-feeds-->
         
				<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'RSS Syndication', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<ul>
					<li><a href="<?php bloginfo( 'rss2_url' ); ?>" title="<?php echo esc_html( get_bloginfo( 'name' ), 1 ) ?> Posts RSS feed" rel="alternate" type="application/rss+xml"><?php _e( 'All posts', 'evolve' ); ?></a></li>
					<li><a href="<?php bloginfo( 'comments_rss2_url' ); ?>" title="<?php echo esc_html( bloginfo( 'name' ), 1 ) ?> Comments RSS feed" rel="alternate" type="application/rss+xml"><?php _e( 'All comments', 'evolve' ); ?></a></li>
				</ul>
                <?php evlwidget_after_widget(); ?> 
                     <!--END #widget-feeds-->

          
			<?php endif; /* (!function_exists('dynamic_sidebar') */ ?>
		<!--END #secondary-2 .aside-->
    
    
     <!-- AD Space 6 -->
  
  
    <?php $options = get_option('evolve');
     if (!empty($options['evl_space_6'])) { 
    
 $ad_space_6 = $options['evl_space_6']; 
 echo '<div style="text-align:center;margin-bottom:25px;overflow:hidden;">'.stripslashes($ad_space_6).'</div>';
 
 } 
?> 
    
    
		</div>
    
