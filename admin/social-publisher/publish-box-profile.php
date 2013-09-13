<?php

/**
 * Add a custom message to your article posted to a Facebook profile
 *
 * @since 1.1
 */
class Facebook_Social_Publisher_Meta_Box_Profile {

	/**
	 * Check page origin before saving.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const NONCE_NAME = 'facebook_profile_meta_box_noncename';

	/**
	 * Post meta key for the message.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const POST_META_KEY_MESSAGE = 'fb_author_message';

	/**
	 * Post meta key for post to Facebook feature enabled / disabled.
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	const POST_META_KEY_FEATURE_ENABLED = 'post_to_facebook_timeline';

	/**
	 * Form field name for author profile message.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const FIELD_MESSAGE = 'facebook_author_message_box_message';

	/**
	 * Form field name for feature enabled or disabled.
	 *
	 * @since 1.2
	 *
	 * @var string
	 */
	const FIELD_FEATURE_ENABLED = 'facebook_author_enabled';

	/**
	 * Add a meta box to the post editor.
	 *
	 * @since 1.1
	 *
	 * @uses add_meta_box()
	 * @param string $post_type target page post type
	 * @return void
	 */
	public static function add_meta_box( $post_type ) {
		add_meta_box(
			'facebook-author-message-box-id',
			__( 'Facebook Status on Your Timeline', 'facebook' ),
			array( 'Facebook_Social_Publisher_Meta_Box_Profile', 'content' ),
			$post_type
		);

		if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/settings-social-publisher.php' );

		// only load mentions-specific features if Facebook app configuration supports tags
		if ( get_option( Facebook_Social_Publisher_Settings::OPTION_OG_ACTION ) )
			add_action( 'admin_enqueue_scripts', array( 'Facebook_Social_Publisher_Meta_Box_Profile', 'enqueue_scripts' ) );
	}

	/**
	 * Load mentions typeahead JavaScript and jQuery UI requirements.
	 *
	 * @since 1.2
	 *
	 * @global \Facebook_Loader $facebook_loader reference plugin directory
	 * @uses wp_enqueue_script()
	 * @return void
	 */
	public static function enqueue_scripts( ) {
		global $facebook_loader;

		$suffix = '.min';
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG )
			$suffix = '';

		wp_enqueue_script( 'facebook-mentions', plugins_url( 'static/js/admin/mentions' . $suffix . '.js', $facebook_loader->plugin_directory . 'facebook.php' ), array( 'jquery-ui-autocomplete' ), '1.3', true );
		wp_enqueue_style( 'facebook-mentions', plugins_url( 'static/css/admin/mentions' . $suffix . '.css', $facebook_loader->plugin_directory . 'facebook.php' ), array(), '1.3' );
	}

	/**
	 * Add content to the profile publisher meta box
	 *
	 * @since 1.0
	 *
	 * @global WP_Locale $wp_locale display Like counts in the number format of the current locale
	 * @param stdClass $post current post
	 * @return void
	 */
	public static function content( $post ) {
		global $wp_locale;

		if ( ! isset( $post->ID ) )
			return;

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), self::NONCE_NAME );

		$feature_enabled = true;
		if ( get_post_meta( $post->ID, self::POST_META_KEY_FEATURE_ENABLED, true ) === '0' )
			$feature_enabled = false;
		echo '<div><p><input class="checkbox" type="checkbox" id="facebook-author-enabled" name="' . self::FIELD_FEATURE_ENABLED . '" value="1"';
		checked( $feature_enabled );
		echo ' /> <label for="facebook-author-enabled">' . esc_html( __( 'Post to Facebook Timeline', 'facebook' ) ) . '</label></p></div>';

		$field_message_id = 'facebook-timeline-mention-message';
		echo '<div id="facebook-timeline-mention-message-container"><input type="text" class="widefat" id="' . $field_message_id . '" name="' . self::FIELD_MESSAGE . '" size="44" placeholder="' . esc_attr( __( 'Summarize the post for your Facebook audience', 'facebook' ) ) . '"';
		$stored_message = get_post_meta( $post->ID, self::POST_META_KEY_MESSAGE, true );
		if ( $stored_message )
			echo ' value="' . esc_attr( $stored_message ) . '"';

		echo ' /><p class="howto"><label for="' . $field_message_id . '">'. esc_html( __( 'This message will show as part of the story on your Facebook Timeline.', 'facebook' ) ) .'</label></p>';

		if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/settings-social-publisher.php' );
		if ( get_option( Facebook_Social_Publisher_Settings::OPTION_OG_ACTION ) ) {
			// set JavaScript properties for localized text
			echo '<script type="text/javascript">jQuery("#' . $field_message_id . '").on("facebook-mentions-onload",function(){';
			echo 'FB_WP.admin.mentions.autocomplete_nonce=' . json_encode( wp_create_nonce( 'facebook_autocomplete_nonce' ) ) . ';';
			if ( isset( $wp_locale ) )
				echo 'FB_WP.admin.mentions.thousands_separator=' . json_encode( $wp_locale->number_format['thousands_sep'] ) . ';';
			echo 'FB_WP.admin.mentions.messages.likes=' . json_encode( _x( '%s like this', 'number of people who Like a Page', 'facebook' ) ) . ';';
			echo 'FB_WP.admin.mentions.messages.talking_about=' . json_encode( _x( '%s talking about this', 'number of people talking about a Page', 'facebook' ) ) . ';';
			echo '});';
			echo '</script>';
		}
		echo '</div>';
	}

	/**
	 * Save the custom Status, used when posting to a User's Timeline.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id WordPress post identifier
	 * @global void
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
		if ( ! ( $post_type && post_type_supports( $post_type, 'author' ) ) )
			return;

		if ( ! class_exists( 'Facebook_Social_Publisher' ) )
			require_once( dirname(__FILE__) . '/social_publisher.php' );
		$capability_singular_base = Facebook_Social_Publisher::post_type_capability_base( $post_type );

		if ( ! current_user_can( 'edit_' . $capability_singular_base, $post_id ) )
			return;

		$feature_enabled = '1';
		if ( ! isset( $_POST[self::FIELD_FEATURE_ENABLED] ) || $_POST[self::FIELD_FEATURE_ENABLED] === '0' )
			$feature_enabled = '0';

		update_post_meta( $post_id, self::POST_META_KEY_FEATURE_ENABLED, $feature_enabled );
		unset( $feature_enabled );

		$message = trim( sanitize_text_field( $_POST[self::FIELD_MESSAGE] ) );
		if ( $message )
			update_post_meta( $post_id, self::POST_META_KEY_MESSAGE, $message );
	}
}

?>