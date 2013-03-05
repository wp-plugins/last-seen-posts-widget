=== Last Seen Posts Widget ===
Contributors: veganist
Tags: post, session, widget, last seen, breadcrumbs, trail
Requires at least: 2.8
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show title and links to the last 5 posts a user has consulted.

== Description ==

This plugin creates a widget which will show the title, thumnail and link to the last 5 posts a user has consulted.
To store the data it uses sessions.

Session handling is provided by slightly modified functions from the simple-session-plugin by pkwooster.

== Installation ==

1. Unzip and upload `/last-seen-posts-widget/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the widget under Appearance => Widgets

== Frequently Asked Questions ==

= Can I modify the style of the post list? =

The plugin comes without any stylesheet. It uses your theme's default markup for widgets.
Furthermore, every link has a class "lastseen" which you may use in your theme's stylesheet.

= Can I configure the number of posts to show up? =

Not yet.

= Can I show a thumbnail alongside the title ? =

Yes. If your post has a featured image, this will show up here.

== Screenshots ==

1. Screenshot of widget and result in sidebar

== Changelog ==

= 1.1 =
* added post thumbnail functionality

= 1.0 =
* Initial release
