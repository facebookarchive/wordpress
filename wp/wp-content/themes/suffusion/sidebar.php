<?php
/**
 * Default (first) sidebar
 *
 * @package Suffusion
 * @subpackage Templates
 */

global $sidebar_alignment, $suf_sidebar_alignment, $suf_wa_sb1_style, $suf_sidebar_1_def_widgets, $suf_sidebar_header;

if (is_page_template('1l-sidebar.php') || is_page_template('2l-sidebars.php') || (is_page_template('1l1r-sidebar.php') && $suf_sidebar_alignment == 'left')) {
	$sidebar_alignment = 'left';
}
else if (is_page_template('1r-sidebar.php') || is_page_template('2r-sidebars.php') || (is_page_template('1l1r-sidebar.php') && $suf_sidebar_alignment == 'right')) {
	$sidebar_alignment = 'right';
}
else if ($suf_sidebar_alignment == 'left') {
	$sidebar_alignment = 'left';
}
else if ($suf_sidebar_alignment == 'right') {
	$sidebar_alignment = 'right';
}
if ($suf_wa_sb1_style != 'tabbed') {
?>
<div class="dbx-group <?php echo $sidebar_alignment;?> <?php echo $suf_wa_sb1_style;?> warea" id="sidebar">
<?php
	if (!dynamic_sidebar()) {
		if ($suf_sidebar_1_def_widgets == 'show') {
?>
	<!--widget start -->
	<div id="categories" class="dbx-box suf-widget widget_categories">
	  <div class="dbx-content">
	  <h3 class="dbx-handle <?php echo $suf_sidebar_header;?>"><?php _e('Categories', 'suffusion'); ?></h3>
	    <ul>
	      <?php wp_list_categories(array('show_count' => true, 'title_li' => null)); ?>
	    </ul>
	  </div>
	</div>
	<!--widget end -->

	<!--widget start -->
	<div id="archives" class="dbx-box suf-widget widget_archive">
	  <div class="dbx-content">
	  <h3 class="dbx-handle <?php echo $suf_sidebar_header;?>"><?php _e('Archives', 'suffusion'); ?></h3>
	    <ul>
	      <?php wp_get_archives('type=monthly'); ?>
	    </ul>
	  </div>
	</div>
	<!--widget end -->

	<!--widget start -->
	<div id="links" class="dbx-box suf-widget">
	  <div class="dbx-content">
	  <h3 class="dbx-handle <?php echo $suf_sidebar_header;?>"><?php _e('Links', 'suffusion'); ?></h3>
	    <ul>
	      <?php wp_list_bookmarks(array('categorize' => false, 'orderby' => 'id', 'order' => 'ASC', 'title_li' => null)); ?>
	    </ul>
	  </div>
	</div>
	<!--widget end -->

	<!--widget start -->
	<div id="meta" class="dbx-box suf-widget">
	  <div class="dbx-content">
	  <h3 class="dbx-handle <?php echo $suf_sidebar_header;?>"><?php _e('Meta', 'suffusion'); ?></h3>
	    <ul>
			  <?php wp_register(); ?>
			  <li class="login"><?php wp_loginout(); ?></li>
			  <?php wp_meta(); ?>
				  <li class="rss"><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)', 'suffusion'); ?></a></li>
	        <li class="rss"><a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)', 'suffusion'); ?></a></li>
	        <li class="wordpress"><a href="http://www.wordpress.org" title="Powered by WordPress">WordPress</a></li>
	    </ul>
	  </div>
	</div>
	<!--widget end -->
<?php
		}
	}
?>
</div><!--/sidebar -->
<?php
}
else {
?>
<div class="tabbed-sidebar tab-box-<?php echo $sidebar_alignment;?> <?php echo $sidebar_alignment;?> warea fix" id="sidebar">
	<ul class="sidebar-tabs">
<?php
	dynamic_sidebar();
?>
	</ul><!--/sidebar-tabs -->
</div><!--/sidebar -->
<?php
}
?>
