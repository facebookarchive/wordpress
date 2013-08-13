<?php

/**
 * Functions related to a Facebook user in the WordPress user system
 *
 * @since 1.1
 */
class Facebook_User {

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
	 * Extend our access token usage time
	 *
	 * @since 1.0
	 */
	public static function extend_access_token() {
		global $facebook, $facebook_loader;

		if ( isset( $facebook ) || ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
			$facebook->setExtendedAccessToken();
	}

	/**
	 * Does the current WordPress user have Facebook data stored?
	 * Has the current viewer authorized the current application to post on his or her behalf?
	 *
	 * @since 1.1
	 * @param int $wp_user_id WordPress user identifier
	 * @return bool true if Facebook information present for current user and publish permissions exist
	 */
	public static function can_publish_to_facebook( $wordpress_user_id = 0, $check_publish_override = true ) {
		if ( ! $wordpress_user_id )
			$wordpress_user_id = get_current_user_id();
		if ( ! $wordpress_user_id )
			return false;

		$facebook_profile_id = self::get_facebook_profile_id( $wordpress_user_id );
		if ( ! $facebook_profile_id )
			return false;

		if ( $check_publish_override && self::get_user_meta( $wordpress_user_id, 'facebook_timeline_disabled', true ) )
			return false;

		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( dirname(__FILE__) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

		$permissions = Facebook_WP_Extend::get_permissions_by_facebook_user_id( $facebook_profile_id );
		if ( ! ( is_array( $permissions ) && ! empty( $permissions ) && isset( $permissions['publish_actions'] ) ) )
			return false;

		return true;
	}

	/**
	 * Retrieve a list of all WordPress users for the current site with the given capability
	 * Check each user for stored data indicating a possible association with a Facebook account
	 *
	 * @since 1.1.6
	 * @param string $capability WordPress capability. default: edit_posts
	 * @return array associative array with Facebook-enabled users (fb key) and other users (wp key)
	 */
	public static function get_wordpress_users_associated_with_facebook_accounts( $capability = 'edit_posts' ) {
		$authors = array(
			'fb' => array(),
			'wp' => array()
		);

		if ( ! $capability )
			return $authors;

		$site_users = get_users( array(
			'fields' => array( 'id', 'display_name', 'user_registered' ),
			'orderby' => 'display_name',
			'order' => 'ASC'
		) );

		foreach( $site_users as $user ) {
			// post authors only
			if ( ! ( isset( $user->id ) && user_can( $user->id, $capability ) ) )
				continue;

			$facebook_user_data = self::get_user_meta( $user->id, 'fb_data', true );
			if ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) {
				$user->fb_data = $facebook_user_data;
				$authors['fb'][] = $user;
			} else {
				$authors['wp'][] = $user;
			}
			unset( $facebook_user_data );
		}

		return $authors;
	}

	/**
	 * Gets and returns a specific Facebook user
	 * Requires public info read access for the account
	 *
	 * @since 1.5
	 * @link https://developers.facebook.com/docs/reference/api/user/ Facebook User fields
	 * @param string $facebook_id Facebook user identifier
	 * @param array $fields User fields to include in the result
	 */
	public static function get_facebook_user( $facebook_id, $fields = array() ) {
		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( dirname(__FILE__) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

		$response = Facebook_WP_Extend::graph_api_with_app_access_token( $facebook_id, 'GET', $fields );

		if ( is_array( $response ) ) {
			return $response;
		}

		return array();
	}

	/**
	 * Get a list of publishable Facebook pages for the currently authenticated Facebook account
	 *
	 * @since 1.5
	 * @link https://www.facebook.com/help/www/289207354498410 Facebook Page admin roles
	 * @param string $permission page permission
	 * @return array associative array with key of id, value of associative array of name, link, and access token values for pages with the given permission
	 */
	public static function get_permissioned_pages( $permission = '' ) {
		global $facebook, $facebook_loader;

		$pages = array();

		if ( ! isset( $facebook ) && ! ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
			return $pages;

		$allowed_permissions = array(
			'ADMINISTER'       => true,
			'EDIT_PROFILE'     => true,
			'CREATE_CONTENT'   => true,
			'MODERATE_CONTENT' => true,
			'CREATE_ADS'       => true,
			'BASIC_ADMIN'      => true
		);

		$fields = 'id,name,link,is_published,access_token';
		if ( is_string( $permission ) && $permission && isset( $allowed_permissions[$permission] ) )
			$fields .= ',perms';
		else
			$permission = '';

		try {
			// refresh token if needed
			self::extend_access_token();
			$accounts = $facebook->api( '/me/accounts', 'GET', array( 'fields' => $fields, 'ref' => 'fbwpp' ) );
		} catch (WP_FacebookApiException $e) {}
		if ( ! ( isset( $accounts ) && is_array( $accounts['data'] ) ) )
			return $pages;
		$accounts = $accounts['data'];

		foreach ( $accounts as $account ) {
			// limit to published pages
			if ( ! ( isset( $account['is_published'] ) && $account['is_published'] === true ) )
				continue;

			// check the specified permission exists for the user accessing the page
			if ( $permission && ! ( is_array( $account['perms'] ) && in_array( $permission, $account['perms'], true ) ) )
				continue;

			// add page if necessary fields exist
			if ( empty( $account['id'] ) || empty( $account['name'] ) || empty( $account['access_token'] ) )
				continue;

			$page = array(
				'name' => $account['name'],
				'access_token' => $account['access_token']
			);
			if ( isset( $account['link'] ) && $account['link'] )
				$page['link'] = $account['link'];

			$pages[ $account['id'] ] = $page;
			unset( $page );
		}

		return $pages;
	}

	/**
	 * Get the Facebook user identifier associated with the given WordPress user identifier, if one exists
	 *
	 * @since 1.2
	 * @param int $wordpress_user_id WordPress user identifier
	 * @return string Facebook user identifier
	 */
	public static function get_facebook_profile_id( $wordpress_user_id ) {
		if ( ! ( is_int( $wordpress_user_id ) && $wordpress_user_id ) )
			return '';

		$facebook_user_data = self::get_user_meta( $wordpress_user_id, 'fb_data', true );
		if ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) )
			return $facebook_user_data['fb_uid'];

		return '';
	}

	/**
	 * Build a link to a Facebook profile based on stored Facebook metadata
	 *
	 * @since 1.5
	 * @param array $facebook_user_data fb_data user metadata
	 * @return string Facebook profile URL or blank of not enough data exists
	 */
	public static function facebook_profile_link( $facebook_user_data ) {
		if ( ! ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) )
			return '';

		if ( isset( $facebook_user_data['link'] ) )
			return $facebook_user_data['link'];
		else if ( isset( $facebook_user_data['name'] ) )
			return 'https://www.facebook.com/' . $facebook_user_data['username'];
		else
			return 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $facebook_user_data['fb_uid'] ), '', '&' );
	}
}

?>
