<?php
require(ABSPATH . 'wp-includes/version.php');

if ( !current_user_can('manage_options') ) {
    die();
}

$step = (isset($_GET['step']) ? $_GET['step'] : null);

?>
<div class="wrap">
    <h2><?php echo dsq_i('Upgrade Disqus Comments'); ?></h2>
    <form method="POST" action="?page=disqus&amp;step=<?php echo $step; ?>">
        <p>You need to upgrade your database to continue.</p>

        <p class="submit" style="text-align: left">
            <input type="submit" name="upgrade" value="Upgrade &raquo;" />
        </p>
    </form>
</div>