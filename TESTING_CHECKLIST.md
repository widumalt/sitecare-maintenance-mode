# TESTING_CHECKLIST.md

## TESTING_CHECKLIST.md

This checklist is for testing **SiteCare Maintenance Mode** as the plugin is built.

The project is ready for Phase 1 implementation. Phase 1 should test that WordPress can detect, activate, and deactivate the plugin skeleton.

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
