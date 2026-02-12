# Template Overrides: Step-by-Step

## 1) Default override location

By default, copy template files into:

```text
your-theme/wppedia/
```

Examples:

- `templates/archive.php` → `your-theme/wppedia/archive.php`
- `templates/content-single.php` → `your-theme/wppedia/content-single.php`
- `templates/search/form.php` → `your-theme/wppedia/search/form.php`

## 2) Overriding a loop item layout

If you want a custom card design for glossary list entries:

1. Copy `templates/content-archive.php` to your theme override path.
2. Keep existing action hooks in place where possible.
3. Wrap sections with your own CSS classes.
4. Add any additional block after `wppedia_after_loop_item_title` via hook.

This gives you custom markup while preserving future extension compatibility.

## 3) Changing template path globally

If your team uses a different structure, change the template path with:

```php
add_filter('wppedia_template_path', function () {
    return 'integrations/wppedia';
});
```

Then WPPedia will load from `your-theme/integrations/wppedia/...`.

## 4) Override maintenance workflow

For each WPPedia update:

1. Review changed files under plugin `templates/`.
2. Compare with your overridden files.
3. Merge relevant fixes while preserving your customizations.
4. Smoke-test archive/single/search/tooltip UX.

## 5) Common pitfalls

- Removing core action hooks unintentionally.
- Copying too many templates upfront (harder maintenance).
- Ignoring child theme precedence when testing.
