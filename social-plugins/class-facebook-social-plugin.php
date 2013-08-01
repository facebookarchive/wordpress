<?php

/**
 * Properties and functions common across all Facebook social plugins
 *
 * @since 1.1
 */
class Facebook_Social_Plugin {

	/**
	 * Customize a font displayed in the button to match your site style
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $font;

	/**
	 * The font to display in the button
	 *
	 * @since 1.1
	 * @var array
	 */
	public static $font_choices = array( 'arial' => true, 'lucida grande' => true, 'segoe ui' => true, 'tahoma' => true, 'trebuchet ms' => true, 'verdana' => true );

	/**
	 * Choose a light or dark color scheme to match your site style
	 *
	 * @since 1.1
	 * @param string
	 */
	protected $colorscheme;

	/**
	 * Use a light or dark color scheme
	 *
	 * @since 1.1
	 * @var array
	 */
	public static $colorscheme_choices = array( 'light' => true, 'dark' => true );

	/**
	 * Add a unique reference to track referrals. Facebook passes this parameter to the destination URL when a Facebook user clicks the link.
	 * Example: 'footer' for a like button in your footer vs. 'banner' for a button in your site banner
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $ref;

	/**
	 * Is your website primarily directed to children in the United States under the age of 13?
	 * @link https://developers.facebook.com/docs/plugins/restrictions/ Child-directed sites and services
	 *
	 * @since 1.5
	 * @var bool
	 */
	protected $kid_directed_site;

	/**
	 * Choose a font to match your site styling
	 *
	 * @since 1.1
	 * @see self::$font_choices
	 * @param string $font a font name from $font_choices
	 * @return Facebook_Social_Plugin support chaining
	 */
	public function setFont( $font ) {
		if ( is_string( $font ) && isset( self::$font_choices[$font] ) )
			$this->font = $font;
		return $this;
	}

	/**
	 * Choose a light or dark color scheme
	 *
	 * @since 1.1
	 * @see self::colorscheme_choices
	 * @param string $color_scheme light|dark
	 * @return Facebook_Social_Plugin support chaining
	 */
	public function setColorScheme( $color_scheme ) {
		if ( is_string( $color_scheme ) && isset( self::$colorscheme_choices[$color_scheme] ) )
			$this->colorscheme = $color_scheme;
		return $this;
	}

	/**
	 * Clean up the ref paramter based on Facebook requirements
	 *
	 * @since 1.1
	 * @param string $ref reference string you would like to track on your site after a Facebook visitor follows a link
	 * @return string cleaned string
	 */
	public static function cleanRef( $ref ) {
		if ( is_string( $ref ) && $ref && strlen( $ref ) < 50 )
			return preg_replace( '/[^a-zA-Z0-9\+\/\=\-.\:\_]/', '', $ref );
		return '';
	}

	/**
	 * Track referrals from Facebook with a string up to 50 chracters.
	 * Characters in string must be alphanumeric or punctuation (currently +/=-.:_)
	 *
	 * @since 1.1
	 * @param string $ref reference string
	 * @return Facebook_Social_Plugin support chaining
	 */
	public function setReference( $ref ) {
		$ref = self::cleanRef( $ref );
		if ( $ref )
			$this->ref = $ref;
		return $this;
	}

	/**
	 * Inform Facebook a social plugin will likely be displayed to a child in the United States under the age of 13
	 *
	 * @since 1.5
	 * @return Facebook_Social_Plugin support chaining
	 */
	public function isKidDirectedSite() {
		$this->kid_directed_site = true;
		return $this;
	}

	/**
	 * Convert the class object into an array, removing default values
	 *
	 * @return array associative array
	 */
	public function toArray() {
		$data = array();

		if ( isset( $this->font ) )
			$data['font'] = $this->font;

		if ( isset( $this->colorscheme ) && $this->colorscheme !== 'light' )
			$data['colorscheme'] = $this->colorscheme;

		if ( isset( $this->ref ) )
			$data['ref'] = $this->ref;

		if ( isset( $this->kid_directed_site ) && $this->kid_directed_site === true )
			$data['kid_directed_site'] = true;

		return $data;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 * will become data-key="value"
	 * Exclude values if default
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		$data = $this->toArray();

		// underscores to data-* dashes
		if ( isset( $data['kid_directed_site'] ) ) {
			$data['kid-directed-site'] = $data['kid_directed_site'];
			unset( $data['kid_directed_site'] );
		}

		return $this->toArray();
	}

	/**
	 * Add a class required by the social plugin to an existing set of attributes
	 *
	 * @since 1.1
	 * @param string $class class name to add to the attributes array
	 * @param array $attributes existing attributes array
	 * @return array attributes array with social plugin class
	 */
	public static function add_required_class( $class, $attributes = array() ) {
		if ( ! is_array( $attributes ) )
			$attributes = array();

		if ( ! is_string( $class ) )
			return $attributes;

		if ( isset( $attributes['class'] ) && is_array( $attributes['class'] ) ) {
			if ( ! in_array( $class, $attributes['class'] ) )
				$attributes['class'][] = $class;
		} else {
			$attributes['class'] = array( $class );
		}

		return $attributes;
	}

	/**
	 * Output div element with data-* attributes
	 *
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes. social plugin parameters in data.
	 * @return string HTML div or empty string
	 */
	public static function div_builder( $div_attributes=array() ) {
		if ( ! ( is_array( $div_attributes ) && ! empty( $div_attributes ) ) )
			return '';

		$div = '<div';

		// basics + customizations
		if ( isset( $div_attributes['class'] ) && is_array( $div_attributes['class'] ) ) {
			$classes = array();
			foreach ( $div_attributes['class'] as $class ) {
				$class = sanitize_html_class( $class );
				if ( $class && ! in_array( $class, $classes, true ) )
					$classes[] = $class;
			}
			if ( ! empty( $classes ) )
				$div .= ' class="' . implode( ' ', $classes ) . '"';
			unset( $classes );
		}
		if ( isset( $div_attributes['id'] ) && is_string( $div_attributes['id'] ) )
			$div .= ' id="' . esc_attr( $div_attributes['id'] ) . '"';
		if ( isset( $div_attributes['style'] ) && is_string( $div_attributes['style'] ) )
			$div .= ' style="' . esc_attr( $div_attributes['style'] ) . '"';

		// add data-* attributes
		if ( is_array( $div_attributes['data'] ) && ! empty( $div_attributes['data'] ) ) {
			foreach( $div_attributes['data'] as $attribute => $value ) {
				$div .= ' data-' . $attribute . '="' . esc_attr( $value ) . '"';
			}
		}

		$div .= '></div>';
		return $div;
	}

	/**
	 * Output XFBML element with attributes
	 *
	 * @since 1.1
	 * @param string $element name of the element, e.g. "like" for fb:like
	 * @return string XFBML element or empty string
	 */
	public static function xfbml_builder( $element, $data=array() ) {
		if ( ! ( is_string( $element ) && $element && is_array( $data ) && ! empty( $data ) ) )
			return '';
		$element = sanitize_html_class( $element );

		// add XMLNS applicable to the current element and its children for compatibility
		$fb = '<fb:' . $element . ' xmlns="http://ogp.me/ns/fb#"';

		foreach( $data as $attribute => $value ) {
			$fb .= ' ' . str_replace( '-', '_', $attribute ) . '="' . esc_attr( $value ) . '"';
		}

		$fb .= '></fb:' . $element . '>';

		return $fb;
	}
}

?>
