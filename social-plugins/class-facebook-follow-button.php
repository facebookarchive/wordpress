<?php

if ( ! class_exists( 'Facebook_Social_Plugin' ) )
	require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

/**
 * Encourage visitors to follow your public updates on Facebook with a Follow Button
 *
 * @since 1.1
 *
 * @link https://developers.facebook.com/docs/reference/plugins/follow/ Facebook Follow Button
 */
class Facebook_Follow_Button extends Facebook_Social_Plugin {

	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const ID = 'follow';

	/**
	 * The Facebook URL representing a user profile or page open to new followers.
	 *
	 * Your account must allow followers. Follow ability only available for accounts belonging to a user over 18 years of age.
	 *
	 * @since 1.1
	 *
	 * @link https://www.facebook.com/about/follow About Facebook Follow
	 * @var string
	 */
	protected $href;

	/**
	 * Which style follow button you would like displayed.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * Choose your follow button.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $layout_choices = array( 'standard' => true, 'button_count' => true, 'box_count' => true );

	/**
	 * Show faces of the viewer's friends already following?
	 *
	 * Only applies to standard layout. Needs the extra width.
	 *
	 * @since 1.1
	 *
	 * @var bool
	 */
	protected $show_faces;

	/**
	 * Define a custom width in whole pixels.
	 *
	 * @since 1.1
	 *
	 * @var int
	 */
	protected $width;

	/**
	 * Option to bypass validation.
	 *
	 * You might validate when changing settings but choose not to validate on future generators.
	 *
	 * @since 1.1
	 *
	 * @param bool $validate false if object should not be validated
	 */
	public function __construct( $validate = true ) {
		if ( $validate === false )
			$this->validate = false;
		else
			$this->validate = true;
	}

	/**
	 * I am a follow button.
	 *
	 * @since 1.1
	 *
	 * @return string Facebook social plugin
	 */
	public function __toString() {
		return 'Facebook Follow Button';
	}

	/**
	 * Setter for href attribute.
	 *
	 * @since 1.1
	 *
	 * @param string $url absolute URL
	 * @return Facebook_Follow_Button support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url ) {
			// you can only follow a Facebook URL
			if ( ! $this->validate || parse_url( $url, PHP_URL_HOST ) === 'www.facebook.com' )
				$this->href = $url;
		}
		return $this;
	}

	/**
	 * Choose a layout option.
	 *
	 * @since 1.1
	 *
	 * @see self::$layout_choices
	 * @param string $layout a supported layout option
	 * @return Facebook_Follow_Button support chaining
	 */
	public function setLayout( $layout ) {
		if ( is_string( $layout ) && isset( self::$layout_choices[$layout] ) )
			$this->layout = $layout;
		return $this;
	}

	/**
	 * Show the faces of a logged-on Facebook user's friends.
	 *
	 * @since 1.1
	 *
	 * @return Facebook_Follow_Button support chaining
	 */
	public function showFaces() {
		$this->show_faces = true;
		return $this;
	}

	/**
	 * Width of the follow button.
	 *
	 * Should be greater than the minimum width of layout option.
	 *
	 * @since 1.1
	 *
	 * @param int $width width in whole pixels
	 * @return Facebook_Follow_Button support chaining
	 */
	public function setWidth( $width ) {
		// narrowest follow button is box_count at 55
		if ( is_int( $width ) && $width > 55 )
			$this->width = $width;
		return $this;
	}

	/**
	 * Compute a minimum width of a follow button based on configured options.
	 *
	 * @since 1.1
	 *
	 * @return int minimum width of the current configuration in whole pixels
	 */
	private function compute_minimum_width() {
		$min_width = 225; // standard
		if ( isset( $this->layout ) ) {
			if ( $this->layout === 'button_count' )
				$min_width = 90;
			else if ( $this->layout === 'box_count' )
				$min_width = 55;
		}

		return $min_width;
	}

	/**
	 * Some options may be in conflict with other options or not available for main choices.
	 *
	 * Reset customizations if we can detect non-compliance to avoid later confusion and/or layout issues.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function validate() {
		// allow overrides
		if ( isset( $this->validate ) && $this->validate === false )
			return;

		// show faces supported in standard layout only
		if ( isset( $this->show_faces ) && $this->show_faces === true && $this->layout !== 'standard' )
			unset( $this->show_faces );

		// is the specified width less than minimum for config?
		if ( isset( $this->width ) ) {
			$min_width = $this->compute_minimum_width();
			if ( $this->width < $min_width )
				$this->width = $min_width;
			unset( $min_width );
		}

		$this->validate = false;
	}

	/**
	 * convert an options array into an object.
	 *
	 * @since 1.1
	 *
	 * @param array $values associative array
	 * @return Facebook_Follow_Button follow object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$follow_button = new Facebook_Follow_Button();

		if ( isset( $values['href'] ) )
			$follow_button->setURL( $values['href'] );

		if ( isset( $values['layout'] ) )
			$follow_button->setLayout( $values['layout'] );

		if ( isset( $values['show_faces'] ) && ( $values['show_faces'] === true || $values['show_faces'] === 'true' || $values['show_faces'] == 1 ) )
			$follow_button->showFaces();

		if ( isset( $values['width'] ) )
			$follow_button->setWidth( absint( $values['width'] ) );

		if ( isset( $values['font'] ) )
			$follow_button->setFont( $values['font'] );

		if ( isset( $values['colorscheme'] ) )
			$follow_button->setColorScheme( $values['colorscheme'] );

		if ( isset( $values['kid_directed_site'] ) && ( $values['kid_directed_site'] === true || $values['kid_directed_site'] === 'true' || $values['kid_directed_site'] == 1 ) )
			$follow_button->isKidDirectedSite();

		return $follow_button;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array.
	 *
	 * will become data-key="value". Exclude values if default.
	 *
	 * @since 1.1
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		if ( ! isset( $this->href ) )
			return array();

		$data = parent::toHTMLDataArray();

		$data['href'] = $this->href;

		// show_faces only if standard layout
		if ( isset( $this->layout ) && $this->layout !== 'standard' )
			$data['layout'] = $this->layout;
		else if ( isset( $this->show_faces ) && $this->show_faces === true )
			$data['show-faces'] = 'true';

		if ( isset( $this->width ) && is_int( $this->width ) )
			$data['width'] = strval( $this->width );

		return $data;
	}

	/**
	 * Output Follow button with data-* attributes.
	 *
	 * @since 1.1
	 *
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes=array() ) {
		$data = $this->toHTMLDataArray();
		// if no target href then do nothing
		if ( empty( $data ) )
			return '';

		$div_attributes = self::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = $data;

		return self::div_builder( $div_attributes );
	}

	/**
	 * Output Follow button as XFBML.
	 *
	 * @since 1.1
	 *
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		$data = $this->toHTMLDataArray();
		if ( empty( $data ) )
			return '';

		return self::xfbml_builder( self::ID, $data );
	}
}
?>
