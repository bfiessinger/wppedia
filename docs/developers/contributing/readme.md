# Contribution Guide (WPPedia Core and Extensions)

This page explains how to contribute safely and predictably.

## 1) Local environment

Install dependencies:

```bash
composer install
npm install
```

Build assets:

```bash
npm run build
```

Use watch mode during active frontend work:

```bash
npm run dev
```

## 2) Branching and scope

- Create focused branches (one concern per branch).
- Keep PRs small enough for detailed review.
- Separate refactoring from behavior changes where possible.

## 3) Coding expectations

- Follow project naming/style conventions.
- Prefer extension points over hard-coded behavior.
- Preserve backward compatibility of hooks/options where possible.
- Add inline docs for new hooks and non-obvious logic.

## 4) Documentation expectations

When behavior changes are user-visible or extension-relevant:

- update user docs under `docs/users/`,
- update developer docs under `docs/developers/`,
- update root docs index if navigation changes.

## 5) Validation before PR

Minimum:

1. Run test suite (`vendor/bin/phpunit`).
2. Rebuild assets if `source/` changed.
3. Manually validate primary glossary flows.
4. Confirm docs are updated.

## 6) PR writing tips

A strong PR should include:

- problem statement,
- implementation summary,
- extension/backward-compatibility notes,
- test evidence,
- follow-up items (if any).
