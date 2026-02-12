# Shortcodes and Frontend Integration

This page explains practical frontend integration patterns beyond template overrides.

## 1) Understand the boundary: content vs shell

- **Content shell** (archive page, wrapper, list/single layout) is primarily template-driven.
- **In-content behavior** (cross-linking, tooltip text, excerpts, search form attributes) is often hook/filter-driven.

Use the right tool for the right layer.

## 2) Search form integration

WPPedia search form output can be customized by:

- template override of `templates/search/form.php`,
- search form attribute filters,
- before/after search form actions.

Useful when integrating utility CSS frameworks or custom JS search behavior.

## 3) Navigation integration

The initial-character navigation can be:

- enabled/disabled by settings,
- repositioned via template hooks,
- overridden via template customization.

## 4) Companion plugin pattern (recommended)

For reusable frontend behavior, create a custom plugin, e.g.:

- `my-company-wppedia-extension.php`
- `src/Rendering/GlossaryLayout.php`
- `src/Enhancements/TooltipTweaks.php`

Benefits:

- independent versioning,
- clearer ownership,
- easier reuse across sites.
