# TESTING_CHECKLIST.md

This checklist is for testing **SiteCare Maintenance Mode** as the plugin is built.

The project is preparing for the first stable release, version `1.0.0`.

## Version 1.0.0 Release Checklist

Use this checklist before tagging or publishing the first stable release.

### Version and Files

- [ ] Main plugin header shows version `1.0.0`.
- [ ] `SITECARE_MAINTENANCE_VERSION` is `1.0.0`.
- [ ] `readme.txt` stable tag is `1.0.0`.
- [ ] `readme.txt` changelog includes version `1.0.0`.
- [ ] No Composer, npm, React, framework, licensing, payment, email capture, or unrelated feature files were added.
- [ ] Plugin source files and preset templates are included.
- [ ] LocalWP core WordPress files, uploads, database files, and debug logs are not committed.

### Activation and Basic Admin

- [ ] Plugin appears in WordPress Admin > Plugins.
- [ ] Plugin activates without a fatal error.
- [ ] Plugin deactivates without a fatal error.
- [ ] WordPress Admin remains accessible after activation.
- [ ] Settings > SiteCare Maintenance opens for administrators.
- [ ] Non-admin users cannot manage plugin settings.
- [ ] Admin tabs appear in order: General, Content, Design, Custom HTML, Bypass, Import / Export, Preview & Reset.

### Settings Security

- [ ] Settings saves require the `manage_options` capability.
- [ ] Settings forms use nonce protection.
- [ ] Saved values are sanitized before saving.
- [ ] Admin output is escaped before display.
- [ ] Custom HTML is sanitized with `wp_kses_post()`.
- [ ] Import validates JSON and ignores unknown, unsafe, or invalid values.
- [ ] Export downloads only SiteCare Maintenance Mode settings and plugin metadata.

### Maintenance Mode Behavior

- [ ] Manual maintenance mode can be enabled and disabled.
- [ ] Scheduled maintenance mode validates required start and end times.
- [ ] Schedule start must be before schedule end.
- [ ] Schedule uses the WordPress site timezone.
- [ ] Logged-out visitors see the maintenance page when maintenance mode is active.
- [ ] Logged-out visitors see the normal site when maintenance mode is inactive.
- [ ] Administrators can view the normal site while maintenance mode is active.
- [ ] Selected bypass roles can view the normal site while maintenance mode is active.
- [ ] Whitelisted exact IPv4 and IPv6 addresses can bypass maintenance mode.
- [ ] `wp-admin` remains accessible.
- [ ] `wp-login.php` remains accessible.
- [ ] AJAX requests are not blocked.
- [ ] REST API requests are not blocked.
- [ ] Cron requests are not blocked.

### SEO and Headers

- [ ] Real visitor maintenance responses send HTTP `503`.
- [ ] Real visitor maintenance responses include `Retry-After: 3600`.
- [ ] Real visitor maintenance responses include no-cache headers.
- [ ] Maintenance page HTML includes `<meta name="robots" content="noindex, nofollow">`.
- [ ] Preview mode does not send HTTP `503`.
- [ ] Preview mode does not send `Retry-After`.

### Content and Design

- [ ] Custom page title displays correctly.
- [ ] Custom message displays correctly.
- [ ] Contact email displays only when provided and uses `mailto:`.
- [ ] Contact phone displays only when provided and uses `tel:`.
- [ ] Social links display only when provided and open in a new tab with safe `rel` attributes.
- [ ] Footer text displays only when provided.
- [ ] Logo displays when selected and has meaningful alt text.
- [ ] Background color applies correctly.
- [ ] Text color applies correctly.
- [ ] Layout width options apply correctly.
- [ ] Countdown appears only when enabled with a valid future target.
- [ ] Countdown does not automatically disable maintenance mode.

### Preset Templates

- [ ] Classic saves correctly and displays correctly in preview.
- [ ] Center Card saves correctly and displays correctly in preview.
- [ ] Minimal saves correctly and displays correctly in preview.
- [ ] Bold Panel saves correctly and displays correctly in preview.
- [ ] Split Screen saves correctly and displays correctly in preview.
- [ ] All five presets display correctly for logged-out visitors when maintenance mode is active.
- [ ] All five presets are readable on mobile, tablet, and desktop.
- [ ] Preset template files output only plugin-managed preset markup, not full standalone HTML documents.

### Custom HTML Override

- [ ] When Custom HTML Override is disabled, the selected preset displays normally.
- [ ] When Custom HTML Override is enabled and the custom HTML field is empty, the selected preset displays normally.
- [ ] When Custom HTML Override is enabled and non-empty, only the custom HTML content displays.
- [ ] Title, message, logo, countdown, contact details, social links, footer text, and preset content are hidden during active custom HTML override.
- [ ] Safe custom HTML such as `<p>`, `<strong>`, `<ul>`, `<li>`, and `<a>` displays correctly.
- [ ] Unsafe markup such as `<script>alert(1)</script>` is removed or does not execute.

### Import, Export, Reset, and Uninstall

- [ ] Export downloads readable JSON with plugin metadata and settings only.
- [ ] Import restores valid exported settings.
- [ ] Invalid JSON shows an error notice.
- [ ] Invalid role keys are ignored during import.
- [ ] Invalid IP addresses are ignored during import.
- [ ] Imported custom HTML is sanitized again.
- [ ] Reset Settings changes visible fields to defaults without saving until Save Settings is clicked.
- [ ] Uninstall deletes only `sitecare_maintenance_options`.

### Final Debug Checks

- [ ] `wp-content/debug.log` has no new PHP warnings, notices, or fatal errors after activation.
- [ ] `wp-content/debug.log` has no new PHP warnings, notices, or fatal errors after saving settings.
- [ ] `wp-content/debug.log` has no new PHP warnings, notices, or fatal errors after previewing all presets.
- [ ] `wp-content/debug.log` has no new PHP warnings, notices, or fatal errors after import/export testing.

## WordPress.org Submission Notes

Use these notes when preparing the WordPress.org Plugin Directory SVN repository.

- [ ] Put functional plugin code in `/trunk`.
- [ ] Copy the `1.0.0` release from `/trunk` to `/tags/1.0.0`.
- [ ] Put WordPress.org directory assets in the SVN `/assets` folder, not inside the plugin code unless they are runtime plugin assets.
- [ ] Required directory asset dimensions to prepare before submission:
  - `assets/icon-128x128.png` at 128 x 128 px.
  - `assets/icon-256x256.png` at 256 x 256 px.
  - `assets/banner-772x250.png` at 772 x 250 px.
  - `assets/banner-1544x500.png` at 1544 x 500 px.
  - `assets/screenshot-1.png` sized for the settings page screenshot.
  - `assets/screenshot-2.png` sized for the maintenance page screenshot.
- [ ] Do not generate fake screenshots, icons, or banners. Use real plugin screenshots and brand-approved artwork.
- [ ] Do not commit LocalWP files, debug logs, database exports, uploads, build files, ZIP files, backups, or temporary files to SVN.
- [ ] Include release plugin files only: `sitecare-maintenance-mode.php`, `readme.txt`, `uninstall.php`, `index.php`, `includes/`, `admin/`, `public/`, `templates/`, plugin runtime `assets/`, and `languages/`.
- [ ] Keep development planning docs out of the WordPress.org `/trunk` release copy unless intentionally submitting them as documentation.

## Repository Checks

- [ ] The repo contains the planned documentation files.
- [ ] Documentation consistently uses the name **SiteCare Maintenance Mode**.
- [ ] Documentation consistently uses the slug `sitecare-maintenance-mode`.
- [ ] Roadmap items are described as future work unless they are part of the current phase.
- [ ] No out-of-scope source files were created.

## LocalWP Environment Checklist

- [ ] LocalWP is installed.
- [ ] LocalWP site created: `sitecare-plugin-dev`.
- [ ] WordPress admin login works.
- [ ] Plugin source folder exists: `C:\dev\WP-maintenance-offline-plugin`.
- [ ] GitHub remote points to `https://github.com/widumalt/sitecare-maintenance-mode`.
- [ ] Plugin folder is linked into LocalWP `wp-content/plugins` using a Windows junction.
- [ ] `WP_DEBUG` is enabled.
- [ ] `WP_DEBUG_LOG` is enabled.
- [ ] `WP_DEBUG_DISPLAY` is disabled.
- [ ] `debug.log` is checked after activation.

## Git Checklist

- [ ] `git status` checked before changes.
- [ ] Changes committed after each milestone.
- [ ] Changes pushed to GitHub.
- [ ] LocalWP core WordPress files are not committed.
- [ ] `wp-content/uploads` is not committed.
- [ ] Database files are not committed.
- [ ] Debug logs are not committed.

## Phase 1 Skeleton Testing

Use these after the plugin skeleton is created.

- [ ] Plugin appears in WordPress Admin > Plugins.
- [ ] Plugin name displays correctly.
- [ ] Plugin description displays correctly.
- [ ] Plugin version displays correctly.
- [ ] Plugin activates without fatal error.
- [ ] Plugin deactivates without fatal error.
- [ ] No PHP warnings appear in `debug.log`.
- [ ] Admin area remains accessible after activation.

## Plugin Activation Checks

Use these after the first plugin file is created.

- [ ] WordPress detects the plugin.
- [ ] The plugin can be activated.
- [ ] The plugin can be deactivated.
- [ ] Activation does not create a fatal error.
- [ ] Deactivation restores normal WordPress behavior.

## Admin Settings Checks

Use these after an admin settings screen is added.

- [ ] Only authorized users can access the settings screen.
- [ ] The maintenance mode toggle appears in the admin area.
- [ ] Settings save successfully.
- [ ] Saved settings persist after refreshing the page.
- [ ] Settings are validated where possible.
- [ ] Settings are sanitized before saving.
- [ ] Settings forms use nonces.
- [ ] Saved values are escaped before display.
- [ ] Success and error messages are understandable.

## Maintenance Mode Checks

Use these after front-end maintenance mode behavior is added.

- [ ] Maintenance mode can be enabled.
- [ ] Maintenance mode can be disabled.
- [ ] Logged-out visitors see the maintenance page when enabled.
- [ ] Logged-out visitors see the normal site when disabled.
- [ ] Administrators can view the normal site when maintenance mode is enabled.
- [ ] The WordPress admin area remains accessible to administrators.
- [ ] The WordPress login page remains accessible.
- [ ] AJAX requests are not blocked unless deliberately planned.
- [ ] REST API requests are not blocked unless deliberately planned.
- [ ] Cron requests are not blocked unless deliberately planned.

## Maintenance Page Content Checks

Use these after editable content fields are added.

- [ ] Default title and message display correctly.
- [ ] Custom title displays correctly.
- [ ] Custom message displays correctly.
- [ ] Empty fields fall back to safe defaults.
- [ ] Special characters display safely.
- [ ] Unsafe scripts or markup are not executed.

## Contact Details Checks

Use these after contact fields are added.

- [ ] Email address displays only when provided.
- [ ] Phone number displays only when provided.
- [ ] Social links display only when provided.
- [ ] Invalid values are handled safely.
- [ ] Removing a contact value removes it from the maintenance page.

## Preview Mode Checks

Use these after preview mode is added.

- [ ] Authorized admins can preview the maintenance page.
- [ ] Unauthorized users cannot access preview mode.
- [ ] Preview mode works when maintenance mode is disabled.
- [ ] Preview mode does not turn maintenance mode on for visitors.
- [ ] Preview content matches the real maintenance page.

## Template and Styling Checks

Use these after templates or design settings are added.

- [ ] Template choices save correctly.
- [ ] The selected template appears on the maintenance page.
- [ ] The page is readable on desktop.
- [ ] The page is readable on mobile.
- [ ] Colors have enough contrast.
- [ ] Logo or image settings do not break the layout.

## Compatibility Checks

Use these before considering a release.

- [ ] Test with a default WordPress theme.
- [ ] Test with a custom or popular theme.
- [ ] Test while logged out.
- [ ] Test while logged in as an administrator.
- [ ] Test while logged in as a non-administrator.
- [ ] Test with pretty permalinks enabled.
- [ ] Test with pretty permalinks disabled.
- [ ] Test basic behavior with a caching plugin if possible.

## Regression Checks

Use these after every meaningful change.

- [ ] Maintenance mode still turns on.
- [ ] Maintenance mode still turns off.
- [ ] Admin bypass still works.
- [ ] Visitors still see the correct page.
- [ ] Settings still save correctly.
- [ ] No new PHP warnings or fatal errors appear.
