<?php

if ( ! class_exists( 'Facebook_Social_Plugin' ) )
	require_once( dirname(__FILE__) . '/class-facebook-social-plugin.php' );

/**
 * Recommend content to site visitors based on site activity, interests, and a friends
 *
 * @since 1.1
 *
 * @link https://developers.facebook.com/docs/plugins/recommendations-bar/ Recommendations Bar social plugin documentation
 */
class Facebook_Recommendations_Bar extends Facebook_Social_Plugin {

	/**
	 * Element and class name used in markup builders.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const ID = 'recommendations-bar';

	/**
	 * Override the URL used for the like action.
	 *
	 * Default is og:url or link[rel=canonical] or document.URL.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $href;

	/**
	 * Choose when the plugin expands.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $trigger;

	/**
	 * Number of seconds to wait before expanding the plugin.
	 *
	 * @since 1.1
	 *
	 * @var int
	 */
	protected $read_time;

	/**
	 * Like or recommend.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * The verb to display on the button.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $action_choices = array( 'like' => true, 'recommend' => true );

	/**
	 * Display the recommendations bar on the left or right side.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $side;

	/**
	 * Choose to display the recommendations bar on the left or right side.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	public static $side_choices = array( 'left' => true, 'right' => true );

	/**
	 * One or more domains to show recommendations for.
	 *
	 * @since 1.1
	 *
	 * @var array
	 */
	protected $site = array();

	/**
	 * Number of recommendations to display.
	 *
	 * @since 1.1
	 *
	 * @var int
	 */
	protected $num_recommendations;

	/**
	 * Only include articles newer than a given number of days.
	 *
	 * @since 1.1
	 *
	 * @var int
	 */
	protected $max_age;

	/**
	 * I am a recommendations bar.
	 *
	 * @since 1.1
	 *
	 * @return string Facebook social plugin name
	 */
	public function __toString() {
		return 'Facebook Recommendations Bar';
	}

	/**
	 * Setter for href attribute.
	 *
	 * @since 1.1
	 *
	 * @param string $url absolute URL
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setURL( $url ) {
		$url = esc_url_raw( $url, array( 'http', 'https' ) );
		if ( $url )
			$this->href = $url;
		return $this;
	}

	/**
	 * Choose when the plugin expands.
	 *
	 * Evaluated in addition to the read_time parameter.
	 *
	 * @since 1.1
	 *
	 * @param string $trigger onvisible|manual|X%
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setTrigger( $trigger ) {
		if ( is_string( $trigger ) && $trigger ) {
			if ( $trigger === 'onvisible' || $trigger === 'manual' ) {
				$this->trigger = $trigger;
			} else {
				$len = strlen( $trigger );
				if ( $len > 1 && $len < 5 && substr( $trigger, -1 ) === '%' ) { // 2% - 100%
					$pct = absint( substr( $trigger, 0, $len - 1 ) );
					if ( $pct > 0 && $pct < 101 ) // positive integer less than or equal to 100
						$this->trigger = strval( $pct ) . '%';
				}
			}
		}
		return $this;
	}

	/**
	 * Set the number of seconds before the plugin will expand.
	 *
	 * Minimum: 10 seconds
	 *
	 * @since 1.1
	 * @param int $seconds whole seconds
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setReadTime( $seconds ) {
		if ( is_int( $seconds ) && $seconds >= 10 )
			$this->read_time = $seconds;
		return $this;
	}

	/**
	 * Override the default "like" text with "recommend"
	 *
	 * @since 1.1
	 *
	 * @param string $action like|recommend
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setAction( $action ) {
		if ( is_string( $action ) && isset( self::$action_choices[$action] ) )
			$this->action = $action;
		return $this;
	}

	/**
	 * Display plugin on the left or right side.
	 *
	 * By default the recommendations bar will display at the end of a normal page scan based on the locale (e.g. right side in the left-to-right reading style of English)
	 *
	 * @since 1.1
	 * @param string $side left|right
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setSide( $side ) {
		if ( is_string( $side ) && isset( self::$side_choices[$side] ) )
			$this->side = $side;
		return $this;
	}

	/**
	 * Show recommendations for an additional domain
	 *
	 * @since 1.1
	 *
	 * @param string $domain domain name
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function addSite( $domain ) {
		if ( is_string( $domain ) && $domain && ! in_array( $domain, $this->site, true ) )
			$this->site[] = $domain;
		return $this;
	}

	/**
	 * Set the number of recommendations to display
	 *
	 * Accepts a number between 1 and 5
	 *
	 * @since 1.1
	 *
	 * @param int $num number of recommendations. between 1 and 5
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setNumRecommendations( $num ) {
		if ( is_int( $num ) && $num > 0 && $num < 6 )
			$this->num_recommendations = $num;
		return $this;
	}

	/**
	 * Limit recommendations to a number of days between 1 and 180
	 *
	 * Plugin defaults to no maximum age (age=0)
	 *
	 * @since 1.1
	 *
	 * @param int $days number of whole days
	 * @return Facebook_Recommendations_Bar support chaining
	 */
	public function setMaxAge( $days ) {
		if ( is_int( $days ) && $days >= 0 && $days < 181 )
			$this->max_age = $days;
		return $this;
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

		if ( isset( $this->trigger ) )
			$data['trigger'] = $this->trigger;

		if ( isset( $this->read_time ) && $this->read_time !== 30 ) // default: 30
			$data['read-time'] = strval( $this->read_time );

		if ( isset( $this->action ) && $this->action !== 'like' ) // default: like
			$data['action'] = $this->action;

		if ( isset( $this->side ) ) // default: auto
			$data['side'] = $this->side;

		if ( isset( $this->site ) && is_array( $this->site ) && ! empty( $this->site ) )
			$data['site'] = implode( ',', $this->site );

		if ( isset( $this->num_recommendations ) && $this->num_recommendations != 2 ) // default: 2
			$data['num-recommendations'] = strval( $this->num_recommendations );

		if ( isset( $this->max_age ) && $this->max_age != 0 ) // default: 0 (no limit)
			$data['max-age'] = $this->max_age;

		// remove generic social plugin properties not applicable to recommendations bar
		foreach( array( 'font', 'colorscheme' ) as $prop ) {
			unset( $data[$prop] );
		}

		return $data;
	}

	/**
	 * convert an options array into an object.
	 *
	 * @since 1.1
	 *
	 * @param array $values associative array
	 * @return Facebook_Recommendations_Bar recommendations bar object
	 */
	public static function fromArray( $values ) {
		if ( ! is_array( $values ) || empty( $values ) )
			return;

		$bar = new Facebook_Recommendations_Bar();

		if ( isset( $values['href'] ) )
			$bar->setURL( $values['href'] );

		if ( isset( $values['trigger'] ) )
			$bar->setTrigger( $values['trigger'] );

		if ( isset( $values['read_time'] ) )
			$bar->setReadTime( absint( $values['read_time'] ) );

		if ( isset( $values['action'] ) )
			$bar->setAction( $values['action'] );

		if ( isset( $values['side'] ) )
			$bar->setSide( $values['side'] );

		if ( isset( $values['site'] ) ) {
			if ( is_string( $values['site'] ) ) {
				$bar->addAction( $values['site'] );
			} else if ( is_array( $values['site'] ) ) {
				foreach( $values['site'] as $action ) {
					$bar->addAction( $action );
				}
			}
		}

		if ( isset( $values['num_recommendations'] ) )
			$bar->setNumRecommendations( absint( $values['num_recommendations'] ) );

		if ( isset( $values['max_age'] ) )
			$bar->setMaxAge( absint( $values['max_age'] ) );

		if ( isset( $values['ref'] ) )
			$bar->setReference( $values['ref'] );

		return $bar;
	}

	/**
	 * Output Recommendations Box div with data-* attributes.
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
	 * Output Activity Feed as XFBML.
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