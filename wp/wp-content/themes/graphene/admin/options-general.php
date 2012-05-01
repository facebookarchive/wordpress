<?php
function graphene_options_general() { 
    
    global $graphene_settings;
    ?>
        <input type="hidden" name="graphene_general" value="true" />
        
        <?php /* Slider Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Slider Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="slider_disable"><?php _e( 'Disable slider', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[slider_disable]" id="slider_disable" <?php checked( $graphene_settings['slider_disable'] ); ?> value="true" data-toggleOptions="true" /></td>
                    </tr>
                </table>
                <table class="form-table<?php if ( $graphene_settings['slider_disable'] == true ) echo ' hide'; ?>">
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'What do you want to show in the slider', 'graphene' ); ?></label><br />                            
                        </th>
                        <td>
                            <input type="radio" name="graphene_settings[slider_type]" value="latest_posts" class="slider-type" id="slider_type_latest_posts" <?php checked( $graphene_settings['slider_type'], 'latest_posts' ); ?>/>
                            <label for="slider_type_latest_posts"><?php _e( 'Show latest posts', 'graphene' ); ?></label>                            
                            <br />
                            <input type="radio" name="graphene_settings[slider_type]" value="random" class="slider-type" id="slider_type_random" <?php checked( $graphene_settings['slider_type'], 'random' ); ?>/>
                            <label for="slider_type_random"><?php _e( 'Show random posts', 'graphene' ); ?></label>
                            <br />
                            <input type="radio" name="graphene_settings[slider_type]" value="posts_pages" class="slider-type" id="slider_type_posts_pages" <?php checked( $graphene_settings['slider_type'], 'posts_pages' ); ?>/>
                            <label for="slider_type_posts_pages"><?php _e( 'Show specific posts/pages', 'graphene' ); ?></label>                            
                            <br />
                            <input type="radio" name="graphene_settings[slider_type]" value="categories" class="slider-type" id="slider_type_categories" <?php checked( $graphene_settings['slider_type'], 'categories' ); ?>/>
                            <label for="slider_type_categories"><?php _e( 'Show posts from categories', 'graphene' ); ?></label>                            
                        </td>
                    </tr>
                    <tr class="row_slider_type_posts_pages<?php if ( $graphene_settings['slider_type'] != 'posts_pages' ) echo ' hide'; ?>">
                        <th scope="row">
                            <label for="slider_specific_posts"><?php _e( 'Posts and/or pages to display', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[slider_specific_posts]" id="slider_specific_posts" value="<?php echo $graphene_settings['slider_specific_posts']; ?>" size="60" class="wide code" /><br />
                            <span class="description">
							<?php _e( 'Enter ID of posts and/or pages to be displayed, separated by comma. Example: <code>1,13,45,33</code>', 'graphene' ); ?><br />
							<?php _e( 'Applicable only if <strong>Show specific posts/pages</strong> is selected above.', 'graphene' ); ?>
                            </span>                        
                        </td>
                    </tr>
                    <tr class="row_slider_type_categories<?php if ( $graphene_settings['slider_type'] != 'categories' ) echo ' hide'; ?>">
                        <th scope="row">
                            <label for="slider_specific_categories"><?php _e( 'Categories to display', 'graphene' ); ?></label>
                            <small><?php _e( 'All posts within the categories selected here will be displayed on the slider. Usage example: create a new category "Featured" and assign all posts to be displayed on the slider to that category, and then select that category here.', 'graphene' ); ?></small>
                        </th>
                        <td>
                            <select name="graphene_settings[slider_specific_categories][]" id="slider_specific_categories" multiple="multiple" class="select-multiple">
                               <?php /* Get the list of categories */ 
                                    $selected_cats = $graphene_settings['slider_specific_categories'];
                                    $categories = get_categories();
                                    foreach ( $categories as $category) :
                                ?>
                                <option value="<?php echo $category->cat_ID; ?>" <?php if ( $selected_cats && in_array( $category->cat_ID, $selected_cats ) ) { echo 'selected="selected"'; }?>><?php echo $category->cat_name; ?></option>
                                <?php endforeach; ?> 
                            </select>                       
                        </td>
                    </tr>
                    <tr class="row_slider_type_categories<?php if ( $graphene_settings['slider_type'] != 'categories' ) echo ' hide'; ?>">
                        <th scope="row">
                            <label for="slider_specific_categories"><?php _e( 'Exclude the categories from posts listing', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<select name="graphene_settings[slider_exclude_categories]">
                        		<option type="radio" name="graphene_settings[slider_exclude_categories]" id="slider_exclude_categories_disabled" <?php selected( $graphene_settings['slider_exclude_categories'], 'disabled' ); ?> value="disabled" data-toggleOptions="true"><?php _e( 'Disabled', 'graphene' ); ?></option>
                                <option type="radio" name="graphene_settings[slider_exclude_categories]" id="slider_exclude_categories_frontpage" <?php selected( $graphene_settings['slider_exclude_categories'], 'frontpage' ); ?> value="homepage" data-toggleOptions="true"><?php _e( 'Home Page', 'graphene' ); ?></option>
                                <option type="radio" name="graphene_settings[slider_exclude_categories]" id="slider_exclude_categories_everywhere" <?php selected( $graphene_settings['slider_exclude_categories'], 'everywhere' ); ?> value="everywhere" data-toggleOptions="true"><?php _e( 'Everywhere', 'graphene' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="row_slider_type_categories<?php if ( $graphene_settings['slider_type'] != 'categories' ) echo ' hide'; ?>">
                        <th scope="row">
                            <label for="slider_random_category_posts"><?php _e( 'Show posts from categories in random order', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="checkbox" name="graphene_settings[slider_random_category_posts]" id="slider_random_category_posts" <?php checked( $graphene_settings['slider_random_category_posts'] ); ?> value="true" data-toggleOptions="true" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="slider_postcount"><?php _e( 'Number of posts to display', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[slider_postcount]" id="slider_postcount" value="<?php echo $graphene_settings['slider_postcount']; ?>" size="3" />
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_img"><?php _e( 'Slider image', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <select name="graphene_settings[slider_img]" id="slider_img">
                                <option value="disabled" <?php selected( $graphene_settings['slider_img'], 'disabled' ); ?>><?php _e("Don't show image", 'graphene' ); ?></option>
                                <option value="featured_image" <?php selected( $graphene_settings['slider_img'], 'featured_image' ); ?>><?php _e("Featured Image", 'graphene' ); ?></option>
                                <option value="post_image" <?php selected( $graphene_settings['slider_img'], 'post_image' ); ?>><?php _e("First image in post", 'graphene' ); ?></option>
                                <option value="custom_url" <?php selected( $graphene_settings['slider_img'], 'custom_url' ); ?>><?php _e("Custom URL", 'graphene' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_imgurl"><?php _e( 'Custom slider image URL', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[slider_imgurl]" id="slider_imgurl" value="<?php echo $graphene_settings['slider_imgurl']; ?>" size="60" class="widefat code" /><br />
                            <span class="description"><a href="#" class="upload_image_button"><?php _e( 'Upload or select image from gallery', 'graphene' );?></a> - <?php _e( 'Make sure you select Custom URL in the slider image option above to use this custom url.', 'graphene' ); ?></span>
                            
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="slider_display_style"><?php _e( 'Slider display style', 'graphene' ); ?></label><br />
                        </th>
                        <td>
                            <select name="graphene_settings[slider_display_style]" id="slider_display_style">
                                <option value="thumbnail-excerpt" <?php selected( $graphene_settings['slider_display_style'], 'thumbnail-excerpt' ); ?>><?php _e( 'Thumbnail and excerpt', 'graphene' ); ?></option>
                                <option value="bgimage-excerpt" <?php selected( $graphene_settings['slider_display_style'], 'bgimage-excerpt' ); ?>><?php _e( 'Background image and excerpt', 'graphene' ); ?></option>
                                <option value="full-post" <?php selected( $graphene_settings['slider_display_style'], 'full-post' ); ?>><?php _e( 'Full post content', 'graphene' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_animation"><?php _e( 'Slider animation', 'graphene' ); ?></label>
                        </th>
                        <td>
                            
                            <select name="graphene_settings[slider_animation]" id="slider_animation">
                                <option value="horizontal-slide" <?php selected( $graphene_settings['slider_animation'], 'slide' ); ?>><?php _e( 'Horizontal slide', 'graphene' ); ?></option>
                                <option value="vertical-slide" <?php selected( $graphene_settings['slider_animation'], 'vertical-slide' ); ?>><?php _e( 'Vertical slide', 'graphene' ); ?></option>
                                <option value="fade" <?php selected( $graphene_settings['slider_animation'], 'fade' ); ?>><?php _e( 'Fade', 'graphene' ); ?></option>
                                <option value="none" <?php selected( $graphene_settings['slider_animation'], 'none' ); ?>><?php _e( 'No effect', 'graphene' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_height"><?php _e( 'Slider height', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[slider_height]" id="slider_height" value="<?php echo $graphene_settings['slider_height']; ?>" size="3" /> px                        
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_speed"><?php _e( 'Slider speed', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[slider_speed]" id="slider_speed" value="<?php echo $graphene_settings['slider_speed']; ?>" size="4" /> <?php _e( 'milliseconds', 'graphene' ); ?><br />
                            <span class="description"><?php _e( 'This is the duration that each slider item will be shown', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_trans_speed"><?php _e( 'Slider transition speed', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[slider_trans_speed]" id="slider_trans_speed" value="<?php echo $graphene_settings['slider_trans_speed']; ?>" size="4" /> <?php _e( 'milliseconds', 'graphene' ); ?><br />
                            <span class="description"><?php _e( 'This is the speed of the slider transition. Lower values = higher speed.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="slider_position"><?php _e( 'Move slider to bottom of page', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[slider_position]" id="slider_position" <?php checked( $graphene_settings['slider_position'] ); ?> value="true" /></td>
                    </tr>                    
                </table>
            </div>
        </div>
        
        
        <?php /* Front Page Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Front Page Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">       	
                    <tr>
                        <th scope="row">
                            <label for="frontpage_posts_cats"><?php _e( 'Front page posts categories', 'graphene' ); ?></label>
                            <p>
                            	<small><?php _e( 'Only posts that belong to the categories selected here will be displayed on the front page. Does not affect Static Front Page.', 'graphene' ); ?></small>
                            </p>
                        </th>
                        <td>
                            <select name="graphene_settings[frontpage_posts_cats][]" id="frontpage_posts_cats" multiple="multiple" class="select-multiple">
                                <option value="" <?php if ( empty( $graphene_settings['frontpage_posts_cats'] ) ) { echo 'selected="selected"'; } ?>><?php _e( '--Disabled--', 'graphene' ); ?></option>
                                <?php /* Get the list of categories */ 
                                    $categories = get_categories();
                                    foreach ( $categories as $category) :
                                ?>
                                <option value="<?php echo $category->cat_ID; ?>" <?php if ( in_array( $category->cat_ID, $graphene_settings['frontpage_posts_cats'] ) ) {echo 'selected="selected"';}?>><?php echo $category->cat_name; ?></option>
                                <?php endforeach; ?>
                            </select><br />
                            <span class="description"><?php _e( 'You may select multiple categories by holding down the CTRL key.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        
        <?php /* Homepage panes options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
            	<div title="Click to toggle" class="handlediv"><br /></div>
            	<h3 class="hndle"><?php _e( 'Homepage Panes', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">            	
                <?php if ( 'page' == get_option( 'show_on_front' ) ) : ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="disable_homepage_panes"><?php _e( 'Disable homepage panes', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="graphene_settings[disable_homepage_panes]" id="disable_homepage_panes" <?php checked( $graphene_settings['disable_homepage_panes'] ); ?> value="true" data-toggleOptions="true" />
                        </td>
                    </tr>
                </table>
                <table class="form-table site-summary<?php if ( $graphene_settings['disable_homepage_panes'] == true ) echo ' hide'; ?>">
                    <tr>
                    	<th scope="row">
                            <?php _e( 'Type of content to show', 'graphene' ); ?>
                        </th>
                        <td>
                            <input type="radio" name="graphene_settings[show_post_type]" value="latest-posts" class="homepage-panes-post-type" id="show_post_type_latest-posts" <?php checked( $graphene_settings['show_post_type'], 'latest-posts' ); ?>/>
                            <label for="show_post_type_latest-posts"><?php _e( 'Latest posts', 'graphene' ); ?></label>
                            
                            <input type="radio" name="graphene_settings[show_post_type]" value="cat-latest-posts" class="homepage-panes-post-type" id="show_post_type_cat-latest-posts" <?php checked( $graphene_settings['show_post_type'],  'cat-latest-posts' ); ?>/>
                            <label for="show_post_type_cat-latest-posts"><?php _e( 'Latest posts by category', 'graphene' ); ?></label>
                           
                            <input type="radio" name="graphene_settings[show_post_type]" value="posts" class="homepage-panes-post-type" id="show_post_type_pages" <?php checked( $graphene_settings['show_post_type'], 'posts' ); ?>/>
                            <label for="show_post_type_pages"><?php _e( 'Posts and/or pages', 'graphene' ); ?></label>
                        </td>
                    </tr>
                    <tr id="row_show_post_type_latest-posts"<?php if ( ! in_array( $graphene_settings['show_post_type'], array( 'latest-posts', 'cat-latest-posts' ) )) echo ' class="hide"'; ?>>
                        <th scope="row">
                            <label for="homepage_panes_count"><?php _e( 'Number of latest posts to display', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[homepage_panes_count]" id="homepage_panes_count" value="<?php echo $graphene_settings['homepage_panes_count']; ?>" size="1" /><br />
                            <span class="description"><?php _e( 'Applicable only if <strong>Latest posts</strong> or <strong>Latest posts by category</strong> is selected above.', 'graphene' ); ?></span>                        
                        </td>
                    </tr>
                    <tr id="row_show_post_type_cat-latest-posts"<?php if ( 'cat-latest-posts' != $graphene_settings['show_post_type'] ) echo ' class="hide"'; ?>>
                        <th scope="row">
                            <label for="homepage_panes_cat"><?php _e( 'Category to show latest posts from', 'graphene' ); ?></label>
                        </th>
                        <td>                            
                            <select name="graphene_settings[homepage_panes_cat][]" id="homepage_panes_cat" multiple="multiple" class="select-multiple">
                                <?php /* Get the list of categories */ 
                                    foreach ( $categories as $category) :
                                ?>
                                <option value="<?php echo $category->cat_ID; ?>" <?php if ( in_array( $category->cat_ID, (array) $graphene_settings['homepage_panes_cat'] ) ) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
                                <?php endforeach; ?>
                            </select><br />
                            <span class="description"><?php _e( 'Applicable only if <strong>Latest posts by category</strong> is selected above.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr id="row_show_post_type_posts"<?php if ( 'posts' != $graphene_settings['show_post_type'] ) echo ' class="hide"'; ?>>
                        <th scope="row">
                            <label for="homepage_panes_posts"><?php _e( 'Posts and/or pages to display', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="graphene_settings[homepage_panes_posts]" id="homepage_panes_posts" value="<?php echo $graphene_settings['homepage_panes_posts']; ?>" size="10" class="code" /><br />
                            <span class="description">
							<?php _e( 'Enter ID of posts and/or pages to be displayed, separated by comma. Example: <code>1,13,45,33</code>', 'graphene' ); ?><br />
							<?php _e( 'Applicable only if <strong>Posts and/or pages</strong> is selected above.', 'graphene' ); ?>
                            </span>                        
                        </td>
                    </tr>                    
                </table>
                <?php else : ?>
                <p><?php _e( '<strong>Note:</strong> homepage panes are only displayed when using a <a href="http://codex.wordpress.org/Creating_a_Static_Front_Page">static front page</a>.', 'graphene' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        
        <?php /* Comments Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Comments Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">       	
                    <tr>
                        <th scope="row">
                            <label for="comments_setting"><?php _e( 'Commenting', 'graphene' ); ?></label>                            
                        </th>
                        <td>
                            <select name="graphene_settings[comments_setting]" id="comments_setting">
                                <option value="wordpress" <?php selected( $graphene_settings['comments_setting'], 'wordpress' ); ?>><?php _e( 'Use WordPress settings', 'graphene' ); ?></option>
                                <option value="disabled_pages" <?php selected( $graphene_settings['comments_setting'], 'disabled_pages' ); ?>><?php _e( 'Disabled for pages', 'graphene' ); ?></option>
                                <option value="disabled_completely" <?php selected( $graphene_settings['comments_setting'], 'disabled_completely' ); ?>><?php _e( 'Disabled completely', 'graphene' ); ?></option>                               
                            </select><br />
                            <span class="description"><?php _e( 'Note: this setting overrides the global WordPress Discussion Setting called "Allow people to post comments on new articles" and also the "Allow comments" option for individual posts/pages.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        
        <?php /* Child Page Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Child Page Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">       	
                    <tr>
                        <th scope="row">
                            <label for="hide_parent_content_if_empty"><?php _e( 'Hide parent box if content is empty', 'graphene' ); ?></label>                            
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_parent_content_if_empty]" id="hide_parent_content_if_empty" <?php checked( $graphene_settings['hide_parent_content_if_empty'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="child_page_listing"><?php _e( 'Child page listings', 'graphene' ); ?></label>                            
                        </th>
                        <td>
                            <select name="graphene_settings[child_page_listing]" id="child_page_listing">
                                <option value="show_always" <?php selected( $graphene_settings['child_page_listing'], 'show_always' ); ?>><?php _e( 'Show listing', 'graphene' ); ?></option>
                                <option value="hide" <?php selected( $graphene_settings['child_page_listing'], 'hide' ); ?>><?php _e( 'Hide listing', 'graphene' ); ?></option>
                                <option value="show_if_parent_empty" <?php selected( $graphene_settings['child_page_listing'], 'show_if_parent_empty' ); ?>><?php _e( 'Only show listing if parent content is empty', 'graphene' ); ?></option>
                            </select>
                        </td>                            
                    </tr>
                </table>
            </div>
        </div>
        
        
        <?php /* Widget Area Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Widget Area Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
            	<h4><?php _e( 'Header widget area', 'graphene' ); ?></h4>
                <p><?php _e( '<strong>Important:</strong> This widget area is unstyled, as it is often used for advertisement banners, etc. If you enable it, make sure you style it to your needs using the Custom CSS option.', 'graphene' ); ?></p>
                <table class="form-table site-summary">
                    <tr>
                        <th scope="row">
                        	<label for="enable_header_widget"><?php _e( 'Enable header widget area', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="checkbox" value="true" name="graphene_settings[enable_header_widget]" id="enable_header_widget" <?php checked( $graphene_settings['enable_header_widget'] ); ?> />
                        </td>
                    </tr>
                </table>
                
                
                <h4><?php _e( 'Alternate Widgets', 'graphene' ); ?></h4>
                <p><?php _e( 'You can enable the theme to show different widget areas in the front page than the rest of the website. If you enable this option, additional widget areas that will only be displayed on the front page will be added to the Widget settings page.', 'graphene' ); ?></p>
                <table class="form-table">       	
                    <tr>
                        <th scope="row" style="width:350px;"><label for="alt_home_sidebar"><?php _e( 'Enable alternate front page sidebar widget area', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[alt_home_sidebar]" id="alt_home_sidebar" <?php checked( $graphene_settings['alt_home_sidebar'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="alt_home_footerwidget"><?php _e( 'Enable alternate front page footer widget area', 'graphene' ); ?></label><br />
                        <small><?php _e( 'You can also specify different column counts for the front page footer widget and the rest-of-site footer widget if you enable this option.', 'graphene' ); ?></small>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[alt_home_footerwidget]" id="alt_home_footerwidget" <?php checked( $graphene_settings['alt_home_footerwidget'] ); ?> value="true" /></td>
                    </tr>
                </table>
            </div>
        </div>
                
        
        <?php /* Top Bar Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Top Bar Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="hide_top_bar"><?php _e( 'Hide the top bar', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_top_bar]" id="hide_top_bar" <?php checked( $graphene_settings['hide_top_bar'] ); ?> value="true" data-toggleOptions="true" rel="social-media-table" /></td>
                    </tr>
                </table>
                
                <h4 class="social-media-table"><?php _e( 'Social Media', 'graphene' ); ?></h4>
                <table class="form-table social-media-table<?php if ( $graphene_settings['hide_top_bar'] == true ) echo ' hide'; ?>">
                    <tr class="non-essential-option">
                        <th scope="row"><label for="social_media_new_window"><?php _e( 'Open social media links in new window', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[social_media_new_window]" id="social_media_new_window" <?php checked( $graphene_settings['social_media_new_window'] ); ?> value="true" /></td>    
                    </tr>
                    <tr class="non-essential-option">
                		<td colspan="2"><p><?php _e( '<strong>Hint:</strong> drag and drop to rearrange the placement of the social media icons.', 'graphene' ); ?></p></td>
                	</tr>
                    <tr class="non-essential-option">
                        <td colspan="2" id="socialprofile-sortable">                            
                            <?php        
                                /*
								 * Credits go to Benjamin Reid for the icons - Social Media Bookmark Icon +
								 * http://www.nouveller.com/general/free-social-media-bookmark-icon-pack-the-ever-growing-icon-set/
								 */
                                $available_profiles = array (   'Twitter', 'Facebook', 'LinkedIn', 'RSS', '-', __( 'Custom', 'graphene' ), '-',
                                                    'audioBoo', 'Bebo', 'Behance', 'Blogger', 'Buzz', 'CreativeCommons', 'DailyBooth', 'Delicious', 'DesignFloat', 'DeviantArt', 
                                                    'Digg', 'Dopplr', 'Dribbble', 'Email', 'Ember', 'Flickr', 'Forrst', 'Friendfeed', 'Google', 
                                                    'Gowalla', 'Grooveshark', 'Hyves', 'LastFM', 'LiveJournal', 'Lockerz', 'Megavideo', 'MySpace', 'Piano', 
                                                    'Playfire', 'PlayStation', 'Reddit', 'Skype', 'Socialvibe', 'SoundCloud', 'Spotify', 'Steam', 'StumbleUpon', 
                                                    'Technorati', 'Tumblr', 'TwitPic', 'Typepad', 'Vimeo', 'Wakoopa', 'WordPress', 'Xing', 'Yahoo', 'YouTube' );

                                $social_profiles = ( ! empty( $graphene_settings['social_profiles'] ) ) ? $graphene_settings['social_profiles'] : array();
                            ?>
                            <?php 
								if ( ! in_array( false, $social_profiles) ) : 
								foreach ($social_profiles as $profile_key => $profile_data) : 
							?>
                                <table class="form-table socialprofile-table">
                                    <tr>
                                        <th scope="row" rowspan="<?php echo $profile_data['type'] == sanitize_title( __( 'Custom', 'graphene' ) ) ? '3' : '2'; ?>" class="small-row">                            
                                            <?php echo $profile_data['name']; ?><br />
                                            <input type="hidden" name="graphene_settings[social_profiles][<?php echo $profile_key; ?>][type]" value="<?php echo $profile_data['type']; ?>" />
                                            <input type="hidden" name="graphene_settings[social_profiles][<?php echo $profile_key; ?>][name]" value="<?php echo $profile_data['name']; ?>" />
                                            <?php if ( $profile_data['type'] == 'custom' ) : ?>
                                            <img class="mysocial-icon" src="<?php echo $profile_data['icon_url']; ?>" alt="" />
                                            <?php else : ?>
                                            <div class="mysocial social-<?php echo $profile_data['type']; ?>">&nbsp;</div>
                                            <?php endif; ?>
                                        </th>
                                        <th class="small-row"><?php _e( 'Title attribute', 'graphene' ); ?></th>
                                        <td><input type="text" name="graphene_settings[social_profiles][<?php echo $profile_key; ?>][title]" value="<?php echo $profile_data['title']; ?>" class="widefat code" /></td>
                                    </tr>
                                    <tr>
                                        <th class="small-row"><?php _e('URL', 'graphene'); ?></th>
                                        <td>
                                            <input type="text" name="graphene_settings[social_profiles][<?php echo $profile_key; ?>][url]" value="<?php echo $profile_data['url']; ?>" class="widefat code" />
                                            <?php if ( $profile_data['type'] == 'rss' ) : ?>
                                                <br /><span class="description"><?php _e('Leave the URL empty to use the default RSS URL.', 'graphene'); ?></span>
                                            <?php endif; ?>
                                    <?php if ( $profile_data['type'] == 'custom' ) : ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="small-row"><?php _e('Icon URL', 'graphene'); ?></th>
                                        <td>
                                            <input type="text" name="graphene_settings[social_profiles][<?php echo $profile_key; ?>][icon_url]" value="<?php echo $profile_data['icon_url']; ?>" class="widefat code" />                            
                                    <?php endif; ?>
                                            <br /><span class="delete"><a href="#" class="socialprofile-del"><?php _e( 'Delete', 'graphene' ); ?></a></span>
                                        </td>
                                    </tr>
                                </table>            
							<?php endforeach; endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="socialprofile-dragging">
                                <tr>
                                    <td colspan="2">
                                        <strong><?php _e( 'Add Social Media Profile', 'graphene' ); ?></strong>
                                        <input type="hidden" id="socialprofile-next-index" value="<?php echo count($social_profiles)+1; ?>" />                                                                                
                                        <input type="hidden" id="new-socialprofile-data" 
                                                data-icon-url="<?php echo get_template_directory_uri() . '/images/social/'; ?>"
                                                data-custom-title="<?php echo sanitize_title( __( 'Custom', 'graphene' ) ); ?>"
                                                data-text-icon-url="<?php _e('Icon URL', 'graphene'); ?>"
                                                data-text-title-attr="<?php _e('Title attribute', 'graphene'); ?>"
                                                data-text-url="<?php _e('URL', 'graphene'); ?>"
                                                data-text-delete="<?php _e( 'Delete', 'graphene' ); ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e( 'Type', 'graphene' ); ?></th>
                                    <td>
                                        <select id="new-socialprofile-type">
                                            <option disabled="disabled" value="-">- <?php _e( 'Choose type', 'graphene' ); ?> -</option>
                                            <?php foreach ( $available_profiles as $profile_type) : ?>                                
                                                <?php if ($profile_type == '-') : ?>
                                                <option disabled="disabled" value="-">-----------------------</option>
                                                <?php else : ?>
                                                <option value="<?php echo sanitize_title( $profile_type ); ?>"><?php echo $profile_type; ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>                            
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e('Title attribute', 'graphene'); ?></th>
                                    <td><input type="text" id="new-socialprofile-title" class="widefat code" /></td>
                                </tr>
                                <tr>
                                    <th><?php _e('URL', 'graphene'); ?></th>
                                    <td><input type="text" id="new-socialprofile-url" class="widefat code" />
                                        <span id="new-socialprofile-url-description" class="hide"><?php _e('Leave the URL empty to use the default RSS URL.', 'graphene'); ?></span>
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <th><?php _e('Icon URL', 'graphene'); ?></th>
                                    <td><input type="text" id="new-socialprofile-iconurl" class="widefat code" /></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><a href="#" id="new-socialprofile-add"><?php _e( 'Add new Social Media Profile', 'graphene' ); ?></a></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        
        <?php /* Social Sharing Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Social Sharing Buttons', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="show_addthis"><?php _e( 'Show social sharing button', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[show_addthis]" id="show_addthis" <?php checked( $graphene_settings['show_addthis'] ); ?> value="true" data-toggleOptions="true" /></td>
                    </tr>
                </table>
                <table class="form-table<?php if ( $graphene_settings['show_addthis'] != true ) echo ' hide'; ?>">                    
                    <tr>
                        <th scope="row"><label for="show_addthis_page"><?php _e( 'Show in Pages as well?', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[show_addthis_page]" id="show_addthis_page" <?php checked( $graphene_settings['show_addthis_page'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="show_addthis_page"><?php _e( 'Show in home and archive pages?', 'graphene' ); ?></label></th>
                        <td>
                        	<input type="checkbox" name="graphene_settings[show_addthis_archive]" id="show_addthis_archive" <?php checked( $graphene_settings['show_addthis_archive'] ); ?> value="true" /><br />
                            <span class="description"><?php printf( __( "Enabling this will cause the social sharing buttons to appear on posts listing pages, like the home page and archive pages. Use the available tags in the code below to get the post's URL, title, and excerpt. Otherwise, all your buttons will share the same URL and title. If you're using AddThis, see the %s.", 'graphene' ), '<a href="http://support.addthis.com/customer/portal/articles/381263-addthis-client-api#.T2UTU9V7lI1">' . __( 'AddThis Client API', 'graphene' ) . '</a>' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="addthis_location"><?php _e( 'Social sharing buttons location', 'graphene' ); ?></label></th>
                        <td>
                        	<select name="graphene_settings[addthis_location]" id="addthis_location">
                        		<option value="post-bottom" <?php selected( $graphene_settings['addthis_location'], 'post-bottom' ); ?>><?php _e( 'Bottom of posts', 'graphene' ); ?></option>
                                <option value="post-top" <?php selected( $graphene_settings['addthis_location'], 'post-top' ); ?>><?php _e( 'Top of posts', 'graphene' ); ?></option>
                                <option value="top-bottom" <?php selected( $graphene_settings['addthis_location'], 'top-bottom' ); ?>><?php _e( 'Both top and bottom', 'graphene' ); ?></option>
                        	</select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="addthis_code"><?php _e("Your social sharing button code", 'graphene' ); ?></label><br />
                            <small><?php _e( 'You can use codes from any popular social sharing sites, like Facebook, Digg, AddThis, etc.', 'graphene' ); ?></small>
                        </th>
                        <td><textarea name="graphene_settings[addthis_code]" id="addthis_code" cols="60" rows="10" class="widefat code"><?php echo htmlentities(stripslashes( $graphene_settings['addthis_code'] ) ); ?></textarea><br />
                        	<span class="description"><?php _e("You may use these tags to get the post's URL, title, and excerpt:", 'graphene' ); ?> <code>[#post-url]</code>, <code>[#post-title]</code>, <code>[#post-excerpt]</code></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div> 
        
        
        <?php /* AdSense Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Adsense Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="show_adsense"><?php _e( 'Show Adsense advertising', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[show_adsense]" id="show_adsense" <?php checked( $graphene_settings['show_adsense'] ); ?> value="true" data-toggleOptions="true" /></td>
                    </tr>
                </table>
                <table class="form-table<?php if ( $graphene_settings['show_adsense'] == false ) echo ' hide'; ?>"> 
                    <tr>
                        <th scope="row">
                            <label for="adsense_show_frontpage"><?php _e( 'Show ads on front page as well', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[adsense_show_frontpage]" id="adsense_show_frontpage" <?php checked( $graphene_settings['adsense_show_frontpage'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adsense_code"><?php _e("Your Adsense code", 'graphene' ); ?></label>
                        </th>
                        <td><textarea name="graphene_settings[adsense_code]" id="adsense_code" cols="60" rows="10" class="widefat code"><?php echo htmlentities(stripslashes( $graphene_settings['adsense_code'] ) ); ?></textarea></td>
                    </tr>
                </table>
            </div>
        </div>
        
        
        <?php /* Google Analytics Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Google Analytics Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="show_ga"><?php _e( 'Enable Google Analytics tracking', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[show_ga]" id="show_ga" <?php checked( $graphene_settings['show_ga'] ); ?> value="true" data-toggleOptions="true" /></td>
                    </tr>
                </table>                
                <table class="form-table<?php if ( $graphene_settings['show_ga'] == false ) echo ' hide'; ?>">      
                    <tr>
                        <td colspan="2">
                            <p><?php _e( '<strong>Note:</strong> the theme now places the Google Analytics script in the <code>&lt;head&gt;</code> element to better support the new asynchronous Google Analytics script. Please make sure you update your script to use the new asynchronous script from Google Analytics.', 'graphene' ); ?></p>
                        </td>
                    </tr>                    
                    <tr>
                        <th scope="row"><label for="ga_code"><?php _e("Google Analytics tracking code", 'graphene' ); ?></label><br />
                        <small><?php _e( 'Make sure you include the full tracking code (including the <code>&lt;script&gt;</code> and <code>&lt;/script&gt;</code> tags) and not just the <code>UA-#######-#</code> code.','graphene' ); ?></small>
                        </th>
                        <td><textarea name="graphene_settings[ga_code]" id="ga_code" cols="60" rows="7" class="widefat code"><?php echo htmlentities(stripslashes( $graphene_settings['ga_code'] ) ); ?></textarea></td>
                    </tr>
                </table>
            </div>
        </div>
        
        
        <?php /* Footer Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Footer Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">       	
                    <tr>
                        <th scope="row"><label for="show_cc"><?php _e( 'Use Creative Commons licence for content', 'graphene' ); ?></label><br />
                        <span class="cc-logo">&nbsp;</span>
                        <td><input type="checkbox" name="graphene_settings[show_cc]" id="show_cc" <?php checked( $graphene_settings['show_cc'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="copy_text"><?php _e( "Copyright text (html allowed)", 'graphene' ); ?></label>
                        <br /><small><?php _e( 'If this field is empty, the following default copyright text will be displayed:', 'graphene' ); ?></small>
                        <p style="background-color:#fff;padding:5px;border:1px solid #ddd;"><small><?php printf( '&copy; %1$s %2$s.', date( 'Y' ), get_bloginfo( 'name' ) ); ?></small></p>
                        </th>
                        <td><textarea name="graphene_settings[copy_text]" id="copy_text" cols="60" rows="7"><?php echo stripslashes( $graphene_settings['copy_text'] ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hide_copyright"><?php _e( 'Do not show copyright info', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[hide_copyright]" id="hide_copyright" <?php checked( $graphene_settings['hide_copyright'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hide_return_top"><?php _e( 'Do not show the "Return to top" link', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[hide_return_top]" id="hide_return_top" <?php checked( $graphene_settings['hide_return_top'] ); ?> value="true" /></td>
                    </tr>
                </table>
            </div>
        </div> 
        
        
        <?php /* Print Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Print Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="print_css"><?php _e( 'Enable print CSS for single posts and pages?', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[print_css]" id="print_css" <?php checked( $graphene_settings['print_css'] ); ?> value="true" data-toggleOptions="true" /></td>
                    </tr> 
                </table>
                <table class="form-table<?php if ( $graphene_settings['print_css'] == false ) echo ' hide'; ?>"> 
                    <tr>
                        <th scope="row"><label for="print_button"><?php _e( 'Show print button', 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[print_button]" id="print_button" <?php checked( $graphene_settings['print_button'] ); ?> value="true" /></td>                        
                    </tr>
                </table>
            </div>
        </div>  

<?php } // Closes the graphene_options_general() function definition