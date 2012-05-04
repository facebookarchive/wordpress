// FIELDS

jQuery(document).ready(function(){
			
			// init fields
				
			if(jQuery('#search-text').val() !=  '')
				jQuery('#search_label span').hide();
				
				if(jQuery('#comment-author').val() !=  '')
				jQuery('#comment-name_label span').hide();
        
				if(jQuery('#comment-email').val() !=  '')
				jQuery('#comment-email_label span').hide();     
        
				if(jQuery('#comment-url').val() !=  '')
				jQuery('#comment-url_label span').hide();     
        
				if(jQuery('#comment-text').val() !=  '')
				jQuery('#comment-text_label span').hide();                 

			// on focus
			
			jQuery('#search_label input').focus(function() {
				jQuery('#search_label span').fadeTo(100, 0.5);
				
				if(jQuery('#search-text').val() !==  '')
				jQuery('#search_label span').hide();
			});
			
						jQuery('#comment-name_label input').focus(function() {
				jQuery('#comment-name_label span').fadeTo(100, 0.5);
				
				if(jQuery('#comment-author').val() !==  '')
				jQuery('#comment-name_label span').hide();
        
      });  
        
						jQuery('#comment-email_label input').focus(function() {
				jQuery('#comment-email_label span').fadeTo(100, 0.5);
				
				if(jQuery('#comment-email').val() !==  '')
				jQuery('#comment-email_label span').hide();        
			});
      
						jQuery('#comment-url_label input').focus(function() {
				jQuery('#comment-url_label span').fadeTo(100, 0.5);
				
				if(jQuery('#comment-url').val() !==  '')
				jQuery('#comment-url_label span').hide();        
			});   
      
						jQuery('#comment-text_label textarea').focus(function() {
				jQuery('#comment-text_label span').fadeTo(100, 0.5);
				
				if(jQuery('#comment-text').val() !==  '')
				jQuery('#comment-text_label span').hide();        
			});            
			
			//on keydown
			
			jQuery('#search_label input').keydown(function() {
				if(jQuery('#search-text').val() !==  '')
				jQuery('#search_label span').hide();
			});
			
				jQuery('#comment-name_label input').keydown(function() {
				if(jQuery('#comment-author').val() !==  '')
				jQuery('#comment-name_label span').hide();
      });  
				jQuery('#comment-email_label input').keydown(function() {
				if(jQuery('#comment-email').val() !==  '')
				jQuery('#comment-email_label span').hide();
                
			});
      
				jQuery('#comment-url_label input').keydown(function() {
				if(jQuery('#comment-url').val() !==  '')
				jQuery('#comment-url_label span').hide();
                
			});    
      
				jQuery('#comment-text_label textarea').keydown(function() {
				if(jQuery('#comment-text').val() !==  '')
				jQuery('#comment-text_label span').hide();
                
			});          
			
			//on click
			jQuery('#search_label').click(function() {
				jQuery('#search-text').trigger('focus');
				if(jQuery('#search-text').val() !==  '')
				jQuery('#search_label span').hide();
			});
					jQuery('comment-name_label').click(function() {
				jQuery('#comment-author').trigger('focus');
				if(jQuery('#comment-author').val() !== '')
				jQuery('#comment-name_label span').hide();
      });  
					jQuery('comment-email_label').click(function() {
				jQuery('#comment-email').trigger('focus');
				if(jQuery('#comment-email').val() !== '')
				jQuery('#comment-email_label span').hide();        
			});
					jQuery('comment-url_label').click(function() {
				jQuery('#comment-url').trigger('focus');
				if(jQuery('#comment-url').val() !== '')
				jQuery('#comment-url_label span').hide();        
			});   
      
					jQuery('comment-text_label').click(function() {
				jQuery('#comment-text').trigger('focus');
				if(jQuery('#comment-text').val() !== '')
				jQuery('#comment-text_label span').hide();        
			});          
			
			//on blur

			jQuery('#search-text').blur(function() {
				if( jQuery('#search-text').val() ==  '') {
					jQuery('#search_label span').show();
					jQuery('#search_label span').fadeTo(100, 1);
				}
				if(jQuery('#search-text').val() !==  '')
				jQuery('#search_label span').hide();
			});
					jQuery('#comment-author').blur(function() {
				if( jQuery('#comment-author').val() ==  '') {
					jQuery('#comment-name_label span').show();
					jQuery('#comment-name_label span').fadeTo(100, 1);
				}
				if(jQuery('#comment-author').val() !==  '')
				jQuery('#comment-name_label span').hide();
     	});  
					jQuery('#comment-email').blur(function() {
				if( jQuery('#comment-email').val() ==  '') {
					jQuery('#comment-email_label span').show();
					jQuery('#comment-email_label span').fadeTo(100, 1);
				}
				if(jQuery('#comment-email').val() !==  '')
				jQuery('#comment-email_label span').hide();        
			});
					jQuery('#comment-url').blur(function() {
				if( jQuery('#comment-url').val() ==  '') {
					jQuery('#comment-url_label span').show();
					jQuery('#comment-url_label span').fadeTo(100, 1);
				}
				if(jQuery('#comment-url').val() !==  '')
				jQuery('#comment-url_label span').hide();        
			});   
					jQuery('#comment-text').blur(function() {
				if( jQuery('#comment-text').val() ==  '') {
					jQuery('#comment-text_label span').show();
					jQuery('#comment-text_label span').fadeTo(100, 1);
				}
				if(jQuery('#comment-text').val() !==  '')
				jQuery('#comment-text_label span').hide();        
			});           
		});
		
// FIELDS