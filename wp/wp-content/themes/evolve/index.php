<?php
/**
 * Template: Index.php
 *
 * @package EvoLve
 * @subpackage Template
 */

get_header();
?>



    <?php $xyz = ""; $options = get_option('evolve');
  if (($options['evl_sidebar_num'] == "disable") || ($options['evl_sidebar_num'] == "disable") && ($options['evl_width_layout'] == "fluid"))  
  
  
    { ?>
  
  
  <?php } else { ?>

  <?php $options = get_option('evolve');
  if ($options['evl_sidebar_num'] == "two") { ?> 
  
  <?php get_sidebar('2'); ?>
  
  
  <?php } ?>
  
    <?php } ?>  



			<!--BEGIN #primary .hfeed-->
			<div id="primary" class="hfeed">
      

     
 
 <!---------------------- 
 ---- attachment begin
 ----------------------->  


 <?php if (is_attachment()) { ?>
      
      
     <?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
				
				<!--BEGIN .hentry-->
				<div id="post-<?php the_ID(); ?>" class="<?php semantic_entries(); ?>">

            <?php $options = get_option('evolve'); if (($options['evl_header_meta'] == "") || ($options['evl_header_meta'] == "single_archive")) 
        { ?>
        
        <h1 class="entry-title"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment" style="font-size:24px;"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php if ( get_the_title() ){ the_title();
 } else { _e( 'Untitled', 'evolve' );  } ?></h1>
        
        
        	
	<!--BEGIN .entry-meta .entry-header-->
					<div class="entry-meta entry-header">
          <a href="<?php the_permalink() ?>"><span class="published"><?php the_time('Md'); ?><br /><strong><?php the_time('Y'); ?></strong></span></a>
 
          <?php if ( comments_open() ) : ?>           
          <span class="comment-count"><?php comments_popup_link( __( 'Leave a Comment', 'evolve' ), __( '1 Comment', 'evolve' ), __( '% Comments', 'evolve' ) ); ?></span>
          <?php else : // comments are closed 
           endif; ?>
         
          
          <span class="author vcard">
          
          <?php $options = get_option('evolve');
          if ($options['evl_author_avatar'] == "") { echo get_avatar( get_the_author_meta('email'), '30' );
          
          } ?>
          
          

          <?php _e( 'By', 'evolve' ); ?> <strong><?php printf( '<a class="url fn" href="' . get_author_posts_url( $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( 'View all posts by %s', $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></strong></span>
						
						<?php edit_post_link( __( 'edit', 'evolve' ), '<span class="edit-post">', '</span>' ); ?>

					<!--END .entry-meta .entry-header-->
                    </div>
                    
                     <?php } else { ?>
                    
                    <h1 class="entry-title" style="float:left;"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h1>
                    
                     <?php if ( current_user_can( 'edit_post', $post->ID ) ): ?>
       
				    <?php edit_post_link( __( 'EDIT', 'evolve' ), '<span class="edit-post" style="left:10px;position:relative;top:15px;">', '</span>' ); ?>
                    <?php endif; ?>

                    <br /><br /><?php } ?>
					
					<!--BEGIN .entry-content .article-->
					<div class="entry-content article">
				
     
							<?php if ( wp_attachment_is_image() ) :
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID )
			break;
	}
	$k++;
	// If there is more than 1 image attachment in a gallery
	if ( count( $attachments ) > 1 ) {
		if ( isset( $attachments[ $k ] ) )
			// get the URL of the next image attachment
			$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
		else
			// or get the URL of the first image attachment
			$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
	} else {
		// or, if there's only 1 image attachment, get the URL of the image
		$next_attachment_url = wp_get_attachment_url();
	}
?>
						<p class="attachment" align="center"><a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="single-gallery-image"><?php
							echo wp_get_attachment_image( $post->ID, $size='medium' ); // filterable image width with, essentially, no limit for image height.
						?></a></p>

						
			
              
              <div class="navigation-links single-page-navigation" style="clear:both;">
<div class="nav-next"><?php next_image_link ( false, 'Next Image &nbsp;&nbsp;&raquo;&nbsp;&nbsp;' ); ?></div>              
	<div class="nav-previous"><?php previous_image_link ( false, '&nbsp;&nbsp;&laquo;&nbsp;&nbsp; Previous Image' ); ?></div>
	
<!--END .navigation-links-->

              
              
              
              
						</div><!-- #nav-below -->
<?php else : ?>
						<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
<?php endif; ?>

<div class="entry-caption"><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></div>
         
			

					 <!--END .entry-content .article-->
           <div style="clear:both;"></div>
					</div>
				<!--END .hentry-->
				</div>

         <?php $options = get_option('evolve'); if (($options['evl_share_this'] == "single_archive") || ($options['evl_share_this'] == "all")) { 
        evolve_sharethis();  } else { ?> <div style="margin-bottom:40px;"></div> <?php }?>
        
        
				<?php comments_template( '', true ); ?>
                
				<?php endwhile; else : ?>

				<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
					<h1 class="entry-title">Not Found</h1>

					<!--BEGIN .entry-content-->
					<div class="entry-content">
						<p>Sorry, no attachments matched your criteria.</p>
					<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div>
        
         <!---------------------- 
 ---- attachment end
 ----------------------->  

			<?php endif; ?>      

 <!---------------------- 
 ---- single post begin
 ----------------------->     
      
 <?php } elseif (is_single()) { ?>
 
 
 <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                
                 <?php $options = get_option('evolve'); if (($options['evl_post_links'] == "before") || ($options['evl_post_links'] == "both")) { ?>
          
          
         <span class="nav-top">
				<?php get_template_part( 'navigation', 'index' ); ?>
        </span>
        
        <?php } ?> 

				<!--BEGIN .hentry-->
				<div id="post-<?php the_ID(); ?>" class="<?php semantic_entries(); ?>">
					



          <?php $options = get_option('evolve'); if (($options['evl_header_meta'] == "") || ($options['evl_header_meta'] == "single") || ($options['evl_header_meta'] == "single_archive")) 
        { ?>  <h1 class="entry-title"><?php if ( get_the_title() ){ the_title(); }else{ _e( 'Untitled', 'evolve' );  } ?></h1>
        
        
					<!--BEGIN .entry-meta .entry-header-->
					<div class="entry-meta entry-header">
          <a href="<?php the_permalink() ?>"><span class="published"><?php the_time('Md'); ?><br /><strong><?php the_time('Y'); ?></strong></span></a>
 
          <?php if ( comments_open() ) : ?>           
          <span class="comment-count"><?php comments_popup_link( __( 'Leave a Comment', 'evolve' ), __( '1 Comment', 'evolve' ), __( '% Comments', 'evolve' ) ); ?></span>
          <?php else : // comments are closed 
           endif; ?>
         
          
          <span class="author vcard">
          
          <?php $options = get_option('evolve');
          if ($options['evl_author_avatar'] == "") { echo get_avatar( get_the_author_meta('email'), '30' );
          
          } ?>
          
          

          <?php _e( 'Written by', 'evolve' ); ?> <strong><?php printf( '<a class="url fn" href="' . get_author_posts_url( $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( 'View all posts by %s', $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></strong></span>
						
						
            				    <?php edit_post_link( __( 'edit', 'evolve' ), '<span class="edit-post">', '</span>' ); ?>
					<!--END .entry-meta .entry-header-->
                    </div>   <?php } else { ?>
                    
                    <h1 class="entry-title" style="float:left;"><?php the_title(); ?></h1>
                    
                     <?php if ( current_user_can( 'edit_post', $post->ID ) ): ?>
       
						<?php edit_post_link( __( 'EDIT', 'evolve' ), '<span class="edit-post" style="left:10px;position:relative;top:15px;">', '</span>' ); ?>
            
                        				    
				
                    <?php endif; ?>

                    <br /><br /><?php } ?>
                    
                    
         <!-- AD Space 7 -->
  
  
    <?php $options = get_option('evolve');
     if (!empty($options['evl_space_7'])) { 
    
 $ad_space_7 = $options['evl_space_7']; 
 echo '<div style="text-align:center;margin:25px 0;clear:both;overflow:hidden;">'.stripslashes($ad_space_7).'</div>';
 
 } 
?>                     
      
			<!--BEGIN .entry-content .article-->
					<div class="entry-content article">
						<?php the_content( __('READ MORE &raquo;', 'evolve' ) ); ?>
            <?php wp_link_pages( array( 'before' => '<div id="page-links"><p>' . __( '<strong>Pages:</strong>', 'evolve' ), 'after' => '</p></div>' ) ); ?>
					<!--END .entry-content .article-->
					
          <div style="clear:both;"></div>
          </div>
          
          
          
         <!-- AD Space 8 -->
  
   <?php $options = get_option('evolve');
     if (!empty($options['evl_space_8'])) {  
    
 $ad_space_8 = $options['evl_space_8']; 
 echo '<div style="text-align:center;margin:25px 0;clear:both;overflow:hidden;">'.stripslashes($ad_space_8).'</div>';
      }

 
?>              

						<!--BEGIN .entry-meta .entry-footer-->
                    <div class="entry-meta entry-footer">
                    	<?php if ( evolve_get_terms( 'cats' ) ) { ?>
                    	<span class="entry-categories"><strong><?php _e('Posted in', 'evolve' ); ?></strong> <?php echo evolve_get_terms( 'cats' ); ?></span>
                      <?php } ?><?php if ( evolve_get_terms( 'cats' ) && evolve_get_terms( 'tags' ) ) { ?><span class="meta-sep">-</span><?php } ?>
						<?php if ( evolve_get_terms( 'tags' ) ) { ?>
                                                <span class="entry-tags"><strong><?php _e('Tagged', 'evolve' ); ?></strong> <?php echo evolve_get_terms( 'tags' ); ?></span>
                        <?php } ?>
					<!--END .entry-meta .entry-footer-->
                    </div>
                    
                    
                                   
                    <!-- Auto Discovery Trackbacks
					<?php trackback_rdf(); ?>
					-->
				<!--END .hentry-->
				</div>
        
      <?php $options = get_option('evolve'); if (($options['evl_share_this'] == "") || ($options['evl_share_this'] == "single") || ($options['evl_share_this'] == "single_archive")  || ($options['evl_share_this'] == "all")) { 
        evolve_sharethis(); } else { ?> <div style="margin-bottom:40px;"></div> <?php }?>
        
        
        
        
<?php $options = get_option('evolve'); if (($options['evl_similar_posts'] == "") || ($options['evl_similar_posts'] == "disable")) {} else {
evlsimilar_posts(); } ?>  

       
        <?php $options = get_option('evolve'); if (($options['evl_post_links'] == "") || ($options['evl_post_links'] == "after") || ($options['evl_post_links'] == "both")) { ?>
               
				<?php get_template_part( 'navigation', 'index' ); ?>

        
        <?php } ?>   

				<?php comments_template( '', true ); ?>
                
				<?php endwhile; else : ?>

				<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
					<h1 class="entry-title"><?php _e( 'Not Found', 'evolve' ); ?></h1>
          
          

					<!--BEGIN .entry-content-->
					<div class="entry-content">
						<p><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'evolve' ); ?></p>
						<?php get_search_form(); ?>
					<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div>

			<?php endif; ?>

 <!---------------------- 
 ---- single post end
 -----------------------> 


 <!---------------------- 
 ---- home/date/category/tag/search/author begin
 ----------------------->         
      
      <?php } elseif (is_home() || is_date() || is_category() || is_tag() || is_search() || is_author()) { ?>
 
 
 
 <!---------------------- 
 ---- 2 or 3 columns begin
 ----------------------->
 

 
      <?php if (is_date()) { ?> 
      
      
      	<?php /* If this is a daily archive */ if ( is_day() ) { ?>
				<h2 class="page-title archive-title"><?php _e( 'Daily archives for', 'evolve' ); ?> <span class="daily-title"><?php the_time( 'F jS, Y' ); ?></span></h2>
        				<?php /* If this is a monthly archive */ } elseif ( is_month() ) { ?>
				<h2 class="page-title archive-title"><?php _e( 'Monthly archives for', 'evolve' ); ?> <span class="monthly-title"><?php the_time( 'F, Y' ); ?></span></h2>
				<?php /* If this is a yearly archive */ } elseif ( is_year() ) { ?>
				<h2 class="page-title archive-title"><?php _e( 'Yearly archives for', 'evolve' ); ?> <span class="yearly-title"><?php the_time( 'Y' ); ?></span></h2>
				<?php } ?>
        
      <?php } elseif (is_category()) { ?> 
    <h2 class="page-title archive-title"><?php _e( 'Posts in category', 'evolve' ); ?> <span id="category-title"><?php single_cat_title(); ?></span></h2>

      
       <?php } elseif (is_tag()) { ?> 
       <h2 class="page-title archive-title"><?php _e( 'Posts tagged', 'evolve' ); ?> <span id="tag-title"><?php single_tag_title(); ?></span></h2>
       
       
       <?php } elseif (is_search()) { ?>
       
       
       <h2 class="page-title search-title"><?php _e( 'Search results for', 'evolve' ); ?> <?php echo '<span class="search-term">'.the_search_query().'</span>'; ?></h2>
       
          <?php } elseif (is_author()) { ?>
       
       
       <h2 class="page-title archive-title"><?php _e( 'Posts by', 'evolve' ); ?> <span class="author-title"><?php the_post(); echo $authordata->display_name; rewind_posts(); ?></span></h2>
       
       <?php } ?>
 
  <?php $options = get_option('evolve'); if ($options['evl_post_layout'] == "two" || $options['evl_post_layout'] == "three") { ?>      
    
    
       <?php if (($options['evl_nav_links'] == "before") || ($options['evl_nav_links'] == "both")) { ?>
          
          
        
				   <span class="nav-top">
				<?php get_template_part( 'navigation', 'index' ); ?>
        </span>
        
        <?php } else {?> 
        
        <?php } ?>         
    
   
      
			<?php if ( have_posts() ) : ?>
      
      
 
      
      
                <?php while ( have_posts() ) : the_post(); ?>
        
                

				<!--BEGIN .hentry-->
				<div id="post-<?php the_ID(); ?>" class="<?php semantic_entries(); ?> 
        
       <?php $options = get_option('evolve'); if ($options['evl_post_layout'] == "two") { ?> 
        <?php echo 'odd'.($xyz++%2); ?>
        
        <?php } else { ?>
        <?php echo 'odd'.($xyz++%3); ?>
        
        
        <?php } ?>
        
        " style="margin-bottom:40px;">
        
        
        
          <?php $options = get_option('evolve'); if (($options['evl_header_meta'] == "") || ($options['evl_header_meta'] == "single_archive")) 
        { ?>
        
					<h1 class="entry-title">
          
          
         
          <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
<?php
if ( get_the_title() ){ $title = the_title('', '', false);
echo evltruncate($title, 40, '...'); }else{ _e( 'Untitled', 'evolve' );  }
 ?></a> 
          
          
          
          </h1>

					<!--BEGIN .entry-meta .entry-header-->
					<div class="entry-meta entry-header">
          <a href="<?php the_permalink() ?>"><span class="published"><?php the_time('Md'); ?><br /><strong><?php the_time('Y'); ?></strong></span></a>
          <span class="author vcard">
 
          <?php _e( 'Written by', 'evolve' ); ?> <strong><?php printf( '<a class="url fn" href="' . get_author_posts_url( $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( 'View all posts by %s', $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></strong></span>
						
						 <?php edit_post_link( __( 'edit', 'evolve' ), '<span class="edit-post">', '</span>' ); ?>

					<!--END .entry-meta .entry-header-->
                    </div>
                    
                  <?php } else { ?>
                    
                    <h1 class="entry-title" style="float:left;"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
<?php
if ( get_the_title() ){ $title = the_title('', '', false);
echo evltruncate($title, 40, '...'); }else{ _e( 'Untitled', 'evolve' );  }
 ?></a> </h1>
                    
                     <?php if ( current_user_can( 'edit_post', $post->ID ) ): ?>
       
						<?php edit_post_link( __( 'EDIT', 'evolve' ), '<span class="edit-post" style="left:10px;position:relative;top:15px;">', '</span>' ); ?>
            
            
				
                    <?php endif; ?>

                    <br /><br /><?php } ?> 

					<!--BEGIN .entry-content .article-->
					<div class="entry-content article">
          
            <?php  
           
          
if(has_post_thumbnail()) {
	echo '<a href="'; the_permalink(); echo '">';the_post_thumbnail(array(100,100)); echo '</a>';
  
     } else {

                      $image = evlget_first_image(); 
                        if ($image):
                      echo '<a href="'; the_permalink(); echo'"><img src="'.$image.'" alt="';the_title();echo'" /></a>';
                      
                      
                       endif;
               } ?>
               

          
          <?php $postexcerpt = get_the_content();
$postexcerpt = apply_filters('the_content', $postexcerpt);
$postexcerpt = str_replace(']]>', ']]&gt;', $postexcerpt);
$postexcerpt = strip_tags($postexcerpt);
$postexcerpt = strip_shortcodes($postexcerpt);

echo evltruncate($postexcerpt, 350, ' [...]');
 ?>
          
          
          <div class="entry-meta entry-footer">
          
          <span class="read-more">
           <a href="<?php the_permalink(); ?>"><?php _e('READ MORE &raquo;', 'evolve' ); ?></a> 
           </span>
          
           <?php if ( comments_open() ) : ?>           
          <span class="comment-count"><?php comments_popup_link( __( 'Leave a Comment', 'evolve' ), __( '1 Comment', 'evolve' ), __( '% Comments', 'evolve' ) ); ?></span>
          <?php else : // comments are closed 
           endif; ?>
          </div>

					<!--END .entry-content .article-->
          <div style="clear:both;"></div>
					</div>
          
          

				<!--END .hentry-->
				</div>   
        
        <?php $i='';$i++; ?> 

				<?php endwhile; ?>
				<?php get_template_part( 'navigation', 'index' ); ?>
				<?php else : ?>
        
        
        
        <?php if (is_search()) { ?>
        
        
        	<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
					<h1 class="entry-title"><?php _e( 'Your search for', 'evolve' ); ?> "<?php echo the_search_query(); ?>" <?php _e( 'did not match any entries', 'evolve' ); ?></h1>
					
					<!--BEGIN .entry-content-->
					<div class="entry-content">
				<br />
						<p><?php _e( 'Suggestions:', 'evolve' ); ?></p>
						<ul>
							<li><?php _e( 'Make sure all words are spelled correctly.', 'evolve' ); ?></li>
							<li><?php _e( 'Try different keywords.', 'evolve' ); ?></li>
							<li><?php _e( 'Try more general keywords.', 'evolve' ); ?></li>
						</ul>
					<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div>
        
        <?php } else { ?>

				<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
					<h1 class="entry-title"><?php _e( 'Not Found', 'evolve' ); ?></h1>

					<!--BEGIN .entry-content-->
					<div class="entry-content">
						<p><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'evolve' ); ?></p>
							<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div>   
        
        <?php } ?>

			<?php endif; ?>
           
      
<!---------------------- 
 -----------------------
 -----------------------  
 ---- 2 or 3 columns end
 -----------------------
 -----------------------
 ----------------------->  
 
 
 <!---------------------- 
 -----------------------
 -----------------------  
 ---- 1 column begin
 -----------------------
 -----------------------
 -----------------------> 
  
  
  <?php } else { ?>    
     
      <?php $options = get_option('evolve'); if (($options['evl_nav_links'] == "before") || ($options['evl_nav_links'] == "both")) { ?>
          
          
        
				   <span class="nav-top">
				<?php get_template_part( 'navigation', 'index' ); ?>
        </span>
        
        <?php } else {?> 
        
        <?php } ?> 
         

      
			<?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                
                
                  


				<!--BEGIN .hentry-->
				<div id="post-<?php the_ID(); ?>" class="<?php semantic_entries(); ?>">
					


          <?php $options = get_option('evolve'); if (($options['evl_header_meta'] == "") || ($options['evl_header_meta'] == "single_archive")) 
        { ?>
        
        <h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php if ( get_the_title() ){ the_title();}else{ _e( 'Untitled', 'evolve' );  } ?></a></h1>
        
					<!--BEGIN .entry-meta .entry-header-->
					<div class="entry-meta entry-header">
          <a href="<?php the_permalink() ?>"><span class="published"><?php the_time('Md'); ?><br /><strong><?php the_time('Y'); ?></strong></span></a>
          
           <?php if ( comments_open() ) : ?>           
          <span class="comment-count"><a href="<?php comments_link(); ?>"><?php comments_popup_link( __( 'Leave a Comment', 'evolve' ), __( '1 Comment', 'evolve' ), __( '% Comments', 'evolve' ) ); ?></a></span>
          <?php else : // comments are closed 
           endif; ?>
          
          <span class="author vcard">
          
          <?php $options = get_option('evolve');
          if ($options['evl_author_avatar'] == "") { echo get_avatar( get_the_author_meta('email'), '30' );
          
          } ?>
          
          

          <?php _e( 'Written by', 'evolve' ); ?> <strong><?php printf( '<a class="url fn" href="' . get_author_posts_url( $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( 'View all posts by %s', $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></strong></span>
						
						
						
            <?php edit_post_link( __( 'edit', 'evolve' ), '<span class="edit-post">', '</span>' ); ?>
					<!--END .entry-meta .entry-header-->
                    </div>
                    
                    <?php } else { ?>
                    
                    <h1 class="entry-title" style="float:left;"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php if ( get_the_title() ){ the_title();}else{ _e( 'Untitled', 'evolve' );  } ?></a></h1>
                    
                     <?php if ( current_user_can( 'edit_post', $post->ID ) ): ?>
       
						<?php edit_post_link( __( 'EDIT', 'evolve' ), '<span class="edit-post" style="left:10px;position:relative;top:15px;">', '</span>' ); ?>
            
				
                    <?php endif; ?>

                    <br /><br /><?php } ?>

					<!--BEGIN .entry-content .article-->
					<div class="entry-content article">
          
          
           <?php $options = get_option('evolve'); if (($options['evl_excerpt_thumbnail'] == "1")) { ?> 
           
            <?php if(has_post_thumbnail()) {
	echo '<span class="thumbnail"><a href="'; the_permalink(); echo '">';the_post_thumbnail(array(100,100)); echo '</a></span>';
  
     } else {

                      $image = evlget_first_image(); 
                        if ($image):
                      echo '<span class="thumbnail"><a href="'; the_permalink(); echo'"><img src="'.$image.'" alt="';the_title();echo'" /></a></span>';
                       endif;
               } ?>
               
               
               
               

               

          
          <?php the_excerpt();?>
 
          
           <span class="read-more">
           <a href="<?php the_permalink(); ?>"><?php _e('READ MORE &raquo;', 'evolve' ); ?></a>
           </span>
           
          <?php } else { ?>
          
          
						<?php the_content( __('READ MORE &raquo;', 'evolve' ) ); ?>
            
            <?php wp_link_pages( array( 'before' => '<div id="page-links"><p>' . __( '<strong>Pages:</strong>', 'evolve' ), 'after' => '</p></div>' ) ); ?>
            
            <?php } ?>
						
					<!--END .entry-content .article--> 
          <div style="clear:both;"></div>                    
					</div>
          
          
          
					<!--BEGIN .entry-meta .entry-footer-->
         
                    <div class="entry-meta entry-footer">
                     <?php if ( evolve_get_terms( 'cats' ) ) { ?>
                    	<span class="entry-categories"><strong><?php _e('Posted in', 'evolve' ); ?></strong> <?php echo evolve_get_terms( 'cats' ); ?></span>
                      <?php } ?><?php if ( evolve_get_terms( 'cats' ) && evolve_get_terms( 'tags' ) ) { ?><span class="meta-sep">-</span><?php } ?>
						<?php if ( evolve_get_terms( 'tags' ) ) { ?>
                        
                        <span class="entry-tags"><strong><?php _e('Tagged', 'evolve' ); ?></strong> <?php echo evolve_get_terms( 'tags' ); ?></span>
                        <?php } ?>
					<!--END .entry-meta .entry-footer-->
                    </div>
                   
				<!--END .hentry-->
				</div>
        
        
       <?php $options = get_option('evolve'); if (($options['evl_share_this'] == "single_archive") || ($options['evl_share_this'] == "all")) { 
        evolve_sharethis();  } else { ?> <div style="margin-bottom:40px;"></div> <?php }?>
      
         
      <?php comments_template(); ?>  
       

				<?php endwhile; ?>
        
        
        <?php $options = get_option('evolve'); if (($options['evl_nav_links'] == "") || ($options['evl_nav_links'] == "after") || ($options['evl_nav_links'] == "both")) { ?>
          
          
        
				<?php get_template_part( 'navigation', 'index' ); ?>
        
        <?php } else {?>
        
        <?php } ?>
        
				<?php else : ?>

		     <?php if (is_search()) { ?>
        
        
        	<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
			
    		<h1 class="entry-title"><?php _e( 'Your search for', 'evolve' ); ?> "<?php echo the_search_query(); ?>" <?php _e( 'did not match any entries', 'evolve' ); ?></h1>
					
					<!--BEGIN .entry-content-->
					<div class="entry-content">
				<br />
						<p><?php _e( 'Suggestions:', 'evolve' ); ?></p>
						<ul>
							<li><?php _e( 'Make sure all words are spelled correctly.', 'evolve' ); ?></li>
							<li><?php _e( 'Try different keywords.', 'evolve' ); ?></li>
							<li><?php _e( 'Try more general keywords.', 'evolve' ); ?></li>
						</ul>
					<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div>
        
        <?php } else { ?>

				<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
					<h1 class="entry-title"><?php _e( 'Not Found', 'evolve' ); ?></h1>

					<!--BEGIN .entry-content-->
					<div class="entry-content">
						<p><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'evolve' ); ?></p>
            
            
            
							<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div>   
        
        <?php } ?>

			<?php endif; ?>
      
      
      
      <?php } ?>
      
 <!---------------------- 
 -----------------------
 -----------------------  
 ---- 1 column end
 -----------------------
 -----------------------
 ----------------------->       
      
<!---------------------- 
  -----------------------
  -----------------------
  ---- home/date/category/tag/search/author end
  -----------------------
  -----------------------
  -----------------------> 
      
      <?php } elseif (is_page()) { ?>
      
      
      <?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>

				<!--BEGIN .hentry-->
				<div id="post-<?php the_ID(); ?>" class="<?php semantic_entries(); ?>"> 
				<h1 class="entry-title"><?php if ( get_the_title() ){ the_title(); }else{ _e( 'Untitled', 'evolve' );  } ?></h1>  
                    
                    <?php if ( current_user_can( 'edit_post', $post->ID ) ): ?>
       
						<?php edit_post_link( __( 'EDIT', 'evolve' ), '<span class="edit-page">', '</span>' ); ?>
            
				
                    <?php endif; ?>

                    <br /><br />

					<!--BEGIN .entry-content .article-->
					<div class="entry-content article">
						<?php the_content( __('READ MORE &raquo;', 'evolve' ) ); ?>
					<!--END .entry-content .article-->
          <div style="clear:both;"></div>
					</div>
          
             

					<!-- Auto Discovery Trackbacks
					<?php trackback_rdf(); ?>
					-->
				<!--END .hentry-->
				</div>
        
               <?php $options = get_option('evolve'); if (($options['evl_share_this'] == "all")) { 
        evolve_sharethis();  } ?>
        
				<?php comments_template( '', true ); ?>

			<?php endwhile; endif; ?>
   
   
   
      <?php } elseif (is_404()) { ?>
     
     	<!--BEGIN #post-0-->
				<div id="post-0" class="<?php semantic_entries(); ?>">
           <h1 class="entry-title"><?php _e( 'Not Found', 'evolve' ); ?></h1>

					<!--BEGIN .entry-content-->
					<div class="entry-content">
						<p><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'evolve' ); ?></p>
            
            
					<!--END .entry-content-->
					</div>
				<!--END #post-0-->
				</div> 
      
        
      
      <?php } ?>


			<!--END #primary .hfeed-->
			</div>
      
      <?php $options = get_option('evolve');
  if (($options['evl_sidebar_num'] == "disable") || ($options['evl_sidebar_num'] == "disable") && ($options['evl_width_layout'] == "fluid"))  
  
  
    { ?>
  
  
  <?php } else { ?>


<?php get_sidebar(); ?>

    <?php } ?>

<?php get_footer(); ?>