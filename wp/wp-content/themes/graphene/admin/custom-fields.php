<?php
/**
 * This file adds a new meta box to the Edit Post and Edit Page screens that contain
 * additional post- and page-specific options for use with the theme
 *
 * @package Graphene
 * @since Graphene 1.1
*/

/** 
 * Add the custom meta box 
*/
function graphene_add_meta_box(){
	add_meta_box( 'graphene_custom_meta', __( 'Graphene post-specific options','graphene' ), 'graphene_custom_meta', 'post', 'normal', 'high' );
	add_meta_box( 'graphene_custom_meta', __( 'Graphene page-specific options','graphene' ), 'graphene_custom_meta', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'graphene_add_meta_box' );




/**
 * Add or update the options
*/
function graphene_save_custom_meta( $post_id ){
	
	/** 
	 * verify this came from our screen and with proper authorization, because
	 * save_post can be triggered at other times 
	*/
	if (isset( $_POST['graphene_save_custom_meta']) ){
		if ( ! wp_verify_nonce( $_POST['graphene_save_custom_meta'], 'graphene_save_custom_meta' ) ) {
			  return $post_id;
		}
	} else {
		return $post_id;
	}
  
	/**
	 * verify if this is an auto save routine. If it is our form has not been submitted, 
	 * so we dont want to do anything
	*/
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
  
	/* Check permissions */
	if ( 'page' == $_POST['post_type']) {
	  if ( ! current_user_can( 'edit_page', $post_id) )
		return $post_id;
	} else {
	  if ( ! current_user_can( 'edit_post', $post_id) )
		return $post_id;
	}

	/* OK, we're authenticated: saving the data */
	update_post_meta( $post_id, '_graphene_slider_img', $_POST['graphene_slider_img']);        
	update_post_meta( $post_id, '_graphene_slider_imgurl', $_POST['graphene_slider_imgurl']);
        update_post_meta( $post_id, '_graphene_slider_url', $_POST['graphene_slider_url']);
	update_post_meta( $post_id, '_graphene_show_addthis', $_POST['graphene_show_addthis']);
        
	/* Post-specific options */
	if ( 'post' == $_POST['post_type'] ) {
		update_post_meta( $post_id, '_graphene_post_date_display', $_POST['graphene_post_date_display'] );
	}
		
	/* Page-specific options */
	if ( 'page' == $_POST['post_type']) {
		update_post_meta( $post_id, '_graphene_nav_description', $_POST['graphene_nav_description']);        
	}
}
add_action( 'save_post', 'graphene_save_custom_meta' );




/**
 * Display the custom meta box content
*/
function graphene_custom_meta( $post){ 

	// Use nonce for verification
	wp_nonce_field( 'graphene_save_custom_meta', 'graphene_save_custom_meta' );
	
	/* Get the current settings */
	$slider_img = ( get_post_meta( $post->ID, '_graphene_slider_img', true ) ) ? get_post_meta( $post->ID, '_graphene_slider_img', true ) : 'global';
        $slider_url = ( get_post_meta( $post->ID, '_graphene_slider_url', true ) ) ? get_post_meta( $post->ID, '_graphene_slider_url', true ) : '';
	$slider_imgurl = ( get_post_meta( $post->ID, '_graphene_slider_imgurl', true ) ) ? get_post_meta( $post->ID, '_graphene_slider_imgurl', true ) : '';
	$show_addthis = ( get_post_meta( $post->ID, '_graphene_show_addthis', true ) ) ? get_post_meta( $post->ID, '_graphene_show_addthis', true ) : 'global';
        
	if ( 'post' == $post->post_type ){
		$post_date_display = ( get_post_meta( $post->ID, '_graphene_post_date_display', true ) ) ? get_post_meta( $post->ID, '_graphene_post_date_display', true ) : '';
	}

	if ( 'page' == $post->post_type){
		$nav_description = ( get_post_meta( $post->ID, '_graphene_nav_description', true ) ) ? get_post_meta( $post->ID, '_graphene_nav_description', true ) : '';
	}
	?>
    
	<p><?php _e("These settings will only be applied to this particular post or page you're editing. They will override the global settings set in the Graphene Options or Graphene Display options page.", 'graphene' ); ?></p>
    <h4><?php _e( 'Slider options', 'graphene' ); ?></h4>    
    <table class="form-table">
    	<tr>
            <th scope="row">
                <label for="graphene_slider_img"><?php _e( 'Slider image', 'graphene' ); ?></label>
            </th>
            <td>
                <select id="graphene_slider_img" name="graphene_slider_img">
                	<option value="global" <?php selected( $slider_img, 'global' ); ?>><?php _e( 'Use global setting', 'graphene' ); ?></option>
                    <option value="disabled" <?php selected( $slider_img, 'disabled' ); ?>><?php _e("Don't show image", 'graphene' ); ?></option>
                    <option value="featured_image" <?php selected( $slider_img, 'featured_image' ); ?>><?php _e("Featured Image", 'graphene' ); ?></option>
                    <option value="post_image" <?php selected( $slider_img, 'post_image' ); ?>><?php _e("First image in post", 'graphene' ); ?></option>
                    <option value="custom_url" <?php selected( $slider_img, 'custom_url' ); ?>><?php _e("Custom URL", 'graphene' ); ?></option>
                </select>
            </td>
        </tr>        
        <tr>
            <th scope="row">
                <label for="graphene_slider_imgurl"><?php _e( 'Custom slider image URL', 'graphene' ); ?></label>
            </th>
            <td>
                <input type="text" id="graphene_slider_imgurl" name="graphene_slider_imgurl" value="<?php echo $slider_imgurl; ?>" size="60" /><br />
                <span class="description"><?php _e( 'Make sure you select Custom URL in the slider image option above to use this custom url.', 'graphene' ); ?></span>                        
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="graphene_slider_url"><?php _e( 'Custom slider URL', 'graphene' ); ?></label>
            </th>
            <td>
                <input type="text" id="graphene_slider_url" name="graphene_slider_url" value="<?php echo $slider_url; ?>" size="60" /><br />
                <span class="description"><?php _e( 'Use this to override the link that is used in the slider.', 'graphene' ); ?></span>                        
            </td>
        </tr>
    </table>
    <h4><?php _e( 'Display options', 'graphene' ); ?></h4>
    <table class="form-table">
    	<tr>
            <th scope="row">
                <label for="graphene_show_addthis"><?php _e( 'AddThis Social Sharing button', 'graphene' ); ?></label>
            </th>
            <td>
                <select id="graphene_show_addthis" name="graphene_show_addthis">
                	<option value="global" <?php selected( $show_addthis, 'global' ); ?>><?php _e( 'Use global setting', 'graphene' ); ?></option>
                    <option value="show" <?php selected( $show_addthis, 'show' ); ?>><?php _e("Show button", 'graphene' ); ?></option>
                    <option value="hide" <?php selected( $show_addthis, 'hide' ); ?>><?php _e("Hide button", 'graphene' ); ?></option>
                </select>
            </td>
        </tr>
        
        <?php if ( 'post' == $post->post_type) : ?>
        <tr>
            <th scope="row">
                <label for="graphene_post_date_display"><?php _e( 'Post date display', 'graphene' ); ?></label>
            </th>
            <td>
                <select id="graphene_post_date_display" name="graphene_post_date_display">
                	<option value="global" <?php selected( $post_date_display, 'global' ); ?>><?php _e( 'Use global setting', 'graphene' ); ?></option>
                    <option value="hide" <?php selected( $post_date_display, 'hide' ); ?>><?php _e( 'Hide date', 'graphene' ); ?></option>
                </select>
            </td>
        </tr>
        <?php endif; ?>
        
    </table>
    <?php if ( 'page' == $post->post_type): ?>
    <h4><?php _e( 'Navigation options', 'graphene' ); ?></h4>
    <table class="form-table">
    	<tr>
            <th scope="row">
                <label for="graphene_nav_description"><?php _e( 'Description', 'graphene' ); ?></label>
            </th>
            <td>
                <input type="text" id="graphene_nav_description" name="graphene_nav_description" value="<?php echo $nav_description; ?>" size="60" /><br />
                <span class="description"><?php _e( 'Only required if you need a description in the navigation menu and you are not using a custom menu.', 'graphene' ); ?></span>                        
            </td>
        </tr>
    </table>
     <?php endif; ?>
<?php	
}

?>