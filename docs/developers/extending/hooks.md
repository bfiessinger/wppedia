# Hook Reference and Extension Patterns

This page focuses on practical hooks for extending WPPedia behavior.

## 1) Template path and resolution hooks

### `wppedia_template_path` (filter)

Use this to change the theme subfolder WPPedia reads templates from.

```php
add_filter('wppedia_template_path', function ($path) {
    return 'my-custom-glossary';
});
```

### `wppedia_locate_template` (filter)

Lets you rewrite the final template path before it is loaded.

### `wppedia_get_template_part` (filter)

Lets you intercept template part loading and point to a template shipped by your own plugin.

## 2) Layout and loop action hooks

Common action points used by archive/single templates:

- `wppedia_before_main_content`
- `wppedia_after_main_content`
- `wppedia_sidebar`
- `wppedia_before_post_loop`
- `wppedia_after_post_loop`
- `wppedia_before_loop_item`
- `wppedia_before_loop_item_title`
- `wppedia_loop_item_title`
- `wppedia_after_loop_item_title`
- `wppedia_after_loop_item`
- `wppedia_before_single_post`
- `wppedia_single_post`
- `wppedia_after_single_post`
- `wppedia_archive_description`
- `wppedia_no_entries_found`

### Example: prepend a custom archive intro block

```php
add_action('wppedia_before_post_loop', function () {
    echo '<div class="my-glossary-intro">Browse our glossary by topic.</div>';
}, 5);
```

### Example: add a custom CTA below excerpts

```php
add_action('wppedia_after_loop_item_title', function () {
    echo '<a class="my-cta" href="/contact">Need help with this term?</a>';
}, 30);
```

## 3) Title, excerpt, and form filters

Useful output filters:

- `wppedia_page_title`
- `wppedia_tooltip_excerpt`
- `wppedia_show_page_title`
- `wppedia_search_input_id`
- `wppedia_searchform_attrs__role`
- `wppedia_searchform_attrs__method`
- `wppedia_searchform_attrs__class`
- `wppedia_searchform_attrs__id`

### Example: customize glossary page title

```php
add_filter('wppedia_page_title', function ($title) {
    if (is_post_type_archive('glossary')) {
        return 'Knowledge Base';
    }
    return $title;
});
```

## 4) Admin-side integration hook

- `wppedia_admin_settings_page_header_content` can be used to output custom information above settings sections.

## 5) Hooking strategy recommendations

- Use explicit priorities and comment why (`10`, `20`, etc.).
- Prefer one callback per concern.
- Avoid anonymous functions in large projects where unhooking is required.
- Add compatibility checks (`function_exists`, `class_exists`) for safer deployments.
