<?php
/**
 * Encourage visitors to Like a Facebook Page.
 *
 * Optionally display profile photos of friends who already like your page or latest updates from your page.
 *
 * @since 1.1
 *
 * @link https://developers.facebook.com/docs/plugins/like-box-for-pages/ Like Box social plugin
 */
class Facebook_Like_Box {
	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const ID = 'like-box';

	/**
	 * Minimum allowed width of the Like Box in whole pixels.
	 *
	 * @since 1.1.11
	 *
	 * @var int
	 */
	const MIN_WIDTH = 292;

	/**
	 * Minimum allowed height of the Like Box in whole pixels.
	 *
	 * No faces, no stream.
	 *
	 * @since 1.1.11
	 * @var int
	 */
	const MIN_HEIGHT = 63;

	/**
	 * Use a light or dark color scheme.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $colorscheme_choices = array( 'light' => true, 'dark' => true );

	/**
	 * URL of a Facebook Page. The target of the Like action.
	 *
	 * @since 1.1.11
	 *
	 * @var string
	 */
	protected $href;

	/**
	 * Define a custom width in whole pixels.
	 *
	 * Min: 292. Default: 300.
	 *
	 * @since 1.1.11
	 *
	 * @var int
	 */
	protected $width;

	/**
	 * Define a custom height in whole pixels.
	 *
	 * Default height varies.
	 *
	 * @since 1.1.11
	 *
	 * @var int
	 */
	protected $height;

	/**
	 * Choose a light or dark color scheme to match your site style
	 *
	 * @since 1.1.11
	 *
	 * @param string
	 */
	protected $colorscheme;

	/**
	 * Show faces of the viewer's friends who have already liked the page?
	 *
	 * @since 1.1.11
	 *
	 * @var bool
	 */
	protected $show_faces;

	/**
	 * Display the latest posts from the Facebook Page's wall?
	 *
	 * @since 1.1.11
	 *
	 * @var bool
	 */
	protected $stream;

	/**
	 * Display a Facebook header at the top of the social plugin. e.g. "Find us on Facebook"
	 *
	 * @since 1.1.11
	 *
	 * @var bool
	 */
	protected $header;

	/**
	 * Show a border around the plugin
	 *
	 * Default: true. Set to false to style the resulting iframe with your custom CSS.
	 *
	 * @since 1.5
	 *
	 * @var bool
	 */
	protected $show_border;

	/**
	 * Places-specific features: should the stream contain posts from a Pages' walls instead of checkins by friends?
	 *
	 * @since 1.1.11
	 *
	 * @var bool
	 */
	protected $force_wall;

	/**
	 * I am a Like Box
	 *
	 * @since 1.1
	 * @return string Facebook social plugin name
	 */
	public function __toString() {
		return 'Facebook Like Box';
	}

	/**
	 * Setter for href attribute.
	 *
	 * @since 1.1.11
	 *
	 * @param string $url absolute URL
	 * @return Facebook_Like_Box support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url )
			$this->href = $url;
		return $this;
	}

	/**
	 * Define the width of the Like Box in whole pixels.
	 *
	 * @since 1.1.11
	 *
	 * @param int $width width in whole pixels
	 * @return Facebook_Like_Box support chaining
	 */
	public function setWidth( $width ) {
		if ( is_int( $width ) && $width > 0 ) {
			if ( $width < self::MIN_WIDTH )
				$width = self::MIN_WIDTH;
			$this->width = $width;
		}
		return $this;
	}

	/**
	 * Define the height of the recommendations box in whole pixels.
	 *
	 * @since 1.1
	 *
	 * @param int $height height in whole pixels
	 * @return Facebook_Like_Box support chaining
	 */
	public function setHeight( $height ) {
		if ( is_int( $height ) && $height > 0 ) {
			if ( $height < self::MIN_HEIGHT )
				$height = self::MIN_HEIGHT;
			$this->height = $height;
		}
		return $this;
	}

	/**
	 * Choose a light or dark color scheme.
	 *
	 * @since 1.1.11
	 *
	 * @see self::colorscheme_choices
	 * @param string $color_scheme light|dark
	 * @return Facebook_Like_Box support chaining
	 */
	public function setColorscheme( $color_scheme ) {
		if ( is_string( $color_scheme ) && isset( self::$colorscheme_choices[$color_scheme] ) )
			$this->colorscheme = $color_scheme;
		return $this;
	}

	/**
	 * Show the faces of a logged-on Facebook user's friends.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function showFaces() {
		$this->show_faces = true;
		return $this;
	}

	/**
	 * Do not display the faces of a logged-on Facebook user's friends.
	 *
	 * Reverts to default state.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function hideFaces() {
		$this->show_faces = false;
		return $this;
	}

	/**
	 * Show the Facebook Page stream.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function showStream() {
		$this->stream = true;
		return $this;
	}

	/**
	 * Hide the Facebook header.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function hideStream() {
		$this->stream = false;
		return $this;
	}

	/**
	 * Show the Facebook header.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function showHeader() {
		$this->header = true;
		return $this;
	}

	/**
	 * Hide the Facebook header.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function hideHeader() {
		$this->header = false;
		return $this;
	}

	/**
	 * Add a border to the box
	 *
	 * @since 1.5
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function showBorder() {
		$this->show_border = true;
		return $this;
	}

	/**
	 * Hide the box border.
	 *
	 * @since 1.5
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function hideBorder() {
		$this->show_border = false;
		return $this;
	}

	/**
	 * Place-specific: show latest wall posts instead of checkins.
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function showWall() {
		$this->force_wall = true;
		return $this;
	}

	/**
	 * Place-specific: allow the default state of Place checkins displayed in Page stream.
	 *
	 * The counter-function to showWall()
	 *
	 * @since 1.1.11
	 *
	 * @return Facebook_Like_Box support chaining
	 */
	public function showCheckins() {
		$this->force_wall = false;
		return $this;
	}

	/**
	 * convert an options array into an object.
	 *
	 * @since 1.1.11
	 *
	 * @param array $values associative array
	 * @return Facebook_Like_Box like box object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$like_box = new Facebook_Like_Box();

		if ( isset( $values['href'] ) && is_string( $values['href'] ) )
			$like_box->setURL( $values['href'] );

		if ( isset( $values['width'] ) )
			$like_box->setWidth( absint( $values['width'] ) );

		if ( isset( $values['height'] ) )
			$like_box->setHeight( absint( $values['height'] ) );

		if ( isset( $values['colorscheme'] ) )
			$like_box->setColorscheme( $values['colorscheme'] );

		if ( isset( $values['show_faces'] ) && ( $values['show_faces'] === false || $values['show_faces'] === 'false' || $values['show_faces'] == 0 ) )
			$like_box->hideFaces();
		else
			$like_box->showFaces();

		if ( isset( $values['stream'] ) && ( $values['stream'] === false || $values['stream'] === 'false' || $values['stream'] == 0 ) ) {
			$like_box->hideStream();
		} else {
			$like_box->showStream();
			if ( isset( $values['force_wall'] ) && ( $values['force_wall'] === true || $values['force_wall'] === 'true' || $values['force_wall'] == 1 ) )
				$like_box->showWall();
			else
				$like_box->showCheckins();
		}

		if ( isset( $values['header'] ) && ( $values['header'] === false || $values['header'] === 'false' || $values['header'] == 0 ) )
			$like_box->hideHeader();
		else
			$like_box->showHeader();

		if ( isset( $values['show_border'] ) && ( $values['show_border'] === false || $values['show_border'] === 'false' || $values['show_border'] == 0 ) )
			$like_box->hideBorder();
		else
			$like_box->showBorder();

		return $like_box;
	}

	/**
	 * Convert the class object into an array, removing default values
	 *
	 * @since 1.1.11
	 *
	 * @return array associative array
	 */
	public function toArray() {
		$data = array();

		if ( isset( $this->href ) )
			$data['href'] = $this->href;

		if ( isset( $this->width ) && is_int( $this->width ) && $this->width >= self::MIN_WIDTH && $this->width !== 300 )
			$data['width'] = $this->width;

		if ( isset( $this->height) && is_int( $this->height ) && $this->height >= self::MIN_HEIGHT )
			$data['height'] = $this->height;

		if ( isset( $this->colorscheme ) && $this->colorscheme !== 'light' )
			$data['colorscheme'] = $this->colorscheme;

		if ( isset( $this->show_faces ) ) {
			if ( $this->show_faces === false )
				$data['show-faces'] = 'false';
			else
				$data['show-faces'] = 'true';
		}

		if ( isset( $this->stream ) ) {
			if ( $this->stream === false ) {
				$data['stream'] = 'false';
			} else {
				$data['stream'] = 'true';
				if ( isset( $this->force_wall ) && $this->force_wall === true )
					$data['force-wall'] = 'true';
			}
		}

		if ( isset( $this->header ) && $this->header === false )
			$data['header'] = 'false';

		if ( isset( $this->show_border ) && $this->show_border === false )
			$data['show-border'] = 'false';

		return $data;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 *
	 * Exclude values if default.
	 *
	 * @since 1.1.11
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		return $this->toArray();
	}

	/**
	 * Output Like Box with data-* attributes.
	 *
	 * @since 1.1.11
	 *
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes = array() ) {
		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		$div_attributes = Facebook_Social_Plugin::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = $this->toHTMLDataArray();

		return Facebook_Social_Plugin::div_builder( $div_attributes );
	}

	/**
	 * Output Like Box as XFBML
	 *
	 * @since 1.1.11
	 *
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		if ( ! class_exists( 'Facebook_Social_Plugin' ) )
			require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

		return Facebook_Social_Plugin::xfbml_builder( self::ID, $this->toHTMLDataArray() );
	}
}
?>
