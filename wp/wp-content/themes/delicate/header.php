<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title>
<?php if ( is_home()) { bloginfo('name'); ?> - <?php bloginfo('description'); } ?>
<?php if ( is_search()) { bloginfo('name'); ?> - <?php _e('Search Results', 'nattywp'); } ?>
<?php if ( is_author()) { bloginfo('name'); ?> - <?php _e('Author Archives', 'nattywp'); } ?>
<?php if ( is_single()) { $custom_title = get_post_meta($post->ID, 'natty_title', true); 
if (strlen($custom_title)) {echo strip_tags(stripslashes($custom_title));}else { wp_title(''); ?> - <?php bloginfo('name'); }} ?>
<?php if ( is_page()) { $custom_title = get_post_meta($post->ID, 'natty_title', true); 
if (strlen($custom_title)) {echo strip_tags(stripslashes($custom_title));}else { bloginfo('name'); ?> - <?php wp_title(''); }}?>
<?php if ( is_category()) { bloginfo('name'); ?> - <?php _e('Archive','nattywp'); ?> - <?php single_cat_title(); } ?>
<?php if ( is_month()) { bloginfo('name'); ?> - <?php _e('Archive','nattywp'); ?> - <?php the_time('F');  } ?>
<?php if (function_exists('is_tag')) { if ( is_tag() ) { bloginfo('name'); ?> - <?php _e('Tag Archive','nattywp'); ?> - <?php  single_tag_title("", true); } } ?>
</title>

<?php /* Include the jQuery framework (see hooks.php) */ ?>
<!-- Style sheets -->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<?php include (TEMPLATEPATH . '/style.php'); ?>
<?php wp_head(); ?>

<!-- Feed link -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!-- jQuery utilities -->
<?php if (t_get_option('t_cufon_replace') == 'yes') { ?>
<!--[if gte IE 9]>
	<script type="text/javascript">
      /*<![CDATA[*/
	     Cufon.set('engine', 'canvas');
	    /*]]>*/
	</script>
<![endif]-->
<script type="text/javascript">/*<![CDATA[*/Cufon.replace('.post .title h2 a', {hover:true});/*]]>*/</script>
<?php } ?>

<!--[if IE 6]>
      <?php wp_print_scripts(array('ie_menu')); ?>
    	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/ie6.css" />
        <style type="text/css">
            img.png {
            filter: expression(
            (runtimeStyle.filter == '') ? runtimeStyle.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src='+src+', sizingMethod=scale)' : '',
            width = width,
            src = '<?php echo get_template_directory_uri(); ?>/images/px.gif');
    }
        </style>
	<![endif]-->
    
  <!--[if IE 7]>
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/ie7.css" />
	<![endif]-->
<style type="text/css">
<?php 
  $t_show_slideshow = t_get_option( "t_show_slideshow" );  
  $t_scroll_pages = t_get_option( "t_scroll_pages" );  
?>
</style>
</head>

<body <?php body_class(); ?>>
<div class="content-pad">

<div id="header">
	<?php t_get_logo ('<div id="logo">', '</div>', 'logo.gif', true); ?>
        
  <div id="top_search"> 
      <?php get_search_form(); ?>
  </div>
</div>

<div class="top">
    <div id="menu">	
       <?php natty_show_navigation ('primary', 'natty_show_pagemenu'); ?>
    </div>                
</div> <!-- END top -->
<div class="clear"></div>
<div class="head-img">

  <?php if ($t_show_slideshow == 'hide') {}
  elseif (!isset($t_show_slideshow) || $t_show_slideshow == 'no') { // Display Slideshow ?>  
  <div class="slideshow-bg module">
    <div class="slideshow">
      <?php if ($t_scroll_pages == 'no' || $t_scroll_pages[0] == 'no' || $t_scroll_pages[0] == ''){
        echo '<div><div class="tagline">Welcome to Delicate template</div><img src="'.get_template_directory_uri().'/images/header/headers.jpg" alt="Header" /></div>';
        echo '<div><div class="tagline">Just another WordPress site</div><img src="'.get_template_directory_uri().'/images/header/header.jpg" alt="Header" /></div>';
      } else { 
        foreach ($t_scroll_pages as $ad_pgs ) { 
         query_posts('page_id='.$ad_pgs ); while (have_posts()) : the_post(); ?>
      <div>
        <div class="tagline"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
        <?php if ( has_post_thumbnail() ) {the_post_thumbnail('slide-thumb');} // 970x225 ?>
      </div>
      <?php endwhile; wp_reset_query(); ?>	
      <?php } //end foreach ?>  
    <?php } ?>  
    </div><!-- END Slideshow -->
    <div id="slideshow-nav"></div>
  </div> <!-- END slideshow-bg -->

  <?php } else { // Display Header Image    
    $header_image = get_header_image();
    if ( !empty( $header_image ) ) : ?>
      <div class="tagline"><?php bloginfo('description'); ?></div>
      <img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="Header" />
    <?php endif;     
  } // End if ?>
</div>
<!-- END Header -->