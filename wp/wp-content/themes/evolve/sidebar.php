<?php
/**
 * Template: Sidebar.php
 *
 * @package EvoLve
 * @subpackage Template
 */
?>
        <!--BEGIN #secondary .aside-->
        <div id="secondary" class="aside">
        
      
      
  <!-- AD Space 3 -->
  
  
    <?php $options = get_option('evolve');
     if (!empty($options['evl_space_3'])) { 
    
 $ad_space_3 = $options['evl_space_3']; 
  echo '<div style="text-align:center;margin-bottom:25px;overflow:hidden;">'.stripslashes($ad_space_3).'</div>';
 
 } 
?> 
        
        
        
			<?php	/* Widgetized Area */
					if ( !dynamic_sidebar( 'sidebar-1' )) : ?>


           <!--BEGIN #widget-posts-->            
          
				<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Recent Posts', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<ul>
<?php $myposts = get_posts('numberposts=5'); // number of posts
foreach($myposts as $post) :?>
<li><a title="<?php the_title();?>" href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
<?php endforeach; ?>
				</ul>
            <?php evlwidget_after_widget(); ?>   
                <!--END #widget-posts-->
                
                
                 <!--BEGIN #widget-comments-->
        
			<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Recent Comments', 'evolve' ); ?><?php evlwidget_after_title(); ?>

          <?php
global $wpdb;
$output = '';
$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
comment_post_ID, comment_author, comment_date_gmt, comment_approved,
comment_type,comment_author_url,
SUBSTRING(comment_content,1,30) AS com_excerpt
FROM $wpdb->comments
LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
$wpdb->posts.ID)
WHERE comment_approved = '1' AND comment_type = '' AND
post_password = ''
ORDER BY comment_date_gmt DESC
LIMIT 5";    // number of comments
$comments = $wpdb->get_results($sql);
$output .= "\n<ul>";
foreach ($comments as $comment) {
$output .= "\n<li>".strip_tags($comment->comment_author)
." on " . "<a href=\"" . get_permalink($comment->ID) .
"#comment-" . $comment->comment_ID . "\">" . $comment->post_title
."</a></li>";
}
$output .= "\n</ul>";
echo $output;?>


            <?php evlwidget_after_widget(); ?>  
               <!--END #widget-comments-->
                

            <!--BEGIN #widget-calendar-->
        <?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Calendar', 'evolve' ); ?><?php evlwidget_after_title(); ?>
  
               
               <?php get_calendar(); ?>
               
                    <?php evlwidget_after_widget(); ?> 
               <!--END #widget-calendar-->
               
               
               			
<?php if ( get_tags() ) { ?>
            <!--BEGIN #widget-tags-->
        
			<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Tags', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<?php wp_tag_cloud( 'title_li=' ); ?>
                     <?php evlwidget_after_widget(); ?> 
                     <!--END #widget-tags-->
<?php } ?>


	<!--BEGIN #widget-archives-->
           
				<?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Archives', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<ul>
					<?php wp_get_archives( 'type=monthly' ) ?>
				</ul>
          <?php evlwidget_after_widget(); ?>   
            <!--END #widget-archives-->

               
                 <!--BEGIN #widget-meta-->
          
          <?php evlwidget_before_widget(); ?><?php evlwidget_before_title(); ?><?php _e( 'Meta', 'evolve' ); ?><?php evlwidget_after_title(); ?>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
</ul>
			<?php evlwidget_after_widget(); ?>
      			<!--END #widget-meta-->



			<?php endif; /* (!function_exists('dynamic_sidebar') */ ?>
		<!--END #secondary .aside-->
    
    
      <!-- AD Space 4 -->
  
  
    <?php $options = get_option('evolve');
     if (!empty($options['evl_space_4'])) { 
    
 $ad_space_4 = $options['evl_space_4']; 
 echo '<div style="text-align:center;margin-bottom:25px;overflow:hidden;">'.stripslashes($ad_space_4).'</div>';
 
 } 
?> 
    
    
		</div>  