<?php
/**
 * Shows the byline of a post/page in a single line. This file should not be loaded by itself, but should instead be included using get_template_part or locate_template.
 * Users can override this in a child theme.
 *
 * @since 3.8.9
 * @package Suffusion
 * @subpackage Custom
 */

global $post, $suf_page_show_comment, $suf_page_show_posted_by, $suf_page_meta_position, $suf_date_box_show;
$format = suffusion_get_post_format();
if ($format == 'standard') {
	$format = '';
}
else {
	$format = $format . '_';
}
$show_cats = 'suf_post_' . $format . 'show_cats';
$show_posted_by = 'suf_post_' . $format . 'show_posted_by';
$show_tags = 'suf_post_' . $format . 'show_tags';
$show_comment = 'suf_post_' . $format . 'show_comment';
$show_perm = 'suf_post_' . $format . 'show_perm';
$with_title_show_perm = 'suf_post_' . $format . 'with_title_show_perm';

global $$show_cats, $$show_posted_by, $$show_tags, $$show_comment, $$show_perm, $$with_title_show_perm;
$post_show_cats = $$show_cats;
$post_show_posted_by = $$show_posted_by;
$post_show_tags = $$show_tags;
$post_show_comment = $$show_comment;
$post_show_perm = $$show_perm;
$post_with_title_show_perm = $$with_title_show_perm;

if (($suf_date_box_show != 'hide' || ($suf_date_box_show == 'hide-search' && !is_search())) || $post_show_cats != 'hide' ||
	$post_show_posted_by != 'hide' || $post_show_tags != 'hide' || $post_show_comment != 'hide' || $post_show_perm != 'hide' || $post_with_title_show_perm != 'hide') { ?>
<div class='postdata line'>
	<?php
		$title = get_the_title();
		if (($post_show_perm == 'show-tleft' || $post_show_perm == 'show-tright') && (($title == '' || !$title) || (!($title == '' || !$title) && $post_with_title_show_perm != 'hide'))) {
			$permalink_text = apply_filters('suffusion_permalink_text', __('Permalink', 'suffusion'));
			echo "<span class='permalink'><span class='icon'>&nbsp;</span>" . suffusion_get_post_title_and_link($permalink_text) . "</span>\n";
		}
	if ($suf_date_box_show != 'hide' || ($suf_date_box_show == 'hide-search' && !is_search())) {
		echo "<span class='line-date'><span class='icon'>&nbsp;</span>" . get_the_time(get_option('date_format')) . "</span>\n";
	}
	if ($post_show_posted_by != 'hide') {
		suffusion_print_author_byline();
	}
	if ($post_show_cats != 'hide') {
		$categories = get_the_category();
		if (is_array($categories) && count($categories) > 0) {
			echo '<span class="category"><span class="icon">&nbsp;</span>';
			the_category(', ');
			echo '</span>';
		}
	}
	if ($post_show_tags != 'hide') {
		$tags = get_the_tags();
		if (is_array($tags) && count($tags) > 0) {
			echo '<span class="tags"><span class="icon">&nbsp;</span>';
			the_tags('', ', ');
			echo '</span>';
		}
	}
	if (is_singular() && $post_show_comment != 'hide') {
		if ('open' == $post->comment_status) {
			if (is_attachment()) {
				$mime = get_post_mime_type();
				if (strpos($mime, '/') > -1) {
					$mime = substr($mime, 0, strpos($mime, '/'));
				}
				$comments_disabled_var = "suf_{$mime}_comments";
				global $$comments_disabled_var;
				if (isset($$comments_disabled_var)) {
					$comments_disabled = $$comments_disabled_var;
					if (!$comments_disabled) {
					}
				}
				else {
?>
			<span class="comments"><span class="icon">&nbsp;</span><a href="#respond"><?php _e('Add comments', 'suffusion'); ?></a></span>
<?php
				}
			}
			else {
?>
			<span class="comments"><span class="icon">&nbsp;</span><a href="#respond"><?php _e('Add comments', 'suffusion'); ?></a></span>
<?php
			}
		}
	}
	else if ($post_show_comment != 'hide') {
		echo "<span class='comments'><span class='icon'>&nbsp;</span>";
		comments_popup_link(__('No Responses', 'suffusion') . ' &#187;', __('1 Response', 'suffusion') . ' &#187;', __('% Responses', 'suffusion') . ' &#187;');
		echo "</span>";
	}

	if (get_edit_post_link() != '') {
		?>
		<span class="edit"><span class="icon">&nbsp;</span><?php edit_post_link(__('Edit', 'suffusion'), '', ''); ?></span>
		<?php

	}
	?>
</div>
	<?php
}