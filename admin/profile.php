<?php
/**
 * Add content to a WordPress user profile
 *
 * @since 1.2
 */
class Facebook_User_Profile {

	/**
	 * Conditionally load features on the edit profile page
	 *
	 * @since 1.2
	 */
	public static function init() {
		if ( ! current_user_can( 'edit_posts' ) )
			return;

		add_action( 'show_user_profile', array( 'Facebook_User_Profile', 'facebook_section' ) );
		add_action( 'admin_enqueue_scripts', array( 'Facebook_User_Profile', 'enqueue_scripts' ) );

		// disable posting to Facebook when publish_actions present
		add_action( 'personal_options', array( 'Facebook_User_Profile', 'personal_options' ) );

		// listen for Facebook changes
		add_action( 'personal_options_update', array( 'Facebook_User_Profile', 'save_data' ) );
	}

	/**
	 * Add the login JavaScript to the WordPress script queue
	 *
	 * @since 1.5
	 * @uses wp_enqueue_script()
	 */
	public static function enqueue_scripts() {
		global $wp_scripts;

		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( dirname(__FILE__) . '/settings.php' );

		$handle = Facebook_Settings::register_login_script();
		wp_enqueue_script( $handle );

		// attach initialization JavaScript to WordPress enqueue. enqueue function for execution with Facebook SDK for JavaScript async loader
		$script = 'jQuery(document).one("facebook-login-load",function(){if(FB_WP.queue && FB_WP.queue.add){FB_WP.queue.add(function(){FB_WP.admin.login.person.init()})}});';

		$data = $wp_scripts->get_data( $handle, 'data' );
		if ( $data )
			$script = $data . "\n" . $script;
		$wp_scripts->add_data( $handle, 'data', $script );
	}

	/**
	 * Allow an author to disable posting to Timeline by default
	 *
	 * @since 1.2
	 * @param $wordpress_user WP_User object for the current profile
	 */
	public static function personal_options( $wordpress_user ) {
		if ( ! ( $wordpress_user && isset( $wordpress_user->ID ) ) )
			return;

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );

		if ( ! Facebook_User::can_publish_to_facebook( $wordpress_user->ID, false /* do not check for the presence of publish override in this option field */ ) )
			return;

		echo '<tr class="facebook-post-to-timeline"><th scope="row">Facebook</th><td><input class="checkbox" type="checkbox" name="facebook_timeline" id="facebook-timeline" value="1"';
		checked( ! Facebook_User::get_user_meta( $wordpress_user->ID, 'facebook_timeline_disabled', true ) );

		echo ' /> <label for="facebook-timeline">' . esc_html( __( 'Post an article to my Facebook Timeline after it is public.', 'facebook' ) ) . '</label><br /></td></tr>';
	}

	/**
	 * Add a Facebook section to the WordPress user profile page
	 *
	 * @since 1.5
	 * @param WP_User $wp_user WordPress user for the current profile page
	 */
	public static function facebook_section( $wp_user ) {
		global $facebook_loader;

		if ( ! ( $wp_user && isset( $wp_user->ID ) && method_exists( $wp_user, 'exists' ) && $wp_user->exists() && user_can( $wp_user, 'edit_posts' ) ) )
			return;

		$section = '<h3>' . esc_html( __( 'Facebook Account', 'facebook' ) ) . '</h3>';

		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );
		$facebook_user_data = Facebook_User::get_user_meta( $wp_user->ID, 'fb_data', true );

		$section .= '<table id="facebook-info" class="form-table"';

		// test if Facebook account associated with current WordPress user context
		if ( is_array( $facebook_user_data ) && isset( $facebook_user_data['fb_uid'] ) ) {

			$section .= ' data-fbid="' . esc_attr( $facebook_user_data['fb_uid'] ) . '"';
			if ( isset( $facebook_loader->credentials['app_id'] ) )
				$section .= ' data-appid="' . esc_attr( $facebook_loader->credentials['app_id'] ) . '">';
			$section .= '<tr><th scope="row">' . esc_html( _x( 'Connected Profile', 'Connected Facebook Profile', 'facebook' ) ) . '</th>';
			$section .= '<td><a href="' . esc_url( Facebook_User::facebook_profile_link( $facebook_user_data ), array( 'http', 'https' ) ) . '">' . esc_html( $facebook_user_data['fb_uid'] ) . '</a>';
			if ( isset( $facebook_user_data['activation_time'] ) )
				$section .= '<div class="description"><p>' . sprintf( esc_html( __( 'Associated on %s', 'facebook' ) ), '<time datetime="' . gmstrftime( '%FT%T', $facebook_user_data['activation_time'] ) . '+00:00">' . date_i18n( get_option('date_format'), $facebook_user_data['activation_time'] ) . '</time>' ) . '</p></div>';
			$section .= '</td></tr>';

			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( $facebook_loader->plugin_directory . 'includes/facebook-php-sdk/class-facebook-wp.php' );
			$permissions = Facebook_WP_Extend::get_permissions_by_facebook_user_id( $facebook_user_data['fb_uid'] );
			if ( ! empty( $permissions ) ) {
				$permission_labels = array();
				if ( isset( $permissions['installed'] ) )
					$permission_labels[] = '<a href="https://www.facebook.com/about/privacy/your-info#public-info">' . esc_html( __( 'Public profile information', 'facebook' ) ) . '</a>';
				if ( isset( $permissions['publish_actions'] ) )
					$permission_labels[] = esc_html( __( 'Publish to Timeline', 'facebook' ) );
				if ( isset( $permissions['manage_pages'] ) && isset( $permissions['publish_stream'] ) )
					$permission_labels[] = '<a href="https://developers.facebook.com/docs/reference/login/page-permissions/">' . esc_html( __( 'Manage your pages on your behalf (including creating content)', 'facebook' ) ) . '</a>';
				$section .= '<tr><th scope="row">' . esc_html( __( 'Permissions', 'facebook' ) ) . '</th><td>';
				if ( empty( $permissions ) ) {
					$section .= __( 'None', 'facebook' );
				} else {
					$section .= '<ul><li>' . implode( '</li><li>', $permission_labels ) .  '</li></ul>';
				}
				$section .= '<div id="facebook-login"></div></td></tr>';
			}
		} else {
			$section .= '><tr><th scope="row">' . esc_html( _x( 'Get started', 'Begin the process', 'facebook' ) ) . '</th>';
			$section .= '<td id="facebook-login"></td></tr>';
		}

		$section .= '</table>';
		echo $section;
	}

	/**
	 * Save custom user information
	 *
	 * @since 1.2
	 * @param int $wordpress_user_id WordPress user identifier
	 */
	public static function save_data( $wordpress_user_id ) {
		if ( ! ( $wordpress_user_id && current_user_can( 'edit_user', $wordpress_user_id ) ) )
			return;

		if ( isset( $_POST['facebook_fbid'] ) && ctype_digit( $_POST['facebook_fbid'] ) ) {
			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );

			try {
				$facebook_user = Facebook_User::get_facebook_user( $_POST['facebook_fbid'], array( 'fields' => array( 'id', 'username', 'link', 'third_party_id' ) ) );
				if ( isset( $facebook_user['id'] ) ) {
					$facebook_user_data = array(
						'fb_uid' => $facebook_user['id'],
						'activation_time' => time()
					);
					if ( ! empty( $facebook_user['username'] ) )
						$facebook_user_data['username'] = $facebook_user['username'];
					if ( ! empty( $facebook_user['link'] ) )
						$facebook_user_data['link'] = $facebook_user['link'];
					if ( ! empty( $facebook_user['third_party_id'] ) )
						$facebook_user_data['third_party_id'] = $facebook_user['third_party_id'];

					Facebook_User::update_user_meta( $wordpress_user_id, 'fb_data', $facebook_user_data );
					unset( $facebook_user_data );
				}
				unset( $facebook_user );
			} catch(Exception $e) {}
		}

		if ( isset( $_POST[ 'facebook_timeline' ] ) && $_POST[ 'facebook_timeline' ] == '1' ) {
			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );
			Facebook_User::delete_user_meta( $wordpress_user_id, 'facebook_timeline_disabled' ); // delete if stored
		} else {
			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );
			Facebook_User::update_user_meta( $wordpress_user_id, 'facebook_timeline_disabled', '1' );
		}
	}
}
?>
