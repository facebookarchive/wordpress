<?php
/**
 * Store an app access token alongside app_id and app_secret
 * Pass Comments Box settings through the sanitizer to verify proper saving of comments enabled option
 *
 * @since 1.1.6
 */
class Facebook_Migrate_Options_115 {

	/**
	 * Add an app access token and an app namespace to existing Facebook application data
	 *
	 * @since 1.1.6
	 */
	public static function app_settings() {
		if ( ! class_exists( 'Facebook_Application_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-app.php' );
		$app_options = get_option( Facebook_Application_Settings::OPTION_NAME );
		if ( is_array( $app_options ) && isset( $app_options['app_id'] ) )
			update_option( Facebook_Application_Settings::OPTION_NAME, Facebook_Application_Settings::sanitize_options( $app_options ) );
	}

	/**
	 * Reprocess Comments Box settings to verify saved data
	 *
	 * @since 1.1.6
	 */
	public static function comments_settings() {
		if ( ! class_exists( 'Facebook_Comments_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-comments.php' );
		$comments_options = get_option( Facebook_Comments_Settings::OPTION_NAME );
		if ( ! is_array( $comments_options ) )
			$comments_options = array();
		$comments_options['show_on'] = array_keys( Facebook_Comments_Settings::get_display_conditionals_by_feature( 'comments', 'posts' ) );
		update_option( Facebook_Comments_Settings::OPTION_NAME, Facebook_Comments_Settings::sanitize_options( $comments_options ) );
	}

	/**
	 * Update options
	 *
	 * @since 1.1.6
	 */
	public static function migrate() {
		self::app_settings();
		self::comments_settings();
	}
}
?>