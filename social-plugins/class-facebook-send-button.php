<?php

if ( ! class_exists( 'Facebook_Social_Plugin' ) )
	require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

/**
 * Help visitors send a message to his or her Facebook friend(s) with a send button
 *
 * @since 1.1
 *
 * @link https://developers.facebook.com/docs/plugins/send-button/ Send Button docs
 */
class Facebook_Send_Button extends Facebook_Social_Plugin {

	/**
	 * Element and class name used in markup builders.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const ID = 'send';

	/**
	 * Override the URL used for the Send action.
	 *
	 * Default is og:url or link[rel=canonical] or document.URL
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $href;

	/**
	 * I am a send button
	 *
	 * @since 1.1
	 *
	 * @return string Facebook social plugin name
	 */
	public function __toString() {
		return 'Facebook Send Button';
	}

	/**
	 * Setter for href attribute
	 *
	 * @since 1.1
	 *
	 * @param string $url absolute URL
	 * @return Facebook_Send_Button support chaining
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
	 * @since 1.1
	 *
	 * @param array $values associative array
	 * @return Facebook_Send_Button send button object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$send_button = new Facebook_Send_Button();

		if ( isset( $values['href'] ) && is_string( $values['href'] ) )
			$send_button->setURL( $values['href'] );

		if ( isset( $values['font'] ) )
			$send_button->setFont( $values['font'] );

		if ( isset( $values['colorscheme'] ) )
			$send_button->setColorScheme( $values['colorscheme'] );

		if ( isset( $values['ref'] ) )
			$send_button->setReference( $values['ref'] );

		if ( isset( $values['kid_directed_site'] ) && ( $values['kid_directed_site'] === true || $values['kid_directed_site'] === 'true' || $values['kid_directed_site'] == 1 ) )
			$send_button->isKidDirectedSite();

		return $send_button;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 *
	 * will become data-key="value". Exclude values if default
	 *
	 * @since 1.1
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		$data = parent::toHTMLDataArray();

		if ( isset( $this->href ) )
			$data['href'] = $this->href;

		return $data;
	}

	/**
	 * Output Send button with data-* attributes.
	 *
	 * @since 1.1
	 *
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes=array() ) {
		$div_attributes = self::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = $this->toHTMLDataArray();

		return self::div_builder( $div_attributes );
	}

	/**
	 * Output Send button as XFBML
	 *
	 * @since 1.1
	 *
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		return self::xfbml_builder( self::ID, $this->toHTMLDataArray() );
	}
}

?>
