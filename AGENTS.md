# AGENTS.md

## Project

This repository is for a WordPress plugin named **SitePause - Custom HTML Offline & Maintenance Mode Activator**.

The plugin will let site administrators enable a maintenance/offline mode, show regular visitors a custom maintenance page, and allow administrators to bypass that page while they work on the site.

## Repository Status

- The repository has completed the initial planning stage.
- Source files may now be created only when the user explicitly requests implementation.
- Do not create random, unrelated, or out-of-scope files.
- Do not create advanced features unless they are part of the requested phase.

## Local Development Environment

- OS: Windows
- Local source folder: `C:\dev\WP-maintenance-offline-plugin`
- GitHub repo: `https://github.com/widumalt/sitecare-maintenance-mode`
- WordPress local environment: LocalWP
- LocalWP site name: `sitecare-plugin-dev`
- The plugin will be linked into the LocalWP `wp-content/plugins` folder using a Windows junction.

## Plugin Identity

- Plugin name: `SitePause - Custom HTML Offline & Maintenance Mode Activator`
- Plugin slug: `sitecare-maintenance-mode`
- Main plugin file: `sitecare-maintenance-mode.php`
- Text domain: `sitecare-maintenance-mode`
- Prefix all functions, classes, constants, options, and hooks with `sitecare` or `SiteCare` to avoid conflicts.

## Goals for Future Agents

- Keep changes practical and beginner-friendly.
- Prefer simple WordPress plugin patterns before introducing advanced abstractions.
- Make small, focused changes that are easy to review.
- Explain important WordPress concepts clearly in documentation and comments.
- Avoid unrelated refactors or tooling changes.

## Development Rules

- Follow normal WordPress plugin development practices.
- Use PHP, WordPress hooks, WordPress Options API, simple CSS, and simple JavaScript only.
- Do not use Composer, npm, React, Laravel, Symfony, or any build tools in the MVP.
- Do not create custom database tables in the MVP.
- Do not add licensing, payment, marketplace, or external API code in the MVP.
- Keep code beginner-friendly and well-commented.
- Make small, focused changes.
- Do not refactor unrelated files.

## Security Rules

- Check user permissions using `manage_options` before showing or saving settings.
- Use WordPress nonces for admin forms.
- Validate values where possible before sanitizing.
- Sanitize all input before saving.
- Escape all output before displaying it.
- Do not trust option values just because they come from the WordPress database.
- Custom HTML is out of scope for Phase 1 and should not be implemented yet.

## Phase 1 Allowed Scope

Codex may create the basic plugin skeleton when the user requests Phase 1 implementation:

- Main plugin file.
- Plugin header.
- Basic constants.
- Basic `includes` folder.
- Basic `admin` folder.
- Basic `public` folder.
- Basic `templates` folder.
- Basic `assets` folder.
- Basic `languages` folder.
- Basic `readme.txt`.
- Basic `uninstall.php`.
- Basic `index.php` files.
- Basic `.gitignore`.

## Phase 1 Must Not Create

- Full custom HTML editor.
- Template gallery.
- Countdown timer.
- Email capture.
- API integrations.
- License activation.
- Payment logic.
- WooCommerce-specific controls.
- Complex styling system.
- React or block editor features.

## Expected Future Plugin Direction

Phase 1 creates only the plugin skeleton so WordPress can detect the plugin. Phase 2 will add the first working maintenance mode MVP:

1. An admin settings screen with a maintenance mode toggle.
2. Saved settings using the WordPress Options API.
3. Front-end logic that shows a maintenance page to public visitors.
4. Admin bypass so logged-in administrators can still view the site.

Later versions may add:

- Maintenance page templates.
- Contact details.
- Custom HTML content.
- Preview mode.
- More styling controls.

## File and Structure Guidance

Do not create folders or files just because they might be useful later. Add structure only when it matches the requested phase.

For Phase 1, the planned skeleton folders are allowed because they keep the plugin organized for the MVP. More advanced structure should wait until it is needed.

## Manual Testing Rules

After implementation, remind the user to test:

- Plugin appears in WordPress Admin > Plugins.
- Plugin activates without fatal errors.
- Plugin deactivates without fatal errors.
- No errors appear in `wp-content/debug.log`.
- Admin area remains accessible.

## Communication Style

Write documentation and comments for someone who is learning WordPress plugin development. Use plain language, concrete examples, and short explanations.
