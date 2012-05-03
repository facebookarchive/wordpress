<?php 
/**
 * Get the custom style attributes, these are defined by theme options.
 * 
 * @global type $graphene_settings
 * @global type $graphene_defaults
 * @global type $content_width
 * @return string 
 */
function graphene_get_custom_style(){ 
	global $graphene_settings, $graphene_defaults, $content_width;
	
	$background = get_theme_mod( 'background_image', false);
	$bgcolor = get_theme_mod( 'background_color', false);
	$widgetcolumn = (is_front_page() && $graphene_settings['alt_home_footerwidget']) ? $graphene_settings['alt_footerwidget_column'] : $graphene_settings['footerwidget_column'];
	$container_width = apply_filters( 'graphene_container_width', $graphene_settings['container_width'] );
	$gutter = $graphene_settings['gutter_width'];
	$grid_width = $graphene_settings['grid_width'];
        
$style = '';
	
	/* Disable default background if a custom background colour is defined */
	if ( ! $background && $bgcolor ) {
		$style .= 'body{background-image:none;}';
	}
		
	/* Set the width of the bottom widget items if number of columns is specified */
	if ( $widgetcolumn ) {
		$widget_width = floor( ( ( ( $container_width - $gutter * 2 ) - 20 * ( $widgetcolumn - 1 ) ) / $widgetcolumn ) - 20 );
		$style .= '#sidebar_bottom .sidebar-wrap{width:'.$widget_width.'px}';
	}
        
	/* Set the width of the nav menu dropdown menu item width if specified */
	if ( $graphene_settings['navmenu_child_width'] ) {
		$nav_width = $graphene_settings['navmenu_child_width'];
		$style .= '#nav li ul{width:'.$nav_width.'px;}';
		
		if ( ! is_rtl() ){
			$background_left = -652-(200-$nav_width);
			$tmp_width = $nav_width-35;
            
			$style .= '	#nav li ul ul{margin-left:'.$nav_width.'px}
                       	#header-menu ul li.menu-item-ancestor > a {
						background-position:'.$background_left.'px -194px;
						width:'.$tmp_width.'px;
                        }
                        #header-menu ul li.menu-item-ancestor:hover > a,
                        #header-menu ul li.current-menu-item > a,
                        #header-menu ul li.current-menu-ancestor > a {
						background-position:'.$background_left.'px -238px;
                        }
						#secondary-menu ul li.menu-item-ancestor > a {
						background-position:'.$background_left.'px -286px;
						width:'.$tmp_width.'px;
						}
						#secondary-menu ul li.menu-item-ancestor:hover > a,
						#secondary-menu ul li.current-menu-item > a,
						#secondary-menu ul li.current-menu-ancestor > a {
						background-position:'.$background_left.'px -319px;
						}';
		} else {
            $style .= '	#nav li ul ul{margin-right:'.$nav_width.'px; margin-left: 0;}
						#header-menu ul li.menu-item-ancestor > a,
						#secondary-menu ul li.menu-item-ancestor > a {
						width:'.($nav_width-35).'px;
						}';
        }
		
		$style .= '#header-menu ul li a{width:'.($nav_width-20).'px;}';
		$style .= '#secondary-menu ul li a{width:'.($nav_width-30).'px;}';
	}
	
	/* Header title text style */ 
	$font_style = '';
	$font_style .= ( $graphene_settings['header_title_font_type']) ? 'font-family:'.$graphene_settings['header_title_font_type'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_lineheight']) ? 'line-height:'.$graphene_settings['header_title_font_lineheight'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_size']) ? 'font-size:'.$graphene_settings['header_title_font_size'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_weight']) ? 'font-weight:'.$graphene_settings['header_title_font_weight'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_style']) ? 'font-style:'.$graphene_settings['header_title_font_style'].';' : '';
	if ( $font_style ) { $style .= '.header_title { '.$font_style.' }'; }

	/* Header description text style */ 
	$font_style = '';
	$font_style .= ( $graphene_settings['header_desc_font_type']) ? 'font-family:'.$graphene_settings['header_desc_font_type'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_size']) ? 'font-size:'.$graphene_settings['header_desc_font_size'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_lineheight']) ? 'line-height:'.$graphene_settings['header_desc_font_lineheight'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_weight']) ? 'font-weight:'.$graphene_settings['header_desc_font_weight'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_style']) ? 'font-style:'.$graphene_settings['header_desc_font_style'].';' : '';
	if ( $font_style ) { $style .= '.header_desc { '.$font_style.' }'; }
	
	/* Content text style */ 
	$font_style = '';
	$font_style .= ( $graphene_settings['content_font_type']) ? 'font-family:'.$graphene_settings['content_font_type'].';' : '';
	$font_style .= ( $graphene_settings['content_font_size']) ? 'font-size:'.$graphene_settings['content_font_size'].';' : '';
	$font_style .= ( $graphene_settings['content_font_lineheight']) ? 'line-height:'.$graphene_settings['content_font_lineheight'].';' : '';
	$font_style .= ( $graphene_settings['content_font_colour'] != $graphene_defaults['content_font_colour']) ? 'color:'.$graphene_settings['content_font_colour'].';' : '';
	if ( $font_style ) { $style .= '.entry-content, .sidebar, .comment-entry { '.$font_style.' }'; }
	
	/* Title text colour */
	$font_style = '';
	$font_style .= ( $graphene_settings['title_font_colour'] != $graphene_defaults['title_font_colour']) ? 'color:'.$graphene_settings['title_font_colour'].';' : '';
	if ( $font_style ) { $style .= '.post-title, .post-title a { '.$font_style.' }'; }
	
    /* Adjust post title if author's avatar is shown */
	if ( $graphene_settings['show_post_avatar']) {
		$tmp_margin = !is_rtl() ? 'margin-right' : 'margin-left';
		$style .= '.post-title a, .post-title a:visited{display:block;'.$tmp_margin.':45px;padding-bottom:0;}';
	}
	
	/* Slider height */
	if ( $graphene_settings['slider_height']) {
		$style .= '.featured_slider #slider_root{height:'.$graphene_settings['slider_height'].'px;}';
	}
	
	/* Header image height */
	if ( $graphene_settings['header_img_height'] != $graphene_defaults['header_img_height'] ){
		$style .= '#header{height:'. HEADER_IMAGE_HEIGHT .'px;}';
	}
	
	/* Link header image */
	if ( $graphene_settings['link_header_img'] && ( HEADER_IMAGE_WIDTH != 960 || HEADER_IMAGE_HEIGHT != $graphene_defaults['header_img_height'] ) ) {
		$style .= '#header_img_link{width:'. HEADER_IMAGE_WIDTH .'px; height:'. HEADER_IMAGE_HEIGHT .'px;}';
	}
		
	// Link style
	if ( $graphene_settings['link_colour_normal'] != $graphene_defaults['link_colour_normal']) { $style.='a,.post-title,.post-title a,#comments > h4.current a{color:'.$graphene_settings['link_colour_normal'].';}';}
	if ( $graphene_settings['link_colour_visited'] != $graphene_defaults['link_colour_visited']) { $style.='a:visited,.post-title a:visited{color:'.$graphene_settings['link_colour_visited'].';}';}
	if ( $graphene_settings['link_colour_hover'] != $graphene_defaults['link_colour_hover']) { $style.='a:hover,.post-title a:hover{color:'.$graphene_settings['link_colour_hover'].';}';}
	if ( $graphene_settings['link_decoration_normal']) { $style.='a,.post-title a{text-decoration:'.$graphene_settings['link_decoration_normal'].';}';}
	if ( $graphene_settings['link_decoration_hover']) { $style.='a:hover,.post-title a:hover{text-decoration:'.$graphene_settings['link_decoration_hover'].';}';}
	
	// Custom column width
	$style .= graphene_get_custom_column_width();
	
	return $style;
}


/**
 * Get the custom colour style attributes defined by the theme colour settings
 * 
 * @global type $graphene_settings
 * @global type $graphene_defaults
 * @return string 
 */
function graphene_get_custom_colours(){
	global $graphene_settings, $graphene_defaults;
    $style = '';
    
	if ( ! is_admin() || strpos( $_SERVER["REQUEST_URI"], 'page=graphene_options&tab=display' ) ) {

    	/* Customised colours */
		
		// Content area
		if ( $graphene_settings['bg_content_wrapper'] != $graphene_defaults['bg_content_wrapper']) {$style .= '#content, .menu-bottom-shadow, #sidebar_bottom{background-color:'.$graphene_settings['bg_content_wrapper'].';}';}
		if ( $graphene_settings['bg_content'] != $graphene_defaults['bg_content']) {$style .= '.post{background-color:'.$graphene_settings['bg_content'].';}';}
		if ( $graphene_settings['bg_meta_border'] != $graphene_defaults['bg_meta_border']) {$style .= '.post-title, .post-title a, .post-title a:visited, .entry-footer{border-color:'.$graphene_settings['bg_meta_border'].';}';}
		if ( $graphene_settings['bg_post_top_border'] != $graphene_defaults['bg_post_top_border']) {$style .= '.post{border-top-color:'.$graphene_settings['bg_post_top_border'].';}';}
		if ( $graphene_settings['bg_post_bottom_border'] != $graphene_defaults['bg_post_bottom_border']) {$style .= '.post{border-bottom-color:'.$graphene_settings['bg_post_bottom_border'].';}';}
		if ( $graphene_settings['bg_post_bottom_border'] != $graphene_defaults['bg_post_bottom_border']) {$style .= '.post{border-bottom-color:'.$graphene_settings['bg_post_bottom_border'].';}';}
		
		// Widgets
		if ( $graphene_settings['bg_widget_item'] != $graphene_defaults['bg_widget_item']) {$style .= '.sidebar div.sidebar-wrap{background-color:'.$graphene_settings['bg_widget_item'].';}';}
		if ( $graphene_settings['bg_widget_box_shadow'] != $graphene_defaults['bg_widget_box_shadow']) {$style .= '.sidebar div.sidebar-wrap{
				-moz-box-shadow: 0 0 5px '.$graphene_settings['bg_widget_box_shadow'].';
				-webkit-box-shadow: 0 0 5px '.$graphene_settings['bg_widget_box_shadow'].';
				box-shadow: 0 0 5px '.$graphene_settings['bg_widget_box_shadow'].';
		}';}
		if ( $graphene_settings['bg_widget_list'] != $graphene_defaults['bg_widget_list']) {$style .= '.sidebar ul li{border-color:'.$graphene_settings['bg_widget_list'].';}';}
		if ( $graphene_settings['bg_widget_header_border'] != $graphene_defaults['bg_widget_header_border']) {$style .= '.sidebar h3{border-color:'.$graphene_settings['bg_widget_header_border'].';}';}
		if ( $graphene_settings['bg_widget_title'] != $graphene_defaults['bg_widget_title']) {$style .= '.sidebar h3, .sidebar h3 a, .sidebar h3 a:visited{color:'.$graphene_settings['bg_widget_title'].';}';}
		if ( $graphene_settings['bg_widget_title_textshadow'] != $graphene_defaults['bg_widget_title_textshadow']) {$style .= '.sidebar h3{text-shadow: 0 -1px '.$graphene_settings['bg_widget_title_textshadow'].';}';}
		$grad_top = $graphene_settings['bg_widget_header_top'];
		$grad_bottom = $graphene_settings['bg_widget_header_bottom'];
		if ( $grad_bottom != $graphene_defaults['bg_widget_header_bottom'] || $grad_top != $graphene_defaults['bg_widget_header_top']) {$style .= '.sidebar h3{
				background: ' . $grad_top . ';
				background: -moz-linear-gradient( ' . $grad_top . ', ' . $grad_bottom . ' );
				background: -webkit-linear-gradient(top, ' . $grad_top . ', ' . $grad_bottom . ' );
				-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'' . $grad_top . '\', EndColorStr=\'' . $grad_bottom . '\')";
				background: linear-gradient( ' . $grad_top . ', ' . $grad_bottom . ' );
		}';}
		
		// Slider
		if ( $graphene_settings['slider_display_style'] != 'bgimage-excerpt' ) {
			$grad_top = $graphene_settings['bg_slider_top'];
			$grad_bottom = $graphene_settings['bg_slider_bottom'];
			if ( $grad_bottom != $graphene_defaults['bg_slider_bottom'] || $grad_top != $graphene_defaults['bg_slider_top']) {$style .= '.featured_slider {
					-pie-background: linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
					background: ' . $grad_top . ';
					background: -moz-linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
					background: -webkit-linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
					-ms-filter: "progid:DXImageTransform.Microsoft.gradient(gradientType=1, startColorStr=\'' . $grad_top . '\', EndColorStr=\'' . $grad_bottom . '\')";
					background: linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
			}';}
		}
		
		// Block button
		$grad_top = $graphene_settings['bg_button'];
		$grad_bottom = graphene_hex_addition( $grad_top, -26);
		$grad_bottom_hover = graphene_hex_addition( $grad_top, -52);
		$font_color = $graphene_settings['bg_button_label'];
		$font_shadow = $graphene_settings['bg_button_label_textshadow'];
		$box_shadow = $graphene_settings['bg_button_box_shadow'];
		if ( $grad_top != $graphene_defaults['bg_button']) {
			$style .= '.block-button, .block-button:visited, .Button, .button, #commentform #submit {
							background: ' . $grad_top . ';
							background: -moz-linear-gradient( ' . $grad_top . ', ' . $grad_bottom . ' );
							background: -webkit-linear-gradient(top, ' . $grad_top . ', ' . $grad_bottom . ' );
							-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'' . $grad_top . '\', EndColorStr=\'' . $grad_bottom . '\')";
							background: linear-gradient( ' . $grad_top . ', ' . $grad_bottom . ' );
							border-color: ' . $grad_bottom . ';
							text-shadow: 0 -1px 1px ' . $font_shadow . ';
							color: ' . $font_color . ';
							-moz-box-shadow: 0 0 5px ' . $box_shadow . ';
							-webkit-box-shadow: 0 0 5px ' . $box_shadow . ';
							box-shadow: 0 0 5px ' . $box_shadow . ';
						}';
			$style .= '.block-button:hover, .button:hover, #commentform #submit:hover {
							background: ' . $grad_top . ';
							background: -moz-linear-gradient( ' . $grad_top . ', ' . $grad_bottom_hover . ' );
							background: -webkit-linear-gradient(top, ' . $grad_top . ', ' . $grad_bottom_hover . ' );
							-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'' . $grad_top . '\', EndColorStr=\'' . $grad_bottom_hover . '\')";
							background: linear-gradient( ' . $grad_top . ', ' . $grad_bottom_hover . ' );
							color: ' . $font_color . ';
						}';
		}
		if ( is_admin() )
			$style = str_replace( '.button', '.colour-preview .button', $style );
                
        // Archive
		$grad_left = $graphene_settings['bg_archive_left'];
		$grad_right = $graphene_settings['bg_archive_right'];
		if ( $grad_left != $graphene_defaults['bg_archive_left'] || $grad_right != $graphene_defaults['bg_archive_right']) {$style .= '.page-title {
				-pie-background: linear-gradient(left top, ' . $grad_left . ', ' . $grad_right . ' );
				background: ' . $grad_top . ';
				background: -moz-linear-gradient(left top, ' . $grad_left . ', ' . $grad_right . ' );
				background: -webkit-linear-gradient(left top, ' . $grad_left . ', ' . $grad_right . ' );
				-ms-filter: "progid:DXImageTransform.Microsoft.gradient(gradientType=1, startColorStr=\'' . $grad_left . '\', EndColorStr=\'' . $grad_right . '\')";
				background: linear-gradient(left top, ' . $grad_left . ', ' . $grad_right . ' );
		}';}
        if ( $graphene_settings['bg_archive_label'] != $graphene_defaults['bg_archive_label']) {$style .= '.page-title{color:'.$graphene_settings['bg_archive_label'].';}';}
        if ( $graphene_settings['bg_archive_text'] != $graphene_defaults['bg_archive_text']) {$style .= '.page-title span{color:'.$graphene_settings['bg_archive_text'].';}';}
		if ( $graphene_settings['bg_archive_textshadow'] != $graphene_defaults['bg_archive_textshadow']) {$style .= '.page-title{text-shadow: 0 -1px 0 '.$graphene_settings['bg_archive_textshadow'].';}';}
		
		// Comments area
		if ( $graphene_settings['bg_comments'] != $graphene_defaults['bg_comments']) {$style .= '#comments ol li.comment, #comments ol li.pingback, #comments ol li.trackback{background-color:'.$graphene_settings['bg_comments'].';}';}
		if ( $graphene_settings['comments_text_colour'] != $graphene_defaults['comments_text_colour']) {$style .= '#comments{color:'.$graphene_settings['comments_text_colour'].';}';}
		if ( $graphene_settings['threaded_comments_border'] != $graphene_defaults['threaded_comments_border']) {
			$style .= '#comments ol.children li.comment{border-color:'.$graphene_settings['threaded_comments_border'].';}';
			$style .= '#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{border-color: '.$graphene_settings['bg_author_comments_border'].';}';
		}
		if ( $graphene_settings['bg_author_comments'] != $graphene_defaults['bg_author_comments']) {$style .= '#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{background-color:'.$graphene_settings['bg_author_comments'].';}';}
		if ( $graphene_settings['bg_author_comments_border'] != $graphene_defaults['bg_author_comments_border']) {$style .= '#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{border-color: '.$graphene_settings['bg_author_comments_border'].';}';}
		if ( $graphene_settings['author_comments_text_colour'] != $graphene_defaults['author_comments_text_colour']) {$style .= '#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{font-color:'.$graphene_settings['author_comments_text_colour'].';}';}
		if ( $graphene_settings['bg_comment_form'] != $graphene_defaults['bg_comment_form']) {$style .= '#commentform{background-color:'.$graphene_settings['bg_comment_form'].';}';}
		if ( $graphene_settings['comment_form_text'] != $graphene_defaults['comment_form_text']) {$style .= '#commentform{color:'.$graphene_settings['comment_form_text'].';}';}
	}
	
	// Admin only
	if ( is_admin() && strpos( $_SERVER["REQUEST_URI"], 'page=graphene_options&tab=display' ) ) {
		
		// Widgets
		if ( $graphene_settings['content_font_colour'] != $graphene_defaults['content_font_colour']) {$style .= '.graphene, .graphene li, .graphene p{color:'.$graphene_settings['content_font_colour'].';}';}
		if ( $graphene_settings['link_colour_normal'] != $graphene_defaults['link_colour_normal']) {$style .= '.graphene a{color:'.$graphene_settings['link_colour_normal'].';}';}
		if ( $graphene_settings['link_colour_visited'] != $graphene_defaults['link_colour_visited']) {$style .= '.graphene a:visited{color:'.$graphene_settings['link_colour_visited'].';}';}
		if ( $graphene_settings['link_colour_hover'] != $graphene_defaults['link_colour_hover']) {$style .= '.graphene a:hover{color:'.$graphene_settings['link_colour_hover'].';}';}
		
		// Slider
		$grad_bottom = $graphene_settings['bg_slider_bottom'];
		$grad_top = $graphene_settings['bg_slider_top'];
		if ( $grad_bottom != $graphene_defaults['bg_slider_bottom'] || $grad_top != $graphene_defaults['bg_slider_top']) {$style .= '#grad-box {
				-pie-background: linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
				background: ' . $grad_top . ';
				background: -moz-linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
				background: -webkit-gradient(linear, left top, right bottom, from( ' . $grad_top . ' ), to( ' . $grad_bottom . ' ) );
				-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'' . $grad_top . '\', EndColorStr=\'' . $grad_bottom . '\')";
				background: linear-gradient(left top, ' . $grad_top . ', ' . $grad_bottom . ' );
                            }';
                }
	}
        
    return $style;
}


/**
 * Build and return the CSS styles custom column width
 *
 * @package Graphene
 * @since 1.6
 * @return string $style CSS styles
*/
function graphene_get_custom_column_width(){
	global $graphene_settings, $graphene_defaults;
	$column_mode = graphene_column_mode();
	$container = $graphene_settings['container_width'];
	$grid = $graphene_settings['grid_width'];
	$gutter = $graphene_settings['gutter_width'];
	$style = '';
	
	/* Custom container width */
	if ( $container != $graphene_defaults['container_width'] ){
		$style .= ".container_16 {width:{$container}px}";
		for ( $i = 1; $i <= 16; $i++ ){
			
			/* Grid */
			$style .= '.container_16 .grid_' . $i . '{width:';
			$style .= ( $grid * $i ) + ( $gutter * ( ( $i * 2 ) - 2) );
			$style .= 'px}';
			
			/* Prefix */
			$style .= '.container_16 .prefix_' . $i . '{padding-left:';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
			
			/* Suffix */
			$style .= '.container_16 .suffix_' . $i . '{padding-right:';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
			
			/* Push */
			$style .= '.container_16 .push_' . $i . '{left:';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
			
			/* Pull */
			$style .= '.container_16 .pull_' . $i . '{left:-';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
		}
	}
	
	/* Custom column width - one-column mode */
	if ( strpos( $column_mode, 'one_col' ) === 0 && ( $container != $graphene_defaults['container_width'] ) ){
		$content = $container - $gutter * 2;
		
		$style .= '.one-column .comment-form-author, .one-column .comment-form-email, .one-column .comment-form-url {width:' . ( ( $content - $gutter * 6 ) / 3 ). 'px}';
		$style .= '.one-column .graphene-form-field {width:' . ( ( ( $content - $gutter * 6 ) / 3 ) - 8 ) . 'px}';
		$style .= '.one-column #commentform textarea {width:' . ( ( $content - $gutter * 2 ) - 8 ) . 'px}';
	}
	
	/* Custom column width - two-column mode */
	$content = $graphene_settings['column_width']['two_col']['content'];
	$content_default = $graphene_defaults['column_width']['two_col']['content'];
	
	if ( strpos( $column_mode, 'two_col' ) === 0 && ( $content != $content_default ) ){
		$sidebar = $graphene_settings['column_width']['two_col']['sidebar'];

		$style .= '#content-main, .container_16 .slider_post {width:' . $content . 'px}';
		$style .= '#sidebar1, #sidebar2 {width:' . $sidebar . 'px}';
		$style .= '.comment-form-author, .comment-form-email, .comment-form-url {width:' . ( ( $content - $gutter * 6 ) / 3 ). 'px}';
		$style .= '.graphene-form-field {width:' . ( ( ( $content - $gutter * 6 ) / 3 ) - 8 ) . 'px}';
		$style .= '#commentform textarea {width:' . ( ( $content - $gutter * 2 ) - 8 ) . 'px}';
	}
	
	/* Custom column width - three-column mode */
	$content = $graphene_settings['column_width']['three_col']['content'];
	$sidebar_left = $graphene_settings['column_width']['three_col']['sidebar_left'];
	$sidebar_right = $graphene_settings['column_width']['three_col']['sidebar_right'];
	$content_default = $graphene_defaults['column_width']['three_col']['content'];
	$sidebar_left_default = $graphene_defaults['column_width']['three_col']['sidebar_left'];
	$sidebar_right_default = $graphene_defaults['column_width']['three_col']['sidebar_right'];
	
	if ( strpos( $column_mode, 'three_col' ) === 0 && ( $content != $content_default || $sidebar_left != $sidebar_left_default || $sidebar_right != $sidebar_right_default ) ){

		$style .= '#content-main, .container_16 .slider_post {width:' . $content . 'px}';
		$style .= '#sidebar1 {width:' . $sidebar_right . 'px}';
		$style .= '#sidebar2 {width:' . $sidebar_left . 'px}';
		$style .= '.three-columns .comment-form-author, .three-columns .comment-form-email, .three-columns .comment-form-url {width:' . ( ( $content - $gutter * 6 ) / 3 ). 'px}';
		$style .= '.three-columns .graphene-form-field {width:' . ( ( ( $content - $gutter * 6 ) / 3 ) - 8 ) . 'px}';
		$style .= '.three-columns #commentform textarea {width:' . ( ( $content - $gutter * 2 ) - 8 ) . 'px}';
	}
	
	return apply_filters( 'graphene_custom_column_width_style', $style );
}
 

/**
 * Sets the various customised styling according to the options set for the theme.
 *
 * @package Graphene
 * @since Graphene 1.0.8
*/
function graphene_custom_style(){
    global $graphene_settings;
	$style = '';
    
    // the custom colours are needed in both the display and admin mode
    $style .= graphene_get_custom_colours();
    
	// only get the custom css styles when were not in the admin mode
    if ( ! is_admin() ) {
        $style .= graphene_get_custom_style();
	
		// always the custom css at the end, this is the most important
	    if ( $graphene_settings['custom_css']) { $style .= $graphene_settings['custom_css']; }
    }
    
    if ( $style ){ echo '<style type="text/css">'."\n".$style."\n".'</style>'."\n"; }
    do_action( 'graphene_custom_style' ); 
}
add_action( 'wp_head', 'graphene_custom_style' );
add_action( 'admin_head', 'graphene_custom_style' );


/**
 * Check to see if there's a favicon.ico in wordpress root directory and add
 * appropriate head element for the favicon
*/
function graphene_favicon(){
	global $graphene_settings;
	if ( $graphene_settings['favicon_url'] ) { ?>
		<link rel="icon" href="<?php echo $graphene_settings['favicon_url']; ?>" type="image/x-icon" />
	<?php
    } elseif ( is_file( ABSPATH . 'favicon.ico' ) ){ ?>
		<link rel="icon" href="<?php echo home_url(); ?>/favicon.ico" type="image/x-icon" />
	<?php }
}
add_action( 'wp_head', 'graphene_favicon' );


/**
 * Add the .htc file for partial CSS3 support in Internet Explorer
*/
function graphene_ie_css3(){ ?>
	<!--[if lte IE 8]>
      <style type="text/css" media="screen">
      	#footer, div.sidebar-wrap, .block-button, .featured_slider, #slider_root, #nav li ul, .pie{behavior: url(<?php echo get_template_directory_uri(); ?>/js/PIE.php);}
        .featured_slider{margin-top:0 !important;}
      </style>
    <![endif]-->
    <?php
}
add_action( 'wp_head', 'graphene_ie_css3' );


/**
 * Fix IE8 image scaling issues when using max-width property on images
*/
function graphene_ie8_img(){ ?>
	<!--[if IE 8]>
    <script type="text/javascript">
        (function( $) {
            var imgs, i, w;
            var imgs = document.getElementsByTagName( 'img' );
            maxwidth = 0.98 * $( '.entry-content' ).width();
            for( i = 0; i < imgs.length; i++ ) {
                w = imgs[i].getAttribute( 'width' );
                if ( w > maxwidth ) {
                    imgs[i].removeAttribute( 'width' );
                    imgs[i].removeAttribute( 'height' );
                }
            }
        })(jQuery);
    </script>
    <![endif]-->
<?php
}
add_action( 'wp_footer', 'graphene_ie8_img' );


/**
 * Add Google Analytics code if tracking is enabled 
 */ 
function graphene_google_analytics(){
	global $graphene_settings;
    if ( $graphene_settings['show_ga']) : ?>
    <!-- BEGIN Google Analytics script -->
    	<?php echo stripslashes( $graphene_settings['ga_code']); ?>
    <!-- END Google Analytics script -->
    <?php endif; 
}
add_action( 'wp_head', 'graphene_google_analytics', 1000);


/**
 * This function prints out the title for the website.
 * If present, the theme will display customised site title structure.
*/
function graphene_title( $title, $sep = '&raquo;', $seplocation = '' ){
	global $graphene_settings;
	$default_title = $title;
	
	if ( is_feed() ){
		
		$title = $default_title;
		
	} elseif ( is_front_page() ) { 
	
		if ( $graphene_settings['custom_site_title_frontpage']) {
			$title = $graphene_settings['custom_site_title_frontpage'];
			$title = str_replace( '#site-name', get_bloginfo( 'name' ), $title);
			$title = str_replace( '#site-desc', get_bloginfo( 'description' ), $title);
		} else {
			$title = get_bloginfo( 'name' ) . " &raquo; " . get_bloginfo( 'description' );
		}
		
	} else {
		
		if ( $graphene_settings['custom_site_title_content'] ) {
			$title = $graphene_settings['custom_site_title_content'];
			$title = str_replace( '#site-name', get_bloginfo( 'name' ), $title );
			$title = str_replace( '#site-desc', get_bloginfo( 'description' ), $title );
			$title = str_replace( '#post-title', $default_title, $title );
		} else {
			$title = $default_title . " &raquo; " . get_bloginfo( 'name' );
		}
	}
	
	return ent2ncr( apply_filters( 'graphene_title', $title ) );
}
add_filter( 'wp_title', 'graphene_title', 10, 3 );
?>