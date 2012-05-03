<?php
/**
 * Template: Searchform.php
 *
 * @package EvoLve
 * @subpackage Template
 */
?>
<!--BEGIN #searchform-->
       <form action="<?php echo home_url(); ?>" method="get" class="searchform">
       
         <div id="search-text-box">
  
  <label class="searchfield" id="search_label" for="search-text"><span><?php _e( 'Type your search', 'evolve' ); ?></span><input id="search-text" type="text" tabindex="1" name="s" value="" class="search" /></label> 
  
  </div>
  
           <div id="search-button-box">
      
	<button id="search-button" tabindex="2" type="submit" class="search-btn"><?php _e( 'Submit', 'evolve' ); ?></button>
  
  </div>
  
  

  
  
    
</form>

<div style="clear:both;"></div>

<!--END #searchform-->