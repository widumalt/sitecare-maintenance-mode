# PROJECT_BRIEF.md

## Project Name

**SiteCare Maintenance Mode**

## Current Status

Planning is complete, and the project is ready for **Phase 1 implementation**.

Phase 1 is plugin skeleton only. It should make WordPress recognize the plugin, but it should not add real maintenance mode behavior yet unless the user specifically requests that work.

Phase 2 will be the first functional MVP with an admin toggle, saved setting, visitor maintenance page, and admin bypass.

## Project Details

- Plugin name: `SiteCare Maintenance Mode`
- Plugin slug: `sitecare-maintenance-mode`
- Main plugin file: `sitecare-maintenance-mode.php`
- Text domain: `sitecare-maintenance-mode`
- Local project folder: `C:\dev\WP-maintenance-offline-plugin`
- GitHub repository: `https://github.com/widumalt/sitecare-maintenance-mode`
- Local WordPress environment: LocalWP
- LocalWP site name: `sitecare-plugin-dev`

## What This Plugin Will Do

SiteCare Maintenance Mode is a WordPress plugin that helps site administrators temporarily take a website offline for visitors while updates, design changes, or repairs are being made.

When maintenance mode is enabled, normal visitors will see a custom maintenance page instead of the regular website. Administrators will still be able to log in, view the site, and continue working.

## Learning Goal

This plugin is a learning project before building more advanced commercial plugins, including a future auction plugin.

The project should teach the WordPress plugin lifecycle in a practical way:

- Creating a valid plugin skeleton.
- Activating and deactivating a plugin safely.
- Adding an admin settings page.
- Saving settings with WordPress APIs.
- Rendering front-end behavior.
- Applying basic WordPress security practices.

## Target Users

This plugin is for:

- WordPress site owners.
- Freelancers maintaining client websites.
- Small agencies.
- Beginners who need a simple maintenance mode tool.
- Administrators who want a clear way to hide a site during updates.

## Main Problem

Site owners often need to work on a live WordPress site without visitors seeing broken layouts, unfinished pages, plugin errors, or incomplete changes.

This plugin solves that by providing a controlled offline mode with an easy admin toggle.

## Phase 1 Scope

Phase 1 should create only the plugin skeleton:

- Main plugin file.
- WordPress plugin header.
- Basic constants.
- Basic folders for future code organization.
- Basic `readme.txt`.
- Basic `uninstall.php`.
- Empty `index.php` files for folder protection.
- Basic `.gitignore`.

Phase 1 should not add working maintenance mode behavior unless the user explicitly changes the requested scope.

## Phase 2 MVP Scope

The first usable maintenance mode version should include:

- A WordPress admin setting to enable or disable maintenance mode.
- A maintenance page shown to logged-out visitors.
- A clear default message for visitors.
- Admin bypass so administrators can still access the site.
- Safe settings handling using WordPress APIs.

## Later Features

Future versions may include:

- Custom maintenance page title and message.
- Contact details such as email, phone, or social links.
- Template choices.
- Custom HTML support.
- Preview mode so admins can see the maintenance page before enabling it.
- Basic design controls such as colors and logo upload.

## Out of Scope for the MVP

Avoid adding these until the core plugin works:

- Complex page builders.
- Email capture forms.
- Countdown timers.
- Analytics dashboards.
- Advanced design systems.
- External service integrations.
- Licensing or payment logic.
- React or block editor features.

## Success Criteria

The plugin is successful when:

- WordPress can detect and activate the plugin.
- An administrator can enable maintenance mode quickly in Phase 2.
- Visitors see a clear maintenance message in Phase 2.
- Administrators are not locked out of the site.
- The plugin behaves predictably.
- The code is understandable for a beginner WordPress developer.

## Guiding Principle

Build the smallest useful version first, then improve it carefully.
