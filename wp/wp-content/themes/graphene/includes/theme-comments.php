<?php

/**
 * Defines the callback function for use with wp_list_comments(). This function controls
 * how comments are displayed.
*/

if (!function_exists( 'graphene_comment' ) ) :

	function graphene_comment( $comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'clearfix' ); ?>>
				<?php do_action( 'graphene_before_comment' ); ?>
                
                <?php /* Added support for comment numbering using Greg's Threaded Comment Numbering plugin */ ?>
                <?php if (function_exists( 'gtcn_comment_numbering' ) ) {gtcn_comment_numbering( $comment->comment_ID, $args);} ?>
                
					<div class="comment-wrap clearfix">
                    	
                        <?php if ( $avatar = get_avatar( $comment, apply_filters( 'graphene_gravatar_size', 40) ) ) : ?>
                            <div class="comment-avatar-wrap">
                                <?php echo $avatar; ?>
                                <?php do_action( 'graphene_comment_gravatar' ); ?>
                            </div>
                        <?php endif; ?>
                        
						<h5 class="comment-author">
                        	<cite><?php comment_author_link(); ?></cite>
	                        <?php do_action( 'graphene_comment_author' ); ?>
                        </h5>
						<div class="comment-meta">
							<p class="commentmetadata">
                            	<?php /* translators: %1$s is the comment date, %2$s is the comment time */ ?>
								<?php printf( __( '%1$s at %2$s', 'graphene' ), get_comment_date(), get_comment_time() ); ?>
								<span class="timezone"><?php echo '(UTC '.get_option( 'gmt_offset' ).')'; ?></span>
                                <?php edit_comment_link(__( 'Edit comment','graphene' ),' (',') ' ); ?>
                                <span class="comment-permalink"><a href="<?php echo get_comment_link(); ?>"><?php _e( 'Link to this comment', 'graphene' ); ?></a></span>
                            	<?php do_action( 'graphene_comment_metadata' ); ?>    
                            </p>
							<p class="comment-reply-link">
								<?php comment_reply_link(array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => __( 'Reply', 'graphene' ) )); ?>
                            
                            	<?php do_action( 'graphene_comment_replylink' ); ?>
                            </p>
                            
							<?php do_action( 'graphene_comment_meta' ); ?>
						</div>
						<div class="comment-entry">
                        	<?php do_action( 'graphene_before_commententry' ); ?>
                            
							<?php if ( $comment->comment_approved == '0' ) : ?>
							   <p><em><?php _e( 'Your comment is awaiting moderation.', 'graphene' ) ?></em></p>
                               <?php do_action( 'graphene_comment_moderation' ); ?>
							<?php else : ?>
								<?php comment_text(); ?>
                            <?php endif; ?>
                            
                            <?php do_action( 'graphene_after_commententry' ); ?>
						</div>
					</div>
                
                <?php do_action( 'graphene_after_comment' ); ?>
	<?php

	}

endif;


/**
 * Customise the comment form
*/
function graphene_comment_form_fields(){
	
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? ' aria-required="true"' : '' );
	$req_mark = ( $req ? ' <span class="required">*</span>' : '' );
	$commenter = wp_get_current_commenter();
	
	$fields =  array( 
		'author' => 
					'<p class="comment-form-author clearfix">
						<label for="author" class="graphene_form_label">' . __( 'Name:', 'graphene' ) . $req_mark . '</label>
						<input id="author" name="author" type="text" class="graphene-form-field"' . $aria_req . ' value="' . esc_attr( $commenter['comment_author'] ) . '" />
					</p>',
		'email'  => 
					'<p class="comment-form-email clearfix">
						<label for="email" class="graphene_form_label">' . __( 'Email:', 'graphene' ) . $req_mark . '</label>
						<input id="email" name="email" type="text" class="graphene-form-field"' . $aria_req . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" />
					</p>',
		'url'    => 
					'<p class="comment-form-url clearfix">
						<label for="url" class="graphene_form_label">' . __( 'Website:', 'graphene' ) . ' </label>
						<input id="url" name="url" type="text" class="graphene-form-field" value="' . esc_attr( $commenter['comment_author_url'] ) . '" />
					</p>',
	);
	
	$fields = apply_filters( 'graphene_comment_form_fields', $fields );
	
	return $fields;
}

// The comment field textarea
function graphene_comment_textarea(){
	$html =  
		'<p class="comment-form-message clearfix">
			<label class="graphene_form_label">' . __( 'Message:', 'graphene' ) . ' <span class="required">*</span></label>
			<textarea name="comment" id="comment" cols="40" rows="10" class="graphene-form-field" aria-required="true"></textarea>
		 </p>';
	echo apply_filters( 'graphene_comment_textarea', $html );
	
	do_action( 'graphene_comment_textarea' );
}
	
// Clear
function  graphene_comment_clear(){
	echo '<div class="clear"></div>';
}

// Add all the filters we defined
add_filter( 'comment_form_default_fields', 'graphene_comment_form_fields' );
add_filter( 'comment_form_field_comment', 'graphene_comment_textarea' );
add_filter( 'comment_form', 'graphene_comment_clear', 1000 );


/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 * 
 * In Graphene version 1.3 the $noneText param has been removed
 *
 * @package Graphene
 * @since Graphene 1.3
*/
function graphene_comment_count( $type = 'comments', $oneText = '', $moreText = '' ){
	
	$result = graphene_get_comment_count( $type );

	//if( $result == 0):
	//	echo str_replace( '%', $result, $noneText);
    if( $result == 1) : 
		return str_replace( '%', $result, $oneText);
	elseif( $result > 1) : 
		return str_replace( '%', $result, $moreText);
	else :
		return false;
	endif;
}


/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks. Return the number of comments, but do not print them.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 * 
 * In Graphene version 1.3 the $noneText param has been removed
 *
 * @package Graphene
 * @since Graphene 1.3
*/
function graphene_get_comment_count( $type = 'comments', $only_approved_comments = true ){
	if( $type == 'comments' ) :
		$typeSql = 'comment_type = ""';
	elseif( $type == 'pings' ) :
		$typeSql = 'comment_type != ""';
	elseif( $type == 'trackbacks' ) :
		$typeSql = 'comment_type = "trackback"';
	elseif( $type == 'pingbacks' ) :
		$typeSql = 'comment_type = "pingback"';
	endif;
	
	$typeSql = apply_filters( 'graphene_comments_typesql', $typeSql, $type );
        $approvedSql = $only_approved_comments ? ' AND comment_approved="1"' : '';
        
	global $wpdb;

    $result = $wpdb->get_var( '
        SELECT
            COUNT(comment_ID)
        FROM
            '.$wpdb->comments.'
        WHERE
            '.$typeSql.$approvedSql.' AND           
            comment_post_ID= '.get_the_ID() );
	
	return $result;
}


/**
 * Custom jQuery script for the comments/pings tabs
*/
function graphene_tabs_js(){ 
	global $tabbed;
	if ( $tabbed ) :
?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function( $){
			$(function(){
				// to allow the user to switch tabs
				$("div#comments h4.comments a").click(function(){
					$("div#comments .comments").addClass( 'current' );
					$("div#comments .pings").removeClass( 'current' );
					$("div#comments #pings_list").hide();
					$("div#comments #comments_list").fadeIn(300);
					return false;
				});
				$("div#comments h4.pings a").click(function(){
					$("div#comments .pings").addClass( 'current' );
					$("div#comments .comments").removeClass( 'current' );
					$("div#comments #comments_list").hide();
					$("div#comments #pings_list").fadeIn(300);
					return false;
				});
			});
		});
		//]]>
	</script>
<?php
	endif;
}
add_action( 'wp_footer', 'graphene_tabs_js' );


/**
 * Helps to determine if the comments should be shown.
 */
if ( ! function_exists( 'graphene_should_show_comments' ) ) :

function graphene_should_show_comments() {
    global $graphene_settings, $post;
    
	if ( $graphene_settings['comments_setting'] == 'disabled_completely' )
        return false;
    
	if ( $graphene_settings['comments_setting'] == 'disabled_pages' && get_post_type( $post->ID) == 'page' )
        return false;
	
	if ( ! is_singular() && $graphene_settings['hide_post_commentcount'] )
		return false;
	
	if ( ! comments_open() && have_comments() && ! is_singular() )
		return false;
	
    return true;
}

endif;
?>