<?php get_header(); ?> 
<div id="main">		
	<div class="columns">    
    <div class="narrowcolumn singlepage">
     <?php if (have_posts()) : ?>
     <?php while (have_posts()) : the_post(); ?>							
			<div class="post">
            	<div class="date">
                    <span class="post-day">404</span>                    
                </div>
                <div class="title"><h2><?php _e('Error 404','nattywp'); ?></h2></div>                
				<div class="entry">  
                  <p><?php _e('It appears you\'ve missed you\'re intended destination, either through a bad or outdated link, or a typo in the page you were hoping to reach.','nattywp'); ?></p>
                  <p><strong><?php _e('You can take a look through my recent posts.','nattywp'); ?></strong></p>
                       <?php query_posts('showposts=15'); ?>
                       <?php while (have_posts()) : the_post(); ?>
                          <ul class="arc">
                             <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
                          </ul>
                       <?php endwhile;?>
                  <div class="clear"></div>
				</div>   				              
			</div>	          	
	<?php endwhile; ?>		
    <?php endif; ?>				
	</div> <!-- END Narrowcolumn -->
    <div id="sidebar" class="profile">
       <?php get_sidebar();?>
    </div>    
<div class="clear"></div>
<?php get_footer(); ?> 