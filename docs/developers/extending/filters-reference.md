# Filter Reference

This page lists **all WPPedia filters** currently exposed by the plugin (prefix `wppedia_`) and explains what each one changes.

## Template loading and path filters

### `wppedia_template_path`

- **Where**: `WPPedia::template_path()` in `wppedia.php`
- **Input**: default template folder name (`wppedia`)
- **Returns**: theme subdirectory used for template overrides
- **Use case**: move overrides from `your-theme/wppedia/` to custom path.

### `wppedia_locate_template`

- **Where**: `wppedia_locate_template()` in `core/inc/template-functions.php`
- **Input**: resolved template path, template filename, template path base
- **Returns**: final template path loaded by helper
- **Use case**: force alternate template files from custom integration.

### `wppedia_get_template_part`

- **Where**: `wppedia_get_template_part()` in `core/inc/template-functions.php`
- **Input**: resolved template path, slug, optional name
- **Returns**: final template-part file path
- **Use case**: serve a template part from your extension plugin.

### `wppedia_custom_index_file`

- **Where**: template selection logic in `core/classes/class-template.php`
- **Input**: default custom index filename (`index-wppedia.php`)
- **Returns**: index template filename to look up in theme
- **Use case**: use alternate index integration filename.

## Layout, classes, and title filters

### `wppedia_body_class`

- **Where**: body class injection in `core/classes/class-template.php`
- **Input**: default class string (`wppedia-page wppedia`)
- **Returns**: class string appended to body class array
- **Use case**: align with theme-specific body-class conventions.

### `wppedia_post_class`

- **Where**: post class assembly in `core/classes/class-template.php`
- **Input**: post class array
- **Returns**: modified post class array
- **Use case**: add card/layout utility classes for glossary loop items.

### `wppedia_page_title`

- **Where**: `wppedia_page_title()` in `core/inc/template-functions.php`
- **Input**: calculated page title for archive/single/search/tax contexts
- **Returns**: final displayed title
- **Use case**: rename glossary archive title to brand-specific label.

### `wppedia_show_page_title`

- **Where**: page-title visibility check in `templates/archive.php`
- **Input**: default boolean (`true`)
- **Returns**: whether title should render
- **Use case**: suppress title when theme already renders its own header.

## Navigation and character list filters

### `wppedia_list_chars`

- **Where**: utility helper in `core/inc/utility-functions.php`
- **Input**: default initials/character list
- **Returns**: modified character list used by navigation logic
- **Use case**: limit navigation to specific ranges or add locale-specific characters.

### `wppedia_navigation_link__classes`

- **Where**: navigation link generation in `core/classes/class-template.php`
- **Input**: default classes array for char links
- **Returns**: class array
- **Use case**: inject framework classes for nav pills/buttons.

### `wppedia_navigation_link__active_class`

- **Where**: navigation link generation in `core/classes/class-template.php`
- **Input**: default active-class name (`active`)
- **Returns**: active class token
- **Use case**: map active state class to your CSS framework naming.

### `wppedia_navigation_link__name`

- **Where**: navigation link label generation in `core/classes/class-template.php`
- **Input**: default label text/slug for current link
- **Returns**: final display label
- **Use case**: localize or transform labels (e.g., uppercase mapping).

### `wppedia_navigation_link`

- **Where**: final navigation link HTML in `core/classes/class-template.php`
- **Input**: built link HTML string
- **Returns**: final HTML output
- **Use case**: wrap/augment rendered links with custom markup attributes.

## Search form filters

### `wppedia_search_input_id`

- **Where**: search template and localized script config (`templates/search/form.php`, `core/inc/assets.php`)
- **Input**: default input id (`wppedia_search_input`)
- **Returns**: input id used in markup and JS config
- **Use case**: avoid ID collisions in complex page builders.

### `wppedia_searchform_attrs__role`

- **Where**: `wppedia_get_search_form_attrs()` in `core/inc/template-functions.php`
- **Input**: default role (`search`)
- **Returns**: form role attribute
- **Use case**: customize semantics for accessibility strategy.

### `wppedia_searchform_attrs__method`

- **Where**: `wppedia_get_search_form_attrs()`
- **Input**: default method (`GET`)
- **Returns**: form method
- **Use case**: adjust request style for custom integrations.

### `wppedia_searchform_attrs__class`

- **Where**: `wppedia_get_search_form_attrs()`
- **Input**: default class list (`search-form wppedia-search`)
- **Returns**: form class string
- **Use case**: append design system utility classes.

### `wppedia_searchform_attrs__id`

- **Where**: `wppedia_get_search_form_attrs()`
- **Input**: default form id (`wppedia_searchform`)
- **Returns**: form id
- **Use case**: avoid duplicate IDs in embedded contexts.

## Tooltip and excerpt filters

### `wppedia_tooltip_excerpt`

- **Where**: `get_the_excerpt_wppedia()` in `core/inc/template-functions.php`
- **Input**: computed tooltip excerpt HTML/text
- **Returns**: final tooltip excerpt output
- **Use case**: shorten, sanitize, or annotate tooltip text.

### `wppedia_tooltip_before_excerpt`

- **Where**: tooltip rendering in `core/classes/modules/class-tooltip.php`
- **Input**: default opening wrapper (`<div class="wppedia-tooltip-content">`)
- **Returns**: opening wrapper markup
- **Use case**: change wrapper element/classes for styling frameworks.

### `wppedia_tooltip_after_excerpt`

- **Where**: tooltip rendering in `core/classes/modules/class-tooltip.php`
- **Input**: default closing wrapper (`</div>`)
- **Returns**: closing wrapper markup
- **Use case**: pair with custom `before` wrapper output.

## Cross-link and pagination filters

### `wppedia_crosslink_ignored_tags`

- **Where**: cross-link parser in `core/classes/modules/class-cross-link-content.php`
- **Input**: default tags excluded from cross-link processing
- **Returns**: final ignored-tags array
- **Use case**: add tags where auto-linking should never run.

### `wppedia_crosslink_ignored_classes`

- **Where**: cross-link parser in `core/classes/modules/class-cross-link-content.php`
- **Input**: default ignored class list (empty by default)
- **Returns**: ignored classes array
- **Use case**: disable linking inside specific styled components.

### `wppedia_crosslink_ignored_ids`

- **Where**: cross-link parser in `core/classes/modules/class-cross-link-content.php`
- **Input**: default ignored id list (empty by default)
- **Returns**: ignored IDs array
- **Use case**: exclude critical sections from auto-link rewriting.

### `wppedia_posts_pagination_arguments`

- **Where**: pagination arg construction in `template-hooks/loop.php`
- **Input**: pagination argument array passed to pagination renderer
- **Returns**: modified pagination args
- **Use case**: alter prev/next labels, mid-size, end-size, etc.

## Example: centralize filters in one extension class

```php
add_filter('wppedia_show_page_title', '__return_false');

add_filter('wppedia_searchform_attrs__class', function ($class) {
    return $class . ' form form--glossary';
});
```
