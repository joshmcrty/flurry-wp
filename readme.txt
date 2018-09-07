=== Flurry ===
Contributors: joshmccarty
Tags: flurry, snow, falling snow, snowing, jquery, winter
Requires at least: 4.5
Tested up to: 4.9.8
Stable tag: 1.1.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds falling snow to your site using the Flurry plugin for jQuery.

== Description ==

Flurry adds a falling snow effect to your WordPress site. It uses the [Flurry jQuery plugin](https://github.com/joshmcrty/flurry) to create the effect. [See an example](https://cdn.rawgit.com/joshmcrty/Flurry/master/index.html).

The Flurry jQuery plugin takes advantage of CSS `transforms`, CSS `transitions` and `requestAnimationFrame` to provide smooth animation for modern browsers. It uses unicode characters as snowflakes, with no dependencies on images or CSS files. Polyfills are automatically provided for `requestAnimationFrame` and CSS `transitions` (falling back to `setInterval` and jQuery's `.animate()` feature respectively) for excellent browser compatibility. See the [Flurry website](https://github.com/joshmcrty/flurry) for more information.

== Installation ==

1. Upload the plugin to your site's plugins directory, or search for "Flurry" on the "Plugins > Add New" menu in WordPress and install it
2. Activate the plugin through the "Plugins" menu in WordPress
3. After activation, if you want to adjust settings, go to the "Flurry" settings page under the "Appearance" menu in WordPress.

== Frequently Asked Questions ==

= How do I change the animation speed/number of snowflakes on the page/how far the snowflakes fall/etc.? =

Go to the "Flurry" settings page under the "Appearance" menu in WordPress and modify the settings. Each setting has a description of what it does.

= Does this impact the performance of my website? =

Yes, of course, in the sense that it adds another JavaScript file to download and that elements are being animated on the page. However, great care has been taken with the Flurry jQuery plugin to ensure the best possible browser performance so your site visitors have a negligible performance impact.

= Will this work OK in browser X? =

Probably, but very old browsers may have poor performance (or not work at all). See the [Flurry website](https://github.com/joshmcrty/flurry) for more information on browser compatibility.

= How to I set this to only run during a certain date range? =

That feature is not currently available. When you no longer want the falling snow effect, go to the "Plugins" menu in WordPress and deactivate the plugin. Your settings will be saved unless you delete the plugin.

= Is there a version that doesn't require jQuery?=

Not currently, although a vanilla JS version may be available in the future.

== Screenshots ==

1. All of the available settings for Flurry

== Changelog ==
= 1.1.1 =
* Ensure the latest Flurry script is loaded if browser has an older version cached

= 1.1.0 =
* Update to latest version of Flurry jQuery plugin
* Add additional character, color, and startRotation options

= 1.0.1 =
* Fixes a permissions issue for non-admin users.
* Updates "tested up to" to WordPress 4.9

= 1.0 =
* Initial release to WordPress.org plugin repository

= 0.3 =
* First public release

== Upgrade Notice ==
= 1.0 =
Provides the latest version of Flurry for jQuery with improved performance.

= 1.0.1 =
Fixes a permissions issue for non-admin users.

= 1.1.0 = 
Provides the latest version of Flurry for jQuery with additional customization options.

= 1.1.1 =
Ensures the latest Flurry script is loaded by browsers.