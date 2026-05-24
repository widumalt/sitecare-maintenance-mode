# ROADMAP.md

This roadmap describes a practical path for building **SitePause**.

The project should move in small steps. Each phase should produce something understandable, testable, and useful.

## Phase 0: Planning

Status: completed

Goals:

- Create project planning documents.
- Define the plugin purpose.
- Agree on the first version scope.
- Avoid creating plugin source files until implementation is requested.

Deliverables:

- `AGENTS.md`
- `PROJECT_BRIEF.md`
- `ROADMAP.md`
- `ARCHITECTURE.md`
- `DECISIONS.md`
- `TESTING_CHECKLIST.md`

## Phase 1: Basic Plugin Skeleton

Status: current/next phase

Goal: make WordPress recognize the plugin in LocalWP.

Planned skeleton structure:

```text
sitepause-custom-html-offline-maintenance-mode-activator.php
readme.txt
uninstall.php
index.php
.gitignore

includes/
- index.php

admin/
- index.php

public/
- index.php

templates/
- index.php

assets/
- index.php

languages/
- index.php
```

Planned work:

- Add the main plugin PHP file.
- Add the WordPress plugin header.
- Add basic plugin constants.
- Add basic folders for future organization.
- Add placeholder `index.php` files to prevent direct directory browsing.
- Add a basic `readme.txt`.
- Add a basic `uninstall.php`.
- Add a basic `.gitignore`.
- Confirm the plugin appears in the WordPress admin Plugins screen.

Success criteria:

- GitHub repo contains the plugin skeleton.
- WordPress detects the plugin in LocalWP.
- Plugin activates without fatal errors.
- Plugin deactivates without fatal errors.
- No actual maintenance mode behavior is added yet unless specifically requested.

## Phase 2: Functional Maintenance Mode MVP

Goal: add the first working maintenance mode behavior.

Planned work:

- Add an admin menu page.
- Add a maintenance mode toggle.
- Save the setting using the WordPress Options API.
- Detect normal front-end visitor requests.
- Show a simple maintenance page when maintenance mode is enabled.
- Allow administrators with `manage_options` to bypass the maintenance page.

Success criteria:

- Logged-out visitors see the maintenance page when enabled.
- Administrators can still view the normal site.
- Disabling maintenance mode restores normal visitor access.
- Admin settings are permission-checked, nonce-protected, sanitized, and escaped.

## Phase 3: Custom Text Settings

Goal: let admins customize the visitor-facing content.

Planned work:

- Add fields for page title and message.
- Sanitize saved text.
- Escape displayed text.
- Provide sensible defaults when fields are empty.

Success criteria:

- Admins can change the maintenance page text.
- Unsafe input is not rendered directly.
- Empty settings do not break the page.

## Phase 4: Contact Details

Goal: let visitors know how to contact the site owner.

Planned work:

- Add optional contact fields such as email, phone, and social link.
- Display only fields that have values.
- Validate and sanitize each field appropriately.

Success criteria:

- Contact details can be added or removed.
- Invalid or unsafe values are handled safely.
- The maintenance page remains clear and uncluttered.

## Phase 5: Preview Mode

Goal: let administrators view the maintenance page before enabling it for visitors.

Planned work:

- Add a preview action or preview link in the admin screen.
- Only allow users with the correct capability to preview.
- Render the same maintenance page used by visitors.

Success criteria:

- Admins can preview the page while maintenance mode is off.
- Visitors are not affected by preview mode.
- Preview access is protected.

## Phase 6: Templates and Styling

Goal: provide simple design choices without making the plugin complicated.

Planned work:

- Add a small set of templates.
- Add basic design settings such as logo, colors, or layout choice.
- Keep default styling clean and accessible.

Success criteria:

- Admins can choose a template.
- The maintenance page looks good on desktop and mobile.
- Styling options do not make the settings screen confusing.

## Phase 7: Hardening and Polish

Goal: improve reliability before broader use.

Planned work:

- Review security and escaping.
- Test with common WordPress themes.
- Test with common caching plugins.
- Add developer documentation where helpful.
- Consider automated tests if the codebase grows.

Success criteria:

- The plugin is stable across normal WordPress setups.
- Known edge cases are documented.
- The code remains easy to understand.
