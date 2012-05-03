=== Simple Facebook Connect ===
Contributors: Otto42
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom
Tags: facebook, connect, simple, otto, otto42, javascript, comments, share, status
Requires at least: 3.3
Tested up to: 3.3
Stable Tag: 1.3

== Description ==

Simple Facebook Connect is a framework and series of sub-systems that let you add any sort of Facebook functionality you like to a WordPress blog. This lets you have an integrated site without a lot of coding, and still letting you customize it exactly the way you'd like.

After activating the plugin and setting up a Facebook Application for your site, you can enable individual pieces of functionality to let you integrate your site in various ways.

The plugin is also a developer framework for both the Graph API and the Facebook Javascript SDK, allowing you to make other custom plugins or theme modifications with much less code. The basics of Facebook integration are already handled by the core plugin, so just a few function calls can be made to contact Facebook systems and connect with them in various ways.

Requires WordPress 3.0 and PHP 5. 

* Enables your site to connect to Facebook with JS SDK
* Implements OpenGraph tags, entirely automatically
* Comment using Facebook credentials (with Facebook avatar support)
* Automatically Publish new posts to Facebook Profile or Application or Fan Page
* Manually Publish posts to FB Profile or Application or Fan Page
* Integrate comments made on Facebook back into your own site
* Login with your Facebook credentials, optionally using Facebook avatars instead of Gravatars
* New user registration with Facebook credentials (using the Facebook Registration Authentication system)
* Facebook Photo Album integration in the Media uploader
* Like Button and shortcode
* User Status Widget and shortcode
* Live Stream Widget and shortcode
* Fan Box Widget and shortcode
* Fan Count Chicklet and Widget
* Activity Feed Widget
* "Share" button and Shortcode (reworked version of Like button, as Share button is no longer supported by Facebook)

If you have suggestions for a new add-on, feel free to email me at otto@ottodestruct.com .

Want regular updates? Become a fan of my sites on Facebook!
http://www.facebook.com/ottopress
http://www.facebook.com/apps/application.php?id=116002660893

Or follow my sites on Twitter!
http://twitter.com/ottodestruct

== Installation ==

1. Upload the files to the `/wp-content/plugins/simple-facebook-connect/` directory
1. Activate the "Simple Facebook Connect" plugin through the 'Plugins' menu in WordPress
1. Follow the instructions that the plugin itself will give you.

== Frequently Asked Questions ==

= The comments addon isn't working! =

You have to modify your theme to use the comments plugin.

(Note: If you have WordPress 3.0 and a theme using the new comment_form() method, then this step is not necessary).

In your comments.php file (or wherever your comments form is), you need to do the following.

1. Find the three inputs for the author, email, and url information. They need to have those ID's on the inputs (author, email, url). This is what the default theme and all standardized themes use, but some may be slightly different. You'll have to alter them to have these ID's in that case.

2. Just before the first input, add this code:
[div id="comment-user-details"]
[?php do_action('alt_comment_login'); ?]

(Replace the []'s with normal html greater/less than signs).

3. Just below the last input (not the comment text area, just the name/email/url inputs, add this:
[/div]

That will add the necessary pieces to allow the script to work.

If you're using WordPress 3.0 and the new "comments_form" code (like in the Twenty Ten theme), then this is unnecessary! Check ottopress.com for info on how to upgrade your theme to use the new 3.0 features.

= Facebook Avatars look wrong. =

Facebook avatars use slightly different code than other avatars. They should style the same, but not all themes will have this working properly, due to various theme designs and such. 

However, it is almost always possible to correct this with some simple CSS adjustments. For this reason, they are given an "fbavatar" class, for you to use to style them as you need. Just use .fbavatar in your CSS and add styling rules to correct those specific avatars.

Also note that Facebook "square" avatars are limited to 50 by 50 pixels in size. If your theme uses a bigger one, then it'll get scaled up and may look bad. There's no easy fix for this, as the other avatars offered by Facebook are not square shaped. If it returned those, then it could mess up many more themes. complain to Facebook and tell them to offer larger squared off avatars.

= How do I use this Fanbox custom CSS option? =
Well, first you have to learn CSS.

Next, try starting with this code in the custom CSS box:
.connect_widget .connect_widget_facebook_logo_menubar {}
.fan_box .full_widget .connect_top {}
.fan_box .full_widget .page_stream {}
.fan_box .full_widget .connections {}

That should be enough to get you started.

= How do I use this with multi-site across subdomains/subdirectories? =

(This is a new feature to 0.21 and it has NOT been really tested well yet. You have been warned.)

Many people want to set up a "network" of sites, and enable SFC across all of them. Furthermore, they'd like people to stay "connected" across them all, and to only use one Facebook Application to connect their users. This is entirely possible with a bit of setup.

First, create your Facebook Application. It should use the base domain field as well as the normal fields. No subdirectories or subdomains anywhere. For this example, we'll use "example.com".

Next, you can add these to your site's wp-config:
define('SFC_APP_SECRET', 'xxxxx');
define('SFC_APP_ID', 'xxxxx');
define('SFC_FANPAGE', '(this one is optional)');

These are the exact same settings as on the normal SFC base configuration screen, and they will override those settings for the entire network of sites. In fact, when those are defined, the corresponding settings options won't even appear. This may look odd at first.

With this setup, SFC *should* work across all your subdomains and subdirectories. So it'll work on example.com or blog.example.com or otto.example.com or whatever. It should also work on example.com/blog. 

The "base domain" setting on your Facebook application is important. In this case, it MUST be set to the base domain (example.com), so that the cookies will be set there and thus will let you stay "connected" across the sites.

Leaving the Fan Page setting undefined will allow each site to enter in their own Fan Page, but still using your single Application to publish to them.

Notes: 
* SFC-Login may or may not work. It's hard to say. Try it, it might work. I need more info to debug this.
* Using other base domains with the domain mapping plugin absolutely will NOT work. Period. The redirection to the base domain for login breaks SFC-Login, because there is no way for Facebook to stay connected across domain names. 

Email me if you have problems... But only if you're also willing to help solve those problems, I can't reproduce most setups and I can't fix what I can't see.

= How do I use Facebook Avatars? =

The Comments module will automatically use Facebook avatars for users that leave comments using Facebook.

The Login module can optionally make the system use Facebook avatars instead of Gravatars, for users that have connected their accounts to Facebook.


== Screenshots ==

1. Main Settings
2. Sub-modules listing
3. Facebook OpenGraph configuration and Avatar settings
4. Dropdown for SFC-Login
5. Connecting accounts between FB and WP
6. Registration screen (non multi-site only)
7. Photo album support
8. Manual publishing
9. FB Comments pull back
10. Widgets
11. Like and "Share" buttons
12. Automatic Publishing
13. Fan Box CSS customization


== Upgrade Notice ==

= 1.3 =
* This version removes support for posting to Facebook Application Profile Walls, since FB is removing them.
* This is an interim release, to get SFC to work now for many users having problems with 1.2. Another release will be made soon to handle FB's deprecation of offline_access. This version still requires offline_access to function fully.

= 1.2 = 
* This version REQUIRES WordPress 3.3. If you have not yet updated to WordPress 3.3, DO NOT UPGRADE THIS PLUGIN. This plugin will not work with WordPress 3.2.

== Changelog ==

= 1.3 =
* Dump application wall publishing support.
* Move help info into separate file
* Fix image handling when auto or manually publishing some videos
* Yet another logout issue. FB sure makes it hard to disconnect from them when you want to do so.
* Minor improvements to photo module.
* Minor bugfixes.
* Height/width fix for video support
* Better manual excerpt handling
* Fix problem relating to FB's new access token requirements

= 1.2 =
* Lots of minor bugfixes
* Logout JS fix for edge cases. Props johnkleinschmidt.
* Updated FB schema URIs to be compliant with the latest spec
* Locale handling improvements
* Vast changes to OpenGraph support. Now supports audio, various video sites, etc. Media handling migrated to sfc-media.php for easier expansion in the future.
* Registration improvements
* Support website linkage properly in comments (FB does silly things here with multiple websites)
* Publish using message fields to get greater edgerank for auto posts
* Publish using "link" feed type, since edgerank seems to be improved in this way (use SFC_PUBLISH_ENDPOINT define if you want to change it back to "feed")
* Help Screen additions, along with a dismissable WP 3.3 pointer to point you to them
* Permissions fixes for publishing
* Text Domain loading fixes for translations
* Made modules generic for easier additions in the future and by separate plugins
* Improved asyncronous JS loading, to reduce conflicting code cases
* Added channel URL support (should reduce extraneous log of FB requests, and eliminate frame-busting issues)

= 1.1 = 
* OAuth 2.0 upgrade. Signed requests are now used exclusively.
* Added SFC_LOCALE define to allow users to override the FB Locale string in the wp-config.php file.
* Filter sfc_register_fields added for adding new fields to the registration screen.
* Comments init script added back to allow anon FB "logged in" users to show up automatically in comments form
* Comments fixed for OAuth 2.0 issues
* Comemnts share fixed to have Read Post action links and comment text as FB post content
* Plugin screen action links fixed
* Slightly better publish permissions interface (auto-submit on button click)
* Caption bug in publisher fixed (published items won't show the domain name as the caption anymore)
* SSL problems fixed for hosts that had them
* Activity feed widget fix (FB made a non-backward compatible change to the XFBML code)
* Locale issues worked around. Non-recognized locales will get en_US locale. Future versions may fix this better, but this will at least let the plugin function.
* Swapped AppID/Secret field order to be more like how FB displays them
* sfc_is_fan function fixed and re-enabled
* Image and Video meta scanning improved. Still has minor problems with YouTube HTML5 capable iframe code, however works fine with most oEmbed code.

= 1.0 = 
* Entirely rewritten plugin.
* Graph API support.
* OpenGraph support.
* FB JS SDK support.
* Registration model changed to use FB's new registration system.
* Meta handling improved and centralized.
* Publish reworked (app auto-publishing works now!)
* Comments reworked (sharing comments to Facebook now happens entirely in the background)
* Share removed and replaced with modified Like button (FB Share is no longer supported by Facebook, and incompatible with newer code)
* "Send" option now available with Like button.
* NEW Photo integration module
* Widgets reworked
* Deprecated widgets removed

= 0.26 (never released) =
* SFC-Login: Profile connection fix
* SFC-Base: SSL admin fix (thanks to jwz)
* SFC-Publish: Squeeze out max possible number of chars (jwz)
* SFC-Publish: More filters on more things for more customization potential
* SFC-Register: Remove admin nag for fb connected users (hattip: Stephan Muller)

= 0.25 =
* Fix UTF-8 encoding problem for non-English publishing.
* Add FB Icon to username for FB connected users

= 0.24 =
* Fix missing xd_receiver URL.

= 0.23 =
* i18n thanks to Burak Tuyan.
* New filter: sfc_img_exclude will let other plugin authors add their own image classes to exclude from the automatic image finder for share and publish and such.
* sfc_like_button now supports a url parameter to add a like button to a specific URL, like the homepage.
* Publish now sends up to 1000 chars from the post to Facebook. Thanks to jwz for the patch.
* Publish now gets images correctly in more cases. Thanks to jwz.
* If you enable login avatars (by uncommenting that code), it will show them for comments now too.
* xid elimination
* Custom Post Type support for automatic publishing (any CPT with public=>true will get auto-published)
* Contextual help added
* Improved error messages
* Numerous optimizations and bugfixes

= 0.22 = 
* Error due to bad code release. Do not use.

= 0.21 =
* The main SFC base plugin now has a proper and working check for PHP 5 only systems. I think. I hope. I'm sick of people talking about "error on line 210" or what have you. Read the requirements, folks!
* The base plugin now has ways to pre-define the main four settings (api-key, app-secret, app-id, and fanpage-id). You can add defines for each of them into your wp-config with these names: SFC_API_KEY, SFC_APP_SECRET, SFC_APP_ID, and SFC_FANPAGE. All of them are optional, and when defined they will no longer show up on the settings page. This may be useful for people using the multi-site capabilities of WP 3.0 and wanting to use a single application across domains. You can also define SFC_IGNORE_ERRORS to "true" to force the settings page to not display the "Incorrect URL" error. This is necessary for multi-user systems using a base domain application across multiple subdomains, as the error checker cannot account for that.
* Facebook avatars now use the Graph API for displaying. This eliminates the fb:profile XFBML tag and makes it back into a normal IMG tag, like Gravatars and Twitter avatars are. This should remove styling problems people had, though they are still classed as "fbavatar" if you want to style them differently. Side note: Because of this, the Facebook logo on these avatars is now gone. Graph API has no way to force the logo.
* Several like button options added.
* Remove minimum width of Fanbox.
* More options in activity feed widget.
* Assorted bug fixes.

= 0.20 =
* Added Activity Feed widget.
* Minor update to fanbox. functions get_sfc_fanbox() and sfc_fanbox() are now available for direct theme usage.
* Added lots more og: meta data. Facebook added an "article" type, specifically for blog posts and other such similar things. This fixes the every-post-becomes-a-fan-page problem.
* Minor wording changes.
* Minor bugfixes.
* Fix for login for people with high FB ID numbers. Maybe. I can't test this patch, but somebody told me it worked.
* Changed the init call to force email request on connection. This may solve some problems for people unable to comment and such.

= 0.19 =
* Added og:site_name to base plugin, for OpenGraph info.
* Added fb-like shortcode.
* Offline permissions fix, assorted thumbnail handling fixes.

= 0.18 = 
* Added Upcoming Events widget. This will show your upcoming events from Facebook. For a user, it shows events that person has been invited to. For a fan page, application, or group, it will show events created by that entity.
* Added Like button. Will be reworking entire plugin soon for FB's new OpenGraph support. Expect that to be a 1.0 release.

= 0.17 =
* Add error checking for bad API and similar inputs which caused a Facebook API error. The error from FB is now echoed and handled properly.
* Add Locale support. If your WordPress is in a locale, then that locale will be passed to Facebook's Javascript to make the default text appear in that language. Logged in users will still see the text localized to their own lanaguage.
* Support for WordPress 3.0. Works with Multi-Site, but it must be configured separately for each individual site, it will not work sitewide. Sitewide coming soon for a small percentage of configurations (it's only possible if all sites are on the same domain, not in multiple domains).
* Fixed image handling in Share and Publish plugins (requires WP 3.0 kses fixes for it to really work).
* Facebook decided to remove email_domain without telling anybody in advance. That's now fixed.

= 0.16.1 =
* Missed a trailing slash issue in 0.16.

= 0.16 =
* Error handling on login now tells you "Facebook user not recognized" if you try to login with a FB user that isn't attached to a WP user.
* Added Facebook Platform Status Feed on config page. This might help to tell users when Facebook is having issues, hopefully to make them stop emailing me every time Facebook's API servers have another hiccup.
* Rewritten javascript for Comments plugin. Now the Sharing is optional with a checkbox (per FB's new guidelines) and the comment submission should be more bulletproof (for browsers that don't do Javascript quite properly... I'm looking at you Safari and Chrome!).
* Fixed avatar styling issues. If you had already made changes to your CSS to account for the avatar divs, then those are now gone and the avatar class is properly on the IMG instead. If you do need special styling, use .fbavatar to refer to facebook avatars only.
* Fixed minor share button problem with "method 2".
* Main plugin now does some sanity checking for common errors.

= 0.15 = 
* Script and style tags now get stripped properly when publishing.
* Fanbox widget now has a height option.
* The Connect Widget will now actually log a user into WordPress via redirecting them to wp-login.php. Login plugin must be activated for this to work. This can also trigger Register plugin, if that plugin is activated. User will be redirected back to current page, but with logged in cookies set. 

= 0.14 =
* Fanbox had an error in 0.13.
* Fixes publish error for people using themes that don't support post thumbnails

= 0.13 =

* Bug fix: Publish and Share could miss images in the content sometimes.
* Minor speed improvements to Fan Box CSS handling.
* Automatic Fan Page publishing now tries to force privacy setting to "Everyone", to eliminate issues with invalid privacy settings making posts not visible to Fans.
* Post thumbnail support in Publish and Share plugins.
* Minor Fanbox custom CSS improvement.
* Publish won't send mere edits of already existing posts to Facebook now.
* Changed comment plugin to use newer translateable Connect with Facebook button.

= 0.12 =

* Fan Box custom CSS support.
* PHP 5 version checking as a base requirement. No way around this, Facebook's PHP libraries are PHP 5 and up only. PHP 4 is just dead.
* Login and Comments plugins add Facebook person extension data to Atom feeds, based on Friendfeed <a href="http://friendfeed.com/jessestay/0293c591/i-would-love-to-see-rss-and-or-atom-support">discussion</a>.
* Additional error checking to try to prevent odd PHP errors whenever Facebook's API goes wonky.
* Login now has an option to prevent people from disconnecting their WP and FB accounts. Add a "define('SFC_ALLOW_DISCONNECT',false); to your wp-config to prevent disconnection of accounts.
* Fixed logout bugs in Login plugin. Logout works correctly now.
* SSL Support. The base plugin now loads the scripts correctly for SSL connections. No guarantees, but it should work for SSL Admin users now.
* Added "Find us on Facebook" button in widget and shortcode form. Button links to your main Facebook App/Fan Page wall. Use [fb-find] in posts for shortcode.
* Automatic publishing to Fan Pages works now. Automatic publish to Application Walls does not work yet, due to Facebook bugs.
* Register plugin now has a "one-click" mode, to skip all prompting. Add "define ('SFC_REGISTER_TRANSPARENT', true);" to your wp-config to enable this mode. WARNING: May be buggy, not recommended for production sites.
* Minor speed enhancement that should fix some of the delays people see when logging in with FB on their sites.
* Height support in Fan Box shortcode.

= 0.11 =

* Fix html entities in publish dialogs.
* Publish plugin now supports automatic publishing! Look on the SFC settings page to grant permissions and enable automatic publishing.
* Real email address support in comments and register. You need to fill in the "Email Domain" on the FB Applications tab to be given a proper choice.
* Register plugin is now working. Requires login plugin to be enabled first.
* Publish plugin is now smarter and won't show you publishing buttons if you're not connected to Facebook.
* Published posts now also have a See Comments link on Facebook. 

= 0.10 =

* Fix quoting problems with publish and comments, for stream publishing (quote marks in titles and such shouldn't cause problems any more)
* Comment email improvement: If you have the "Comment author must fill out name and e-mail" checked in Settings->Discussion, the comments plugin will now ask the Facebook user for Permission to email that user. This will allow things like replying to the comment emails and Subscribe to Comments and similar plugins to work with Simple Facebook Connect. Yes, you can actually reply to the Facebook commenter when their comment gets emailed to you, and the reply *works*. Tested, proven.
* Comments plugin now uses comment meta table for storing FB user id, making for *much* quicker avatar generation. Avatars used to be built by getting FB UID from the email field, which took time for regex parsing. Old avatars will be auto-converted to new method when displayed. This also has an advantage in that there's now an 'fbuid' comment meta field on every facebook connected comment, to tie back to the author of the comment. 
* Comments now don't rely on Javascript quite so much. Facebook PHP code is used to get relevant data.
* Publish post-processing improvements, to try to get more images from the post content by using the_content filter.
* Publish button now shows "Fan Page" instead of "Application", if you're using a Fan Page.
* Made comment login button hook a bit more generic (anticipating a "Simple Twitter Connect" plugin).

= 0.9 = 

* Added share button type option.
* Improved login support. Now it verifies your users email address with Facebook before allowing them to connect their accounts. This ensures that at least they're using the same email on FB and on WP.
* Fixed problem with page reloading for no obvious reason (using different reload method for login plugin).
* Share button shortcode is now [fb-share] if you want to use that in a post.
* Added new Publisher button to publish to your own Facebook profile (this is the same as sharing the post with the share button, actually, but a few people requested it).
* Added Facebook logo checkbox to fanbox plugin.

= 0.8 =

* Added Fan Page support, for people who already have Fan Pages that they don't want to give up. I do not recommend using this option, but it's there if you really need it.
* Improved login capabilities. Now a Connect button shows on the login screen, and logging out actually logs you out properly.

= 0.7 =

* Added shortcode for fanbox widget. [fb-fanbox]. Optional parameters are stream (1 or 0), connections (int), and width (int).
* Added Application Secret field to main plugin. Login plugin will need it.
* Facebook login now partially working. If you connect your WP account to your FB account and you visit the wp-login page while logged into Facebook as well, you will get auto-logged into WordPress, without any prompting or intervention. This may not be 100% secure or safe, and I do not recommend using it at this point, it's for testing only. I would, however, appreciate feedback on the best way to implement this, sort of thing.

= 0.6 = 

* Added shortcode for live stream widget. [fb-livestream] will work in pages and posts. The width and height are optional parameters.
* Added shortcode for user status widget. [fb-userstatus profileid="12345"] will work similarly. The profileid is required.
* Added Connect button widget and shortcode [fb-connect].
* Added Bookmark button widget and shortcode [fb-bookmark].

= 0.5 =

* Live Stream widget
* Manual Publishing plugin. Lets you post links to your posts on the Facebook Application's Wall. These will show up as "updates" to Fans of your application (which makes the Fan Box widget more useful). Currently, this is manual in that it will only push posts to the Wall when you click the button on the Edit Post page and publish it there.

= 0.4 =

* Added Fan Box Widget
* Added new Application ID field to main plugin
* Minor internal reorganizing, for planned addons
* Decided to keep all the version numbers in sync

= 0.3 =

* Comment avatars working, beginnings of a Facebook login capability.

= 0.2.3 =

* Comments working now. Requires minor theme modifications to make it work.

= 0.2.2 =

* Support FBFoundations compatibility, to some extent (make it easier to switch)
* Correct minor errors

= 0.2.1 = 

* Add meta information to share button, so that stuff shows up nicely on Facebook.

= 0.2 =

* Functional enough to use. Barely. Comments still not working. Share button works. XFBML works.

= 0.1 =

* Pre-Alpha. DO NOT USE.