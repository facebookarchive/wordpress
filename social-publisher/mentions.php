<?php
/**
 * Output mentions above, below, or above and below the content
 *
 * @since 1.0
 */
function facebook_social_publisher_mentioning_output( $content ) {
	global $post;

	if ( ! isset( $post ) )
		return $content;

	$anchor_extras = '';
	$anchor_target = apply_filters( 'facebook_anchor_target', '_blank', 'mentions' );
	if ( $anchor_target && in_array( $anchor_target, array( '_blank', '_self', '_parent', '_top' ), true ) )
		$anchor_extras = ' target="' . $anchor_target . '"';
	unset( $anchor_target );

	$mentions = array();

	$img_args = array( 'width' => 16, 'height' => 16 );
	if ( is_ssl() )
		$img_args['return_ssl_resources'] = 1;

	$fb_mentioned_friends = get_post_meta( $post->ID, 'fb_mentioned_friends', true );
	if ( ! empty( $fb_mentioned_friends ) ) {
		foreach( $fb_mentioned_friends as $fb_mentioned_friend ) {
			$mentions[] = '<a class="fb-mentions-profile" href="' . esc_url( 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $fb_mentioned_friend['id'] ) ), array( 'http', 'https' ) ) . '" title="' . esc_attr( sprintf( __( '%s Facebook profile', 'facebook' ), $fb_mentioned_friend['name'] ) ) .'"' . $anchor_extras . '><img src="' . esc_url( 'http' . ( isset( $img_args['return_ssl_resources'] ) ? 's' : '' ) . '://graph.facebook.com/' . esc_attr($fb_mentioned_friend['id']) . '/picture?' . http_build_query( $img_args ) ) . '" width="16" height="16"> ' . esc_html( $fb_mentioned_friend['name'] ) . '</a>';
		}
	}

	$fb_mentioned_pages	 = get_post_meta( $post->ID, 'fb_mentioned_pages', true );
	if ( ! empty($fb_mentioned_pages) ) {
		foreach( $fb_mentioned_pages as $fb_mentioned_page ) {
			$mentions[] = '<a class="fb-mentions-page" href="' . esc_url( 'https://www.facebook.com/' . $fb_mentioned_page['id'], array( 'http', 'https' ) ) . '" title="' . esc_attr( sprintf( __( '%s on Facebook.', 'facebook' ), $fb_mentioned_page['name'] ) ) . '"' . $anchor_extras . '><img src="' . esc_url( 'http' . ( isset( $img_args['return_ssl_resources'] ) ? 's' : '' ) . '://graph.facebook.com/' . esc_attr($fb_mentioned_page['id']) . '/picture?' . http_build_query( $img_args ), array( 'http', 'https' ) ) . '" width="16" height="16"> ' . esc_html( $fb_mentioned_page['name'] ) . '</a>';
		}
	}

	if ( ! empty( $mentions ) ) {
		$options = get_option( 'facebook_mentions' );
		$position = 'both';
		if ( is_array( $options ) && isset( $options['position'] ) )
			$position = $options['position'];

		$mentions_count = count( $mentions );
		$mentions_str = "\n" . '<div class="' . implode( ' ', apply_filters( 'facebook_mentions_classes', array( 'fb-mentions', 'entry-meta' ) ) ) . '">' . esc_html( sprintf( _n( '%u mention', '%u mentions', $mentions_count, 'facebook' ), $mentions_count ) ) . ': ' . implode( ' ', $mentions ) . '</div>' . "\n";

		switch ( $position ) {
			case 'top':
				return $mentions_str . $content;
				break;
			case 'bottom':
				return $content . $mentions_str;
				break;
			case 'both':
				return $mentions_str . $content . $mentions_str;
				break;
			default:
				break;
		}
	}

	return $content;
}
?>