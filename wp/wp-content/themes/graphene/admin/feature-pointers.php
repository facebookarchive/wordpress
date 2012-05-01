<?php
/* 
 * Add admin feature pointers
*/
function graphene_feature_pointers() {
	if ( ! strpos( $_SERVER["REQUEST_URI"], 'page=graphene_options' ) ) return;
	
    $pointer_content = '<h3>' . esc_js( __( 'Where are all the options?!', 'graphene' ) ) . '</h3>';
    $pointer_content .= '<p>' . esc_js( __( "We've decided to clean things up!", 'graphene' ) ) . '</p>';
	$pointer_content .= '<p>' . esc_js( __( "We know how too many options can really be daunting to new users, so we've hidden them.", 'graphene' ) ) . '</p>';
	$pointer_content .= '<p>' . esc_js( __( "But no worries! If you're a seasoned user of the Graphene theme, or whenever you feel ready to further customise your site, just click on the \"Show all options\" link, and they will magically appear to you.", 'graphene' ) ) . '</p>';
	?>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		var pointer_hide = grapheneGetCookie('graphene-p0');
		if (pointer_hide != 'true' ){
			$('.toggle-options-wrapper').pointer({
				content: '<?php echo $pointer_content; ?>',
				position: 'top',
				close: function() {
					grapheneSetCookie('graphene-p0', true, 100);
				}
			}).pointer('open');
			$('.appearance_page_graphene_options #wp-pointer-0').css('margin-left', '-95px');
		}
	});
	//]]>
	</script>
	<?php
}
add_action( 'admin_footer', 'graphene_feature_pointers' );
?>