<?php
/*
Template Name: Archives
*/
?>
<?php get_header();?>   
<div id="main">		
	<div class="columns">       
    <div class="narrowcolumn singlepage">					
			<div class="post">
             <div class="title">
				<h2><?php bloginfo('name'); echo ' '; _e('Archives', 'nattywp'); ?></h2>                
                </div>         
				<div class="entry">
                    <h3><?php _e('Archives by Month:','nattywp'); ?></h3>
                    <div class="clear"></div> 
                    <ul class="arc">
                        <?php wp_get_archives('type=monthly'); ?>
                    </ul>
                    <br /><br />
                    <h3><?php _e('Archives by Subject:','nattywp'); ?></h3>
                    <div class="clear"></div>        
                    <ul class="arc">
                         <?php wp_list_categories('title_li='); ?>
                    </ul>
                    <div class="clear"></div>                 
				</div>   				              
			</div>
	</div> <!-- END Narrowcolumn -->
    <div id="sidebar" class="profile">
       <?php get_sidebar();?>
    </div>    
<div class="clear"></div>
<?php get_footer(); ?> 