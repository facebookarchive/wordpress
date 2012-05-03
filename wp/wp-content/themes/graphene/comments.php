<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to graphene_comment which is
 * located in the functions.php file.
 *
 * @package Graphene
 * @since Graphene 1.0
 */
global $graphene_settings;
?>

<?php 
    /* Only show comments depending on the theme setting */
    if ( ! graphene_should_show_comments() ) : 
        return;
    endif;
?>

<?php if ( post_password_required() && ( comments_open() || have_comments() ) ) : ?>
			<div id="comments">
				<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'graphene' ); ?></p>
                
                <?php do_action( 'graphene_protected_comment' ); ?>
			</div><!-- #comments -->
<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
?>

<?php do_action( 'graphene_before_comment_template' ); ?>

<?php /* Lists all the comments for the current post */ ?>
<?php if ( have_comments() ) : ?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('li.comment .comment-permalink').hide();
		$('.comment-wrap').hover( function(){ $('.comment-permalink', this).fadeIn(200); }, function(){ $('.comment-permalink:eq(0)', this).fadeOut(200); });
	});
</script>

<div id="comments" class="clearfix">
	<?php /* Get the comments and pings count */ 
		global $tabbed;
		$comments_num = graphene_get_comment_count();
		// to also show comments awaiting approval
		$allcomments_num = graphene_get_comment_count( 'comments', false );
		$pings_num = graphene_get_comment_count( 'pings' );
		if ( $comments_num )
			$comment_count = sprintf( _n( '1 comment', '%d comments', $comments_num, 'graphene' ), number_format_i18n( $comments_num ) );
		if ( $pings_num ) 
			$ping_count = sprintf( _n( '1 ping', '%d pings', $pings_num, 'graphene' ), number_format_i18n( $pings_num ) );
		$tabbed = ( $comments_num && $pings_num ) ? true : false;
	?>
    <?php if ( $comments_num ) : ?>
    	<h4 class="comments gutter-left current"><?php if ($tabbed) {echo '<a href="#">'.$comment_count.'</a>';} else {echo $comment_count;}?></h4>
	<?php endif; ?>
    <?php if ( $pings_num ) : ?>
	    <h4 class="pings gutter-left"><?php if ($tabbed) {echo '<a href="#">'.$ping_count.'</a>';} else {echo $ping_count;}?></h4>
    <?php endif; ?>

	<?php do_action( 'graphene_before_comments' ); ?>

	<?php if ( $comments_num || $allcomments_num ) : ?>
        <ol class="clearfix" id="comments_list">
            <?php
            /* Loop through and list the comments. Tell wp_list_comments()
             * to use graphene_comment() to format the comments.
             * If you want to overload this in a child theme then you can
             * define graphene_comment() and that will be used instead.
             * See graphene_comment() in functions.php for more.
             */
             $args = array( 'callback' => 'graphene_comment', 'style' => 'ol', 'type' => 'comment' );
             wp_list_comments( apply_filters( 'graphene_comments_list_args', $args ) ); ?>
             
            <?php // Are there comments to navigate through? ?>
            <?php if (get_comment_pages_count() > 1 && get_option( 'page_comments' )) : ?>
            <div class="comment-nav clearfix">
                <?php if ( function_exists( 'wp_commentnavi' ) ) : ?>
                    <?php wp_commentnavi(); ?>
                    <p class="commentnavi-view-all"><?php wp_commentnavi_all_comments_link(); ?></p>
                <?php else : ?> 
                    <p><?php paginate_comments_links(); ?>&nbsp;</p>
                <?php endif; ?>
                <?php do_action( 'graphene_comments_pagination' ); ?>
            </div>
            <?php endif; // Ends the comment navigation ?>
        </ol>
    <?php endif; ?>
    
    <?php if ( $pings_num ) : ?>
        <ol class="clearfix" id="pings_list" <?php if ( ! $comments_num ) echo 'style="display: block;"'; ?>>
            <?php
            /* Loop through and list the pings. Use the same callback function as
             * listing comments above, graphene_comment() to format the pings.
             */
             $args = array( 'callback' => 'graphene_comment', 'style' => 'ol', 'type' => 'pings', 'per_page' => 0 );
             wp_list_comments( apply_filters( 'graphene_pings_list_args', $args ) ); ?>
        </ol>
    <?php endif; ?>
    
    <?php do_action( 'graphene_after_comments' ); ?>
</div>
<?php endif; // Ends the comment listing ?>


<?php /* Display comments disabled message if there's already comments, but commenting is disabled */ ?>
<?php if ( ! comments_open() && have_comments() ) : ?>
	<div id="respond">
		<h3 id="reply-title"><?php _e( 'Comments have been disabled.', 'graphene' ); ?></h3>
        <?php do_action( 'graphene_comments_disabled' ); ?>
    </div>
<?php endif; ?>


<?php /* Display the comment form if comment is open */ ?>
<?php if ( comments_open() ) : ?>

	<div id="comment-form-wrap" class="clearfix">
		<?php do_action( 'graphene_before_commentform' );
        
        /* Get the comment form. */ 
        
        $allowedtags = '';
		if ( ! $graphene_settings['hide_allowedtags'] ){
			$allowedtags .= '<p class="form-allowed-tags">';
            $allowedtags .= sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'graphene' ), '<code>' . allowed_tags() . '</code>' );
			$allowedtags .=	'</p>';
		}
        
        $args = array(
                    'comment_notes_after'  => $allowedtags,
                    'id_form'              => 'commentform',
                    'label_submit'         => __( 'Submit Comment', 'graphene' ),
                     );
        comment_form( apply_filters( 'graphene_comment_form_args', $args ) ); 
    
        do_action( 'graphene_after_commentform' );  ?>
	</div>
    
<?php endif; // Ends the comment status ?>