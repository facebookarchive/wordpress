<?php
/**
 * This is shown if your current view has no posts. This file is not to be loaded directly, but is instead loaded from different templates.
 *
 * @package Suffusion
 * @subpackage Templates
 */

if (is_search()) {
?>
	<div class="post fix">
		<h2 class='posttitle'><?php _e('Nothing Found', 'suffusion');?></h2>
		<div class='entry'>
			<p><?php _e('Please try another search.', 'suffusion');?></p>
			<?php get_search_form(); ?>
		</div>
	</div><!--post -->
<?php
}
else {
?>
	<div class="post fix">
		<h2 class='posttitle'><?php _e("Not Found", "suffusion"); ?></h2>
		<div class='entry'>
			<p><?php _e("Sorry, but you are looking for something that isn't here", "suffusion"); ?></p>
		</div>
	</div><!--post -->
<?php
}
?>
