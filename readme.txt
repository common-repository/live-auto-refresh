=== Live Auto Refresh ===
Contributors: perron
Donate link: https://paypal.me/perronuk/
Tags: reload, refresh, auto, automatic, edit, save, theme, live reload, developer
Requires at least: 4.7
Tested up to: 6.3.2
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Instantly reloads the browser when any theme file code is edited during development or when a content edit is saved.

== Description ==

**THIS PLUGIN IS FOR USE DURING THE DEVELOPMENT OF WORDPRESS THEMES**

One of the challenges of web development is to ensure that the website instantly reflects the latest changes made in the code or the content. Manually refreshing the browser after every edit can be tedious and is inefficient and inconvenient.

Some web development tools such as Live Reload provide a feature that instantly reloads the browser when any theme file code is edited during development or when a content edit is saved, which enables the developer to see the updated version of the website in browser as soon as the changes are saved in the code editor. No additional actions are required to view the results.

This AUTO REFRESH plugin integrates this functionality with WordPress theme development, as well as detecting content saves in the backend for automatic browser reloading. This feature is only visible to logged in administrators, so it does not affect the experience of live website visitors.

This greatly enhances the productivity and accuracy of web development. It allows the developer to test and debug the website in real time and to instantly view how the content appears on the front end of all loaded logged in browsers and devices. With this feature, the developer can always ensure that the website matches the intended design and functionality.

== Installation ==

1. Install to your site from the Wordpress Plugin Directory
2. Activate the plugin through the Plugins menu in WordPress

== Frequently Asked Questions ==

= What files does this monitor for code changes? =

All files within the active theme and child theme if applicable.

= Do style changes reload the page? =

Changes to CSS files will hot reload to display the updates styling without reloading the HTML DOM.

= What content saves trigger a refresh? =

Editor saves to the wordpress page or post content.

= How are PHP errors handled? =

If WP_DEBUG is enabled then any non critical error messages will display.
Critical errors will prevent the plugin running, so a manual reload will be required once the code error is fixed to restart monitoring.

= Do you accept donations? =

This plugin saves developers valuable time, so any donations are greatly appreciated!
[DONATE](https://paypal.me/perronuk/)

== Screenshots ==

1. Admin Bar toggle to enable or disable the monitoring.
2. Console notifications of active monitoring, file change detection, style detection.
3. Disables monitoring after a 10 minute timeout only if no changes have been detected.

== Changelog ==

= 1.0 =
* Initial version

== Upgrade Notice ==

= 1.0 =
* Initial version