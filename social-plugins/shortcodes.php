<?php
/**
 * Convert shortcodes into HTML data-* elements for interpretation by the Facebook JavaScript SDK
 *
 * @since 1.1.6
 */
class Facebook_Shortcodes {

	/**
	 * Register shortcode handlers
	 *
	 * @since 1.1.6
	 */
	public static function init() {
		add_shortcode( 'facebook_like_button', array( 'Facebook_Shortcodes', 'like_button' ) );
		add_shortcode( 'facebook_send_button', array( 'Facebook_Shortcodes', 'send_button' ) );
	}

	/**
	 * Generate a HTML div element with data-* attributes to be converted into a Like Button by the Facebook JavaScript SDK
	 *
	 * @since 1.1.6
	 * @param array $attributes shortcode attributes. overrides site options for specific button attributes
	 * @param string $content shortcode content. no effect
	 */
	public static function like_button( $attributes, $content = null ) {
		global $post;

		$site_options = get_option( 'facebook_like_button' );
		if ( ! is_array( $site_options ) )
			$site_options = array();

		$options = shortcode_atts( array(
			'href' => '',
			'send' => isset( $site_options['send'] ) && $site_options['send'],
			'layout' => isset( $site_options['layout'] ) ? $site_options['layout'] : '',
			'show_faces' => isset( $site_options['show_faces'] ) && $site_options['show_faces'],
			'width' => isset( $site_options['width'] ) ? $site_options['width'] : 0,
			'action' => isset( $site_options['action'] ) ? $site_options['action'] : '',
			'font' => isset( $site_options['font'] ) ? $site_options['font'] : '',
			'colorscheme' => isset( $site_options['colorscheme'] ) ? $site_options['colorscheme'] : '',
			'ref' => 'shortcode'
		), $attributes, 'facebook_like_button' );

		// check for valid href value. unset if not valid, allowing for a possible permalink replacement
		if ( is_string( $options['href'] ) && $options['href'] )
			$options['href'] = esc_url_raw( $options['href'], array( 'http', 'https' ) );
		if ( ! ( is_string( $options['href'] ) && $options['href'] ) ) {
			unset( $options['href'] );
			if ( isset( $post ) )
				$options['href'] = apply_filters( 'facebook_rel_canonical', get_permalink( $post->ID ) );
		}

		foreach ( array( 'send', 'show_faces' ) as $bool_key ) {
			$options[$bool_key] = (bool) $options[$bool_key];
		}
		$options['width'] = absint( $options['width'] );
		if ( $options['width'] < 1 )
			unset( $options['width'] );

		foreach( array( 'layout', 'action', 'font', 'colorscheme', 'ref' ) as $key ) {
			$options[$key] = trim( $options[$key] );
			if ( ! $options[$key] )
				unset( $options[$key] );
		}

		if ( ! function_exists( 'facebook_get_like_button' ) )
			require_once( dirname(__FILE__) . '/social-plugins.php' );

		return facebook_get_like_button( $options );
	}

	/**
	 * Generate a HTML div element with data-* attributes to be converted into a Send Button by the Facebook JavaScript SDK
	 *
	 * @since 1.1.6
	 * @param array $attributes shortcode attributes. overrides site options for specific button attributes
	 * @param string $content shortcode content. no effect
	 */
	public static function send_button( $attributes, $content = null ) {
		global $post;

		$site_options = get_option( 'facebook_send_button' );
		if ( ! is_array( $site_options ) )
			$site_options = array();

		$options = shortcode_atts( array(
			'href' => '',
			'font' => isset( $site_options['font'] ) ? $site_options['font'] : '',
			'colorscheme' => isset( $site_options['colorscheme'] ) ? $site_options['colorscheme'] : '',
			'ref' => 'shortcode'
		), $attributes, 'facebook_send_button' );

		// check for valid href value. unset if not valid, allowing for a possible permalink replacement
		if ( is_string( $options['href'] ) && $options['href'] )
			$options['href'] = esc_url_raw( $options['href'], array( 'http', 'https' ) );
		if ( ! ( is_string( $options['href'] ) && $options['href'] ) ) {
			unset( $options['href'] );
			if ( isset( $post ) )
				$options['href'] = apply_filters( 'facebook_rel_canonical', get_permalink( $post->ID ) );
		}

		foreach ( array( 'font', 'colorscheme', 'ref' ) as $key ) {
			$options[$key] = trim( $options[$key] );
			if ( ! $options[$key] )
				unset( $options[$key] );
		}

		if ( ! function_exists( 'facebook_get_send_button' ) )
			require_once( dirname(__FILE__) . '/social-plugins.php' );

		return facebook_get_send_button( $options );
	}
}
?>