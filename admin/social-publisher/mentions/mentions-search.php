<?php

/**
 * Search Facebook for friend or page
 *
 * @since 1.1
 */
class Facebook_Mentions_Search {
	/**
	 * Maximum number of search results to return.
	 *
	 * @since 1.2
	 *
	 * @var int
	 */
	const MAX_RESULTS = 8;

	/**
	 * Respond to WordPress admin AJAX requests.
	 *
	 * Adds custom wp_ajax_* actions.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public static function wp_ajax_handlers() {
		add_action( 'wp_ajax_facebook_mentions_search_autocomplete', array( 'Facebook_Mentions_Search', 'search_endpoint' ) );
	}

	/**
	 * Check for minimum requirements before conducting a Facebook search.
	 *
	 * Populate result set with up to MAX_RESULTS results from current user's Facebook friends or Facebook pages.
	 *
	 * @since 1.2
	 *
	 * @global \Facebook_Loader $facebook_loader Determine if app access token exists
	 * @return void
	 */
	public static function search_endpoint() {
		global $facebook_loader;

		header( 'Content-Type: application/json; charset=utf-8', true );

		if ( array_key_exists( 'REQUEST_METHOD', $_SERVER ) && $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
			status_header( 405 );
			header( 'Allow: GET', true );
			echo json_encode( array( 'error' => 'please use HTTP GET' ) );
			exit;
		}

		if ( ! current_user_can( 'edit_posts' ) || empty( $_GET['autocompleteNonce'] ) || ! wp_verify_nonce( $_GET['autocompleteNonce'], 'facebook_autocomplete_nonce' ) ) {
			status_header( 403 );
			// use WordPress standard rejection message
			echo json_encode( array( 'error' => __( 'Cheatin\' uh?' ) ) );
			exit;
		}

		if ( ! ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() ) ) {
			status_header( 403 );
			echo json_encode( array( 'error' => __( 'Facebook credentials not properly configured on the server', 'facebook' ) ) );
			exit;
		}

		if ( ! ( isset( $_GET ) && ! empty( $_GET['q'] ) && $search_term = sanitize_text_field( trim( $_GET['q'] ) ) ) ) {
			status_header( 400 );
			echo json_encode( array( 'error' => __( 'search term required', 'facebook' ) ) );
			exit;
		}

		$results = self::search_friends( $search_term, self::MAX_RESULTS / 2 );
		$results = array_merge( $results, self::search_pages( $search_term, self::MAX_RESULTS - count($results) ) );
		if ( empty( $results ) ) {
			status_header( 404 );
			echo json_encode( array( 'error' => __( 'No results' ) ) );
		} else {
			echo json_encode( $results );
		}
		exit;
	}

	/**
	 * Search Facebook friends with names matching a given string up to a maximum number of results
	 *
	 * @since 1.2
	 *
	 * @param string $search_term search string
	 * @param int $limit maximum number of results
	 * @return array {
	 *     friend results
	 *
	 *     @type string 'object_type' user. Differentiate between User and Page results combined in one search.
	 *     @type string 'id' Facebook User identifier.
	 *     @type string 'name' Facebook User name.
	 *     @type string 'picture' Facebook User picture URL.
	 * }
	 */
	public static function search_friends( $search_term, $limit = 4 ) {
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/facebook-user.php' );

		$facebook_user_id = Facebook_User::get_facebook_profile_id( get_current_user_id() );
		if ( ! $facebook_user_id )
			return array();

		// cached list of all friends
		$cache_key = 'facebook_13_friends_' . $facebook_user_id;
		$friends = get_transient( $cache_key );
		if ( $friends === false ) {
			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

			try {
				$friends = Facebook_WP_Extend::graph_api_with_app_access_token( $facebook_user_id . '/friends', 'GET', array( 'fields' => 'id,name,picture', 'ref' => 'fbwpp' ) );
			} catch ( WP_FacebookApiException $e ) {
				return array();
			}

			if ( isset( $friends['data'] ) && is_array( $friends['data'] ) ) {
				$friends = $friends['data'];
				$clean_friends = array();
				foreach ( $friends as $friend ) {
					// FBID and name required
					if ( ! ( isset( $friend['name'] ) && $friend['name'] && isset( $friend['id'] ) && $friend['id'] ) )
						continue;

					$clean_friend = array( 'id' => $friend['id'], 'name' => $friend['name'], 'name_lower' => strtolower( $friend['name'] ) );
					if ( isset( $friend['picture']['data']['url'] ) )
						$clean_friend['picture'] = $friend['picture']['data']['url'];
					$clean_friends[] = $clean_friend;
					unset( $clean_friend );
				}
				$friends = $clean_friends;
				unset( $clean_friends );
			} else {
				$friends = array();
			}
			set_transient( $cache_key, $friends, 60*15 ); // cache friends list for 15 minutes
		}

		// no friends to match against
		if ( empty( $friends ) )
			return array();

		$search_term = strtolower( $search_term );
		// nothing to search against
		if ( ! $search_term )
			return array();

		$matched_friends = array();
		$matched_count = 0;
		foreach( $friends as $friend ) {
			if ( $matched_count === $limit )
				break;

			// does the search term appear in the name?
			if ( strpos( $friend['name_lower'], $search_term ) !== false ) {
				$friend['object_type'] = 'user';
				unset( $friend['name_lower'] );
				$matched_friends[] = $friend;
				$matched_count++;
			}
		}

		return $matched_friends;
	}

	/**
	 * Search for Facebook pages matching a given string up to maximum number of results
	 *
	 * @since 1.2
	 *
	 * @param string $search_term search string
	 * @param int $limit maximum number of results
	 * @return array {
	 *     friend results
	 *
	 *     @type string 'object_type' page. Differentiate between Page and User objects in the same search results set
	 *     @type string 'id' Facebook Page id.
	 *     @type string 'name' Facebook Page name.
	 *     @type string 'image' Facebook Page image URL
	 *     @type int 'likes' Number of Likes received by the Page.
	 *     @type int 'talking_about_count' Number of Facebook Users talking about the Page.
	 *     @type string 'category' Page category.
	 *     @type string 'location' Page location (if a physical place).
	 * }
	 */
	public static function search_pages( $search_term, $limit = 4 ) {
		global $facebook_loader;

		$cache_key = 'facebook_12_pages_' . $search_term;

		$matched_pages = get_transient( $cache_key );
		if ( $matched_pages === false ) {
			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

			$params = array( 'type' => 'page', 'fields' => 'id,name,is_published,picture,category,location,likes,talking_about_count', 'limit' => $limit, 'q' => $search_term, 'ref' => 'fbwpp' );
			if ( isset( $facebook_loader ) && isset( $facebook_loader->locale ) )
				$params['locale'] = $facebook_loader->locale;

			try {
				$pages = Facebook_WP_Extend::graph_api_with_app_access_token( 'search', 'GET', $params );
			} catch (WP_FacebookApiException $e) {
				return array();
			}
			unset( $params );

			if ( ! ( isset( $pages['data'] ) && is_array( $pages['data'] ) ) )
				return array();

			$pages = $pages['data'];

			$matched_pages = array();
			$matched_count = 0;

			// cleanup the picture response
			foreach ( $pages as $page ) {
				if ( $matched_count === $limit )
					break;

				if ( ! ( isset( $page['id'] ) && isset( $page['name'] ) && isset( $page['is_published'] ) ) )
					continue;
				if ( ! $page['is_published'] )
					continue;

				if ( isset( $page['picture'] ) ) {
					if ( isset( $page['picture']['data']['url'] ) && ( ! isset( $page['picture']['data']['is_silhouette'] ) || $page['picture']['data']['is_silhouette'] === false ) ) {
						$picture = esc_url_raw( $page['picture']['data']['url'], array( 'http', 'https' ) );
						if ( $picture )
							$page['image'] = $picture;
						unset( $picture );
					}
					unset( $page['picture'] );
				}

				$clean_page = array(
					'object_type' => 'page',
					'id' => $page['id'],
					'name' => $page['name']
				);
				if ( isset( $page['image'] ) )
					$clean_page['image'] = $page['image'];
				if ( isset( $page['likes'] ) )
					$clean_page['likes'] = absint( $page['likes'] );
				if ( isset( $page['talking_about_count'] ) )
					$clean_page['talking_about'] = absint( $page['talking_about_count'] );
				if ( isset( $page['category'] ) )
					$clean_page['category'] = $page['category'];
				if ( isset( $page['location'] ) )
					$clean_page['location'] = $page['location'];
				$matched_pages[] = $clean_page;
				$matched_count++;
				unset( $clean_page );
			}
			set_transient( $cache_key, $matched_pages, 60*60 );
		}

		return $matched_pages;
	}
}

?>
