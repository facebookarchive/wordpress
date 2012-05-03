<?php
// nattywp Admin Interface: Setup, Pages, Machine
function nattywp_add_admin() {

    global $natty_themename, $options, $query_string;     
	$page_color = false;    
   
// Check all the Options, then if the no options are created for a ralitive sub-page... it's not created.    

  add_theme_page(__('Theme Options', 'nattywp'), __('Theme Options', 'nattywp'), 'edit_theme_options', 'nattywp_home', 'nattywp_page_gen');	
  add_theme_page(__('Color Options', 'nattywp'), __('Color Options', 'nattywp'), 'edit_theme_options', 'nattywp_color', 'nattywp_color');
  add_theme_page(__('More themes', 'nattywp'), __('More themes', 'nattywp'), 'edit_theme_options', 'more-nattywp', 'nattywp_more_themes_page');
}  


add_action('admin_head', 'bfa_add_stuff_admin_head');
add_action('admin_menu', 'nattywp_add_admin');
function bfa_add_stuff_admin_head() {
$url_base = get_template_directory_uri();
if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_color'){ 
 GLOBAL $controls, $tcontrols, $path_theme, $path_rainbow_images, $preset_styles, $root_block;	
	echo "<link rel=\"stylesheet\" href=\"$url_base/functions/moorainbow/mooRainbow.css\" type=\"text/css\" />\n";	
	echo "<script src=\"$url_base/functions/js/mootools.js\" type=\"text/javascript\"></script>\n";
	echo "<script src=\"$url_base/functions/moorainbow/mooRainbow.js\" type=\"text/javascript\"></script>\n";
	echo "<script type=\"text/javascript\">";
	require_once (TEMPLATEPATH . '/functions/js/color_control.php');
	echo "</script>"; 
}
	echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/functions/css/admin.css" type="text/css" media="all" />';
	echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/functions/css/ui.multiselect.css" type="text/css" media="all" />';
	
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_home'){ 
    echo "<script src=\"$url_base/functions/js/jquery-ui.js\" type=\"text/javascript\"></script>\n";
    echo "<script src=\"$url_base/functions/js/ui.multiselect.js\" type=\"text/javascript\"></script>\n";
	}
	?>	

	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/functions/js/ajaxupload.js"></script>
	
	<script type="text/javascript">
			jQuery(document).ready(function(){			
		
		<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_home'){ 
			echo "jQuery('.multiselect').multiselect();\n";
     } ?>
			//Tabs 
			jQuery('.tab').hide();
      jQuery('.tab:first').show();
      jQuery('.frame-nav li:first').addClass('current');
      jQuery('.frame-nav li a').click(function(event){				
						jQuery('.frame-nav li').removeClass('current');
						jQuery(this).parent().addClass('current');						
						var clicked_group = jQuery(this).attr('href');		 
						jQuery('.tab').hide();						
						jQuery(clicked_group).fadeIn();		
						event.preventDefault();						
				});
			
      // Popup Message
			jQuery.fn.center = function () {
				this.animate({"top":( jQuery(window).height() - this.height() - 400 ) / 2+jQuery(window).scrollTop() + "px"},100);
				this.css("left", 250 );
				return this;
			}
			jQuery('#natty-popup-save').center();
			jQuery('#natty-popup-backup').center();
			jQuery('#natty-popup-restore').center();			
			
			jQuery(window).scroll(function() { 			
				jQuery('#natty-popup-save').center();	
				jQuery('#natty-popup-backup').center();
				jQuery('#natty-popup-restore').center();					
			});			
						
			//AJAX Upload form 	
			jQuery('.image_upload_button').each(function(){			
        var clickedObject = jQuery(this);
        var clickedID = jQuery(this).attr('id');	
        new AjaxUpload(clickedID, {
				  action: '<?php echo admin_url("admin-ajax.php"); ?>',
				  name: clickedID, // File upload name
				  data: { // Additional data to send
						action: 'natty_ajax_post_action',
						type: 'upload',
						data: clickedID },
				  autoSubmit: true, // Submit file after selection
				  responseType: false,
				  onChange: function(file, extension){},
				  onSubmit: function(file, extension){
            if(clickedID == 'nattywp_custom_favicon') {
              if (! (extension && /^(ico|ICO)$/.test(extension))) {
                jQuery(".upload-error").remove();
                clickedObject.parent().after('<span class="upload-error">Only supports the .ICO file format</span>');
                return false;
              }  
            }
						clickedObject.text('Uploading'); // change button text, when user selects file	
						this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
						interval = window.setInterval(function(){
							var text = clickedObject.text();
							if (text.length < 13){	clickedObject.text(text + '.'); }
							else { clickedObject.text('Uploading'); } 
						}, 200);
				  },
				  
				  onComplete: function(file, response) {				   
					window.clearInterval(interval);
					clickedObject.text('Upload and Replace');	
					this.enable(); // enable upload button
					
					// If there was an error
					if(response.search('Upload Error') > -1){
						var buildReturn = '<span class="upload-error">' + response + '</span>';
						jQuery(".upload-error").remove();
						clickedObject.parent().after(buildReturn);
					
					}
					else{
						var buildReturn = '<img class="hide custom-logo-image" id="image_'+clickedID+'" src="'+response+'" alt="" />';
						jQuery(".upload-error").remove();
						jQuery("#image_" + clickedID).remove();	
						clickedObject.parent().after(buildReturn);
						jQuery('img#image_'+clickedID).fadeIn();
						clickedObject.next('span').fadeIn();
						clickedObject.parent().prev('input').val(response);
					}
				  }
				});			
			});
			
			//AJAX Remove (clear option value)
				jQuery('.image_reset_button').click(function(){			
					var clickedObject = jQuery(this);
					var clickedID = jQuery(this).attr('id');
					var theID = jQuery(this).attr('title');	
	
					var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
				
					var data = {
						action: 'natty_ajax_post_action',
						type: 'image_reset',
						data: theID
					};
					
					jQuery.post(ajax_url, data, function(response) {
						var image_to_remove = jQuery('#image_' + theID);
						var button_to_hide = jQuery('#reset_' + theID);
						image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
						button_to_hide.fadeOut();
						clickedObject.parent().prev('input').val('');
					});					
					return false; 					
				});   
				
		// Update options
			jQuery('.natty-options-submit').click(function(){				
					function newValues() {
					  var serializedValues = jQuery("#natty-options_form").serialize();
					  return serializedValues;
					}
					jQuery(":checkbox, :radio").click(newValues);
					jQuery("select").change(newValues);
					jQuery('.ajax-loading-img').fadeIn();
					var serializedReturn = newValues();
					 
					var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
				
					var data = {
						<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_home'){ ?>
                type: 'theme_options',              
						<?php } ?>
						<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_color'){ ?>
                type: 'color_options',
						<?php } ?>
						action: 'natty_ajax_post_action',
						data: serializedReturn
					};
					
					jQuery.post(ajax_url, data, function(response) {
						var success = jQuery('#natty-popup-save');
						var loading = jQuery('.ajax-loading-img');
						loading.fadeOut();  
						success.fadeIn();
						window.setTimeout(function(){
						   success.fadeOut(); 
						}, 2000);						
					});
					return false; 					
				});  
				
		// Backup options	
			jQuery('.natty-backup-submit').click(function(){	
        function newValues() {
					  var serializedValues = jQuery("#natty-options_form").serialize();
					  return serializedValues;
					}
					jQuery(":checkbox, :radio").click(newValues);
					jQuery("select").change(newValues);
					jQuery('.ajax-loading-img').fadeIn();
					var serializedReturn = newValues();
					 
					var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
				
					var data = {
						<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_home'){ ?>
                type: 'theme_options-backup',              
						<?php } ?>
						<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_color'){ ?>
                type: 'color_options-backup',
						<?php } ?>
						action: 'natty_ajax_post_action',
						data: serializedReturn
					};
					
					jQuery.post(ajax_url, data, function(response) {
						var success = jQuery('#natty-popup-backup');
						var loading = jQuery('.ajax-loading-img');
						loading.fadeOut();  
						success.fadeIn();
						window.setTimeout(function(){
						   success.fadeOut(); 
						}, 2000);
					});
					return false; 
			});	
			
			// Restore options				
			jQuery('.natty-restore-submit').click(function(){	
        function newValues() {
					  var serializedValues = jQuery("#natty-options_form").serialize();
					  return serializedValues;
					}
					jQuery(":checkbox, :radio").click(newValues);
					jQuery("select").change(newValues);
					jQuery('.ajax-loading-img').fadeIn();
					var serializedReturn = newValues();
					
					var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
				
					var data = {
						<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_home'){ ?>
                type: 'theme_options-restore',              
						<?php } ?>
						<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'nattywp_color'){ ?>
                type: 'color_options-restore',
						<?php } ?>
						action: 'natty_ajax_post_action',
						data: serializedReturn
					};
					
					jQuery.post(ajax_url, data, function(response) {
						var success = jQuery('#natty-popup-restore');
						var loading = jQuery('.ajax-loading-img');
						loading.fadeOut();  
						success.fadeIn();
						window.setTimeout(function(){
						   success.fadeOut(); 
						}, 2000);
						location.reload();
					});
					return false; 
			});			 	
				
			});
		</script>	
	<?php
}

function nattywp_color(){ nattywp_page_gen('color'); }

function nattywp_page_gen($page){
global $options, $natty_themename, $natty_manualurl, $natty_include_path, $natty_current;  

?>
    <div class="wrap">
    <div class="natty-options">
    <form action="" enctype="multipart/form-data" id="natty-options_form">
    
    <div class="header">
      <div class="main-top">
        <div class="general-pad">
         <div class="left">
            <h1><?php echo $natty_themename; ?> &rarr; <span class="blu">Theme Options</span></h1>
            <small>Theme Version: <?php echo $natty_current; ?></small>
         </div>
         <div class="right">
          <span class="copy">Theme by <a href="http://www.nattywp.com">NattyWP</a></span>
          <div class="social">
              <ul>
                <li class="tit"><small>Follow Us:</small></li>
                <li class="rss"><a href="http://www.nattywp.com/feed/rss.xml" class="png_crop">rss</a></li>
                <li class="twitter"><a href="http://twitter.com/nattywp" class="png_crop">twitter</a></li>
              </ul><div class="clear"></div>   
          </div>
            
         </div>   
         <div class="clear"></div>   
         </div>
      </div>
      <div class="supportline">
       <div class="general-pad-two">
        <div class="left">
          <a class="ico-docs" href="<?php echo $natty_manualurl; ?>">Theme Documentation</a>
          <a class="ico-support" href="http://support.nattywp.com">Submit a Ticket <small>(register first)</small></a>
        </div>
        <div class="right">
             	<img style="display:none" src="<?php echo get_template_directory_uri(); ?>/functions/images/loading.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." /><input type="submit" value="<?php _e('Update Options', 'nattywp') ?> &raquo;" class="button button-primary submit-button natty-options-submit" />
        </div>
        <div class="clear"></div>
        </div>
      </div>
    </div> <!-- end header -->
    
<div id="main"<?php if($page == 'color') {echo ' class="color-page"';} else { echo ' class="settings-page"';} ?>> 
  <div id="natty-popup-save" class="save-popup"><div class="popup-bg">Options Updated</div></div>
  <div id="natty-popup-backup" class="save-popup"><div class="popup-bg">Options Backuped</div></div>
  <div id="natty-popup-restore" class="save-popup"><div class="popup-bg">Options Restored</div></div>
  <?php 
  global $natty_functions_path;
	  if($page == 'color') {
	  	include ($natty_functions_path . 'theme-color.php');
	  } else {			
      include ($natty_functions_path . 'theme-options.php');  			
	  }
	?>
	<div class="clear"></div>
</div> <!-- end main -->

<div class="footer">
  <div class="general-pad-two">
    <div class="right">
      <input type="submit" class="natty-options-submit button-primary" name="Submit" value="<?php _e('Update Options', 'nattywp') ?> &raquo;" />
    </div>
	<div class="clear"></div>
	</div>
</div>
    	
   <p class="submit"><input type="submit" class="natty-backup-submit" name="backup-submit" value="<?php _e('Backup Options', 'nattywp') ?> &raquo;" /></p>	
   
   <?php 
	  if($page == 'color') { ?>
	  	<p class="submit"><input type="submit" class="natty-restore-submit<?php if(get_option($natty_themename.'_color_settings_back') == '') {echo ' no-but';} ?>" name="restore-color" value="<?php _e('Restore Color Options', 'nattywp') ?> &raquo;" /></p>
	 <?php } else {	 ?>		
      <p class="submit"><input type="submit" class="natty-restore-submit<?php if(get_option($natty_themename.'_settings_back') == '') {echo ' no-but';} ?>" name="restore-options" value="<?php _e('Restore Theme Options', 'nattywp') ?> &raquo;" /></p>		
	 <?php }	?>   
</form>
    
        
  
<div style="clear:both;"></div>  
</div> <!-- natty-options -->  
</div><!--wrap-->

 <?php
}
add_action('wp_ajax_natty_ajax_post_action', 'natty_ajax_callback');

function natty_ajax_callback() {
  global $controls, $tcontrols, $natty_themename;
  $save_type = $_POST['type'];  
  
  if($save_type == 'upload'){		
		$clickedID = $_POST['data']; // Acts as the name
		$filename = $_FILES[$clickedID];
    $filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']); 
		
		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';    
		$uploaded_file = wp_handle_upload($filename,$override);
		 
		$upload_tracking[] = $clickedID;
		update_option( $clickedID , $uploaded_file['url'] );
				
		if(!empty($uploaded_file['error'])) {echo 'Upload Error: ' . $uploaded_file['error']; }	
		else { echo $uploaded_file['url']; 
		} // Is the Response
	} 	
	if($save_type == 'image_reset'){			
			$id = $_POST['data']; // Acts as the name
			update_option($id, '');
	}			
  if ($save_type == 'theme_options') {
      $data = $_POST['data'];     
      parse_str($data,$output);       
      update_option($natty_themename.'_settings', $output);      
   }  
  if ($save_type == 'color_options') {
      $data = $_POST['data'];
      parse_str($data,$output);  
      update_option($natty_themename.'_color_settings', $output);
   }
  if ($save_type == 'theme_options-backup') {
      $data = $_POST['data'];     
      parse_str($data,$output);       
      update_option($natty_themename.'_settings_back', $output);      
   }
  if ($save_type == 'theme_options-restore') {
      $tmp = get_option($natty_themename.'_settings_back');   
      update_option($natty_themename.'_settings', $tmp);      
   }  
  if ($save_type == 'color_options-backup') {
      $data = $_POST['data'];
      parse_str($data,$output);  
      update_option($natty_themename.'_color_settings_back', $output);
   }
  if ($save_type == 'color_options-restore') {
      $tmp = get_option($natty_themename.'_color_settings_back');   
      update_option($natty_themename.'_color_settings', $tmp); 
   }
    die();
}
?>