	<?php $options = get_option('evolve'); if (is_home()) { 
  $settings = $options['evl_home_header_content'];
        } elseif (is_single()) {
     $settings = $options['evl_single_header_content'];   
        } else {
         $settings = $options['evl_archives_header_content']; } ?>
  
  
  <div class="container" style="margin:0 auto;">   



 <?php $options = get_option('evolve');
  if ($options['evl_header_slider'] == "disable" || $options['evl_header_slider'] == "") { 
  
   $number_items = 1;
  
   } else { $number_items = 10; } ?>  
  
 
        <?php 
  if ($settings == "search_social" || $settings == "") { ?>
  
  <div style="float:left;width:620px;height:50px;margin-top:12px;">
  
  <!--BEGIN #subscribe-follow-->
 
<span class="social-title"><?php _e( 'Follow', 'evolve' ); ?></span>

<?php get_template_part('social-buttons', 'header'); ?>


<!--END #subscribe-follow-->
  
  
</div>  
  
   <!--BEGIN #righttopcolumn-->  
  <div id="righttopcolumn"> 
       
<?php get_search_form(); ?> 

</div> 
  <!--END #righttopcolumn-->


  
  <?php } elseif ($settings == "post_search_social") { ?>
  
   
  
  
  
  
  
      
       
         <div id="slide_holder">
         
         

  	<div class="slide-container">
    
    
  	

	
		<ul class="slides">
		
    <?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args=array(
   'cat'=>-5981,
   'showposts'=>$number_items,
   'post__not_in' =>get_option("sticky_posts"),
   );
query_posts($args);
?>


<?php if (have_posts()) : $featured = new WP_Query($args); while($featured->have_posts()) : $featured->the_post(); ?>

<li class="slide"><div class="featured-title">
     

<a class="title" href="<?php the_permalink() ?>">
<?php
$title = the_title('', '', false);
echo evltruncate($title, 40, '...');
 ?></a> 
 
 
 </div>
 
 <?php  
           
          
if(has_post_thumbnail()) {
	echo '<a href="'; the_permalink(); echo '">';the_post_thumbnail(array(80,80)); echo '</a>';
  
     } else {

                      $image = evlget_first_image(); 
                      if ($image):
                      echo '<a href="'; the_permalink(); echo'"><img src="'.$image.'" alt="';the_title();echo'" /></a>';
                      endif;
               } ?>


<p>
<?php $postexcerpt = get_the_content();
$postexcerpt = apply_filters('the_content', $postexcerpt);
$postexcerpt = str_replace(']]>', ']]&gt;', $postexcerpt);
$postexcerpt = strip_tags($postexcerpt);
$postexcerpt = strip_shortcodes($postexcerpt);

echo evltruncate($postexcerpt, 180, ' [...]');
 ?>
 
</p> 
 
 
 





<a class="post-more" href="<?php the_permalink(); ?>"><?php _e( 'Read more &raquo;', 'evolve' ); ?></a>


</li>       




 
<?php endwhile; ?> 


<?php else: ?>

<li>
<?php _e( 'Oops, please try to refresh the page', 'evolve' ); ?>
</li>

<?php endif; ?>
   
   
<?php wp_reset_query(); ?>
   
   
   </ul>

      
       </div>  </div>
       

 <!--BEGIN #righttopcolumn-->  
  <div id="righttopcolumn"> 

       
       
 <!--BEGIN #searchform--> 
 
 
 <?php get_search_form(); ?>     
   
  


    
<!--END #searchform-->


<!--BEGIN #subscribe-follow-->


<span class="social-title" style="margin:20px 0 10px 0;"><?php _e( 'Follow', 'evolve' ); ?></span> <br style="clear:both;" />



<?php get_template_part('social-buttons', 'header'); ?>




<!--END #subscribe-follow-->



 <!--END #righttopcolumn-->  
  </div> 
  



<?php } else { ?>
   
   
          
       
       <?php } ?>
       
    
       
       
       </div>