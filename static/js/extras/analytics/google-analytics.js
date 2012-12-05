/*
 * track Facebook JavaScript SDK events related to social plugins using Google Analytics social tracking feature.
 *
 * @version 1.1.9
 */

// Google Analytics queue
var _gaq = _gaq || [];

var FB_WP = FB_WP || {};
FB_WP.extras = FB_WP.extras || {};
FB_WP.extras.analytics = FB_WP.extras.analytics || {}
FB_WP.extras.analytics.google = {
	/**
	 * Tracks social interactions by iterating through each Google Analytics tracker on the page, calling its _trackSocial method
	 *
	 * @link https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiSocialTracking
	 * @param {string} socialAction The type of action
	 * @param {string} the action URL target
	 * @return a function that iterates over each tracker object and calls the _trackSocial method
	 */
	trackSocialAction: function( socialAction, opt_target ) {
		return function() {
			var trackers = _gat._getTrackers();
			for ( var i = 0, tracker; tracker = trackers[i]; i++ ) {
				tracker._trackSocial( "facebook", socialAction, opt_target );
			}
		}
	},

	/**
	 * Attaches event handlers to Facebook JavaScript SDK social plugin button events
	 * Tracks creation of a Like, destruction of a Like, and a Send message sent
	 *
	 * @link https://developers.facebook.com/docs/reference/javascript/FB.Event.subscribe/
	 */
	addEventHandlers: function() {
		try {
			FB.Event.subscribe( "edge.create", function( opt_target ) {
				_gaq.push( FB_WP.extras.analytics.google.trackSocialAction( "like", opt_target ) );
			} );
			FB.Event.subscribe( "edge.remove", function( opt_target ) {
				_gaq.push( FB_WP.extras.analytics.google.trackSocialAction( "unlike", opt_target ) );
			} );
			FB.Event.subscribe( "message.send", function( opt_target ) {
				_gaq.push( FB_WP.extras.analytics.google.trackSocialAction( "send", opt_target ) );
			} );
			FB.Event.subscribe( "comment.create", function( comment ) {
				if ( comment.href ) {
					_gaq.push( FB_WP.extras.analytics.google.trackSocialAction( "comment", comment.href ) );
				}
			} );
			FB.Event.subscribe( "comment.remove", function( comment ) {
				if ( comment.href ) {
					_gaq.push( FB_WP.extras.analytics.google.trackSocialAction( "uncomment", comment.href ) );
				}
			} );
		} catch (e) {}
	},

	init: function() {
		if ( FB && FB.Event && FB.Event.subscribe ) {
			FB_WP.extras.analytics.google.addEventHandlers();
		}
	}
}