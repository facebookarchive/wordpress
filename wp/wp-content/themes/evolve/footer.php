<?php
/**
 * Template: Footer.php
 *
 * @package EvoLve
 * @subpackage Template
 */
?>
		<!--END #content-->
		</div>
    
    	<!--END .container-->
	</div> 
  
  

      	<!--END .content-->
	</div> 
  
     <!--BEGIN .content-bottom--> 
  <div class="content-bottom">
  
       	<!--END .content-bottom-->
  </div>
			
		<!--BEGIN .footer-->
		<div class="footer">
    
    <div class="footer-top"></div>
    
    <div class="radial-effect" style="height:100px;"></div>
    
  
   	<!--BEGIN .container-->
	<div class="container" style="margin-bottom:0;position:relative;">    
  
  
           <!-- AD Space 9 -->
  
  
    <?php $options = get_option('evolve');
     if (!empty($options['evl_space_9'])) {  
    
 $ad_space_9 = $options['evl_space_9']; 
 echo '<div style="text-align:center;margin-bottom:25px;overflow:hidden;">'.stripslashes($ad_space_9).'</div>';
 
 } 
?> 


  <?php $options = get_option('evolve');

// if Footer widgets exist

  if (($options['evl_widgets_num'] == "") || ($options['evl_widgets_num'] == "disable"))  
{ } else { ?> 
  
  <!--BEGIN .widgets-holder-->
    <div class="widgets-holder">
    
    <div class="footer-1">
    	<?php	if ( !dynamic_sidebar( 'footer-1' ) ) : ?>
      <?php endif; ?>
      </div>
     
     <div class="footer-2"> 
      <?php	if ( !dynamic_sidebar( 'footer-2' ) ) : ?>
      <?php endif; ?>
      </div>
    
    <div class="footer-3">  
	    <?php	if ( !dynamic_sidebar( 'footer-3' ) ) : ?>
      <?php endif; ?>
      </div>      
    
    
    <div class="footer-4">  
    	<?php	if ( !dynamic_sidebar( 'footer-4' ) ) : ?>
      <?php endif; ?>
      </div>
        
    </div> 
    
    <!--END .widgets-holder--> 
    
    <?php } ?>


<div style="clear:both;"></div> 
  
  <?php $options = get_option('evolve');
 $footer_content = $options['evl_footer_content']; 
 if ($footer_content === false) $footer_content = '';
 echo stripslashes($footer_content);
?>   


 

  
  

			<!-- Theme Hook -->
      
      <?php evlfooter_hooks(); ?> 
      
		  

          	<!--END .container-->  
	</div> 

 
		
		<!--END .footer-->
		</div>

<!--END body-->  



  <?php $options = get_option('evolve');
  if ($options['evl_pos_button'] == "disable" || $options['evl_pos_button'] == "") { ?>
  
   <?php } else { ?>
   
     <div id="backtotop"><a href="#top" id="top-link"><span class="top-icon"><?php _e( 'Back to Top', 'evolve' ); ?></span></a></div>   

<?php } ?>

<?php if ($options['evl_custom_background'] == "1") { ?>
</div>
<?php } ?>

<?php wp_footer(); ?> 



<?php if ($options['evl_custom_background'] == "1") { ?>
</div>
<?php } ?>

</body>
<!--END html(kthxbye)-->
</html>