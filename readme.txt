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

SiteCare Maintenance Mode helps WordPress administrators show visitors a simple maintenance or offline page while site work is in progress.

This MVP version adds basic admin settings for enabling or disabling maintenance mode, scheduling maintenance windows, customizing the maintenance page text, showing optional visitor contact details, applying simple branding, previewing the maintenance page, choosing logged-in roles and trusted IP addresses that can bypass maintenance mode, and resetting the visible settings form to defaults. When enabled, visitors see a simple maintenance page, while administrators, selected logged-in roles, and whitelisted IP addresses can continue viewing the normal site.

Future phases may add templates and custom HTML.

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

You can customize the maintenance page title, message, contact email, phone number, social links, footer text, logo, background color, text color, and layout width.

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

This version is for development and learning. Test it carefully before using it on a live site.

== Changelog ==

= 1.0.0 =

* Added the initial WordPress plugin skeleton.
* Added basic activation and deactivation hooks.
* Added a basic admin settings page.
* Added a maintenance mode enable/disable checkbox.
* Added scheduled maintenance mode with WordPress timezone-aware start and end times.
* Added role-based bypass settings for selected logged-in user roles.
* Added IP whitelist bypass for exact IPv4 and IPv6 addresses.
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
