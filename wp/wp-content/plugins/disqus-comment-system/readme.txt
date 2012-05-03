=== Disqus Comment System ===
Contributors: disqus, alexkingorg, crowdfavorite
Tags: comments, threaded, email, notification, spam, avatars, community, profile, widget, disqus
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: 2.72

The Disqus comment system replaces your WordPress comment system with your comments hosted and powered by Disqus.

== Description ==

Disqus, pronounced "discuss", is a service and tool for web comments and
discussions. Disqus makes commenting easier and more interactive,
while connecting websites and commenters across a thriving discussion
community.

The Disqus for WordPress plugin seamlessly integrates using the Disqus API and by syncing with WordPress comments.

= Disqus for WordPress =

* Uses the Disqus API
* Comments indexable by search engines (SEO-friendly)
* Support for importing existing comments
* Auto-sync (backup) of comments with Disqus and WordPress database

= Disqus Features =

* Threaded comments and replies
* Notifications and reply by email
* Subscribe and RSS options
* Aggregated comments and social mentions
* Powerful moderation and admin tools
* Full spam filtering, blacklists and whitelists
* Support for Disqus community widgets
* Connected with a large discussion community
* Increased exposure and readership

== Installation ==

**NOTE: It is recommended that you backup your database before installing the plugin.**

1. Unpack archive to this archive to the 'wp-content/plugins/' directory inside
   of WordPress

  * Maintain the directory structure of the archive (all extracted files
    should exist in 'wp-content/plugins/disqus-comment-system/'

2. From your blog administration, click on Comments to change settings
   (WordPress 2.0 users can find the settings under Options > Disqus.)

= More documentation =

Go to [http://disqus.com/help/wordpress](http://disqus.com/help/wordpress)

== Changes ==

2.72

* Load count.js via SSL when page is accessed via HTTPS
* Fixed styling issue with Disqus admin.

2.71

* Fixed issue where embed wasn't using SSL if page was loaded via HTTPS
* Fixed issue with syncing where to user's without a display_name would
  revert back to Anonymous (really this time).
* Fixed issue where Google Webmaster Tools would incorrectly report 404s.
* Fixed issue with Disqus admin display issues.

2.70

* Properly uninstall disqus_dupecheck index when uninstalling plugin.
* Fixed issue with syncing where to user's without a display_name would
  revert back to Anonymous.
* Fixed issue where IP addresses weren't being synced properly.
* Allow non-Administrators (e.g., editors) to see Disqus Moderate panel
  inline (fixes GH-3)

2.69

* Bumped version number.

2.68

* Removed debugging information from web requests in CLI scripts (thanks
  Ryan Dewhurst for the report).
* Reduced sync lock time to 1 hour.
* Fixed an issue which was not allowing pending posts (for sync) to clear.
* Fixed an issue with CLI scripts when used with certain caching plugins.

2.67

* Bumped synchronization timer delays to 5 minutes.
* wp-cli.php now requires php_sapi_name to be set to 'cli' for execution.
* Fixed a bug with imported comments not storing the correct relative date.
* Added a lock for dsq_sync_forum, which can be overriden in the command line script
  with the --force tag.
* dsq_sync_forum will now handle all pending post metadata updates (formerly a separate
  cron task, dsq_sync_post).

2.66

* Fixed issue with jQuery usage which conflicted with updated jQuery version.

2.65

* Corrected a bug that was causing posts to not appear due to invalid references.

2.64

* Added an option to disable Disqus without deactivating the plugin.
* Added a second check for comment sync to prevent stampede race conditions in WP cron.

2.63

* Added command line script to import comments from DISQUS (scripts/import-comments.php).
* Added command line script to export comments to DISQUS (scripts/export-comments.php).
* The exporter will now only do one post at a time.
* The exporter now only sends required attributes to DISQUS.
* Moved media into its own directory.

2.62

* Changed legacy query to use = operator instead of LIKE so it can be indexed.

2.61

* Fixed an issue which was causing invalid information to be presented in RSS feeds.

2.60

* Added support for new Single Sign-On (API version 3.0).
* Improved support for legacy Single Sign-On.

2.55

* Added support for get_comments_number in templates.

2.54

* Updated URL to forum moderation.

2.53

* Fixed an issue with fsockopen and GET requests (only affects certain users).

2.52

* Fixed issue with Disqus-API package not getting updated (only affecting PHP4).

2.51

* Added CDATA comments for JavaScript.
* Syncing comments will now restore missing thread information from old imports.
* Install and uninstall processes have been improved.
* Fixed an issue in PHP4 with importing comments.
* Fixed an issue that could cause duplicate comments in some places.
* Added an option to remove existing imported comments when importing.

2.50

* Added missing file.

2.49

* Database usage has been optimized for storing comment meta data.

You can perform this migration automatically by visiting Comments -> Disqus, or if
you have a large database, you may do this by hand:

CREATE INDEX disqus_dupecheck ON `wp_commentmeta` (meta_key, meta_value(11));
INSERT INTO `wp_options` (blog_id, option_name, option_value, autoload) VALUES (0, 'disqus_version', '2.49', 'yes') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);

2.48

* Comment synchronization has been optimized to be a single call per-site.
* disqus.css will now only load when displaying comments

2.47

* Fixed a security hole with comment importing.
* Reverted ability to use default template comments design.
* Comments will now store which version they were imported under.
* Added an option to disable server side rendering.

2.46

* Better debugging information for export errors.
* Added the ability to manual import Disqus comments into Wordpress.
* Added thread_identifier support to exports.
* Cleaned up API error messages.
* Fixed a bug which was causing the import process to not grab only the latest set of comments.
* Added an option to disable automated synchronization with Disqus.

2.45

* Comments should now store thread information as well as certain other meta data.
* Optimize get_thread polling to only pull comments which aren't stored properly.

2.44

* Fixed JavaScript response for comments sync call.
* Comments are now marked as closed while showing the embed (fixes showing default respond form).

2.43

* Fixed a JavaScript syntax error which would cause linting to fail.
* Correct an issue that was causing comments.php to throw a syntax error under some configurations.

2.42

* Correct a bug with saving disqus_user_api_key (non-critical).
* Added settings to Debug Information.
* Adjusting all includes to use absolute paths.
* Adjusted JSON usage to solve a problem for some clients.

2.41

* Correct a bug with double urlencoding titles.

2.40

* Comments are now synced with Disqus as a delayed asynchronous cron event.
* Comment count code has been updated to use the new widget. (Comment counts
  must be linked to get tracked within "the loop" now).
* API bindings have been migrated to the generic 1.1 Disqus API.
* Pages will now properly update their permalink with Disqus when it changes. This is
  done within the sync event above.
* There is now a Debug Information pane under Advanced to assist with support requests.
* When Disqus is unreachable it will fallback to the theme's built-in comment display.
* Legacy mode is no longer available.
* The plugin management interface can now be localized.
* The plugin is now valid HTML5.

== Support ==

* Visit http://disqus.com/help/wordpress for help documentation.

* Visit http://help.disqus.com for help from our support team.

* Disqus also recommends the [WordPress HelpCenter](http://wphelpcenter.com/) for extended help. Disqus is not associated with the WordPress HelpCenter in any way.
