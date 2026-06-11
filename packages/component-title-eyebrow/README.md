# component-title-eyebrow

Section heading with a small uppercase "eyebrow" label above it. Single-element,
attribute-driven shortcode.

## Shortcodes registered

- `bma_title_eyebrow` — renders the eyebrow + H2 heading block.

Attributes:

- `eyebrow` — small uppercase text above the title.
- `title` — the main heading (H2).
- `align` — `left` | `center` | `right` (default `center`).
- `color` — `default` (inherits) | `white`.

## WPBakery / Visual Composer

Maps one VC element under the **BMA Elements** category: "BMA Title + Eyebrow"
(`vcMap` hooked on `vc_before_init` when `vc_map()` exists).

## Soft dependencies

None. The component is self-contained.

## CSS

This package owns its CSS. Consumers should import:

    ~/vendor/balefireict/component-title-eyebrow/src/style.css

The stylesheet reproduces the rockerbox visual result in vanilla CSS (no
Tailwind runtime, no design-token variables required).

## Source attribution

Ported from the rockerbox theme: `inc/shortcodes/bma-title-eyebrow.php`
(styles mined from `resources/css/sections.css`, "Preheader & Title" block).
