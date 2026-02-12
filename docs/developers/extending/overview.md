# Extending WPPedia: Overview

WPPedia was designed to be extended via WordPress conventions. In most cases, you should create a small companion plugin or theme integration layer instead of modifying WPPedia core directly.

## 1) Recommended extension methods

### Method A: Hook-based customization (preferred)

Use `add_action` and `add_filter` against WPPedia hook names to:

- inject output before/after built-in blocks,
- alter computed labels/titles,
- adjust template paths and template file selection,
- tune search form attributes and other view-level data.

This method survives plugin updates best.

### Method B: Theme template overrides

Copy a template file from `templates/` into your theme under the WPPedia template path, then modify the copy.

Good for:

- major markup refactors,
- framework-specific wrappers,
- custom HTML structure.

### Method C: Companion plugin module

Create your own plugin that:

- loads after WPPedia,
- registers hooks on `init` / `wp` / template actions,
- encapsulates business logic for reusable deployments.

This method is ideal for agencies or productized implementations.

## 2) Decision matrix

- Need to **insert or reorder content**? → Start with hooks.
- Need to **change full markup**? → Use template overrides.
- Need **multi-site reuse/versioning**? → Companion plugin.

In real projects, these methods are often combined.

## 3) Safe extension checklist

- Never edit WPPedia plugin files directly in production.
- Prefix your own hooks/functions/classes.
- Document every WPPedia hook you rely on.
- Keep custom code in git.
- Re-test after each WPPedia update.
