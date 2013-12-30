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
	 *
	 * @uses add_shortcode()
	 * @uses wp_embed_register_handler()
	 * @return void
	 */
	public static function init() {
		// expose social plugin markup using WordPress Shortcode API
		add_shortcode( 'facebook_like_button', array( 'Facebook_Shortcodes', 'like_button' ) );
		add_shortcode( 'facebook_send_button', array( 'Facebook_Shortcodes', 'send_button' ) );
		add_shortcode( 'facebook_follow_button', array( 'Facebook_Shortcodes', 'follow_button' ) );
		add_shortcode( 'facebook_embedded_post', array( 'Facebook_Shortcodes', 'embedded_post' ) );

		// Convert a Facebook URL possibly representing a public post into Facebook embedded post markup
		wp_embed_register_handler( 'facebook_embedded_post_vanity', '#^https?://www\.facebook\.com/([A-Za-z0-9\.-]{2,50})/posts/([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
		wp_embed_register_handler( 'facebook_embedded_post_no_vanity', '#^https?://www\.facebook\.com/permalink\.php\?story_fbid=([\d]+)&id=([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
		wp_embed_register_handler( 'facebook_embedded_post_activity', '#^https?://www\.facebook\.com/([A-Za-z0-9\.-]{2,50})/activity/([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
		wp_embed_register_handler( 'facebook_embedded_post_question', '#^https?://www\.facebook\.com/questions/([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
		wp_embed_register_handler( 'facebook_embedded_post_photo', '#^https?://www\.facebook\.com/photo\.php\?fbid=([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
		wp_embed_register_handler( 'facebook_embedded_post_video', '#^https?://www\.facebook\.com/photo\.php\?v=([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
		wp_embed_register_handler( 'facebook_embedded_post_note', '#^https?://www\.facebook\.com/notes/([A-Za-z0-9\.-]{2,50})/([^/]+)/([\d]+)#i', array( 'Facebook_Shortcodes', 'wp_embed_handler_embedded_post' ) );
	}

	/**
	 * Facebook Embedded Post embed handler callback.
	 *
	 * Facebook does not support oEmbed.
	 *
	 * @since 1.5
	 *
	 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 * @param array $attr Embed attributes. Not used.
	 * @param string $url The original URL that was matched by the regex. Not used.
	 * @param array $rawattr The original unmodified attributes. Not used.
	 * @return string social plugin XFBML or an empty string if minimum requirements unmet
	 */
	public static function wp_embed_handler_embedded_post( $matches, $attr, $url, $rawattr ) {
		return self::embedded_post( array( 'href' => $matches[0] ) );
	}

	/**
	 * Generate a HTML div element with data-* attributes to be converted into a Like Button by the Facebook JavaScript SDK.
	 *
	 * @since 1.1.6
	 *
	 * @param array $attributes shortcode attributes. overrides site options for specific button attributes
	 * @param string $content shortcode content. no effect
	 * @return string Like Button div HTML or empty string if minimum requirements not met
	 */
	public static function like_button( $attributes, $content = null ) {
		global $post;

		$site_options = get_option( 'facebook_like_button' );
		if ( ! is_array( $site_options ) )
			$site_options = array();

		$options = shortcode_atts( array(
			'href' => '',
			'share' => isset( $site_options['share'] ) && $site_options['share'],
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

		foreach ( array( 'share', 'show_faces' ) as $bool_key ) {
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
	 *
	 * @param array $attributes shortcode attributes. overrides site options for specific button attributes
	 * @param string $content shortcode content. no effect
	 * @return string send button HTML div or empty string if minimum requirements not met
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

	/**
	 * Generate a HTML div element with data-* attributes to be converted into a Follow Button by the Facebook JavaScript SDK
	 * The passed href URL value must be a Facebook User profile URL; this URL is not validated before attempting to use in a Follow Button parameter
	 *
	 * @since 1.5
	 *
	 * @param array $attributes shortcode attributes. overrides site options for specific button attributes
	 * @param string $content shortcode content. no effect
	 * @return string Follow Button div HTML or empty string if minimum requirements not met
	 */
	public static function follow_button( $attributes, $content = null ) {
		$site_options = get_option( 'facebook_follow_button' );
		if ( ! is_array( $site_options ) )
			$site_options = array();

		$options = shortcode_atts( array(
			'href' => '',
			'layout' => isset( $site_options['layout'] ) ? $site_options['layout'] : '',
			'show_faces' => isset( $site_options['show_faces'] ) && $site_options['show_faces'],
			'width' => isset( $site_options['width'] ) ? $site_options['width'] : 0,
			'font' => isset( $site_options['font'] ) ? $site_options['font'] : '',
			'colorscheme' => isset( $site_options['colorscheme'] ) ? $site_options['colorscheme'] : '',
			'ref' => 'shortcode'
		), $attributes, 'facebook_follow_button' );

		// Facebook User profile URL required as the target of the follow
		if ( is_string( $options['href'] ) && $options['href'] )
			$options['href'] = esc_url_raw( trim( $options['href'] ), array( 'http', 'https' ) );
		else
			return '';

		$options['show_faces'] = (bool) $options['show_faces'];
		$options['width'] = absint( $options['width'] );
		if ( ! $options['width'] )
			unset( $options['width'] );

		foreach( array( 'layout', 'font', 'colorscheme' ) as $key ) {
			$options[$key] = trim( $options[$key] );
			if ( ! $options[$key] )
				unset( $options[$key] );
		}

		if ( ! function_exists( 'facebook_get_follow_button' ) )
			require_once( dirname(__FILE__) . '/social-plugins.php' );

		return facebook_get_follow_button( $options );
	}

	/**
	 * Generate a HTML div element with data-* attributes to be converted into a Facebook embedded post
	 *
	 * @since 1.5
	 *
	 * @param array $attributes shortcode attributes. overrides site options for specific button attributes
	 * @param string $content shortcode content. no effect
	 * @return string Follow Button div HTML or empty string if minimum requirements not met
	 */
	public static function embedded_post( $attributes, $content = null ) {
		global $content_width;

		$options = shortcode_atts( array(
			'href' => '',
			'width' => 0,
			'show_border' => true
		), $attributes, 'facebook_embedded_post' );

		$options['href'] = trim( $options['href'] );
		if ( ! $options['href'] )
			return '';

		if ( ! class_exists( 'Facebook_Embedded_Post' ) )
			require_once( dirname(__FILE__) . '/class-facebook-embedded-post.php' );

		$options['width'] = absint( $options['width'] );
		if ( ! Facebook_Embedded_Post::isValidWidth( $options['width'] ) ) {
			unset($options['width']);
			if ( isset($content_width) ) {
				$width = absint($content_width);
				if ( Facebook_Embedded_Post::isValidWidth( $width ) )
					$options['width'] = $width;
			}
		}

		$embed = Facebook_Embedded_Post::fromArray( $options );
		if ( ! $embed )
			return '';
		return $embed->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	}
}
?>
