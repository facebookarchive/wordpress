<?php

// parent class
if ( ! class_exists( 'WP_Facebook' ) )
	require_once( dirname( __FILE__ ) . '/facebook.php' );

// load user functions
if ( ! class_exists( 'Facebook_User' ) )
	require_once( dirname( dirname( dirname(__FILE__) ) ) . '/facebook-user.php' );

/**
 * Override default Facebook PHP SDK behaviors with WordPress-friendly features
 *
 * @since 1.0
 */
class Facebook_WP_Extend extends WP_Facebook {

	/**
	 * Override Facebook PHP SDK cURL function with WP_HTTP
	 * Facebook PHP SDK is POST-only
	 *
	 * @since 1.0
	 * @todo add file upload support if we care
	 * @param string $url request URL
	 * @param array $params parameters used in the POST body
	 * @param CurlHandler $ch Initialized curl handle. unused: here for compatibility with parent method parameters only
	 * @return string HTTP response body
	 */
	protected function makeRequest( $url, $params, $ch=null ) {
		global $wp_version;

		if ( empty( $url ) || empty( $params ) )
			throw new WP_FacebookApiException( array( 'error_code' => 400, 'error' => array( 'type' => 'makeRequest', 'message' => 'Invalid parameters and/or URI passed to makeRequest' ) ) );

		$params = array(
			'redirection' => 0,
			'httpversion' => '1.1',
			'timeout' => 60,
			'user-agent' => apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) . '; facebook-php-' . self::VERSION . '-wp' ),
			'headers' => array( 'Connection' => 'close' , 'Content-type' => 'application/x-www-form-urlencoded'),
			'sslverify' => false, // warning: might be overridden by 'https_ssl_verify' filter
			'body' => http_build_query( $params, '', '&' )
		);

		$response = wp_remote_post( $url, $params );

		if ( is_wp_error( $response ) ) {
			throw new WP_FacebookApiException( array( 'error_code' => $response->get_error_code(), 'error_msg' => $response->get_error_message() ) );
		} else if ( wp_remote_retrieve_response_code( $response ) != '200' ) {
			$fb_response = json_decode( $response['body'] );

			$error_subcode = '';

			if ( isset( $fb_response->error->error_subcode ) ) {
				$error_subcode = $fb_response->error->error_subcode;
			}

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
	 * Request current application permissions for an authenticated Facebook user
	 *
	 * @since 1.1
	 * @return array user permissions as flat array
	 */
	public function get_current_user_permissions( $current_user = '' ) {
		if ( ! $current_user ) {
			// simply verify a connection between user and app
			$current_user = Facebook_User::get_current_user( array( 'id' ) );
			if ( ! $current_user )
				return array();
		}

		try {
			$response = $this->api( '/me/permissions', 'GET', array( 'ref' => 'fbwpp' ) );
		} catch ( WP_FacebookApiException $e ) {
			$error_result = $e->getResult();
			if ( $error_result && isset( $error_result['error_code'] ) ) {
				// try to extend access token if request failed
				if ( $error_result['error_code'] === 2500 )
					$this->setExtendedAccessToken();
			}
			return array();
		}

		if ( is_array( $response ) && isset( $response['data'][0] ) )
			return array_keys( $response['data'][0] );

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
			self::errorLog('Unsupported key passed to setPersistentData.');
			return;
		}

		//WP 3.0+
		Facebook_User::update_user_meta( get_current_user_id(), $key, $value );
	}

	protected function getPersistentData( $key, $default = false ) {
		if ( ! in_array( $key, self::$kSupportedKeys ) ) {
			self::errorLog('Unsupported key passed to getPersistentData.');
			return $default;
		}

		return Facebook_User::get_user_meta( get_current_user_id(), $key, true );
	}

	protected function clearPersistentData($key) {
		if ( ! in_array( $key, self::$kSupportedKeys ) ) {
			self::errorLog('Unsupported key passed to clearPersistentData.');
			return;
		}

		Facebook_User::delete_user_meta( get_current_user_id(), $key );
	}

	protected function clearAllPersistentData() {
		foreach ( self::$kSupportedKeys as $key ) {
			$this->clearPersistentData($key);
		}
	}
}
?>
