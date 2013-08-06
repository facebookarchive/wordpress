<?php

/**
 * Facebook Embedded post
 *
 * @since 1.5
 * @link https://developers.facebook.com/docs/plugins/embedded-posts/ Facebook Embedded Posts
 */
class Facebook_Embedded_Post {
	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.1
	 * @var string
	 */
	const ID = 'post';

	/**
	 * The Facebook URL representing public post by a person or page
	 *
	 * @since 1.5
	 * @var string
	 */
	protected $href;

	/**
	 * Show a border around the plugin
	 * Default: true
	 * Set to false to style the resulting iframe with your custom CSS
	 *
	 * @since 1.5
	 * @var bool
	 */
	protected $show_border = true;

	/**
	 * I am an embedded post
	 *
	 * @since 1.5
	 * @return string
	 */
	public function __toString() {
		return 'Facebook Embedded Post';
	}

	/**
	 * Setter for href attribute
	 *
	 * @since 1.5
	 * @param string $url absolute URL
	 * @return Facebook_Embedded_Post support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url )
			$this->href = $url;
		return $this;
	}

	/**
	 * Add a border to the box
	 *
	 * @since 1.5
	 * @return Facebook_Embedded_Post support chaining
	 */
	public function showBorder() {
		$this->show_border = true;
		return $this;
	}

	/**
	 * Hide the box border
	 *
	 * @since 1.5
	 * @return Facebook_Embedded_Post support chaining
	 */
	public function hideBorder() {
		$this->show_border = false;
		return $this;
	}

	/**
	 * convert an options array into an object
	 *
	 * @since 1.5
	 * @param array $values associative array
	 * @return Facebook_Embedded_Post embedded post object
	 */
	public static function fromArray( $values ) {
		if ( ! ( is_array( $values ) && isset( $values['href'] ) ) )
			return;

		$embed = new Facebook_Embedded_Post();

		if ( isset( $values['href'] ) && $values['href'] )
			$embed->setURL( $values['href'] );

		if ( isset( $values['show_border'] ) && ( $values['show_border'] === false || $values['show_border'] === 'false' || $values['show_border'] == 0 ) )
			$embed->hideBorder();
		else
			$embed->showBorder();

		return $embed;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 * will become data-key="value"
	 * Exclude values if default
	 *
	 * @since 1.5
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		$data = array();
		if ( ! ( isset( $this->href ) && $this->href ) )
			return $data;

		$data['href'] = $this->href;

		if ( isset( $this->show_border ) && $this->show_border === false )
			$data['show-border'] = 'false';

		return $data;
	}

	/**
	 * Output Embedded Post with data-* attributes
	 *
	 * @since 1.5
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes = array() ) {
		$data = $this->toHTMLDataArray();
		// if no target href then do nothing
		if ( empty( $data ) )
			return '';

		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		$div_attributes = Facebook_Social_Plugin::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = $data;

		return Facebook_Social_Plugin::div_builder( $div_attributes );
	}

	/**
	 * Output Embedded Post as XFBML
	 *
	 * @since 1.5
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		$data = $this->toHTMLDataArray();
		// if no target href then do nothing
		if ( empty( $data ) )
			return '';

		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		return Facebook_Social_Plugin::xfbml_builder( self::ID, $data );
	}
}

?>
