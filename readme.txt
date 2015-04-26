=== BuddyPress Simple Events ===
Contributors: shanebp
Donate link: http://www.philopress.com/donate/
Tags: buddypress, events
Author URI: http://philopress.com/contact/
Plugin URI: http://philopress.com/products/
Requires at least: WP 4.0 / BP 2.2
Tested up to: WP 4.2 / BP 2.2.3.1
Stable tag: 1.4.3
License: GPLv2 or later

A simple Events plugin for BuddyPress

== Description ==

This BuddyPress plugin allows members to create, edit and delete Events from their profile.

It:

* provides a tab on each members' profile for front-end creation, editing and deletion
* uses the Google Places API for creating locations
* uses Google Maps to show Event location 
* creates a custom post type called 'event'
* uses WP and BP templates that can be overloaded
* includes a widget


It does NOT have:

* ticketing
* calendars
* recurring events

If you would like support for Images, an Attending button and an option for assignment to a Group,
you may be interested in http://www.philopress.com/buddypress-simple-events-pro

For more BuddyPress plugins, please visit http://www.philopress.com/

== Installation ==

1. Unzip and then upload the 'bp-simple-events' folder to the '/wp-content/plugins/' directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Go to Settings -> BP Simple Events and select which user roles are allowed to create Events. 
Admins are automatically given permission.   Other settings are also available.


== Frequently Asked Questions ==

= MultiSite support? =

Yes. Tested in the following configuration:

* WP.4.1.1 - Multisite
* BuddyPress 2.2 + - Network Activated
* BuddyPress Simple Events - Network Activated

Roles can be assigned via the Network Admin > Settings > BP Simple Events screen.

But a member _must_ be a member of the main site in order to create Events.
If they are not a member of the main site, they will not see the Events tab.


== Screenshots ==
1. Shows the front-end Create an Event screen on a member profile
2. Shows the Dashboard > Settings screen


== Changelog ==

= 1.0 =
* Initial release.

= 1.1 =
* Refactored as a component.

= 1.2 =
* Add file missing from last release.

= 1.3 =
* Add multisite support, improved cleanup on trash

= 1.3.2 =
* Use trash hook instead of delete hook for cleanup on Event deletion 

= 1.3.4 =
* Check if BP is activated 

= 1.4 =
* Tested in WP 4.2 & BP 2.2.3.1, tweak subnav creation, replace template_redirect with template_include

= 1.4.1 =
* typo in single template filter

= 1.4.2 =
* fix bug in WP templates filter

= 1.4.3 =
* close the recent XSS vulnerability found in add_query_arg

== Upgrade Notice ==

= 1.0 =

= 1.1 =
* Refactored as a component. Pagination fixed.

= 1.2 =
* Add file missing from last release.

= 1.3 =
* Add multisite support, improved cleanup on trash

= 1.3.2 =
* Use trash hook instead of delete hook for cleanup on Event deletion 

= 1.3.4 =
* Check if BP is activated 

= 1.4 =
* Tested in WP 4.2 & BP 2.2.3.1, tweak subnav creation, replace template_redirect with template_include

= 1.4.1 =
* typo in single template filter

= 1.4.2 =
* fix bug in WP templates filter

= 1.4.3 =
* close the recent XSS vulnerability found in add_query_arg

