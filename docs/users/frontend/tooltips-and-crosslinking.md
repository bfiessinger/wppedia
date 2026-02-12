# Tooltips and Cross-Linking

## 1) What this feature does

Tooltip/cross-link functionality helps users understand terms inline without losing reading context.

Depending on configuration, matching glossary terms in content may:

- become links to glossary entries,
- show tooltip definitions on hover/focus.

## 2) Good usage patterns

- Keep definitions concise for tooltip readability.
- Avoid over-linking every repeated term occurrence.
- Test accessibility and mobile behavior.

## 3) QA checklist

- Verify terms are linked where expected.
- Verify no false-positive linking in unrelated words.
- Verify tooltip content is readable and not truncated unexpectedly.
- Verify behavior with cache/CDN enabled.

> Screenshot placeholder: Inline tooltip shown over linked glossary term.
