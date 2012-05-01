<?php
/*
 * Theme Settings
 * 
 * @package Hatch
 * @subpackage Template
 */
add_action( 'admin_menu', 'hatch_theme_admin_setup' );

function hatch_theme_admin_setup() {
    
	global $theme_settings_page;
	
	/* Get the theme settings page name */
	$theme_settings_page = 'appearance_page_theme-settings';

	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	/* Create a settings meta box only on the theme settings page. */
	add_action( 'load-appearance_page_theme-settings', 'hatch_theme_settings_meta_boxes' );

	/* Add a filter to validate/sanitize your settings. */
	add_filter( "sanitize_option_{$prefix}_theme_settings", 'hatch_theme_validate_settings' );
	
	/* Enqueue scripts */
	add_action( 'admin_enqueue_scripts', 'hatch_admin_scripts' );
}

/* Adds custom meta boxes to the theme settings page. */
function hatch_theme_settings_meta_boxes() {

	/* Add a custom meta box. */
	add_meta_box(
		'hatch-theme-meta-box',			// Name/ID
		__( 'General settings', 'hatch' ),	// Label
		'hatch_theme_meta_box',			// Callback function
		'appearance_page_theme-settings',		// Page to load on, leave as is
		'normal',					// Which meta box holder?
		'high'					// High/low within the meta box holder
	);

	/* Add additional add_meta_box() calls here. */
}

/* Function for displaying the meta box. */
function hatch_theme_meta_box() { ?>

	<table class="form-table">
	    
		<!-- Favicon upload -->
		<tr class="favicon_url">
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_favicon_url' ) ); ?>"><?php _e( 'Favicon:', 'hatch' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_favicon_url' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'hatch_favicon_url' ) ); ?>" value="<?php echo esc_url( hybrid_get_setting( 'hatch_favicon_url' ) ); ?>" />
				<input id="hatch_favicon_upload_button" class="button" type="button" value="Upload" />
				<br />
				<span class="description"><?php _e( 'Upload favicon image (recommended max size: 32x32).', 'hatch' ); ?></span>
				
				<?php /* Display uploaded image */
				if ( hybrid_get_setting( 'hatch_favicon_url' ) ) { ?>
                    <p><img src="<?php echo esc_url( hybrid_get_setting( 'hatch_favicon_url' ) ); ?>" alt="" /></p>
				<?php } ?>
			</td>
		</tr>
		
		<!-- Logo upload -->
		<tr class="logo_url">
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_logo_url' ) ); ?>"><?php _e( 'Logo:', 'hatch' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_logo_url' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'hatch_logo_url' ) ); ?>" value="<?php echo esc_url( hybrid_get_setting( 'hatch_logo_url' ) ); ?>" />
				<input id="hatch_logo_upload_button" class="button" type="button" value="Upload" />
				<br />
				<span class="description"><?php _e( 'Upload logo image (recommended max width: 200px).', 'hatch' ); ?></span>
				
				<?php /* Display uploaded image */
				if ( hybrid_get_setting( 'hatch_logo_url' ) ) { ?>
                    <p><img src="<?php echo esc_url( hybrid_get_setting( 'hatch_logo_url' ) ); ?>" alt="" /></p>
				<?php } ?>
			</td>
		</tr>
		
		<!-- Author Description -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_author_bio' ) ); ?>"><?php _e( 'Author:', 'hatch' ); ?></label>
			</th>
			<td>
				
				<?php /* Set arguments for the authors dropdown list */
				$author_bio_id = hybrid_settings_field_id( 'hatch_author_bio' );
				$author_bio_name = hybrid_settings_field_name( 'hatch_author_bio' );
				$author_bio_selected = hybrid_get_setting( 'hatch_author_bio' );
				
				$args = array(
					'id' => $author_bio_id,
					'name' => $author_bio_name,
					'selected' => $author_bio_selected
				);
				
				/* Display the authors dropdown list */
				wp_dropdown_users( $args ); ?>
				
				<span class="description"><?php _e( 'Whose biography to display on the home page?', 'hatch' ); ?></span>
				
			</td>
		</tr>		
		
		<!-- Font family -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_font_family' ) ); ?>"><?php _e( 'Font family:', 'hatch' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_font_family' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'hatch_font_family' ) ); ?>">
				<option value="Arial" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Arial' ); ?>> <?php echo __( 'Arial', 'hatch' ) ?> </option>
				<option value="Verdana" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Verdana' ); ?>> <?php echo __( 'Verdana', 'hatch' ) ?> </option>				
				<option value="Bitter" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Bitter' ); ?>> <?php echo __( 'Bitter', 'hatch' ) ?> </option>
				<option value="Georgia" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Georgia' ); ?>> <?php echo __( 'Georgia', 'hatch' ) ?> </option>
				<option value="Droid Serif" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Droid Serif' ); ?>> <?php echo __( 'Droid Serif', 'hatch' ) ?> </option>				
				<option value="Helvetica" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Helvetica' ); ?>> <?php echo __( 'Helvetica', 'hatch' ) ?> </option>
				<option value="Istok Web" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Istok Web' ); ?>> <?php echo __( 'Istok Web', 'hatch' ) ?> </option>			
				<option value="Lucida Sans Unicode" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Lucida Sans Unicode' ); ?>> <?php echo __( 'Lucida Sans Unicode', 'hatch' ) ?> </option>
				<option value="Droid Sans" <?php selected( hybrid_get_setting( 'hatch_font_family' ), 'Droid Sans' ); ?>> <?php echo __( 'Droid Sans', 'hatch' ) ?> </option>
			    </select>
			</td>
		</tr>
		
		<!-- Font size -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_font_size' ) ); ?>"><?php _e( 'Font size:', 'hatch' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_font_size' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'hatch_font_size' ) ); ?>">
				<option value="16" <?php selected( hybrid_get_setting( 'hatch_font_size' ), '16' ); ?>> <?php echo __( 'default', 'hatch' ) ?> </option>
				<option value="17" <?php selected( hybrid_get_setting( 'hatch_font_size' ), '17' ); ?>> <?php echo __( '17', 'hatch' ) ?> </option>
				<option value="16" <?php selected( hybrid_get_setting( 'hatch_font_size' ), '16' ); ?>> <?php echo __( '16', 'hatch' ) ?> </option>
				<option value="15" <?php selected( hybrid_get_setting( 'hatch_font_size' ), '15' ); ?>> <?php echo __( '15', 'hatch' ) ?> </option>
				<option value="14" <?php selected( hybrid_get_setting( 'hatch_font_size' ), '14' ); ?>> <?php echo __( '14', 'hatch' ) ?> </option>
			    </select>
			    <span class="description"><?php _e( 'The base font size in pixels.', 'hatch' ); ?></span>
			</td>
		</tr>		
	    
		<!-- Link color -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_link_color' ) ); ?>"><?php _e( 'Link color:', 'hatch' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_link_color' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'hatch_link_color' ) ); ?>" size="8" value="<?php echo ( hybrid_get_setting( 'hatch_link_color' ) ) ? esc_attr( hybrid_get_setting( 'hatch_link_color' ) ) : '#64a2d8'; ?>" data-hex="true" />
				<div id="colorpicker_link_color"></div>
				<span class="description"><?php _e( 'Set the theme link color.', 'hatch' ); ?></span>
			</td>
		</tr>	    

		<!-- Custom CSS -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_custom_css' ) ); ?>"><?php _e( 'Custom CSS:', 'hatch' ); ?></label>
			</th>
			<td>
				<textarea id="<?php echo esc_attr( hybrid_settings_field_id( 'hatch_custom_css' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'hatch_custom_css' ) ); ?>" cols="60" rows="8"><?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'hatch_custom_css' ) ) ); ?></textarea>
				<span class="description"><?php _e( 'Add your custom CSS here. It would overwrite any default or custom theme settings.', 'hatch' ); ?></span>
			</td>
		</tr>

		<!-- End custom form elements. -->
	</table><!-- .form-table --><?php
	
}

/* Validate theme settings. */
function hatch_theme_validate_settings( $input ) {
	
    $input['hatch_favicon_url'] = esc_url_raw( $input['hatch_favicon_url'] );   
    $input['hatch_logo_url'] = esc_url_raw( $input['hatch_logo_url'] );
	$input['hatch_author_bio'] = wp_filter_nohtml_kses( $input['hatch_author_bio'] );
	$input['hatch_font_family'] = wp_filter_nohtml_kses( $input['hatch_font_family'] );
	$input['hatch_font_size'] = wp_filter_nohtml_kses( $input['hatch_font_size'] );
    $input['hatch_link_color'] = wp_filter_nohtml_kses( $input['hatch_link_color'] );   
    $input['hatch_custom_css'] = wp_filter_nohtml_kses( $input['hatch_custom_css'] );

    /* Return the array of theme settings. */
    return $input;
}

/* Enqueue scripts (and related stylesheets) */
function hatch_admin_scripts( $hook_suffix ) {
    
    global $theme_settings_page;
	
    if ( $theme_settings_page == $hook_suffix ) {
	    
	    /* Enqueue Scripts */
	    wp_enqueue_script( 'hatch_functions-admin', get_template_directory_uri() . '/admin/functions-admin.js', array( 'jquery', 'media-upload', 'thickbox', 'farbtastic' ), '1.0', false );
		
		/* Localize script strings */
		wp_localize_script( 'hatch_functions-admin', 'js_text', array( 'insert_into_post' => __( 'Use this Image', 'hatch' ) ) );		
	    
	    /* Enqueue Styles */
	    wp_enqueue_style( 'functions-admin', get_template_directory_uri() . '/admin/functions-admin.css', false, 1.0, 'screen' );
	    wp_enqueue_style( 'farbtastic' );
    }
}


?>