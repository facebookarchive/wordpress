<?php

if ( ! class_exists( 'Facebook_Recommendations_Box' ) )
	require_once( dirname(__FILE__) . '/class-facebook-recommendations-box.php' );

/**
 * Display activity happening on your site including likes and comments in a social context
 *
 * @since 1.0.3
 * @link https://developers.facebook.com/docs/reference/plugins/activity/ Activity Feed social plugin documentation
 */
class Facebook_Activity_Feed extends Facebook_Recommendations_Box {

	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.0.3
	 * @var string
	 */
	const id = 'activity';

	/**
	 * Include recommendations?
	 *
	 * @since 1.0.3
	 * @var bool
	 */
	protected $recommendations;

	/**
	 * Filter which URLs are shown in the plugin
	 * Up to two path directories: e.g. /section1/section2
	 *
	 * @since 1.0.3
	 * @var string
	 */
	protected $filter;

	/**
	 * I am an activity feed
	 *
	 * @since 1.0.3
	 */
	public function __toString() {
		return 'Facebook Activity Feed';
	}

	/**
	 * Always show recommendations?
	 *
	 * @since 1.0.3
	 * @return Facebook_Activity_Feed support chaining
	 */
	public function includeRecommendations() {
		$this->recommendations = true;
		return $this;
	}

	/**
	 * Filter which URLs are shown in the plugin by including a directory path
	 * Facebook will parse up to two directories deep: e.g. /section1/section2
	 * Does not apply to recommendations
	 *
	 * @since 1.0.3
	 * @var string $path URL path in current site
	 * @return Facebook_Activity_Feed support chaining
	 */
	public function setFilter( $path ) {
		if ( is_string( $path ) ) {
			$path = parse_url( $path, PHP_URL_PATH );
			if ( $path )
				$this->filter = $path;
		}
		return $this;
	}

	/**
	 * Convert the class to data-* attribute friendly associative array
	 * will become data-key="value"
	 * Exclude values if default
	 *
	 * @return array associative array
	 */
	public function toHTMLDataArray() {
		$data = parent::toHTMLDataArray();

		if ( isset( $this->recommendations ) && $this->recommendations === true )
			$data['recommendations'] = 'true';

		if ( isset( $this->filter ) )
			$data['filter'] = $this->filter;

		return $data;
	}

	/**
	 * convert an options array into an object
	 *
	 * @since 1.0.3
	 * @param array $values associative array
	 * @return Facebook_Activity_Feed activity feed object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$feed = new Facebook_Activity_Feed();

		if ( array_key_exists( 'site', $values ) )
			$feed->setSite( $values['site'] );

		if ( array_key_exists( 'action', $values ) ) {
			if ( is_string( $values['action'] ) ) {
				$feed->addAction( $values['action'] );
			} else if ( is_array( $values['action'] ) ) {
				foreach( $values['action'] as $action ) {
					$feed->addAction( $action );
				}
			}
		}

		if ( array_key_exists( 'app_id', $values ) )
			$feed->setAppID( $values['app_id'] );

		if ( array_key_exists( 'width', $values ) )
			$feed->setWidth( absint( $values['width'] ) );

		if( array_key_exists( 'height', $values ) )
			$feed->setHeight( absint( $values['height'] ) );

		if ( array_key_exists( 'header', $values ) && ( $values['header'] === false || $values['header'] === 0 || $values['header'] === 'false' ) )
			$feed->hideHeader();
		else
			$feed->showHeader();

		if ( array_key_exists( 'border_color', $values ) )
			$feed->setBorderColor( $values['border_color'] );

		if ( array_key_exists( 'recommendations', $values ) && ( $values['recommendations'] == true || $values['recommendations'] === 1 || $values['recommendations'] === 'true' ) )
			$feed->includeRecommendations();

		if ( array_key_exists( 'filter', $values ) )
			$feed->setFilter( $values['filter'] );

		if ( array_key_exists( 'linktarget', $values ) )
			$feed->setLinkTarget( $values['linktarget'] );

		if ( array_key_exists( 'max_age', $values ) )
			$feed->setMaxAge( absint( $values['max_age'] ) );

		if ( array_key_exists( 'font', $values ) )
			$feed->setFont( $values['font'] );

		if ( array_key_exists( 'colorscheme', $values ) )
			$feed->setColorScheme( $values['colorscheme'] );

		if ( array_key_exists( 'ref', $values ) )
			$feed->setReference( $values['ref'] );

		return $feed;
	}

	/**
	 * Output Activity Feed div with data-* attributes
	 *
	 * @since 1.0.3
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes=array() ) {
		$div_attributes = self::add_required_class( 'fb-' . self::id, $div_attributes );
		$div_attributes['data'] = $this->toHTMLDataArray();

		return self::div_builder( $div_attributes );
	}

	/**
	 * Output Activity Feed as XFBML
	 *
	 * @since 1.0.3
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		return self::xfbml_builder( self::id, $this->toHTMLDataArray() );
	}
}

?>