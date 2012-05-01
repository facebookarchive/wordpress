<?php
/**
 * Custom theme Hooks.
 */
function delicate_scripts() {
  wp_enqueue_script("jquery");
	if (is_singular() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	wp_enqueue_script('superfish', get_template_directory_uri().'/js/superfish.js', array('jquery'), '2.9.2');
	wp_enqueue_script('cycle', get_template_directory_uri().'/js/jquery.cycle.all.min.js', array('jquery'));
	wp_register_script('ie_menu', get_stylesheet_directory_uri() . '/js/menu.js', array(), NULL, true);
	
	if (t_get_option('t_cufon_replace') == 'yes') {
    wp_enqueue_script('cufon', get_template_directory_uri().'/js/cufon.js');
    wp_enqueue_script('cufon_font', get_template_directory_uri().'/js/font.js');
	}
}
add_action('wp_enqueue_scripts', 'delicate_scripts'); // scripts

 
add_editor_style(); 
add_theme_support( 'post-thumbnails' );
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'slide-thumb', 970, 225, true); //(cropped)
}

add_custom_background(); // Add support for custom backgrounds

// Custom Header
define( 'HEADER_TEXTCOLOR', '333333' );
if (get_option('nattywp_custom_header') != '') {
  define('HEADER_IMAGE', get_option('nattywp_custom_header')); // %s is the template dir uri
} else {
  define ('HEADER_IMAGE', '');}
define ('HEADER_IMAGE_WIDTH', apply_filters( 'delicate_header_image_width', 970 ));
define ('HEADER_IMAGE_HEIGHT', apply_filters( 'delicate_header_image_height', 225 ));
add_theme_support( 'custom-header', array( 'random-default' => true ) ); // Enable Random
add_custom_image_header ('delicate_header_style', 'delicate_admin_header_style', 'delicate_admin_header_image');
	register_default_headers (array(
		'blue' => array(
			'url' => '%s/images/header/headers.jpg',
			'thumbnail_url' => '%s/images/header/headers-thumbnail.jpg',
			'description' => __('Blue', 'nattywp')
		),
		'green' => array(
			'url' => '%s/images/header/header.jpg',
			'thumbnail_url' => '%s/images/header/header-thumbnail.jpg',
			'description' => __('Green', 'nattywp')
		),
	));
	
if ( !function_exists('delicate_header_style'))	:
  function delicate_header_style() { ?>
	<style type="text/css">
     <?php if ( 'blank' == get_header_textcolor() ) : ?>
      .head-img .tagline {display:none;}
    <?php else : ?>
      .head-img .tagline {color: #<?php echo get_header_textcolor(); ?> !important;}
    <?php endif; ?>
	</style>
	<?php
}
endif; // delicate_header_style	

if ( !function_exists( 'delicate_admin_header_style')) :
  function delicate_admin_header_style() { ?>
	<style type="text/css">
    .appearance_page_custom-header #headimg {border: none; width:970px;}
    #headimg {position:relative; background:#f7f7f7;}
    #desc {font-size:12px; font-weight:bold; padding:6px 17px 4px 12px; position:absolute; top:50%; right:0px; background:#fff; text-transform:uppercase; <?php if ( get_header_textcolor() != HEADER_TEXTCOLOR ) echo 'color: #'. get_header_textcolor().';'; ?>}
    #headimg img {max-width:970px; height: auto; width: 100%;}
	</style>
<?php
}
endif; // delicate_admin_header_style

if ( !function_exists('delicate_admin_header_image')) :
  function delicate_admin_header_image() { ?>
    <div id="headimg">
      <div id="desc"><?php bloginfo( 'description' ); ?></div>
      <?php $header_image = get_header_image();
      if ( ! empty( $header_image ) ) : ?>
        <img src="<?php echo esc_url( $header_image ); ?>" alt="Header" />
      <?php endif; ?>
    </div>
  <?php }
endif; // delicate_admin_header_image

if (is_admin() && isset($_GET['page'] )) {
    if ($_GET['page'] == 'custom-header' && (t_get_option("t_show_slideshow") != 'yes')) 
    echo '<div id="message4" class="updated" style="border:1px solid #c43;"><p><strong>Note:</strong> The Custom Header is currently disabled. You should get back to Theme Options and configure Header Area Settings. To do this, open <a href="?page=nattywp_home">Theme Options</a> select <strong>Front Page Settings</strong> tab and choose <strong>Display Header Image</strong> value from drop down list.</p></div>';
}
// END Custom Header
 
if (!isset($content_width))
	$content_width = 590;
	
if (function_exists('register_nav_menus')) 
register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'nattywp' ),
		//'secondary' => __( 'Secondary Navigation', 'nattywp'),
) );

function natty_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'natty_page_menu_args' );

function natty_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Read more <span class="meta-nav">&rarr;</span>', 'nattywp' ) . '</a>';
}
function natty_auto_excerpt_more( $more ) {
	return ' &hellip;' . natty_continue_reading_link();
}
function natty_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= natty_continue_reading_link();
	}
	return $output;
}
add_filter( 'excerpt_more', 'natty_auto_excerpt_more' );
add_filter( 'get_the_excerpt', 'natty_custom_excerpt_more' );


function natty_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'natty_remove_gallery_css' );


function natty_show_navigation($args, $func) {		
 if (function_exists('wp_nav_menu')) {
wp_nav_menu( array( 'container' => '', 'menu_class' => 'topnav fl fr sf-js-enabled sf-shadow', 'menu_id' => 'nav-ie', 'theme_location' => $args, 'link_before' => '<span>', 'link_after' => '</span>', 'fallback_cb' => $func ) );
 } else { 
  natty_show_pagemenu ();
	}
}

function natty_show_pagemenu () {
 echo '<ul id="nav-ie" class="topnav fl fr sf-js-enabled sf-shadow">';
 echo '<li ';
    if(is_home()){ echo 'class="current_page_item"';}
 echo '><a href="/"><span>';
 if (t_get_option('t_home_name') == 'no')
    echo 'Home';
 else
    echo t_get_option('t_home_name');
 echo '</span></a></li>';
 t_show_pag();
 echo '</ul>';
}

function natty_get_profile() {
	printf( __( '%1$s', 'nattywp' ),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'nattywp' ), get_the_author() ),
			get_the_author()
		)
	);
}
?>