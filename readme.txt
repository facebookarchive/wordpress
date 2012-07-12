=== Facebook ===
Contributors: Facebook, automattic, mattwkelly, niallkennedy, rgharpuray, ngfeldman, jamesgpearce, ravi.grover, danielbachhuber, gigawats, eosgood, Otto42
Tags: Facebook, comments, social, friends, like, like button, social plugins, facebook platform, page, posts, sidebar, plugin, open graph
Requires at least: 3.2.1
Tested up to: 3.4
Stable tag: trunk

Make your WordPress site social in a couple of clicks, powered by Facebook.

== Description ==

This WordPress plugin makes your site deeply social by integrating functionality from Facebook.

For more information, check out http://developers.facebook.com/wordpress.

**Page and Post Features**

All of these features are easy to enable via checkboxes on the Facebook settings page.

* Post to an Author's Facebook Timeline whenever they publish a new WordPress Post or Page.
* Mention friends and Facebook Pages.  This posts to their Timelines as well as lists them on the WordPress Post or Page.
* Post all new WordPress Post or Pages to a specified Facebook Page.
* Like, send, and subscribe buttons can be enabled in a click and are fully customizable.
* Facebook Comments, including full SEO support.
* Open Graph Protocol integration.
* Recommendations bar, which allows users to click to start getting recommendations, Like content, and add what they're reading to Timeline as they go.

**Widgets**

All of these features are easy to enable via the Widgets settings page.

* Activity Feed Box. This shows the Facebook user the activity that their friends are doing on your website.
* Recommendations Box.  This shows the Facebook user recommendations of pages they should visit based on the actions their friends are taking on your website.
* Like, send, and subscribe buttons.

Facebook Insights (http://www.facebook.com/insights) integration included. This plugin also supports internationalization and mobile.

== Installation ==

1. Install Facebook for WordPress either via the WordPress.org plugin directory, or by uploading the files to your server (in the `/wp-content/plugins/` directory).
1. After activating the plugin, you will be asked to set up your Facebook app (via http://developers.facebook.com/apps ), with step-by-step instructions.
1. That's it. You're ready to go!

== Screenshots ==

1. Facebook settings screen.
2. Boxes to mention Facebook friends and pages in a WordPress Post or Page.
3. Set the status update that will be published to Facebook, along with the WordPress Page or Post.
4. The resulting Post on Celebuzz.
5. The Post is published to the author's (Andy Scott) Timeline.
6. The Post is published on Duets Facebook Timeline, since it was mentioned in the Post.
7. The Post is published to the Celebuzz Facebook Page.
8. Widgets are also available.

== Changelog ==

= 1.0.2 =

* Fixed issue where some sites were extremely slow as a result of installing the plugin (due to cURL not being installed).
* Added warning and actively disable portions of the plugin if SSL not installed.
* Fixed bug in social publishing.
* Added logic to disable meta boxes/publishing if social publishing is disabled.
* Fixed forever loading issue on FB settings page if no active user exists.
* Added proper escaping.
* Added global settings to set whether social plugins show on all posts, all pages, both, or neither. Done for like button, subscribe button, send button, comments, and recommendations bar.
* Added per-post/page settings for showing/hiding social plugins.
* Fixed poorly formatted description that was being set when publishing to friends' and Pages' feeds.
* Added notification if plugins that are potentially conflicting are installed.
* Added suggestions for what to enter in fields in the new user experience.
* Bug fixes to ensure everything works on mobile (including support for WPTouch).
* Bug fixes to Pages drop down on the Facebook settings page.
* Removed the need to create PHP sessions, relying on user meta/transients now.

= 1.0.1 =

* Comment count bug fix.
* Comments width bug fix.
* Like, send, subscribe: fixed incorrect hrefs for homepage buttons.
* Added like/send button to settings page (spread the word!).
* Changed minimum and maximum supported WP versions.
* Security fix in the admin control panel.
* Fixed issue with publishing a post/page if there isn't a thumbnail defined.
* Changed auto-completes to play nice with UTF-8.
* Moved extended access token function to Facebook_WP class.
* Added debug link on settings page that outputs debug information.
* Lots of i18n fixes.
* Added easier debugging of Facebook API errors.
* Added better logged in state detection in admin UI, depending on if the user has authenticated and given certain permissions.
* Fixed publishing a post if no friends or pages mentioned.
* Theme fixes to prevent like and mentions bar from showing up in the wrong place.
* Fixed configure link on plugins page.
* Fixes for bugs happening on 404 page.
* Bug fix for if a WP admin removes the app via facebook.com.
* Added status messages for what was/wasn't posted to Facebook as part of a Post/Page being taken live.
* Added functionality to disable publishing to a page if access token fails.
* Clearer error messages for certain scenarios (like inability to post to a friend of page's Timeline because of privacy settings.
* Fixed conflicts with Power Editor and extraneous text being added to og:description.  Thanks to Angelo Mandato (http://wordpress.org/support/topic/plugin-facebook-plugin-conflicts-with-powerpress?replies=16)

= 1.0 =

* Launch.