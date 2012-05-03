<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content-main">
 *
 * @package Graphene
 * @since graphene 1.0
 */
global $graphene_settings;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <title><?php wp_title( '' ); ?></title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" /> 
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php if (!get_theme_mod('background_image', false) && !get_theme_mod('background_color', false)) : ?>
<div class="bg-gradient">
<?php endif; ?>

<?php do_action( 'graphene_container_before' ); ?>

<div id="container" class="container_16">
    
    <?php if ($graphene_settings['hide_top_bar'] != true) : ?>
        <div id="top-bar">
                <?php do_action('graphene_before_feed_icon'); ?>
				<div id="profiles" class="clearfix gutter-left">
                    <?php do_action('graphene_social_profiles'); ?>
                </div>
            <?php
            /**
             * Retrieves our custom search form.
             */
            ?>
            <?php if (($search_box_location = $graphene_settings['search_box_location']) && $search_box_location == 'top_bar' || $search_box_location == '') : ?>
                <div id="top_search" class="grid_4">
                    <?php get_search_form(); ?>
                    <?php do_action('graphene_top_search'); ?>
                </div>
            <?php endif; ?>
            
            <?php do_action( 'graphene_top_bar' ); ?>
            
        </div>
    <?php endif; ?>

    <?php
        if ($post)
            $post_id = $post->ID;
        else
            $post_id = false;
        $header_img = apply_filters( 'graphene_header_image', graphene_get_header_image( $post_id ) );

        /*
         * Check if the page uses SSL and change HTTP to HTTPS if true 
         * 
         * Experimental. Let me know if there's any problem.
         */
        if (is_ssl() && stripos($header_img, 'https') === false) {
            $header_img = str_replace('http', 'https', $header_img);
        }

        // Gets the colour for header texts, or if we should display them at all
        if ('blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR))
            $style = ' style="display:none;"';
        else
            $style = ' style="color:#' . get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) . ';"';
    ?>
    <div id="header" style="background-image:url(<?php echo $header_img; ?>);">
        <?php if ($graphene_settings['link_header_img']) : ?>
        <a href="<?php echo home_url(); ?>" id="header_img_link" title="<?php esc_attr_e('Go back to the front page', 'graphene'); ?>">&nbsp;</a>
        <?php endif; ?>
        
        <?php /* Header widget area */
		if ($graphene_settings['enable_header_widget'] && is_active_sidebar('header-widget-area')) {
			echo '<div class="header-widget">';
			dynamic_sidebar('header-widget-area');
			echo '</div>';
		}
		?>
		
        <?php /* The site title and description */ 
			if (is_singular() && !is_front_page()) { 
				$title_tag = 'h2';
				$desc_tag = 'h3';
			} else {
				$title_tag = 'h1';
				$desc_tag = 'h2';
			}
		?>
		<?php echo '<'.$title_tag; ?> <?php echo $style; ?> class="header_title push_1 grid_15">
        	<?php if ( ! is_front_page() ) : ?><a <?php echo $style; ?> href="<?php echo home_url(); ?>" title="<?php esc_attr_e('Go back to the front page', 'graphene'); ?>"><?php endif; ?>
				<?php bloginfo('name'); ?>
            <?php if ( ! is_front_page() ) : ?></a><?php endif; ?>
        </<?php echo $title_tag; ?>>
        
		<?php echo '<'.$desc_tag; ?> <?php echo $style; ?> class="header_desc push_1 grid_15">
			<?php bloginfo('description'); ?>
        </<?php echo $desc_tag; ?>>
        
		<?php do_action('graphene_header'); ?>
    </div>
    <div id="nav">
        <?php /* The navigation menu */ ?>
        <div id="header-menu-wrap" class="clearfix">
			<?php
            /* Header menu */
            $args = array(
                'container' => '',
                'menu_id' => 'header-menu',
                'menu_class' => graphene_get_menu_class('menu clearfix'),
                'fallback_cb' => 'graphene_default_menu',
                'depth' => 5,
                'theme_location' => 'Header Menu',
            );
			if ( ! $graphene_settings['disable_menu_desc'] )
				$args = array_merge( $args, array( 'walker' => new Graphene_Description_Walker() ) );
				
            wp_nav_menu( apply_filters( 'graphene_header_menu_args', $args ) ); ?>
            
            <div class="clear"></div>
            
			<?php if (($search_box_location = $graphene_settings['search_box_location']) && $search_box_location == 'nav_bar') : ?>
                <div id="top_search" class="grid_4">
                    <?php get_search_form(); ?>
                    <?php do_action('graphene_nav_search'); ?>
                </div>
            <?php endif; ?>
            
            <?php do_action( 'graphene_header_menu' ); ?>
        
        </div>
		
        <?php
        /* Secondary menu */
        $args = array(
            'container' => 'div',
			'container_id' => 'secondary-menu-wrap',
			'container_class' => 'clearfix',
            'menu_id' => 'secondary-menu',
            'menu_class' => 'menu clearfix',
            'fallback_cb' => 'none',
            'depth' => 5,
            'theme_location' => 'secondary-menu',
        );
        wp_nav_menu( apply_filters( 'graphene_secondary_menu_args', $args ) );
        ?>
        
        <div class="menu-bottom-shadow">&nbsp;</div>


        <?php do_action( 'graphene_top_menu' ); ?>

    </div>

    <?php do_action( 'graphene_before_content' ); ?>

    <div id="content" class="clearfix hfeed">
        <?php do_action( 'graphene_before_content-main' ); ?>
        
        <?php
        
            /* Sidebar2 on the left side? */
            if ( in_array( graphene_column_mode(), array( 'three_col_right', 'three_col_center', 'two_col_right' ) ) ){
                get_sidebar( 'two' );
            }
			
			/* Sidebar1 on the left side? */            
            if ( in_array( graphene_column_mode(), array( 'three_col_right' ) ) ){
                get_sidebar();                
            }
        
        ?>
        
        <div id="content-main" <?php graphene_grid( 'clearfix', 16, 11, 8 ); ?>>
        <?php do_action( 'graphene_top_content' ); ?>