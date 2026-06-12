# component-portrait-grid

BMA Portrait Grid — 3-column portrait image tiles with bottom-left titles and a full color overlay hover state.

## What it is

A parent/child WPBakery container pair:

- `[bma_portrait_grid overlay_color="#00338f"]` — parent grid wrapper. Emits the shared `bma-auto-grid` markup.
- `[bma_portrait_grid_item image="123" title="Casino Tours" link="url:%2Ftours%2Fcasino%2F"]` — a single portrait tile.

The component also registers typo/hyphen aliases for manual shortcode usage: `[bma_protrait_grid]`, `[bma-portrait-grid]`, and `[bma-protrait-grid]` plus matching `_item` / `-item` child aliases. WPBakery uses the canonical underscore names.

Default visual behavior is based on `davidtours/layouts/sections/portrait-grid.svg`:

- default tile: image, dark wash, bottom gradient, white title at bottom-left
- hover/focus tile: full `#00338f` color overlay at 86.2% opacity
- title position: bottom 35px, left 32px
- title hover animation: `margin-block-end` from `0` to `3rem`
- card radius: 5px
- card ratio: 368 / 467

## Shortcodes registered

- `bma_portrait_grid` (parent container)
- `bma_portrait_grid_item` (child tile)

Global function wrappers:

- `bma_portrait_grid_shortcode()`
- `bma_portrait_grid_item_shortcode()`

## WPBakery elements

- "Portrait Grid" (parent, Custom Elements, searchable by "BMA")
- "Portrait Tile" (child, Custom Elements, searchable by "BMA")

## CSS

Consumers import:

```css
@import "~/vendor/balefireict/component-portrait-grid/src/style.css";
```

The parent grid's `bma-auto-grid` / `auto-grid-*` classes are styled by `component-auto-grid`.

## Source attribution

Reference mockup: `/Users/nusser/Herd/davidtours/layouts/sections/portrait-grid.svg`.
Color reference: David Tours blue `#00338f`.
