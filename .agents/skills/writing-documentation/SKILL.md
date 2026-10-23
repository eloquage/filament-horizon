---
name: writing-documentation
description: Write or edit developer documentation for this Eloquage Laravel package, including README sections, guides, API explanations, examples, and release notes; remove vague or inflated AI-style prose.
metadata:
  short-description: Clear, evidence-based package documentation
---

# Writing and documentation

Write documentation for `eloquage/filament-horizon` (This is my package filament-horizon) that lets the intended reader complete a concrete task.

## Source and structure

- Inspect implementation, public API, tests, Composer metadata, and existing docs before describing behavior.
- State what the package does, prerequisites, steps, expected result, and relevant limitations in the order a user needs them.
- Prefer exact class names, commands, config keys, paths, and small runnable examples. Keep explanations near the example they explain.
- Keep compatibility and behavior claims aligned with `composer.json` and CI. Distinguish verified behavior from planned or unverified behavior.
- Match the repository's established documentation layout and terminology; update cross-links when moving or renaming content.

## Stop-slop edit

Remove empty intensifiers, generic praise, repeated summaries, vague claims, fake certainty, and boilerplate introductions. Replace abstractions with concrete behavior. Avoid phrases such as “seamlessly,” “powerful,” “robust,” “in today's fast-paced world,” “it's important to note,” and “leverage” unless a specific, testable meaning is intended. Do not make prose artificially terse when a caveat or explanation helps the reader.

Use active voice where clear, varied sentence lengths, meaningful headings, and plain language. Preserve technical precision and necessary caveats. Do not fabricate benchmarks, testimonials, security guarantees, or execution results.

## Final check

Read the result as a new package user: can they install it, follow the example, understand what is optional, and find the next relevant section? Check links, commands, names, and Markdown structure against the repository.
