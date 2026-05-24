# ARCHITECTURE.md

This document explains the expected architecture for **SitePause** in beginner-friendly terms.

The project is now ready for Phase 1 implementation. Phase 1 may create the plugin skeleton when requested, but it should not add working maintenance mode behavior unless specifically requested.

## High-Level Design

The plugin will have two main responsibilities:

1. Admin features for configuring maintenance mode.
2. Public features for showing the maintenance page to visitors.

Keeping these responsibilities clear will make the plugin easier to understand and test.

## Initial Phase 1 Architecture

Phase 1 should keep the architecture simple:

- The main plugin file, `sitecare-maintenance-mode.php`, loads basic constants and any required includes.
- No complex class architecture is required in Phase 1.
- Folders may be added now only to keep the project organized.
- Classes should be added only when they become useful in Phase 2 or later.
- No real maintenance mode behavior should be added in Phase 1 unless the user requests it.

Expected Phase 1 folders:

- `includes/`
- `admin/`
- `public/`
- `templates/`
- `assets/`
- `languages/`

Each folder should include a basic `index.php` file.

## Recommended Future Class Structure

When the plugin grows, these classes may be useful:

- `includes/class-plugin.php` for main plugin bootstrap.
- `includes/class-settings.php` for settings registration.
- `includes/class-maintenance-renderer.php` for maintenance page rendering.
- `includes/class-template-loader.php` for template loading.
- `admin/class-admin-menu.php` for admin page registration.
- `public/class-frontend.php` for frontend request handling.

Do not add these classes before they are useful. A simple procedural Phase 1 skeleton is acceptable.

## Expected WordPress Concepts

The plugin will likely use these WordPress features:

- Plugin header so WordPress can detect the plugin.
- Admin menu or settings page for configuration.
- Options API for saving plugin settings.
- Hooks for running code at the correct time.
- Capability checks to restrict admin actions.
- Nonces to protect settings forms.
- Sanitizing and escaping to keep data safe.

## Future Phase 2 Request Flow

When a visitor requests a page in Phase 2:

1. WordPress loads normally.
2. The plugin checks whether maintenance mode is enabled.
3. The plugin checks whether the current user should bypass maintenance mode.
4. If bypass is allowed, WordPress continues showing the normal site.
5. If bypass is not allowed, the plugin shows the maintenance page.

This logic should run on `template_redirect` for normal frontend visitor requests.

## Frontend Request Rules for Phase 2

The maintenance page should not appear for every WordPress request.

Future Phase 2 behavior should:

- Do not show the maintenance page in `wp-admin`.
- Do not block `wp-login.php`.
- Do not block administrators with `manage_options`.
- Avoid interfering with AJAX, REST API, cron, and admin requests unless deliberately planned.
- Use `template_redirect` for normal frontend visitor requests.

## Admin Area Responsibilities

The admin area should let authorized users:

- Turn maintenance mode on or off.
- Edit maintenance page content when that feature is added.
- Preview the maintenance page when preview mode is added.

Admin actions should:

- Check that the user has permission.
- Use a nonce when saving forms.
- Validate and sanitize values before saving.
- Escape output when displaying saved values.
- Show clear success or error messages.

## Public Area Responsibilities

The public side should:

- Detect when visitors should see maintenance mode.
- Render a clear maintenance page.
- Avoid showing maintenance mode to administrators who should bypass it.
- Avoid breaking login, admin, AJAX, REST API, cron, or necessary WordPress system requests.

The maintenance page should be simple at first. More layout and styling options can be added later.

## Settings Storage

The plugin should use the WordPress Options API.

Recommended naming convention:

- Use one main option array where practical: `sitecare_maintenance_options`.
- For the MVP, a simple option is acceptable if it keeps the implementation easier to understand.
- If a simple option is used first, document that decision in `DECISIONS.md`.
- Use safe defaults so the plugin works even before an admin customizes anything.

Later, settings may include:

- Page title.
- Message.
- Contact email.
- Contact phone.
- Social links.
- Template choice.
- Custom HTML.

## Security Basics

Security should be part of the first functional implementation, not added later.

Important rules:

- Check capabilities with `manage_options` before showing or saving admin settings.
- Use nonces for form submissions.
- Validate values where possible before sanitizing.
- Sanitize all user input before saving.
- Escape all output before displaying it.
- Do not trust values just because they came from the WordPress admin or database.
- Do not implement custom HTML in Phase 1.

## Bypass Rules

The first functional version should allow administrators to bypass maintenance mode.

A practical default is to allow users with `manage_options` to see the normal site.

Future versions may support:

- Bypass links.
- Role-based bypass.
- IP-based bypass.
- Temporary preview URLs.

These should not be added until the basic version is working.

## Custom HTML

Custom HTML is planned for a later version.

Because custom HTML can create security risks, it should be handled carefully. Future implementation should decide which HTML tags are allowed and should use WordPress sanitization functions designed for HTML.

Custom HTML is out of scope for Phase 1.

## Extensibility

The plugin should begin simple. As it grows, code may be separated into files or classes for:

- Admin settings.
- Front-end maintenance rendering.
- Settings validation.
- Templates.

Do not add this structure too early. Add it when it makes the code easier to maintain.
