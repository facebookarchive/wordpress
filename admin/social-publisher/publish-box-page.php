<?php

/**
 * Add a custom message to your article posted to a Facebook page
 *
 * @since 1.1
 */
class Facebook_Social_Publisher_Meta_Box_Page {
	/**
	 * Check page origin before saving
	 *
	 * @since 1.1
	 * @var string
	 */
	const NONCE_NAME = 'facebook_fan_page_message_box_noncename';

	/**
	 * Post meta key for the message
	 *
	 * @since 1.1
	 * @var string
	 */
	const POST_META_KEY = 'fb_fan_page_message';

	/**
	 * Form field name for page message
	 *
	 * @since 1.1
	 * @var string
	 */
	const FIELD_MESSAGE = 'facebook_page_message_box_message';

	/**
	 * Add a meta box to the post editor
	 *
	 * @since 1.1
	 * @param string $post_type target page post type
	 * @param array Facebook page info
	 */
	public static function add_meta_box( $post_type, $page ) {
		add_meta_box(
			'facebook-fan-page-message-box-id',
			sprintf( __( 'Facebook Status on %s Timeline', 'facebook' ), $page['name'] ),
			array( 'Facebook_Social_Publisher_Meta_Box_Page', 'content' ),
			$post_type
		);
	}

	/**
	 * Add content to the page publisher meta box
	 *
	 * @since 1.0
	 * @param stdClass $post current post
	 */
	public static function content( $post ) {
		$page = get_option( 'facebook_publish_page' );
		if ( ! ( is_array( $page ) && isset( $page['access_token'] ) && isset( $page['id'] ) && isset( $page['name'] ) ) )
			return;

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), self::NONCE_NAME );

		$stored_message = get_post_meta( $post->ID, self::POST_META_KEY, true );
		echo '<input type="text" class="widefat" id="friends-mention-message" name="' . self::FIELD_MESSAGE . '" size="44" placeholder="' . esc_attr( __( 'Summarize the post for your Facebook audience', 'facebook' ) ) . '"';
		if ( $stored_message )
			echo ' value="' . esc_attr( $stored_message ) . '"';
		echo ' /><p class="howto">' . esc_html( sprintf( __( 'This message will show as part of the story on the %s Timeline.', 'facebook'), $page['name'] ) ) . '</p>';
	}

	/**
	 * Save the custom Status, used when posting to an Fan Page's Timeline
	 *
	 * @since 1.0
	 * @param int $post_id post identifier
	 */
	public static function save( $post_id ) {
		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( ! isset( $_POST[self::FIELD_MESSAGE] ) || empty( $_POST[self::NONCE_NAME] ) || ! wp_verify_nonce( $_POST[self::NONCE_NAME], plugin_basename( __FILE__ ) ) )
			return;

		// Check permissions
		$post_type = get_post_type( $post_id );
		if ( ! $post_type )
			return;

		if ( ! class_exists( 'Facebook_Social_Publisher' ) )
			require_once( dirname(__FILE__) . '/social_publisher.php' );
		$capability_singular_base = Facebook_Social_Publisher::post_type_capability_base( $post_type );
	
		if ( ! current_user_can( 'edit_' . $capability_singular_base, $post_id ) )
			return;

		$message = trim( sanitize_text_field( $_POST[self::FIELD_MESSAGE] ) );
		if ( $message )
			update_post_meta( $post_id, self::POST_META_KEY, $message );
	}
}

?>