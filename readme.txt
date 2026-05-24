=== SiteCare Maintenance Mode ===
Contributors: widumalt
Tags: maintenance, coming soon, offline, admin
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A beginner-friendly WordPress maintenance mode plugin.

== Description ==

SiteCare Maintenance Mode helps WordPress administrators show visitors a maintenance or offline page while site work is in progress.

Version 1.0.0 includes admin settings for enabling or disabling maintenance mode, scheduling maintenance windows, customizing the maintenance page text, showing optional safe custom HTML, showing optional visitor contact details, applying simple branding, choosing one of five frontend design presets, showing an optional countdown timer, previewing the maintenance page, choosing logged-in roles and trusted IP addresses that can bypass maintenance mode, importing or exporting settings as JSON, and resetting the visible settings form to defaults. When enabled, visitors see the maintenance page, while administrators, selected logged-in roles, and whitelisted IP addresses can continue viewing the normal site.

The settings page is organized into simple tabs for General, Content, Design, Custom HTML, Bypass, Import / Export, and Preview & Reset controls.

This first stable release keeps the plugin lightweight and beginner-friendly. It does not use Composer, npm, React, external frameworks, custom database tables, payment code, or licensing code.

== Installation ==

1. Copy or link the `sitecare-maintenance-mode` plugin folder into `wp-content/plugins`.
2. Open WordPress Admin > Plugins.
3. Find "SiteCare Maintenance Mode".
4. Click "Activate".

For local development, this project is intended to be linked into the LocalWP site named `sitecare-plugin-dev` using a Windows junction.

== Frequently Asked Questions ==

= Does this version enable maintenance mode? =

Yes. Administrators can enable or disable maintenance mode from the WordPress admin settings page.

= What can I customize? =

You can customize the maintenance page title, message, safe custom HTML content, contact email, phone number, social links, footer text, logo, background color, text color, layout width, design preset, and optional countdown timer.

= Which design presets are included? =

This version includes Classic, Center Card, Minimal, Bold Panel, and Split Screen design presets. The settings page shows simple HTML/CSS thumbnail previews for each preset.

= Can I add custom HTML? =

Yes. You can enable custom HTML content and add limited safe HTML such as paragraphs, lists, links, bold text, and emphasis. When custom HTML is enabled and not empty, it replaces the built-in preset content on the maintenance page. Unsafe tags like scripts are removed when settings are saved.

= Does the countdown timer end maintenance mode automatically? =

No. The countdown timer is visual only. Manual maintenance mode and scheduled maintenance mode still control whether maintenance mode is active.

= Can I back up or copy settings? =

Yes. Use the Import / Export tab to download a JSON backup or paste a previous export to restore validated plugin settings.

= Can I preview the maintenance page? =

Yes. Administrators can use the protected preview button on the settings page without enabling maintenance mode for visitors.

= Can logged-in users bypass maintenance mode? =

Yes. Administrators can choose which WordPress roles can view the normal website while maintenance mode is active. Administrators bypass by default.

= Can trusted IP addresses bypass maintenance mode? =

Yes. Administrators can add exact IPv4 or IPv6 addresses to the IP whitelist. Add one IP address per line. CIDR ranges are not supported yet.

= Can I reset the settings? =

Yes. The settings page includes a reset button that changes the visible form fields back to defaults. Those values are not saved until you click Save Settings.

= Does this plugin create database tables? =

No. The plugin uses the WordPress Options API and does not create custom database tables.

= What happens when I uninstall the plugin? =

The plugin deletes its `sitecare_maintenance_options` option. It does not delete anything else.

= Can I use this on a live site now? =

This is the first stable 1.0.0 release. Test changes on a staging or local site before using them on a production website.

== Changelog ==

= 1.0.0 =

* Added the initial WordPress plugin skeleton.
* Added basic activation and deactivation hooks.
* Added a basic admin settings page.
* Added a maintenance mode enable/disable checkbox.
* Added scheduled maintenance mode with WordPress timezone-aware start and end times.
* Added role-based bypass settings for selected logged-in user roles.
* Added IP whitelist bypass for exact IPv4 and IPv6 addresses.
* Added Classic, Center Card, Minimal, Bold Panel, and Split Screen frontend template selection.
* Added HTML/CSS template thumbnail previews in the admin settings page.
* Added an optional visual countdown timer.
* Added a small class-based organization layer for settings and frontend hooks.
* Improved template presets so Classic, Center Card, and Minimal have clearer visual styles.
* Added safe custom HTML content using WordPress HTML sanitization.
* Added a red admin bar status label when maintenance mode is active.
* Organized the admin settings page into simple WordPress-style tabs.
* Moved Custom HTML Override into its own admin tab with clearer override warnings.
* Added JSON import and export for plugin settings.
* Moved built-in preset markup into separate PHP template files under `templates/presets`.
* Cleaned preset templates so they render only plugin-managed maintenance content inside the shared page shell.
* Added a small admin settings page credit for WiTEDS.
* Added editable maintenance page title and message fields.
* Added optional email, phone, social link, and footer text fields.
* Added logo, color, and layout width settings.
* Added protected maintenance page preview mode for administrators.
* Added reset settings button for restoring visible form fields to defaults before saving.
* Added uninstall cleanup for the plugin option.
* Improved maintenance page spacing, mobile responsiveness, and contact/social link styling.
* Improved admin settings page organization, helper text, and maintenance status notices.
* Improved production maintenance responses with 503 status, Retry-After, no-cache headers, and noindex robots meta.
* Added a simple visitor-facing maintenance page.
* Added administrator bypass for users with the manage_options capability.

== License ==

SiteCare Maintenance Mode is licensed under the GPLv2 or later.
