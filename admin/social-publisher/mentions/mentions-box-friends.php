<?php

/**
 * Associate Facebook Pages with a post
 *
 * @since 1.1
 */
class Facebook_Mentions_Box_Friends {
	/**
	 * Nonce reference
	 *
	 * @since 1.1
	 * @var string
	 */
	const NONCE_NAME = 'facebook_friend_mention_box_noncename';

	/**
	 * Post meta key for the mentions
	 *
	 * @since 1.1
	 * @var string
	 */
	const POST_META_KEY_MENTIONS = 'fb_mentioned_friends';

	/**
	 * Post meta key for the mentions
	 *
	 * @since 1.1
	 * @var string
	 */
	const POST_META_KEY_MESSAGE = 'fb_mentioned_friends_message';

	/**
	 * Form field name for the autocomplete input
	 *
	 * @since 1.1
	 * @var string
	 */
	const FIELD_AUTOCOMPLETE = 'facebook_friend_mention_box_autocomplete';

	/**
	 * Form field name for the message field
	 *
	 * @since 1.1
	 * @var string
	 */
	const FIELD_MESSAGE = 'facebook_friend_mention_box_message';

	/**
	 * Add a meta box to the specified post type edit page
	 *
	 * @since 1.1
	 * @param string $post_type page, post, custom post type, etc.
	 */
	public static function add_meta_box( $post_type ) {
		add_meta_box(
			'facebook-friend-mention-box-id',
			__( 'Mention Facebook Friends', 'facebook' ),
			array( 'Facebook_Mentions_Box_Friends', 'content' ),
			$post_type,
			'side'
		);
	}

	/**
	 * Output inner content of the meta box
	 *
	 * @since 1.1
	 * @param stdClass $post current post
	 */
	public static function content( $post ) {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), self::NONCE_NAME );

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/facebook-user.php' );

		$facebook_user_permissions = Facebook_User::get_permissions();

		if ( is_array( $facebook_user_permissions ) && ! empty( $facebook_user_permissions ) && isset( $facebook_user_permissions['publish_actions'] ) && isset( $facebook_user_permissions['publish_stream'] ) ) {

			$saved_mentions = get_post_meta( $post->ID, self::POST_META_KEY_MENTIONS, true );
			echo '<label for="suggest-friends">' . esc_html( __( "Friend's Name", 'facebook' ) ) . '</label>';
			echo '<input type="text" class="widefat" id="suggest-friends" autocomplete="off" name="' . self::FIELD_AUTOCOMPLETE . '" size="44" placeholder="' . esc_attr( __( 'Type to find a friend.', 'facebook' ) ) . '"';

			// add a value attribute just in case noscript. tokeninput should handle this in JS case
			if ( is_array( $saved_mentions ) ) {
				$mentions = array();
				foreach( $saved_mentions as $mention ) {
					if ( ! ( isset( $mention['id'] ) && isset( $mention['name'] ) ) )
						continue;
					$mentions[] = '[' . esc_attr( $mention['id'] ) . '|' . esc_attr( $mention['name'] ) . ']';
				}
				if ( ! empty( $mentions ) )
					echo ' value="' . implode( ',', $mentions ) . '"';
				unset( $mentions );
			}
			echo ' />';
			echo '<script type="text/javascript">jQuery("#suggest-friends").on("facebook-friends-mentions-onload",function(){';
			if ( is_array( $saved_mentions ) )
				echo 'FB_WP.admin.mentions.friend_suggest.stored_values=' . json_encode( $saved_mentions ) . ';';
			echo 'FB_WP.admin.mentions.friend_suggest.hint=' . json_encode( __( 'Type to find a friend.', 'facebook' ) ) . ';';
			echo 'FB_WP.admin.mentions.friend_suggest.noresults=' . json_encode( __( 'No friend found.', 'facebook' ) ) . ';';
			echo '});</script>';
			unset( $saved_mentions );

			$saved_message = get_post_meta( $post->ID, self::POST_META_KEY_MESSAGE, true );
			echo '<label for="friends-mention-message">' . esc_html( __( 'Message', 'facebook' ) ) . '</label> ';
			echo '<input type="text" class="widefat" id="friends-mention-message" name="' . self::FIELD_MESSAGE . '" size="44" placeholder="' . esc_attr( __( 'Write something...', 'facebook' ) ) . '"';
			if ( $saved_message )
				echo ' value="' . esc_attr( $saved_message ) . '"';
			echo ' />';
			unset( $saved_message );

			$post_type = get_post_type( $post );

			echo '<p class="howto">';
			echo esc_html( sprintf( __( 'This will add the %1$s and message to the Timeline of each friend mentioned. They will also appear in the contents of the %1$s.', 'facebook' ), $post_type ) );
			echo '</p>';
		} else {
			echo '<p>' . esc_html( __( 'Facebook social publishing is enabled.', 'facebook' ) ) . '</p>';
			echo '<p>' . sprintf( esc_html( __( '%1$s to get full functionality including mentioning %2$s.', 'facebook') ), '<span class="facebook-login" data-scope="page" style="font-weight:bold">' . esc_html( __( 'Link your Facebook account to your WordPress account', 'facebook' ) ) . '</span>', esc_html( __( 'Facebook friends', 'facebook' ) ) ) . '</p>';
		}
	}

	/**
	 * Save submitted post after create / update
	 *
	 * @since 1.1
	 * @param int $post_id post identifier
	 */
	public static function save( $post_id ) {
		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/facebook-user.php' );

		$facebook_user = Facebook_User::get_current_user();

		if ( ! $facebook_user )
			return;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! isset( $_POST[self::FIELD_AUTOCOMPLETE] ) || empty( $_POST[self::NONCE_NAME] ) || ! wp_verify_nonce( $_POST[self::NONCE_NAME], plugin_basename( __FILE__ ) ) )
			return;


		// Check permissions
		$post_type = get_post_type( $post_id );
		if ( ! ( $post_type && post_type_supports( $post_type, 'author' ) ) )
			return;

		if ( ! class_exists( 'Facebook_Social_Publisher' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social_publisher.php' );
		$capability_singular_base = Facebook_Social_Publisher::post_type_capability_base( $post_type );
	
		if ( ! current_user_can( 'edit_' . $capability_singular_base, $post_id ) )
			return;

		// process data then save it
		preg_match_all(
			'/\[(\d*?)\|(.*?)\]/su',
			$_POST[self::FIELD_AUTOCOMPLETE],
			$friend_details,
			PREG_SET_ORDER
		);

		$friends_details_meta = array();
		foreach ( $friend_details as $friend_detail ) {
			$friends_details_meta[] = array(
				'id' => $friend_detail[1],
				'name' => sanitize_text_field( $friend_detail[2] )
			);
		}

		if ( ! empty( $friends_details_meta ) )
			update_post_meta( $post_id, self::POST_META_KEY_MENTIONS, $friends_details_meta );

		$message = trim( sanitize_text_field( $_POST[self::FIELD_MESSAGE] ) );
		if ( $message )
			update_post_meta( $post_id, self::POST_META_KEY_MESSAGE, $message );
	}
}

?>