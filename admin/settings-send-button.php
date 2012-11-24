<?php

if ( ! class_exists( 'Facebook_Social_Plugin_Button_Settings' ) )
	require_once( dirname(__FILE__) . '/settings-social-plugin-button.php' );

/**
 * Site settings for the Facebook Send Button social plugin
 *
 * @since 1.1
 */
class Facebook_Send_Button_Settings extends Facebook_Social_Plugin_Button_Settings {

	/**
	 * Setting page identifier
	 *
	 * @since 1.1
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-send-button';

	/**
	 * Define our option array value
	 *
	 * @since 1.1
	 * @var string
	 */
	const OPTION_NAME = 'facebook_send_button';

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
		return __( 'Send Button', 'facebook' );
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
		$send_button_settings = new Facebook_Send_Button_Settings();

		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( &$send_button_settings, 'settings_page' )
		);

		if ( $hook_suffix ) {
			$send_button_settings->hook_suffix = $hook_suffix;
			register_setting( $hook_suffix, self::OPTION_NAME, array( 'Facebook_Send_Button_Settings', 'sanitize_options' ) );
			add_action( 'load-' . $hook_suffix, array( &$send_button_settings, 'onload' ) );
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
	 * @param string $page target grouping
	 */
	private function settings_api_init() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		$section = 'facebook-send-button';
		add_settings_section(
			$section,
			'', // blank title for main page section
			array( &$this, 'section_header' ),
			$this->hook_suffix
		);

		// when, where
		add_settings_field(
			'facebook-send-show-on',
			__( 'Show on', 'facebook' ),
			array( &$this, 'display_show_on' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-send-position',
			__( 'Position', 'facebook' ),
			array( &$this, 'display_position' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-send-position' )
		);

		// send button option
		add_settings_field(
			'facebook-send-font',
			__( 'Font', 'facebook' ),
			array( &$this, 'display_font' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-send-position' )
		);
		add_settings_field(
			'facebook-send-colorscheme',
			__( 'Color scheme', 'facebook' ),
			array( &$this, 'display_colorscheme' ),
			$this->hook_suffix,
			$section
		);
	}

	/**
	 * Introduce publishers to the Send Button social plugin
	 *
	 * @since 1.1
	 */
	public function section_header() {
		echo '<p>';
		echo esc_html( sprintf( __( 'Help site visitors send your URL in a message to any email address, a message to his or her %1$s friends, or a post to a %1$s group.', 'facebook' ), 'Facebook' ) );
		echo ' ' . ' <a href="https://developers.facebook.com/docs/reference/plugins/send/" title="' . esc_attr( sprintf( __( '%s social plugin documentation', 'facebook' ), 'Facebook ' . self::social_plugin_name() ) ) . '">' . esc_html( __( 'Read more...', 'facebook' ) ) . '</a>';
		echo '</p>';
	}

	/**
	 * Where should the button appear?
	 *
	 * @since 1.1
	 * @param array $extra_attributes custom form attributes
	 */
	public function display_show_on( $extra_attributes = array() ) {
		$key = 'show_on';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-send-show-on',
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		echo '<fieldset id="' . $id . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo '>' . self::show_on_choices( $name, self::get_display_conditionals_by_feature( 'send', 'all' ), 'all' ) . '</fieldset>';

		echo '<p class="description">' . esc_html( self::show_on_description( __( 'Send Button', 'facebook' ) ) ) . '</p>';
	}

	/**
	 * Where would you like it?
	 *
	 * @since 1.1
	 * @param array $extra_attributes custom form attributes
	 */
	public function display_position( $extra_attributes = array() ) {
		$key = 'position';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-send-' . $key,
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		if ( isset( $this->existing_options[$key] ) && in_array( $this->existing_options[$key], self::$position_choices, true ) )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = 'both';

		echo '<select name="' . esc_attr( $name ) . '" id="' . $id . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo '>' . self::position_choices( $existing_value ) . '</select>';
	}

	/**
	 * Choose a custom font
	 *
	 * @since 1.1
	 * @param array $extra_attributes custom form attributes
	 */
	public function display_font( $extra_attributes = array() ) {
		$key = 'font';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-send-' . $key,
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		echo '<select name="' . esc_attr( $name ) . '" id="' . $id . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo '>' . self::font_choices( isset( $this->existing_options[$key] ) ? $this->existing_options[$key] : '' ) . '</select>';
	}

	/**
	 * Customize the color scheme
	 *
	 * @since 1.1
	 * @param array $extra_attributes custom form attributes
	 */
	public function display_colorscheme( $extra_attributes = array() ) {
		$key = 'colorscheme';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-send-' . $key,
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		echo '<fieldset id="' . $id . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo '>' . self::color_scheme_choices( $name, isset( $this->existing_options[$key] ) ? $this->existing_options[$key] : '' ) . '</fieldset>';
	}

	/**
	 * Sanitize Send Button settings before they are saved to the database
	 *
	 * @since 1.1
	 * @param array $options Send Button options
	 * @return array clean option sets. note: we remove Send Button social plugin default options, storing only custom settings (e.g. dark color scheme stored, light is default and therefore not stored)
	 */
	public static function sanitize_options( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		$clean_options = array();

		if ( ! class_exists( 'Facebook_Send_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-send-button.php' );

		// Handle display preferences first
		$clean_options = parent::sanitize_options( $options );
		if ( isset( $clean_options['show_on'] ) ) {
			self::update_display_conditionals( 'send', $clean_options['show_on'], self::get_show_on_choices( 'all' ) );
			unset( $clean_options['show_on'] );
		}
		unset( $options['show_on'] );

		$send_button = Facebook_Send_Button::fromArray( $options );
		$send_button_options = $send_button->toHTMLDataArray();
		if ( ! empty( $send_button_options ) )
			return array_merge( $clean_options, $send_button_options );

		return $clean_options;
	}
}

?>