<?php

if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
	require_once( dirname(__FILE__) . '/settings-social-plugin.php' );

class Facebook_Recommendations_Bar_Settings extends Facebook_Social_Plugin_Settings {

	/**
	 * Setting page identifier
	 *
	 * @since 1.1
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-recommendations-bar';

	/**
	 * Define our option array value
	 *
	 * @since 1.1
	 * @var string
	 */
	const OPTION_NAME = 'facebook_recommendations_bar';

	/**
	 * Initialize with an options array
	 *
	 * @since 1.1
	 * @param array $options existing options
	 */
	public function __construct( $options = array() ) {
		if ( is_array( $options ) && ! empty( $options ) )
			$this->existing_options = $options;
		else
			$this->existing_options = array();
	}

	/**
	 * Reference the social plugin by name
	 *
	 * @since 1.1
	 * @return string social plugin name
	 */
	public static function social_plugin_name() {
		return __( 'Recommendations Bar', 'facebook' );
	}

	/**
	 * Evaluate the Facebook_Recommendations_Bar class file if it is not already loaded
	 *
	 * @since 1.1
	 */
	public static function require_recommendations_bar_builder() {
		if ( ! class_exists( 'Facebook_Recommendations_Bar' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-recommendations-bar.php' );
	}

	/**
	 * Navigate to the settings page through the Facebook top-level menu item
	 *
	 * @since 1.1
	 * @uses add_submenu_page()
	 * @param string $parent_slug Facebook top-level menu item slug
	 * @return string submenu hook suffix
	 */
	public static function add_submenu_item( $parent_slug ) {
		$recommendations_bar_settings = new Facebook_Recommendations_Bar_Settings();

		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( &$recommendations_bar_settings, 'settings_page' )
		);

		if ( $hook_suffix ) {
			$recommendations_bar_settings->hook_suffix = $hook_suffix;
			register_setting( $hook_suffix, self::OPTION_NAME, array( 'Facebook_Recommendations_Bar_Settings', 'sanitize_options' ) );
			add_action( 'load-' . $hook_suffix, array( &$recommendations_bar_settings, 'onload' ) );
		}

		return $hook_suffix;
	}

	/**
	 * Load stored options and scripts on settings page view
	 *
	 * @since 1.1
	 */
	public function onload() {
		$options = get_option( self::OPTION_NAME );
		if ( ! is_array( $options ) )
			$options = array();
		$this->existing_options = $options;

		$this->settings_api_init();
	}	

	/**
	 * Load the page
	 *
	 * @since 1.1
	 */
	public function settings_page() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		Facebook_Settings::settings_page_template( $this->hook_suffix, sprintf( __( '%s Settings', 'facebook' ), self::social_plugin_name() ) );
	}

	/**
	 * Hook into the settings API
	 *
	 * @since 1.1
	 * @uses add_settings_section()
	 * @uses add_settings_field()
	 * @param string $options_group target grouping
	 */
	private function settings_api_init() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		$section = 'facebook-recommendations-bar';
		add_settings_section(
			$section,
			'', // no title for main section
			array( &$this, 'section_header' ),
			$this->hook_suffix
		);

		// when, where
		add_settings_field(
			'facebook-recommendations-bar-show-on',
			__( 'Show on', 'facebook' ),
			array( &$this, 'display_show_on' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-recommendations-bar-side',
			__( 'Side', 'facebook' ),
			array( &$this, 'display_side' ),
			$this->hook_suffix,
			$section
		);

		// social plugin fields
		add_settings_field(
			'facebook-recommendations-bar-action',
			__( 'Action', 'facebook' ),
			array( &$this, 'display_action' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-recommendations-bar-trigger',
			__( 'Trigger', 'facebook' ),
			array( &$this, 'display_trigger' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-recommendations-bar-read-time',
			__( 'Read time', 'facebook' ),
			array( &$this, 'display_read_time' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-recommendations-bar-read-time' )
		);
		add_settings_field(
			'facebook-recommendations-bar-num-recommendations',
			__( 'Number of recommendations', 'facebook' ),
			array( &$this, 'display_num_recommendations' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-recommendations-bar-num-recommendations' )
		);
		add_settings_field(
			'facebook-recommendations-bar-max-age',
			__( 'Maximum age', 'facebook' ),
			array( &$this, 'display_max_age' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-recommendations-bar-max-age' )
		);
	}

	/**
	 * Introduce publishers to the Recommendations Bar social plugin
	 *
	 * @since 1.1
	 */
	public function section_header() {
		echo '<p>' . esc_html( __( 'Encourage additional pageviews with site recommendations based on social context.', 'facebook' ) ) . ' ' . esc_html( sprintf( __( 'Adds a %s overlay to the bottom of your page with an expanded list of recommendations triggered by time or position on the page.', 'facebook' ), __( 'Like Button', 'facebook' ) ) ) . '<br /><a href="https://developers.facebook.com/docs/reference/plugins/recommendationsbar/" title="' . esc_attr( sprintf( __( '%s social plugin documentation', 'facebook' ), 'Facebook ' . self::social_plugin_name() ) ) . '">' . esc_html( __( 'Read more...', 'facebook' ) ) . '</a></p>';
	}

	/**
	 * Where should the button appear?
	 *
	 * @since 1.1
	 */
	public function display_show_on() {
		echo '<fieldset id="facebook-recommendations-bar-show-on">' . self::show_on_choices( self::OPTION_NAME . '[show_on]', self::get_display_conditionals_by_feature( 'recommendations_bar', 'all' ) ) . '</fieldset>';

		echo '<p class="description">' . esc_html( self::show_on_description( self::social_plugin_name() ) ) . '</p>';
	}

	/**
	 * Choose to display the recommendations bar on the left or right side
	 *
	 * @since 1.1
	 */
	public function display_side() {
		$key = 'side';

		self::require_recommendations_bar_builder();

		if ( isset( $this->existing_options[$key] ) && in_array( $this->existing_options[$key], Facebook_Recommendations_Bar::$side_choices, true ) )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = 'right';

		$choices = array();
		foreach ( Facebook_Recommendations_Bar::$side_choices as $side ) {
			$choices[] = '<label><input type="radio" name="' . self::OPTION_NAME . '[' . $key . ']" value="' . $side . '"' . checked( $existing_value, $side, false ) . ' /> ' . esc_html( __( $side, 'facebook' ) ) . '</label>';
		}
		echo '<fieldset id="facebook-recommendations-bar-' . $key . '">' . implode( ' ', $choices ) . '</fieldset>';

		echo '<p class="description">' . esc_html( sprintf( __( 'The side of the screen where the %s will be displayed.', 'facebook' ), self::social_plugin_name() ) ) . '</p>';
	}

	/**
	 * Choose action text.
	 *
	 * @since 1.1
	 */
	public function display_action() {
		$key = 'action';
		$name = self::OPTION_NAME . '[' . $key . ']';

		self::require_recommendations_bar_builder();

		if ( isset( $this->existing_options[$key] ) && in_array( $this->existing_options[$key], Facebook_Recommendations_Bar::$action_choices ) )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = 'like';

		$fields = array();
		foreach( Facebook_Recommendations_Bar::$action_choices as $action ) {
			$fields[] = '<label><input type="radio" name="' . $name . '" value="' . $action . '"' . checked( $action, $existing_value, false ) . ' /> ' . esc_html( __( $action, 'facebook' ) ) . '</label>';
		}

		echo '<fieldset id="facebook-recommendations-bar-' . $key . '">' . implode( ' ', $fields ) . '</fieldset>';
		echo '<p class="description">' . esc_html( sprintf( __( 'Action verb displayed in the %s.', 'facebook' ), __( 'Like Button', 'facebook' ) ) ) . '</p>';
	}

	/**
	 * What page progression should trigger the recommendations bar?
	 *
	 * @since 1.1
	 */
	public function display_trigger() {
		$key = 'trigger';
		$name = self::OPTION_NAME . '[' . $key . ']';

		if ( isset( $this->existing_options[$key] ) )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = 'onvisible';

		if ( $existing_value && substr( $existing_value, -1 ) === '%' ) {
			$pct = absint( substr( $existing_value, 0, strlen( $existing_value ) - 1 ) );
			if ( $pct > 0 && $pct < 101 ) // positive integer less than or equal to 100
				$existing_value = $pct;
			unset( $pct );
		}

		echo '<fieldset id="facebook-recommendations-bar-' . $key . '">';
		echo '<div><label><input type="radio" name="' . $name . '" value="onvisible"' . checked( $existing_value, 'onvisible', false ) . ' /> onvisible — ' . esc_html( __( 'Bottom of page for home & archive pages, after post for single post types.', 'facebook' ) ) . '</label></div>';

		// select the X% option then provide a % number
		echo '<div><input type="radio" name="' . $name . '" value="pct"' . checked( is_int( $existing_value ), true, false ) . ' /> ';
		echo '<label>' . sprintf( esc_html( __( 'After a visitor scrolls through %s of the total page height.', 'facebook' ) ), '<input type="number" name="' . self::OPTION_NAME . '[' . $key . '_pct]" size="3" maxlength="3" min="1" max="100" step="1" value="' . ( is_int( $existing_value ) ? $existing_value : 50 ) . '" title="' . esc_attr( __( 'Percentage of total page height expressed as a whole positive number.', 'facebook' ) ) . '" />%' ) . '</label></div>';

		// advanced users: set manually
		echo '<div><label><input type="radio" name="' . $name . '" value="manual"' . checked( $existing_value, 'manual', false ) . ' /> manual — ' . sprintf( esc_html( __( 'I will call the %s function manually from my site JavaScript.', 'facebook' ) ), '<code class="javascript">FB.XFBML.RecommendationsBar.markRead</code>' ) . '</label></div>';

		echo '</fieldset>';
	}

	/**
	 * Trigger the recommendations bar after a given number of seconds
	 *
	 * @since 1.1
	 */
	public function display_read_time() {
		$key = 'read_time';

		if ( isset( $this->existing_options[$key] ) )
			$existing_value = absint( $this->existing_options[$key] );

		// enforce minimum. reset to default
		if ( ! isset( $existing_value ) || $existing_value < 10 )
			$existing_value = 30;

		echo '<input type="number" name="' . self::OPTION_NAME . '[' . $key . ']" id="facebook-recommendations-bar-read-time" size="3" min="10" step="1" value="' . $existing_value . '" /> ' . esc_html( __( 'seconds', 'facebook' ) ) . '';
		echo '<p class="description">' . esc_html( __( 'Number of seconds before the plugin will expand', 'facebook' ) ) . '</p>';
	}

	/**
	 * Maximum number of recommendations displayed
	 *
	 * @since 1.1
	 */
	public function display_num_recommendations() {
		$key = 'num_recommendations';

		if ( isset( $this->existing_options[$key] ) )
			$existing_value = absint( $this->existing_options[$key] );

		// enforce minimum. reset to default
		if ( ! isset( $existing_value ) || $existing_value < 1 || $existing_value > 5 )
			$existing_value = 2;

		echo '<input type="number" name="' . self::OPTION_NAME . '[' . $key . ']" id="facebook-recommendations-bar-num-recommendations" size="1" maxlength="1" min="1" max="5" step="1" value="' . $existing_value . '" /> ' . _n( 'maximum recommendation', 'maximum recommendations', $existing_value, 'facebook' );
	}

	/**
	 * Maximum age of a recommended article
	 *
	 * @since 1.1
	 */
	public function display_max_age() {
		$key = 'max_age';

		if ( isset( $this->existing_options[$key] ) )
			$existing_value = absint( $this->existing_options[$key] );

		// enforce minimum. reset to default
		if ( ! isset( $existing_value ) || $existing_value > 180 )
			$existing_value = 0;

		echo '<input type="number" name="' . self::OPTION_NAME . '[' . $key . ']" id="facebook-recommendations-bar-max-age" size="3" maxlength="3" min="0" max="180" step="1" value="' . $existing_value . '" /> ' . esc_html( _n( 'day old', 'days old', $existing_value, 'facebook' ) );

		// days === 0 can be confusing. clarify
		if ( $existing_value === 0 )
			echo ' ' . esc_html( __( '(no limit)', 'facebook' ) );

		echo '<p class="description">' . esc_html( __( 'Limit recommendations to articles authored within the last N days.', 'facebook' ) ) . ' ' . esc_html( sprintf( __( 'Reset this value to %s for no date-based limits.', 'facebook' ), '"0"' ) ) . '</p>';
	}

	/**
	 * Translate HTML data response returned from Facebook social plugin builder into underscored keys and PHP values before saving
	 *
	 * @since 1.1
	 * @param array $options data-* options returned from Facebook social plugin builder
	 * @return array $options options to store in WordPress
	 */
	public static function html_data_to_options( $options ) {
		if ( ! is_array( $options ) )
			return array();

		foreach( array( 'max-age', 'num-recommendations', 'read-time' ) as $option ) {
			if ( isset( $options[$option] ) ) {
				$options[ str_replace( '-', '_', $option ) ] = absint( $options[$option] );
				unset( $options[$option] );
			}
		}

		return $options;
	}

	/**
	 * Sanitize Recommendations Bar settings before they are saved to the database
	 *
	 * @since 1.1
	 * @param array $options recommendation bar options
	 * @return array clean option sets. note: we remove Recommendation Button social plugin default options, storing only custom settings (e.g. recommend action preference value stored, like is not stored)
	 */
	public static function sanitize_options( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		if ( isset( $options['trigger'] ) && $options['trigger'] === 'pct' ) {
			$pct = 0;
			if ( isset( $options['trigger_pct'] ) ) {
				$pct = absint( $options['trigger_pct'] );
				unset( $options['trigger_pct'] );
			}

			if ( $pct > 0 )
				$options['trigger'] = $pct . '%';
			else
				$options['trigger'] = 'onvisible';
		}

		self::require_recommendations_bar_builder();

		// Handle like button display preferences first
		$clean_options = parent::sanitize_options( $options );
		if ( isset( $clean_options['show_on'] ) ) {
			self::update_display_conditionals( 'recommendations_bar', $clean_options['show_on'], self::get_show_on_choices() );
			unset( $clean_options['show_on'] );
		}
		unset( $options['show_on'] );

		$bar = Facebook_Recommendations_Bar::fromArray( $options );
		if ( $bar )
			return array_merge( $clean_options, self::html_data_to_options( $bar->toHTMLDataArray() ) );

		return $clean_options;
	}
}

?>