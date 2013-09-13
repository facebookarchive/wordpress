<?php

/**
 * Generate HTML for a single Like Button.
 *
 * @since 1.0
 *
 * @param array $options like button options
 * @return string HTML div for use with the Facebook SDK for JavaScript
 */
function facebook_get_like_button( $options = array() ) {
	if ( ! class_exists( 'Facebook_Like_Button' ) )
		require_once( dirname(__FILE__) . '/class-facebook-like-button.php' );

	$like_button = Facebook_Like_Button::fromArray( $options );
	if ( ! $like_button )
		return '';

	$html = $like_button->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( $html )
		return "\n" . $html . "\n";

	return '';
}

/**
 * Add Like Button(s) to post content.
 *
 * Adds a like button above the post, below the post, or both above and below the post depending on stored preferences.
 *
 * @since 1.1
 *
 * @global stdClass|WP_Post $post WordPress post. Used to request a post permalink.
 * @param string $content existing content
 * @return string passed content with Like Button markup prepended, appended, or both.
 */
function facebook_the_content_like_button( $content ) {
	global $post;

	// Like Buttons should not be the only content in the post
	if ( ! $content )
		return $content;

	$options = get_option( 'facebook_like_button' );
	if ( ! is_array( $options ) )
		$options = array();

	if ( ! isset( $options['position'] ) )
		return $content;

	// duplicate_hook
	$options['href'] = apply_filters( 'facebook_rel_canonical', get_permalink( $post->ID ) );

	if ( $options['position'] === 'top' ) {
		$options['ref'] = 'above-post';
		return facebook_get_like_button( $options ) . $content;
	} else if ( $options['position'] === 'bottom' ) {
		$options['ref'] = 'below-post';
		return $content . facebook_get_like_button( $options );
	} else if ( $options['position'] === 'both' ) {
		$options['ref'] = 'above-post';
		$above = facebook_get_like_button( $options );
		$options['ref'] = 'below-post';
		return $above . $content . facebook_get_like_button( $options );
	}

	// don't break the filter
	return $content;
}

/**
 * Recommendations Bar markup for use with Facebook SDK for JavaScript
 *
 * @since 1.1
 *
 * @param array $options stored options
 * @return string HTML div markup or empty string
 */
function facebook_get_recommendations_bar( $options = array() ) {
	if ( ! class_exists( 'Facebook_Recommendations_Bar' ) )
		require_once( dirname(__FILE__) . '/class-facebook-recommendations-bar.php' );

	$bar = Facebook_Recommendations_Bar::fromArray( $options );
	if ( ! $bar )
		return '';

	$html = $bar->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( $html )
		return "\n" . $html . "\n";

	return '';
}

/**
 * Add Recommendations Bar to the end of post content.
 *
 * Triggers the Recommendations Bar display once a visitor scrolls past the end of the post in 'onvisible' trigger mode.
 *
 * @since 1.1
 *
 * @global stdClass|WP_Post WordPress post object. Used to scope to singular post views.
 * @param string $content post content
 * @return string the content with the Recommendations Bar HTML5-style data-* div
 */
function facebook_the_content_recommendations_bar( $content ) {
	global $post;

	// single post view only
	if ( ! isset( $post ) || ! is_singular( get_post_type( $post ) ) )
		return $content;

	$options = get_option( 'facebook_recommendations_bar' );
	if ( ! is_array( $options ) )
		$options = array();

	$options['ref'] = 'recommendations-bar';

	return $content . facebook_get_recommendations_bar( $options );
}

/**
 * Generate HTML for a send button based on passed options.
 *
 * @since 1.1
 *
 * @param array $options customizations
 * @return string send button HTML for use with the Facebook SDK for JavaScript
 */
function facebook_get_send_button( $options = array() ) {
	if ( ! class_exists( 'Facebook_Send_Button' ) )
		require_once( dirname(__FILE__) . '/class-facebook-send-button.php' );

	$send_button = Facebook_Send_Button::fromArray( $options );
	if ( ! $send_button )
		return '';

	$html = $send_button->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( $html )
		return "\n" . $html . "\n";

	return '';
}

/**
 * Add Send Button(s) to post content.
 *
 * Adds a send button above the post, below the post, or both above and below the post depending on stored preferences.
 *
 * @since 1.1
 *
 * @global stdClass|WP_Post WordPress post object. Used to generate a post permalink.
 * @param string $content existing content
 * @return string passed content with Send Button markup prepended, appended, or both.
 */
function facebook_the_content_send_button( $content ) {
	global $post;

	// Send Button should not be the only content
	if ( ! $content )
		return $content;

	$options = get_option( 'facebook_send_button' );
	if ( ! is_array( $options ) )
		$options = array();


	$options['href'] = apply_filters( 'facebook_rel_canonical', get_permalink( $post->ID ) );

	if ( $options['position'] === 'top' ) {
		$options['ref'] = 'above-post';
		return facebook_get_send_button( $options ) . $content;
	} else if ( $options['position'] === 'bottom' ) {
		$options['ref'] = 'below-post';
		return $content . facebook_get_send_button( $options );
	} else if ( $options['position'] === 'both' ) {
		$options['ref'] = 'above-post';
		$above = facebook_get_send_button( $options );
		$options['ref'] = 'below-post';
		return $above . $content . facebook_get_send_button( $options );
	}

	// don't break the filter
	return $content;
}

/**
 * Generate HTML for a follow button based on passed options.
 *
 * @since 1.1
 *
 * @param array $options customizations
 * @return string follow button HTML for use with the Facebook SDK for JavaScript
 */
function facebook_get_follow_button( $options = array() ) {
	// need a subscription target
	if ( ! is_array( $options ) || empty( $options['href'] ) )
		return '';

	if ( ! class_exists( 'Facebook_Follow_Button' ) )
		require_once( dirname(__FILE__) . '/class-facebook-follow-button.php' );

	$follow_button = Facebook_Follow_Button::fromArray( $options );
	if ( ! $follow_button )
		return '';

	$html = $follow_button->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
	if ( is_string($html) && $html )
		return "\n" . $html . "\n";

	return '';
}

/**
 * Add Follow Button(s) to post content
 *
 * Adds a follow button above the post, below the post, or both above and below the post depending on stored preferences.
 *
 * @since 1.1
 *
 * @param string $content existing content
 * @return string passed content with Follow Button markup prepended, appended, or both.
 */
function facebook_the_content_follow_button( $content ) {
	// Follow Button should not be the only content
	if ( ! $content )
		return $content;

	$options = get_option( 'facebook_follow_button' );
	if ( ! is_array( $options ) )
		$options = array();

	if ( ! class_exists( 'Facebook_User' ) )
		require_once( dirname( dirname( __FILE__ ) ) . '/facebook-user.php' );

	$facebook_user = Facebook_User::get_user_meta( get_the_author_meta( 'ID' ), 'fb_data', true );
	if ( ! ( $facebook_user && isset( $facebook_user['fb_uid'] ) ) )
		return $content;

	$options['href'] = Facebook_User::facebook_profile_link( $facebook_user );
	if ( ! $options['href'] )
		return $content;

	if ( $options['position'] === 'top' ) {
		$options['ref'] = 'above-post';
		return facebook_get_follow_button( $options ) . $content;
	} else if ( $options['position'] === 'bottom' ) {
		$options['ref'] = 'below-post';
		return $content . facebook_get_follow_button( $options );
	} else if ( $options['position'] === 'both' ) {
		$options['ref'] = 'above-post';
		$above = facebook_get_follow_button( $options );
		$options['ref'] = 'below-post';
		return $above . $content . facebook_get_follow_button( $options );
	}

	// don't break the filter
	return $content;
}

?>
