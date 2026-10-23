---
name: image-generation
description: Plan or create raster visuals for this Eloquage Laravel package's GitHub README, documentation, examples, or release materials when a visual asset materially helps explain the package.
metadata:
  short-description: Purposeful project image generation
---

# Image generation

Use for visual assets associated with `eloquage/filament-horizon` (This is my package filament-horizon), such as a README banner, concept illustration, or workflow diagram rendered as an image.

- First decide whether a visual helps. Prefer Markdown, Mermaid, or editable SVG for diagrams, logos, and interface-like graphics; use raster generation for illustrative or photographic assets.
- Keep the image tied to the package's real purpose. Do not invent features, UI screens, logos, endorsements, or brand elements. Use user-provided brand assets when available.
- If an image-generation tool is available, create the asset from a clear prompt describing subject, composition, style, dimensions/aspect ratio, and intended placement. Preserve transparency when requested.
- Avoid embedded text unless it is short and exact; generated lettering is unreliable. Add labels in Markdown or SVG instead.
- Save generated assets under an appropriate repository path (for example `docs/images/`) and use a relative link in documentation. Do not overwrite an existing asset without checking its use and getting a distinct output path when appropriate.
- After generation, inspect the image at its intended display size, check crop/contrast/legibility, and ensure the README or docs reference the committed path. Never describe a visual as generated or verified if no generation/inspection occurred.
