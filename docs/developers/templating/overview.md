# Templating in WPPedia (Key Extension Surface)

Templating is one of the most powerful extension points in WPPedia and is usually the first place to customize output for real projects.

## 1) How template resolution works

WPPedia template loading follows a WordPress-style fallback model:

1. Look in your (child) theme under the configured WPPedia template path.
2. If not found, use plugin defaults from `templates/`.
3. Allow final path rewrites via template filters.

By default, the theme path is `yourtheme/wppedia/...`, and this can be changed through `wppedia_template_path`.

## 2) The two mechanisms you should combine

### Template overrides

Use when changing HTML structure or introducing design-system wrappers.

### Template action hooks

Use when inserting/reordering sections without copying entire templates.

In many cases, hooks should be tried first because they reduce upgrade maintenance.

## 3) Core template groups

- `templates/archive.php` – archive rendering and loop orchestration.
- `templates/content-single.php` – single glossary entry rendering.
- `templates/content-archive.php` – loop item rendering.
- `templates/global/*` – wrappers/sidebar pieces.
- `templates/loop/*` – loop subparts (title, image, excerpt, wrappers).
- `templates/search/form.php` – glossary search form.
- `templates/nav/char-navigation.php` – initial-character navigation.

## 4) Upgrade-safe templating principles

- Keep custom overrides minimal and purpose-specific.
- Track overridden files in a dedicated changelog.
- On WPPedia updates, diff your overrides against new plugin templates.
- Prefer hook-based insertions over full file copies where possible.
