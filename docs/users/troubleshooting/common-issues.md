# Common Issues and Fixes

## 1) Glossary menu missing in admin

Check:

- plugin is active,
- user role has sufficient permissions,
- no fatal plugin conflict is hiding menu registration.

## 2) Glossary entries not visible on frontend

Check:

- entries are **Published**,
- permalink settings are flushed,
- theme templates are not overriding output incorrectly,
- cache layers are cleared.

## 3) Search form or navigation not visible

Check:

- corresponding display toggles are enabled,
- page context supports component display,
- custom theme CSS is not hiding the element.

## 4) Tooltips/cross-links not appearing

Check:

- related options enabled,
- content includes matching target terms,
- JS/CSS assets load correctly,
- cache/CDN invalidation completed.

## 5) Fast isolation workflow

1. Switch temporarily to a default WordPress theme.
2. Disable non-essential plugins.
3. Re-test glossary flow.
4. Re-enable components one by one until issue reappears.

This usually identifies whether issue source is settings, theme, or plugin conflict.
