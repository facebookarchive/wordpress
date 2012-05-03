<?php
/**
 * The Sidebar for display in the content page. 
 *
 * @package Graphene
 * @since graphene 1.0.8
 */
global $graphene_settings;

if (((!$graphene_settings['alt_home_footerwidget'] || !is_front_page()) && is_active_sidebar('footer-widget-area')) 
	|| ($graphene_settings['alt_home_footerwidget'] && is_active_sidebar('home-footer-widget-area') && is_front_page())) : ?>
    
    <?php do_action('graphene_before_bottomsidebar'); ?>
    
    <div id="sidebar_bottom" class="sidebar clearfix">
        
        <?php do_action('graphene_bottomsidebar'); ?>
		
		<?php if (is_front_page() && $graphene_settings['alt_home_footerwidget']) : ?>
            <?php dynamic_sidebar('home-footer-widget-area'); ?>
        <?php else : ?>
            <?php dynamic_sidebar('footer-widget-area'); ?>
        <?php endif; ?>
    </div>

	<?php do_action('graphene_after_bottomsidebar'); ?>
<?php endif; ?>