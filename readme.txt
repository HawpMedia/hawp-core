=== Hawp Theme ===
Requires at least: 6.4
Tested up to: 6.6.1
Requires PHP: 7.0
Stable tag: 6.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Hawp Theme is Hawp Media's boilerplate starter theme for all custom websites

== Changelog ==

= 6.5.11 =
* Released: April 17, 2025

* New - Added whitelabel functionality for the admin area. This allows devs to remove hawp media related branding in the wp-admin.
* Fix - Fix issue causing RankMath conflicts with admin script loading, removed hawp theme admin scripts since we dont need them.
* Fix - There was an scss issue with map-has-key no longer working in Dart SASS 3.0.0, so we changed it to map.has-key. 

= 6.5.10 =
* Released: April 8, 2025

* Fix - Remove gravity forms spinner and input to button replacer, it was causing issues and often times users would submit forms multiple times.

= 6.5.9 =
* Released: April 3, 2025

* New - Added dynamic localization of ACF theme options to the child theme JavaScript file so we can get our theme option values in the child javascript file.

= 6.5.8 =
* Released: April 3, 2025

* New - Added widget_area shortcode that can return a widget area by its ID.
* Enhancement - Disable sticky header scripts, sometimes we don't need these.
* Enhancement - Combine primary nav styles into one file.
* Enhancement - Improve desktop nav submenu styles.
* Fix - Adjust the function adding has-featured-image body class so it works properly with the posts page.

= 6.5.7 =
* Released: February 25, 2025

* Enhancement - Refactor sass so that we no longer use @import since it will be deprecated in Dart Sass 3.0. Removed 01-settings and 02-tools and replaced with 01-base and renamed sass directories accordingly.
* Enhancement - Add a function_exists check to get_theme_option and get_theme_option_prefix.

= 6.5.6 =
* Released: February 11, 2025

* Enhancement - Update Freemius SDK to 2.11.0
* Enhancement - Adjust a few default styles for search forms and gravity forms.

= 6.5.5 =
* Released: December 16, 2024

= 6.5.4 =
* Released: August 8, 2024

= 6.5.3 =
* Released: August 5, 2024

= 6.5.2 =
* Released: August 3, 2024

= 6.5.1 =
* Released: July 23, 2024

= 6.5 =
* Released: June 5, 2024

== Copyright ==

Hawp WordPress Theme, (C) 2023 WordPress.org
Hawp is distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
