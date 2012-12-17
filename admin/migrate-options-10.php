<?php
/**
 * Migrate settings from the fb_options structure of plugin version 1.0.* to the multiple options of plugin version 1.1+
 *
 * @since 1.1
 */
class Facebook_Migrate_Options_10 {
	/**
	 * Convert old show_on choices to new show_on array
	 *
	 * @since 1.1
	 * @param array $options existing feature options
	 * @return array show_on post type array
	 */
	public static function show_on( $options ) {
		$show_on = array();

		if ( ! isset( $options['enabled'] ) ) {
			$options['show_on'] = array();
			return $options;
		}

		if ( isset( $options['show_on'] ) ) {
			if ( $options['show_on'] === 'all posts and pages' )
				$show_on = array( 'post', 'page' );
			else if ( $options['show_on'] === 'all posts' )
				$show_on = array( 'post' );
			else if ( $options['show_on'] === 'all pages' )
				$show_on = array( 'page' );
		}
		if ( isset( $options['show_on_homepage'] ) ) {
			$show_on[] = 'home';
			unset( $options['show_on_homepage'] );
		}

		if ( empty( $show_on ) )
			unset( $options['show_on'] );
		else
			$options['show_on'] = $show_on;

		return $options;
	}

	/**
	 * migrate options from Facebook plugin 1.0 style to 1.1
	 *
	 * @since 1.1
	 */
	public static function migrate() {
		$old_options = get_option( 'fb_options' );
		if ( ! is_array( $old_options ) || empty( $old_options ) )
			return;

		$app_settings = array();
		foreach( array( 'app_id', 'app_secret', 'app_namespace' ) as $option_name ) {
			if ( isset( $old_options[$option_name] ) )
				$app_settings[$option_name] = trim( $old_options[$option_name] );
		}
		if ( ! empty( $app_settings ) ) {
			if ( ! class_exists( 'Facebook_Application_Settings' ) )
				require_once( dirname(__FILE__) . '/settings-app.php' );

			$app_settings = Facebook_Application_Settings::sanitize_options( $app_settings );
			if ( ! empty( $app_settings ) )
				update_option( Facebook_Application_Settings::OPTION_NAME, $app_settings );
		}
		unset( $app_settings );

		if ( isset( $old_options['comments'] ) )
			self::migrate_comments( $old_options['comments'] );
		if ( isset( $old_options['like'] ) )
			self::migrate_like_button( $old_options['like'] );
		if ( isset( $old_options['recommendations_bar'] ) )
			self::migrate_recommendations_bar( $old_options['recommendations_bar'] );
		if ( isset( $old_options['send'] ) )
			self::migrate_send_button( $old_options['send'] );
		if ( isset( $old_options['social_publisher'] ) )
			self::migrate_social_publisher( $old_options['social_publisher'] );
		if ( isset( $old_options['follow'] ) )
			self::migrate_follow_button( $old_options['follow'] );
	}

	/**
	 * Migrate comments-specific settings
	 *
	 * @since 1.1
	 * @param array $options existing settings
	 * @return result of update_option, if run
	 */
	public static function migrate_comments( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		$options = self::show_on( $options );

		// pass comments settings through same sanitizer as individual settings page
		if ( ! class_exists( 'Facebook_Comments_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-comments.php' );
		$options = Facebook_Comments_Settings::sanitize_options( $options );
		if ( ! empty( $options ) )
			return update_option( Facebook_Comments_Settings::OPTION_NAME, $options );
	}

	/**
	 * Migrate like button settings
	 *
	 * @since 1.1
	 * @param array $options existing settings
	 * @return result of update_option, if run
	 */
	public static function migrate_like_button( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		$options = self::show_on( $options );

		// pass like button settings through same sanitizer as individual settings page
		if ( ! class_exists( 'Facebook_Like_Button_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-like-button.php' );

		$options = Facebook_Like_Button_Settings::sanitize_options( $options );
		if ( ! empty( $options ) )
			return update_option( Facebook_Like_Button_Settings::OPTION_NAME, $options );
	}

	/**
	 * Migrate recommendations bar settings
	 *
	 * @since 1.1
	 * @param array $options existing settings
	 * @return result of update_option, if run
	 */
	public static function migrate_recommendations_bar( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		if ( isset( $options['trigger'] ) ) {
			$trigger_pct = absint( $options['trigger'] );
			if ( $trigger_pct > 0 ) {
				$options['trigger'] = 'pct';
				$options['trigger_pct'] = $trigger_pct;
			} else {
				unset( $options['trigger'] );
			}
			unset( $trigger_pct );
		}

		// pass like button settings through same sanitizer as individual settings page
		if ( ! class_exists( 'Facebook_Recommendations_Bar_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-recommendations-bar.php' );
		$options = Facebook_Recommendations_Bar_Settings::sanitize_options( $options );
		if ( ! empty( $options ) )
			return update_option( Facebook_Recommendations_Bar_Settings::OPTION_NAME, $options );
	}

	/**
	 * Migrate send button settings
	 *
	 * @since 1.1
	 * @param array $options existing settings
	 * @return result of update_option, if run
	 */
	public static function migrate_send_button( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		$options = self::show_on( $options );

		if ( ! class_exists( 'Facebook_Send_Button_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-send-button.php' );
		$options = Facebook_Send_Button_Settings::sanitize_options( $options );
		if ( ! empty( $options ) )
			return update_option( Facebook_Send_Button_Settings::OPTION_NAME, $options );
	}

	/**
	 * Migrate social publisher settings
	 *
	 * @since 1.1
	 * @param array $options existing settings
	 * @return result of update_option, if run
	 */
	public static function migrate_social_publisher( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		$mentions = array( 'show_on' => array( 'post', 'page' ), 'position' => 'both' );
		if ( isset( $options['mentions_position'] ) )
			$mentions['position'] = $options['mentions_position'];
		if ( isset( $options['show_on_homepage'] ) )
			$mentions['show_on'][] = 'home';

		if ( ! class_exists( 'Facebook_Social_Publisher_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-social-publisher.php' );

		$mentions = Facebook_Social_Publisher_Settings::sanitize_mentions_options( $mentions );
		if ( ! empty( $mentions ) )
			update_option( Facebook_Social_Publisher_Settings::MENTIONS_OPTION_NAME, $mentions );

		// publish to fan page info
		if ( isset( $options['publish_to_fan_page'] ) && $options['publish_to_fan_page'] !== 'disabled' ) {
			preg_match_all( "/(.*?)@@!!(.*?)@@!!(.*?)$/su", $options['publish_to_fan_page'], $fan_page_info, PREG_SET_ORDER );
			if ( isset( $fan_page_info ) && isset( $fan_page_info[0] ) && is_array( $fan_page_info[0] ) && ! empty( $fan_page_info[0][1] ) && ! empty( $fan_page_info[0][2] ) && ! empty( $fan_page_info[0][3] ) ) {
				Facebook_Social_Publisher_Settings::update_publish_to_page( array(
					'access_token' => $fan_page_info[0][3],
					'id' => $fan_page_info[0][2],
					'name' => $fan_page_info[0][1]
				) );
			}
		}
	}

	/**
	 * Migrate follow button settings
	 *
	 * @since 1.1
	 * @param array $options existing settings
	 * @return result of update_option, if run
	 */
	public static function migrate_follow_button( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		$options = self::show_on( $options );

		if ( ! class_exists( 'Facebook_Follow_Button_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-follow-button.php' );
		$options = Facebook_Follow_Button_Settings::sanitize_options( $options );
		if ( ! empty( $options ) )
			return update_option( Facebook_Follow_Button_Settings::OPTION_NAME, $options );
	}
}
?>