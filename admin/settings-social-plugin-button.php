<?php

if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
	require_once( dirname(__FILE__) . '/settings-social-plugin.php' );

/**
 * Settings shared between social plugin buttons
 *
 * @since 1.1
 */
class Facebook_Social_Plugin_Button_Settings extends Facebook_Social_Plugin_Settings {
	/**
	 * Place a social plugin button above, below, or both above and below post content.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $position_choices = array( 'both', 'top', 'bottom' );

	/**
	 * Choose the position of a social plugin button above, below, or above and below your content.
	 *
	 * @since 1.1
	 *
	 * @param string $existing_value stored option value
	 * @return string HTML <option>s
	 */
	public static function position_choices( $existing_value = 'both' ) {
		if ( ! ( is_string( $existing_value) && $existing_value && in_array( $existing_value, self::$position_choices ) ) )
			$existing_value = 'both';

		$descriptions = array(
			'both' => __( 'before & after the post', 'facebook' ),
			'bottom' => __( 'after the post', 'facebook' ),
			'top' => __( 'before the post', 'facebook' )
		);

		$options = '';
		foreach( self::$position_choices as $position ) {
			$options .= '<option value="' . $position . '"' . selected( $position, $existing_value, false ) . '>';
			if ( isset( $descriptions[$position] ) )
				$options .= esc_html( $descriptions[$position] );
			else
				$options .= $position;
			$options .= '</option>';
		}

		return $options;
	}

	/**
	 * Sanitize social plugin button common settings before they are saved to the database
	 *
	 * @since 1.1
	 *
	 * @uses Facebook_Social_Plugin_Settings::sanitize_options()
	 * @param array $options social plugin button options
	 * @return array clean option set
	 */
	public static function sanitize_options( $options ) {
		$clean_options = parent::sanitize_options( $options );

		if ( isset( $options['position'] ) && in_array( $options['position'], self::$position_choices, true ) )
			$clean_options['position'] = $options['position'];
		else
			$clean_options['position'] = 'both';

		return $clean_options;
	}
}

?>