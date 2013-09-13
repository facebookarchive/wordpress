<?php

if ( ! class_exists( 'Facebook_Social_Plugin' ) )
	require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

/**
 * Allow page visitors to easily share content on Facebook with a Like button.
 *
 * @since 1.1
 *
 * @link https://developers.facebook.com/docs/reference/plugins/like/ Like button social plugin documentation
 */
class Facebook_Like_Button extends Facebook_Social_Plugin {

	/**
	 * Element and class name used in markup builders.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const ID = 'like';

	/**
	 * Override the URL used for the like action.
	 *
	 * Default is og:url or link[rel=canonical] or document.URL
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $href;

	/**
	 * Display a send button alongside the like button?
	 *
	 * @since 1.1
	 *
	 * @var bool
	 */
	protected $send_button;

	/**
	 * Which like button you would like displayed.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * Choose your like button
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $layout_choices = array( 'standard' => true, 'button_count' => true, 'box_count' => true );

	/**
	 * Show faces of the viewer's friends?
	 *
	 * Only applies to standard layout. Needs the extra width.
	 *
	 * @since 1.1
	 *
	 * @var bool
	 */
	protected $show_faces;

	/**
	 * Define a custom width in whole pixels
	 *
	 * @since 1.1
	 *
	 * @var int
	 */
	protected $width;

	/**
	 * Like or recommend
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * The verb to display on the button
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $action_choices = array( 'like' => true, 'recommend' => true );

	/**
	 * Option to bypass validation.
	 *
	 * You might validate when changing settings but choose not to validate on future generators
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
	 * I am a like button
	 *
	 * @since 1.1
	 *
	 * @return string Facebook social plugin name
	 */
	public function __toString() {
		return 'Facebook Like Button';
	}

	/**
	 * Setter for href attribute.
	 *
	 * @since 1.1
	 *
	 * @param string $url absolute URL
	 * @return Facebook_Like_Button support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url )
			$this->href = $url;
		return $this;
	}

	/**
	 * Should a send button appear next to the like button?
	 *
	 * @since 1.1
	 *
	 * @return Facebook_Like_Button support chaining
	 */
	public function includeSendButton() {
		$this->send_button = true;
		return $this;
	}

	/**
	 * Choose a layout option.
	 *
	 * @since 1.1
	 *
	 * @see self::$layout_choices
	 * @param string $layout a supported layout option
	 * @return Facebook_Like_Button support chaining
	 */
	public function setLayout( $layout ) {
		if ( is_string( $layout ) && isset( self::$layout_choices[$layout] ) )
			$this->layout = $layout;
		return $this;
	}

	/**
	 * Show the faces of a logged-on Facebook user's friends
	 *
	 * @since 1.1
	 *
	 * @return Facebook_Like_Button support chaining
	 */
	public function showFaces() {
		$this->show_faces = true;
		return $this;
	}

	/**
	 * Do not display the faces of a logged-on Facebook user's friends
	 *
	 * Reverts to default state
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Button support chaining
	 */
	public function hideFaces() {
		$this->show_faces = false;
		return $this;
	}

	/**
	 * Width of the like button
	 *
	 * Should be greater than the minimum width of layout + send button (if enabled) + recommend text (if chosen).
	 *
	 * @since 1.1
	 *
	 * @param int $width width in whole pixels
	 * @return Facebook_Like_Button support chaining
	 */
	public function setWidth( $width ) {
		// narrowest like button is box_count at 55
		if ( is_int( $width ) && $width > 55 )
			$this->width = $width;
		return $this;
	}

	/**
	 * Override the default "like" text with "recommend"
	 *
	 * @since 1.1
	 *
	 * @param string $action like|recommend
	 * @return Facebook_Like_Button support chaining
	 */
	public function setAction( $action ) {
		if ( is_string( $action ) && isset( self::$action_choices[$action] ) )
			$this->action = $action;
		return $this;
	}

	/**
	 * Compute a minimum width of a like button based on configured options.
	 *
	 * Note: Minimum widths vary based on the language-specific text used for "like" and "recommend" action. Language variances are not factored into this calculation.
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

		if ( $this->action === 'recommend' )
			$min_width += 40;

		if ( $this->send_button === true )
			$min_width += 60;

		return $min_width;
	}

	/**
	 * Some options may be in conflict with other options or not available for main choices.
	 *
	 * Reset customizations if we can detect non-compliance to avoid later confusion and/or layout issues.
	 *
	 * @since 1.1
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
	 * @return Facebook_Like_Button like object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$like_button = new Facebook_Like_Button();

		if ( isset( $values['href'] ) && is_string( $values['href'] ) )
			$like_button->setURL( $values['href'] );

		if ( isset( $values['layout'] ) && is_string( $values['layout'] ) )
			$like_button->setLayout( $values['layout'] );

		if ( isset( $values['show_faces'] ) && ( $values['show_faces'] === true || $values['show_faces'] === 'true' || $values['show_faces'] == 1 ) )
			$like_button->showFaces();

		if ( isset( $values['send'] ) && ( $values['send'] === true || $values['send'] === 'true' || $values['send'] == 1 ) )
			$like_button->includeSendButton();

		if ( isset( $values['width'] ) )
			$like_button->setWidth( absint( $values['width'] ) );

		if ( isset( $values['action'] ) )
			$like_button->setAction( $values['action'] );

		if ( isset( $values['font'] ) )
			$like_button->setFont( $values['font'] );

		if ( isset( $values['colorscheme'] ) )
			$like_button->setColorScheme( $values['colorscheme'] );

		if ( isset( $values['ref'] ) )
			$like_button->setReference( $values['ref'] );

		if ( isset( $values['kid_directed_site'] ) && ( $values['kid_directed_site'] === true || $values['kid_directed_site'] === 'true' || $values['kid_directed_site'] == 1 ) )
			$like_button->isKidDirectedSite();

		return $like_button;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 *
	 * will become data-key="value". Exclude values if default.
	 *
	 * @since 1.1
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		$data = parent::toHTMLDataArray();

		if ( isset( $this->href ) )
			$data['href'] = $this->href;

		if ( isset( $this->layout ) && $this->layout !== 'standard' )
			$data['layout'] = $this->layout;

		if ( isset( $this->send_button ) && $this->send_button === true )
			$data['send'] = 'true';

		if ( isset( $this->show_faces ) && $this->show_faces === true )
			$data['show-faces'] = 'true';

		if ( isset( $this->width ) && is_int( $this->width ) && $this->width > 0 )
			$data['width'] = strval( $this->width );

		if ( isset( $this->action ) && $this->action !== 'like' )
			$data['action'] = $this->action;

		return $data;
	}

	/**
	 * Output Like button with data-* attributes
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
	 * Output Like button as XFBML
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
