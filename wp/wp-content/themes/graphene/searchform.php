<form id="searchform" class="searchform" method="get" action="<?php echo get_home_url(); ?>">
    <p class="clearfix default_searchform">
        <input type="text" name="s" onblur="if (this.value == '') {this.value = '<?php _e('Search','graphene'); ?>';}" onfocus="if (this.value == '<?php _e('Search','graphene'); ?>') {this.value = '';}" value="<?php _e('Search','graphene'); ?>" />
        <button type="submit"><span><?php _e('Search', 'graphene'); ?></span></button>
    </p>
    <?php do_action('graphene_search_form'); ?>
</form>