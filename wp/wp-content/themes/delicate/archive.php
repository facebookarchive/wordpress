<?php get_header();?>      

<?php 
$t_show_post = t_get_option( "t_show_post" );		
?>    
<div id="main">		
	<div class="columns">
     <div class="narrowcolumn">
     <?php if (have_posts()) : ?>
     <?php while (have_posts()) : the_post(); ?>							
			<div <?php post_class();?>>
            	
                <div class="title">
				<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                <small><?php the_time('F jS, Y') ?> | <?php _e('Posted by','nattywp'); ?> <span class="author"><?php natty_get_profile() ?></span> <?php _e('in','nattywp'); ?> <?php the_category(' | ');?> - (<?php comments_popup_link(__('0 Comments', 'nattywp'), __('1 Comments', 'nattywp'), __('% Comments', 'nattywp')); ?>)</small> <?php edit_post_link(__('Edit','nattywp'), ' | ', ''); ?>
                </div>              
				<div class="entry">
          <?php 
                  if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
                       the_post_thumbnail('thumbnail');} 
                  if ($t_show_post == 'no') {//excerpt  
                         the_excerpt();  
                  } else { //fullpost 
                        t_show_video($post->ID);
                        the_content();                    
                  } ?>
           <div class="clear"></div>
        </div>              
                
				<p class="postmetadata">
           <span class="category"><?php the_tags('', ', ', ''); ?></span>   
				</p>
			</div>			
	<?php endwhile; ?>	
    		
		<div id="navigation">
		<?php natty_pagenavi(); ?>
		</div>    
        
    <?php else : 
		echo '<div class="post">';
		if ( is_category() ) { // If this is a category archive
			printf(__('<h2 class=\'center\'>Sorry, but there aren\'t any posts in the %s category yet.</h2>','nattywp'), single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			_e('<h2>Sorry, but there aren\'t any posts with this date.</h2>','nattywp');
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf(__('<h2 class=\'center\'>Sorry, but there aren\'t any posts by %s yet.</h2>','nattywp'), $userdata->display_name);
		} else {
      _e('<h2 class=\'center\'>No posts found.</h2>','nattywp');
		}
		get_search_form();	
		echo '</div>';		
	endif; ?>
	
 </div> <!-- END Narrowcolumn -->
   <div id="sidebar" class="profile">
     <?php get_sidebar();?>
   </div>    
<div class="clear"></div>    
<?php get_footer(); ?> 