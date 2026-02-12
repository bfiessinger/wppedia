# Architecture Overview

This page explains the core pieces of WPPedia so extension work can be scoped correctly.

## 1) Runtime bootstrap

- The plugin entry point is `wppedia.php`.
- The `WPPedia` class initializes shared services in its internal container.
- During initialization, the plugin registers:
  - template handling,
  - REST controller,
  - WP query setup,
  - admin interfaces,
  - options/settings,
  - post meta,
  - post type registration,
  - cross-link and tooltip modules,
  - DB upgrade routines.

### Why this matters for extension authors

When extending behavior, choose the integration layer based on timing:

- **Theme/frontend output change** → template overrides/hooks.
- **Data/query behavior** → query setup/helpers/hooks.
- **Admin settings behavior** → options/admin hooks.
- **Cross-link/tooltip behavior** → module-level hooks/filters.

## 2) Folder-level map

- `core/classes/` – object-oriented systems (admin/options/template modules, etc.).
- `core/inc/` – functional helpers and glue code.
- `template-hooks/` – hook callbacks connected to template action points.
- `templates/` – default rendered templates that can be overridden.
- `source/` and `dist/` – source assets and compiled bundles.
- `tests/` – PHPUnit tests.

## 3) Rendering flow (high level)

1. Request resolves to WPPedia archive/single/search context.
2. Template loading logic determines plugin template vs theme override.
3. Template files fire action hooks (`do_action`) for sections.
4. `template-hooks/*.php` callbacks render concrete blocks.
5. Additional modules (search, navigation, cross-link, tooltip) alter output where applicable.

## 4) Stable extension points

Most stable extension points in this project are:

- action/filter hooks with `wppedia_` prefix,
- template overrides in your theme,
- selected shortcodes and helper functions.

Prefer these over editing plugin core files directly.
