<?php

if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
	require_once( dirname(__FILE__) . '/settings-social-plugin.php' );

/**
 * Customize Comments Box Facebook social plugin parameters
 *
 * @since 1.1
 */
class Facebook_Comments_Settings extends Facebook_Social_Plugin_Settings {

	/**
	 * Setting page identifier.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'facebook-comments';

	/**
	 * Define our option array value.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	const OPTION_NAME = 'facebook_comments';

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

		$this->supporting_post_types = self::post_types_supporting_comments();
	}

	/**
	 * Reference the social plugin by name.
	 *
	 * @since 1.1
	 *
	 * @return string social plugin name
	 */
	public static function social_plugin_name() {
		return __( 'Comments Box', 'facebook' );
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
		$comments_settings = new Facebook_Comments_Settings();

		// no post types support comments. nothing to do here
		if ( empty( $comments_settings->supporting_post_types ) )
			return '';

		$hook_suffix = add_submenu_page(
			$parent_slug,
			self::social_plugin_name(),
			self::social_plugin_name(),
			'manage_options',
			self::PAGE_SLUG,
			array( &$comments_settings, 'settings_page' )
		);

		if ( $hook_suffix ) {
			$comments_settings->hook_suffix = $hook_suffix;
			register_setting( $hook_suffix, self::OPTION_NAME, array( 'Facebook_Comments_Settings', 'sanitize_options' ) );
			add_action( 'load-' . $hook_suffix, array( &$comments_settings, 'onload' ) );
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

		$section = 'facebook-comments-box';
		add_settings_section(
			$section,
			'', // no title for main section
			array( &$this, 'section_header' ),
			$this->hook_suffix
		);

		add_settings_field(
			'facebook-comments-show-on',
			_x( 'Show on', 'Display the social plugin in specific areas of a website', 'facebook' ),
			array( &$this, 'display_show_on' ),
			$this->hook_suffix,
			$section
		);

		// comments box options
		add_settings_field(
			'facebook-comments-num-posts',
			_x( 'Number of comments', 'Number of comments to display in a list of comments', 'facebook' ),
			array( &$this, 'display_num_posts' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-comments-num-posts' )
		);
		add_settings_field(
			'facebook-comments-order-by',
			__( 'Order by', 'facebook' ),
			array( &$this, 'display_order_by' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'facebook-comments-width',
			__( 'Width', 'facebook' ),
			array( &$this, 'display_width' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'facebook-comments-width' )
		);
		add_settings_field(
			'facebook-comments-colorscheme',
			__( 'Color scheme', 'facebook' ),
			array( &$this, 'display_colorscheme' ),
			$this->hook_suffix,
			$section
		);
	}

	/**
	 * Introduce publishers to the Comments Box social plugin.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function section_header() {
		global $facebook_loader;

		echo '<p>' . esc_html( __( 'Enable social commenting backed by a Facebook account and optional providers AOL, Hotmail, or Yahoo!.', 'facebook' ) ) . ' <a href="https://developers.facebook.com/docs/plugins/comments/" title="' . esc_attr( sprintf( __( '%s social plugin documentation', 'facebook' ), 'Facebook ' . self::social_plugin_name() ) ) . '">' . esc_html( __( 'Read more...', 'facebook' ) ) . '</a></p>';
		echo '<p>' . esc_html( __( "Comments appear in the author's Facebook Timeline.", 'facebook' ) ) . ' ' . esc_html( __( 'All administrators of your Facebook application will be able to moderate comments.', 'facebook' ) ) . '</p>';
		echo '<p>' . sprintf( esc_html( __( 'You may customize your %1$s settings from the %2$s including moderated comments, blacklisted words, and comment sorting.', 'facebook' ) ), self::social_plugin_name(), '<a href="' . esc_url( 'https://developers.facebook.com/tools/comments' . ( isset( $facebook_loader->credentials['app_id'] ) ? '?' . http_build_query( array( 'id' => $facebook_loader->credentials['app_id'] ) ) : '' ), array( 'http', 'https' ) ) . '">' . esc_html( __( 'Facebook Comments Tool', 'facebook' ) ) . '</a>' ) . ' ' . esc_html( __( 'You may also specify individual comment moderators not associated with your application.', 'facebook' ) ) . '</p>';
	}

	/**
	 * Return a list of all public post types supporting the comments feature.
	 *
	 * @since 1.1
	 *
	 * @uses get_post_types()
	 * @return array post type names supporting comments feature
	 */
	public static function post_types_supporting_comments() {
		// get a list of all public post types
		$public_post_types = get_post_types( array( 'public' => true ) );

		// reduce the list of public post types to just the post types supporting comments
		$post_types_supporting_comments = array();
		foreach( $public_post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) )
				$post_types_supporting_comments[] = $post_type;
		}
		return $post_types_supporting_comments;
	}

	/**
	 * On which single pages should the comments box appear?
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function display_show_on() {
		$existing_value = self::get_display_conditionals_by_feature( 'comments', 'posts' );
		if ( ! is_array( $existing_value ) ) {
			$existing_value = array();
			foreach ( $this->supporting_post_types as $post_type ) {
				$existing_value[$post_type] = true;
			}
		}

		$fields = array();
		foreach( $this->supporting_post_types as $type ) {
			$field = '<label><input type="checkbox" name="' . self::OPTION_NAME . '[show_on][]" value="' . esc_attr( $type ) . '"';
			$field .= checked( isset( $existing_value[$type] ), true, false );
			$field .= ' /> ' . esc_html( $type ) . '</label>';

			$fields[] = $field;
			unset( $field );
		}

		echo '<fieldset id="facebook-comments-show-on">' . implode( ' ', $fields ) . '</fieldset>';

		echo '<p class="description">' . esc_html( sprintf( __( 'Display the %s on one or more post types.', 'facebook' ), self::social_plugin_name() ) ) . '</p>';
	}

	/**
	 * Maximum number of posts displayed before viewer expansion.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function display_num_posts() {
		$key = 'num_posts';

		$existing_value = 10;
		if ( isset( $this->existing_options[$key] ) ) {
			$val = absint( $this->existing_options[$key] );
			if ( $val > 0 )
				$existing_value = $val;
			unset( $val );
		}

		echo '<input type="number" name="' . self::OPTION_NAME . '[' . $key . ']" id="facebook-comments-num-posts" size="3" min="1" step="1" value="' . $existing_value . '" /> ';
		echo esc_html( _x( 'top-level comments shown before viewer expansion.', 'Digit prefix. Example: 5 comments shown', 'facebook' ) );
	}

	/**
	 * Allow the publisher to customize the width of the Comments Box.
	 *
	 * @since 1.1
	 *
	 * @global int $content_width content width of the theme
	 * @return void
	 */
	public function display_width() {
		global $content_width;

		$key = 'width';

		if ( isset( $this->existing_options[$key] ) )
			$existing_value = absint( $this->existing_options[$key] );
		else if ( isset( $content_width ) )
			$existing_value = absint( $content_width );

		if ( ! isset( $existing_value ) || $existing_value < 400 )
			$existing_value = 470; // match social plugin config page default

		echo '<input type="number" name="' . self::OPTION_NAME . '[' . $key . ']" id="facebook-comments-' . $key . '" size="5" min="400" step="1"';
		if ( $existing_value >= 400 )
			echo ' value="' . $existing_value . '"';
		echo ' />';

		if ( isset( $content_width ) )
			echo ' ' . esc_html( sprintf( __( 'Content width: %u', 'facebook' ), absint( $content_width ) ) );

		echo '<p class="description">' . esc_html( sprintf( __( 'The width of the %s display area in whole pixels.', 'facebook' ), self::social_plugin_name() ) ) . '</p>';
	}

	/**
	 * Customize the color scheme.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function display_colorscheme() {
		$key = 'colorscheme';

		echo '<fieldset id="facebook-comments-' . $key . '">' . self::color_scheme_choices( self::OPTION_NAME . '[' . $key . ']', isset( $this->existing_options[$key] ) ? $this->existing_options[$key] : '' ) . '</fieldset>';
	}

	/**
	 * Ordering choices.
	 *
	 * @since 1.3
	 *
	 * @return array associative array of social plugin field value and translated label
	 */
	public static function order_by_choices() {
		return array(
			'social' => __( 'social', 'facebook' ),
			'time' => _x( 'oldest first', 'display comments ordered from oldest to newest', 'facebook' ),
			'reverse_time' => _x( 'newest first', 'display comments ordered from newest to oldest', 'facebook' )
		);
	}

	/**
	 * Customize comment order.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function display_order_by() {
		$key = 'order_by';
		$choices = self::order_by_choices();
		if ( isset( $this->existing_options[$key] ) )
			$existing_value = $this->existing_options[$key];
		else
			$existing_value = 'social';

		echo '<fieldset id="facebook-comments-' . $key . '">';
		foreach( $choices as $choice => $label ) {
			echo '<label><input type="radio" name="' . self::OPTION_NAME . '[' . $key  . ']" value="' . $choice . '"';
			checked( $choice, $existing_value );
			echo ' /> ' . esc_html( $label ) . '</label> ';
		}
		echo '</fieldset>';
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

		if ( isset( $options['num-posts'] ) ) {
			$options['num_posts'] = absint( $options['num-posts'] );
			unset( $options['num-posts'] );
		}
		if ( isset( $options['order-by'] ) ) {
			$options['order_by'] = $options['order-by'];
			unset( $options['order-by'] );
		}
		if ( isset( $options['width'] ) )
			$options['width'] = absint( $options['width'] );

		return $options;
	}

	/**
	 * Sanitize Comments Box settings before they are saved to the database
	 *
	 * @since 1.1
	 *
	 * @param array $options Comments Box options
	 * @return array clean option sets. note: we remove Comments Box social plugin default options, storing only custom settings (e.g. dark color scheme stored, light is default and therefore not stored)
	 */
	public static function sanitize_options( $options ) {
		if ( ! is_array( $options ) || empty( $options ) )
			return array();

		if ( ! class_exists( 'Facebook_Comments_Box' ) )
			require_once( dirname( dirname(__FILE__) ) . '/social-plugins/class-facebook-comments-box.php' );

		// Handle display preferences first
		$clean_options = parent::sanitize_options( $options );
		if ( isset( $clean_options['show_on'] ) ) {
			self::update_display_conditionals( 'comments', $clean_options['show_on'], self::post_types_supporting_comments() );
			if ( empty( $clean_options['show_on'] ) )
				delete_option( 'facebook_comments_enabled' );
			else
				update_option( 'facebook_comments_enabled', '1' );
			unset( $clean_options['show_on'] );
		} else {
			delete_option( 'facebook_comments_enabled' );
		}
		unset( $options['show_on'] );

		foreach( array( 'width', 'num_posts' ) as $option ) {
			if ( isset( $options[ $option ] ) )
				$options[ $option ] = absint( $options[ $option ] );
		}

		$comments_box = Facebook_Comments_Box::fromArray( $options );
		if ( $comments_box )
			return array_merge( $clean_options, self::html_data_to_options( $comments_box->toHTMLDataArray() ) );

		return $clean_options;
	}
}

?>
