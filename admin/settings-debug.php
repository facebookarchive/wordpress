<?php

/**
 * Summarize the plugin configuration on a single page
 *
 * @since 1.1.6
 */
class Facebook_Settings_Debugger {

	/**
	 * Page identifier
	 *
	 * @since 1.1.6
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-debug';

	/**
	 * HTML span noting a feature exists
	 *
	 * @since 1.1.6
	 * @var string
	 */
	const EXISTS = '<span class="feature-present">&#10003;</span>';

	/**
	 * HTML span noting a feature does not exist
	 *
	 * @since 1.1.6
	 * @var string
	 */
	const DOES_NOT_EXIST = '<span class="feature-not-present">X</span>';

	/**
	 * Reference the social plugin by name
	 *
	 * @since 1.1.6
	 * @return string social plugin name
	 */
	public static function social_plugin_name() {
		return __( 'Debugger', 'facebook' );
	}

	/**
	 * Navigate to the debugger page through the Facebook top-level menu item
	 *
	 * @since 1.1.6
	 * @uses add_submenu_page()
	 * @param string $parent_slug Facebook top-level menu item slug
	 * @return string submenu hook suffix
	 */
	public static function add_submenu_item( $parent_slug ) {
		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( 'Facebook_Settings_Debugger', 'content' )
		);

		if ( $hook_suffix ) {
			add_action( 'load-' . $hook_suffix, array( 'Facebook_Settings_Debugger', 'onload' ) );
		}

		return $hook_suffix;
	}

	/**
	 * Load scripts and other setup functions on page load
	 */
	public static function onload() {
		add_action( 'admin_enqueue_scripts', array( 'Facebook_Settings_Debugger', 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.1.6
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( self::PAGE_SLUG, plugins_url( 'static/css/admin/debug' . ( ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min' )  . '.css', dirname( __FILE__ ) ), array(), '1.1.6' );
	}

	/**
	 * Page content
	 *
	 * @since 1.1.6
	 */
	public static function content() {
		global $facebook_loader;

		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( dirname(__FILE__) . '/settings.php' );

		echo '<div class="wrap">';
		echo '<header><h2>' . esc_html( self::social_plugin_name() ) . '</h2></header>';

		// only show users if app credentials stored
		if ( $facebook_loader->app_access_token_exists() ) {
			self::users_section();
			self::post_to_page_section();
		}

		self::enabled_features_by_view_type();
		self::widgets_section();
		self::server_info();

		echo '</div>';

		Facebook_Settings::stats_beacon();
	}

	/**
	 * Get all users with edit_posts capabilities broken out into Facebook-permissioned users and non-Facebook permissioned users
	 *
	 * @since 1.1.6
	 */
	public static function get_all_wordpress_facebook_users() {
		if ( ! class_exists( 'Facebook_User' ) )
			require_once( dirname( dirname( __FILE__ ) ) . '/facebook-user.php' );
		// fb => [], wp => []
		$users = Facebook_User::get_wordpress_users_associated_with_facebook_accounts();

		$users_with_app_permissions = array();

		if ( ! empty( $users['fb'] ) ) {

			if ( ! class_exists( 'Facebook_WP_Extend' ) )
				require_once( dirname( dirname( __FILE__ ) ) . '/includes/facebook-php-sdk/class-facebook-wp.php' );

			foreach ( $users['fb'] as $user ) {
				if ( ! isset( $user->fb_data['fb_uid'] ) ) {
					$users['wp'][] = $user;
					continue;
				}

				$facebook_user_permissions = Facebook_WP_Extend::get_permissions_by_facebook_user_id( $user->fb_data['fb_uid'] );
				if ( ! is_array( $facebook_user_permissions ) || ! isset( $facebook_user_permissions['installed'] ) ) {
					$users['wp'][] = $user;
					continue;
				}
				$user->fb_data['permissions'] = $facebook_user_permissions;
				unset( $facebook_user_permissions );
				$users_with_app_permissions[] = $user;
			}
		}

		$users['fb'] = $users_with_app_permissions;

		return $users;
	}

	/**
	 * Detail site users and their association with Facebook
	 *
	 * @since 1.1.6
	 */
	public static function users_section() {
		global $wpdb;

		$users = self::get_all_wordpress_facebook_users();

		// should only happen if errors
		if ( empty( $users['fb'] ) && empty( $users['wp'] ) )
			return;

		echo '<section id="debug-users"><header><h3>' . esc_html( __( 'Authors' ) ) . '</h3></header>';

		if ( ! empty( $users['fb'] ) ) {
			echo '<table><caption>' . esc_html( __( 'Connected to Facebook', 'facebook' ) ) . '</caption><colgroup><col><col span="2" class="permissions"></colgroup><thead><tr><th>' . esc_html( __( 'Name' ) ) . '</th><th title="' . esc_attr( __( 'Facebook account', 'facebook' ) ) . '"></th><th>' . esc_html( __( 'Post to timeline', 'facebook' ) ) . '</th><th>' . esc_html( __( 'Manage pages', 'facebook' ) ) . '</th></tr></thead><tbody>';
			foreach( $users['fb'] as $user ) {
				echo '<tr><th><a href="' . esc_url( get_author_posts_url( $user->id ) ) . '">' . esc_html( $user->display_name ) . '</a></th>';

				echo '<td><a class="facebook-icon" href="' . esc_url( 'https://www.facebook.com/' . ( isset( $user->fb_data['username'] ) ? $user->fb_data['username'] : 'profile.php?' . http_build_query( array( 'id' => $user->fb_data['fb_uid'] ) ) ), array( 'http', 'https' ) ) . '"></a></td>';

				echo '<td>';
				if ( isset( $user->fb_data['permissions']['publish_stream'] ) && $user->fb_data['permissions']['publish_stream'] )
					echo self::EXISTS;
				else
					echo self::DOES_NOT_EXIST;
				echo '</td>';

				echo '<td>';
				if ( isset( $user->fb_data['permissions']['manage_pages'] ) && $user->fb_data['permissions']['manage_pages'] )
					echo self::EXISTS;
				else
					echo self::DOES_NOT_EXIST;
				echo '</td></tr>';
			}
			echo '</tbody></table>';
		}

		if ( ! empty( $users['wp'] ) ) {
			// last 90 days
			$where = ' AND ' . $wpdb->prepare( 'post_date > %s', date( 'Y-m-d H:i:s', time() - 90*24*60*60 ) );

			$public_post_types = get_post_types( array( 'public' => true ) );
			if ( is_array( $public_post_types ) && ! empty( $public_post_types ) ) {
				$public_post_types = array_values( $public_post_types );
				$where .= ' AND post_type';
				if ( count( $public_post_types ) === 1 ) {
					$where .= $wpdb->prepare( ' = %s', $public_post_types[0] );
				} else {
					$s = '';
					foreach( $public_post_types as $post_type ) {
						$s .= "'" . $wpdb->escape( $post_type ) . "',";
					}
					$where .= ' IN (' . rtrim( $s, ',' ) . ')';
					unset( $s );
				}
			}

			$public_states = get_post_stati( array( 'public' => true ) );
			if ( is_array( $public_states ) && ! empty( $public_states ) ) {
				$public_states = array_values( $public_states );
				$where .= ' AND post_status';
				if ( count( $public_states ) === 1 ) {
					$where .= $wpdb->prepare( ' = %s', $public_states[0] );
				} else {
					$s = '';
					foreach( $public_states as $state ) {
						$s .= "'" . $wpdb->escape( $state ) . "',";
					}
					$where .= ' IN (' . rtrim( $s, ',' ) . ')';
					unset( $s );
				}
			}
			unset( $public_states );

			echo '<table><caption>' . esc_html( __( 'Not connected to Facebook', 'facebook' ) ) . '</caption><thead><th>' . esc_html( __( 'Name' ) ) . '</th><th><abbr title="' . esc_attr( sprintf( __( 'Number of published posts in the last %u days', 'facebook' ), 90 ) ) . '">' . esc_html( _x( '# of recent posts', 'recent articles. used as a table column header', 'facebook' ) ) . '</abbr></th></thead><tbody>';

			foreach( $users['wp'] as $user ) {
				echo '<tr><th><a href="' . esc_url( get_author_posts_url( $user->id ) ) . '">' . esc_html( $user->display_name ) . '</a></th>';
				echo '<td>' . $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d $where", $user->id ) ) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		}

		echo '</section>';
	}

	/**
	 * Display the currently associated Facebook page, if one exists.
	 *
	 * @since 1.1.6
	 */
	public static function post_to_page_section() {
		if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
			require_once( dirname( __FILE__ ) . '/settings-social-publisher.php' );

		$post_to_page = get_option( Facebook_Social_Publisher_Settings::OPTION_PUBLISH_TO_PAGE );
		if ( ! ( is_array( $post_to_page ) && isset( $post_to_page['id'] ) && isset( $post_to_page['name'] ) && isset( $post_to_page['access_token'] ) ) )
			return;

		echo '<section id="debug-page"><header><h3>' . esc_html( __( 'Facebook Page', 'facebook' ) ) . '</h3></header>';
		echo '<p>' . sprintf( esc_html( _x( 'Publishing to %s.', 'publishing to a page name on Facebook.com', 'facebook' ) ), '<a href="' . esc_url( 'https://www.facebook.com/' . $post_to_page['id'], array( 'http', 'https' ) ) . '">' . esc_html( $post_to_page['name'] ) . '</a>' );
		if ( isset( $post_to_page['set_by_user'] ) ) {
			$current_user = wp_get_current_user();
			if ( $current_user->ID == $post_to_page['set_by_user'] ) {
				echo ' ' . esc_html( __( 'Saved by you.', 'facebook' ) );
			} else {
				$setter = get_userdata( $post_to_page['set_by_user'] );
				if ( $setter ) {
					echo ' ' . esc_html( sprintf( _x( 'Saved by %s.', 'saved by person name', 'facebook' ), $setter->display_name ) );
				}
				unset( $setter );
			}
			unset( $current_user );
		}
		echo '</p>';
		
		echo '</section>';
	}

	/**
	 * Which features are enabled for the site on each major view type?
	 *
	 * @since 1.1.6
	 */
	public static function enabled_features_by_view_type() {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname( __FILE__ ) . '/settings-social-plugin.php' );
		$views = Facebook_Social_Plugin_Settings::get_show_on_choices( 'all' );
		if ( ! is_array( $views ) || empty( $views ) )
			return;


		echo '<section id="debug-social-plugins"><header><h3>' . esc_html( __( 'Social Plugins', 'facebook' ) ) . '</h3></header>';
		echo '<table><caption>' . esc_html( _x( 'Features enabled by view type', 'software features available based on different classifications website view', 'facebook' ) ) . '</caption>';

		$features = array(
			'like' => array(
				'name' => __( 'Like Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/like/'
			),
			'send' => array(
				'name' => __( 'Send Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/send/'
			),
			'follow' => array(
				'name' => __( 'Follow Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/follow/'
			),
			'recommendations_bar' => array(
				'name' => __( 'Recommendations Bar', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/recommendationsbar/'
			),
			'comments' => array(
				'name' => __( 'Comments Box', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/comments/'
			)
		);

		echo '<thead><tr><th>' . esc_html( __( 'View', 'facebook' ) ) . '</th>';
		foreach ( $features as $slug => $properties ) {
			echo '<th>';
			if ( isset( $properties['url'] ) )
				echo '<a href="' . esc_url( $properties['url'], array( 'http', 'https' ) ) . '">' . esc_html( $properties['name'] ) . '</a>';
			else
				echo esc_html( $properties['name'] );
			echo '</th>';
		}
		echo '</tr></thead><tbody>';

		foreach( $views as $view ) {
			echo '<tr><th>' . $view . '</th>';
			$view_features = get_option( 'facebook_' . $view . '_features' );
			if ( ! is_array( $view_features ) )
				$view_features = array();
			foreach( $features as $feature => $properties ) {
				echo '<td>';
				if ( isset( $view_features[$feature] ) )
					echo self::EXISTS;
				else
					echo ' ';
				echo '</td>';
			}
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</section>';
	}

	/**
	 * Widgets enabled for the site
	 *
	 * @since 1.1.6
	 */
	public static function widgets_section() {
		if ( ! class_exists( 'Facebook_Settings' ) )
			require_once( dirname(__FILE__) . '/settings.php' );

		$active_widgets = Facebook_Settings::get_active_widgets();
		if ( ! is_array( $active_widgets ) || empty( $active_widgets ) )
			return;

		$all_widgets = array(
			'activity-feed' => array(
				'name' => __( 'Activity Feed', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/activity/'
			),
			'like' => array(
				'name' => __( 'Like Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/like/'
			),
			'recommendations' => array(
				'name' => __( 'Recommendations Box', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/recommendations/'
			),
			'send' => array(
				'name' => __( 'Send Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/send/'
			),
			'follow' => array(
				'name' => __( 'Follow Button', 'facebook' ),
				'url' => 'https://developers.facebook.com/docs/reference/plugins/follow/'
			)
		);

		echo '<section id="debug-widgets"><header><h3>' . esc_html( __( 'Widgets' ) ) . '</h3></header>';
		echo '<table><thead><tr><th>' . esc_html( _x( 'Widget name', 'name of a page component', 'facebook' ) ) . '</th><th>' . esc_html( __( 'Active', 'facebook' ) ) . '</th></tr></thead><tbody>';
		foreach( $all_widgets as $slug => $widget ) {
			echo '<tr><th><a href="' . esc_url( $widget['url'], array( 'http', 'https' ) ) . '">' . esc_html( $widget['name'] ) . '</a></th><td>';
			if ( in_array( $slug , $active_widgets ) )
				echo self::EXISTS;
			else
				echo ' ';
			echo '</td></tr>';
		}
		echo '</tbody></table></section>';
	}

	/**
	 * How does the site communicate with Facebook?
	 *
	 * @since 1.1.6
	 */
	public static function server_info() {
		echo '<section id="debug-server"><header><h3>' . esc_html( __( 'Server configuration', 'facebook' ) ) . '</h3></header><table><thead><th>' . esc_html( __( 'Feature', 'facebook' ) ) . '</th><th>' . esc_html( _x( 'Info', 'Information', 'facebook' ) ) . '</th></thead><tbody>';

		// PHP version
		echo '<tr><th>' . esc_html( sprintf( _x( '%s version', 'software version', 'facebook' ), 'PHP' ) ) . '</th><td>';
		// PHP > 5.2.7
		if ( defined( 'PHP_MAJOR_VERSION' ) && defined( 'PHP_MINOR_VERSION' ) && defined( 'PHP_RELEASE_VERSION' ) )
			echo esc_html( PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION );
		else
			esc_html( phpversion() );
		echo '</td></tr>';

		// WordPress version
		echo '<tr><th>' . esc_html( sprintf( _x( '%s version', 'software version', 'facebook' ), 'WordPress' ) ) . '</th><td>' . esc_html( get_bloginfo( 'version' ) ) . '</td></tr>';

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) )
			echo '<tr><th>' . esc_html( __( 'Server software', 'facebook' ) ) . '</th><td>' . esc_html( $_SERVER['SERVER_SOFTWARE'] ) . '</td></tr>';

		// WP_HTTP connection for SSL
		echo '<tr id="debug-server-http">';
		echo '<th>' . sprintf( esc_html( _x( '%s connection method', 'server-to-server connection', 'facebook' ) ), '<a href="http://codex.wordpress.org/HTTP_API">WP_HTTP</a>' ) . '</th><td>';
		$http_obj = _wp_http_get_object();
		$http_transport = $http_obj->_get_first_available_transport( array( 'ssl' => true ) );
		if ( is_string( $http_transport ) && strlen( $http_transport ) > 8 ) {
			$http_transport = strtolower( substr( $http_transport, 8 ) );
			if ( $http_transport === 'curl' ) {
				echo '<a href="http://php.net/manual/book.curl.php">cURL</a>';
				$curl_version = curl_version();
				if ( isset( $curl_version['version'] ) )
					echo ' ' . esc_html( $curl_version['version'] );
				if ( isset( $curl_version['ssl_version'] ) ) {
					echo '; ';
					$ssl_version = $curl_version['ssl_version'];
					if ( strlen( $curl_version['ssl_version'] ) > 8 && substr_compare( $ssl_version, 'OpenSSL/', 0, 8 ) === 0 )
						echo '<a href="http://openssl.org/">OpenSSL</a>/' . esc_html( substr( $ssl_version, 8 ) );
					else
						echo esc_html( $ssl_version );
					unset( $ssl_version );
				}
				unset( $curl_version );
			} else if ( $http_transport === 'streams' ) {
				echo '<a href="http://www.php.net/manual/book.stream.php">Stream</a>';
			} else if ( $http_transport === 'fsockopen' ) {
				echo '<a href="http://php.net/manual/function.fsockopen.php">fsockopen</a>';
			} else {
				echo $http_transport;
			}
		} else {
			echo __( 'none available', 'facebook' );
		}
		echo '</td></tr>';
		unset( $http_transport );
		unset( $http_obj );

		echo '</table></section>';
	}
}

?>