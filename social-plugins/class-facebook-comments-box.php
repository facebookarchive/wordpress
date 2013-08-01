<?php

/**
 * Enable user commenting for a URL
 *
 * @since 1.1
 * @link https://developers.facebook.com/docs/reference/plugins/comments/ Facebook Comments Box
 */
class Facebook_Comments_Box {

	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.1
	 * @var string
	 */
	const ID = 'comments';

	/**
	 * Override the URL related to this comment.
	 * Default is og:url or link[rel=canonical] or document.URL
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $href;

	/**
	 * Define a custom width in whole pixels
	 * Minimum recommended width: 400 pixels
	 *
	 * @since 1.1
	 * @var int
	 */
	protected $width;

	/**
	 * Choose a light or dark color scheme to match your site style
	 *
	 * @since 1.1.11
	 * @param string
	 */
	protected $colorscheme;

	/**
	 * Use a light or dark color scheme
	 *
	 * @since 1.1.11
	 * @var array
	 */
	public static $colorscheme_choices = array( 'light' => true, 'dark' => true );

	/**
	 * The number of comments to show by default
	 *
	 * @since 1.1
	 * @var int
	 */
	protected $num_posts;

	/**
	 * The order to use when displaying comments
	 *
	 * @since 1.3
	 * @var string
	 */
	protected $order_by;

	/**
	 * Choices for the order_by property
	 *
	 * @since 1.3
	 * @var string
	 */
	public static $order_by_choices = array( 'social' => true, 'time' => true, 'reverse_time' => true );

	/**
	 * Should we force the mobile-optimized version?
	 * Default is auto-detect
	 *
	 * @var bool
	 */
	protected $mobile;

	/**
	 * I am a comments box
	 *
	 * @since 1.1
	 */
	public function __toString() {
		return 'Facebook Comments Box Social Plugin';
	}

	/**
	 * Setter for href attribute
	 *
	 * @since 1.1
	 * @param string $url absolute URL
	 * @return Facebook_Comments_Box support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url )
			$this->href = $url;
		return $this;
	}

	/**
	 * Width of the like button
	 * Should be greater than the minimum width of layout + send button (if enabled) + recommend text (if chosen)
	 *
	 * @since 1.1
	 * @param int $width width in whole pixels
	 * @return Facebook_Comments_Box support chaining
	 */
	public function setWidth( $width ) {
		// narrowest recommended width is 400
		if ( is_int( $width ) && $width > 400 )
			$this->width = $width;
		return $this;
	}

	/**
	 * Choose a light or dark color scheme
	 *
	 * @since 1.1
	 * @see self::colorscheme_choices
	 * @param string $color_scheme light|dark
	 * @return Facebook_Comments_Box support chaining
	 */
	public function setColorScheme( $color_scheme ) {
		if ( is_string( $color_scheme ) && $color_scheme && isset( self::$colorscheme_choices[$color_scheme] ) )
			$this->colorscheme = $color_scheme;
		return $this;
	}

	/**
	 * The maximum number of comments to display by default
	 *
	 * @since 1.1
	 * @param int $num positive number of comments
	 * @return Facebook_Comments_Box support chaining
	 */
	public function setNumPosts( $num ) {
		if ( is_int( $num ) && $num > 0 )
			$this->num_posts = $num;
		return $this;
	}

	/**
	 * Choose a social, chronological, or reverse chronological comment ordering
	 *
	 * @since 1.3
	 * @see self::order_by_choices
	 * @param string $order_by social|time|
	 * @return Facebook_Comments_Box support chaining
	 */
	public function setOrderBy( $order_by ) {
		if ( is_string( $order_by ) && $order_by && isset( self::$order_by_choices[$order_by] ) )
			$this->order_by = $order_by;
		return $this;
	}

	/**
	 * Force the mobile view of comments
	 *
	 * @return Facebook_Comments_Box support chaining
	 */
	public function forceMobile() {
		$this->mobile = true;
		return $this;
	}

	/**
	 * convert an options array into an object
	 *
	 * @since 1.1
	 * @param array $values associative array
	 * @return Facebook_Comments_Box comments box object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$comments_box = new Facebook_Comments_Box();

		if ( isset( $values['href'] ) && is_string( $values['href'] ) )
			$comments_box->setURL( $values['href'] );

		if ( isset( $values['width'] ) )
			$comments_box->setWidth( absint( $values['width'] ) );

		if ( isset( $values['num_posts'] ) )
			$comments_box->setNumPosts( absint( $values['num_posts'] ) );

		if ( isset( $values['colorscheme'] ) )
			$comments_box->setColorScheme( $values['colorscheme'] );

		if ( isset( $values['order_by'] ) )
			$comments_box->setOrderBy( $values['order_by'] );

		if ( isset( $values['mobile'] ) && ( $values['mobile'] === true || $values['mobile'] === 'true' || $values['mobile'] == 1 )  )
			$comments_box->forceMobile();

		return $comments_box;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 * will become data-key="value"
	 * Exclude values if default
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		$data = array();

		if ( isset( $this->href ) )
			$data['href'] = $this->href;

		if ( isset( $this->width ) && is_int( $this->width ) && $this->width > 0 )
			$data['width'] = $this->width;

		if ( isset( $this->colorscheme ) && $this->colorscheme !== 'light' )
			$data['colorscheme'] = $this->colorscheme;

		if ( isset( $this->num_posts ) && is_int( $this->num_posts ) && $this->num_posts > 0 && $this->num_posts !== 10 )
			$data['num-posts'] = $this->num_posts;

		if ( isset( $this->order_by ) )
			$data['order-by'] = $this->order_by;

		if ( isset( $this->mobile ) && $this->mobile === true )
			$data['mobile'] = 'true';

		return $data;
	}

	/**
	 * Output Like button with data-* attributes
	 *
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes. social plugin parameters in data.
	 * @return string HTML div or empty string
	 */
	public function asHTML( $div_attributes=array() ) {
		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		$div_attributes = Facebook_Social_Plugin::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = $this->toHTMLDataArray();

		return Facebook_Social_Plugin::div_builder( $div_attributes );
	}

	/**
	 * Output XFBML element with attributes
	 *
	 * @since 1.1
	 * @return string XFBML element or empty string
	 */
	public function asXFBML() {
		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		return Facebook_Social_Plugin::xfbml_builder( self::ID, $this->toHTMLDataArray() );
	}
}

?>