<?php
get_header();
?>
    <div id="main-col">
		<?php suffusion_before_begin_content(); ?>
  	<div id="content">
<?php
global $post;
if (have_posts()) {
	while (have_posts()) {
		the_post();
		global $suf_prev_next_above_below;
		if ($suf_prev_next_above_below == 'above' || $suf_prev_next_above_below == 'above-below') {
			get_template_part('custom/prev-next');
		}
		$original_post = $post;
		$custom_class = "";

		if ($post->post_type != 'post' && $post->post_type != 'page') {
			// Custom post type. See if there is style inheritance
			$suffusion_post_types = get_option('suffusion_post_types');
			if (is_array($suffusion_post_types)) {
				foreach ($suffusion_post_types as $suffusion_post_type) {
					if ($suffusion_post_type['style_inherit'] != 'custom') {
						$custom_class = $suffusion_post_type['style_inherit'];
					}
				}
			}
			if ($custom_class == "") {
				$custom_class = "post";
			}
		}
?>
	<div <?php post_class($custom_class);?> id="post-<?php the_ID(); ?>">
<?php
		suffusion_after_begin_post();
	?>
		<div class="entry-container fix">
			<div class="entry fix">
<?php
		suffusion_content();
?>
			</div><!--/entry -->
<?php
		// Due to the inclusion of Ad Hoc Widgets the global variable $post might have got changed. We will reset it to the original value.
		$post = $original_post;
		suffusion_after_content();
?>
		</div><!-- .entry-container -->
<?php
		suffusion_before_end_post();
		comments_template();
?>
	</div><!--/post -->
<?php
		if ($suf_prev_next_above_below == 'below' || $suf_prev_next_above_below == 'above-below') {
			get_template_part('custom/prev-next');
		}
	}
}
else {
?>
        <div class="post fix">
		<p><?php _e('Sorry, no posts matched your criteria.', 'suffusion'); ?></p>
        </div><!--post -->

<?php
}
?>
      </div><!-- content -->
    </div><!-- main col -->
<?php
get_footer();
?>