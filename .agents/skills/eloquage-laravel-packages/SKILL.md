---
name: eloquage-laravel-packages
description: Build, maintain, and review Laravel packages in the Eloquage package collection, including service providers, package configuration, commands, events, migrations, publishing, compatibility, tests, and release-facing changes.
metadata:
  short-description: Laravel package development for Eloquage
---

# Laravel package development

Use this skill when changing this Composer package: `eloquage/filament-horizon` — This is my package filament-horizon

## Package context

- Supported PHP constraint from Composer: `^8.1`. Check `composer.json` and CI before changing compatibility claims.
- Follow the package's existing layout and use its `spatie/laravel-package-tools` conventions where already present; do not migrate scaffolding without a task-specific reason.
- This is a distributable library. Keep application-only assumptions out of package code and make optional integrations fail gracefully when absent.
- Preserve public API, configuration keys, published filenames/tags, command names, and documented behavior unless the request explicitly changes them.

## Work approach

1. Inspect `composer.json`, service providers, config, tests, README, CI workflow matrices, and nearby implementation before editing.
2. Check supported Laravel/Illuminate versions against package constraints and CI. For version-sensitive APIs, confirm using current official Laravel documentation or the installed framework sources.
3. Keep dependencies and provider boot work minimal. Guard optional dependencies and avoid doing expensive work during package discovery.
4. Test package behavior through the repository's Testbench/Pest patterns. Add coverage for installation/discovery, configuration/publishing, and supported framework versions when relevant.
5. Prefer the existing commands: `composer test`, `composer analyse`, and `composer format`. Run only what fits the requested change and report precisely what ran.
6. Update README usage and compatibility claims when public behavior changes; derive them from code and CI, not assumptions.

## Review checklist

- Is the change compatible with every declared Illuminate version?
- Are configuration and published resources backward-compatible?
- Can the package be installed in a clean Laravel consumer application?
- Are optional services, environment configuration, and integration points handled without requiring them for unrelated users?
- Do tests assert public package behavior rather than implementation details?
