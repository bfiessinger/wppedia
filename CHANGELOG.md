# Changelog

## 1.3.0
### New
- the static frontpage now acts like a glossary archive with additional content
- added a page title to the glossary archive
- added a page title to glossary taxonomies
- added a content section to glossary taxonomies 

### Bug fixes
- fixed a bug where automated crosslinking was not working if the link phrase was paranthesed

### Enhancements
- implemented php-scoper for composer dependencies to avoid namespace issues

### Code Quality
- updated comments

## 1.2.3
### Bug fixes
- fixed alphabetical index redirect does not respect the permalink base (since 1.2.2)

### Enhancements
- more robust regex for alphabetical index rewrite rules

## 1.2.2
### Bug fixes
- fixed alphabetical index redirect for non paged archives

## 1.2.1
### Bug fixes
- fixed a redirection issue when `use initial character in URL` is not active

### Enhancements
- enhanced UX on admin pages
- automatically update version constant
- added trailing slashes to the post URL's

## 1.2.0
### New
- option `use initial character in URL` is now available in the free version
- new taxonomy for glossary categorization

### Bug fixes
- fixed some styling issues
- fixed an issue where edit scripts where not enqueued for new posts
- fixed a template issue where 2 loop wrapper elements exists

### Enhancements
- better theme compatibility with default WordPress themes
- stabilized templates
- added more editable template parts

### Breaking ⚠️
- switch classNames to camel case
- rename plugin constants
- rename plugin namespaces

## 1.1.6
### Bug fixes
- fixed permalink base setting had no effect on the actual permalinks
- fixed could not dismiss admin notices

### Enhancements
- stabilized query functions

## 1.1.5
### Bug fixes
- fixed a bug where fresh glossary terms would often result in 404 Errors and needed manual saving of permalinks

### Enhancements
- enhanced default styles

### Code Quality
- removed unused dependencies

## 1.1.4
### Enhancements
- fixed minor styling issues

## 1.1.3
### New Features
- added new filter "wppedia_crosslink_ignored_tags" where users might add or remove specific HTML tags in which no automatic crosslinks should be created
- added new option to enable / disable the tooltip feature
- added new option to select tooltip styles
- new tooltip style "light-with-border"
- new tooltip style "material"
- new tooltip style "translucent"
- added loop section identifiers
- added featured image in archive and singular page layouts

### Bug fixes
- fixed a bug where rewrite rules were not flushed when updating the glossary frontpage or the permalink base setting
- fixed loop pagination priority

### Enhancements
- changed behaviour of filter wppedia_template_path: the returned template path must not include a trailing slash anymore
- excluded wppedia-search script on pages where no searchbar is active
- removed the excerpt wrapper in post loop if a post has no excerpt content
- optimized default styles to fix some issues in several themes

### Code Quality
- improved documentation in the comments

## 1.1.2
### Bug fixes
- fixed a bug where WPPedia Templates were used on other post types

### Enhancements
- **We are glad to announce that WPPedia now allows 500 glossary articles in the free plan! 🎉🎉🎉**

## 1.1.1
### Bug fixes
- fixed posts per page was unable to change on glossary archives
- fixed orderby was set wrong in some situations

## 1.1.0
### New Features
- allow up to 3 seperate alternative post terms (in addition to the post's title) that can be used for crosslinking and searching
- added plugin version upgrade mechanism for deprecations

### Enhancements
- added a plugin action link to the edit screen of WPPedia
- added a plugin action link to the settings screen of WPPedia
- added a helper message to notify users if their glossary frontpage slug does not match the glossaries permalink base setting
- enhanced accessability for tooltips
- enabled hash navigation for options page tabs
- enhanced default glossary styling
- restructured options pages

### Bug fixes
- fixed a styling issue with disabled switch buttons in wp-admin
- fixed a bug where crosslinks could not be created case insensitive
- fixed a bug where crosslinks could not be created at the start of the content
- fixed a bug where crosslinks could not be created inside HTML tags
- fixed a bug where conditional page functions would give wrong results in some situations

### Code Quality
- added some missing comments
- applied more consistency in usage of internal methods

### Various
- change default primary color

## 1.0.0
- Initial Release