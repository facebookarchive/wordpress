<div id="author-<?php the_author_meta( 'ID' ); ?>" <?php post_class(array( 'clearfix', 'page', 'author' ) ); ?>>

    <div class="entry author-entry clearfix">
    
    	<?php do_action( 'graphene_author_entry' ); ?>
    
    	<?php /* Display the user's profile image */ ?>
        <?php 
			if (get_the_author_meta( 'graphene_author_imgurl' ) ) {
				echo '<img class="avatar" src="'.get_the_author_meta( 'graphene_author_imgurl' ).'" alt="" />';
			} else {
				echo get_avatar( get_the_author_meta( 'user_email' ), graphene_grid_width( 30, 2 ) ); 
			}
		?>
            
        <?php /* Page title, which is the user's nicename */ ?>
        <h2 <?php graphene_grid( 'post-title', 13, 8, 5 ); ?>><?php echo ucfirst(get_the_author_meta( 'display_name' ) ); ?></h2>
                            
        <?php /* Post content */ ?>
        <div <?php graphene_grid( 'entry-content clearfix', 13, 8, 5 ); ?>>
        
        	<?php /* Author's details */ ?>
            <h4 class="author-first-heading"><?php _e("Author's details", 'graphene' ); ?></h4>
            <p>
				<?php if ( get_the_author_meta( 'user_firstname' ) != '' || get_the_author_meta( 'user_lastname' ) != '' ) { /* translators: %1$s is the first name, %2$s is the last name */ printf(__( '<strong>Name:</strong> %1$s %2$s', 'graphene' ), get_the_author_meta( 'user_firstname' ), get_the_author_meta( 'user_lastname' ) ); echo '<br />';} ?>
                <?php printf(__( '<strong>Date registered:</strong> %1$s','graphene' ),mysql2date(get_option( 'date_format' ), get_the_author_meta( 'user_registered' ) ) ); ?>
                <?php if ( get_the_author_meta( 'user_url' ) != '' ) {echo '<br />';printf( '<strong>URL:</strong> %1$s', '<a href="'.get_the_author_meta( 'user_url' ).'">'.get_the_author_meta( 'user_url' ).'</a>' );} ?>
                <?php if ( get_the_author_meta( 'aim' ) != '' ) {echo '<br />';printf( '<strong>AIM:</strong> %1$s', get_the_author_meta( 'aim' ) );} ?>
                <?php if ( get_the_author_meta( 'jabber' ) != '' ) {echo '<br />';printf( '<strong>Jabber / Google Talk:</strong> %1$s', get_the_author_meta( 'jabber' ) );} ?>
                <?php if ( get_the_author_meta( 'yim' ) != '' ) {echo '<br />';printf( '<strong>Yahoo! IM:</strong> %1$s', get_the_author_meta( 'yim' ) );} ?>
			</p>
            <?php do_action( 'graphene_author_details' ); ?>
            
			<?php if (get_the_author_meta( 'description' ) != '' ) : ?>
            <h4><?php _e( 'Biography', 'graphene' ); ?></h4>
			<p><?php the_author_meta( 'description' ); ?></p>
            
            <?php do_action( 'graphene_author_desc' ); ?>
            <?php endif; ?>
         
            
            <?php /* Lists the author's latest posts */ ?>
            <?php 
			$args = array(
						'posts_per_page' 	=> 5,
						'author' 			=> get_the_author_meta( 'ID' ),
						'orderby'			=> 'date',
						'suppress_filters'	=> 0,
						);
                        $postsQuery = new WP_Query( apply_filters( 'graphene_author_latest_posts_args', $args ) );
                        if ( $postsQuery->have_posts() ) : ?>            
            <h4><?php _e( 'Latest posts', 'graphene' ); ?></h4>
            <ol>	
            <?php while ( $postsQuery->have_posts() ) : $postsQuery->the_post(); ?>
                <li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink Link to %s', 'graphene' ), the_title_attribute( 'echo=0' ) ); ?>"><?php if ( get_the_title() == '' ) {_e( '(No title)','graphene' );} else {the_title();} ?></a> &mdash; <?php echo get_the_date(); ?></li>    
            <?php endwhile;  ?>
            </ol>
            <?php do_action( 'graphene_author_latestposts' ); ?>
            
            <?php endif; wp_reset_postdata(); ?>
            
            
            <?php /* Lists the author's most commented posts */ ?>
            <?php 
			$args = array(
						'posts_per_page' => 5,
						'author' => get_the_author_meta( 'ID' ),
						'orderby' => 'comment_count',
						'suppress_filters' => 0,
						);
			$postsQuery = new WP_Query(apply_filters( 'graphene_author_popular_posts_args', $args) ); 
			
			// Check if at least one of the author's post has comments
			$have_comments = NULL;
                        $comments_ol_html = '';
                        while ( $postsQuery->have_posts() ){
                            $postsQuery->the_post();
                            setup_postdata( $post);
                            $nr_comments = get_comments_number();
                            /* List the post only if comment is open 
                             * and there's comment(s) 
                             * and the post is not password-protected */
                            if (comments_open() && empty( $post->post_password) && $nr_comments != 0){
                                $have_comments = TRUE;
                                $comments_ol_html .= '<li><a href="'. get_permalink() .'" rel="bookmark" title="'. sprintf(esc_attr__( 'Permalink Link to %s', 'graphene' ), the_title_attribute( 'echo=0' ) ) .'">'. ( get_the_title() == '' ? __( '(No title)','graphene' ) : get_the_title() ) . '</a> &mdash; '. ( sprintf( _n( '1 comment', '%d comments', $nr_comments, 'graphene' ), number_format_i18n( $nr_comments ) ) ). '</li>';
                            }
                        }
			
			if ( $have_comments) :
			?>
                
            <h4><?php _e( 'Most commented posts', 'graphene' ); ?></h4>
            <ol>
            <?php echo $comments_ol_html; ?>
            </ol>
            <?php do_action( 'graphene_author_popularposts' ); ?>
            
            <?php endif; wp_reset_postdata(); ?>
            
        </div>
        
        <?php /* Post footer */ ?>
        <div class="entry-footer clearfix">                        
            <?php 
                /**
                 * Display AddThis social sharing button if enabled.
                 * See the graphene_addthis() function in functions.php
                */ 
            ?>
            <?php graphene_addthis( get_the_ID() ); ?>
        </div>
    </div>
</div>	