<?php
/**
 * Settings Validator
 * 
 * This file defines the function that validates the theme's options
 * upon submission.
*/
function graphene_settings_validator( $input ){
	
	if (!isset($_POST['graphene_uninstall'])) {
		global $graphene_defaults, $allowedposttags;
		
		// Add <script> tag to the allowed tags in code
		$allowedposttags = array_merge( $allowedposttags, array( 'script' => array( 'type' => array(), 'src' => array() ) ) );
		
		if (isset($_POST['graphene_general'])) {
		
			/* =Slider Options 
			--------------------------------------------------------------------------------------*/
                     
			// Slider category
			if ( isset($input['slider_type']) && !in_array($input['slider_type'], array('latest_posts', 'random', 'posts_pages', 'categories' ) ) ){
				unset($input['slider_type']);
				add_settings_error('graphene_options', 2, __('ERROR: Invalid category to show in slider.', 'graphene'));
			} elseif ( $input['slider_type'] == 'posts_pages' && empty ( $input['slider_specific_posts'] ) ) {
				unset($input['slider_type']);
				add_settings_error('graphene_options', 2, __('ERROR: You must specify the posts/pages to be displayed when you have "Show specific posts/pages" selected for the slider.', 'graphene'));
                        } elseif ( $input['slider_type'] == 'categories' && empty ( $input['slider_specific_categories'] ) ) {
				unset($input['slider_type']);
				add_settings_error('graphene_options', 2, __('ERROR: You must have selected at least one category when you have "Show posts from categories" selected for the slider.', 'graphene'));
			}                        
			// Posts and/or pages to display
			if (isset($input['slider_type']) && $input['slider_type'] == 'posts_pages' && isset($input['slider_specific_posts'])) {
				$input['slider_specific_posts'] = str_replace(' ', '', $input['slider_specific_posts']);
			}
			// Categories to display posts from
			if (isset($input['slider_type']) && $input['slider_type'] == 'categories' && isset($input['slider_specific_categories']) && is_array($input['slider_specific_categories'])){
				if ( in_array ( false, array_map( 'ctype_digit', (array) $input['slider_specific_categories'] ) ) ) {
					unset($input['slider_specific_categories']);
					add_settings_error('graphene_options', 2, __('ERROR: Invalid category selected for the slider categories.', 'graphene'));
				}
			}
			// Exclude categories from posts listing
			$input = graphene_validate_dropdown( $input, 'slider_exclude_categories', array('disabled', 'homepage', 'everywhere'), __('ERROR: Invalid option for the slider categories exclusion from posts listing is specified.', 'graphene') );
			// Display posts from categories in random order
			$input['slider_random_category_posts'] = (isset($input['slider_random_category_posts'])) ? true : false;
			// Number of posts to display
			if (!empty($input['slider_postcount']) && !ctype_digit($input['slider_postcount'])){
				unset($input['slider_postcount']);
				add_settings_error('graphene_options', 2, __('ERROR: The number of posts to displayed in the slider must be a an integer value.', 'graphene'));
			}
			// Slider image
			$input = graphene_validate_dropdown( $input, 'slider_img', array('disabled', 'featured_image', 'post_image', 'custom_url'), __('ERROR: Invalid option for the slider image is specified.', 'graphene') );
			// Custom slider image URL
			$input = graphene_validate_url( $input, 'slider_imgurl', __('ERROR: Bad URL entered for the custom slider image URL.', 'graphene') );
			// Slider display style
			$input = graphene_validate_dropdown( $input, 'slider_display_style', array('thumbnail-excerpt', 'bgimage-excerpt', 'full-post'), __('ERROR: Invalid option for the slider display style is specified.', 'graphene') );
			// Slider height
			$input = graphene_validate_digits( $input, 'slider_height', __('ERROR: The value for slider height must be an integer.', 'graphene'));
			// Slider speed
			$input = graphene_validate_digits( $input, 'slider_speed', __('ERROR: The value for slider speed must be an integer.', 'graphene'));
			// Slider transition speed
			$input = graphene_validate_digits( $input, 'slider_trans_speed', __('ERROR: The value for slider transition speed must be an integer.', 'graphene'));
			// Slider animation
			$input = graphene_validate_dropdown( $input, 'slider_animation', array( 'horizontal-slide', 'vertical-slide', 'fade', 'none' ), __( 'ERROR: Invalid slider animation.', 'graphene' ) );
            // Slider position
			$input['slider_position'] = (isset($input['slider_position'])) ? true : false;
			// Slider disable switch
			$input['slider_disable'] = (isset($input['slider_disable'])) ? true : false;
			
			
			/* =Front Page Options 
			--------------------------------------------------------------------------------------*/
			
			// Front page posts categories
			if ( ! in_array ( '', (array) $input['frontpage_posts_cats'] ) ) {
				if ( in_array ( false, array_map( 'ctype_digit', (array) $input['frontpage_posts_cats'] ) ) ) {
					unset($input['frontpage_posts_cats']);
					add_settings_error('graphene_options', 2, __('ERROR: Invalid category selected for the front page posts categories.', 'graphene'));
				}
			} else {
				$input['frontpage_posts_cats'] = $graphene_defaults['frontpage_posts_cats'];
			}
			
			
			/* =Homepage Panes
			--------------------------------------------------------------------------------------*/
			
			// Type of content to show
			$input = graphene_validate_dropdown( $input, 'show_post_type', array('latest-posts', 'cat-latest-posts', 'posts'), __('ERROR: Invalid option for the type of content to show in homepage panes.', 'graphene') );
			// Number of latest posts to display
			$input = graphene_validate_digits( $input, 'homepage_panes_count', __('ERROR: The value for the number of latest posts to display in homepage panes must be an integer.', 'graphene') );
			// Categories to show latest posts from
                        if ($input['show_post_type'] == 'cat-latest-posts' && isset($input['homepage_panes_cat']) && is_array($input['homepage_panes_cat'])) {
                            if ( in_array ( false, array_map( 'ctype_digit', (array) $input['homepage_panes_cat'] ) ) ) {
                                unset($input['slider_specific_categories']);
                                add_settings_error('graphene_options', 2, __('ERROR: Invalid category selected for the latest posts to show from in the homepage panes.', 'graphene'));
                            }
                        }			
			// Posts and/or pages to display
			if ($input['show_post_type'] == 'posts' && isset($input['homepage_panes_posts'])) {
				$input['homepage_panes_posts'] = str_replace(' ', '', $input['homepage_panes_posts']);
			}
			// Disable switch
			$input['disable_homepage_panes'] = (isset($input['disable_homepage_panes'])) ? true : false;
			
            
			/* =Comments Options
			--------------------------------------------------------------------------------------*/
			
			$input = graphene_validate_dropdown( $input, 'comments_setting', array('wordpress', 'disabled_pages', 'disabled_completely'), __('ERROR: Invalid option for the comments option.', 'graphene') );
			
			            
			/* =Child Page Options
			--------------------------------------------------------------------------------------*/
			
			// Hide parent box if content is empty
			$input['hide_parent_content_if_empty'] = (isset($input['hide_parent_content_if_empty'])) ? true : false;                        
			// Child page listing
			$input = graphene_validate_dropdown( $input, 'child_page_listing', array('hide', 'show_always', 'show_if_parent_empty'), __('ERROR: Invalid option for the child page listings.', 'graphene') );
			
                        
			/* = RSS Feed Options
			--------------------------------------------------------------------------------------*/
			
			// Use a custom RSS feed
			$input['use_custom_rss_feed'] = (isset($input['use_custom_rss_feed'])) ? true : false;                        
			// Child page listing
			if ( $input['use_custom_rss_feed'] && empty ( $input['custom_rss_feed_url'] ) ) {
				unset($input['slider_type']);
				add_settings_error('graphene_options', 2, __('ERROR: You must supply an URL for the custom RSS Feed.', 'graphene'));
			} else {     
				$input = graphene_validate_url( $input, 'custom_rss_feed_url', __('ERROR: Bad URL entered for the custom RSS Feed URL.', 'graphene') );			
			}
			                                                
                        
			/* =Widget Area Options
			--------------------------------------------------------------------------------------*/
			
			$input['enable_header_widget'] = (isset($input['enable_header_widget'])) ? true : false;
			$input['alt_home_sidebar'] = (isset($input['alt_home_sidebar'])) ? true : false;
			$input['alt_home_footerwidget'] = (isset($input['alt_home_footerwidget'])) ? true : false;
                        
                        
			/* =Top Bar Options
			--------------------------------------------------------------------------------------*/
			// Hide top bar
            $input['hide_top_bar'] = (isset($input['hide_top_bar'])) ? true : false;
			// Open in new window
			$input['social_media_new_window'] = (isset($input['social_media_new_window'])) ? true : false;			
			/* Social profiles */
			$social_profiles = ( ! empty( $input['social_profiles'] ) ) ? $input['social_profiles'] : array();
		
			if ( ! empty( $social_profiles ) ){
				$ix = 0;
				unset( $input['social_profiles'] );
				foreach ( $social_profiles as $social_icon ){
					if ( ! empty( $social_icon['type'] ) ){
						$input['social_profiles'][$ix]['type'] = $social_icon['type'];
						$input['social_profiles'][$ix]['name'] = $social_icon['name'];
						$input['social_profiles'][$ix]['title'] = esc_attr( $social_icon['title'] );
						$social_icon['url'] = esc_url_raw( $social_icon['url'] );
						if ( empty( $social_icon['url'] ) && $social_icon['type'] != 'rss' ){
							add_settings_error( 'graphene_options', 2, sprintf( __( 'ERROR: Bad URL entered for the %s URL.'), $social_icon['name'] ) );
						} else {
							$input['social_profiles'][$ix]['url'] = $social_icon['url'];
						}
						
						if ( $social_icon['type'] == 'custom' ){
							$input['social_profiles'][$ix]['icon_url'] = $social_icon['icon_url'];
						}  
						$ix++;
					}                                
				}
			} else {
				$input['social_profiles'] = array( 0 => false );
			}
			            
                        
			/* =Social Sharing Options
			--------------------------------------------------------------------------------------*/
			
			// Show social sharing button switch
			$input['show_addthis'] = (isset($input['show_addthis'])) ? true : false;
			// Show buttons in pages switch
			$input['show_addthis_page'] = (isset($input['show_addthis_page'])) ? true : false;
			// Show buttons in home and archive pages
			$input['show_addthis_archive'] = (isset($input['show_addthis_archive'])) ? true : false;
			// Social sharing buttons location
			$input = graphene_validate_dropdown( $input, 'addthis_location', array('post-bottom', 'post-top', 'top-bottom'), __('ERROR: Invalid option for the social sharing buttons location.', 'graphene') );
			// Social sharing buttons code
			$input['addthis_code'] = trim( stripslashes( $input['addthis_code'] ) );
			
                        
			/* =Adsense Options
			--------------------------------------------------------------------------------------*/
			
			// Show Adsense ads switch
			$input['show_adsense'] = (isset($input['show_adsense'])) ? true : false;
			// Show ads on front page switch
			$input['adsense_show_frontpage'] = (isset($input['adsense_show_frontpage'])) ? true : false;
			// Adsense code
			$input['adsense_code'] = wp_kses_post( $input['adsense_code'] );
			
						
			/* =Google Analytics Options
			--------------------------------------------------------------------------------------*/
			
			// Enable tracking switch
			$input['show_ga'] = (isset($input['show_ga'])) ? true : false;
			// Tracking code
			$input['ga_code'] = wp_kses_post($input['ga_code']);
			
                        
			/* =Footer Options
			--------------------------------------------------------------------------------------*/
			
			// Show creative common logo switch
			$input['show_cc'] = (isset($input['show_cc'])) ? true : false;
			// Copyright HTML
			$input['copy_text'] = wp_kses_post($input['copy_text']);
			// Hide copyright switch
			$input['hide_copyright'] = (isset($input['hide_copyright'])) ? true : false;
			// Hide "Return to top" link switch
			$input['hide_return_top'] = (isset($input['hide_return_top'])) ? true : false;
                        
                        
			/* =Print Options
			--------------------------------------------------------------------------------------*/  
			
			// Enable print CSS switch
			$input['print_css'] = (isset($input['print_css'])) ? true : false;
			// Show print button switch
			$input['print_button'] = (isset($input['print_button'])) ? true : false;
	
			
			
		} // Ends the General options
		
		
		if (isset($_POST['graphene_display'])) {
			
			/* =Header Display Options
			--------------------------------------------------------------------------------------*/  
			
			$input['light_header'] = (isset($input['light_header'])) ? true : false;
			$input['link_header_img'] = (isset($input['link_header_img'])) ? true : false;
			$input['featured_img_header'] = (isset($input['featured_img_header'])) ? true : false;
			$input['use_random_header_img'] = (isset($input['use_random_header_img'])) ? true : false;
			$input = graphene_validate_digits( $input, 'header_img_height', __('ERROR: The value for the header image height must be an integer.', 'graphene') );
			$input = graphene_validate_dropdown( $input, 'search_box_location', array('top_bar', 'nav_bar', 'disabled'), __('ERROR: Invalid option for the Search box location.', 'graphene') );
			
			
			/* =Column Options
			--------------------------------------------------------------------------------------*/
			$input = graphene_validate_dropdown( $input, 'column_mode', array('one_column', 'two_col_left', 'two_col_right', 'three_col_left', 'three_col_right', 'three_col_center'), __('ERROR: Invalid option for the column mode.', 'graphene') );
			$input = graphene_validate_dropdown( $input, 'bbp_column_mode', array('one_column', 'two_col_left', 'two_col_right', 'three_col_left', 'three_col_right', 'three_col_center'), __('ERROR: Invalid option for the bbPress column mode.', 'graphene') );
			
			
			/* =Column Width Options
			--------------------------------------------------------------------------------------*/
			foreach( $input['column_width'] as $column_mode => $columns ){
				foreach( $columns as $column => $width ){
					$input = graphene_validate_column_width( $input, $column_mode, $column, sprintf( __( 'ERROR: Invalid width for %s. Width value must be positive number without units.', 'graphene' ), $column_mode . ' ' . $column ) );
				}
			}
                        
                        
			/* =Post Display Options
			--------------------------------------------------------------------------------------*/                        
			$input['hide_post_author'] = (isset($input['hide_post_author'])) ? true : false;
			$input = graphene_validate_dropdown( $input, 'post_date_display', array('hidden', 'icon_no_year', 'icon_plus_year', 'text'), __('ERROR: Invalid option for the post date display.', 'graphene') ); 
			$input['hide_post_cat'] = (isset($input['hide_post_cat'])) ? true : false;
			$input['hide_post_tags'] = (isset($input['hide_post_tags'])) ? true : false;
			$input['hide_post_commentcount'] = (isset($input['hide_post_commentcount'])) ? true : false;
			$input['show_post_avatar'] = (isset($input['show_post_avatar'])) ? true : false;
			$input['show_post_author'] = (isset($input['show_post_author'])) ? true : false;
                        
                        
			/* =Excerpts Display Options
			--------------------------------------------------------------------------------------*/     
			$input['posts_show_excerpt'] = (isset($input['posts_show_excerpt'])) ? true : false;                        
			$input['archive_full_content'] = (isset($input['archive_full_content'])) ? true : false;					
			$input['show_excerpt_more'] = (isset($input['show_excerpt_more'])) ? true : false;
                        
                        
			/* =Comments Display Options
			--------------------------------------------------------------------------------------*/
			$input['hide_allowedtags'] = (isset($input['hide_allowedtags'])) ? true : false;
                        

			/* =Colour Options
			--------------------------------------------------------------------------------------*/
			$colour_opts = array(
								// Content area
								'bg_content_wrapper',
								'bg_content',
								'bg_meta_border',
								'bg_post_top_border',
								'bg_post_bottom_border',
								
								// Widgets
								'bg_widget_item',
								'bg_widget_list',
								'bg_widget_header_border',
								'bg_widget_title',
								'bg_widget_title_textshadow',
								'bg_widget_header_bottom',
								'bg_widget_header_top',
								'bg_widget_box_shadow',
								
								// Slider
								'bg_slider_top',
								'bg_slider_bottom',
								
								// Block button
								'bg_button',
								'bg_button_label',
								'bg_button_label_textshadow',
								'bg_button_box_shadow',
								
								// Archive
								'bg_archive_left',
								'bg_archive_right',
								'bg_archive_label',
								'bg_archive_text',
								'bg_archive_textshadow',
								
								// Comments
								'bg_comments',
								'comments_text_colour',
								'threaded_comments_border',
								'bg_author_comments',
								'bg_author_comments_border',
								'author_comments_text_colour',
								'bg_comment_form',
								'comment_form_text',
								
								// Text
								'content_font_colour',
								'title_font_colour',
								'link_colour_normal',
								'link_colour_visited',
								'link_colour_hover',								
							);
			
			$input = graphene_validate_colours( $input, $colour_opts );
			
                        
			/* =Footer Widget Display Options
			--------------------------------------------------------------------------------------*/
			// Number of columns to display
			$input = graphene_validate_digits( $input, 'footerwidget_column', __('ERROR: The number of columns to be displayed in the footer widget must be a an integer value.', 'graphene' ) );
			
			
			/* =Navigation Menu Display Options
			--------------------------------------------------------------------------------------*/
			$input = graphene_validate_digits( $input, 'navmenu_child_width', __('ERROR: The width of the submenu must be a an integer value.', 'graphene' ) );
			$input['navmenu_home_desc'] = wp_kses_post( $input['navmenu_home_desc'] );
			$input['disable_menu_desc'] = (isset($input['disable_menu_desc'])) ? true : false;
			
			/* =Miscellaneous Display Options
			--------------------------------------------------------------------------------------*/
			$input['custom_site_title_frontpage'] = strip_tags( $input['custom_site_title_frontpage'] );
			$input['custom_site_title_content'] = strip_tags( $input['custom_site_title_content'] );
			$input = graphene_validate_url( $input, 'favicon_url', __( 'ERROR: Bad URL entered for the favicon URL.', 'graphene' ) );
			$input['disable_editor_style'] = (isset($input['disable_editor_style'])) ? true : false;
			
			/* =Custom CSS Options 
			--------------------------------------------------------------------------------------*/
			$input['custom_css'] = strip_tags( $input['custom_css'] );
		
		} // Ends the Display options
                
		if ( isset($_POST['graphene_advanced'] ) ) {
			$input['enable_preview'] = ( isset( $input['enable_preview'] ) ) ? true : false; 
			
			if ( isset( $input['widget_hooks'] ) && is_array( $input['widget_hooks'] ) ) {
				if ( ! ( array_intersect( $input['widget_hooks'], graphene_get_action_hooks( true ) ) === $input['widget_hooks'] ) ) {
					unset( $input['widget_hooks'] );
					add_settings_error( 'graphene_options', 2, __( 'ERROR: Invalid action hook selected widget action hooks.', 'graphene' ) );
				}
			} else {
				$input['widget_hooks'] = $graphene_defaults['widget_hooks'];
			}
		} // Ends the Advanced options
		
		
		// Merge the new settings with the previous one (if exists) before saving
		$input = array_merge( get_option('graphene_settings', array() ), $input );
		
		/* Only save options that have different values than the default values */
		foreach ( $input as $key => $value ){
			if ( ( $graphene_defaults[$key] === $value || $value === '' ) && $key != 'db_version' ) {
				unset( $input[$key] );
			}
		}
		
	} // Closes the uninstall conditional
	
	return $input;
}


/**
 * Define the data validation functions
*/
function graphene_validate_digits( $input, $option_name, $error_message ){
	global $graphene_defaults;
	if ( '0' === $input[$option_name] || ! empty($input[$option_name] ) ){
		if (!ctype_digit($input[$option_name])) {
			$input[$option_name] = $graphene_defaults[$option_name];
			add_settings_error('graphene_options', 2, $error_message);
		}
	} else {
		$input[$option_name] = $graphene_defaults[$option_name];
	}
	return $input;
}

function graphene_validate_column_width( $input, $column_mode, $option_name, $error_message ){
	global $graphene_defaults;
	if ( '0' === $input['column_width'][$column_mode][$option_name] || ! empty($input['column_width'][$column_mode][$option_name] ) ){
		$width = $input['column_width'][$column_mode][$option_name];
		if ( ! ( is_numeric( $width ) && $width >= 0 ) ) {
			$input['column_width'] = $graphene_defaults['column_width'];
			$input['container_width'] = $graphene_defaults['container_width'];
			add_settings_error('graphene_options', 2, $error_message);
		}
	} else {
		$input['column_width'] = $graphene_defaults['column_width'];
		$input['container_width'] = $graphene_defaults['container_width'];
	}
	return $input;
}

function graphene_validate_dropdown( $input, $option_name, $possible_values, $error_message ){
	
	if (isset($input[$option_name]) && !in_array($input[$option_name], $possible_values)){
		unset($input[$option_name]);
		add_settings_error('graphene_options', 2, $error_message);
	}
	return $input;
}

function graphene_validate_url( $input, $option_name, $error_message ) {
	global $graphene_defaults;
	if (!empty($input[$option_name])){
		$input[$option_name] = esc_url_raw($input[$option_name]);
		if ($input[$option_name] == '') {
			$input[$option_name] = $graphene_defaults[$option_name];
			add_settings_error('graphene_options', 2, $error_message);
		}	
	}	
	return $input;
}

function graphene_validate_colours( $input, $options ) {
	global $graphene_defaults;
	foreach ( $options as $option ){
		if ( ! empty( $input[$option] ) ){
			if ( stripos( $input[$option], '#' ) !== 0 ) {
				$input[$option] = '#' . $input[$option];
			}	
		} else {
			$input[$option] = $graphene_defaults[$option];
		}
	}
	return $input;
}
?>