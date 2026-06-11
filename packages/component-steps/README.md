# balefireict/component-steps

Numbered steps grid for WPBakery: a parent container of step cards, each with an
icon, title and body. Ported from the rockerbox balefire theme.

## Shortcodes registered

- `[bma_steps columns="3"]` — parent grid container (auto-grid). `columns` is
  one of `1`, `2`, `3` (default), `4` and drives the `lg:auto-grid-cols-N` class.
- `[bma_step icon="123" title="..."]Body[/bma_step]` — one step card. `icon` is
  a numeric attachment id, or omit it and supply an inline SVG via
  `[bma_step_icon]`. Body copy is the enclosed (rich text) content.
- `[bma_step_icon]<svg>…</svg>[/bma_step_icon]` — inline SVG passthrough used
  inside a `[bma_step]` body.

## WPBakery (Visual Composer) elements

- **BMA Steps** (`bma_steps`) — container element, category "BMA Cards".
  `as_parent` = `bma_step`, registers `WPBakeryShortCode_BMA_Steps`
  (a `WPBakeryShortCodesContainer` subclass) on `vc_after_init`.
- **BMA Step** (`bma_step`) — child element, `as_child` = `bma_steps`.

## Soft dependencies (optional, guarded with `function_exists`)

- `bma_safe_svg()` — sanitises inline SVG (component-arrow / theme).
- `bma_inline_svg_attachment()` — inlines an SVG attachment by id.
- `bma_render_image_or_svg()` — renders a non-SVG image attachment.

None of these are composer-required; if absent the icon simply renders empty.

## CSS

This package owns the `.simple-steps-card` styles. Consumers must import:

```
~/vendor/balefireict/component-steps/src/style.css
```

The grid container classes (`.bma-auto-grid`, `.auto-grid-cols-N`,
`.auto-grid-gap-6`) are emitted in the markup but their CSS is owned by
`component-auto-grid`, not this package.

## Source attribution

Ported from rockerbox theme `inc/shortcodes/bma-steps.php` (card CSS from
`resources/css/sections.css`, "Simple Steps Card" section).
