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
	 * @return Facebook_Follow_Button support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url )
			$this->href = $url;
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

		return $embed;
	}

	/**
	 * Output Embedded Post with data-* attributes
	 *
	 * @since 1.5
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes = array() ) {
		if ( ! ( isset( $this->href ) && $this->href ) )
			return '';

		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		$div_attributes = Facebook_Social_Plugin::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = array( 'href' => $this->href );

		return Facebook_Social_Plugin::div_builder( $div_attributes );
	}

	/**
	 * Output Embedded Post as XFBML
	 *
	 * @since 1.5
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		if ( ! ( isset( $this->href ) && $this->href ) )
			return '';

		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		return Facebook_Social_Plugin::xfbml_builder( self::ID, array( 'href' => $this->href ) );
	}
}

?>
