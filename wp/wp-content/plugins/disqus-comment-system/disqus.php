<?php
/*
Plugin Name: Disqus Comment System
Plugin URI: http://disqus.com/
Description: The Disqus comment system replaces your WordPress comment system with your comments hosted and powered by Disqus. Head over to the Comments admin page to set up your DISQUS Comment System.
Author: Disqus <team@disqus.com>
Version: 2.72
Author URI: http://disqus.com/
*/

/*.
    require_module 'standard';
    require_module 'pcre';
    require_module 'mysql';
.*/

require_once(dirname(__FILE__) . '/lib/wp-api.php');

if (defined('DISQUS_LOCAL')) { // DISQUS defines this for local development purposes
    define('DISQUS_DOMAIN',         'dev.disqus.org:8000');
    define('DISQUS_IMPORTER_URL',   'http://dev.disqus.org:8001/');
} else {
    define('DISQUS_DOMAIN',         'disqus.com');
    define('DISQUS_IMPORTER_URL',   'http://import.disqus.com/');
}
define('DISQUS_URL',                'http://' . DISQUS_DOMAIN . '/');
define('DISQUS_MEDIA_URL',          'http://' . DISQUS_DOMAIN . '/media/');
define('DISQUS_API_URL',            'http://' . DISQUS_DOMAIN . '/api/');
define('DISQUS_RSS_PATH',           '/latest.rss');
define('DISQUS_CAN_EXPORT',         is_file(dirname(__FILE__) . '/export.php'));
if (!defined('DISQUS_DEBUG')) {
    define('DISQUS_DEBUG',          false);
}
define('DISQUS_VERSION',            '2.72');
define('DISQUS_SYNC_TIMEOUT',       30);

/**
 * Returns an array of all option identifiers used by DISQUS.
 * @return array[int]string
 */
function dsq_options() {
    return array(
        '_disqus_sync_lock',
        '_disqus_sync_post_ids',
        # render disqus in the embed
        'disqus_active',
        'disqus_public_key',
        'disqus_secret_key',
        'disqus_forum_url',
        'disqus_api_key',
        'disqus_user_api_key',
        'disqus_partner_key',
        'disqus_replace',
        'disqus_cc_fix',
        # disables automatic sync via cron
        'disqus_manual_sync',
        # disables server side rendering
        'disqus_disable_ssr',
        # the last sync comment id (from get_forum_posts)
        'disqus_last_comment_id',
        'disqus_version',
    );
}

/**
 * @param string $file
 * @return string
 */
function dsq_plugin_basename($file) {
    $file = dirname($file);

    // From WP2.5 wp-includes/plugin.php:plugin_basename()
    $file = str_replace('\\','/',$file); // sanitize for Win32 installs
    $file = preg_replace('|/+|','/', $file); // remove any duplicate slash
    $file = preg_replace('|^.*/' . PLUGINDIR . '/|','',$file); // get relative path from plugins dir

    if ( strstr($file, '/') === false ) {
        return $file;
    }

    $pieces = explode('/', $file);
    return !empty($pieces[count($pieces)-1]) ? $pieces[count($pieces)-1] : $pieces[count($pieces)-2];
}

if ( !defined('WP_CONTENT_URL') ) {
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}
if ( !defined('PLUGINDIR') ) {
    define('PLUGINDIR', 'wp-content/plugins'); // Relative to ABSPATH.  For back compat.
}

define('DSQ_PLUGIN_URL', WP_CONTENT_URL . '/plugins/' . dsq_plugin_basename(__FILE__));

$mt_disqus_version = '2.01';
/**
 * Response from Disqus get_thread API call for comments template.
 *
 * @global    string    $dsq_response
 * @since    1.0
 */
$dsq_response = '';
/**
 * Disqus API instance.
 *
 * @global    string    $dsq_api
 * @since    1.0
 */
$dsq_api = new DisqusWordPressAPI(get_option('disqus_forum_url'), get_option('disqus_api_key'));

/**
 * DISQUS currently unsupported dev toggle to output comments for this query.
 *
 * @global    bool    $DSQ_QUERY_COMMENTS
 * @since    ?
 */
$DSQ_QUERY_COMMENTS = false;

/**
 * DISQUS array to store post_ids from WP_Query for comment JS output.
 *
 * @global    array    $DSQ_QUERY_POST_IDS
 * @since    2.2
 */
$DSQ_QUERY_POST_IDS = array();

/**
 * Helper functions.
 */

/**
 * Tests if required options are configured to display the Disqus embed.
 * @return bool
 */
function dsq_is_installed() {
    return get_option('disqus_forum_url') && get_option('disqus_api_key');
}

/**
 * @return bool
 */
function dsq_can_replace() {
    global $id, $post;

    if (get_option('disqus_active') === '0'){ return false; }

    $replace = get_option('disqus_replace');

    if ( is_feed() )                       { return false; }
    if ( 'draft' == $post->post_status )   { return false; }
    if ( !get_option('disqus_forum_url') ) { return false; }
    else if ( 'all' == $replace )          { return true; }

    if ( !isset($post->comment_count) ) {
        $num_comments = 0;
    } else {
        if ( 'empty' == $replace ) {
            // Only get count of comments, not including pings.

            // If there are comments, make sure there are comments (that are not track/pingbacks)
            if ( $post->comment_count > 0 ) {
                // Yuck, this causes a DB query for each post.  This can be
                // replaced with a lighter query, but this is still not optimal.
                $comments = get_approved_comments($post->ID);
                foreach ( $comments as $comment ) {
                    if ( $comment->comment_type != 'trackback' && $comment->comment_type != 'pingback' ) {
                        $num_comments++;
                    }
                }
            } else {
                $num_comments = 0;
            }
        }
        else {
            $num_comments = $post->comment_count;
        }
    }

    return ( ('empty' == $replace && 0 == $num_comments)
        || ('closed' == $replace && 'closed' == $post->comment_status) );
}

function dsq_manage_dialog($message, $error = false) {
    global $wp_version;

    echo '<div '
        . ( $error ? 'id="disqus_warning" ' : '')
        . 'class="updated fade'
        . ( (version_compare($wp_version, '2.5', '<') && $error) ? '-ff0000' : '' )
        . '"><p><strong>'
        . $message
        . '</strong></p></div>';
}

function dsq_sync_comments($comments) {
    global $wpdb;

    // user MUST be logged out during this process
    wp_set_current_user(0);

    // we need the thread_ids so we can map them to posts
    $thread_map = array();
    foreach ( $comments as $comment ) {
        $thread_map[$comment->thread->id] = null;
    }
    $thread_ids = "'" . implode("', '", array_keys($thread_map)) . "'";

    $results = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'dsq_thread_id' AND meta_value IN ({$thread_ids}) LIMIT 1");
    foreach ( $results as $result ) {
        $thread_map[$result->meta_value] = $result->post_id;
    }
    unset($result);

    foreach ( $comments as $comment ) {
        $ts = strtotime($comment->created_at);
        if (!$thread_map[$comment->thread->id] && !empty($comment->thread->identifier)) {
            // legacy threads dont already have their meta stored
            foreach ( $comment->thread->identifier as $identifier ) {
                // we know identifier starts with post_ID
                if ($post_ID = (int)substr($identifier, 0, strpos($identifier, ' '))) {
                    $thread_map[$comment->thread->id] = $post_ID;
                    update_post_meta($post_ID, 'dsq_thread_id', $comment->thread->id);
                    if (DISQUS_DEBUG) {
                        echo "updated post {$post_ID}: dsq_thread_id set to {$comment->thread->id}\n";
                    }
                }
            }
            unset($identifier);
        }
        if (!$thread_map[$comment->thread->id]) {
            // shouldn't ever happen, but we can't be certain
            if (DISQUS_DEBUG) {
                if (!empty($comment->thread->identifier)) {
                    $idents = implode(', ', $comment->thread->identifier);
                    echo "skipped {$comment->id}: missing thread for identifiers ({$idents})\n";
                } else {
                    echo "skipped {$comment->id}: missing thread (no identifier)\n";
                }
            }
            continue;
        }
        $results = $wpdb->get_results($wpdb->prepare("SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'dsq_post_id' AND meta_value = %s LIMIT 1", $comment->id));
        if (count($results)) {
            // already exists
            if (DISQUS_DEBUG) {
                echo "skipped {$comment->id}: comment already exists\n";
            }
            if (count($results) > 1) {
                // clean up duplicates -- fixes an issue where a race condition allowed comments to be synced multiple times
                $results = array_slice($results, 1);
                foreach ($results as $result) {
                    $wpdb->prepare("DELETE FROM $wpdb->commentmeta WHERE comment_id = %s LIMIT 1", $result);
                }
            }
            continue;
        }

        $commentdata = false;

        // first lets check by the id we have stored
        if ($comment->meta) {
            $meta = explode(';', $comment->meta);
            foreach ($meta as $value) {
                $value = explode('=', $value);
                $meta[$value[0]] = $value[1];
            }
            unset($value);
            if ($meta['wp_id']) {
                $commentdata = $wpdb->get_row($wpdb->prepare( "SELECT comment_ID, comment_parent FROM $wpdb->comments WHERE comment_ID = %s LIMIT 1", $meta['wp_id']), ARRAY_A);
            }
        }

        // skip comments that were imported but are missing meta information
        if (!$commentdata && $comment->imported) {
            if (DISQUS_DEBUG) {
                echo "skipped {$comment->id}: comment not found and marked as imported\n";
            }
            continue;
        }

        // and follow up using legacy Disqus agent
        if (!$commentdata) {
            $commentdata = $wpdb->get_row($wpdb->prepare( "SELECT comment_ID, comment_parent FROM $wpdb->comments WHERE comment_agent = 'Disqus/1.0:{$comment->id}' LIMIT 1"), ARRAY_A);
        }
        if (!$commentdata) {
            // Comment doesnt exist yet, lets insert it
            if ($comment->status == 'approved') {
                $approved = 1;
            } elseif ($comment->status == 'spam') {
                $approved = 'spam';
            } else {
                $approved = 0;
            }
            $commentdata = array(
                'comment_post_ID' => $thread_map[$comment->thread->id],
                'comment_date' => date('Y-m-d\TH:i:s', strtotime($comment->created_at) + (get_option('gmt_offset') * 3600)),
                'comment_date_gmt' => $comment->created_at,
                'comment_content' => apply_filters('pre_comment_content', $comment->message),
                'comment_approved' => $approved,
                'comment_agent' => 'Disqus/1.1('.DISQUS_VERSION.'):'.intval($comment->id),
                'comment_type' => '',
            );
            if ($comment->is_anonymous) {
                $commentdata['comment_author'] = $comment->anonymous_author->name;
                $commentdata['comment_author_email'] = $comment->anonymous_author->email;
                $commentdata['comment_author_url'] = $comment->anonymous_author->url;
                $commentdata['comment_author_IP'] = $comment->ip_address;
            } else {
                if (!empty($comment->author->display_name)) {
                    $commentdata['comment_author'] = $comment->author->display_name;
                } else {
                    $commentdata['comment_author'] = $comment->author->username;
                }
                $commentdata['comment_author_email'] = $comment->author->email;
                $commentdata['comment_author_url'] = $comment->author->url;
                $commentdata['comment_author_IP'] = $comment->ip_address;
            }
            $commentdata = wp_filter_comment($commentdata);
            if ($comment->parent_post) {
                $parent_id = $wpdb->get_var($wpdb->prepare( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'dsq_post_id' AND meta_value = %s LIMIT 1", $comment->parent_post));
                if ($parent_id) {
                    $commentdata['comment_parent'] = $parent_id;
                }
            }

            // due to a race condition we need to test again for coment existance
            if ($wpdb->get_row($wpdb->prepare( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'dsq_post_id' AND meta_value = %s LIMIT 1", $comment->id))) {
                // already exists
                if (DISQUS_DEBUG) {
                    echo "skipped {$comment->id}: comment already exists (second check)\n";
                }
                continue;
            }

            $commentdata['comment_ID'] = wp_insert_comment($commentdata);
            if (DISQUS_DEBUG) {
                echo "inserted {$comment->id}: id is {$commentdata[comment_ID]}\n";
            }
        }
        if (!$commentdata['comment_parent'] && $comment->parent_post) {
            $parent_id = $wpdb->get_var($wpdb->prepare( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'dsq_post_id' AND meta_value = %s LIMIT 1", $comment->parent_post));
            if ($parent_id) {
                $wpdb->query($wpdb->prepare( "UPDATE $wpdb->comments SET comment_parent = %s WHERE comment_id = %s", $parent_id, $commentdata['comment_ID']));
                if (DISQUS_DEBUG) {
                    echo "updated {$comment->id}: comment_parent changed to {$parent_id}\n";
                }

            }
        }
        $comment_id = $commentdata['comment_ID'];
        update_comment_meta($comment_id, 'dsq_parent_post_id', $comment->parent_post);
        update_comment_meta($comment_id, 'dsq_post_id', $comment->id);
    }
    unset($comment);

    if( isset($_POST['dsq_api_key']) && $_POST['dsq_api_key'] == get_option('disqus_api_key') ) {
        if( isset($_GET['dsq_sync_action']) && isset($_GET['dsq_sync_comment_id']) ) {
            $comment_parts = explode('=', $_GET['dsq_sync_comment_id']);

            if (!($comment_id = intval($comment_parts[1])) > 0) {
                return;
            }

            if( 'wp_id' != $comment_parts[0] ) {
                $comment_id = $wpdb->get_var($wpdb->prepare('SELECT comment_ID FROM ' . $wpdb->comments . ' WHERE comment_post_ID = %d AND comment_agent LIKE %s', intval($post->ID), 'Disqus/1.0:' . $comment_id));
            }

            switch( $_GET['dsq_sync_action'] ) {
                case 'mark_spam':
                    wp_set_comment_status($comment_id, 'spam');
                    echo "<!-- dsq_sync: wp_set_comment_status($comment_id, 'spam') -->";
                    break;
                case 'mark_approved':
                    wp_set_comment_status($comment_id, 'approve');
                    echo "<!-- dsq_sync: wp_set_comment_status($comment_id, 'approve') -->";
                    break;
                case 'mark_killed':
                    wp_set_comment_status($comment_id, 'hold');
                    echo "<!-- dsq_sync: wp_set_comment_status($comment_id, 'hold') -->";
                    break;
            }
        }
    }
}

function dsq_request_handler() {
    global $dsq_response;
    global $dsq_api;
    global $post;
    global $wpdb;

    if (!empty($_GET['cf_action'])) {
        switch ($_GET['cf_action']) {
            case 'sync_comments':
                if( !( $post_id = $_GET['post_id'] ) ) {
                    header("HTTP/1.0 400 Bad Request");
                    die();
                }
                // schedule the event for 5 minutes from now in case they
                // happen to make a quick post
                dsq_add_pending_post_id($post_id);

                if (DISQUS_DEBUG) {
                    $response = dsq_sync_forum();
                    if (!$response) {
                        die('// error: '.$dsq_api->get_last_error());
                    } else {
                        list($last_comment_id, $comments) = $response;
                        die('// synced '.$comments.' comments');
                    }
                } else {
                    $ts = time() + 300;
                    wp_schedule_single_event($ts, 'dsq_sync_forum');
                    die('// sync scheduled');
                }
            break;
            case 'export_comments':
                if (current_user_can('manage_options') && DISQUS_CAN_EXPORT) {
                    $timestamp = intval($_GET['timestamp']);
                    $post_id = intval($_GET['post_id']);
                    global $wpdb, $dsq_api;
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
                    $max_post_id = $wpdb->get_var($wpdb->prepare("
                        SELECT MAX(ID)
                        FROM $wpdb->posts
                        WHERE post_type != 'revision'
                        AND post_status = 'publish'
                        AND comment_count > 0
                    ", $post_id));
                    $eof = (int)($post_id == $max_post_id);
                    if ($eof) {
                        $status = 'complete';
                        $msg = 'Your comments have been sent to Disqus and queued for import!<br/><a href="'.DISQUS_IMPORTER_URL.'" target="_blank">See the status of your import at Disqus</a>';
                    }
                    else {
                        $status = 'partial';
                        $msg = dsq_i('Processed comments on post #%s&hellip;', $post_id);
                    }
                    $result = 'fail';
                    $response = null;
                    if ($post) {
                        require_once(dirname(__FILE__) . '/export.php');
                        $wxr = dsq_export_wp($post);
                        $response = $dsq_api->import_wordpress_comments($wxr, $timestamp, $eof);
                        if (!($response['group_id'] > 0)) {
                            $result = 'fail';
                            $msg = '<p class="status dsq-export-fail">'. dsq_i('Sorry, something unexpected happened with the export. Please <a href="#" id="dsq_export_retry">try again</a></p><p>If your API key has changed, you may need to reinstall Disqus (deactivate the plugin and then reactivate it). If you are still having issues, refer to the <a href="%s" onclick="window.open(this.href); return false">WordPress help page</a>.', 'http://disqus.com/help/wordpress'). '</p>';
                            $response = $dsq_api->get_last_error();
                        }
                        else {
                            if ($eof) {
                                $msg = dsq_i('Your comments have been sent to Disqus and queued for import!<br/><a href="%s" target="_blank">See the status of your import at Disqus</a>', $response['link']);

                            }
                            $result = 'success';
                        }
                    }
// send AJAX response
                    $response = compact('result', 'timestamp', 'status', 'post_id', 'msg', 'eof', 'response');
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'import_comments':
                if (current_user_can('manage_options')) {
                    if (!isset($_GET['last_comment_id'])) $last_comment_id = false;
                    else $last_comment_id = $_GET['last_comment_id'];

                    if ($_GET['wipe'] == '1') {
                        $wpdb->query("DELETE FROM `".$wpdb->prefix."commentmeta` WHERE meta_key IN ('dsq_post_id', 'dsq_parent_post_id')");
                        $wpdb->query("DELETE FROM `".$wpdb->prefix."comments` WHERE comment_agent LIKE 'Disqus/%%'");
                    }

                    ob_start();
                    $response = dsq_sync_forum($last_comment_id, true);
                    $debug = ob_get_clean();
                    if (!$response) {
                        $status = 'error';
                        $result = 'fail';
                        $error = $dsq_api->get_last_error();
                        $msg = '<p class="status dsq-export-fail">'.dsq_i('There was an error downloading your comments from Disqus.').'<br/>'.htmlspecialchars($error).'</p>';
                    } else {
                        list($comments, $last_comment_id) = $response;
                        if (!$comments) {
                            $status = 'complete';
                            $msg = dsq_i('Your comments have been downloaded from Disqus and saved in your local database.');
                        } else {
                            $status = 'partial';
                            $msg = dsq_i('Import in progress (last post id: %s) &hellip;', $last_comment_id);
                        }
                        $result = 'success';
                    }
                    $debug = explode("\n", $debug);
                    $response = compact('result', 'status', 'comments', 'msg', 'last_comment_id', 'debug');
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
        }
    }
}

add_action('init', 'dsq_request_handler');

function dsq_add_pending_post_id($post_id) {
    update_post_meta($post_id, 'dsq_needs_sync', '1', $unique=true);
}

function dsq_get_pending_post_ids() {
    global $wpdb;

    $results = $wpdb->get_results( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'dsq_needs_sync'");
    $post_ids = array();
    foreach ($results as $result) {
        $post_ids[] = $result->post_id;
    }
    return $post_ids;
}

function dsq_clear_pending_post_ids($post_ids) {
    global $wpdb;

    $post_ids_query = "'" . implode("', '", $post_ids) . "'";
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'dsq_needs_sync' AND post_id IN ({$post_ids_query})");

    update_meta_cache('dsq_needs_sync', $post_ids);
}


function dsq_sync_post($post_id) {
    global $dsq_api, $wpdb;

    $post = get_post($post_id);

    // Call update_thread to ensure our permalink is up to date
    dsq_update_permalink($post);
}

function dsq_sync_forum($last_comment_id=false, $force=false) {
    global $dsq_api, $wpdb;

    set_time_limit(DISQUS_SYNC_TIMEOUT);

    if ($force) {
        $sync_time = null;
    } else {
        $sync_time = (int)get_option('_disqus_sync_lock');
    }

    // lock expires after 1 hour
    if ($sync_time && $sync_time > time() - 60*60) {
        $dsq_api->api->last_error = 'Sync already in progress (lock found)';
        return false;
    } else {
        update_option('_disqus_sync_lock', time());
    }

    // sync all pending posts
    $post_ids = dsq_get_pending_post_ids();
    dsq_clear_pending_post_ids($post_ids);

    foreach ($post_ids as $post_id) {
        dsq_sync_post($post_id);
    }

    if ($last_comment_id === false) {
        $last_comment_id = get_option('disqus_last_comment_id');
        if (!$last_comment_id) {
            $last_comment_id = 0;
        }
    }
    if ($last_comment_id) {
        $last_comment_id++;
    }

    //$last_comment_id = 0;

    // Pull comments from API
    $dsq_response = $dsq_api->get_forum_posts($last_comment_id);
    if( $dsq_response < 0 || $dsq_response === false ) {
        return false;
    }

    // Sync comments with database.
    dsq_sync_comments($dsq_response);
    $total = 0;
    if ($dsq_response) {
        foreach ($dsq_response as $comment) {
            $total += 1;
            if ($comment->id > $last_comment_id) $last_comment_id = $comment->id;
        }
        if ($last_comment_id > get_option('disqus_last_comment_id')) {
            update_option('disqus_last_comment_id', $last_comment_id);
        }
    }
    unset($comment);
    delete_option('_disqus_sync_lock');
    return array($total, $last_comment_id);
}

add_action('dsq_sync_forum', 'dsq_sync_forum');

function dsq_update_permalink($post) {
    global $dsq_api;

    if (DISQUS_DEBUG) {
        echo "updating post on disqus: {$post->ID}\n";
    }

    $response = $dsq_api->api->update_thread(null, array(
        'thread_identifier'    => dsq_identifier_for_post($post),
        'title' => dsq_title_for_post($post),
        'url' => dsq_link_for_post($post)
    ));

    update_post_meta($post->ID, 'dsq_thread_id', $response->id);

    return $response;
}

/**
 *  Compatibility
 */

if (!function_exists ( '_wp_specialchars' ) ) {
function _wp_specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false ) {
    $string = (string) $string;

    if ( 0 === strlen( $string ) ) {
        return '';
    }

    // Don't bother if there are no specialchars - saves some processing
    if ( !preg_match( '/[&<>"\']/', $string ) ) {
        return $string;
    }

    // Account for the previous behaviour of the function when the $quote_style is not an accepted value
    if ( empty( $quote_style ) ) {
        $quote_style = ENT_NOQUOTES;
    } elseif ( !in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
        $quote_style = ENT_QUOTES;
    }

    // Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
    if ( !$charset ) {
        static $_charset;
        if ( !isset( $_charset ) ) {
            $alloptions = wp_load_alloptions();
            $_charset = isset( $alloptions['blog_charset'] ) ? $alloptions['blog_charset'] : '';
        }
        $charset = $_charset;
    }
    if ( in_array( $charset, array( 'utf8', 'utf-8', 'UTF8' ) ) ) {
        $charset = 'UTF-8';
    }

    $_quote_style = $quote_style;

    if ( $quote_style === 'double' ) {
        $quote_style = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ( $quote_style === 'single' ) {
        $quote_style = ENT_NOQUOTES;
    }

    // Handle double encoding ourselves
    if ( !$double_encode ) {
        $string = wp_specialchars_decode( $string, $_quote_style );
        $string = preg_replace( '/&(#?x?[0-9a-z]+);/i', '|wp_entity|$1|/wp_entity|', $string );
    }

    $string = @htmlspecialchars( $string, $quote_style, $charset );

    // Handle double encoding ourselves
    if ( !$double_encode ) {
        $string = str_replace( array( '|wp_entity|', '|/wp_entity|' ), array( '&', ';' ), $string );
    }

    // Backwards compatibility
    if ( 'single' === $_quote_style ) {
        $string = str_replace( "'", '&#039;', $string );
    }

    return $string;
}
}

if (!function_exists ( 'wp_check_invalid_utf8' ) ) {
function wp_check_invalid_utf8( $string, $strip = false ) {
    $string = (string) $string;

    if ( 0 === strlen( $string ) ) {
        return '';
    }

    // Store the site charset as a static to avoid multiple calls to get_option()
    static $is_utf8;
    if ( !isset( $is_utf8 ) ) {
        $is_utf8 = in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
    }
    if ( !$is_utf8 ) {
        return $string;
    }

    // Check for support for utf8 in the installed PCRE library once and store the result in a static
    static $utf8_pcre;
    if ( !isset( $utf8_pcre ) ) {
        $utf8_pcre = @preg_match( '/^./u', 'a' );
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases
    if ( !$utf8_pcre ) {
        return $string;
    }

    // preg_match fails when it encounters invalid UTF8 in $string
    if ( 1 === @preg_match( '/^./us', $string ) ) {
        return $string;
    }

    // Attempt to strip the bad chars if requested (not recommended)
    if ( $strip && function_exists( 'iconv' ) ) {
        return iconv( 'utf-8', 'utf-8', $string );
    }

    return '';
}
}

if (!function_exists ( 'esc_html' ) ) {
function esc_html( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
    $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
    return apply_filters( 'esc_html', $safe_text, $text );
}
}

if (!function_exists ( 'esc_attr' ) ) {
function esc_attr( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
    $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
    return apply_filters( 'attribute_escape', $safe_text, $text );
}
}

/**
 *  Filters/Actions
 */

// ugly global hack for comments closing
$EMBED = false;
function dsq_comments_template($value) {
    global $EMBED;
    global $post;
    global $comments;

    if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
        return;
    }

    if ( !dsq_is_installed() || !dsq_can_replace() ) {
        return $value;
    }

    // TODO: If a disqus-comments.php is found in the current template's
    // path, use that instead of the default bundled comments.php
    //return TEMPLATEPATH . '/disqus-comments.php';
    $EMBED = true;
    return dirname(__FILE__) . '/comments.php';
}

function dsq_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ($comment->comment_type):
        case '' :
    ?>
    <li <?php comment_class(); ?> id="dsq-comment-<?php echo comment_ID(); ?>">
        <div id="dsq-comment-header-<?php echo comment_ID(); ?>" class="dsq-comment-header">
            <cite id="dsq-cite-<?php echo comment_ID(); ?>">
<?php if(comment_author_url()) : ?>
                <a id="dsq-author-user-<?php echo comment_ID(); ?>" href="<?php echo comment_author_url(); ?>" target="_blank" rel="nofollow"><?php echo comment_author(); ?></a>
<?php else : ?>
                <span id="dsq-author-user-<?php echo comment_ID(); ?>"><?php echo comment_author(); ?></span>
<?php endif; ?>
            </cite>
        </div>
        <div id="dsq-comment-body-<?php echo comment_ID(); ?>" class="dsq-comment-body">
            <div id="dsq-comment-message-<?php echo comment_ID(); ?>" class="dsq-comment-message"><?php echo wp_filter_kses(comment_text()); ?></div>
        </div>
    </li>

    <?php
            break;
        case 'pingback'  :
        case 'trackback' :
    ?>
    <li class="post pingback">
        <p><?php echo dsq_i('Pingback:'); ?> <?php comment_author_link(); ?><?php edit_comment_link(dsq_i('(Edit)'), ' '); ?></p>
    </li>
    <?php
            break;
    endswitch;
}

// Mark entries in index to replace comments link.
// As of WordPress 3.1 this is required to return a numerical value
function dsq_comments_number($count) {
    global $post;

    return $count;
}

function dsq_comments_text($comment_text) {
    global $post;

    if ( dsq_can_replace() ) {
        return '<span class="dsq-postid" rel="'.htmlspecialchars(dsq_identifier_for_post($post)).'">'.$comment_text.'</span>';
    } else {
        return $comment_text;
    }
}

function dsq_bloginfo_url($url) {
    if ( get_feed_link('comments_rss2') == $url && dsq_can_replace() ) {
        return 'http://' . strtolower(get_option('disqus_forum_url')) . '.' . DISQUS_DOMAIN . DISQUS_RSS_PATH;
    } else {
        return $url;
    }
}

function dsq_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="edit-comments.php?page=disqus#adv">'.dsq_i('Settings').'</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'dsq_plugin_action_links', 10, 2);

/**
 * Hide the default comment form to stop spammers by marking all comments
 * as closed.
 */
function dsq_comments_open($open, $post_id=null) {
    global $EMBED;
    if ($EMBED) return false;
    return $open;
}
add_filter('comments_open', 'dsq_comments_open');

// Always add Disqus management page to the admin menu
function dsq_add_pages() {
     add_submenu_page(
         'edit-comments.php',
         'Disqus',
         'Disqus',
         'moderate_comments',
         'disqus',
         'dsq_manage'
     );
}
add_action('admin_menu', 'dsq_add_pages', 10);

// a little jQuery goodness to get comments menu working as desired
function dsq_menu_admin_head() {
?>
<script type="text/javascript">
jQuery(function($) {
    // fix menu
    var mc = $('#menu-comments');
    mc.find('a.wp-has-submenu').attr('href', 'edit-comments.php?page=disqus').end().find('.wp-submenu  li:has(a[href="edit-comments.php?page=disqus"])').prependTo(mc.find('.wp-submenu ul'));
});
</script>
<?php
}
add_action('admin_head', 'dsq_menu_admin_head');

// only active on dashboard
function dsq_dash_comment_counts() {
    global $wpdb;
// taken from wp-includes/comment.php - WP 2.8.5
    $count = $wpdb->get_results("
        SELECT comment_approved, COUNT( * ) AS num_comments
        FROM {$wpdb->comments}
        WHERE comment_type != 'trackback'
        AND comment_type != 'pingback'
        GROUP BY comment_approved
    ", ARRAY_A );
    $total = 0;
    $approved = array('0' => 'moderated', '1' => 'approved', 'spam' => 'spam');
    $known_types = array_keys( $approved );
    foreach( (array) $count as $row_num => $row ) {
        $total += $row['num_comments'];
        if ( in_array( $row['comment_approved'], $known_types ) )
            $stats[$approved[$row['comment_approved']]] = $row['num_comments'];
    }

    $stats['total_comments'] = $total;
    foreach ( $approved as $key ) {
        if ( empty($stats[$key]) )
            $stats[$key] = 0;
    }
    $stats = (object) $stats;
?>
<style type="text/css">
#dashboard_right_now .inside,
#dashboard_recent_comments div.trackback {
    display: none;
}
</style>
<script type="text/javascript">
jQuery(function($) {
    $('#dashboard_right_now').find('.b-comments a').html('<?php echo number_format($stats->total_comments); ?>').end().find('.b_approved a').html('<?php echo number_format($stats->approved); ?>').end().find('.b-waiting a').html('<?php echo number_format($stats->moderated); ?>').end().find('.b-spam a').html('<?php echo number_format($stats->spam); ?>').end().find('.inside').slideDown();
    $('#dashboard_recent_comments div.trackback').remove();
    $('#dashboard_right_now .inside table td.last a, #dashboard_recent_comments .inside .textright a.button').attr('href', 'edit-comments.php?page=disqus');
});
</script>
<?php
}
function dsq_wp_dashboard_setup() {
    add_action('admin_head', 'dsq_dash_comment_counts');
}
add_action('wp_dashboard_setup', 'dsq_wp_dashboard_setup');

function dsq_manage() {
    if (dsq_does_need_update() && isset($_POST['upgrade'])) {
        dsq_install();
    }

    if (dsq_does_need_update() && isset($_POST['uninstall'])) {
        include_once(dirname(__FILE__) . '/upgrade.php');
    } else {
        include_once(dirname(__FILE__) . '/manage.php');
    }
}

function dsq_admin_head() {
    if (isset($_GET['page']) && $_GET['page'] == 'disqus') {
?>
<link rel='stylesheet' href='<?php echo DSQ_PLUGIN_URL; ?>/media/styles/manage.css' type='text/css' />
<style type="text/css">
.dsq-importing, .dsq-imported, .dsq-import-fail, .dsq-exporting, .dsq-exported, .dsq-export-fail {
    background: url(<?php echo admin_url('images/loading.gif'); ?>) left center no-repeat;
    line-height: 16px;
    padding-left: 20px;
}
p.status {
    padding-top: 0;
    padding-bottom: 0;
    margin: 0;
}
.dsq-imported, .dsq-exported {
    background: url(<?php echo admin_url('images/yes.png'); ?>) left center no-repeat;
}
.dsq-import-fail, .dsq-export-fail {
    background: url(<?php echo admin_url('images/no.png'); ?>) left center no-repeat;
}
</style>
<script type="text/javascript">
jQuery(function($) {
    $('#dsq-tabs li').click(function() {
        $('#dsq-tabs li.selected').removeClass('selected');
        $(this).addClass('selected');
        $('.dsq-main, .dsq-advanced').hide();
        $('.' + $(this).attr('rel')).show();
    });
    if (location.href.indexOf('#adv') != -1) {
        $('#dsq-tab-advanced').click();
    }
    dsq_fire_export();
    dsq_fire_import();
});
dsq_fire_export = function() {
    var $ = jQuery;
    $('#dsq_export a.button, #dsq_export_retry').unbind().click(function() {
        $('#dsq_export').html('<p class="status"></p>');
        $('#dsq_export .status').removeClass('dsq-export-fail').addClass('dsq-exporting').html('Processing...');
        dsq_export_comments();
        return false;
    });
}
dsq_export_comments = function() {
    var $ = jQuery;
    var status = $('#dsq_export .status');
    var export_info = (status.attr('rel') || '0|' + (new Date().getTime()/1000)).split('|');
    $.get(
        '<?php echo admin_url('index.php'); ?>',
        {
            cf_action: 'export_comments',
            post_id: export_info[0],
            timestamp: export_info[1]
        },
        function(response) {
            switch (response.result) {
                case 'success':
                    status.html(response.msg).attr('rel', response.post_id + '|' + response.timestamp);
                    switch (response.status) {
                        case 'partial':
                            dsq_export_comments();
                            break;
                        case 'complete':
                            status.removeClass('dsq-exporting').addClass('dsq-exported');
                            break;
                    }
                break;
                case 'fail':
                    status.parent().html(response.msg);
                    dsq_fire_export();
                break;
            }
        },
        'json'
    );
}
dsq_fire_import = function() {
    var $ = jQuery;
    $('#dsq_import a.button, #dsq_import_retry').unbind().click(function() {
        var wipe = $('#dsq_import_wipe').is(':checked');
        $('#dsq_import').html('<p class="status"></p>');
        $('#dsq_import .status').removeClass('dsq-import-fail').addClass('dsq-importing').html('Processing...');
        dsq_import_comments(wipe);
        return false;
    });
}
dsq_import_comments = function(wipe) {
    var $ = jQuery;
    var status = $('#dsq_import .status');
    var last_comment_id = status.attr('rel') || '0';
    $.get(
        '<?php echo admin_url('index.php'); ?>',
        {
            cf_action: 'import_comments',
            last_comment_id: last_comment_id,
            wipe: (wipe ? 1 : 0)
        },
        function(response) {
            switch (response.result) {
                case 'success':
                    status.html(response.msg).attr('rel', response.last_comment_id);
                    switch (response.status) {
                        case 'partial':
                            dsq_import_comments(false);
                            break;
                        case 'complete':
                            status.removeClass('dsq-importing').addClass('dsq-imported');
                            break;
                    }
                break;
                case 'fail':
                    status.parent().html(response.msg);
                    dsq_fire_import();
                break;
            }
        },
        'json'
    );
}
</script>
<?php
// HACK: Our own styles for older versions of WordPress.
        global $wp_version;
        if ( version_compare($wp_version, '2.5', '<') ) {
            echo "<link rel='stylesheet' href='" . DSQ_PLUGIN_URL . "/media/styles/manage-pre25.css' type='text/css' />";
        }
    }
}
add_action('admin_head', 'dsq_admin_head');

function dsq_warning() {
    $page = (isset($_GET['page']) ? $_GET['page'] : null);
    if ( !get_option('disqus_forum_url') && !isset($_POST['forum_url']) && $page != 'disqus' ) {
        dsq_manage_dialog('You must <a href="edit-comments.php?page=disqus">configure the plugin</a> to enable Disqus Comments.', true);
    }

    if ( !dsq_is_installed() && $page != 'disqus' && !empty($_GET['step']) && !isset($_POST['uninstall']) ) {
        dsq_manage_dialog('Disqus Comments has not yet been configured. (<a href="edit-comments.php?page=disqus">Click here to configure</a>)');
    }
}

/**
 * Wrapper for built-in __() which pulls all text from
 * the disqus domain and supports variable interpolation.
 */
function dsq_i($text, $params=null) {
    if (!is_array($params))
    {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'disqus'), $params);
}

// catch original query
function dsq_parse_query($query) {
    add_action('the_posts', 'dsq_add_request_post_ids', 999);
}
add_action('parse_request', 'dsq_parse_query');

// track the original request post_ids, only run once
function dsq_add_request_post_ids($posts) {
    dsq_add_query_posts($posts);
    remove_action('the_posts', 'dsq_log_request_post_ids', 999);
    return $posts;
}

function dsq_maybe_add_post_ids($posts) {
    global $DSQ_QUERY_COMMENTS;
    if ($DSQ_QUERY_COMMENTS) {
        dsq_add_query_posts($posts);
    }
    return $posts;
}
add_action('the_posts', 'dsq_maybe_add_post_ids');

function dsq_add_query_posts($posts) {
    global $DSQ_QUERY_POST_IDS;
    if (count($posts)) {
        foreach ($posts as $post) {
            $post_ids[] = intval($post->ID);
        }
        $DSQ_QUERY_POST_IDS[md5(serialize($post_ids))] = $post_ids;
    }
}

// check to see if the posts in the loop match the original request or an explicit request, if so output the JS
function dsq_loop_end($query) {
    if ( get_option('disqus_cc_fix') == '1' || !count($query->posts) || is_single() || is_page() || is_feed() || !dsq_can_replace() ) {
        return;
    }
    global $DSQ_QUERY_POST_IDS;
    foreach ($query->posts as $post) {
        $loop_ids[] = intval($post->ID);
    }
    $posts_key = md5(serialize($loop_ids));
    if (isset($DSQ_QUERY_POST_IDS[$posts_key])) {
        dsq_output_loop_comment_js($DSQ_QUERY_POST_IDS[$posts_key]);
    }
}
add_action('loop_end', 'dsq_loop_end');

// if someone has a better hack, let me know
// prevents duplicate calls to count.js
$_HAS_COUNTS = false;

function dsq_output_loop_comment_js($post_ids = null) {
    global $_HAS_COUNTS;
    if ($_HAS_COUNTS) return;
    $_HAS_COUNTS = true;
    if (count($post_ids)) {
?>
    <script type="text/javascript">
    // <![CDATA[
        var disqus_shortname = '<?php echo strtolower(get_option('disqus_forum_url')); ?>';
        (function () {
            var nodes = document.getElementsByTagName('span');
            for (var i = 0, url; i < nodes.length; i++) {
                if (nodes[i].className.indexOf('dsq-postid') != -1) {
                    nodes[i].parentNode.setAttribute('data-disqus-identifier', nodes[i].getAttribute('rel'));
                    url = nodes[i].parentNode.href.split('#', 1);
                    if (url.length == 1) { url = url[0]; }
                    else { url = url[1]; }
                    nodes[i].parentNode.href = url + '#disqus_thread';
                }
            }
            var s = document.createElement('script'); s.async = true;
            s.type = 'text/javascript';
            <?php
            if (is_ssl()) {
                $connection_type = "https";
            } else {
                $connection_type = "http";
            }
            ?>
            s.src = '<?php echo $connection_type; ?>' + '://' + '<?php echo DISQUS_DOMAIN; ?>/forums/' + disqus_shortname + '/count.js';
            (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
        }());
    //]]>
    </script>
<?php
    }
}

function dsq_output_footer_comment_js() {
    if (!dsq_can_replace()) return;
    if (get_option('disqus_cc_fix') != '1') return;
?>
    <script type="text/javascript">
    // <![CDATA[
        var disqus_shortname = '<?php echo strtolower(get_option('disqus_forum_url')); ?>';
        (function () {
            var nodes = document.getElementsByTagName('span');
            for (var i = 0, url; i < nodes.length; i++) {
                if (nodes[i].className.indexOf('dsq-postid') != -1) {
                    nodes[i].parentNode.setAttribute('data-disqus-identifier', nodes[i].getAttribute('rel'));
                    url = nodes[i].parentNode.href.split('#', 1);
                    if (url.length == 1) url = url[0];
                    else url = url[1]
                    nodes[i].parentNode.href = url + '#disqus_thread';
                }
            }
            var s = document.createElement('script'); s.async = true;
            s.type = 'text/javascript';
            s.src = 'http://<?php echo DISQUS_DOMAIN; ?>/forums/' + disqus_shortname + '/count.js';
            (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
        }());
    //]]>
    </script>
<?php
}
add_action('wp_footer', 'dsq_output_footer_comment_js');

// UPDATE DSQ when a permalink changes

$dsq_prev_permalinks = array();

function dsq_prev_permalink($post_id) {
// if post not published, return
    $post = &get_post($post_id);
    if ($post->post_status != 'publish') {
        return;
    }
    global $dsq_prev_permalinks;
    $dsq_prev_permalinks['post_'.$post_id] = get_permalink($post_id);
}
add_action('pre_post_update', 'dsq_prev_permalink');

function dsq_check_permalink($post_id) {
    global $dsq_prev_permalinks;
    if (!empty($dsq_prev_permalinks['post_'.$post_id]) && $dsq_prev_permalinks['post_'.$post_id] != get_permalink($post_id)) {
        $post = get_post($post_id);
        dsq_update_permalink($post);
    }
}
add_action('edit_post', 'dsq_check_permalink');

add_action('admin_notices', 'dsq_warning');

// Only replace comments if the disqus_forum_url option is set.
add_filter('comments_template', 'dsq_comments_template');
add_filter('comments_number', 'dsq_comments_text');
add_filter('get_comments_number', 'dsq_comments_number');
add_filter('bloginfo_url', 'dsq_bloginfo_url');

/**
 * JSON ENCODE for PHP < 5.2.0
 * Checks if json_encode is not available and defines json_encode
 * to use php_json_encode in its stead
 * Works on iteratable objects as well - stdClass is iteratable, so all WP objects are gonna be iteratable
 */
if(!function_exists('cf_json_encode')) {
    function cf_json_encode($data) {
// json_encode is sending an application/x-javascript header on Joyent servers
// for some unknown reason.
//         if(function_exists('json_encode')) { return json_encode($data); }
//         else { return cfjson_encode($data); }
        return cfjson_encode($data);
    }

    function cfjson_encode_string($str) {
        if(is_bool($str)) {
            return $str ? 'true' : 'false';
        }

        return str_replace(
            array(
                '"'
                , '/'
                , "\n"
                , "\r"
            )
            , array(
                '\"'
                , '\/'
                , '\n'
                , '\r'
            )
            , $str
        );
    }

    function cfjson_encode($arr) {
        $json_str = '';
        if (is_array($arr)) {
            $pure_array = true;
            $array_length = count($arr);
            for ( $i = 0; $i < $array_length ; $i++) {
                if (!isset($arr[$i])) {
                    $pure_array = false;
                    break;
                }
            }
            if ($pure_array) {
                $json_str = '[';
                $temp = array();
                for ($i=0; $i < $array_length; $i++) {
                    $temp[] = sprintf("%s", cfjson_encode($arr[$i]));
                }
                $json_str .= implode(',', $temp);
                $json_str .="]";
            }
            else {
                $json_str = '{';
                $temp = array();
                foreach ($arr as $key => $value) {
                    $temp[] = sprintf("\"%s\":%s", $key, cfjson_encode($value));
                }
                $json_str .= implode(',', $temp);
                $json_str .= '}';
            }
        }
        else if (is_object($arr)) {
            $json_str = '{';
            $temp = array();
            foreach ($arr as $k => $v) {
                $temp[] = '"'.$k.'":'.cfjson_encode($v);
            }
            $json_str .= implode(',', $temp);
            $json_str .= '}';
        }
        else if (is_string($arr)) {
            $json_str = '"'. cfjson_encode_string($arr) . '"';
        }
        else if (is_numeric($arr)) {
            $json_str = $arr;
        }
        else if (is_bool($arr)) {
            $json_str = $arr ? 'true' : 'false';
        }
        else {
            $json_str = '"'. cfjson_encode_string($arr) . '"';
        }
        return $json_str;
    }
}

// Single Sign-on Integration

function dsq_sso() {
    if ($key = get_option('disqus_partner_key')) {
        // use old style SSO
        $new = false;
    } elseif (($key = get_option('disqus_secret_key')) && ($public = get_option('disqus_public_key'))) {
        // use new style SSO
        $new = true;
    } else {
        // sso is not configured
        return array();
    }
    global $current_user, $dsq_api;
    get_currentuserinfo();
    if ($current_user->ID) {
        $avatar_tag = get_avatar($current_user->ID);
        $avatar_data = array();
        preg_match('/(src)=((\'|")[^(\'|")]*(\'|"))/i', $avatar_tag, $avatar_data);
        $avatar = str_replace(array('"', "'"), '', $avatar_data[2]);
        $user_data = array(
            'username' => $current_user->display_name,
            'id' => $current_user->ID,
            'avatar' => $avatar,
            'email' => $current_user->user_email,
        );
    }
    else {
        $user_data = array();
    }
    $user_data = base64_encode(cf_json_encode($user_data));
    $time = time();
    $hmac = dsq_hmacsha1($user_data.' '.$time, $key);

    $payload = $user_data.' '.$hmac.' '.$time;

    if ($new) {
        return array('remote_auth_s3'=>$payload, 'api_key'=>$public);
    } else {
        return array('remote_auth_s2'=>$payload);
    }
}

// from: http://www.php.net/manual/en/function.sha1.php#39492
//Calculate HMAC-SHA1 according to RFC2104
// http://www.ietf.org/rfc/rfc2104.txt
function dsq_hmacsha1($data, $key) {
    $blocksize=64;
    $hashfunc='sha1';
    if (strlen($key)>$blocksize)
        $key=pack('H*', $hashfunc($key));
    $key=str_pad($key,$blocksize,chr(0x00));
    $ipad=str_repeat(chr(0x36),$blocksize);
    $opad=str_repeat(chr(0x5c),$blocksize);
    $hmac = pack(
                'H*',$hashfunc(
                    ($key^$opad).pack(
                        'H*',$hashfunc(
                            ($key^$ipad).$data
                        )
                    )
                )
            );
    return bin2hex($hmac);
}

function dsq_identifier_for_post($post) {
    return $post->ID . ' ' . $post->guid;
}

function dsq_title_for_post($post) {
    $title = get_the_title($post);
    $title = strip_tags($title, DISQUS_ALLOWED_HTML);
    return $title;
}

function dsq_link_for_post($post) {
    return get_permalink($post);
}

function dsq_does_need_update() {
    $version = (string)get_option('disqus_version');
    if (empty($version)) {
        $version = '0';
    }

    if (version_compare($version, '2.49', '<')) {
        return true;
    }

    return false;
}

function dsq_install($allow_database_install=true) {
    global $wpdb, $userdata;

    $version = (string)get_option('disqus_version');
    if (!$version) {
        $version = '0';
    }

    if ($allow_database_install)
    {
        dsq_install_database($version);
    }

    if (version_compare($version, DISQUS_VERSION, '=')) return;

    // if this is a new install, we should not set disqus active
    if ($version == '0') {
        add_option('disqus_active', 0);
    } else {
        add_option('disqus_active', 1);
    }

    update_option('disqus_version', DISQUS_VERSION);
}

/**
 * Initializes the database if it's not already present.
 */
function dsq_install_database($version=0) {
    global $wpdb;

    if (version_compare($version, '2.49', '<')) {
        $wpdb->query("CREATE INDEX disqus_dupecheck ON `".$wpdb->prefix."commentmeta` (meta_key, meta_value(11));");
    }
}
function dsq_uninstall_database($version=0) {
    if (version_compare($version, '2.49', '>=')) {
        $wpdb->query("DROP INDEX disqus_dupecheck ON `".$wpdb->prefix."commentmeta`;");
    }
}
?>
