<?php if (!function_exists('bfa_get_options')) {
	function bfa_get_options() {
		global $options, $bfa_ata;

		if (get_option('bfa_ata4') === FALSE) {

			// 268 Old ATA 3.X options:
			$bfa_ata3 = array(
			"start_here",
			"import_settings",
			"use_bfa_seo",
			"homepage_meta_description",
			"homepage_meta_keywords",
			"add_blogtitle",
			"title_separator_code",
			"archive_noindex",
			"cat_noindex",
			"tag_noindex",
			"h1_on_single_pages",
			"nofollow",
			"body_style",
			"link_color",
			"link_hover_color",
			"link_default_decoration",
			"link_hover_decoration",
			"link_weight",
			"layout_width",
			"layout_min_width",
			"layout_max_width",
			"layout_style",
			"layout_style_leftright_padding",
			"favicon_file",
			"configure_header",
			"logoarea_style",
			"logo",
			"logo_style",
			"blog_title_show",
			"blog_title_style",
			"blog_title_weight",
			"blog_title_color",
			"blog_title_color_hover",
			"blog_tagline_show",
			"blog_tagline_style",
			"show_search_box",
			"searchbox_style",
			"searchbox_text",
			"horbar1",
			"horbar2",
			"header_image_info",
			"header_image_javascript",
			"header_image_javascript_preload",
			"header_image_clickable",
			"headerimage_height",
			"headerimage_alignment",
			"header_opacity_left",
			"header_opacity_left_width",
			"header_opacity_left_color",
			"header_opacity_right",
			"header_opacity_right_width",
			"header_opacity_right_color",
			"overlay_blog_title",
			"overlay_blog_tagline",
			"overlay_box_style",
			"rss_settings_info"	,
			"rss_box_width",
			"show_posts_icon",
			"post_feed_link",
			"post_feed_link_title",
			"show_comments_icon",
			"comment_feed_link",
			"comment_feed_link_title",
			"show_email_icon",
			"email_subscribe_link",
			"email_subscribe_link_title",
			"feedburner_email_id",
			"feedburner_old_new",
			"animate_page_menu_bar",
			"home_page_menu_bar",
			"exclude_page_menu_bar",
			"levels_page_menu_bar",
			"sorting_page_menu_bar",
			"titles_page_menu_bar",
			"page_menu_1st_level_not_linked",
			"anchor_border_page_menu_bar",
			"page_menu_bar_background_color",
			"page_menu_bar_background_color_hover",
			"page_menu_bar_background_color_parent",
			"page_menu_font",
			"page_menu_bar_link_color",
			"page_menu_bar_link_color_hover",
			"page_menu_transform",
			"page_menu_arrows",
			"page_menu_submenu_width",
			"animate_cat_menu_bar",
			"home_cat_menu_bar",
			"exclude_cat_menu_bar",
			"levels_cat_menu_bar",
			"sorting_cat_menu_bar",
			"order_cat_menu_bar",
			"titles_cat_menu_bar",
			"add_descr_cat_menu_links",
			"default_cat_descr_text",
			"anchor_border_cat_menu_bar",
			"cat_menu_bar_background_color",
			"cat_menu_bar_background_color_hover",
			"cat_menu_bar_background_color_parent",
			"cat_menu_font",
			"cat_menu_bar_link_color",
			"cat_menu_bar_link_color_hover",
			"cat_menu_transform",
			"cat_menu_arrows",
			"cat_menu_submenu_width",
			"center_column_style",
			"content_above_loop",
			"content_inside_loop",
			"content_below_loop",
			"content_not_found",
			"next_prev_orientation",
			"home_multi_next_prev",
			"home_single_next_prev",
			"multi_next_prev_newer",
			"multi_next_prev_older",
			"single_next_prev_newer",
			"single_next_prev_older",
			"comments_next_prev_newer",
			"comments_next_prev_older",
			"location_comments_next_prev",
			"next_prev_style_comments_above",
			"next_prev_style_comments_below",
			"next_prev_comments_pagination",
			"location_multi_next_prev",
			"location_single_next_prev",
			"next_prev_style_top",
			"next_prev_style_middle",
			"next_prev_style_bottom",
			"leftcol_on",
			"left_col_pages_exclude",
			"left_col_cats_exclude",
			"leftcol2_on",
			"left_col2_pages_exclude",
			"left_col2_cats_exclude",
			"rightcol_on",
			"right_col_pages_exclude",
			"right_col_cats_exclude",
			"rightcol2_on",
			// legacy "ight_col2" besides "right_col2" due to typo in "bfa_theme_options.php" in ATA 3.4.6
			"ight_col2_pages_exclude",
			"right_col2_pages_exclude",
			//
			"right_col2_cats_exclude",
			"left_sidebar_width",
			"left_sidebar2_width",
			"right_sidebar_width",
			"right_sidebar2_width",
			"left_sidebar_style",
			"left_sidebar2_style",
			"right_sidebar_style",
			"right_sidebar2_style",
			"widget_container",
			"widget_title_box",
			"widget_title",
			"widget_content",
			"widget_lists",
			"widget_lists2",
			"widget_lists3",
			"category_widget_display_type",
			"select_font_size",
			"widget_areas_reset",
			"widget_areas_info",
			"post_kicker_home",
			"post_kicker_multi",
			"post_kicker_single",
			"post_kicker_page",
			"post_byline_home",
			"post_byline_multi",
			"post_byline_single",
			"post_byline_page",
			"post_footer_home",
			"post_footer_multi",
			"post_footer_single",
			"post_footer_page",
			"post_container_style",
			"post_container_sticky_style",
			"post_kicker_style",
			"post_kicker_style_links",
			"post_kicker_style_links_hover",
			"post_headline_style",
			"post_headline_style_text",
			"post_headline_style_links",
			"post_headline_style_links_hover",
			"post_byline_style",
			"post_byline_style_links",
			"post_byline_style_links_hover",
			"post_bodycopy_style",
			"post_footer_style",
			"post_footer_style_links",
			"post_footer_style_links_hover",
			"excerpt_length",
			"dont_strip_excerpts",
			"custom_read_more",
			"excerpts_home",
			"full_posts_homepage",
			"excerpts_category",
			"excerpts_archive",
			"excerpts_tag",
			"excerpts_search",
			"excerpts_author",
			"post_thumbnail_width",
			"post_thumbnail_height",
			"post_thumbnail_crop",
			"post_thumbnail_css",
			"more_tag",
			"author_highlight",
			"author_highlight_color",
			"author_highlight_border_color",
			"comment_background_color",
			"comment_alt_background_color",
			"comment_border",
			"comment_author_size",
			"comment_reply_link_text",
			"comment_edit_link_text",
			"comment_moderation_text",
			"comments_are_closed_text",
			"comments_on_pages",
			"separate_trackback",
			"avatar_size",
			"avatar_style",
			"show_xhtml_tags",
			"comment_form_style",
			"submit_button_style",
			"comment_display_order",
			"footer_style",
			"footer_style_links",
			"footer_style_links_hover",
			"footer_style_content",
			"sticky_layout_footer",
			"footer_show_queries",
			"table",
			"table_caption",
			"table_th",
			"table_td",
			"table_tfoot_td",
			"table_zebra_stripes",
			"table_zebra_td",
			"table_hover_rows",
			"table_hover_td",
			"form_input_field_style",
			"form_input_field_background",
			"highlight_forms",
			"highlight_forms_style",
			"button_style",
			"button_style_hover",
			"blockquote_style",
			"blockquote_style_2nd_level",
			"post_image_style",
			"post_image_caption_style",
			"image_caption_text",
			"html_inserts_header",
			"html_inserts_body_tag",
			"html_inserts_body_top",
			"html_inserts_body_bottom",
			"html_inserts_css",
			"archives_page_id",
			"archives_date_show",
			"archives_date_title",
			"archives_date_type",
			"archives_date_limit",
			"archives_date_count",
			"archives_category_show",
			"archives_category_title",
			"archives_category_count",
			"archives_category_depth",
			"archives_category_orderby",
			"archives_category_order",
			"archives_category_feed",
			"css_external",
			"javascript_external",
			"pngfix_selectors",
			"css_compress",
			"allow_debug",
			
			"IEDocType",
			"overlay_header_image"
			);

			// If no old settings exit, use the new 'default' style
			$old_setting_exists = 'no';

			foreach ($bfa_ata3 as $old_option) {
				if (get_option( 'bfa_ata_' . $old_option ) !== FALSE )
					$old_setting_exists = 'yes';
			}
			
			
			// Separate option bfa_widget_areas
			if (get_option('bfa_widget_areas') !== FALSE) {
				$all_old_widget_areas = get_option('bfa_widget_areas');
				foreach ( $all_old_widget_areas as $old_widget_area) {
					if ( isset($old_widget_area) )
						$old_setting_exists = 'yes';
				}
			}
			// toArray: turn object into array for PHP < 5.1 - included JSON.php does not return array with ,TRUE
			/*
			if ( $old_setting_exists == 'yes' ) 
				$bfa_ata_default = toArray(json_decode(bfa_file_get_contents( TEMPLATEPATH . '/styles/ata-classic.txt' ), TRUE));
			else
				$bfa_ata_default = toArray(json_decode(bfa_file_get_contents( TEMPLATEPATH . '/styles/ata-classic.txt' ), TRUE));	
			*/
			$templateURL = get_bloginfo('template_directory');
			$default_options = '{"IEDocType":"None","overlay_header_image":"","page_post_options":"No","use_bfa_seo":"No","homepage_meta_description":"","homepage_meta_keywords":"","add_blogtitle":"Page Title - Blog Title","title_separator_code":"1","archive_noindex":"No","cat_noindex":"No","tag_noindex":"No","h1_on_single_pages":"Yes","nofollow":"No","body_style":"font-family: tahoma, arial, sans-serif;\r\nfont-size: 0.8em;\r\ncolor: #000000;\r\nbackground: #ffffff;","link_color":"666666","link_hover_color":"CC0000","link_default_decoration":"none","link_hover_decoration":"underline","link_weight":"bold","layout_width":"99%","layout_min_width":"","layout_max_width":"","layout_style":"padding: 0;","layout_style_leftright_padding":"0","favicon_file":"new-favicon.ico","configure_header":"%pages %logo %bar1 %image %bar2","logoarea_style":"","logo":"logo.png","logo_style":"margin: 0 10px 0 0;","blog_title_show":"Yes","blog_title_style":"margin: 0;\npadding: 0;\nletter-spacing: -1px;\nline-height: 1.0em;\nfont-family: tahoma, arial, sans-serif;\nfont-size: 240%;","blog_title_weight":"bold","blog_title_color":"666666","blog_title_color_hover":"000000","blog_tagline_show":"Yes","blog_tagline_style":"margin: 0;\npadding: 0;\nfont-size: 1.2em;\nfont-weight: bold;\ncolor: #666666;","show_search_box":"Yes","searchbox_style":"border: 1px dashed #cccccc;\nborder-bottom: 0;\nwidth: 200px;\nmargin: 0;\npadding: 0;","searchbox_text":"","horbar1":"height: 5px;\nbackground: #ffffff;\nborder-top: dashed 1px #cccccc;","horbar2":"height: 5px;\nbackground: #ffffff;\nborder-bottom: dashed 1px #cccccc;","header_image_javascript":"0","header_image_sort_or_shuffle":"Sort","crossslide_fade":"0","header_image_javascript_preload":"Yes","header_image_clickable":"No","headerimage_height":"150","headerimage_alignment":"top center","header_opacity_left":"40","header_opacity_left_width":"200","header_opacity_left_color":"FFFFFF","header_opacity_right":"40","header_opacity_right_width":"200","header_opacity_right_color":"FFFFFF","overlay_blog_title":"No","overlay_blog_tagline":"No","overlay_box_style":"margin-top: 30px;\r\nmargin-left: 30px;","rss_box_width":"280","show_posts_icon":"Yes","post_feed_link":"Posts","post_feed_link_title":"Subscribe to the POSTS feed","show_comments_icon":"Yes","comment_feed_link":"Comments","comment_feed_link_title":"Subscribe to the COMMENTS feed","show_email_icon":"No","email_subscribe_link":"By Email","email_subscribe_link_title":"Subscribe by EMAIL","feedburner_email_id":"","feedburner_old_new":"New - at feedburner.google.com","animate_page_menu_bar":"No","home_page_menu_bar":"Home","exclude_page_menu_bar":"","levels_page_menu_bar":"0","sorting_page_menu_bar":"menu_order","titles_page_menu_bar":"No","page_menu_1st_level_not_linked":"No","anchor_border_page_menu_bar":"dashed 1px #cccccc","page_menu_bar_background_color":"FFFFFF","page_menu_bar_background_color_hover":"EEEEEE","page_menu_bar_background_color_parent":"DDDDDD","page_menu_font":"11px Arial, Verdana, sans-serif","page_menu_bar_link_color":"777777","page_menu_bar_link_color_hover":"000000","page_menu_transform":"uppercase","page_menu_arrows":"black","page_menu_submenu_width":"11","animate_cat_menu_bar":"No","home_cat_menu_bar":"","exclude_cat_menu_bar":"","levels_cat_menu_bar":"0","sorting_cat_menu_bar":"ID","order_cat_menu_bar":"ASC","titles_cat_menu_bar":"No","add_descr_cat_menu_links":"No","default_cat_descr_text":"View all posts filed under<br \/>%category%","anchor_border_cat_menu_bar":"solid 1px #000000","cat_menu_bar_background_color":"777777","cat_menu_bar_background_color_hover":"CC0000","cat_menu_bar_background_color_parent":"000000","cat_menu_font":"11px Arial, Verdana, sans-serif","cat_menu_bar_link_color":"FFFFFF","cat_menu_bar_link_color_hover":"FFFFFF","cat_menu_transform":"uppercase","cat_menu_arrows":"white","cat_menu_submenu_width":"11","center_column_style":"padding: 10px 15px;","content_above_loop":"<?php \/* For MULTI post pages if activated at ATO -> Next\/Previous Navigation: *\/\r\nbfa_next_previous_page_links(\'Top\'); ?>\r\n","content_inside_loop":"<?php \/* For SINGLE post pages if activated at ATO -> Next\/Previous Navigation: *\/\r\nbfa_next_previous_post_links(\'Top\'); ?>\r\n\r\n<?php \/* Post Container starts here *\/\r\nif ( function_exists(\'post_class\') ) { ?>\r\n<div <?php if ( is_page() ) { post_class(\'post\'); } else { post_class(); } ?> id=\"post-<?php the_ID(); ?>\">\r\n<?php } else { ?>\r\n<div class=\"<?php echo ( is_page() ? \'page \' : \'\' ) . \'post\" id=\"post-\'; the_ID(); ?>\">\r\n<?php } ?>\r\n\r\n<?php bfa_post_kicker(\'<div class=\"post-kicker\">\',\'<\/div>\'); ?>\r\n\r\n<?php bfa_post_headline(\'<div class=\"post-headline\">\',\'<\/div>\'); ?>\r\n\r\n<?php bfa_post_byline(\'<div class=\"post-byline\">\',\'<\/div>\'); ?>\r\n\r\n<?php bfa_post_bodycopy(\'<div class=\"post-bodycopy clearfix\">\',\'<\/div>\'); ?>\r\n\r\n<?php bfa_post_pagination(\'<p class=\"post-pagination\"><strong>\'.__(\'Pages:\',\'atahualpa\').\'<\/strong>\',\'<\/p>\'); ?>\r\n\r\n<?php bfa_post_footer(\'<div class=\"post-footer\">\',\'<\/div>\'); ?>\r\n\r\n<\/div><!-- \/ Post -->","content_below_loop":"<?php \/* Displayed on SINGLE post pages if activated at ATO -> Next\/Previous Navigation: *\/\r\nbfa_next_previous_post_links(\'Middle\'); ?>\r\n\r\n<?php \/* Load Comments template (on single post pages, and static pages, if set on options page): *\/\r\nbfa_get_comments(); ?>\r\n\r\n<?php \/* Displayed on SINGLE post pages if activated at ATO -> Next\/Previous Navigation: *\/\r\nbfa_next_previous_post_links(\'Bottom\'); ?>\r\n\t\t\r\n<?php \/* Archives Pages. Displayed on a specific static page, if configured at ATO -> Archives Pages: *\/\r\nbfa_archives_page(\'<div class=\"archives-page\">\',\'<\/div>\'); ?>\r\n\t\t\t\r\n<?php \/* Displayed on MULTI post pages if activated at ATO -> Next\/Previous Navigation: *\/\r\nbfa_next_previous_page_links(\'Bottom\'); ?>","content_not_found":"<h2><?php _e(\'Not Found\',\'atahualpa\'); ?><\/h2>\r\n<p><?php _e(\"Sorry, but you are looking for something that isn\'t here.\",\"atahualpa\"); ?><\/p>","next_prev_orientation":"Newer Left, Older Right","home_multi_next_prev":"","home_single_next_prev":"","multi_next_prev_newer":"&laquo; Newer Entries","multi_next_prev_older":"Older Entries &raquo;","single_next_prev_newer":"&laquo; %link","single_next_prev_older":"%link &raquo;","single_next_prev_same_cat":"No","comments_next_prev_newer":"Newer Comments &raquo;","comments_next_prev_older":"&laquo; Older Comments","location_comments_next_prev":"Above and Below Comments","next_prev_style_comments_above":"margin: 0 0 10px 0;\npadding: 5px 0 5px 0;","next_prev_style_comments_below":"margin: 0 0 10px 0;\npadding: 5px 0 5px 0;","next_prev_comments_pagination":"Yes","location_multi_next_prev":"Bottom","location_single_next_prev":"Top","next_prev_style_top":"margin: 0 0 10px 0;\npadding: 0 0 10px 0;\nborder-bottom: dashed 1px #cccccc;","next_prev_style_middle":"margin: 10px 0 20px 0;\npadding: 10px 0 10px 0;\nborder-top: dashed 1px #cccccc;\nborder-bottom: dashed 1px #cccccc;","next_prev_style_bottom":"margin: 20px 0 0 0;\npadding: 10px 0 0 0;\nborder-top: dashed 1px #cccccc;","leftcol_on":{"homepage":"on","frontpage":"on","single":"on","page":"on","category":"on","date":"on","tag":"on","taxonomy":"on","search":"on","author":"on","404":"on","attachment":"on","check-if-saved-once":false},"left_col_pages_exclude":"","left_col_cats_exclude":"","leftcol2_on":{"check-if-saved-once":false},"left_col2_pages_exclude":"","left_col2_cats_exclude":"","rightcol_on":{"homepage":"on","frontpage":"on","single":"on","page":"on","category":"on","date":"on","tag":"on","taxonomy":"on","search":"on","author":"on","404":"on","attachment":"on","check-if-saved-once":false},"right_col_pages_exclude":"","right_col_cats_exclude":"","rightcol2_on":{"check-if-saved-once":false},"right_col2_pages_exclude":"","right_col2_cats_exclude":"","left_sidebar_width":"200","left_sidebar2_width":"200","right_sidebar_width":"200","right_sidebar2_width":"200","left_sidebar_style":"border-right: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;","left_sidebar2_style":"border-right: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;","right_sidebar_style":"border-left: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;","right_sidebar2_style":"border-left: dashed 1px #CCCCCC;\npadding: 10px 10px 10px 10px;\nbackground: #ffffff;","widget_container":"margin: 0 0 15px 0;","widget_title_box":"","widget_title":"font-size: 1.6em;\nfont-weight: bold;","widget_content":"","widget_lists":{"li-margin-left":0,"link-weight":"normal","link-padding-left":5,"link-border-left-width":7,"link-color":"666666","link-hover-color":"000000","link-border-left-color":"cccccc","link-border-left-hover-color":"000000"},"widget_lists2":{"li-margin-left":5,"link-weight":"normal","link-padding-left":5,"link-border-left-width":7,"link-color":"666666","link-hover-color":"000000","link-border-left-color":"cccccc","link-border-left-hover-color":"000000"},"widget_lists3":{"li-margin-left":5,"link-weight":"normal","link-padding-left":5,"link-border-left-width":7,"link-color":"666666","link-hover-color":"000000","link-border-left-color":"cccccc","link-border-left-hover-color":"000000"},"category_widget_display_type":"inline","select_font_size":"Default","post_kicker_home":"","post_kicker_multi":"","post_kicker_single":"","post_kicker_page":"","post_byline_home":"","post_byline_multi":"","post_byline_single":"","post_byline_page":"","post_footer_home":"%date(\'F jS, Y\')% | %tags-linked(\'Tags: \', \', \', \' | \')% Category: %categories-linked(\', \')% | %comments(\'Leave a comment\', \'One comment\', \'% comments\', \'Comments are closed\')% %edit(\' | \', \'Edit this post\', \'\')%","post_footer_multi":"%date(\'F jS, Y\')% | %tags-linked(\'Tags: \', \', \', \' | \')% Category: %categories-linked(\', \')% | %comments(\'Leave a comment\', \'One comment\', \'% comments\', \'Comments are closed\')% %edit(\' | \', \'Edit this post\', \'\')%","post_footer_single":"%date(\'F jS, Y\')% | %tags-linked(\'Tags: \', \', \', \' | \')% Category: %categories-linked(\', \')% %edit(\' | \', \'Edit this post\', \'\')%","post_footer_page":"","post_container_style":"margin: 0 0 30px 0;","post_container_sticky_style":"background: #eee url(\''.$templateURL.' \/images\/sticky.gif\') 99% 5% no-repeat;\nborder: dashed 1px #cccccc;\npadding: 10px;","post_kicker_style":"margin: 0 0 5px 0;","post_kicker_style_links":"color: #000000;\ntext-decoration: none;\ntext-transform: uppercase;","post_kicker_style_links_hover":"color: #cc0000;","post_headline_style":"","post_headline_style_text":"padding: 0;\nmargin: 0;","post_headline_style_links":"color: #666666;\ntext-decoration: none;","post_headline_style_links_hover":"color: #000000;\ntext-decoration: none;","post_byline_style":"margin: 5px 0 10px 0;","post_byline_style_links":"","post_byline_style_links_hover":"","post_bodycopy_style":"","post_footer_style":"margin: 0;\npadding: 5px;\nbackground: #eeeeee;\ncolor: #666;\nline-height: 18px;","post_footer_style_links":"color: #333;\nfont-weight: normal;\ntext-decoration: none;","post_footer_style_links_hover":"color: #333;\nfont-weight: normal;\ntext-decoration: underline;","excerpt_length":55,"dont_strip_excerpts":"<p>","custom_read_more":"[...]","excerpts_home":"Full Posts","full_posts_homepage":0,"excerpts_category":"Only Excerpts","excerpts_archive":"Only Excerpts","excerpts_tag":"Only Excerpts","excerpts_search":"Only Excerpts","excerpts_author":"Only Excerpts","post_thumbnail_width":150,"post_thumbnail_height":150,"post_thumbnail_crop":"No","post_thumbnail_css":"float: left;\nborder: 0;\npadding: 0;\nbackground: none;\nmargin: 0 10px 5px 0;\n","more_tag":"Continue reading %post-title%","author_highlight":"Yes","author_highlight_color":"ffecec","author_highlight_border_color":"ffbfbf","comment_background_color":"ffffff","comment_alt_background_color":"eeeeee","comment_border":"dotted 1px #cccccc","comment_author_size":"110%","comment_reply_link_text":" &middot; Reply","comment_edit_link_text":" &middot; Edit","comment_moderation_text":"Your comment is awaiting moderation.","comments_are_closed_text":"<p>Comments are closed.<\/p>","comments_on_pages":"No","separate_trackbacks":"No","avatar_size":"55","avatar_style":"margin: 0 8px 1px 0;\npadding: 3px;\nborder: solid 1px #ddd;\nbackground-color: #f3f3f3;\n-moz-border-radius: 3px;\n-khtml-border-radius: 3px;\n-webkit-border-radius: 3px;\nborder-radius: 3px;","show_xhtml_tags":"Yes","comment_form_style":"margin: 25px 0;\npadding: 25px;\nbackground: #eee;\n-moz-border-radius: 8px;\n-khtml-border-radius: 8px;\n-webkit-border-radius: 8px;\nborder-radius: 8px;","submit_button_style":"padding: 4px 10px 4px 10px;\nfont-size: 1.2em;\nline-height: 1.5em;\nheight: 36px;","comment_display_order":"Oldest on top","footer_style":"background-color: #ffffff;\nborder-top: dashed 1px #cccccc;\npadding: 10px;\r\ntext-align: center;\ncolor: #777777;\nfont-size: 95%;","footer_style_links":"text-decoration: none;\ncolor: #777777;\nfont-weight: normal;","footer_style_links_hover":"text-decoration: none;\ncolor: #777777;\nfont-weight: normal;","footer_style_content":"Copyright &copy; %current-year% %home% - All Rights Reserved","sticky_layout_footer":"No","footer_show_queries":"No","table":"border-collapse: collapse;\nmargin: 10px 0;","table_caption":"background: #eeeeee;\nborder: #999999;\npadding: 4px 8px;\ncolor: #666666;","table_th":"background: #888888;\ncolor: #ffffff;\nfont-weight: bold;\nfont-size: 90%;\npadding: 4px 8px;\n\r\n\t\t\tborder: solid 1px #ffffff;\ntext-align: left;","table_td":"padding: 4px 8px;\nbackground-color: #ffffff;\nborder-bottom: 1px solid #dddddd;\ntext-align: left;","table_tfoot_td":"","table_zebra_stripes":"Yes","table_zebra_td":"background: #f4f4f4;","table_hover_rows":"Yes","table_hover_td":"background: #e2e2e2;","form_input_field_style":"color: #000000;\nborder-top: solid 1px #333333;\nborder-left: solid 1px #333333;\nborder-right: solid 1px #999999;\nborder-bottom: solid 1px #cccccc;","form_input_field_background":"inputbackgr.gif","highlight_forms":"Yes","highlight_forms_style":"background: #e8eff7;\nborder-color: #37699f;","button_style":"background-color: #777777;\ncolor: #ffffff;\nborder: solid 2px #555555;\nfont-weight: bold;","button_style_hover":"background-color: #6b9c6b;\ncolor: #ffffff;\nborder: solid 2px #496d49;","blockquote_style":"color: #555555;\npadding: 1em 1em;\nbackground: #f4f4f4;\nborder: solid 1px #e1e1e1;","blockquote_style_2nd_level":"color: #444444;\npadding: 1em 1em;\nbackground: #e1e1e1;\nborder: solid 1px #d3d3d3;","post_image_style":"padding: 5px;\nborder: solid 1px #dddddd;\nbackground-color: #f3f3f3;\n-moz-border-radius: 3px;\n-khtml-border-radius: 3px;\n-webkit-border-radius: 3px;\nborder-radius: 3px;","post_image_caption_style":"border: 1px solid #dddddd;\ntext-align: center;\nbackground-color: #f3f3f3;\npadding-top: 4px;\nmargin: 10px 0 0 0;\n-moz-border-radius: 3px;\n-khtml-border-radius: 3px;\n-webkit-border-radius: 3px;\nborder-radius: 3px;","image_caption_text":"font-size: 0.8em;\nline-height: 13px;\npadding: 2px 4px 5px;\nmargin: 0;\ncolor: #666666;","html_inserts_header":"","html_inserts_body_tag":"","html_inserts_body_top":"","html_inserts_body_bottom":"","html_inserts_css":"h1 { font-size: 34px; line-height: 1.2; margin: 0.3em 0 10px; }\r\nh2 { font-size: 28px; line-height: 1.3; margin: 1em 0 .2em; }\r\nh3 { font-size: 24px; line-height: 1.3; margin: 1em 0 .2em; }\r\nh4 { font-size: 19px; margin: 1.33em 0 .2em; }\r\nh5 { font-size: 1.3em; margin: 1.67em 0; font-weight: bold; }\r\nh6 { font-size: 1.15em; margin: 1.67em 0; font-weight: bold; }","archives_page_id":"","archives_date_show":"Yes","archives_date_title":"Archives by Month","archives_date_type":"monthly","archives_date_limit":"","archives_date_count":"Yes","archives_category_show":"Yes","archives_category_title":"Archives by Category","archives_category_count":"Yes","archives_category_depth":"0","archives_category_orderby":"name","archives_category_order":"ASC","archives_category_feed":"No","css_external":"Inline","javascript_external":"Inline","pngfix_selectors":"a.posts-icon, a.comments-icon, a.email-icon, img.logo","css_compress":"Yes","allow_debug":"Yes","bfa_widget_areas":false,"h_blogtitle":1,"h_posttitle":2}';
			
			
			$bfa_ata_default = toArray(json_decode($default_options));
			
			foreach ($options as $value) { 
				if ($value['type'] != 'info') {
					if (get_option( 'bfa_ata_' . $value['id'] ) === FALSE) {	
						 $bfa_ata4[ $value['id'] ] = $bfa_ata_default[ $value['id'] ];
					} else {  
						$bfa_ata4[ $value['id'] ] = get_option( 'bfa_ata_' . $value['id'] );
					}
				}
			}				
			
			// Separate option bfa_widget_areas
			$bfa_ata4['bfa_widget_areas'] = get_option('bfa_widget_areas');
			
			update_option('bfa_ata4', $bfa_ata4);
		}

		$bfa_ata = get_option('bfa_ata4');

		if ( is_page() ) {
			global $wp_query;
			$current_page_id = $wp_query->get_queried_object_id();
			}


		//figure out sidebars and "colspan=XX", based on theme options and type or ID of page that we are currently on:

		$cols = 1;
		$left_col = '';
		$left_col2 = '';
		$right_col = '';
		$right_col2 = '';

		if ( is_page() AND (function_exists('is_front_page') ? !is_front_page() : '') AND !is_home() ) {


			if ($bfa_ata['left_col_pages_exclude'] != "") { 
				$pages_exlude_left = explode(",", str_replace(" ", "", $bfa_ata['left_col_pages_exclude']));
				if ( isset($bfa_ata['leftcol_on']['page']) AND !in_array($current_page_id, $pages_exlude_left) ) {
					$cols++; $left_col = "on";
				}
			} else {
				if ( isset($bfa_ata['leftcol_on']['page']) ) {
					$cols++; $left_col = "on";
				}
			}

			if ($bfa_ata['left_col2_pages_exclude'] != "") { 
				$pages_exlude_left2 = explode(",", str_replace(" ", "", $bfa_ata['left_col2_pages_exclude']));
				if ( isset($bfa_ata['leftcol2_on']['page']) AND !in_array($current_page_id, $pages_exlude_left2) ) {
					$cols++; $left_col2 = "on";
				}
			} else {
				if ( isset($bfa_ata['leftcol2_on']['page']) ) {
					$cols++; $left_col2 = "on";
				}
			}
				
			if ($bfa_ata['right_col_pages_exclude'] != "") { 
				$pages_exlude_right = explode(",", str_replace(" ", "", $bfa_ata['right_col_pages_exclude']));
				if ( isset($bfa_ata['rightcol_on']['page']) AND !in_array($current_page_id, $pages_exlude_right) ) {
					$cols++; $right_col = "on"; 
				}
			} else {
				if ( isset($bfa_ata['rightcol_on']['page']) ) {
					$cols++; $right_col = "on"; 
				}
			}

			if ($bfa_ata['right_col2_pages_exclude'] != "") { 
				$pages_exlude_right2 = explode(",", str_replace(" ", "", $bfa_ata['right_col2_pages_exclude']));
				if ( isset($bfa_ata['rightcol2_on']['page']) AND !in_array($current_page_id, $pages_exlude_right2) ) {
					$cols++; $right_col2 = "on"; 
				}
			} else {
				if ( isset($bfa_ata['rightcol2_on']['page']) ) {
					$cols++; $right_col2 = "on"; 
				}
			}
			
		} elseif ( is_category() ) {

			$current_cat_id = get_query_var('cat');

			if ($bfa_ata['left_col_cats_exclude'] != "") {
				$cats_exlude_left = explode(",", str_replace(" ", "", $bfa_ata['left_col_cats_exclude']));
				if ( isset($bfa_ata['leftcol_on']['category']) AND !in_array($current_cat_id, $cats_exlude_left) ) {
					$cols++; $left_col = "on"; 
				}
			} else {
				if ( isset($bfa_ata['leftcol_on']['category']) ) {
					$cols++; $left_col = "on"; 
				}
			}

			if ($bfa_ata['left_col2_cats_exclude'] != "") {
				$cats_exlude_left2 = explode(",", str_replace(" ", "", $bfa_ata['left_col2_cats_exclude']));
				if ( isset($bfa_ata['leftcol2_on']['category']) AND !in_array($current_cat_id, $cats_exlude_left2) ) {
					$cols++; $left_col2 = "on"; 
				}
			} else {
				if ( isset($bfa_ata['leftcol2_on']['category']) ) {
					$cols++; $left_col2 = "on"; 
				}
			}
				
			if ($bfa_ata['right_col_cats_exclude'] != "") {
				$cats_exlude_right = explode(",", str_replace(" ", "", $bfa_ata['right_col_cats_exclude']));
				if ( isset($bfa_ata['rightcol_on']['category']) AND !in_array($current_cat_id, $cats_exlude_right) ) {
					$cols++; $right_col = "on"; 
				}
			} else {
				if ( isset($bfa_ata['rightcol_on']['category']) ) {
					$cols++; $right_col = "on"; 
				}
			}

			if ($bfa_ata['right_col2_cats_exclude'] != "") {
				$cats_exlude_right2 = explode(",", str_replace(" ", "", $bfa_ata['right_col2_cats_exclude']));
				if ( isset($bfa_ata['rightcol2_on']['category']) AND !in_array($current_cat_id, $cats_exlude_right2) ) {
					$cols++; $right_col2 = "on"; 
				}
			} else {
				if ( isset($bfa_ata['rightcol2_on']['category']) ) {
					$cols++; $right_col2 = "on"; 
				}
			}
				
		} else {

			if ( (is_home() && isset($bfa_ata['leftcol_on']['homepage'])) OR 
			( function_exists('is_front_page') ? is_front_page() AND isset($bfa_ata['leftcol_on']['frontpage']) : '') OR 
			( is_single() && isset($bfa_ata['leftcol_on']['single'])) OR ( is_date() AND isset($bfa_ata['leftcol_on']['date'])) OR 
			( is_tag() && isset($bfa_ata['leftcol_on']['tag'])) OR ( is_archive() AND !( is_tag() OR is_author() OR is_date() OR is_category()) && isset($bfa_ata['leftcol_on']['taxonomy'])) 
			OR ( is_search() AND isset($bfa_ata['leftcol_on']['search'])) OR 
			( is_author() && isset($bfa_ata['leftcol_on']['author'])) OR ( is_404() AND isset($bfa_ata['leftcol_on']['404'])) OR 
			( is_attachment() && isset($bfa_ata['leftcol_on']['attachment'])) ) {
				$cols++; $left_col = "on"; 
			}

			if ( (is_home() && isset($bfa_ata['leftcol2_on']['homepage'])) OR 
			( function_exists('is_front_page') ? is_front_page() AND isset($bfa_ata['leftcol2_on']['frontpage']) : '') OR 
			( is_single() && isset($bfa_ata['leftcol2_on']['single'])) OR ( is_date() AND isset($bfa_ata['leftcol2_on']['date'])) OR 
			( is_tag() && isset($bfa_ata['leftcol2_on']['tag'])) OR ( is_archive() AND !( is_tag() OR is_author() OR is_date() OR is_category()) && isset($bfa_ata['leftcol2_on']['taxonomy'])) 
			OR ( is_search() AND isset($bfa_ata['leftcol2_on']['search'])) OR 
			( is_author() && isset($bfa_ata['leftcol2_on']['author'])) OR ( is_404() AND isset($bfa_ata['leftcol2_on']['404'])) OR 
			( is_attachment() && isset($bfa_ata['leftcol2_on']['attachment'])) ) {
				$cols++; $left_col2 = "on"; 
			}
				
			if ( (is_home() && isset($bfa_ata['rightcol_on']['homepage'])) OR 
			( function_exists('is_front_page') ? is_front_page() AND isset($bfa_ata['rightcol_on']['frontpage']) : '') OR 
			( is_single() && isset($bfa_ata['rightcol_on']['single'])) OR ( is_date() AND isset($bfa_ata['rightcol_on']['date'])) OR 
			( is_tag() && isset($bfa_ata['rightcol_on']['tag'])) OR ( is_archive() AND !( is_tag() OR is_author() OR is_date() OR is_category()) && isset($bfa_ata['rightcol_on']['taxonomy'])) 
			OR ( is_search() AND isset($bfa_ata['rightcol_on']['search'])) OR 
			( is_author() && isset($bfa_ata['rightcol_on']['author'])) OR ( is_404() AND isset($bfa_ata['rightcol_on']['404'])) OR 
			( is_attachment() && isset($bfa_ata['rightcol_on']['attachment'])) ) {
				$cols++; $right_col = "on"; 
			}

			if ( (is_home() && isset($bfa_ata['rightcol2_on']['homepage'])) OR 
			( function_exists('is_front_page') ? is_front_page() AND isset($bfa_ata['rightcol2_on']['frontpage']) : '') OR 
			( is_single() && isset($bfa_ata['rightcol2_on']['single'])) OR ( is_date() AND isset($bfa_ata['rightcol2_on']['date'])) OR 
			( is_tag() && isset($bfa_ata['rightcol2_on']['tag'])) OR ( is_archive() AND !( is_tag() OR is_author() OR is_date() OR is_category()) && isset($bfa_ata['rightcol2_on']['taxonomy'])) 
			OR ( is_search() AND isset($bfa_ata['rightcol2_on']['search'])) OR 
			( is_author() && isset($bfa_ata['rightcol2_on']['author'])) OR ( is_404() AND isset($bfa_ata['rightcol2_on']['404'])) OR 
			( is_attachment() && isset($bfa_ata['rightcol2_on']['attachment'])) ) {
				$cols++; $right_col2 = "on"; 
			}
				
		}


		// $bfa_ata['h1_on_single_pages'] turn the blogtitle to h2 and the post/page title to h1 on single post pages and static "page" pages

		if ( $bfa_ata['h1_on_single_pages'] == "Yes" AND ( is_single() OR is_page() ) ) {
			$bfa_ata['h_blogtitle'] = 2; $bfa_ata['h_posttitle'] = 1; 
		} else {
			$bfa_ata['h_blogtitle'] = 1; $bfa_ata['h_posttitle'] = 2; 
		}

		$result = array($bfa_ata, $cols, $left_col, $left_col2, $right_col, $right_col2, $bfa_ata['h_blogtitle'], $bfa_ata['h_posttitle']);

	return $result;
	}
}
?>