<?php 
	global $graphene_settings; 
	$post_type = get_post_type_object( get_post_type() );
?>
<?php 
/**
 * Check if the post has a post format. Load a post-format specific loop file,
 * if it has. Continue with standard loop otherwise.
*/ 
if ( function_exists( 'get_post_format' ) ) {
	global $post_format;
	$post_format = get_post_format();
	
	// Get the post formats supported by the theme
	$supported_formats = get_theme_support( 'post-formats' );
	if ( is_array( $supported_formats ) ) $supported_formats = $supported_formats[0]; 
	
	if ( in_array( $post_format, $supported_formats ) ) {
		
		// Get the post format loop file
		get_template_part( 'loop-post-formats', $post_format );
		
		// Stop this default posts loop
		return;
	}
}
?>

<?php /* Post navigation */ ?>
<?php graphene_post_nav(); ?>
        
<div id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix post' ); ?>>
	
	<?php do_action( 'graphene_before_post' ); ?>
	
	<div class="entry clearfix">
    	
        <?php /* Post date */ ?>
		<?php if ( ( strpos( $graphene_settings['post_date_display'], 'icon_' ) === 0 ) && graphene_should_show_date() ) : ?>
            <?php graphene_post_date( 'icon' ); ?>
        <?php endif; ?>
		
        <?php /* Show the post author's gravatar if enabled */
		if ( $graphene_settings['show_post_avatar'] ) {
			echo '<div class="post-avatar-wrap gutter-left">' . get_avatar( get_the_author_meta( 'user_email' ), 45 ) . '</div>';
		} 
		?>
        
        <?php 
		/* Add a print button only for single pages/posts 
		 * and if the theme option is enabled.
		 */
		if ( $graphene_settings['print_button'] ) : ?>
			<?php graphene_print_button( $post_type ); ?>
		<?php endif; ?>
        
        <?php /* Add an email post icon if the WP-Email plugin is installed and activated */
			if( function_exists( 'wp_email' ) ) { echo '<p class="email">'; email_link(); echo '</p>'; }
		?>
        
		<?php /* Post title */ ?>
        <h1 class="post-title entry-title">
			<?php if ( get_the_title() == '' ) { _e( '(No title)', 'graphene' ); } else { the_title(); } ?>
			<?php do_action( 'graphene_post_title' ); ?>
        </h1>
		
		<?php /* Post meta */ ?>
		<div class="post-meta clearfix">
			
			<?php /* Post category, not shown if admin decides to hide it */ ?>
			<?php if ( ( $graphene_settings['hide_post_cat'] != true ) ) : ?>
			<span class="printonly"><?php _e( 'Categories:', 'graphene' ); ?> </span>
			<p class="meta_categories"><?php the_category( ", " ); ?></p>
			<?php endif; ?>
			
			<?php /* Edit post link, if user is logged in */ ?>
			<?php if ( is_user_logged_in() ) : ?>
			<p class="edit-post">
				<?php edit_post_link( sprintf( __( 'Edit %s', 'graphene' ), $post_type->labels->singular_name ), ' (', ')' ); ?>
			</p>
			<?php endif; ?>
			
			<?php /* Inline post date */ ?>
			<?php if ( $graphene_settings['post_date_display'] == 'text' && graphene_should_show_date() ) : ?>
				<?php graphene_post_date( 'inline' ); ?>
			<?php endif; ?>
			
			<?php /* Post author, not shown if admin decides to hide it */ ?>
			<?php if ( $graphene_settings['hide_post_author'] != true ) : ?>
			<p class="post-author author vcard">
				<?php
				/* translators: this is for the author byline, such as 'by John Doe' */
				$author_url = '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" class="url">' . get_the_author_meta( 'display_name' ) . '</a>';
				printf( __( 'by %s', 'graphene' ), '<span class="fn nickname">' . $author_url . '</span>' );
				?>
			</p>
			<?php endif; ?>
								
			<?php /* For printing: the date of the post */
			if ( $graphene_settings['print_css'] && graphene_should_show_date() ) {
				 echo graphene_print_only_text( '<em>' . get_the_time( get_option( 'date_format' ) ) . '</em>' );  
			}
			?>
			
			<?php do_action( 'graphene_post_meta' ); ?>
		</div>
		
		<?php /* Post content */ ?>
		<div class="entry-content clearfix">
			<?php do_action( 'graphene_before_post_content' ); ?>
				
			<?php /* Social sharing buttons at top of post */ ?>
			<?php if ( stripos( $graphene_settings['addthis_location'], 'top' ) !== false ) { graphene_addthis( get_the_ID() ); } ?>
				
			<?php /* The full content */ ?>
			<?php the_content(); ?>
			
			<?php wp_link_pages( array( 'before' => '<div class="link-pages"><p><strong>' . __( 'Pages:','graphene' ) . '</strong> ', 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
			
			<?php do_action( 'graphene_after_post_content' ); ?>
			
		</div>
		
		<?php /* Post footer */ ?>
		<div class="entry-footer clearfix">
			<?php /* Display the post's tags, if there is any */ ?>
			<?php if ( $graphene_settings['hide_post_tags'] != true ) : ?>
				<p class="post-tags"><?php if ( has_tag() ) { _e( 'Tags:','graphene' ); the_tags( ' ', ', ', '' ); } else { _e( 'This post has no tag','graphene' ); } ?></p>
			<?php endif; ?>
			
			<?php 
			/* Display AddThis social sharing button */
			if ( stripos( $graphene_settings['addthis_location'], 'bottom' ) !== false) { graphene_addthis( get_the_ID() ); } 
			?>
			
			<?php do_action( 'graphene_post_footer' ); ?>
		</div>
	</div>
</div>

<?php 
/**
 * Display the post author's bio in single-post page if enabled
*/
if ( $graphene_settings['show_post_author'] ) :
?>
<h4 class="author_h4 vcard"><?php _e( 'About the author', 'graphene' ); ?></h4>
<div class="author-info clearfix">
	<div <?php graphene_grid( 'author-avatar-wrap', 2, 2, 2, true ); ?>>
	<?php
	if ( $author_imgurl = get_the_author_meta( 'graphene_author_imgurl' ) ) {
		echo '<img class="avatar" src="' . $author_imgurl . '" alt="" />';
	} else {
		echo get_avatar( get_the_author_meta( 'user_email' ), graphene_grid_width( '', 2 ) ); 
	}
	?>
    </div>
	<p class="author_name"><strong><?php the_author_meta( 'display_name' ); ?></strong></p>
	<p class="author_bio"><?php the_author_meta( 'description' ); ?></p>
	
</div>
<?php endif; ?>

<?php /* For printing: the permalink */
	if ( $graphene_settings['print_css'] ) {
		echo graphene_print_only_text( '<span class="printonly url"><strong>' . __( 'Permanent link to this article:', 'graphene' ) . ' </strong><span>' . get_permalink() . '</span></span>' );
	}
?>

<?php 
/**
 * Display Adsense advertising for single post pages 
 * See graphene_adsense() function in functions.php
*/ 
graphene_adsense(); ?>

<?php /* Get the comments template for single post pages */ ?>
<?php comments_template(); ?>

<?php do_action( 'graphene_loop_footer' ); ?>