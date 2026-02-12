# Action Reference

This page lists **all WPPedia action hooks** currently fired in the plugin, with context and extension guidance.

> Scope note: this reference focuses on hooks prefixed with `wppedia_`.

## `wppedia_admin_settings_page_header_content`

- **Type**: action
- **Where fired**: settings page rendering (`core/classes/class-options.php`)
- **Purpose**: add custom content above WPPedia settings sections in wp-admin.
- **Typical use**: display custom notices, onboarding links, or extension diagnostics.

```php
add_action('wppedia_admin_settings_page_header_content', function () {
    echo '<div class="notice notice-info"><p>Extension settings are available below.</p></div>';
});
```

## `wppedia_before_main_content`

- **Type**: action
- **Where fired**: archive and single templates before primary content (`templates/archive.php`, `templates/single.php`)
- **Purpose**: inject wrappers/navigation/search/header elements before the main glossary content starts.
- **Typical use**: add breadcrumbs, hero blocks, or custom intro banners.

## `wppedia_after_main_content`

- **Type**: action
- **Where fired**: archive and single templates after primary content (`templates/archive.php`, `templates/single.php`)
- **Purpose**: render pagination, close wrappers, or append supplementary blocks.
- **Typical use**: add recommendation blocks or newsletter CTA after glossary content.

## `wppedia_sidebar`

- **Type**: action
- **Where fired**: archive and single templates in sidebar region (`templates/archive.php`, `templates/single.php`)
- **Purpose**: output sidebar content for glossary screens.
- **Typical use**: replace default sidebar widget area output.

## `wppedia_archive_description`

- **Type**: action
- **Where fired**: glossary archive template header area (`templates/archive.php`)
- **Purpose**: output frontpage/taxonomy descriptions depending on context.
- **Typical use**: inject custom archive intro text by taxonomy/site section.

## `wppedia_before_post_loop`

- **Type**: action
- **Where fired**: archive template before glossary loop starts (`templates/archive.php`)
- **Purpose**: open wrappers, add controls, render pre-loop UI.
- **Typical use**: add sorting controls, custom filters, or result counts.

## `wppedia_after_post_loop`

- **Type**: action
- **Where fired**: archive template after glossary loop ends (`templates/archive.php`)
- **Purpose**: close loop wrappers and add post-loop content.
- **Typical use**: append archive summary or support CTA below listings.

## `wppedia_no_entries_found`

- **Type**: action
- **Where fired**: archive and single templates in empty/no-content states (`templates/archive.php`, `templates/single.php`)
- **Purpose**: render fallback when no glossary entry matches the current request.
- **Typical use**: customize “no results” experience with links/search suggestions.

## `wppedia_before_single_post`

- **Type**: action
- **Where fired**: single template, before single glossary post body (`templates/single.php`)
- **Purpose**: prepend content to single entry rendering pipeline.
- **Typical use**: add badges, compliance notices, or metadata wrappers.

## `wppedia_single_post`

- **Type**: action
- **Where fired**: single entry content template (`templates/content-single.php`)
- **Purpose**: core single-entry assembly hook (image, title, content, pagination).
- **Typical use**: insert custom blocks among default single content components.

## `wppedia_after_single_post`

- **Type**: action
- **Where fired**: single template, after single glossary post body (`templates/single.php`)
- **Purpose**: append content after the main single entry output.
- **Typical use**: add related terms, feedback widgets, or author info.

## `wppedia_before_loop_item`

- **Type**: action
- **Where fired**: each archive loop item before card/link content (`templates/content-archive.php`)
- **Purpose**: prepend per-item content.
- **Typical use**: add custom badges or item metadata prefix.

## `wppedia_before_loop_item_title`

- **Type**: action
- **Where fired**: each loop item before title output (`templates/content-archive.php`)
- **Purpose**: render components like featured image before title.
- **Typical use**: output thumbnail, icons, or category chips.

## `wppedia_loop_item_title`

- **Type**: action
- **Where fired**: each loop item title slot (`templates/content-archive.php`)
- **Purpose**: render the glossary entry title block.
- **Typical use**: replace default title markup with custom heading structure.

## `wppedia_after_loop_item_title`

- **Type**: action
- **Where fired**: each loop item after title block (`templates/content-archive.php`)
- **Purpose**: render excerpt/content snippets and link closures.
- **Typical use**: append CTA or metadata row below title/excerpt.

## `wppedia_after_loop_item`

- **Type**: action
- **Where fired**: each loop item after all item content (`templates/content-archive.php`)
- **Purpose**: finalize per-item rendering.
- **Typical use**: append separators, analytics hooks, or item-level tools.

## `wppedia_before_search_form`

- **Type**: action
- **Where fired**: search form template before `<form>` markup (`templates/search/form.php`)
- **Purpose**: prepend UI around search form.
- **Typical use**: add custom heading/help text above search input.

## `wppedia_after_search_form`

- **Type**: action
- **Where fired**: search form template after form markup (`templates/search/form.php`)
- **Purpose**: append UI after search component.
- **Typical use**: add advanced search links or keyboard hints.

## Extension pattern notes

- Prefer registering actions in a dedicated extension plugin file/class.
- Use explicit priorities and document why (`5`, `10`, `30`, etc.).
- For upgrade resilience, avoid overriding full templates when a hook insertion can achieve the goal.
