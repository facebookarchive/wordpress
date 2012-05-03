    <?php $options = get_option('evolve');  
  if ($options['evl_header_slider'] == "disable" || $options['evl_header_slider'] == "") { ?>
   
   <style type="text/css"> 
  ul.slides li.slide {
display:block !important; }
</style>    

  <?php } else { ?>
  
  <?php } ?>
 
 <?php $options = get_option('evolve'); 
  if ($options['evl_pos_sidebar'] == "right") { ?>
  
  <?php } if ($options['evl_pos_sidebar'] == "left") { ?> 
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/2col-l.css" type="text/css" media="screen,projection" />
   
  <?php } if ($options['evl_pos_sidebar'] == "left" && $options['evl_sidebar_num'] == "two") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/3col-l.css" type="text/css" media="screen,projection" />
  
  <?php } if ($options['evl_pos_sidebar'] == "right" && $options['evl_sidebar_num'] == "two") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/3col-r.css" type="text/css" media="screen,projection" />
  
  
    <?php } if ($options['evl_pos_sidebar'] == "left_right" && $options['evl_sidebar_num'] == "two") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/3col-l+r.css" type="text/css" media="screen,projection" />
  
  
  
  <?php } if ($options['evl_width_layout'] == "fluid") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/fluid.css" type="text/css" media="screen,projection" />   
  
  <?php } if ($options['evl_width_layout'] == "fluid" && $options['evl_sidebar_num'] == "two") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/2fluid.css" type="text/css" media="screen,projection" />
  
  
    <?php } if ($options['evl_width_layout'] == "fluid" && $options['evl_sidebar_num'] == "two" && $options['evl_pos_sidebar'] == "left_right") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/3fluid.css" type="text/css" media="screen,projection" />
  
  <?php } if ($options['evl_sidebar_num'] == "disable") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/no-sidebar.css" type="text/css" media="screen,projection" />
  
  <?php } if ($options['evl_sidebar_num'] == "disable" && $options['evl_width_layout'] == "fluid") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/no-sidebar-fluid.css" type="text/css" media="screen,projection" />
  
   <?php } if ($options['evl_content_back'] == "dark") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/dark.css" type="text/css" media="screen,projection" />

  
     <?php } if ($options['evl_menu_back'] == "dark") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/dark-menu.css" type="text/css" media="screen,projection" />
  
       <?php } if ($options['evl_main_color'] == "light_grey_blue") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/light-grey-blue.css" type="text/css" media="screen,projection" />
  
  
      <?php } if ($options['evl_main_color'] == "light_grey_blue" && $options['evl_content_back'] == "dark" ) { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/light-grey-blue+dark.css" type="text/css" media="screen,projection" />
  
           <?php } if ($options['evl_main_color'] == "green_yellow") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/green-yellow.css" type="text/css" media="screen,projection" />
  
           <?php } if ($options['evl_main_color'] == "green_yellow" && $options['evl_content_back'] == "dark" ) { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/green-yellow+dark.css" type="text/css" media="screen,projection" />
  
             <?php } if ($options['evl_main_color'] == "red_yellow") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/red-yellow.css" type="text/css" media="screen,projection" />
  
               <?php } if ($options['evl_main_color'] == "red_yellow" && $options['evl_content_back'] == "dark") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/red-yellow+dark.css" type="text/css" media="screen,projection" />
 
               <?php } if ($options['evl_main_color'] == "pink_purple") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/pink-purple.css" type="text/css" media="screen,projection" />
  
  
                 <?php } if ($options['evl_main_color'] == "pink_purple" && $options['evl_content_back'] == "dark" ) { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/pink-purple+dark.css" type="text/css" media="screen,projection" />
  
                 <?php } if ($options['evl_main_color'] == "light_blue") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/light-blue.css" type="text/css" media="screen,projection" />
  
  
                 <?php } if ($options['evl_main_color'] == "light_blue" && $options['evl_content_back'] == "dark" ) { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/light-blue+dark.css" type="text/css" media="screen,projection" />


                 <?php } if ($options['evl_main_color'] == "brown_yellow") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/brown-yellow.css" type="text/css" media="screen,projection" />
  
  
                 <?php } if ($options['evl_main_color'] == "brown_yellow" && $options['evl_content_back'] == "dark" ) { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/brown-yellow+dark.css" type="text/css" media="screen,projection" />
      

                 <?php } if ($options['evl_post_layout'] == "two") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/posts-layout.css" type="text/css" media="screen,projection" />
  
                  <?php } if ($options['evl_post_layout'] == "three") { ?>
  
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/posts-layout-three.css" type="text/css" media="screen,projection" />
  
  
  
                   <?php } if ($options['evl_title_font'] == "tahoma") { ?>
  
 <style type="text/css"> 
  #logo, #logo a {font-family:Tahoma, Geneva, Verdana;font-weight:bold;letter-spacing:-2px;}
</style>

 <?php } if ($options['evl_title_font'] == "georgia") { ?>
  
 <style type="text/css"> 
  #logo, #logo a {font-family:Georgia, Palatino, Palatino Linotype, Times, Times New Roman, serif;font-weight:bold;letter-spacing:-2px;}
</style>

 <?php } if ($options['evl_title_font'] == "arial") { ?>
  
 <style type="text/css"> 
  #logo, #logo a {font-family:Arial Black, Arial, Helvetica Neue, Helvetica, sans-serif;font-weight:bold;letter-spacing:-5px;}
</style>


 <?php } if ($options['evl_title_font'] == "calibri") { ?>
  
 <style type="text/css"> 
  #logo, #logo a {font-family:Calibri,Segoe UI,Myriad Pro,Myriad,Trebuchet MS,Helvetica,Arial,sans-serif;font-weight:bold;letter-spacing:-2px;}
</style>


 <?php } if ($options['evl_content_font'] == "arial") { ?>

  <style type="text/css">body, input, textarea {font-family: Arial, Helvetica Neue, Helvetica, sans-serif;}</style>
  
 <?php } if ($options['evl_content_font'] == "georgia") { ?>

  <style type="text/css">body, input, textarea {font-family: Georgia, Palatino, Palatino Linotype, Times, Times New Roman, serif;}</style>
  
  
   <?php } if ($options['evl_content_font'] == "courier") { ?>

  <style type="text/css">body, input, textarea {font-family: "Courier New", Courier, monospace;}</style>
  
  

 <?php } if ($options['evl_content_font'] == "calibri") { ?>

  <style type="text/css">body, input, textarea {font-family:Calibri,Segoe UI,Myriad Pro,Myriad,Trebuchet MS,Helvetica,Arial,sans-serif;}</style>
  
  
   <?php } if ($options['evl_pos_logo'] == "right") { ?>
   
   <style type="text/css">#logo-image {float:right;margin:0 0 0 20px;} </style>
   
   
     <?php } if ($options['evl_pos_button'] == "left") { ?>
   
   <style type="text/css">#backtotop {left:3%;margin-left:0;} </style>
   
        <?php } if ($options['evl_pos_button'] == "right") { ?>
   
   <style type="text/css">#backtotop {right:3%;} </style>
   
   <?php } if ($options['evl_pos_button'] == "middle" || $options['evl_pos_button'] == "") { ?>
   
   <style type="text/css">#backtotop {left:50%;} </style>
   
   

  
  
  
  

<?php } if ($options['evl_widgets_header'] == "two") { ?>  

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-header/2-widgets.css" type="text/css" media="screen,projection" />
  

  <?php } if ($options['evl_widgets_header'] == "three") { ?>  

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-header/3-widgets.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_header'] == "four") { ?>  

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-header/4-widgets.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_header'] == "two" && $options['evl_width_layout'] == "fluid") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-header/2-widgets-fluid.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_header'] == "three" && $options['evl_width_layout'] == "fluid") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-header/3-widgets-fluid.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_header'] == "four" && $options['evl_width_layout'] == "fluid") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-header/4-widgets-fluid.css" type="text/css" media="screen,projection" />


  
  
  


<?php } if ($options['evl_widgets_num'] == "two") { ?>  

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-footer/2-widgets.css" type="text/css" media="screen,projection" />
  

  <?php } if ($options['evl_widgets_num'] == "three") { ?>  

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-footer/3-widgets.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_num'] == "four") { ?>  

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-footer/4-widgets.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_num'] == "two" && $options['evl_width_layout'] == "fluid") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-footer/2-widgets-fluid.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_num'] == "three" && $options['evl_width_layout'] == "fluid") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-footer/3-widgets-fluid.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_widgets_num'] == "four" && $options['evl_width_layout'] == "fluid") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/widgets-footer/4-widgets-fluid.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_back_images'] == "1") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/nacked.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_back_images'] == "1" && $options['evl_content_back'] == "dark") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/dark-nacked-content.css" type="text/css" media="screen,projection" />

<?php } if ($options['evl_back_images'] == "1" && $options['evl_menu_back'] == "dark") { ?>


<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/media/layouts/dark-nacked-menu.css" type="text/css" media="screen,projection" />


<?php } if ($options['evl_custom_background'] == "1") { ?>
<style type="text/css">
#wrapper {margin:0 auto 30px auto !important;background:#fff;box-shadow:0 0 15px rgba(0,0,0,.2);}</style>

<?php } ?>