<?php
global $dsq_api;

require(ABSPATH . 'wp-includes/version.php');

if ( !current_user_can('moderate_comments') ) {
    die();
}

if(isset($_POST['dsq_username'])) {
    $_POST['dsq_username'] = stripslashes($_POST['dsq_username']);
}

if(isset($_POST['dsq_password'])) {
    $_POST['dsq_password'] = stripslashes($_POST['dsq_password']);
}

// HACK: For old versions of WordPress
if ( !function_exists('wp_nonce_field') ) {
    function wp_nonce_field() {}
}

// Handle export function.
if( isset($_POST['export']) and DISQUS_CAN_EXPORT ) {
    require_once(dirname(__FILE__) . '/export.php');
    dsq_export_wp();
}

// Handle uninstallation.
if ( isset($_POST['uninstall']) ) {
    foreach (dsq_options() as $opt) {
        delete_option($opt);
    }
    unset($_POST);
    dsq_uninstall_database();
?>
<div class="wrap">
    <h2><?php echo dsq_i('Disqus Uninstalled'); ?></h2>
    <form method="POST" action="?page=disqus">
        <p>Disqus has been uninstalled successfully.</p>
        <ul style="list-style: circle;padding-left:20px;">
            <li>Local settings for the plugin were removed.</li>
            <li>Database changes by Disqus were reverted.</li>
        </ul>
        <p>If you wish to <a href="?page=disqus&amp;step=1">reinstall</a>, you can do that now.</p>
    </form>
</div>
<?php
die();
}

// Clean-up POST parameters.
foreach ( array('dsq_forum', 'dsq_username', 'dsq_user_api_key') as $key ) {
    if ( isset($_POST[$key]) ) { $_POST[$key] = strip_tags($_POST[$key]); }
}


// Handle advanced options.
if ( isset($_POST['disqus_forum_url']) && isset($_POST['disqus_replace']) ) {
    $disqus_forum_url = $_POST['disqus_forum_url'];
    if ( $dot_pos = strpos($disqus_forum_url, '.') ) {
        $disqus_forum_url = substr($disqus_forum_url, 0, $dot_pos);
    }
    update_option('disqus_forum_url', $disqus_forum_url);
    update_option('disqus_partner_key', trim(stripslashes($_POST['disqus_partner_key'])));
    update_option('disqus_api_key', trim(stripslashes($_POST['disqus_api_key'])));
    update_option('disqus_user_api_key', trim(stripslashes($_POST['disqus_user_api_key'])));
    update_option('disqus_replace', $_POST['disqus_replace']);
    update_option('disqus_cc_fix', isset($_POST['disqus_cc_fix']));
    update_option('disqus_manual_sync', isset($_POST['disqus_manual_sync']));
    update_option('disqus_disable_ssr', isset($_POST['disqus_disable_ssr']));
    update_option('disqus_public_key', $_POST['disqus_public_key']);
    update_option('disqus_secret_key', $_POST['disqus_secret_key']);
    dsq_manage_dialog('Your settings have been changed.');
}

// handle disqus_active
if (isset($_GET['active'])) {
    update_option('disqus_active', ($_GET['active'] == '1' ? '1' : '0'));
}

$dsq_user_api_key = isset($_POST['dsq_user_api_key']) ? $_POST['dsq_user_api_key'] : null;

// Get installation step process (or 0 if we're already installed).
$step = @intval($_GET['step']);
if ($step > 1 && $step != 3 && $dsq_user_api_key) $step = 1;
elseif ($step == 2 && !isset($_POST['dsq_username'])) $step = 1;
$step = (dsq_is_installed()) ? 0 : ($step ? $step : 1);

// Handle installation process.
if ( 3 == $step && isset($_POST['dsq_forum']) && isset($_POST['dsq_user_api_key']) ) {
    list($dsq_forum_id, $dsq_forum_url) = explode(':', $_POST['dsq_forum']);
    update_option('disqus_forum_url', $dsq_forum_url);
    $api_key = $dsq_api->get_forum_api_key($_POST['dsq_user_api_key'], $dsq_forum_id);
    if ( !$api_key || $api_key < 0 ) {
        update_option('disqus_replace', 'replace');
        dsq_manage_dialog(dsq_i('There was an error completing the installation of Disqus. If you are still having issues, refer to the <a href="http://docs.disqus.com/help/87/">WordPress help page</a>.'), true);
    } else {
        update_option('disqus_api_key', $api_key);
        update_option('disqus_user_api_key', $_POST['dsq_user_api_key']);
        update_option('disqus_replace', 'all');
    }

    if (!empty($_POST['disqus_partner_key'])) {
        $partner_key = trim(stripslashes($_POST['disqus_partner_key']));
        if (!empty($partner_key)) {
            update_option('disqus_partner_key', $partner_key);
        }
    }
}

if ( 2 == $step && isset($_POST['dsq_username']) && isset($_POST['dsq_password']) ) {
    $dsq_user_api_key = $dsq_api->get_user_api_key($_POST['dsq_username'], $_POST['dsq_password']);
    if ( $dsq_user_api_key < 0 || !$dsq_user_api_key ) {
        $step = 1;
        dsq_manage_dialog($dsq_api->get_last_error(), true);
    }

    if ( $step == 2 ) {
        $dsq_sites = $dsq_api->get_forum_list($dsq_user_api_key);
        if ( $dsq_sites < 0 ) {
            $step = 1;
            dsq_manage_dialog($dsq_api->get_last_error(), true);
        } else if ( !$dsq_sites ) {
            $step = 1;
            dsq_manage_dialog(dsq_i('There aren\'t any sites associated with this account. Maybe you want to <a href="%s">create a site</a>?', 'http://disqus.com/admin/register/'), true);
        }
    }
}

$show_advanced = (isset($_GET['t']) && $_GET['t'] == 'adv');

?>
<div class="wrap" id="dsq-wrap">
    <ul id="dsq-tabs">
        <li<?php if (!$show_advanced) echo ' class="selected"'; ?> id="dsq-tab-main" rel="dsq-main"><?php echo (dsq_is_installed() ? 'Manage' : 'Install'); ?></li>
        <li<?php if ($show_advanced) echo ' class="selected"'; ?> id="dsq-tab-advanced" rel="dsq-advanced"><?php echo dsq_i('Advanced Options'); ?></li>
    </ul>

    <div id="dsq-main" class="dsq-content">
    <?php
switch ( $step ) {
case 3:
?>
        <div id="dsq-step-3" class="dsq-main"<?php if ($show_advanced) echo ' style="display:none;"'; ?>>
            <h2><?php echo dsq_i('Install Disqus Comments'); ?></h2>

            <p>Disqus has been installed on your blog.</p>
            <p>If you have existing comments, you may wish to <a href="?page=disqus&amp;t=adv#export">export them</a> now. Otherwise, you're all set, and the Disqus network is now powering comments on your blog.</p>
        </div>
<?php
    break;
case 2:
?>
        <div id="dsq-step-2" class="dsq-main"<?php if ($show_advanced) echo ' style="display:none;"'; ?>>
            <h2><?php echo dsq_i('Install Disqus Comments'); ?></h2>

            <form method="POST" action="?page=disqus&amp;step=3">
            <?php wp_nonce_field('dsq-install-2'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row" valign="top"><?php echo dsq_i('Select a website'); ?></th>
                    <td>
<?php
foreach ( $dsq_sites as $counter => $dsq_site ):
?>
                        <input name="dsq_forum" type="radio" id="dsq-site-<?php echo $counter; ?>" value="<?php echo $dsq_site->id; ?>:<?php echo $dsq_site->shortname; ?>" />
                        <label for="dsq-site-<?php echo $counter; ?>"><strong><?php echo htmlspecialchars($dsq_site->name); ?></strong> (<u><?php echo $dsq_site->shortname; ?>.disqus.com</u>)</label>
                        <br />
<?php
endforeach;
?>
                        <hr />
                        <a href="<?php echo DISQUS_URL; ?>comments/register/"><?php echo dsq_i('Or register a new one on the Disqus website.'); ?></a>
                    </td>
                </tr>
            </table>

            <p class="submit" style="text-align: left">
                <input type="hidden" name="dsq_user_api_key" value="<?php echo htmlspecialchars($dsq_user_api_key); ?>"/>
                <input name="submit" type="submit" value="Next &raquo;" />
            </p>
            </form>
        </div>
<?php
    break;
case 1:
?>
        <div id="dsq-step-1" class="dsq-main"<?php if ($show_advanced) echo ' style="display:none;"'; ?>>
            <h2><?php echo dsq_i('Install Disqus Comments'); ?></h2>

            <form method="POST" action="?page=disqus&amp;step=2">
            <?php wp_nonce_field('dsq-install-1'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row" valign="top"><?php echo dsq_i('Username'); ?></th>
                    <td>
                        <input id="dsq-username" name="dsq_username" tabindex="1" type="text" />
                        <a href="http://disqus.com/profile/signup/"><?php echo dsq_i('(don\'t have a Disqus Profile yet?)'); ?></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top"><?php echo dsq_i('Password'); ?></th>
                    <td>
                        <input type="password" id="dsq-password" name="dsq_password" tabindex="2">
                        <a href="http://disqus.com/forgot/"><?php echo dsq_i('(forgot your password?)'); ?></a>
                    </td>
                </tr>
            </table>

            <p class="submit" style="text-align: left">
                <input name="submit" type="submit" value="Next &raquo;" tabindex="3">
            </p>

            <script type="text/javascript"> document.getElementById('dsq-username').focus(); </script>
            </form>
        </div>
<?php
    break;
case 0:
    $url = get_option('disqus_forum_url');
?>
        <div class="dsq-main"<?php if ($show_advanced) echo ' style="display:none;"'; ?>>
            <h2><?php echo dsq_i('Comments'); ?></h2>
            <iframe src="<?php if ($url) {
                echo 'http://'.$url.'.'.DISQUS_DOMAIN.'/admin/moderate/';
            } else {
                echo DISQUS_URL.'admin/moderate/';
            } ?>?template=wordpress" style="width: 100%; height: 80%; min-height: 600px;"></iframe>
        </div>
<?php } ?>
    </div>

<?php
    $dsq_replace = get_option('disqus_replace');
    $dsq_forum_url = strtolower(get_option('disqus_forum_url'));
    $dsq_api_key = get_option('disqus_api_key');
    $dsq_user_api_key = get_option('disqus_user_api_key');
    $dsq_partner_key = get_option('disqus_partner_key');
    $dsq_cc_fix = get_option('disqus_cc_fix');
    $dsq_manual_sync = get_option('disqus_manual_sync');
    $dsq_disable_ssr = get_option('disqus_disable_ssr');

    $dsq_public_key = get_option('disqus_public_key');
    $dsq_secret_key = get_option('disqus_secret_key');
?>
    <!-- Advanced options -->
    <div id="dsq-advanced" class="dsq-content dsq-advanced"<?php if (!$show_advanced) echo ' style="display:none;"'; ?>>
        <h2><?php echo dsq_i('Advanced Options'); ?></h2>
        <p><?php echo dsq_i('Version: %s', esc_html(DISQUS_VERSION)); ?></p>
        <?php
        if (get_option('disqus_active') === '0') {
            // disqus is not active
            echo '<p class="status">Disqus comments are currently disabled. (<a href="?page=disqus&amp;active=1">Enable</a>)</p>';
        } else {
            echo '<p class="status">Disqus comments are currently enabled. (<a href="?page=disqus&amp;active=0">Disable</a>)</p>';
        }
        ?>
        <form method="POST">
        <?php wp_nonce_field('dsq-advanced'); ?>
        <h3>Configuration</h3>
        <table class="form-table">
            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Disqus short name'); ?></th>
                <td>
                    <input name="disqus_forum_url" value="<?php echo esc_attr($dsq_forum_url); ?>" tabindex="1" type="text" />
                    <br />
                    <?php echo dsq_i('This is the unique identifier for your website on Disqus Comments.'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Disqus API Key'); ?></th>
                <td>
                    <input type="text" name="disqus_api_key" value="<?php echo esc_attr($dsq_api_key); ?>" tabindex="2">
                    <br />
                    <?php echo dsq_i('This is set for you when going through the installation steps.'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Disqus User API Key'); ?></th>
                <td>
                    <input type="text" name="disqus_user_api_key" value="<?php echo esc_attr($dsq_user_api_key); ?>" tabindex="2">
                    <br />
                    <?php echo dsq_i('This is set for you when going through the installation steps.'); ?>
                </td>
            </tr>
            <?php if (!empty($dsq_partner_key)) {// this option only shows if it was already present ?>
            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Disqus Partner Key'); ?></th>
                <td>
                    <input type="text" name="disqus_partner_key" value="<?php echo esc_attr($dsq_partner_key); ?>" tabindex="2">
                    <br />
                    <?php echo dsq_i('Advanced: Used for single sign-on (SSO) integration. (<a href="%s" onclick="window.open(this.href); return false">more info on SSO</a>)', 'http://docs.disqus.com/developers/sso/'); ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Application Public Key'); ?></th>
                <td>
                    <input type="text" name="disqus_public_key" value="<?php echo esc_attr($dsq_public_key); ?>" tabindex="2">
                    <br />
                    <?php echo dsq_i('Advanced: Used for single sign-on (SSO) integration. (<a href="%s" onclick="window.open(this.href); return false">more info on SSO</a>)', 'http://docs.disqus.com/developers/sso/'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Application Secret Key'); ?></th>
                <td>
                    <input type="text" name="disqus_secret_key" value="<?php echo esc_attr($dsq_secret_key); ?>" tabindex="2">
                    <br />
                    <?php echo dsq_i('Advanced: Used for single sign-on (SSO) integration. (<a href="%s" onclick="window.open(this.href); return false">more info on SSO</a>)', 'http://docs.disqus.com/developers/sso/'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Use Disqus Comments on'); ?></th>
                <td>
                    <select name="disqus_replace" tabindex="3" class="disqus-replace">
                        <option value="all" <?php if('all'==$dsq_replace){echo 'selected';}?>><?php echo dsq_i('On all existing and future blog posts.'); ?></option>
                        <option value="closed" <?php if('closed'==$dsq_replace){echo 'selected';}?>><?php echo dsq_i('Only on blog posts with closed comments.'); ?></option>
                    </select>
                    <br />
                    <?php echo dsq_i('NOTE: Your WordPress comments will never be lost.'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Comment Counts'); ?></th>
                <td>
                    <input type="checkbox" id="disqus_comment_count" name="disqus_cc_fix" <?php if($dsq_cc_fix){echo 'checked="checked"';}?> >
                    <label for="disqus_comment_count"><?php echo dsq_i('Output JavaScript in footer'); ?></label>
                    <br /><?php echo dsq_i('NOTE: Check this if you have problems with the comment count displays including: not showing on permalinks, broken featured image carousels, or longer-than-usual homepage load times (<a href="%s" onclick="window.open(this.href); return false">more info</a>).', 'http://docs.disqus.com/help/87/'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Comment Sync'); ?></th>
                <td>
                    <input type="checkbox" id="disqus_manual_sync" name="disqus_manual_sync" <?php if($dsq_manual_sync){echo 'checked="checked"';}?> >
                    <label for="disqus_manual_sync"><?php echo dsq_i('Disable automated comment importing'); ?></label>
                    <br /><?php echo dsq_i('NOTE: If you have problems with WP cron taking too long and large numbers of comments you may wish to disable the automated sync cron. Keep in mind that this means comments will not automatically get synced to your local Wordpress database.'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Server Side Rendering'); ?></th>
                <td>
                    <input type="checkbox" id="disqus_disable_ssr" name="disqus_disable_ssr" <?php if($dsq_disable_ssr){echo 'checked="checked"';}?> >
                    <label for="disqus_disable_ssr"><?php echo dsq_i('Disable server side rendering of comments'); ?></label>
                    <br /><?php echo dsq_i('NOTE: This will hide comments from nearly all search engines'); ?>
                </td>
            </tr>
        </table>

        <p class="submit" style="text-align: left">
            <input name="submit" type="submit" value="Save" class="button-primary button" tabindex="4">
        </p>
        </form>

        <h3>Import / Export</h3>

        <table class="form-table">
            <?php if (DISQUS_CAN_EXPORT): ?>
            <tr id="export">
                <th scope="row" valign="top"><?php echo dsq_i('Export comments to Disqus'); ?></th>
                <td>
                    <div id="dsq_export">
                        <p class="status"><a href="#" class="button"><?php echo dsq_i('Export Comments'); ?></a>  <?php echo dsq_i('This will export your existing WordPress comments to Disqus'); ?></p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Sync Disqus with WordPress'); ?></th>
                <td>
                    <div id="dsq_import">
                        <div class="status">
                            <p><?php echo dsq_i('This will download your Disqus comments and store them locally in WordPress'); ?></p>
                            <label><input type="checkbox" id="dsq_import_wipe" name="dsq_import_wipe" value="1"/> <?php echo dsq_i('Remove all imported Disqus comments before syncing.'); ?></label><br/>
                            <p><a href="#" class="button"><?php echo dsq_i('Sync Comments'); ?></a></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <h3>Uninstall</h3>

        <table class="form-table">
            <tr>
                <th scope="row" valign="top"><?php echo dsq_i('Uninstall Disqus Comments'); ?></th>
                <td>
                    <form action="?page=disqus" method="POST">
                        <?php wp_nonce_field('dsq-uninstall'); ?>
                        <p><input type="submit" value="Uninstall" name="uninstall" onclick="return confirm('<?php echo dsq_i('Are you sure you want to uninstall Disqus?'); ?>')" class="button" /> This will remove all Disqus specific settings, but it will leave your comments unaffected.</p>
                        NOTE: If you have problems with uninstallation taking too long you may wish to manually drop the <code>disqus_dupecheck</code> index from your <code>commentmeta</code> table.
                    </form>
                </td>
            </tr>
        </table>
        <br/>
        <h3><?php echo dsq_i('Debug Information'); ?></h3>
        <p><?php echo dsq_i('Having problems with the plugin? Check out our <a href="%s" onclick="window.open(this.href); return false">WordPress Troubleshooting</a> documentation. You can also <a href="%s">drop us a line</a> including the following details and we\'ll do what we can.', 'http://docs.disqus.com/help/87/', 'mailto:help+wp@disqus.com'); ?></p>
        <textarea style="width:90%; height:200px;">URL: <?php echo get_option('siteurl'); ?>
PHP Version: <?php echo phpversion(); ?>
Version: <?php echo $wp_version; ?>
Active Theme: <?php $theme = get_theme(get_current_theme()); echo $theme['Name'].' '.$theme['Version']; ?>
URLOpen Method: <?php echo dsq_url_method(); ?>

Plugin Version: <?php echo DISQUS_VERSION; ?>

Settings:

dsq_is_installed: <?php echo dsq_is_installed(); ?>

<?php foreach (dsq_options() as $opt) {
    echo $opt.': '.get_option($opt)."\n";
} ?>

Plugins:

<?php
foreach (get_plugins() as $plugin) {
    echo $plugin['Name'].' '.$plugin['Version']."\n";
}
?></textarea><br/>
    </div>
</div>
