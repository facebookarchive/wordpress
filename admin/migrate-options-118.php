<?php
/**
 * Change Subscribe Button to Follow Button
 *
 * @since 1.1.9
 */
class Facebook_Migrate_Options_118 {

	/**
	 * Store subscribe button options as follow button options
	 *
	 * @since 1.1.9
	 */
	public static function update_option() {
		$options = get_option( 'facebook_subscribe_button' );
		if ( ! is_array( $options ) )
			return false;

		update_option( 'facebook_follow_button', $options );
		delete_option( 'facebook_subscribe_button' );
	}

	/**
	 * Iterate through possible view types for subscribe button
	 * Change subscribe to follow if found
	 *
	 * @since 1.1.9
	 */
	public static function update_views() {
		if ( ! class_exists( 'Facebook_Follow_Button_Settings' ) )
			require_once( dirname(__FILE__) . '/settings-follow-button.php' );

		// archives + post types
		$all_possible_display_types = Facebook_Social_Plugin_Settings::get_show_on_choices( 'all' );
		// archives + post types supporting authorship
		$follow_display_types = Facebook_Follow_Button_Settings::get_show_on_choices();
		// iterate through all display types, looking for our feature in each
		foreach ( $all_possible_display_types as $display_type ) {
			$option_name = "facebook_{$display_type}_features";
			$display_preferences = get_option( $option_name );
			if ( ! is_array( $display_preferences ) )
				continue;

			if ( isset( $display_preferences['subscribe'] ) ) {
				unset( $display_preferences['subscribe'] );

				// remove any post types not supporting authorship
				if ( in_array( $display_type, $follow_display_types, true ) )
					$display_preferences['follow'] = true;

				// save new values
				update_option( $option_name, $display_preferences );
			}

			unset( $option_name );
			unset( $display_preferences );
		}
	}

	/**
	 * Update subscribe widget instances to follow widget instances
	 *
	 * @since 1.1.9
	 */
	public static function update_widgets() {
		$sidebars = wp_get_sidebars_widgets();
		if ( ! is_array( $sidebars ) )
			return;

		$found_widgets = false;

		foreach ( $sidebars as $sidebar => $widgets ) {
			foreach ( $widgets as $position => $widget_id ) {
				if ( strlen( $widget_id ) > 18 && substr_compare( $widget_id, 'facebook-subscribe', 0, 18 ) === 0 ) {
					$sidebars[$sidebar][$position] = 'facebook-follow' . substr( $widget_id, strrpos( $widget_id, '-' ) );
					$found_widgets = true;
				}
			}
		}

		if ( $found_widgets ) {
			$existing_instances = get_option( 'widget_facebook-subscribe' );
			if ( is_array( $existing_instances ) )
				update_option( 'widget_facebook-follow', $existing_instances );
			if ( $existing_instances !== false )
				delete_option( 'widget_facebook-subscribe' );
			unset( $existing_instances );
			wp_set_sidebars_widgets( $sidebars );
		}
	}

	/**
	 * Migrate subscribe to follow
	 *
	 * @since 1.1.9
	 */
	public static function migrate() {
		self::update_option();
		self::update_views();
		self::update_widgets();
	}
}
?>