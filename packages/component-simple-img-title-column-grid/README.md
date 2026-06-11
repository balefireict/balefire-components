# component-simple-img-title-column-grid

BMA Simple Image Title Column Grid — image tiles with bottom titles and a full color overlay hover state.

## What it is

A parent/child WPBakery container pair:

- `[bma_simple_img_title_column_grid columns="3" overlay_color="#84081c"]` — parent grid wrapper. Emits the shared `bma-auto-grid` markup.
- `[bma_simple_img_title_column_grid_item image="123" title="Hop-on Casino Bus" url="/casino-bus/"]` — a single image tile.

Default visual behavior is based on `davidtours/layouts/sections/simple-img-title-column-grid.svg`:

- default tile: image, dark bottom gradient, white 24px title near the bottom
- hover/focus tile: full `#84081c` overlay at 70% opacity, centered 30px title
- card radius: 5px
- card height: 235px

## Shortcodes registered

- `bma_simple_img_title_column_grid` (parent container)
- `bma_simple_img_title_column_grid_item` (child tile)

Global function wrappers:

- `bma_simple_img_title_column_grid_shortcode()`
- `bma_simple_img_title_column_grid_item_shortcode()`

## WPBakery elements

- "Image Over Grid" (parent, Custom Elements, searchable by "BMA")
- "Image Title Tile" (child, Custom Elements, searchable by "BMA")

## CSS

Consumers import:

```css
@import "~/vendor/balefireict/component-simple-img-title-column-grid/src/style.css";
```

The parent grid's `bma-auto-grid` / `auto-grid-*` classes are styled by `component-auto-grid`.

## Source attribution

Reference mockup: `/Users/admin/Herd/davidtours/layouts/sections/simple-img-title-column-grid.svg`.
Color reference: David Tours deep red `#84081c` (`--vmg-red-dark` / `--color-accent-hover`).
