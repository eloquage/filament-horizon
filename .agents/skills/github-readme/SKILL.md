---
name: github-readme
description: Write, review, or update a GitHub README for this Eloquage Laravel package, with accurate installation, compatibility, usage, configuration, badges, examples, and contribution guidance.
metadata:
  short-description: Accurate package README authoring
---

# GitHub README

For `eloquage/filament-horizon` (This is my package filament-horizon), make the README useful to a developer deciding whether to install the package and then trying it in a real application.

## Ground every claim

- Read `composer.json`, public classes, service provider, config, commands, tests, CI matrix, license, and current README before drafting.
- Verify Composer package name, PHP/Laravel support, install command, provider discovery, publish tags, config keys/defaults, Artisan commands, and code examples against source.
- Use repository-specific GitHub URLs and workflow names for badges. Remove or correct stale branch, workflow, Packagist, and coverage badges.
- Do not claim a feature, compatibility version, security property, benchmark, or test status without direct repository evidence.

## Structure and examples

Choose sections that fit the package. Common sections include purpose, requirements, installation, configuration, usage, testing, and license. Keep the first paragraph concrete and package-specific; avoid generic marketing claims.

Make examples runnable: include necessary imports and context, use actual class/config names, and show expected output only when verified. Keep advanced options near the feature they describe. Avoid duplicating the full API reference in the README.

Keep Markdown accessible and easy to scan. Use headings in order, descriptive link text, fenced code blocks with language labels, and tables only when they improve comparison. Avoid decorative badges that do not help users make a decision.

After edits, compare every command and symbol in the README with the repository. Do not say checks pass unless they were run in this session.
