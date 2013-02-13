<?php

/**
 * Encourage WordPress users to associate their account with a Facebook profile
 *
 * @since 1.1
 */
class Facebook_Admin_Login {
	/**
	 * Check if the current user has associated his or her Facebook profile with his or her WordPress account
	 * If the current user can edit posts and has not authorized Facebook then show a prompt encouraging action.
	 *
	 * @since 1.1
	 */
	public static function connect_facebook_account( $verify_permissions = null ) {
		global $facebook, $facebook_loader;

		$profile_prompt = false;

		// check for permission to publish Open Graph action (publish article)
		// check for the superset permission: publish_stream
		if ( ! is_array( $verify_permissions ) ) {
			$profile_prompt = true;
			$verify_permissions = array( 'publish_actions', 'publish_stream' );
		}

		$current_user = wp_get_current_user();
		if ( ! ( isset( $current_user ) && isset( $current_user->ID ) ) )
			return;
		$current_user_id = (int) $current_user->ID;
		if ( ! $current_user_id )
			return;

		// no need to alert if he cannot create a post
		if ( ! user_can( $current_user, 'edit_posts' ) )
			return;

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );

		$facebook_user_data_exists = false;
		$facebook_user_data = Facebook_User::get_user_meta( $current_user_id, 'fb_data', true );
		if ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) {
			if ( empty( $verify_permissions ) )
				return;
			$facebook_user_data_exists = true;
		}

		// attempt to extend the access token while suppressing errors and warnings such as headers sent on session start
		try {
			if ( isset( $facebook ) || ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
				$facebook->setExtendedAccessToken();
		}catch(Exception $e){}

		// Facebook information not found
		$facebook_user = Facebook_User::get_current_user( array( 'id','username','third_party_id' ) );
		if ( $facebook_user ) {
			if ( ! isset( $facebook ) && ! ( isset( $facebook_loader ) && $facebook_loader->load_php_sdk() ) )
				return;

			$permissions = $facebook->get_current_user_permissions( $facebook_user );

			$all_permissions_exist = true;
			foreach( $verify_permissions as $permission_to_verify ) {
				if ( ! isset( $permissions[$permission_to_verify] ) ) {
					$all_permissions_exist = false;
					break;
				}
			}

			if ( $all_permissions_exist ) {
				if ( ! $facebook_user_data_exists || $facebook_user_data['fb_uid'] != $facebook_user['id'] ) {
					$facebook_user_data = array(
						'fb_uid' => $facebook_user['id'],
						'activation_time' => time()
					);
					if ( ! empty( $facebook_user['username'] ) )
						$facebook_user_data['username'] = $facebook_user['username'];
					if ( ! empty( $facebook_user['third_party_id'] ) )
						$facebook_user_data['third_party_id'] = $facebook_user['third_party_id'];

					Facebook_User::update_user_meta( $current_user_id, 'fb_data', $facebook_user_data );
				}
				return;
			}
		}

		// priority before js sdk registration needed to add JS inside FbAsyncInit
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Admin_Login', 'add_async_load_javascript_filter' ), -1, 2 );
		// add all others at P11 after scripts registered
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Admin_Login', 'enqueue_scripts' ), 11 );

		if ( $profile_prompt )
			add_action( 'admin_notices', array( 'Facebook_Admin_Login', 'admin_notice' ), 1, 0 ); // up top
	}

	/**
	 * Prompt current user to associate his or her WordPress account with a Facebook profile
	 *
	 * @since 1.1
	 */
	public static function admin_notice() {
		// prompt user to associate his or her WordPress account with a Facebook account
		echo '<div class="updated"><p>';
		echo sprintf( esc_html( __( '%s to publish new posts to your Facebook timeline.', 'facebook' ) ), '<span class="facebook-login" data-scope="person" style="font-weight:bold">' . esc_html( __( 'Associate your WordPress account with a Facebook profile', 'facebook' ) ) . '</span>' );
		echo '</p></div>';
	}

	/**
	 * Add output to the JavaScript SDK async loader success function filter
	 *
	 * @since 1.1
	 */
	public static function add_async_load_javascript_filter() {
		// async load our script after we async load Facebook JavaScript SDK
		add_filter( 'facebook_jssdk_init_extras', array( 'Facebook_Admin_Login', 'async_load_javascript' ), 10, 2 );
	}

	/**
	 * Load support for Facebook JavaScript SDK and FB.login
	 *
	 * @since 1.1
	 */
	public static function enqueue_scripts() {
		wp_enqueue_script( 'jquery' ); // should already be enqueued in wp-admin
		wp_enqueue_script( 'facebook-jssdk' );
	}

	/**
	 * add JavaScript code to the fbAsyncInit function run after Facebook JavaScript SDK has loaded.
	 *
	 * @since 1.1
	 * @return string JavaScript code to be appended to the fbAsyncInit function
	 */
	public static function async_load_javascript( $js_block = '', $app_id = '' ) {
		return $js_block . 'jQuery.ajax({url:' . json_encode( plugins_url( 'static/js/admin/login' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) .  '.js', dirname(__FILE__) ) ) . ',cache:true,dataType:"script"}).success(function(){FB_WP.admin.login.messages.author_permissions_text=' . json_encode( __( 'Allow new posts to your Facebook Timeline', 'facebook' ) ) . ';FB_WP.admin.login.attach_events()});';
	}
}
?>