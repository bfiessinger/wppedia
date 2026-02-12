# Template Hooks Deep Dive

This page explains how WPPedia’s template hooks are wired and how to safely extend them.

## 1) Hook registration model

WPPedia maps template sections to callback functions in `template-hooks/hooks.php`.

Examples:

- `wppedia_before_main_content` → wrapper start/navigation/search injection.
- `wppedia_single_post` → featured image/title/content/page links.
- `wppedia_before_loop_item_title` / `wppedia_after_loop_item_title` → loop card internals.

Because these are standard WordPress actions, you can:

- add additional callbacks,
- remove default callbacks,
- replace output order via priorities.

## 2) Reordering default content blocks

Example: move pagination earlier in the page:

```php
remove_action('wppedia_after_main_content', 'wppedia_posts_pagination', 10);
add_action('wppedia_before_post_loop', 'wppedia_posts_pagination', 50);
```

Use this pattern carefully and keep it in a dedicated integration file.

## 3) Inserting custom content around single entries

```php
add_action('wppedia_before_single_post', function () {
    echo '<div class="glossary-single-notice">Reviewed by editorial team</div>';
}, 5);
```

## 4) Guarding your hooks

For compatibility:

- register hooks only when WPPedia is active,
- use `function_exists('WPPedia')` checks when needed,
- isolate callbacks in namespaced classes for large projects.

## 5) Debugging hook behavior

- Confirm callback priority order.
- Temporarily log active callbacks with `has_action` checks.
- Verify template actually fires the action (`do_action`).
