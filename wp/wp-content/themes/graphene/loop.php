<?php 
	global $graphene_settings; 
	$post_type = get_post_type_object( get_post_type() );
?>
<?php 
/**
 * Check if the post has a post format. Load a post-format specific loop file,
 * if it has. Continue with standard loop otherwise.
*/ 
if ( function_exists( 'get_post_format' ) && $post_type->name != 'page' ) {
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

<?php if ( $post_type->name == 'page' && $graphene_settings['hide_parent_content_if_empty'] && $post->post_content == '' ) : ?>
<h1 class="page-title">
	<?php if ( get_the_title() == '' ) { _e( '(No title)', 'graphene' ); } else { the_title(); } ?>
</h1>
<?php else : ?>                
<div id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix post' ); ?>>
	
	<?php do_action( 'graphene_before_post' ); ?>
	
	<div class="entry clearfix">
    
    	<?php /* Post date is not shown if this is a Page post */ ?>
		<?php if ( ( strpos( $graphene_settings['post_date_display'], 'icon_' ) === 0 ) && graphene_should_show_date() ) : ?>
            <?php graphene_post_date(); ?>
        <?php endif; ?>
		
        <?php /* Show the post author's gravatar if enabled */
		if ( $graphene_settings['show_post_avatar'] ) {
			echo '<div class="post-avatar-wrap gutter-left">' . get_avatar( get_the_author_meta( 'user_email' ), 45 ) . '</div>';
		} 
		?>
        
        <?php 
		/* Add a print button only for single pages/posts 
		 * and if enabled in the theme option.
		 */
		if ( $graphene_settings['print_button'] && is_singular() ) : ?>
			<?php graphene_print_button(); ?>
		<?php endif; ?>
		
		<?php /* Add an email post icon if the WP-Email plugin is installed and activated */
			if( function_exists( 'wp_email' ) && is_singular() ) { echo '<p class="email">'; email_link(); echo '</p>'; }
		?>
        
		<?php /* Post title */ ?>
        <h2 class="post-title entry-title">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'graphene' ), the_title_attribute( 'echo=0' ) ); ?>">
				<?php if ( get_the_title() == '' ) { _e( '(No title)', 'graphene' ); } else { the_title(); } ?>
            </a>
			<?php do_action( 'graphene_post_title' ); ?>
        </h2>
		
		
		<?php /* Post meta */ ?>
		<?php if ( $post_type->name != 'page' || is_user_logged_in() ) : ?>
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
			<p class="post-date-inline updated">
				<abbr class="published" title="<?php the_date( 'c' ); ?>"><?php the_time( get_option( 'date_format' ) ); ?></abbr>
			</p>
			<?php endif; ?>
			
			<?php /* Post author, not shown if this is a Page post or if admin decides to hide it */ ?>
			<?php if ( $post_type->name != 'page' && $graphene_settings['hide_post_author'] != true ) : ?>
            <p class="post-author author vcard">
				<?php
				/* translators: this is for the author byline, such as 'by John Doe' */
				$author_url = '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" class="url" rel="author">' . get_the_author_meta( 'display_name' ) . '</a>';
				printf( __( 'by %s', 'graphene' ), '<span class="fn nickname">' . $author_url . '</span>' );
				?>
			</p>
			<?php endif; ?>
								
			<?php /* For printing: the date of the post */
				if ( $graphene_settings['print_css'] && graphene_should_show_date() ) {
					 echo graphene_print_only_text( get_the_time( get_option( 'date_format' ) ) );  
				} 
			?>
			
			<?php do_action( 'graphene_post_meta' ); ?>
		</div>
		<?php endif; ?>
		
		<?php /* Post content */ ?>
		<div class="entry-content clearfix">
			<?php do_action( 'graphene_before_post_content' ); ?>
			
			<?php if ( ( is_home() && !$graphene_settings['posts_show_excerpt'] ) || is_singular() || ( ! is_singular() && ! is_home() && $graphene_settings['archive_full_content'] ) ) : ?>
				
				<?php /* Social sharing buttons at top of post */ ?>
				<?php if ( stripos( $graphene_settings['addthis_location'], 'top' ) !== false) { graphene_addthis( get_the_ID() ); } ?>
				
				<?php /* The full content */ ?>
				<?php the_content( '<span class="block-button">' . __( 'Read the rest of this entry &raquo;', 'graphene' ) . '</span>' ); ?>

			<?php else : ?>

				<?php /* The post thumbnail */
				if ( has_post_thumbnail( get_the_ID() ) ) { ?>
					<div class="excerpt-thumb">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'graphene' ), the_title_attribute( 'echo=0' ) ); ?>">
						<?php the_post_thumbnail( apply_filters( 'graphene_excerpt_thumbnail_size', 'thumbnail' ) ); ?>
					</a>
					</div>
					<?php
				} else {
					echo graphene_get_post_image( get_the_ID(), apply_filters( 'graphene_excerpt_thumbnail_size', 'thumbnail' ), 'excerpt' );	
				}
				?>
                
                <?php /* Social sharing buttons at top of post */ ?>
				<?php if ( stripos( $graphene_settings['addthis_location'], 'top' ) !== false && $graphene_settings['show_addthis_archive'] ) { graphene_addthis( get_the_ID() ); } ?>
                
				<?php /* The excerpt */ ?>
				<?php the_excerpt(); ?>
                
			<?php endif; ?>
			
			<?php wp_link_pages( array( 'before' => '<div class="link-pages"><p><strong>' . __( 'Pages:','graphene' ) . '</strong> ', 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
			
			<?php do_action( 'graphene_after_post_content' ); ?>
			
		</div>
		
		<?php /* Post footer */ ?>
		<div class="entry-footer clearfix">
			<?php /* Display the post's tags, if there is any */ ?>
			<?php if ( $post_type->name != 'page' && ( $graphene_settings['hide_post_tags'] != true) ) : ?>
			<p class="post-tags"><?php if ( has_tag() ) { _e( 'Tags:', 'graphene' ); the_tags( ' ', ', ', '' ); } else { _e( 'This post has no tag','graphene' ); } ?></p>
			<?php endif; ?>
			
			<?php /* Display comments popup link. */ ?>
            <?php if ( graphene_should_show_comments() ) : ?>
			<p class="comment-link">
				<?php 
				$comments_num = get_comments_number();
				comments_popup_link( __( 'Leave comment', 'graphene' ), __( '1 comment', 'graphene' ), sprintf( _n( '1 comment', "%d comments", $comments_num, 'graphene' ), $comments_num ), 'comments-link', __( "Comments off", 'graphene' ) ); 
				?>
            </p>
            <?php endif; ?>
            
            <?php 
			/* Display AddThis social sharing button */
			if ( stripos( $graphene_settings['addthis_location'], 'bottom' ) !== false && $graphene_settings['show_addthis_archive'] ) { graphene_addthis( get_the_ID() ); } 
			?>
			
			<?php do_action( 'graphene_post_footer' ); ?>
		</div>
	</div>
</div>
<?php endif; ?>

 <?php /* For printing: the permalink */
	if ( $graphene_settings['print_css']) {
		echo graphene_print_only_text( '<span class="printonly url"><strong>' .__( 'Permanent link to this article:', 'graphene' ). ' </strong><span>' . get_permalink(). '</span></span>' );
	} 
?>

<?php /* Display Adsense advertising */ ?>
<?php if ( ! is_front_page() || ( is_front_page() && $graphene_settings['adsense_show_frontpage'] ) ) { graphene_adsense(); } ?>

<?php do_action( 'graphene_loop_footer' ); ?>