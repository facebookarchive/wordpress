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
	 * @param int $user_id WordPress user identifier
	 * @param string $meta_key Optional. The meta key to retrieve. By default, returns data for all keys.
	 * @param bool $single Whether to return a single value.
	 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
	 */
	public static function get_user_meta( $user_id, $meta_key, $single = false ) {
		/**
		 * Allow sites to override user data requests.
		 *
		 * Allow WordPress hosts to implement a custom user meta system by passing data through a custom filter.
		 *
		 * @since 1.0
		 *
		 * @param bool false default state to test if the filter received a new value
		 * @param int $user_id WordPress user identifier
		 * @param string $meta_key the user meta key requested
		 * @param bool $single if true return value of the meta data field
		 */
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
	 * @param int $user_id WordPress user identifier
	 * @param string $meta_key The meta key to update
	 * @param mixed $meta_value new desired value of $meta_key
	 * @param mixed $prev_value Optional. Previous value to check before removing.
	 * @return bool False on failure, true if success.
	 */
	public static function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
		/**
		 * Allow sites to override user data updates
		 *
		 * Allow WordPress hosts to implement a custom user meta system by passing data through a custom filter.
		 *
		 * @since 1.0
		 *
		 * @param bool false default state to test if the filter received a new value
		 * @param int $user_id WordPress user identifier
		 * @param string $meta_key the user meta key to be updated
		 * @param mixed $meta_value The new desired value of the meta_key, which must be different from the existing value. Arrays and objects will be automatically serialized
		 * @param mixed $prev_value Previous value to check before removing
		 */
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
	 * @param int $user_id WordPress user identifier
	 * @param string $meta_key The user meta key to depete
	 * @param mixed $meta_value Optional. Delete a specific value
	 * @return bool False for failure. True for success.
	 */
	public static function delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {
		/**
		 * Allow sites to override user data deletes
		 *
		 * Allow WordPress hosts to implement a custom user meta system by passing data through a custom filter.
		 *
		 * @since 1.0
		 *
		 * @param bool false default state to test if the filter received a new value
		 * @param int $user_id WordPress user identifier
		 * @param string $meta_key the user meta key to be deleted
		 * @param mixed $meta_value Delete a specific value
		 */
		$override = apply_filters( 'fb_delete_user_meta', false, $user_id, $meta_key, $meta_value );
		if ( false !== $override )
			return $override;

		return delete_user_meta( $user_id, $meta_key, $meta_value );
	}

	/**
	 * Extend our access token usage time through the Facebook SDK for PHP
	 *
	 * @since 1.0
	 *
	 * @global Facebook_WP_Extend $facebook a possible existing instance of the Facebook SDK for PHP
	 * @global Facebook_Loader $facebook_loader load the Facebook SDK for PHP if not already loaded
	 * @return void
	 */
	public static function extend_access_token() {
		global $facebook, $facebook_loader;

		if ( isset( $facebook ) || ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
			$facebook->setExtendedAccessToken();
	}

	/**
	 * Does the current WordPress user have Facebook data stored?
	 *
	 * Has the current viewer authorized the current application to post on his or her behalf?
	 *
	 * @since 1.1
	 * @param int $wordpress_user_id WordPress user identifier. Optional. Uses current user id if not passed
	 * @param bool $check_publish_override test for the ability to publish to Facebook disabled by the given $wordpress_user_id
	 * @return bool True if Facebook information present for current user and publish permissions exist
	 */
	public static function can_publish_to_facebook( $wordpress_user_id = 0, $check_publish_override = true ) {
		if ( ! $wordpress_user_id )
			$wordpress_user_id = get_current_user_id();
		if ( ! $wordpress_user_id )
			return false;

		// get associated Facebook account
		$facebook_profile_id = self::get_facebook_profile_id( $wordpress_user_id );
		if ( ! $facebook_profile_id )
			return false;

		// treat a disabled publish to Timeline preference the same as no publish_actions
		if ( $check_publish_override && self::get_user_meta( $wordpress_user_id, 'facebook_timeline_disabled', true ) )
			return false;

		// Facebook HTTP helper functions
		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( dirname(__FILE__) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

		// test for publish permissions
		$permissions = Facebook_WP_Extend::get_permissions_by_facebook_user_id( $facebook_profile_id );
		if ( ! ( is_array( $permissions ) && ! empty( $permissions ) && isset( $permissions['publish_actions'] ) ) )
			return false;

		return true;
	}

	/**
	 * Retrieve a list of all WordPress users for the current site with the given capability.
	 *
	 * Divide a site's WordPress users into a group with an associated Facebook account and a group without an account
	 *
	 * @since 1.1.6
	 * @param string $capability WordPress capability. default: edit_posts
	 * @return array associative array with Facebook-enabled users (fb key) and other users (wp key) {
	 *     WordPress users with the given $capability grouped into users with Facebook accounts and users without
	 *
	 *     @type string fb array of WP_User objects with Facebook account data accessible via the fb_data property
	 *     @type string wp array of WP_User objects without Facebook account data stored
	 * }
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
			// test for requested capability
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
	 * Gets and returns a specific Facebook user.
	 *
	 * Requires basic_info read access for the account. Customize fields to request exactly what you expect to use.
	 *
	 * @since 1.5
	 *
	 * @link https://developers.facebook.com/docs/reference/api/user/ Facebook User fields
	 * @param string $facebook_id Facebook user identifier
	 * @param array $fields User fields to include in the result
	 * @return array a json_decode()d User response from the Facebook Graph API for the requested user and fields
	 */
	public static function get_facebook_user( $facebook_id, $fields = array() ) {
		// Facebook HTTP helper functions
		if ( ! class_exists( 'Facebook_WP_Extend' ) )
			require_once( dirname(__FILE__) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

		$response = Facebook_WP_Extend::graph_api_with_app_access_token( $facebook_id, 'GET', $fields );

		if ( is_array( $response ) ) {
			return $response;
		}

		return array();
	}

	/**
	 * Get a list of publishable Facebook pages for the currently authenticated Facebook account.
	 *
	 * @since 1.5
	 *
	 * @link https://www.facebook.com/help/www/289207354498410 Facebook Page admin roles
	 * @global Facebook_WP_Extend $facebook a possible existing instance of the Facebook SDK for PHP
	 * @global Facebook_Loader $facebook_loader load the Facebook SDK for PHP if not already loaded
	 * @param string $permission page permission
	 * @return array {
	 *     Associative array of pages with the given permission.
	 *
	 *     @type string Facebook Page id
	 *     @type array associative array of name, link, access token
	 * }
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
	 * Get the Facebook user identifier associated with the given WordPress user identifier, if one exists.
	 *
	 * @since 1.2
	 *
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
	 * Build a link to a Facebook profile based on stored Facebook metadata.
	 *
	 * @since 1.5
	 *
	 * @param array $facebook_user_data {
	 *     fb_data user metadata
	 *
	 *     @type string 'fb_uid' Facebook User identifier.
	 *     @type string 'link' Absolute URL to a Facebook profile.
	 *     @type string 'username' Vanity Facebook username.
	 * }
	 * @return string Facebook profile URL or blank of not enough data exists
	 */
	public static function facebook_profile_link( $facebook_user_data ) {
		if ( ! ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) )
			return '';

		if ( isset( $facebook_user_data['link'] ) )
			return $facebook_user_data['link'];
		else if ( isset( $facebook_user_data['username'] ) )
			return 'https://www.facebook.com/' . $facebook_user_data['username'];
		else
			return 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $facebook_user_data['fb_uid'] ), '', '&' );
	}
}

?>
