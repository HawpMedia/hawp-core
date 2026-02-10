=== Hawp Core ===
Requires at least: 6.4
Tested up to: 6.8.2
Requires PHP: 7.4
Stable tag: 1.1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Hawp Core is Hawp Media's boilerplate starter theme for all custom websites

== Changelog ==

= 1.1.7 - Feb 10, 2026 =
* **Enhancement**: Clear cached update transients with the new cache clear helper.
* **Enhancement**: Add a `url` attribute to the `[logo]` shortcode.

= 1.1.6 - Feb 3, 2026 =
* **Enhancement**: Refactor theme option registration and sanitization to use a schema-driven registry.
* **Enhancement**: Refresh the theme options UI with hash-based tabs, code editor fields, and improved media controls.
* **Enhancement**: Sync the ACF color picker palette with editor/theme.json colors, with a fallback palette.
* **Fix**: Correct `get_theme_option` fallback behavior for ACF-stored options.
* **Maintenance**: Clean up custom settings example snippet formatting.

= 1.1.5 - Jan 28, 2026 =
* **Fix**: Avoid `/blog/` pagination and feed 404s by excluding reserved routes from the post prefix rewrite.

= 1.1.4 - Jan 21, 2026 =
* **Fix**: Rename update source folder to the theme slug when GitHub archives unpack with versioned names.
* **Maintenance**: Add optional updater debug logging via `HAWP_GITHUB_DEBUG`.

= 1.1.3 - Jan 21, 2026 =
* **Fix**: Normalize theme folder name during updates using WordPress `move_dir()` when available.
* **Fix**: Improve theme update folder detection based on `style.css` headers.

= 1.1.2 - Jan 21, 2026 =
* **Fix**: Prevent theme update installs from renaming to versioned folders.
* **Fix**: Tighten update rename guardrails to theme updates only.

= 1.1.1 - Jan 21, 2026 =
* **Fix**: Scoped GitHub updater headers to theme repo to avoid updater conflicts.

= 1.1.0 - Nov 25, 2025 =
* **Refactor**: Renamed `parts/layout` to `parts/structure` and split reusable navigation controls into `parts/components`.
* **Refactor**: Introduced consistent `nav-*` template naming (`nav-primary`, `nav-controls-*`) to clarify layout vs. component responsibilities.
* **Compatibility**: Updated parent and child headers to load the new template paths; child themes must target these slugs going forward.

= 1.0.8 - Sep 29, 2025 =
* **Fix**: Harden theme updater to prevent plugin/install conflicts

= 1.0.7 - Sep 16, 2025 =
* **Fix**: Scoped GitHub updater to theme upgrades only (Theme_Upgrader + active theme), preventing plugin update failures.
* **Maintenance**: Added guard clauses in `pre_download()` to avoid intercepting non-theme updates.

= 1.0.6 - Sep 12, 2025 =
* **Feature**: Added GitHub-based theme updater (GitHub Releases; supports release asset or tag zip).
* **Maintenance**: Removed bundled Freemius SDK and all related references.

= 1.0.5 - Sep 2025 =
* **Maintenance**: Internal adjustments and housekeeping; no user-facing changes.

= 1.0.4 - Sep 2025 =
* **Fix**: Minor compatibility fixes and stability improvements.

= 1.0.3 - Aug 2025 =
* **Maintenance**: Preparatory changes for upcoming updater integration.

= 1.0.2 - Aug 25, 2025 =
* **Fix**: Normalized boolean option checks after ACF → Core migration (truthy/falsey instead of brittle true/false or 1/0).
* **Fix**: Ensured checkbox fields save reliably when unchecked (0/1).
* **Maintenance**: Removed legacy querystring-based blog prefix helpers.
* **Maintenance**: Explicitly typed checkbox fields for consistent sanitization.

= 1.0.1 - Aug 4, 2025 =
* **Enhancement**: Improved theme options system with dynamic localization
* **Fix**: Resolved PHP fatal error when ACF plugin is not installed
* **Fix**: Corrected image field handling to return proper image URLs instead of post permalinks
* **Enhancement**: Added automatic detection and localization of all theme options
* **Enhancement**: Added support for automatic ACF field localization when using theme prefix
* **Performance**: Optimized theme options loading with direct database queries
* **Maintenance**: Removed dependency on ACF plugin for core theme functionality

= 1.0.0 - Jul 28, 2025 =
* Initial Release: 

== Copyright ==

Hawp Core WordPress Theme, (C) 2025 WordPress.org
Hawp Core and Hawp Skin are distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
