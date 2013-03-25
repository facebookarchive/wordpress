<?php

if ( ! class_exists( 'Facebook_Social_Plugin' ) )
	require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

/**
 * Display recommended pages based on the browsing history, site interactions, and interests of a Facebook user and friends
 *
 * @since 1.1
 * @link https://developers.facebook.com/docs/reference/plugins/recommendations/ Recommendations Box social plugin
 */
class Facebook_Recommendations_Box extends Facebook_Social_Plugin {

	/**
	 * Element and class name used in markup builders
	 *
	 * @since 1.1
	 * @var string
	 */
	const ID = 'recommendations';

	/**
	 * The activity domain. Defaults to the current domain
	 * 
	 * @since 1.1
	 * @var string
	 */
	protected $site;

	/**
	 * Actions to show activities for
	 *
	 * @since 1.1
	 * @var array
	 */
	protected $action = array();

	/**
	 * Display all actions, custom and global, associated with this application
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $app_id;

	/**
	 * Width of the activity feed box in whole pixels
	 *
	 * @since 1.1
	 * @var int
	 */
	protected $width;

	/**
	 * Height of the activity feed box in whole pixels
	 *
	 * @since 1.1
	 * @var int
	 */
	protected $height;

	/**
	 * Show or hide the Facebook header
	 *
	 * @since 1.1
	 * @var bool
	 */
	protected $header;

	/**
	 * Box border color
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $border_color;

	/**
	 * Define the browsing context of followed links
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $linktarget;

	/**
	 * Allowed browsing contexts
	 *
	 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/browsers.html#browsing-context-names Browsing context names
	 * @since 1.1
	 * @var array
	 */
	public static $linktarget_choices = array( '_blank' => true, '_parent' => true, '_top' => true );

	/**
	 * Limit the number of days since article creation
	 *
	 * @since 1.1
	 * @var int
	 */
	protected $max_age;

	/**
	 * I am a recommendation box
	 *
	 * @since 1.1
	 */
	public function __toString() {
		return 'Facebook Recommendations Box';
	}

	/**
	 * Set the domain for which to show activity
     *
     * @since 1.1
     * @param string $domain domain / hostname
     * @return Facebook_Recommendations_Box support chaining
	 */
	public function setSite( $domain ) {
		if ( is_string( $domain ) && $domain )
			$this->site = $domain;
		return $this;
	}

	/**
	 * Scope the activity feed display to an additional action
	 *
	 * @since 1.1
	 * @param string $action action name. global- or app-scoped
	 * @return Facebook_Recommendations_Box support chaining
	 */
	public function addAction( $action ) {
		if ( is_string( $action ) && ! in_array( $action, $this->action, true ) )
			$this->action[] = $action;
		return $this;
	}

	/**
	 * Display actions associated with the specified application
	 *
	 * @since 1.1
	 * @param string $app_id Facebook application identifer
	 * @return Facebook_Recommendations_Box support chaining
	 */
	public function setAppID( $app_id ) {
		if ( is_string( $app_id ) ) {
			if ( function_exists( 'ctype_digit' ) ) {
				if ( ctype_digit( $app_id ) )
					$this->app_id = $app_id;
			} else if ( preg_match( '/^[\d]+$/', $app_id ) ) {
				$this->app_id = $app_id;
			}
		}
		return $this;
	}

	/**
	 * Define the width of the activity feed box in whole pixels
	 *
	 * @since 1.1
	 * @param int $width width in whole pixels
	 * @return Facebook_Recommendations_Box support chaining
	 */
	public function setWidth( $width ) {
		if ( is_int( $width ) && $width > 0 )
			$this->width = $width;
		return $this;
	}

	/**
	 * Define the height of the recommendations box in whole pixels
	 *
	 * @since 1.1
	 * @param int $height height in whole pixels
	 * @return Facebook_Recommendations_Box support chaining
	 */
	public function setHeight( $height ) {
		if ( is_int( $height ) && $height > 0 )
			$this->height = $height;
		return $this;
	}

	/**
	 * Show the Facebook header
	 *
	 * @since 1.1
	 * @return Facebook_Activity_Feed support chaining
	 */
	public function showHeader() {
		$this->header = true;
		return $this;
	}

	/**
	 * Hide the Facebook header
	 *
	 * @since 1.1
	 * @return Facebook_Recommendations_Box support chaining
	 */
	public function hideHeader() {
		$this->header = false;
		return $this;
	}

	/**
	 * Define the border color of the recommendations box
	 *
	 * @since 1.1
	 * @param string $color hex color
	 * @return Facebook_Recommendations_Box support chaining
	 */
	public function setBorderColor( $color ) {
		if ( is_string( $color ) )
			$this->border_color = $color;
		return $this;
	}

	/**
	 * Define a link target to control browser context on link actions
	 *
	 * @since 1.1
	 * @param string $target _blank|_parent|_top
	 * @return Facebook_Activity_Feed support chaining
	 */
	public function setLinkTarget( $target ) {
		if ( is_string( $target ) && isset( self::$linktarget_choices[$target] ) )
			$this->linktarget = $target;
		return $this;
	}

	/**
	 * Limit recommendations to a number of days between 1 and 180
	 * Plugin defaults to no maximum age (age=0)
	 *
	 * @since 1.1
	 * @param int $days number of whole days
	 * @return Facebook_Activity_Feed support chaining
	 */
	public function setMaxAge( $days ) {
		if ( is_int( $days ) && $days > -1 && $days < 181 )
			$this->max_age = $days;
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

		if ( isset( $this->site ) )
			$data['site'] = $this->site;

		if ( isset( $this->action ) && is_array( $this->action ) && ! empty( $this->action ) )
			$data['action'] = implode( ',', $this->action );

		if ( isset( $this->app_id ) )
			$data['app-id'] = $this->app_id;

		if ( isset( $this->width ) && is_int( $this->width ) && $this->width > 0 && $this->width !== 300 ) // default 300
			$data['width'] = strval( $this->width );

		if ( isset( $this->height ) && is_int( $this->height ) && $this->height > 0 && $this->height !== 300 ) // default 300
			$data['height'] = strval( $this->height );

		if ( isset( $this->header ) && $this->header === false ) // default true
			$data['header'] = 'false';
		else
			$data['header'] = 'true';

		if ( isset( $this->border_color ) )
			$data['border-color'] = $this->border_color;

		if ( isset( $this->linktarget ) )
			$data['linktarget'] = $this->linktarget;

		if ( isset( $this->max_age ) && is_int( $this->max_age ) && $this->max_age > 0 && $this->max_age < 181 ) // default 0
			$data['max-age'] = strval( $this->max_age );

		return $data;
	}

	/**
	 * convert an options array into an object
	 *
	 * @since 1.1
	 * @param array $values associative array
	 * @return Facebook_Recommendations_Box recommendations box object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$box = new Facebook_Recommendations_Box();

		if ( isset( $values['site'] ) )
			$box->setSite( $values['site'] );

		if ( isset( $values['action'] ) ) {
			if ( is_string( $values['action'] ) ) {
				$box->addAction( $values['action'] );
			} else if ( is_array( $values['action'] ) ) {
				foreach( $values['action'] as $action ) {
					$box->addAction( $action );
				}
			}
		}

		if ( isset( $values['app_id'] ) )
			$box->setAppID( $values['app_id'] );

		if ( isset( $values['width'] ) )
			$box->setWidth( absint( $values['width'] ) );

		if( isset( $values['height'] ) )
			$box->setHeight( absint( $values['height'] ) );

		if ( isset( $values['header'] ) && ( $values['header'] === true || $values['header'] == 1 || $values['header'] === 'true' ) )
			$box->showHeader();
		else
			$box->hideHeader();

		if ( isset( $values['border_color'] ) )
			$box->setBorderColor( $values['border_color'] );

		if ( isset( $values['linktarget'] ) )
			$box->setLinkTarget( $values['linktarget'] );

		if ( isset( $values['max_age'] ) )
			$box->setMaxAge( absint( $values['max_age'] ) );

		if ( isset( $values['font'] ) )
			$box->setFont( $values['font'] );

		if ( isset( $values['colorscheme'] ) )
			$box->setColorScheme( $values['colorscheme'] );

		if ( isset( $values['ref'] ) )
			$box->setReference( $values['ref'] );

		return $box;
	}

	/**
	 * Output Recommendations Box div with data-* attributes
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