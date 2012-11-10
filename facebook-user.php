<?php

/**
 * Functions related to a Facebook user in the WordPress user system
 *
 * @since 1.1
 */
class Facebook_User {

	/**
	 * Extend our access token usage time
	 *
	 * @since 1.0
	 */
	public static function extend_access_token() {
		global $facebook;

		if ( isset( $facebook ) )
			$facebook->setExtendedAccessToken();
	}

	/**
	 * Gets and returns the current, active Facebook user
	 *
	 * @since 1.0
	 */
	public static function get_current_user( $fields = array() ) {
		global $facebook;

		if ( ! isset( $facebook ) )
			return;

		$params = array( 'ref' => 'fbwpp' );
		if ( is_array( $fields ) )
			$params['fields'] = implode( ',', $fields );

		try {
			return $facebook->api( '/me', 'GET', $params );
		} catch ( WP_FacebookApiException $e ) {}
	}

	/**
	 * Allow WordPress publishers to interrupt the get_user_meta process
	 *
	 * @since 1.1
	 * @uses get_user_meta()
	 * @link http://codex.wordpress.org/Function_Reference/get_user_meta
	 *
	 * @param int $user_id Post ID.
	 * @param string $key Optional. The meta key to retrieve. By default, returns data for all keys.
	 * @param bool $single Whether to return a single value.
	 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
	 */
	public static function get_user_meta( $user_id, $meta_key, $single = false ) {
		$override = apply_filters( 'fb_get_user_meta', false, $user_id, $meta_key, $single );
		if ( false !== $override )
			return $override;

		return get_user_meta( $user_id, $meta_key, $single );
	}

	/**
	 * Allow WordPress publishers to interrupt the update_user_meta process
	 * Improves cache integration for some publishers
	 *
	 * @since 1.1
	 * @uses update_user_meta()
	 * @link http://codex.wordpress.org/Function_Reference/update_user_meta
	 *
	 * @param int $user_id Post ID.
	 * @param string $meta_key Metadata key.
	 * @param mixed $meta_value Metadata value.
	 * @param mixed $prev_value Optional. Previous value to check before removing.
	 * @return bool False on failure, true if success.
	 */
	public static function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
		$override = apply_filters( 'fb_update_user_meta', false, $user_id, $meta_key, $meta_value, $prev_value );
		if ( false !== $override )
			return $override;

		return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Allow WordPress publishers to interrupt the delete_user_meta process
	 * Improves cache integration for some publishers
	 *
	 * @since 1.1
	 * @uses delete_user_meta()
	 * @link http://codex.wordpress.org/Function_Reference/delete_user_meta
	 *
	 * @param int $user_id user ID
	 * @param string $meta_key Metadata name.
	 * @param mixed $meta_value Optional. Metadata value.
	 * @return bool False for failure. True for success.
	 */
	public static function delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {
		$override = apply_filters( 'fb_delete_user_meta', false, $user_id, $meta_key, $meta_value );
		if ( false !== $override )
			return $override;

		return delete_user_meta( $user_id, $meta_key, $meta_value );
	}

	/**
	 * Check permissions granted for the current user and site application
	 *
	 * @since 1.1
	 * @return array permissions array returned by Facebook Graph API
	 */
	public static function get_permissions() {
		global $facebook;

		if ( ! isset( $facebook ) )
			return array();

		$current_user = wp_get_current_user();

		$facebook_user_data = self::get_user_meta( $current_user->ID, 'fb_data', true );
		if ( ! ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) )
			return array();

		$permissions = $facebook->get_current_user_permissions();
		if ( is_array( $permissions ) && ! empty( $permissions ) )
			return $permissions;

		return array();
	}

	/**
	 * Does the current WordPress user have Facebook data stored?
	 * Has the current viewer authorized the current application to post on his or her behalf?
	 *
	 * @since 1.1
	 * @return bool true if Facebook information present for current user and publish permissions exist
	 */
	public static function can_publish_to_facebook() {
		global $facebook;

		if ( ! isset( $facebook ) )
			return false;

		$current_user = wp_get_current_user();

		// does the current user have associated Facebook account data stored in WordPress?
		$facebook_user_data = self::get_user_meta( $current_user->ID, 'fb_data', true );
		if ( ! ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) )
			return false;

		if ( Facebook_User::get_user_meta( $current_user->ID, 'facebook_timeline_disabled', true ) )
			return false;

		$permissions = $facebook->get_current_user_permissions( $current_user );
		if ( ! ( is_array( $permissions ) && ! empty( $permissions ) && in_array( 'publish_stream', $permissions, true ) && in_array( 'publish_actions', $permissions, true ) ) )
			return false;

		return true;
	}
}

?>
