#!/usr/bin/php
<?php
/**
 * Incrementally (in chunks of EXPORT_CHUNK_SIZE) exports all comments to DISQUS.
 *
 * ``php export-comments.php``
 */

require_once(dirname(__FILE__) . '/../lib/wp-cli.php');
require_once(dirname(__FILE__) . '/../disqus.php');
require_once(dirname(__FILE__) . '/../export.php');

define('EXPORT_CHUNK_SIZE', 100);

$forum_url = get_option('disqus_forum_url');

if (empty($forum_url)) {
    print_line("Disqus has not been configured on this installation!");
    die();
}

print_line('---------------------------------------------------------');
print_line('Discovered DISQUS forum shortname as %s', $forum_url);
print_line('---------------------------------------------------------');

global $wpdb, $dsq_api;

$timestamp = 0;
$post_id = 0;
$total = 0;
$eof = 0;
$total_exported = 0;
$global_start = microtime();

$max_post_id = $wpdb->get_var($wpdb->prepare("
    SELECT MAX(ID)
    FROM $wpdb->posts
    WHERE post_type != 'revision'
    AND post_status = 'publish'
    AND comment_count > 0
    AND ID > %d
", $post_id));

print_line('Max post id is %d', $max_post_id);

while ($post_id < $max_post_id) {
    $start = microtime();

    $post = $wpdb->get_results($wpdb->prepare("
        SELECT *
        FROM $wpdb->posts
        WHERE post_type != 'revision'
        AND post_status = 'publish'
        AND comment_count > 0
        AND ID > %d
        ORDER BY ID ASC
        LIMIT 1
    ", $post_id));
    $post = $post[0];
    $post_id = $post->ID;

    print_line('  Exporting comments for post id %d', $post_id);

    $response = null;
    $query = $wpdb->get_results( $wpdb->prepare("SELECT COUNT(*) as total FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_agent NOT LIKE 'Disqus/%%' LIMIT ".EXPORT_CHUNK_SIZE, $post_id) );
    $total_comments = $query[0]->total;

    $comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_agent NOT LIKE 'Disqus/%%' LIMIT ".EXPORT_CHUNK_SIZE, $post_id) );
    $group_id = null;
    $at = 0;

    // we need to send empty files to ensure EOF happens
    while (($at === 0 && $post_id == $max_post_id) || $at < $total_comments) {
        if ($post_id == $max_post_id && ($at + EXPORT_CHUNK_SIZE) >= $total_comments) {
            $eof = 1;
        }
        $wxr = dsq_export_wp($post, $comments);
        $response = $dsq_api->import_wordpress_comments($wxr, $timestamp, $eof);
        if (!($response['group_id'] > 0)) {
            print_line('---------------------------------------------------------');
            print_line('There was an error communicating with DISQUS!');
            print_line($dsq_api->get_last_error());
            print_line('---------------------------------------------------------');
        }
        $group_id = $response['group_id'];
        print_line('    %d comments exported', count($comments), $time);
        $total_exported += count($comments);
        $at += EXPORT_CHUNK_SIZE;
        $comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_agent NOT LIKE 'Disqus/%%' LIMIT ".EXPORT_CHUNK_SIZE." OFFSET {$at}", $post->ID) );
        // assuming the cache is the internal, reset it's value to empty to avoid
        // large memory consumption
        $wp_object_cache->cache = array();
    }

    $time = abs(microtime() - $start);
    print_line('    Done! (took %.2fs)', $time);
}
$total_time = abs(microtime() - $global_start);
print_line('---------------------------------------------------------');
print_line('Done (processing took %.2fs)! %d comments were sent to DISQUS', $total_time, $total_exported);
if ($group_id) {
    print_line('');
    print_line('Status available at http://import.disqus.com/group/%d/', $group_id);
}
print_line('---------------------------------------------------------');
?>