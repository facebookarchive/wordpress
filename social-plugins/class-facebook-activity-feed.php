<?php

if ( ! class_exists( 'Facebook_Recommendations_Box' ) )
	require_once( dirname(__FILE__) . '/class-facebook-recommendations-box.php' );

/**
 * Display activity happening on your site including likes and comments in a social context
 *
 * @since 1.1
 * @link https://developers.facebook.com/docs/reference/plugins/activity/ Activity Feed social plugin documentation
 */
class Facebook_Activity_Feed extends Facebook_Recommendations_Box {

	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.1
	 * @var string
	 */
	const ID = 'activity';

	/**
	 * Include recommendations?
	 *
	 * @since 1.1
	 * @var bool
	 */
	protected $recommendations;

	/**
	 * Filter which URLs are shown in the plugin
	 * Up to two path directories: e.g. /section1/section2
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $filter;

	/**
	 * I am an activity feed
	 *
	 * @since 1.1
	 */
	public function __toString() {
		return 'Facebook Activity Feed';
	}

	/**
	 * Always show recommendations?
	 *
	 * @since 1.1
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
	 * @since 1.1
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
	 * @since 1.1
	 * @param array $values associative array
	 * @return Facebook_Activity_Feed activity feed object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$feed = new Facebook_Activity_Feed();

		if ( isset( $values['site'] ) )
			$feed->setSite( $values['site'] );

		if ( isset( $values['action'] ) ) {
			if ( is_string( $values['action'] ) ) {
				$feed->addAction( $values['action'] );
			} else if ( is_array( $values['action'] ) ) {
				foreach( $values['action'] as $action ) {
					$feed->addAction( $action );
				}
			}
		}

		if ( isset( $values['app_id'] ) )
			$feed->setAppID( $values['app_id'] );

		if ( isset( $values['width'] ) )
			$feed->setWidth( absint( $values['width'] ) );

		if( isset( $values['height'] ) )
			$feed->setHeight( absint( $values['height'] ) );

		if ( isset( $values['header'] ) && ( $values['header'] === true || $values['header'] == 1 || $values['header'] === 'true' ) )
			$feed->showHeader();
		else
			$feed->hideHeader();

		if ( isset( $values['border_color'] ) )
			$feed->setBorderColor( $values['border_color'] );

		if ( isset( $values['recommendations'] ) && ( $values['recommendations'] == true || $values['recommendations'] == 1 || $values['recommendations'] === 'true' ) )
			$feed->includeRecommendations();

		if ( isset( $values['filter'] ) )
			$feed->setFilter( $values['filter'] );

		if ( isset( $values['linktarget'] ) )
			$feed->setLinkTarget( $values['linktarget'] );

		if ( isset( $values['max_age'] ) )
			$feed->setMaxAge( absint( $values['max_age'] ) );

		if ( isset( $values['font'] ) )
			$feed->setFont( $values['font'] );

		if ( isset( $values['colorscheme'] ) )
			$feed->setColorScheme( $values['colorscheme'] );

		if ( isset( $values['ref'] ) )
			$feed->setReference( $values['ref'] );

		return $feed;
	}

	/**
	 * Output Activity Feed div with data-* attributes
	 *
	 * @since 1.1
	 * @param array $div_attributes associative array. customize the returned div with id, class, or style attributes
	 * @return HTML div or empty string
	 */
	public function asHTML( $div_attributes=array() ) {
		$div_attributes = self::add_required_class( 'fb-' . self::ID, $div_attributes );
		$div_attributes['data'] = $this->toHTMLDataArray();

		return self::div_builder( $div_attributes );
	}

	/**
	 * Output Activity Feed as XFBML
	 *
	 * @since 1.1
	 * @return string XFBML markup
	 */
	public function asXFBML() {
		return self::xfbml_builder( self::ID, $this->toHTMLDataArray() );
	}
}

?>