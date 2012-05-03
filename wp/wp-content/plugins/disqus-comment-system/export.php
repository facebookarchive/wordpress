<?php
/**
 * Adapted from WordPress 2.8
 */
@set_time_limit(0);
@ini_set('memory_limit', '256M');
define('WXR_VERSION', '1.0');

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $categories
 */
function dsq_export_wxr_missing_parents($categories) {
    if ( !is_array($categories) || empty($categories) )
        return array();

    foreach ( $categories as $category )
        $parents[$category->term_id] = $category->parent;

    $parents = array_unique(array_diff($parents, array_keys($parents)));

    if ( $zero = array_search('0', $parents) )
        unset($parents[$zero]);

    return $parents;
}

/**
 * Place string in CDATA tag.
 *
 * @since unknown
 *
 * @param string $str String to place in XML CDATA tag.
 */
function dsq_export_wxr_cdata($str) {
    if ( seems_utf8($str) == false )
        $str = utf8_encode($str);

    // $str = ent2ncr(esc_html($str));

    $str = "<![CDATA[$str" . ( ( substr($str, -1) == ']' ) ? ' ' : '') . "]]>";

    return $str;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return string Site URL.
 */
function dsq_export_wxr_site_url() {
    global $current_site;

    // mu: the base url
    if ( isset($current_site->domain) ) {
        return 'http://'.$current_site->domain.$current_site->path;
    }
    // wp: the blog url
    else {
        return get_bloginfo_rss('url');
    }
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param object $c Category Object
 */
function dsq_export_wxr_cat_name($c) {
    if ( empty($c->name) )
        return;

    echo '<wp:cat_name>' . dsq_export_wxr_cdata($c->name) . '</wp:cat_name>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param object $c Category Object
 */
function dsq_export_wxr_category_description($c) {
    if ( empty($c->description) )
        return;

    echo '<wp:category_description>' . dsq_export_wxr_cdata($c->description) . '</wp:category_description>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param object $t Tag Object
 */
function dsq_export_wxr_tag_name($t) {
    if ( empty($t->name) )
        return;

    echo '<wp:tag_name>' . dsq_export_wxr_cdata($t->name) . '</wp:tag_name>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param object $t Tag Object
 */
function dsq_export_wxr_tag_description($t) {
    if ( empty($t->description) )
        return;

    echo '<wp:tag_description>' . dsq_export_wxr_cdata($t->description) . '</wp:tag_description>';
}

// receives an array of posts to export
function dsq_export_wp($post, $comments=null) {
    global $wpdb;

    if (!$comments) {
        $comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_agent NOT LIKE 'Disqus/%%'", $post->ID) );
    }

    // start catching output
    ob_start();

    echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?' . ">\n";
?>
<?php the_generator('export');?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dsq="http://www.disqus.com/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/"
>

<channel>
    <title><?php bloginfo_rss('name'); ?></title>
    <link><?php bloginfo_rss('url') ?></link>
    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
    <generator>WordPress <?php bloginfo_rss('version'); ?>; Disqus <?php echo DISQUS_VERSION; ?></generator>
<?php
global $wp_query, $post;
$wp_query->in_the_loop = true;  // Fake being in the loop.
setup_postdata($post); ?>
<item>
<title><?php echo apply_filters('the_title_rss', $post->post_title); ?></title>
<link><?php the_permalink_rss() ?></link>
<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
<dc:creator><?php echo dsq_export_wxr_cdata(get_the_author()); ?></dc:creator>
<guid isPermaLink="false"><?php the_guid(); ?></guid>
<content:encoded><?php echo dsq_export_wxr_cdata( apply_filters('the_content_export', $post->post_content) ); ?></content:encoded>
<dsq:thread_identifier><?php echo dsq_identifier_for_post($post); ?></dsq:thread_identifier>
<wp:post_id><?php echo $post->ID; ?></wp:post_id>
<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
<?php
if ( $comments ) { foreach ( $comments as $c ) { ?>
<wp:comment>
<wp:comment_id><?php echo $c->comment_ID; ?></wp:comment_id>
<wp:comment_author><?php echo dsq_export_wxr_cdata($c->comment_author); ?></wp:comment_author>
<wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
<wp:comment_author_url><?php echo $c->comment_author_url; ?></wp:comment_author_url>
<wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
<wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
<wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
<wp:comment_content><?php echo dsq_export_wxr_cdata($c->comment_content) ?></wp:comment_content>
<wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
<wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
<wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
</wp:comment>
<?php } } // comments ?>
    </item>
</channel>
</rss>
<?php

    // end of WXR output
    $output = ob_get_clean();

    return $output;
}

?>
