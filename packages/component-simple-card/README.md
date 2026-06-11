# component-simple-card

BMA Simple Card — a text-only bordered card grid for WPBakery. Ported from the
rockerbox balefire theme (`inc/shortcodes/bma-simple-card.php`).

## What it is

A parent/child container pair: the grid wraps any number of simple cards inside
the shared `bma-auto-grid` responsive wrapper, and each card renders an optional
title plus rich-text body inside a bordered box.

## Shortcodes registered

- `[bma_simple_card_grid columns="3"]…[/bma_simple_card_grid]` — parent grid.
  `columns` accepts `1`, `2`, `3` (default), `4` and maps to
  `lg:auto-grid-cols-N`.
- `[bma_simple_card title="…"]Body copy[/bma_simple_card]` — one card. Title via
  attribute; body is the enclosed (rich text) content.

Original rockerbox global callback names are preserved as function wrappers
(`bma_simple_card_grid_shortcode`, `bma_simple_card_shortcode`).

## WPBakery (Visual Composer) elements

- **BMA Simple Card Grid** — container element (`as_parent` → `bma_simple_card`),
  registered with `php_class_name` `WPBakeryShortCode_BMA_SimpleCard`. The
  `WPBakeryShortCodesContainer` subclass is registered on `vc_after_init`.
- **BMA Simple Card** — child element (`as_child` → `bma_simple_card_grid`).

## Soft dependencies

None. This component has no external `bma_*` helper calls.

The parent emits the `bma-auto-grid` / `auto-grid-cols-*` / `auto-grid-gap-6`
classes, but the grid layout CSS is owned by **component-auto-grid** — this
package does not define it.

## CSS

This package owns its card-internal CSS. Consumers import:

```
~/vendor/balefireict/component-simple-card/src/style.css
```

(Vanilla CSS — no Tailwind tokens. The card radius uses
`var(--radius-card, 0.5rem)` with a standalone fallback.)

## Source attribution

Ported from rockerbox theme: `inc/shortcodes/bma-simple-card.php` and the
`.bma-simple-card` rules in `resources/css/sections.css`.
