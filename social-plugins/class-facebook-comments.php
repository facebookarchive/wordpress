<?php

/**
 * Display Facebook Comments in static markup or in a specially-configured div recognized by the Facebook JavaScript SDK
 *
 * @since 1.1
 */
class Facebook_Comments {

	/**
	 * Hide comments-related content via CSS
	 *
	 * @since 1.1
	 * @uses wp_enqueue_style
	 */
	public static function css_hide_comments() {
		wp_enqueue_style( 'fb_hide_wp_comments', plugins_url( 'static/css/hide-wp-comments.min.css', dirname(__FILE__) ), array(), '1.1' );
	}

	/**
	 * Retrieve a list of comments for the given URL from the Facebook Graph API
	 *
	 * @since 1.1
	 * @param string $url absolute URL
	 * @return array list of comments
	 */
	public static function get_comments_by_url( $url ) {
		global $facebook;

		if ( ! ( isset( $facebook ) && is_string( $url ) && $url ) )
			return array();

		try {
			$comments = $facebook->api( '/comments', array( 'id' => $url ) );
		} catch ( WP_FacebookApiException $e ) {
			return array();
		}

		if ( is_array( $comments['data'] ) && ! empty( $comments['data'] ) )
			return $comments['data'];
		else
			return array();
	}

	/**
	 * Build a XFBML string to place on the page for interpretation by the Facebook JavaScript SDK at runtime
	 *
	 * @since 1.1
	 * @return string XFBML string or empty string if no post permalink context or publisher short-circuits via filter
	 */
	public static function comments_count_xfbml() {
		$url = esc_url( apply_filters( 'fb_rel_canonical', get_permalink() ), array( 'http', 'https' ) );

		if ( $url ) {
			// match comments_number() text builder, adding XFBML element instead of a number
			return str_replace( '%', '<fb:comments-count xmlns="http://ogp.me/ns/fb#" href="' . $url .  '"></fb:comments-count>', __('% Comments') );
		}

		return '';
	}

	/**
	 * Remap Facebook comment fields to WordPress comment object
	 *
	 * @since 1.1
	 * @param array $comment comment array returned from Facebook Graph API
	 * @param int $post_id optional. sets comment_post_ID if present
	 * @return stdClass WordPress style comment object
	 */
	public static function to_wp_comment( $comment, $post_id = 0 ) {
		if ( ! ( is_array( $comment ) && ! empty( $comment ) ) )
			return '';

		$wp_comment = new stdClass();
		$wp_comment->comment_approved = 1; // FB comments map best to pre-approved WP comment
		$wp_comment->comment_type = ''; // not a pingback or a trackback
		$wp_comment->user_id = 0; // not a WP user
		if ( is_int( $post_id ) && $post_id )
			$wp_comment->comment_post_ID = $post_id;
		$wp_comment->comment_ID = $comment['id'];
		$wp_comment->comment_date = iso8601_to_datetime( $comment['created_time'] );
		$wp_comment->comment_date_gmt = iso8601_to_datetime( $comment['created_time'], 'gmt' );
		$wp_comment->comment_content = $comment['message'];
		if ( is_array( $comment['from'] ) ) {
			if ( isset( $comment['from']['name'] ) )
				$wp_comment->comment_author = $comment['from']['name'];
			if ( isset( $comment['from']['id'] ) )
				$wp_comment->comment_author_url = 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $comment['from']['id'] ) );
		}

		return $wp_comment;
	}

	/**
	 * Generate HTML for a single Facebook comment
	 *
	 * @since 1.1
	 * @param array $comment Facebook comment data
	 * @param bool include Schema.org comment markup for semantic meaning
	 * @return string comment markup
	 */
	public static function single_comment( $comment, $schema_org = true ) {
		if ( ! is_array( $comment ) )
			return '';
		if ( ! is_bool( $schema_org ) )
			$schema_org = true;

		$li = '<li id="' . esc_attr( 'comment-' . $comment['id'] ) . '" class="comment"';
		if ( $schema_org )
			$li .= ' itemprop="comment" itemscope itemtype="http://schema.org/UserComments"';
		$li .= '>';

		if ( is_array( $comment['from'] ) ) {
			$author_name = apply_filters( 'get_comment_author', $comment['from']['name'] );
			if ( $author_name ) {
				$li .= '<p';
				$author_url = apply_filters( 'get_comment_author_url', esc_url( 'https://www.facebook.com/profile.php?' . http_build_query( array( 'id' => $comment['from']['id'] ) ), array( 'http', 'https' ) ) );
				if ( $author_url ) {
					$li .= '><a rel="nofollow" ';
					if ( $schema_org )
						$li .= 'itemprop="creator" ';
					$li .= 'href="' . $author_url . '">' . esc_html( $author_name ) . '</a>';
				} else {
					if ( $schema_org )
						$li .= ' itemprop="creator" itemscope itemtype="http://schema.org/Person"';
					$li .= '>' . esc_html( $author_name );
				}
				unset( $author_url );
				$li .= ':</p>';
			}
		}

		if ( isset( $comment['created_time'] ) ) {
			$unix_timestamp = strtotime( $comment['created_time'] );
			// strtotime could possibly fail to parse and return false
			if ( $unix_timestamp ) {
				$li .= '<p class="metadata"><time ';
				if ( $schema_org )
					$li .= 'itemprop="commentTime" ';
				$li .= 'datetime="' . esc_attr( $comment['created_time'] ) . '">' . esc_html( sprintf( _x( '%1$s at %2$s', 'date at time', 'facebook' ), date( get_option( 'date_format', 'F jS, Y' ), $unix_timestamp ), date( get_option( 'time_format', 'g:i a' ), $unix_timestamp ) ) ) . '</time></p>';
			}
			unset( $unix_timestamp );
		}

		$li .= '<div class="comment-content"';
		if ( $schema_org )
			$li .= ' itemprop="commentText"';
		$li .= '>' . wpautop( esc_html( $comment['message'] ), false ) . '</div>';

		if ( isset( $comment['comments'] ) && is_array( $comment['comments']['data'] ) && ! empty( $comment['comments']['data'] ) ) {
			$children = array();
			foreach( $comment['comments']['data'] as $comment_comment ) {
				$children[] = self::single_comment( $comment_comment, $schema_org );
			}

			if ( ! empty( $children ) )
				$li .= '<ol class="children">' . implode( '', $children ) . '</ol>';
			unset( $children );
		}

		$li .= '</li>';

		return $li;
	}

	/**
	 * Possibly generate a nested ol of comments from a comments array
	 *
	 * @since 1.1
	 * @param array $comments comments returned by Facebook
	 * @return string ol.commentlist or empty string
	 */
	public static function list_comments( $comments ) {
		if ( ! is_array( $comments ) )
			return '';

		$comment_list = array();

		// mark up comments in Schema.org structured data
		// allow publishers to override by returning false for the filter
		$schema_org = apply_filters( 'facebook_comment_schema_org', true );
		if ( ! is_bool( $schema_org ) )
			$schema_org = true;

		foreach( $comments as $comment ) {
			$comment_markup = self::single_comment( $comment, $schema_org );
			if ( $comment_markup )
				$comment_list[] = $comment_markup;
			unset( $comment_markup );
		}

		if ( empty( $comment_list ) )
			return '';

		return '<ol class="commentlist">' . implode( '', $comment_list ) . '</ol>';
	}

	/**
	 * Retrieve a list of comments for the current post permalink from Facebook servers
	 * Generate threaded comment markup for the comments
	 * Cache the generated HTML for 15 minutes for speed
	 *
	 * @since 1.1
	 * @param string $wrapper_element the parent element of the list of comments, if any comments exist. noscript (default), div, or section
	 * @return string list of comments possibly wrapped in a valid wrapper
	 */
	public static function comments_markup( $wrapper_element = 'noscript' ) {
		global $post;

		if ( ! isset( $post ) || ! empty( $post->post_password ) )
			return '';

		$url = apply_filters( 'fb_rel_canonical', get_permalink() );
		if ( ! $url ) // could happen. kill it early
			return '';

		$cache_key = 'fb_comments_html_' . $post->ID;
		$html = get_transient( $cache_key ); // false if not found
		if ( ! is_string( $html ) ) {
			$comments = self::get_comments_by_url( $url );

			if ( empty( $comments ) ) {
				// store a non-empty result to prevent checking for Facebook comments with every pageload
				set_transient( $cache_key, ' ', 60*15 );
				return '';
			}

			$html = self::list_comments( $comments );
			if ( is_string( $html ) && $html )
				set_transient( $cache_key, $html, 60*15 );
			else
				$html = '';
		}

		$html = trim( $html );

		if ( $html && is_string( $wrapper_element ) && in_array( $wrapper_element, array( 'noscript', 'div', 'section' ), true ) )
			return '<' . $wrapper_element . '>' . $html . '</' . $wrapper_element . '>';

		return $html;
	}

	/**
	 * Generate markup suitable for the Facebook JavaScript SDK based on site options
	 *
	 * @since 1.1
	 * @param array $options site options for the comments box social plugin
	 * @return string HTML5 div with data attributes for interpretation by the Facebook JavaScript SDK at runtime
	 */
	public static function js_sdk_markup( $options ) {
		if ( ! is_array( $options ) )
			return '';
		if ( ! empty( $options['href'] ) )
			$options['href'] = apply_filters( 'fb_rel_canonical', get_permalink() );

		if ( ! class_exists( 'Facebook_Comments_Box' ) )
			require_once( dirname(__FILE__) . '/class-facebook-comments-box.php' );

		$comments_box = Facebook_Comments_Box::fromArray( $options );
		if ( $comments_box ) {
			$comments_jssdk_div = $comments_box->asHTML( array( 'class' => array( 'fb-social-plugin' ) ) );
			if ( $comments_jssdk_div )
				return "\n" . $comments_jssdk_div . "\n";
		}

		return '';
	}

	/**
	 * Generate Facebook Comments Box social plugin markup for interpretation by the Facebook JavaScript SDK at runtime
	 * Includes a list of existing plugins wrapped in a noscript by default to better reflect full page content to search engines and other noscript users
	 *
	 * @since 1.1
	 * @param array $options chosen options from the Facebook settings page
	 * @param bool $with_noscript include a list of comments in your page markup wrapped in a noscript element. disable if you publish your posts in a non-dynamic environment such as a HTTP small object CDN and would like all content rendered dynamically at runtime
	 * @return string Facebook Comments Box markup for interpretation by the Facebook JavaScript SDK + (optional) noscript markup of existing comments
	 */
	public static function render( $options = array(), $with_noscript = true ) {
		$html = self::js_sdk_markup( $options );

		if ( $with_noscript === true ) {
			$comments_noscript = self::comments_markup( 'noscript' );
			if ( $comments_noscript )
				$html = "\n" . $comments_noscript . "\n" . $html;
		}

		return $html;
	}

	/**
	 * Display comments and comments box after main post content
	 *
	 * @since 1.1
	 * @param string $content post content
	 * @return string post content, possibly with noscript comments content appended and/or comments box markup to be interpreted by the Facebook JavaScript SDK
	 */
	public static function the_content_comments_box( $content ) {
		global $post;

		if ( ! isset( $post ) )
			return;

		$options = get_option( 'facebook_comments' );

		if ( ! is_array( $options ) || empty( $options ) )
			return $content;

		// closed posts can have comments from their previous open state
		// display noscript version of these comments
		$content .= "\n" . self::comments_markup( 'noscript' ) . "\n";

		// no option via JS SDK to display comments yet not accept new comments
		// only display JS SDK version of comments box display if we would like more comments
		if ( comments_open( $post->ID ) ) {
			$url = apply_filters( 'fb_rel_canonical', get_permalink() );
			if ( $url ) // false could happen. let JS SDK handle compatibility mode
				$options['href'] = $url;
			$content .= self::js_sdk_markup( $options );
		}

		return $content;
	}
}

?>