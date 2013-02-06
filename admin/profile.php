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
		$current_user = wp_get_current_user();

		if ( ! ( $current_user && user_can( $current_user, 'edit_posts' ) ) )
			return;

		// prompt to log in or update account info
		if ( ! class_exists( 'Facebook_Admin_Login' ) )
			require_once( dirname(__FILE__) . '/login.php' );
		Facebook_Admin_Login::connect_facebook_account( array( 'publish_actions', 'publish_stream' ) );

		// use the Admin Login async JS an an indicator of further login action needed (not connected or connected without all required Timeline permissions)
		if ( remove_action( 'admin_enqueue_scripts', array( 'Facebook_Admin_Login', 'add_async_load_javascript_filter' ), -1, 2 ) ) {
			add_action( 'admin_enqueue_scripts', array( 'Facebook_User_Profile', 'add_async_load_javascript_filter' ), -1, 2 );
		} else {
			// connected. permissions exist
			add_action( 'personal_options', array( 'Facebook_User_Profile', 'personal_options' ) );
			add_action( 'personal_options_update', array( 'Facebook_User_Profile', 'save_data' ) );
			add_action( 'show_user_profile', array( 'Facebook_User_Profile', 'enhance_input_field' ) );
		}

		add_filter( 'user_contactmethods', array( 'Facebook_User_Profile', 'user_contactmethods' ), 1, 2 );
	}

	/**
	 * Allow an author to disable posting to Timeline
	 *
	 * @since 1.2
	 * @param $wordpress_user WP_User object for the current profile
	 */
	public static function personal_options( $wordpress_user ) {
		echo '<tr class="facebook-post-to-timeline"><th scope="row">Facebook</th><td><input class="checkbox" type="checkbox" name="facebook_timeline" id="facebook-timeline" value="1"';

		if ( $wordpress_user ) {
			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );
			checked( ! Facebook_User::get_user_meta( $wordpress_user->ID, 'facebook_timeline_disabled', true ) );
		}

		echo ' /> <label for="facebook-timeline">' . esc_html( __( 'Post an article to my Facebook Timeline after it is public.', 'facebook' ) ) . '</label><br /></td></tr>';
	}

	/**
	 * Add a Facebook form field to the contact info section
	 *
	 * @since 1.2
	 * @param array $user_contactmethods associative array of id label pairs.
	 * @param WP_User $user WordPress user
	 */
	public static function user_contactmethods( $user_contactmethods, $user ) {
		if ( is_array( $user_contactmethods ) && $user && method_exists( $user, 'exists' ) && user_can( $user, 'edit_posts' ) ) {
			$user_contactmethods['facebook'] = 'Facebook';

			if ( ! class_exists( 'Facebook_User' ) )
				require_once( dirname( dirname(__FILE__) ) . '/facebook-user.php' );
			$facebook_user_data = Facebook_User::get_user_meta( get_current_user_id(), 'fb_data', true );

			if ( isset( $facebook_user_data['username'] ) )
				$user->facebook = esc_url_raw( 'https://www.facebook.com/' . $facebook_user_data['username'], array( 'http', 'https' ) );
			else if ( isset( $facebook_user_data['fb_uid'] ) )
				$user->facebook = esc_url_raw( 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $facebook_user_data['fb_uid'] ), '', '&' ), array( 'http', 'https' ) );
			unset( $facebook_user_data );
		}

		return $user_contactmethods;
	}

	/**
	 * Add output to the JavaScript SDK async loader success function filter
	 *
	 * @since 1.2
	 */
	public static function add_async_load_javascript_filter() {
		// async load our script after we async load Facebook JavaScript SDK
		add_filter( 'facebook_jssdk_init_extras', array( 'Facebook_User_Profile', 'async_load_javascript' ), 10, 2 );
	}

	/**
	 * add JavaScript code to the fbAsyncInit function run after Facebook JavaScript SDK has loaded.
	 *
	 * @since 1.2
	 * @param string $js_block existing JavaScript in filter
	 * @param string Facebook application id
	 * @return string JavaScript code to be appended to the fbAsyncInit function
	 */
	public static function async_load_javascript( $js_block = '', $app_id = '' ) {
		return $js_block . 'jQuery.ajax({url:' . json_encode( plugins_url( 'static/js/admin/login' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) .  '.js', dirname(__FILE__) ) ) . ',cache:true,dataType:"script"}).done(function(){FB_WP.admin.login.messages.author_permissions_text=' . json_encode( __( 'Allow new posts to your Facebook Timeline', 'facebook' ) ) . ';FB_WP.admin.login.edit_profile()});';
	}

	/**
	 * Add information to contact info field after output
	 *
	 * @since 1.2
	 * @param WP_User WordPress user
	 */
	public static function enhance_input_field( $wordpress_user ) {
		global $facebook_loader;

		echo '<script type="text/javascript">jQuery(function(){';
		echo 'var facebook_input_el=jQuery("#facebook");if(facebook_input_el.length===0){return}';

		// disable the input. encourage permissions management on Facebook.com
		echo 'facebook_input_el.prop("disabled",true );';
		if ( isset( $facebook_loader ) && isset( $facebook_loader->credentials['app_id'] ) ) {
			echo 'facebook_input_el.after( jQuery("<div />").append( jQuery("<a />").attr({"href":' . json_encode( esc_url_raw( 'https://www.facebook.com/settings?tab=applications#application-li-' . $facebook_loader->credentials['app_id'], array( 'http', 'https' ) ) ) . ',"target":"_blank"}).text(' . json_encode( _x( 'Edit Permissions', 'edit permissions granted by a Facebook account to a Facebook application', 'facebook' ) ) . ' ) ) );';
		}

		echo '});</script>';
	}

	/**
	 * Save custom user information
	 *
	 * @since 1.2
	 * @param int $wordpress_user_id WordPress user identifier
	 */
	public static function save_data( $wordpress_user_id ) {
		remove_filter( 'user_contactmethods', array( 'Facebook_User_Profile', 'user_contactmethods' ), 1, 2 );

		if ( ! ( $wordpress_user_id && current_user_can( 'edit_user', $wordpress_user_id ) ) )
			return;

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