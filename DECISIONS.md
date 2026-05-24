# DECISIONS.md

This file records project decisions for **SitePause**.

Use it to explain why important choices were made. This helps future contributors understand the project without guessing.

## Decision Log

### 2026-05-23: Create planning documents before source code

Decision:

Create project planning and instruction files before creating plugin source files.

Reason:

The project started from an empty repository. Planning documents made the first implementation easier and reduced confusion.

Impact:

The initial planning stage is complete. Source files may now be created only when the requested task matches the roadmap phase.

### 2026-05-23: Use LocalWP as the first development environment

Decision:

Use LocalWP as the first development environment.

Reason:

LocalWP is easier for beginner WordPress plugin development than manually managing Docker containers.

Impact:

Docker Desktop can remain installed but will not be used for the MVP.

### 2026-05-23: Use GitHub as the source of truth

Decision:

Use GitHub as the source of truth for the project.

Reason:

Codex can work more cleanly with a GitHub repo, and Git gives version history.

Impact:

Every meaningful milestone should be committed and pushed to `https://github.com/widumalt/sitecare-maintenance-mode`.

### 2026-05-23: Move from planning-only mode to Phase 1 implementation readiness

Decision:

Move from planning-only mode to Phase 1 implementation readiness.

Reason:

Planning documents are complete and the user is ready to create the plugin skeleton.

Impact:

Source files may now be created only when the requested task matches the roadmap phase.

### 2026-05-23: Build the MVP first

Decision:

The first functional implementation should focus on a small maintenance mode MVP.

Reason:

A simple working plugin is easier to test, explain, and improve than a large first version with many settings.

Impact:

Phase 1 should create the skeleton only. Phase 2 should prioritize:

- Enable or disable maintenance mode.
- Show a maintenance page to visitors.
- Allow administrator bypass.

### 2026-05-23: Use standard WordPress APIs

Decision:

The plugin should use normal WordPress APIs for settings, hooks, permissions, and security.

Reason:

This keeps the plugin familiar to WordPress developers and easier for beginners to learn from.

Impact:

Future code should use tools such as:

- Options API.
- Admin hooks.
- Front-end hooks.
- Capability checks.
- Nonces.
- Sanitization and escaping functions.

### 2026-05-23: Keep MVP framework-free

Decision:

Keep the MVP framework-free.

Reason:

The project is for learning the WordPress plugin lifecycle.

Impact:

Do not use Composer, npm, React, Laravel, Symfony, or complex build tools in the MVP.

### 2026-05-23: Keep future features separate from the MVP

Decision:

Templates, contact details, custom HTML, and preview mode are planned for later phases.

Reason:

These features are useful, but they add design, security, and testing complexity.

Impact:

The MVP should not depend on these features. They should be added only after the basic maintenance mode behavior works reliably.

### 2026-05-23: Admin bypass should be permission-based

Decision:

The first bypass behavior should be based on a WordPress capability such as `manage_options`.

Reason:

Capability checks are a standard WordPress way to decide whether a user can manage site-level settings.

Impact:

Logged-in administrators should be able to view the normal site while maintenance mode is enabled. Logged-out visitors should see the maintenance page.

## How to Add New Decisions

When adding a new decision, include:

- Date.
- Decision.
- Reason.
- Impact.

Keep entries short and practical.
