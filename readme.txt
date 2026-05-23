=== SiteCare Maintenance Mode ===
Contributors: widumalt
Tags: maintenance, coming soon, offline, admin
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A beginner-friendly WordPress maintenance mode plugin project.

== Description ==

SiteCare Maintenance Mode will help WordPress administrators show visitors a maintenance or offline page while site work is in progress.

This Phase 1 version is only a clean plugin skeleton. It is designed to appear in WordPress Admin > Plugins and activate safely without adding maintenance mode behavior yet.

Future phases will add an admin toggle, visitor-facing maintenance page, administrator bypass, templates, contact details, custom HTML, and preview mode.

== Installation ==

1. Copy or link the `sitecare-maintenance-mode` plugin folder into `wp-content/plugins`.
2. Open WordPress Admin > Plugins.
3. Find "SiteCare Maintenance Mode".
4. Click "Activate".

For local development, this project is intended to be linked into the LocalWP site named `sitecare-plugin-dev` using a Windows junction.

== Frequently Asked Questions ==

= Does this version enable maintenance mode? =

No. Phase 1 only creates the plugin skeleton. Maintenance mode behavior will be added in a later phase.

= Does this plugin create database tables? =

No. The Phase 1 skeleton does not create database tables or save settings.

= Can I use this on a live site now? =

This version is for development and learning. It should activate safely, but it does not provide maintenance mode features yet.

== Changelog ==

= 1.0.0 =

* Added the initial WordPress plugin skeleton.
* Added basic activation and deactivation hooks.
* Added project folders for future development.

== License ==

SiteCare Maintenance Mode is licensed under the GPLv2 or later.
