<?php 
	global $graphene_settings; 
	$post_type = get_post_type_object( get_post_type() );
?>

<?php if ( $graphene_settings['hide_parent_content_if_empty'] && $post->post_content == '' ) : ?>
<h1 class="page-title">
	<?php if ( get_the_title() == '' ) { _e( '(No title)', 'graphene' ); } else { the_title(); } ?>
</h1>
<?php else : ?>                
<div id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix post' ); ?>>
	
	<?php do_action( 'graphene_before_post' ); ?>
	
	<div class="entry clearfix">                
		
        <?php 
		/* Add a print button only for single pages/posts 
		 * and if the theme option is enabled.
		 */
		if ( $graphene_settings['print_button'] ) : ?>
			<?php graphene_print_button( $post_type ); ?>
		<?php endif; ?>
		
		<?php /* Add an email post icon if the WP-Email plugin is installed and activated */
		if( function_exists( 'wp_email' ) && is_singular() ) { echo '<p class="email">'; email_link(); echo '</p>'; }
		?>
        
		<?php /* Post title */ ?>
        <h1 class="post-title entry-title">
			<?php if ( get_the_title() == '' ) { _e( '(No title)', 'graphene' ); } else { the_title(); } ?>
			<?php do_action( 'graphene_page_title' ); ?>
        </h1>
		
		<?php /* Post meta */ ?>
		<div class="post-meta clearfix">
			
			<?php /* Edit post link, if user is logged in */ ?>
			<?php if ( is_user_logged_in() ) : ?>
			<p class="edit-post">
				<?php edit_post_link( sprintf( __( 'Edit %s', 'graphene' ), $post_type->labels->singular_name ), ' (', ')' ); ?>
			</p>
			<?php endif; ?>
            
            <span class="updated">
            	<span class="value-title" title="<?php the_time( 'Y-m-d\TH:i' ); ?>" />
            </span>
														
			<?php do_action( 'graphene_page_meta' ); ?>
		</div>
		
		<?php /* Post content */ ?>
		<div class="entry-content clearfix">
			<?php do_action( 'graphene_before_page_content' ); ?>
				
			<?php /* Social sharing buttons at top of post */ ?>
            <?php if ( stripos( $graphene_settings['addthis_location'], 'top' ) !== false ) { graphene_addthis( get_the_ID() ); } ?>
            
            <?php /* The full content */ ?>
            <?php the_content(); ?>
			
			<?php wp_link_pages( array( 'before' => '<div class="link-pages"><p><strong>' . __( 'Pages:','graphene' ) . '</strong> ', 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
			
			<?php do_action( 'graphene_after_page_content' ); ?>
			
		</div>
		
		<?php /* Post footer */ ?>
		<div class="entry-footer clearfix">
			
			<?php 
				/**
				 * Display AddThis social sharing button
				*/ 
			?>
			<?php if ( stripos( $graphene_settings['addthis_location'], 'bottom' ) !== false) { graphene_addthis( get_the_ID() ); } ?>
			
			<?php do_action( 'graphene_page_footer' ); ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php /* For printing: the permalink */
	if ( $graphene_settings['print_css'] ) {
		echo graphene_print_only_text( '<span class="printonly url"><strong>' . __( 'Permanent link to this article:', 'graphene' ) . ' </strong><span>' . get_permalink() . '</span></span>' );
	}
?>

<?php 
/**
 * Display Adsense advertising
 * See graphene_adsense() function in functions.php
*/ 
graphene_adsense(); ?>

<?php /* List the child pages */ ?>
<?php get_template_part( 'loop', 'children' ); ?>

<?php /* Get the comments template */ ?>
<?php comments_template(); ?>

<?php do_action( 'graphene_loop_footer' ); ?>