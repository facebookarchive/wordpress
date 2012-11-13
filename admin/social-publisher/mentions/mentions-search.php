<?php

/**
 * Search Facebook for friend or page
 *
 * @since 1.1
 */
class Facebook_Mentions_Search {

	/**
	 * Respond to WordPress admin AJAX requests
	 * Adds custom wp_ajax_* actions
	 *
	 * @since 1.1
	 */
	public static function wp_ajax_handlers() {
		add_action( 'wp_ajax_facebook_mentions_friends_autocomplete', array( 'Facebook_Mentions_Search', 'search_endpoint_friends' ) );
		add_action( 'wp_ajax_facebook_mentions_pages_autocomplete', array( 'Facebook_Mentions_Search', 'search_endpoint_pages' ) );
	}

	/**
	 * Check for minimum requirements before conducting a Facebook search
	 * Sets up a JSON response
	 *
	 * @since 1.1
	 * @return null|string discovered search term or null if error generated
	 */
	public static function search_endpoint_setup() {
		global $facebook;

		header( 'Content-Type: application/json; charset=utf-8', true );

		if ( array_key_exists( 'REQUEST_METHOD', $_SERVER ) && $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
			status_header( 405 );
			header( 'Allow: GET', true );
			echo json_encode( array( 'error' => 'please use HTTP GET' ) );
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) || empty( $_GET['autocompleteNonce'] ) || ! wp_verify_nonce( $_GET['autocompleteNonce'], 'facebook_autocomplete_nonce' ) ) {
			status_header( 403 );
			// use WordPress standard rejection message
			echo json_encode( array( 'error' => __( 'Cheatin\' uh?' ) ) );
			return;
		}

		if ( ! isset( $facebook ) ) {
			status_header( 403 );
			echo json_encode( array( 'error' => __( 'Facebook credentials not properly configured on the server', 'facebook' ) ) );
			return;
		}

		if ( ! ( isset( $_GET ) && ! empty( $_GET['q'] ) && $search_term = sanitize_text_field( trim( $_GET['q'] ) ) ) ) {
			status_header( 400 );
			echo json_encode( array( 'error' => __( 'search term required', 'facebook' ) ) );
			return;
		}

		if ( isset( $search_term ) )
			return $search_term;
	}

	/**
	 * Search by friends endpoint
	 *
	 * @since 1.1
	 */
	public static function search_endpoint_friends() {
		$search_term = self::search_endpoint_setup();
		if ( ! $search_term )
			exit;

		$content = self::friend_search( $search_term );
		if ( is_array( $content ) ) {
			echo json_encode( $content );
		} else {
			status_header( 404 );
			echo json_encode( array( 'error' => sprintf( __( 'No friends with name matching "%s" found', 'facebook' ), $search_term ) ) );
		}
		exit;
	}

	/**
	 * Search by page name endpoint
	 *
	 * @since 1.1
	 */
	public static function search_endpoint_pages() {
		$search_term = self::search_endpoint_setup();
		if ( ! $search_term )
			exit;

		$content = self::page_search( $search_term );
		if ( is_array( $content ) ) {
			echo json_encode( $content );
		} else {
			status_header( 404 );
			echo json_encode( array( 'error' => sprintf( __( 'No pages found matching "%s."', 'facebook' ), $search_term ) ) );
		}
		exit;
	}

	/**
	 * Search your friends by name
	 *
	 * @since 1.1
	 * @param string $search_term partial name to match
	 * @return null|array null on error, else array of friends with keys id, uid, name
	 */
	public static function friend_search( $search_term ) {
		global $facebook;

		if ( empty( $search_term ) )
			return;

		$facebook_user_id = $facebook->getUser();
		if ( ! $facebook_user_id )
			return;

		// cached list of all friends
		$cache_key = 'facebook_friends_' . $facebook_user_id;
		$friends = get_transient( $cache_key );
		if ( $friends === false ) {
			try {
				$friends = $facebook->api( '/me/friends', 'GET', array( 'fields' => 'id,name', 'ref' => 'fbwpp' ) );
			} catch ( WP_FacebookApiException $e ) {
				return;
			}

			if ( isset( $friends['data'] ) && is_array( $friends['data'] ) )
				$friends = $friends['data'];
			else
				$friends = array();
			set_transient( $cache_key, $friends, 60*15 ); // cache friends list for 15 minutes
		}

		// no friends to match against
		if ( empty( $friends ) )
			return;

		$search_term = strtolower( $search_term );
		// nothing to search against
		if ( ! $search_term )
			return;

		$matched_friends = array();
		foreach( $friends as $friend ) {
			// enforce minimum requirements
			if ( ! isset( $friend['name'] ) || ! isset( $friend['id'] ) )
				continue;

			// does the search term appear in the name?
			if ( strpos( strtolower($friend['name']), $search_term ) !== false )
				$matched_friends[] = $friend;
		}

		if ( ! empty( $matched_friends ) )
			return $matched_friends;
	}

	/**
	 * Search Facebook pages with a freeform text string
	 *
	 * @since 1.1
	 * @param string $search_term comparison string
	 */
	public static function page_search( $search_term ) {
		global $facebook;

		$cache_key = 'facebook_pages_' . $search_term;

		$matched_pages = get_transient( $cache_key );
		if ( $matched_pages === false ) {
			try {
				$pages = $facebook->api( '/search', 'GET', array( 'type' => 'page', 'fields' => 'id,name,picture,likes', 'ref' => 'fbwpp', 'q' => $search_term ) );
			}
			catch (WP_FacebookApiException $e) {
				return;
			}

			if ( ! ( isset( $pages['data'] ) && is_array( $pages['data'] ) ) )
				return;

			$pages = $pages['data'];

			$matched_pages = array();

			// cleanup the picture response
			foreach ( $pages as $page ) {
				if ( ! ( isset( $page['id'] ) && isset( $page['name'] ) ) )
					continue;
				if ( isset( $page['picture'] ) ) {
					if ( isset( $page['picture']['data']['url'] ) && ( ! isset( $page['picture']['data']['is_silhouette'] ) || $page['picture']['data']['is_silhouette'] === false ) ) {
						$picture = esc_url_raw( $page['picture']['data']['url'], array( 'http', 'https' ) );
						if ( $picture )
							$page['image'] = $picture;
					}
					unset( $page['picture'] );
				}

				$clean_page = array(
					'id' => $page['id'],
					'name' => $page['name']
				);
				if ( isset( $page['image'] ) )
					$clean_page['image'] = $page['image'];
				if ( isset( $page['likes'] ) )
					$clean_page['likes'] = absint( $page['likes'] );
				$matched_pages[] = $clean_page;
				unset( $clean_page );
			}
			set_transient( $cache_key, $matched_pages, 60*60 );
		}

		if ( ! empty( $matched_pages ) )
			return $matched_pages;
	}
}

?>