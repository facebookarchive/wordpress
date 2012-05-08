<?php
/**
 * Builds a pullout with meta information for a post. This file should not be loaded by itself, but should instead be included using get_template_part or locate_template.
 * Users can override this in a child theme. Note that if you want to have a differently styled pullout for a particular post format, you could call the file
 * pullout-<post-format>.php in your child theme. E.g. pullout-aside.php will apply to an aside post format.
 *
 * @since 3.8.3
 * @package Suffusion
 * @subpackage Custom
 */

global $post, $suf_date_box_show, $suffusion_cpt_post_id, $suffusion_cpt_layouts, $suf_cpt_bylines_post_date;
$format = suffusion_get_post_format();
if ($format == 'standard') {
	$format = '';
}
else {
	$format = $format . '_';
}

if (is_single() && $post->post_type != 'post') {
	$is_cpt = true;
}
else {
	$is_cpt = false;
}

$meta_position = 'suf_post_' . $format . 'meta_position';
$show_cats = 'suf_post_' . $format . 'show_cats';
$show_tags = 'suf_post_' . $format . 'show_tags';
$show_posted_by = (!isset($suffusion_cpt_post_id) && !$is_cpt) ? 'suf_post_' . $format . 'show_posted_by' : 'suf_cpt_bylines_posted_by';
$show_comment = (!isset($suffusion_cpt_post_id) && !$is_cpt) ? 'suf_post_' . $format . 'show_comment' : 'suf_cpt_bylines_comments';
$show_perm = (!isset($suffusion_cpt_post_id) && !$is_cpt) ? 'suf_post_' . $format . 'show_perm' : 'suf_cpt_bylines_permalinks';
$with_title_show_perm = 'suf_post_' . $format . 'with_title_show_perm';

global $$meta_position, $$show_cats, $$show_posted_by, $$show_tags, $$show_comment, $$show_perm, $$with_title_show_perm;
$post_meta_position = apply_filters('suffusion_byline_position', $$meta_position);
$post_show_cats = $$show_cats;
$post_show_posted_by = $$show_posted_by;
$post_show_tags = $$show_tags;
$post_show_comment = $$show_comment;
$post_show_perm = $$show_perm;
$post_with_title_show_perm = $$with_title_show_perm;
$show_date = (!isset($suffusion_cpt_post_id) && !$is_cpt) ? ($suf_date_box_show != 'hide' || ($suf_date_box_show == 'hide-search' && !is_search())) : $suf_cpt_bylines_post_date;

$post_meta_position = apply_filters('suffusion_pullout_position', $post_meta_position);
if ($post_meta_position == 'corners') {
	return;
}

echo "<div class='meta-pullout meta-$post_meta_position'>\n";
echo "<ul>\n";

if ($show_date && $show_date != 'hide') {
	echo "<li><span class='pullout-date'><span class='icon'>&nbsp;</span>" . get_the_time(get_option('date_format')) . "</span></li>\n";
}

$title = get_the_title();
if (($post_show_perm && $post_show_perm != 'hide' && (isset($suffusion_cpt_post_id) || $is_cpt)) ||
		(!isset($suffusion_cpt_post_id) && !$is_cpt && $post_show_perm != 'hide' && (($title == '' || !$title) || (!($title == '' || !$title) && $post_with_title_show_perm != 'hide')))) {
	echo "<li>";
	$permalink_text = apply_filters('suffusion_permalink_text', __('Permalink', 'suffusion'));
	echo "<span class='permalink'><span class='icon'>&nbsp;</span> " . suffusion_get_post_title_and_link($permalink_text) . "</span>";
	echo "</li>\n";
}

if ($post_show_posted_by && $post_show_posted_by != 'hide') {
	echo "<li>";
	suffusion_print_author_byline();
	echo "</li>\n";
}

if ($post_show_comment && $post_show_comment != 'hide') {
	if ('open' == $post->comment_status && is_singular()) {
		echo "<li><span class='comments'><span class='icon'>&nbsp;</span> <a href='#respond'>" . __('Add comments', 'suffusion') . "</a></span></li>\n";
	}
	else {
		echo "<li><span class='comments'><span class='icon'>&nbsp;</span> ";
		comments_popup_link(__('No Responses', 'suffusion'), __('1 Response', 'suffusion'), __('% Responses', 'suffusion'));
		echo "</span></li>\n";
	}
}

if ($post_show_cats != 'hide') {
	$categories = get_the_category($post->ID);
	if ($categories) {
		echo "<li><span class='category'><span class='icon'>&nbsp;</span> ";
		the_category(', ');
		echo "</span></li>\n";
	}
}

if ($post_show_tags != 'hide') {
	$tags = get_the_tags($post->ID);
	if ($tags != '') {
		echo "<li><span class='tags'><span class='icon'>&nbsp;</span>";
		the_tags('', ', ');
		echo "</span></li>\n";
	}
}

if (isset($suffusion_cpt_post_id) || $is_cpt) {
	do_action('suffusion_add_taxonomy_bylines_pullout', $suffusion_cpt_post_id, $is_cpt, '<li>', '</li>');
}

if (get_edit_post_link() != '') {
	echo "<li><span class='edit'><span class='icon'>&nbsp;</span> ";
	?>
<?php edit_post_link(__('Edit', 'suffusion'), '', ''); ?>
<?php
		echo "</span></li>\n";
}

echo "</ul>\n";
echo "</div>\n";
?>