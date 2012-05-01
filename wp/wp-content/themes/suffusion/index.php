<?php
/**
 * Core file required with every WP theme. We have files for everything else.
 * So this is essentially the landing page if nothing else is set.
 * This is also the file used for archives.
 *
 * @package Suffusion
 * @subpackage Templates
 */

global $suf_index_excerpt, $suffusion;
$suffusion->set_content_layout($suf_index_excerpt);
get_header();
suffusion_query_posts();
?>
	<div id="main-col">
<?php suffusion_before_begin_content(); ?>
		<div id="content" class="hfeed">
<?php
if ($suf_index_excerpt == 'list') {
	get_template_part('layouts/layout-list');
}
else if ($suf_index_excerpt == 'tiles') {
	suffusion_after_begin_content();
	get_template_part('layouts/layout-tiles');
}
else if ($suf_index_excerpt == 'mosaic') {
	get_template_part('layouts/layout-mosaic');
}
else {
	suffusion_after_begin_content();
	get_template_part('layouts/layout-blog');
}
?>
      </div><!-- content -->
    </div><!-- main col -->
<?php
get_footer();
?>
