#!/usr/bin/php
<?php
/**
 * Incrementally downloads all comments from DISQUS.
 *
 * ``php import-comments.php``
 */

require_once(dirname(__FILE__) . '/../lib/wp-cli.php');
require_once(dirname(__FILE__) . '/../disqus.php');

$forum_url = get_option('disqus_forum_url');

if (empty($forum_url)) {
    print_line("Disqus has not been configured on this installation!");
    die();
}

print_line('---------------------------------------------------------');
print_line('Discovered DISQUS forum shortname as %s', $forum_url);
print_line('---------------------------------------------------------');

$imported = true;
if (in_array('--reset', $argv)) {
    $last_comment_id = 0;
} else {
    $last_comment_id = get_option('disqus_last_comment_id');
}
$force = (in_array('--force', $argv));
$total = 0;
$global_start = microtime();

$memory_usage = memory_get_peak_usage();
while ($imported) {
    print_line('  Importing chunk starting at comment id %d', $last_comment_id);
    $start = microtime();
    $result = dsq_sync_forum($last_comment_id, $force);
    if ($result === false) {
        print_line('---------------------------------------------------------');
        print_line('There was an error communicating with DISQUS!');
        print_line($dsq_api->get_last_error());
        print_line('---------------------------------------------------------');
        die();
        break;
    } else {
        list($imported, $last_comment_id) = $result;
    }
    $total += $imported;
    $time = abs(microtime() - $start);

    // assuming the cache is the internal, reset it's value to empty to avoid
    // large memory consump
    $wp_object_cache->cache = array();

    $new_memory_usage = memory_get_peak_usage();
    print_line('    %d comments imported (took %.2fs, memory increased by %db)', $imported, $time, ($new_memory_usage - $memory_usage));
    $memory_usage = $new_memory_usage;
}
$total_time = abs(microtime() - $global_start);
print_line('---------------------------------------------------------');
print_line('Done (took %.2fs)! %d comments imported from DISQUS', $total_time, $total);
print_line('---------------------------------------------------------');
?>