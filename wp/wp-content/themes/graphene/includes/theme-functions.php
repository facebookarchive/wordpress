<?php
/**
 * This function retrieves the header image for the theme
*/
if ( ! function_exists( 'graphene_get_header_image' ) ) :
	function graphene_get_header_image( $post_id = NULL){
		global $graphene_settings;
		
		if ( is_singular() && has_post_thumbnail( $post_id ) && ( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'post-thumbnail' ) ) &&  $image[1] >= HEADER_IMAGE_WIDTH && !$graphene_settings['featured_img_header']) {
			// Houston, we have a new header image!
			// Gets only the image url. It's a pain, I know! Wish WordPress has better options on this one
			$header_img = get_the_post_thumbnail( $post_id, 'post-thumbnail' );
			$header_img = explode( '" class="', $header_img);
			$header_img = $header_img[0];
			$header_img = explode( 'src="', $header_img);
			$header_img = $header_img[1]; // only the url
		}
		else if ( $graphene_settings['use_random_header_img']){
			$default_header_images = graphene_get_default_headers();
			$randomkey = array_rand( $default_header_images);
			$header_img = str_replace( '%s', get_template_directory_uri(), $default_header_images[$randomkey]['url']);
		} else {
			$header_img = get_header_image();
		}
	return $header_img;
}
add_action( 'graphene_get_header_image', 'graphene_get_header_image' );
endif;


/**
 * This functions adds additional classes to the <body> element. The additional classes
 * are added by filtering the WordPress body_class() function.
*/
function graphene_body_class( $classes ){
    
    $column_mode = graphene_column_mode();
    $classes[] = $column_mode;
    // for easier CSS
    if ( strpos( $column_mode, 'two_col' ) === 0 ){
        $classes[] = 'two-columns';
    } else if ( strpos( $column_mode, 'three_col' ) === 0 ){
        $classes[] = 'three-columns';
    }
    
    // Prints the body class
    return $classes;
}
add_filter( 'body_class', 'graphene_body_class' );


/**
 * Add the .sticky post class to sticky posts in the home page if the "Front page posts 
 * categories" option is being used
*/
function graphene_sticky_post_class( $classes ){
	if ( is_sticky() && ! in_array( 'sticky', $classes ) && is_home() ){
		$classes[] = 'sticky';	
	}
	return $classes;
}
add_filter( 'post_class', 'graphene_sticky_post_class' );


/**
 * Add Facebook and Twitter icon to top bar
*/
function graphene_top_bar_social(){
    global $graphene_settings;

    /* Loop through the registered custom social modia */
    $social_profiles = $graphene_settings['social_profiles'];
	if ( in_array( false, $social_profiles) ) return;
	
    foreach ( $social_profiles as $social_key => $social_profile ) : 
        if ( ! empty( $social_profile['url'] ) || $social_profile['type'] == 'rss' ) : 
            $title = graphene_determine_social_medium_title( $social_profile );
            $class = 'mysocial social-' . $social_profile['type'];
            $extra = $graphene_settings['social_media_new_window'] ?  ' target="_blank"' : '';
            $url = ( $social_profile['type'] == 'rss' && empty( $social_profile['url'] ) ) ? get_bloginfo('rss2_url') : $social_profile['url'];
            if ( $social_profile['type'] == 'custom' ) {
                $extra .= ' style="background-image:url(' . $social_profile['icon_url']. ')"';
                $class = 'mysocial-icon';
            } ?>
            <a href="<?php echo $url; ?>" title="<?php echo $title; ?>" class="<?php echo $class; ?>"<?php echo $extra; ?>><span><?php echo $title; ?></span></a>                
    <?php endif;
    endforeach;
}
add_action( 'graphene_social_profiles', 'graphene_top_bar_social' );

/**
 * Determine the title for the social medium.
 * @param array $social_medium
 * @return string 
 */
function graphene_determine_social_medium_title( $social_medium ) {
    if ( isset( $social_medium['title'] ) && ! empty( $social_medium['title']) ) {
        return esc_attr__( $social_medium['title'] );
    }
    else {
        /* translators: %1$s is the website's name, %2$s is the social media name */
        return sprintf( esc_attr__( 'Visit %1$s\'s %2$s page', 'graphene' ), get_bloginfo( 'name' ), ucfirst( $social_profile['type'] ) );
    }
}


/**
 * Returns the width in pixels for the specified grid number
 *
 * @param int $mod Optional Width in pixels to add/subtract from the calculated grid width
 * @param int $grid_one Grid number for 1 column layout
 * @param int $grid_two Grid number for 1 column layout
 * @param int $grid_three Grid number for 1 column layout
 * @return int Grid width in pixels
 *
 * @package Graphene
 * @since 1.6
*/
function graphene_grid_width( $mod = '', $grid_one = 1, $grid_two = '', $grid_three = '', $post_id = NULL ){
	$grid_two = ( ! $grid_two ) ? $grid_one : $grid_two ;
	$grid_three = ( ! $grid_three ) ? $grid_one : $grid_three ;
	
	global $graphene_settings;
	$grid_width = $graphene_settings['grid_width'];
	$gutter_width = $graphene_settings['gutter_width'] * 2;
	$column_mode = graphene_column_mode( $post_id );
	
	$width = $grid_width;
	
	if ( strpos( $column_mode, 'one_col' ) === 0 )
		$width = $grid_width * $grid_one + $gutter_width * ($grid_one - 1);
	if ( strpos( $column_mode, 'two_col' ) === 0 )
		$width = $grid_width * $grid_two + $gutter_width * ($grid_two - 1);
	if ( strpos( $column_mode, 'three_col' ) === 0 )
		$width = $grid_width * $grid_three + $gutter_width * ($grid_three - 1);
		
	if ( $mod )
		$width += $mod;
		
	if ( $width < 0 )
		$width = 0;
		
	return apply_filters( 'graphene_grid_width', $width, $mod, $grid_one, $grid_two, $grid_three );
}


/**
 * Returns the 960 grid system classes.
 *
 * @param string $classes Optional additional classes
 * @param int $grid_one Grid number for 1 column layout
 * @param int $grid_two Grid number for 1 column layout
 * @param int $grid_three Grid number for 1 column layout
 * @param bool $alpha Switch for the alpha class
 * @param bool $omega Switch for the omega class
 * @return array Grid system classes
 *
 * @package Graphene
 * @since 1.6
*/
function graphene_get_grid( $classes = '', $grid_one = 1, $grid_two = '', $grid_three = '', $alpha = false, $omega = false ){
	
	$grid_two = ( ! $grid_two ) ? $grid_one : $grid_two ;
	$grid_three = ( ! $grid_three ) ? $grid_one : $grid_three ;
	
	$column_mode = graphene_column_mode();
	
	$grid = array();
	
	if ( $classes )
		$grid = array_merge( $grid, explode( ' ', trim( $classes ) ) );
	
	if ( strpos( $column_mode, 'one_col' ) === 0 )
		$grid[] = 'grid_' . $grid_one;
	if ( strpos( $column_mode, 'two_col' ) === 0 )
		$grid[] = 'grid_' . $grid_two;
	if ( strpos( $column_mode, 'three_col' ) === 0 )
		$grid[] = 'grid_' . $grid_three;
	
	if ( $alpha ){
		if ( is_rtl() )
			$grid[] = 'alpha-rtl';
		else
			$grid[] = 'alpha';
	}
	if ( $omega ){
		if ( is_rtl() )		
			$grid[] = 'omega-rtl';
		else
			$grid[] = 'omega';
	}
		
	return apply_filters( 'graphene_grid', $grid, $classes, $grid_one, $grid_two, $grid_three, $alpha, $omega );
}

/**
 * Prints the 960 grid system classes
 *
 * @param string $classes Optional additional classes
 * @param int $grid_one grid number for 1 column layout
 * @param int $grid_two grid number for 1 column layout
 * @param int $grid_three grid number for 1 column layout
 * @param bool $alpha switch for the alpha class
 * @param bool $omega switch for the omega class
 *
 * @package Graphene
 * @since 1.6
*/
function graphene_grid( $classes = '', $grid_one = 1, $grid_two = '', $grid_three = '', $alpha = false, $omega = false ){
	// Separates classes with a single space	
	echo 'class="' . implode( ' ', graphene_get_grid( $classes, $grid_one, $grid_two, $grid_three, $alpha, $omega ) ) . '"';
}

if ( ! function_exists( 'graphene_get_avatar_uri' ) ) :
/**
 * Retrieve the avatar URL for a user who provided a user ID or email address.
 *
 * @uses WordPress' get_avatar() function, except that it
 * returns the URL to the gravatar image only, without the <img> tag.
 *
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL to a default image to use if no avatar is available
 * @param string $alt Alternate text to use in image tag. Defaults to blank
 * @return string URL for the user's avatar
 *
 * @package Graphene
 * @since 1.6
*/
function graphene_get_avatar_uri( $id_or_email, $size = '96', $default = '', $alt = false ) {
	
	// Silently fails if < PHP 5
	if ( ! function_exists( 'simplexml_load_string' ) ) return;
	
	$avatar = get_avatar( $id_or_email, $size, $default, $alt );
	if ( ! $avatar ) return false;
	
	$avatar_xml = simplexml_load_string( $avatar );
	$attr = $avatar_xml->attributes();
	$src = $attr['src'];

	return apply_filters( 'graphene_get_avatar_url', $src, $id_or_email, $size, $default, $alt );
}
endif;


function graphene_feed_link($output, $feed) {
    global $graphene_settings;
    
    if ( ( $feed == 'rss2' || $feed == 'rss' ) 
            && $graphene_settings['use_custom_rss_feed'] && ! empty( $graphene_settings['custom_rss_feed_url'] ) ) {
        $output = $graphene_settings['custom_rss_feed_url'];    
    }
    return $output;
}
add_filter( 'feed_link', 'graphene_feed_link', 1, 2 );


/**
 * Displays a notice to logged in users if there is no widgets placed in the displayed sidebars
 */
function graphene_sidebar_notice( $sidebar_name = '' ){
	$html = '[warning]<p>';
	$html .= sprintf( __( 'You haven\'t placed any widget into this widget area. Go to %1$s and place some widgets in the widget area called %2$s.', 'graphene' ), '<em>' . __( 'WP Admin > Appearance > Widgets', 'graphene' ) . '</em>', '<strong>' . $sidebar_name . '</strong>' ) . '</p>';
	$html .= '<p>' . __( "This notice will not be displayed to your site's visitors.", 'graphene' ) . '</p>';
	$html .= '[/warning]';
	echo do_shortcode( apply_filters( 'graphene_sidebar_notice', $html, $sidebar_name ) );
}
?>