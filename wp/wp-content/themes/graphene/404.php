<?php
$search_term = untrailingslashit(substr($_SERVER['REQUEST_URI'],1));
$search_term = urldecode(stripslashes($search_term));
$find = array("'.html'", "'.+/'", "'[-/_]'");
$replace = " ";
$search_term = trim(preg_replace($find, $replace, $search_term));

// Sanitise the search term
global $wpdb;
$search_term_q = esc_js( $wpdb->escape( urlencode( strip_tags( $search_term ) ) ) );

$redirect_location = get_home_url().'?s='.$search_term_q.'&search_404=1';
get_header();
?>
<script type="text/javascript">
    jQuery(document).ready(function($){
		window.location.replace("<?php echo $redirect_location; ?>");
    });
</script>

<h1 class="page-title">
    <?php
        printf(__('Searching for: <span>%s</span>', 'graphene'), $search_term_q );
    ?>
</h1>

<div class="post clearfix post_404">
    <div class="entry clearfix">
        <h2><?php _e('Error 404 - Page Not Found', 'graphene'); ?></h2>
        <div class="entry-content clearfix">
            <p><?php _e("Sorry, I've looked everywhere but I can't find the page you're looking for.", 'graphene'); ?></p>
            <p><?php _e("If you follow the link from another website, I may have removed or renamed the page some time ago. You may want to try searching for the page:", 'graphene'); ?></p>
            
            <?php get_search_form(); ?>
        </div>
    </div>
</div>
<div class="post clearfix post_404_search">
	<div class="entry clearfix"> 
	<h2><?php _e('Automated search', 'graphene'); ?></h2>   
        <div class="entry-content clearfix">
            <p>
            <?php printf(__('Searching for the terms <strong>%s</strong> ...', 'graphene'), $search_term_q ); ?>
            </p>
        </div>
    </div>
</div>
<?php get_footer(); ?>