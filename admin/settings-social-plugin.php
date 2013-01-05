<?php

/**
 * Common settings shared between Facebook social plugins
 *
 * @since 1.1
 */
class Facebook_Social_Plugin_Settings {

	/**
	 * Existing settings. Referenced by field builders
	 *
	 * @since 1.1
	 * @var array
	 */
	protected $existing_options;

	/**
	 * Style a color scheme setting choice similar to a social plugin background color and text color
	 * These styles may change and therefore be slightly off but should provide a hint of the choice to be made
	 *
	 * @since 1.1
	 * @var array
	 */
	public static $color_scheme_styles = array( 'light' => 'background-color:#ECEEF5;color:#3B5998', 'dark' => 'background-color:#C7C7C7;color:#333' );

	/**
	 * Returns all public post types for the current site
	 *
	 * @since 1.1
	 * @param string $scope 'all' to include home and archive conditionals
	 * @return array flat array of post type identifiers
	 */
	public static function get_show_on_choices( $scope = 'posts' ) {
		$public_post_types = get_post_types( array( 'public' => true ) );
		if ( $scope === 'all' )
			return array_merge( array( 'home', 'archive' ), $public_post_types );
		else
			return $public_post_types;
	}

	/**
	 * Checkboxes allowing a site publisher to which pages should display a social plugin
	 *
	 * @since 1.1
	 * @param string $name HTML name attribute
	 * @param array $existing_value stored preference
	 * @return string labeled checkboxes
	 */
	public static function show_on_choices( $name, $existing_value = '', $scope = 'posts' ) {
		if ( ! ( is_string( $name ) && $name ) )
			return '';

		$choices = self::get_show_on_choices( $scope );
		if ( ! is_array( $existing_value ) )
			$existing_value = $choices;

		$fields = array();
		foreach( $choices as $type ) {
			$field = '<label><input type="checkbox" name="' . $name . '[]" value="' . esc_attr( $type ) . '"';
			$field .= checked( isset( $existing_value[$type] ), true, false );
			$field .= ' /> ' . esc_html( $type ) . '</label>';

			$fields[] = $field;
			unset( $field );
		}

		return implode( ' ', $fields );
	}

	/**
	 * Describe the show_on setting
	 *
	 * @since 1.1
	 * @param string $plugin_name translated name of the social plugin
	 * @return string description text
	 */
	public static function show_on_description( $social_plugin_name ) {
		if ( ! ( is_string( $social_plugin_name ) && $social_plugin_name ) )
			return '';

		return sprintf( __( 'Choose where the %s will appear including your site homepage, archive pages, or individual public post types.', 'facebook' ), $social_plugin_name );
	}

	/**
	 * List of font choices as a HTML option list
	 *
	 * @param string $exsiting_value existing font preference
	 * @return string HTML <option>s suitable for use as children of a select
	 */
	public static function font_choices( $existing_value = '' ) {
		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-social-plugin.php' );

		if ( ! ( is_string( $existing_value ) && in_array( $existing_value, Facebook_Social_Plugin::$font_choices, true ) ) )
			$existing_value = '';

		$options = '<option value=""' . selected( $existing_value, '', false ) . '></option>';
		foreach( Facebook_Social_Plugin::$font_choices as $font ) {
			$options .= '<option value="' . $font . '"' . selected( $font, $existing_value, false ) . ' style="font-family:\'' . $font . '\'">' . $font . '</option>';
		}

		return $options;
	}

	/**
	 * Choose a color scheme from a set of checkboxes
	 *
	 * @param string $name HTML input attribute name
	 * @param string $existing_value stored value, if one exists
	 * @return string one or more input type=checkbox HTML elements; empty string possible if passed parameters in error
	 */
	public static function color_scheme_choices( $name, $existing_value = 'light' ) {
		if ( ! ( is_string( $name ) && $name ) )
			return '';

		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-social-plugin.php' );

		if ( ! ( is_string( $existing_value ) && $existing_value && in_array( $existing_value, Facebook_Social_Plugin::$colorscheme_choices, true ) ) )
			$existing_value = 'light';

		$checkboxes = '';
		foreach( Facebook_Social_Plugin::$colorscheme_choices as $color_scheme ) {
			$checkboxes .= '<label';
			// match background color and text color of the Facebook color scheme options
			// provides a hint of final display. May change but possibly helpful in making a decision
			if ( isset( self::$color_scheme_styles[$color_scheme] ) )
				$checkboxes .= ' style="padding:0.5em;' . self::$color_scheme_styles[$color_scheme] . '"';
			$checkboxes .= '><input type="radio" name="' . $name . '" value="' . $color_scheme . '"' . checked( $existing_value, $color_scheme, false ) . ' />';
			$checkboxes .= ' ' . esc_html( __( $color_scheme, 'facebook' ) ) . '</label> ';
		}
		return rtrim( $checkboxes );
	}

	/**
	 * Clean up custom form field attributes (fieldset, input, select) before use.
	 * Used by widget builders. Could be used by other plugins building on top of plugin
	 *
	 * @since 1.1
	 * @param array $attributes attributes that may possibly map to a HTML attribute we would like to use
	 * @param array $default_values fallback values
	 * @return array sanitized values unique to each field
	 */
	public static function parse_form_field_attributes( $attributes, $default_values ) {
		$attributes = wp_parse_args( (array) $attributes, $default_values );

		if ( ! empty( $attributes['id'] ) )
			$attributes['id'] = sanitize_html_class( $attributes['id'] );
		if ( ! empty( $attributes['class'] ) ) {
			$classes = explode( ' ', $attributes['class'] );
			array_walk( $classes, 'sanitize_html_class' );
			$attributes['class'] = implode( ' ', $classes );
		}

		return $attributes;
	}

	/**
	 * Sanitize social plugin common settings before they are saved to the database
	 *
	 * @since 1.1
	 * @param array $options social plugin options
	 * @return array clean option set
	 */
	public static function sanitize_options( $options ) {
		$clean_options = array( 'show_on' => array() );

		if ( isset( $options['show_on'] ) ) {
			if ( is_array( $options['show_on'] ) )
				$clean_options['show_on'] = array_unique( $options['show_on'] ); // SORT_STRING default after PHP 5.2.10. WordPress min requirement is 5.2.4
			else if ( is_string( $options['show_on'] ) )
				$clean_options['show_on'] = array( $options['show_on'] );
		}

		return $clean_options;
	}

	/**
	 * Build a list of places the publisher would like to display a given feature
	 *
	 * @since 1.1
	 * @param string $feature_slug unique identifier for the feature. e.g. 'like'
	 * @param string $scope specify 'all' to include archive listings and home
	 * @return array matched display conditionals
	 */
	public static function get_display_conditionals_by_feature( $feature_slug, $scope = 'posts' ) {
		$all_possible_display_types = self::get_show_on_choices( $scope );
		$show_on = array();

		// iterate through all display types, looking for our feature in each
		foreach ( $all_possible_display_types as $display_type ) {
			$display_preferences = get_option( "facebook_{$display_type}_features" );
			if ( ! is_array( $display_preferences ) )
				continue;
			if ( isset( $display_preferences[$feature_slug] ) )
				$show_on[$display_type] = true;
			unset( $display_preferences );
		}

		return $show_on;
	}

	/**
	 * Update the options we use to determine what to load based on the incoming request
	 *
	 * @since 1.1
	 * @param string $feature_slug unique identifier for the feature. e.g. 'like'
	 * @param array $show_on publisher preferences for display conditional triggers
	 * @param array $all_possible_values all possible conditional triggers we match agaisnt
	 */
	public static function update_display_conditionals( $feature_slug, $show_on, $all_possible_values ) {
		if ( ! $feature_slug || ! is_array( $all_possible_values ) || empty( $all_possible_values ) )
			return;
		if ( ! is_array( $show_on ) )
			$show_on = array();

		foreach( $all_possible_values as $display_type ) {
			$option_name = "facebook_{$display_type}_features";
			// avoid DB writes if possible
			$update = false;

			$display_preferences = get_option( $option_name );
			if ( ! is_array( $display_preferences ) ) {
				$display_preferences = array();
				$update = true;
			}

			if ( in_array( $display_type, $show_on, true ) ) {
				if ( ! isset( $display_preferences[$feature_slug] ) ) {
					$display_preferences[$feature_slug] = true;
					$update = true;
				}
			} else if ( isset( $display_preferences[$feature_slug] ) ) {
				// remove if present
				unset( $display_preferences[$feature_slug] );
				$update = true;
			}

			if ( $update )
				update_option( $option_name, $display_preferences );

			unset( $update );
			unset( $option_name );
			unset( $display_preferences );
		}
	}
}

?>