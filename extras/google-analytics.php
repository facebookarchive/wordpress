<?php

/**
 * Support for Google Analytics Social Interaction Analytics
 *
 * Queues Facebook-specific calls to the _trackSocial method via the _gaq queue
 *
 * @since 1.1.9
 *
 * @link https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingSocial Google Analytics Social Interaction Analytics
 */
class Facebook_Google_Analytics {
	/**
	 * Handle used in WordPress script queue.
	 *
	 * @since 1.1.9
	 *
	 * @var string
	 */
	const SCRIPT_HANDLE = 'facebook-google-analytics';

	/**
	 * Add the Google Analytics initialization function to the Facebook for WordPress JavaScript queue to be executed after initialization of the Facebook JavaScript SDK
	 *
	 * @since 1.1.9
	 *
	 * @return string JavaScript code snippet
	 */
	public static function add_to_queue() {
		return 'if(FB_WP.queue && FB_WP.queue.add){FB_WP.queue.add(function(){FB_WP.extras.analytics.google.init()})}';
	}

	/**
	 * Relative path to Facebook Google Analytics JavaScript file
	 *
	 * @since 1.1.9
	 *
	 * @return string file path relative to the plugin directory
	 */
	public static function javascript_file_path() {
		return 'static/js/extras/analytics/google-analytics' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.js';
	}

	/**
	 * Echo JavaScript inline to match the style of the plugin
	 *
	 * @since 1.1.9
	 * @return void
	 */
	public static function inline() {
		$js_file = dirname( dirname( __FILE__ ) ) . '/' . self::javascript_file_path();
		if ( ! file_exists( $js_file ) )
			return;

		echo "\n" . file_get_contents( $js_file ) . self::add_to_queue() . "\n";
	}

	/**
	 * Act on queued Google Analytics tracker commands.
	 *
	 * Used to customize Google Analytics for WordPress by Yoast. The plugin only supports filters adding tracker object methods. We must echo our own JavaScript block to pass a function to the Google Analytics queue.
	 *
	 * @since 1.1.9
	 *
	 * @param array $command_array flat array of strings. each value includes a tracker object method and parameters later wrapped in brackets for conversion to an array
	 * @return array flat array of strings
	 */
	public static function gaq_filter( $command_array ) {
		self::enqueue();
		return $command_array;
	}

	/**
	 * Enqueue our Google Analytics script
	 *
	 * @since 1.1.9
	 *
	 * @uses wp_enqueue_script()
	 * @return void
	 */
	public static function enqueue() {
		wp_enqueue_script( self::SCRIPT_HANDLE, plugins_url( self::javascript_file_path(), dirname(__FILE__) ), array('facebook-jssdk'), '1.1.9', true );
		add_action( 'wp_print_footer_scripts', array( 'Facebook_Google_Analytics', 'gaq_push' ), 11, 0 );
	}

	/**
	 * Add a Facebook queue item to the Google Analytics queue after Facebook Google Analytics code has loaded
	 *
	 * @since 1.1.9
	 *
	 * @global WP_Scripts $wp_scripts check if script has been loaded
	 * @return void
	 */
	public static function gaq_push() {
		global $wp_scripts;

		if ( isset( $wp_scripts ) && $wp_scripts->query( self::SCRIPT_HANDLE, 'done' ) )
			echo '<script type="text/javascript">var _gaq=_gaq||[];_gaq.push(function(){' . self::add_to_queue() . '});</script>';
	}
}
?>
