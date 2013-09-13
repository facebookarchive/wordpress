<?php

if ( ! class_exists( 'Facebook_Social_Plugin_Button_Settings' ) )
	require_once( dirname(__FILE__) . '/settings-social-plugin-button.php' );

/**
 * Site settings for the Facebook Send Button social plugin.
 *
 * @since 1.1
 */
class Facebook_Follow_Button_Settings extends Facebook_Social_Plugin_Button_Settings {

	/**
	 * Setting page identifier.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-follow-button';

	/**
	 * Define our option array value.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const OPTION_NAME = 'facebook_follow_button';

	/**
	 * The hook suffix assigned by add_submenu_page()
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	protected $hook_suffix = '';

	/**
	 * Initialize with an options array.
	 *
	 * @since 1.1
	 *
	 * @param array $options existing options
	 */
	public function __construct( $options = array() ) {
		if ( is_array( $options ) && ! empty( $options ) )
			$this->existing_options = $options;
		else
			$this->existing_options = array();
	}

	/**
	 * Reference the social plugin by name.
	 *
	 * @since 1.1
	 *
	 * @return string social plugin name
	 */
	public static function social_plugin_name() {
		return __( 'Follow Button', 'facebook' );
	}

	/**
	 * Evaluate the Facebook_Follow_Button class file if it is not already loaded.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public static function require_follow_button_builder() {
		if ( ! class_exists( 'Facebook_Follow_Button' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-follow-button.php' );
	}

	/**
	 * Navigate to the settings page through the Facebook top-level menu item.
	 *
	 * @since 1.1
	 *
	 * @uses add_submenu_page()
	 * @param string $parent_slug Facebook top-level menu item slug
	 * @return string submenu hook suffix
	 */
	public static function add_submenu_item( $parent_slug ) {
		$follow_button_settings = new Facebook_Follow_Button_Settings();

		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( &$follow_button_settings, 'settings_page' )
		);

		if ( $hook_suffix ) {
			$follow_button_settings->hook_suffix = $hook_suffix;
			register_setting( $hook_suffix, self::OPTION_NAME, array( 'Facebook_Follow_Button_Settings', 'sanitize_options' ) );
			add_action( 'load-' . $hook_suffix, array( &$follow_button_settings, 'onload' ) );
		}

		return $hook_suffix;
	}

	/**
	 * Load stored options and scripts on settings page view.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function onload() {
		$options = get_option( self::OPTION_NAME );
		if ( ! is_array( $options ) )
			$options = array();
		$this->existing_options = $options;

		$this->settings_api_init();
	}

	/**
	 * Load the page.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function settings_page() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		Facebook_Settings::settings_page_template( $this->hook_suffix, sprintf( __( '%s Settings', 'facebook' ), self::social_plugin_name() ) );
	}

	/**
	 * Hook into the settings API.
	 *
	 * @since 1.1
	 *
	 * @uses add_settings_section()
	 * @uses add_settings_field()
	 * @return void
	 */
	private function settings_api_init() {
		if ( ! isset( $this->hook_suffix ) )
			return;

		$section = 'facebook-follow-button';
		add_settings_section(
			$section,
			'', // no title for main section
			array( &$this, 'section_header' ),
			$this->hook_suffix
		);

		// when, where
		add_settings_field(
			'facebook-follow-show-on',
			__( 'Show on', 'facebook' ),
			array( &$this, 'display_show_on' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-follow-position',
			__( 'Position', 'facebook' ),
			array( &$this, 'display_position' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-follow-position' )
		);

		// follow button options
		add_settings_field(
			'facebook-follow-layout',
			__( 'Layout', 'facebook' ),
			array( &$this, 'display_layout' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-follow-show-faces',
			__( 'Show faces', 'facebook' ),
			array( &$this, 'display_show_faces' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-follow-show-faces' )
		);
		add_settings_field(
			'facebook-follow-width',
			__( 'Width', 'facebook' ),
			array( &$this, 'display_width' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-follow-width' )
		);
		add_settings_field(
			'facebook-follow-font',
			__( 'Font', 'facebook' ),
			array( &$this, 'display_font' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-follow-font' )
		);
		add_settings_field(
			'facebook-follow-colorscheme',
			__( 'Color scheme', 'facebook' ),
			array( &$this, 'display_colorscheme' ),
			$this->hook_suffix,
			$section
		);
	}

	/**
	 * Introduce publishers to the Follow Button social plugin.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function section_header() {
		echo '<p>' . esc_html( __( "Encourage visitors to follow to public updates from an author's Facebook account.", 'facebook' ) ) . ' <a href="https://developers.facebook.com/docs/reference/plugins/follow/" title="' . esc_attr( sprintf( __( '%s social plugin documentation', 'facebook' ), 'Facebook ' . self::social_plugin_name() ) ) . '">' . esc_html( __( 'Read more...', 'facebook' ) ) . '</a></p>';
	}

	/**
	 * Archive choices and post type choices.
	 *
	 * @since 1.1.9
	 *
	 * @param string $scope accept the same number of parameters as the parent class function. no effect
	 * @return array list of archive names and public post type names
	 */
	public static function get_show_on_choices( $scope = null ) {
		return array_merge( array( 'home', 'archive' ), self::post_types_supporting_authorship() );
	}

	/**
	 * Limit selectable post types to just public post types supporting authors.
	 *
	 * Not all post types support the concept of an author.
	 *
	 * @since 1.1.9
	 *
	 * @return array list of public post types supporting author feature
	 */
	public static function post_types_supporting_authorship() {
		// get a list of all public post types
		$public_post_types = get_post_types( array( 'public' => true ) );

		// reduce the list of public post types to just the post types supporting comments
		$post_types_supporting_authorship = array();
		foreach( $public_post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'author' ) ) {
				$post_types_supporting_authorship[] = $post_type;
			}
		}

		return $post_types_supporting_authorship;
	}

	/**
	 * Where should the button appear?
	 *
	 * @since 1.1
	 *
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_show_on( $extra_attributes = array() ) {
		$key = 'show_on';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-show-on',
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		$existing_value = self::get_display_conditionals_by_feature( 'follow', 'all' );

		echo '<fieldset id="' . $id . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo '>';

		$choices = self::get_show_on_choices();
		$fields = array();
		foreach( $choices as $type ) {
			$field = '<label><input type="checkbox" name="' . $name . '[]" value="' . esc_attr( $type ) . '"';
			$field .= checked( isset( $existing_value[$type] ), true, false );
			$field .= ' /> ' . esc_html( $type ) . '</label>';

			$fields[] = $field;
			unset( $field );
		}
		echo implode( ' ', $fields );
		echo '</fieldset>';
		echo '<p class="description">' . esc_html( self::show_on_description( self::social_plugin_name() ) ) . '</p>';
	}

	/**
	 * Describe layout choices.
	 *
	 * @since 1.1
	 *
	 * @return array layout descriptions keyed by layout choice
	 */
	public static function layout_descriptions() {
		$follow_plural = __( 'followers', 'facebook' );
		return array(
			'standard' => __( 'Display social text next to the button.', 'facebook' ),
			'button_count' => sprintf( __( 'Display total number of %s next to the button.', 'facebook' ), $follow_plural ),
			'box_count' => sprintf( __( 'Display total number of %s above the button.', 'facebook' ), $follow_plural )
		);
	}

	/**
	 * Choose a Follow Button layout option.
	 *
	 * @since 1.1
	 *
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_layout( $extra_attributes = array() ) {
		$key = 'layout';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-' . $key,
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );
		$name = esc_attr( $name );

		self::require_follow_button_builder();

		if ( isset( $this->existing_options[$key] ) && isset( Facebook_Follow_Button::$layout_choices[ $this->existing_options[$key] ] ) )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = 'standard';

		$descriptions = self::layout_descriptions();

		$layout_choices = array_keys( Facebook_Follow_Button::$layout_choices );
		$choices = array();
		foreach( $layout_choices as $layout ) {
			$choice = '<label><input type="radio" name="' . $name . '" value="' . $layout . '"';
			$choice .= checked( $layout, $existing_value, false );
			$choice .= ' /> ';

			$choice .= $layout;
			if ( isset( $descriptions[$layout] ) )
				$choice .= esc_html( ' — ' . $descriptions[$layout] );
			$choice .= '</label>';

			$choices[] = $choice;
			unset( $choice );
		}

		if ( ! empty( $choices ) ) {
			echo '<fieldset id="' . $id . '"';
			if ( isset( $class ) && $class )
				echo ' class="' . $class . '"';
			echo '><div>';
			echo implode( '</div><div>', $choices );
			echo '</div></fieldset>';
		}
	}

	/**
	 * Option to display faces of friends below the Follow Button.
	 *
	 * @since 1.1
	 *
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_show_faces( $extra_attributes = array() ) {
		$key = 'show_faces';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-show-faces',
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		echo '<label><input type="checkbox" name="' . esc_attr( $name ) . '" id="' . $id . '" value="1"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		checked( isset( $this->existing_options[$key] ) );
		echo ' /> ' . esc_html( __( "Show profile photos of the viewer's friends who already follow this person.", 'facebook' ) ) . '</label>';
	}

	/**
	 * Allow the publisher to customize the width of the Follow Button.
	 *
	 * @since 1.1
	 *
	 * @global int $content_width theme content width used as default width value
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_width( $extra_attributes = array() ) {
		global $content_width;

		$key = 'width';
		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-' . $key,
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		if ( isset( $this->existing_options[$key] ) )
			$existing_value = absint( $this->existing_options[$key] );
		else if ( isset( $content_width ) )
			$existing_value = absint( $content_width );

		if ( ! isset( $existing_value ) || $existing_value < 55 )
			$existing_value = 450; // match social plugin config page default

		echo '<input type="number" name="' . esc_attr( $name ) . '" id="' . $id . '" size="5" min="55" step="1" value="' . $existing_value . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo ' />';
		if ( isset( $content_width ) && $name === self::OPTION_NAME . '[' . $key . ']' ) // hide on widget
			echo ' ' . esc_html( sprintf( __( 'Content width: %u', 'facebook' ), absint( $content_width ) ) );

		echo '<p class="description">' . esc_html( sprintf( __( 'The width of the %s display area in whole pixels.', 'facebook' ), self::social_plugin_name() ) ) . '</p>';
	}

	/**
	 * Customize the color scheme.
	 *
	 * @since 1.1
	 *
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_colorscheme( $extra_attributes = array() ) {
		$key = 'colorscheme';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-' . $key,
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
	 * Choose a custom font.
	 *
	 * @since 1.1
	 *
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_font( $extra_attributes = array() ) {
		$key = 'font';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-' . $key,
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
	 * Where would you like it?
	 *
	 * @since 1.1
	 *
	 * @param array $extra_attributes custom form attributes
	 * @return void
	 */
	public function display_position( $extra_attributes = array() ) {
		$key = 'position';

		extract( self::parse_form_field_attributes(
			$extra_attributes,
			array(
				'id' => 'facebook-follow-' . $key,
				'class' => '',
				'name' => self::OPTION_NAME . '[' . $key . ']'
			)
		) );

		echo '<select name="' . esc_attr( $name ) . '" id="' . $id . '"';
		if ( isset( $class ) && $class )
			echo ' class="' . $class . '"';
		echo '>' . self::position_choices( isset( $this->existing_options[$key] ) ? $this->existing_options[$key] : '' ) . '</select>';
	}

	/**
	 * Translate HTML data response returned from Facebook social plugin builder into underscored keys and PHP values before saving.
	 *
	 * @since 1.1
	 *
	 * @param array $options data-* options returned from Facebook social plugin builder
	 * @return array $options options to store in WordPress
	 */
	public static function html_data_to_options( $options ) {
		if ( ! is_array( $options ) )
			return array();

		if ( isset( $options['show-faces'] ) ) {
			if ( $options['show-faces'] === 'true' )
				$options['show_faces'] = true;
			else if ( $options['show-faces'] === 'false' )
				$options['show_faces'] = false;
			unset( $options['show-faces'] );
		}

		if ( isset( $options['width'] ) )
			$options['width'] = absint( $options['width'] );

		return $options;
	}

	/**
	 * Sanitize Follow Button settings before they are saved to the database.
	 *
	 * @since 1.1
	 *
	 * @param array $options Follow Button options
	 * @return array clean option sets. note: we remove Follow Button social plugin default options, storing only custom settings (e.g. dark color scheme stored, light is default and therefore not stored)
	 */
	public static function sanitize_options( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		self::require_follow_button_builder();

		// Handle display preferences first
		$clean_options = parent::sanitize_options( $options );
		if ( isset( $clean_options['show_on'] ) ) {
			self::update_display_conditionals( 'follow', $clean_options['show_on'], self::get_show_on_choices() );
			unset( $clean_options['show_on'] );
		}
		unset( $options['show_on'] );

		if ( isset( $options['show_faces'] ) )
			$options['show_faces'] = true;
		else
			$options['show_faces'] = false;

		if ( isset( $options['width'] ) )
			$options['width'] = absint( $options['width'] );

		// href required for follow button
		// set href contextual to the post author, not at settings
		// fake it here to pass sanitization, then remove before save
		$options['href'] = 'https://www.facebook.com/zuck';

		$follow_button = Facebook_Follow_Button::fromArray( $options );
		if ( $follow_button ) {
			$follow_button_options = self::html_data_to_options( $follow_button->toHTMLDataArray() );

			// remove the dummy value set above
			// remove here instead of html_data_to_options to separate widget usage with its real href
			unset( $follow_button_options['href'] );

			return array_merge( $clean_options, $follow_button_options );
		}

		return $clean_options;
	}
}

?>
