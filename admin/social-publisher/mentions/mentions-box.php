<?php

/**
 * Mention Facebook profiles and pages in a post meta box
 *
 * @since 1.1
 */
class Facebook_Mentions_Box {

	/**
	 * Load post meta boxes for a given post type
	 *
	 * @since 1.1
	 * @param string $post_type the current page's post type
	 */
	public static function after_posts_load( $post_type ) {
		if ( ! class_exists( 'Facebook_Social_Plugin_Settings' ) )
			require_once( dirname( dirname( dirname(__FILE__) ) ) . '/settings-social-plugin.php' );

		// only show mentions box if mentions displayed alongside posts
		$enabled_post_types = Facebook_Social_Plugin_Settings::get_display_conditionals_by_feature( 'mentions' );
		if ( ! is_array( $enabled_post_types ) || empty( $enabled_post_types ) || ! isset( $enabled_post_types[$post_type] ) )
			return;

		add_action( 'admin_enqueue_scripts', array( 'Facebook_Mentions_Box', 'enqueue_scripts' ) );

		if ( ! class_exists( 'Facebook_Mentions_Box_Friends' ) )
			require_once( dirname(__FILE__) . '/mentions-box-friends.php' );
		Facebook_Mentions_Box_Friends::add_meta_box( $post_type );

		if ( ! class_exists( 'Facebook_Mentions_Box_Pages' ) )
			require_once( dirname(__FILE__) . '/mentions-box-pages.php' );
		Facebook_Mentions_Box_Pages::add_meta_box( $post_type );
	}

	/**
	 * Check for mentions meta box content on post save
	 *
	 * @since 1.1
	 */
	public static function add_save_post_hooks() {
		if ( ! class_exists( 'Facebook_Mentions_Box_Friends' ) )
			require_once( dirname(__FILE__) . '/mentions-box-friends.php' );
		add_action( 'save_post', array( 'Facebook_Mentions_Box_Friends', 'save' ) );

		if ( ! class_exists( 'Facebook_Mentions_Box_Pages' ) )
			require_once( dirname(__FILE__) . '/mentions-box-pages.php' );
		add_action( 'save_post', array( 'Facebook_Mentions_Box_Pages', 'save' ) );
	}

	/**
	 * Load scripts used for mentions box functionality
	 *
	 * @since 1.1
	 * @uses wp_enqueue_script()
	 */
	public static function enqueue_scripts() {
		global $facebook_loader;

		$plugin_file = $facebook_loader->plugin_directory . 'facebook.php';

		$suffix = '.min';
		if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG )
			$suffix = '';

		wp_enqueue_script( 'suggest' );
		wp_enqueue_script( 'facebook-mentions', plugins_url( 'static/js/admin/mentions' . $suffix . '.js', $plugin_file ), array('jquery'), '1.1', true );
		wp_enqueue_style( 'facebook-mentions', plugins_url( 'static/css/admin/mentions' . $suffix . '.css', $plugin_file ), array(), '1.1' );

		$loopj_version = '1.6.0';
		wp_enqueue_script( 'facebook-loopj', plugins_url( 'static/js/admin/loopj-jquery-tokeninput/jquery.tokeninput' . $suffix . '.js', $plugin_file ), array('jquery'), $loopj_version, true );
		wp_enqueue_style( 'facebook-loopj', plugins_url( 'static/js/admin/loopj-jquery-tokeninput/styles/token-input-facebook' . $suffix . '.css', $plugin_file ), array(), $loopj_version );

		wp_enqueue_script( 'tipsy', plugins_url( 'static/js/admin/jquery.tipsy' . $suffix . '.js', $plugin_file ), array('jquery'), '1.0.0a', true );

		wp_localize_script( 'facebook-mentions', 'FBNonce', array(
			// URL to wp-admin/admin-ajax.php to process the request
			//'ajaxurl' => admin_url( 'admin-ajax.php' ),

			// generate a nonce with a unique ID "myajax-post-comment-nonce"
			// so that you can check it later when an AJAX request is sent
			'autocompleteNonce' => wp_create_nonce( 'facebook_autocomplete_nonce' )
		) );
	}
}

?>