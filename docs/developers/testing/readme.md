# Testing Guide

This page covers practical testing for WPPedia extensions and contributions.

## 1) Automated tests

Run the plugin’s PHPUnit suite:

```bash
vendor/bin/phpunit
```

Current baseline tests are in `tests/` and cover utility and glossary-related behavior.

## 2) Manual functional testing checklist

When changing extension points, always validate:

- Glossary archive renders correctly.
- Single glossary entry renders correctly.
- Search form behavior and search results.
- Initial-letter navigation behavior.
- Tooltip and cross-link behaviors (if enabled).

## 3) Template customization regression checklist

If you changed template hooks/overrides:

- Verify wrapper open/close structure is balanced.
- Verify default callbacks still execute (unless intentionally removed).
- Verify no duplicate blocks were introduced by mixed hooks and overrides.
- Verify mobile rendering and keyboard navigation.

## 4) Suggested test matrix for extension plugins

- **Theme matrix**: default theme + target production theme.
- **Config matrix**: with/without navigation, with/without search bar, with/without tooltips.
- **Content matrix**: short entries, long entries, special characters, umlauts/accented characters.

## 5) CI recommendations

For teams shipping WPPedia extensions:

- run PHPUnit on every PR,
- add static analysis/linting for your extension code,
- include at least one smoke test against a real WP environment before release.
