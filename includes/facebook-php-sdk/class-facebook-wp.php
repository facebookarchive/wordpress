<?php

// parent class
if ( ! class_exists( 'WP_Facebook' ) )
	require_once( dirname( __FILE__ ) . '/facebook.php' );

/**
 * Override default Facebook PHP SDK behaviors with WordPress-friendly features
 *
 * @since 1.0
 */
class Facebook_WP_Extend extends WP_Facebook {

	/**
	 * Uniquely identify requests sent from the WordPress site by WordPress version and blog url
	 *
	 * @since 1.4
	 * @return string User-Agent string for use in requests to Facebook
	 */
	public static function generate_user_agent() {
		global $wp_version;

		return apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) . '; facebook-php-' . self::VERSION . '-wp' );
	}

	/**
	 * Handle a response from the WordPress HTTP API
	 * Checks if the response is a WP_Error object. converts WP_Error to a WP_FacebookApiException for compatibility with the Facebook PHP SDK
	 * If the HTTP response code is not 200 OK then a WP_FacebookApiException is thrown with error information returned by Facebook
	 *
	 * @since 1.1.6
	 * @throws WP_FacebookApiException
	 * @param WP_Error|array $response WP_HTTP response
	 * @return string HTTP response body
	 */
	public static function handle_response( $response ) {
		if ( is_wp_error( $response ) ) {
			throw new WP_FacebookApiException( array( 'error_code' => $response->get_error_code(), 'error_msg' => $response->get_error_message() ) );
		} else if ( wp_remote_retrieve_response_code( $response ) != '200' ) {
			$fb_response = json_decode( wp_remote_retrieve_body( $response ) );

			throw new WP_FacebookApiException( array(
				'error_code' => $fb_response->error->code,
				'error' => array(
					'message' => $fb_response->error->message,
					'type' => $fb_response->error->type
				)
			) );
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Override Facebook PHP SDK cURL function with WP_HTTP
	 * Facebook PHP SDK is POST-only
	 *
	 * @since 1.0
	 * @todo add file upload support if we care
	 * @param string $url request URL
	 * @param array $params parameters used in the POST body
	 * @param CurlHandler $ch Initialized curl handle. unused: here for compatibility with parent method parameters only
	 * @throws WP_FacebookApiException
	 * @return string HTTP response body
	 */
	protected function makeRequest( $url, $params, $ch=null ) {
		if ( empty( $url ) || empty( $params ) )
			throw new WP_FacebookApiException( array( 'error_code' => 400, 'error' => array( 'type' => 'makeRequest', 'message' => 'Invalid parameters and/or URI passed to makeRequest' ) ) );

		return self::handle_response( wp_remote_post( $url, array(
			'redirection' => 0,
			'httpversion' => '1.1',
			'timeout' => 60,
			'user-agent' => self::generate_user_agent(),
			'headers' => array( 'Connection' => 'close' , 'Content-Type' => 'application/x-www-form-urlencoded' ),
			'sslverify' => false, // warning: might be overridden by 'https_ssl_verify' filter
			'body' => http_build_query( $params, '', '&' )
		) ) );
	}

	/**
	 * GET request sent through WordPress HTTP API with custom parameters
	 *
	 * @since 1.1.6
	 * @param string absolute URL
	 * @return array decoded JSON response as an associative array
	 */
	public static function get_json_url( $url ) {
		if ( ! is_string( $url ) && $url )
			return array();

		$response = self::handle_response( wp_remote_get( $url, array(
			'redirection' => 0,
			'httpversion' => '1.1',
			'timeout' => 5,
			'headers' => array( 'Connection' => 'close' ),
			'user-agent' => self::generate_user_agent()
		) ) );

		if ( $response )
			return json_decode( $response, true );

		return array();
	}

	/**
	 * Submit a request to the Facebook Graph API outside of the Facebook PHP SDK
	 *
	 * @param string $path Facebook Graph API endpoint
	 * @param string $method HTTP method
	 * @param array $params parameters to pass to the Graph API
	 * @return array|null data response from Graph API
	 */
	public static function graph_api( $path, $method = 'GET', $params = array() ) {
		if ( ! is_string( $path ) )
			return;

		$path = ltrim( $path, '/' ); // normalize the leading slash
		if ( ! $path )
			return;

		// pass a reference to WordPress plugin origin with each request
		if ( ! is_array( $params ) )
			$params = array();
		if ( ! isset( $params['ref'] ) )
			$params['ref'] = 'fbwpp';
		foreach ( $params as $key => $value ) {
			if ( ! is_string( $value ) )
				$params[$key] = json_encode( $value );
		}

		$url = self::$DOMAIN_MAP['graph'] . $path;
		$http_args = array(
			'redirection' => 0,
			'httpversion' => '1.1',
			'sslverify' => false, // warning: might be overridden by 'https_ssl_verify' filter
			'headers' => array( 'Connection' => 'close' ),
			'user-agent' => self::generate_user_agent()
		);

		if ( $method === 'GET' ) {
			if ( ! empty( $params ) )
				$url .= '?' . http_build_query( $params, '', '&' );
			$http_args['timeout'] = 5;
			$response = self::handle_response( wp_remote_get( $url, $http_args ) );
		} else {
			// POST
			// WP_HTTP does not support DELETE verb. store as method param for interpretation by Facebook Graph API server
			if ( $method === 'DELETE' )
				$params['method'] = 'DELETE';
			$http_args['timeout'] = 60;
			$http_args['body'] = http_build_query( $params, '', '&' );
			$http_args['headers']['Content-Type'] = 'application/x-www-form-urlencoded';

			$response = self::handle_response( wp_remote_post( $url, $http_args ) );
		}

		if ( isset( $response ) && $response )
			return json_decode( $response, true );
	}

	/**
	 * Invoke the Graph API for server-to-server communication using an application access token (no user session)
	 *
	 * @since 1.2
	 * @param string $path The Graph API URI endpoint path component
	 * @param string $method The HTTP method (default 'GET')
	 * @param array $params The query/post data
	 *
	 * @return mixed The decoded response object
	 * @throws WP_FacebookApiException
	 */
	public static function graph_api_with_app_access_token( $path, $method = 'GET', $params = array() ) {
		global $facebook_loader;

		if ( ! ( isset( $facebook_loader ) && $facebook_loader->app_access_token_exists() ) )
			return;

		if ( ! is_array( $params ) )
			$params = array();
		$params['access_token'] = $facebook_loader->credentials['access_token'];

		return self::graph_api( $path, $method, $params );
	}

	/**
	 * Retrieve Facebook permissions assigned to the application by a specific Facebook user id
	 *
	 * @since 1.2
	 * @param string $facebook_id Facebook user identifier
	 * @return array Facebook permissions
	 */
	public static function get_permissions_by_facebook_user_id( $facebook_id ) {
		if ( ! ( is_string( $facebook_id ) && $facebook_id ) )
			return array();

		$response = self::graph_api_with_app_access_token( $facebook_id . '/permissions', 'GET' );

		if ( is_array( $response ) && isset( $response['data'][0] ) ) {
			$response = $response['data'][0];
			$permissions = array();
			foreach( $response as $permission => $exists ) {
				$permissions[$permission] = true;
			}
			return $permissions;
		}

		return array();
	}

	/**
	 * Request an access token from the Facebook OAuth endpoint
	 *
	 * @since 1.5
	 * @param array $params associative array of query parameters
	 * @return string access token
	 */
	public static function get_access_token( $params ) {
		if ( ! is_array( $params ) || empty( $params ) )
			return '';

		try {
			$response = self::handle_response( wp_remote_get( self::$DOMAIN_MAP['graph'] . 'oauth/access_token?' . http_build_query( $params, '', '&' ), array(
				'redirection' => 0,
				'httpversion' => '1.1',
				'timeout' => 5,
				'headers' => array( 'Connection' => 'close' ),
				'user-agent' => self::generate_user_agent()
			) ) );
		} catch( WP_FacebookApiException $e ) {
			return '';
		}

		if ( ! ( is_string( $response ) && $response ) )
			return '';

		$response_params = array();
		wp_parse_str( $response, $response_params );
		if ( isset( $response_params['access_token'] ) && $response_params['access_token'] )
			return $response_params['access_token'];

		return '';
	}

	/**
	 * Trade an application id and a application secret for an application token used for future requests
	 *
	 * @since 1.4
	 * @return string access token or false if error
	 */
	public static function get_app_access_token( $app_id, $app_secret ) {
		if ( ! ( is_string( $app_id ) && $app_id && is_string( $app_secret ) && $app_secret ) )
			return '';

		return self::get_access_token( array( 'client_id' => $app_id, 'client_secret' => $app_secret, 'grant_type' => 'client_credentials' ) );
	}

	/**
	 * Exchange a short-term access token for a long-lived access token
	 *
	 * @link https://developers.facebook.com/docs/facebook-login/access-tokens/#extending Access Tokens: Extending Access Tokens
	 * @param string $token existing access token
	 * @return string long-lived access token
	 */
	public static function exchange_token( $token ) {
		global $facebook_loader;

		if ( ! ( is_string( $token ) && $token && isset( $facebook_loader ) && isset( $facebook_loader->credentials ) && isset( $facebook_loader->credentials['app_id'] ) && isset( $facebook_loader->credentials['app_secret'] ) ) )
			return '';

		return self::get_access_token( array( 'client_id' => $facebook_loader->credentials['app_id'], 'client_secret' => $facebook_loader->credentials['app_secret'], 'grant_type' => 'fb_exchange_token', 'fb_exchange_token' => $token ) );
	}

	/**
	 * Get application details including app name, namespace, link, and more.
	 *
	 * @since 1.4
	 * @param string $app_id application identifier
	 * @param array $fields app fields to retrieve. if blank a default set will be returned
	 * @return array application data response from Facebook API
	 */
	public static function get_app_details( $app_id = '', $fields = null ) {
		if ( ! ( is_string( $app_id ) && $app_id ) )
			return array();

		$url = self::$DOMAIN_MAP['graph'] . $app_id;

		// switch to HTTP for server configurations not supporting HTTPS
		if ( substr_compare( $url, 'https://', 0, 8 ) === 0 && ! wp_http_supports( array( 'ssl' => true ) ) )
			$url = 'http://' . substr( $url, 8 );

		if ( ! $url )
			return array();

		if ( is_array( $fields ) && ! empty( $fields ) )
			$url .= '?' . http_build_query( array( 'fields' => implode( ',', $fields ) ), '', '&' );

		try {
			$app_info = self::get_json_url( $url );
		} catch( WP_FacebookApiException $e ) {
			return array();
		}

		if ( is_array( $app_info ) && isset( $app_info['id'] ) )
			return $app_info;

		return array();
	}

	/**
	 * Get application details based on an application access token
	 *
	 * @since 1.4
	 * @param string $access_token application access token
	 * @return array application information returned by Facebook servers
	 */
	public static function get_app_details_by_access_token( $access_token, $fields ) {
		if ( ! ( is_string( $access_token ) && $access_token ) )
			return array();

		$params = array( 'access_token' => $access_token );
		if ( is_array( $fields ) && ! empty( $fields ) ) {
			if ( ! in_array( 'id', $fields, true ) )
				$fields[] = 'id';
			$params['fields'] = implode( ',', $fields );
		}

		try {
			$app_info = self::graph_api( 'app', 'GET', $params );
		} catch( WP_FacebookApiException $e ) {
			return array();
		}

		if ( is_array( $app_info ) && isset( $app_info['id'] ) )
			return $app_info;

		return array();
	}

	/**
	 * Provides the implementations of the inherited abstract
	 * methods.  The implementation uses user meta to maintain
	 * a store for authorization codes, user ids, CSRF states, and
	 * access tokens.
	 */
	protected function setPersistentData( $key, $value ) {
		if ( ! in_array( $key, self::$kSupportedKeys ) ) {
			self::errorLog( 'Unsupported key passed to setPersistentData.' );
			return;
		}

		// load user functions
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname(__FILE__) ) ) . '/facebook-user.php' );
		Facebook_User::update_user_meta( get_current_user_id(), $key, $value );
	}

	/**
	 * Get data persisted by the Facebook PHP SDK using WordPress-specific access methods
	 *
	 * @since 1.0
	 */
	protected function getPersistentData( $key, $default = false ) {
		if ( ! in_array( $key, self::$kSupportedKeys ) ) {
			self::errorLog( 'Unsupported key passed to getPersistentData.' );
			return $default;
		}

		// load user functions
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname(__FILE__) ) ) . '/facebook-user.php' );
		return Facebook_User::get_user_meta( get_current_user_id(), $key, true );
	}

	/**
	 * Delete data persisted by the Facebook PHP SDK using WordPress-specific access method
	 *
	 * @since 1.0
	 */
	protected function clearPersistentData( $key ) {
		if ( ! in_array( $key, self::$kSupportedKeys ) ) {
			self::errorLog( 'Unsupported key passed to clearPersistentData.' );
			return;
		}

		// load user functions
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( dirname(__FILE__) ) ) . '/facebook-user.php' );
		Facebook_User::delete_user_meta( get_current_user_id(), $key );
	}

	/**
	 * Delete data persisted by the Facebook PHP SDK for every possible Facebook PHP SDK data key
	 *
	 * @since 1.0
	 */
	protected function clearAllPersistentData() {
		foreach ( self::$kSupportedKeys as $key ) {
			$this->clearPersistentData($key);
		}
	}
}
?>
