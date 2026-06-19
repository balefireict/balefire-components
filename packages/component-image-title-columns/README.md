# component-image-title-columns

BMA Image Title Columns — a responsive flex grid of image tiles, each with a
centered title beneath the image. Built for museum / destination / fleet-style
galleries where photos are the dominant element.

Derived from a David Tours SVG reference (landscape image rectangles with a
title beneath each).

## Shortcodes

- `[bma_image_title_columns]` — parent grid container.
- `[bma_image_title_columns_item image="" title=""]` — one tile.

```html
[bma_image_title_columns]
  [bma_image_title_columns_item image="42" title="The Franklin Institute"]
  [bma_image_title_columns_item image="43" title="Mutter Museum"]
[/bma_image_title_columns]
```

## Layout behavior

- **Responsive cols:** 1 column on mobile (<768px), 3 columns at 768–1279px,
  4 columns at >=1280px.
- **Centered last row:** the grid is `display:flex; flex-wrap:wrap; justify-content:center`,
  so a partial final row (e.g. 2 items left in a 4-col grid) is centered rather
  than left-aligned.
- **Even columns:** `align-items:flex-start` top-aligns tiles, so tiles whose
  titles wrap to a second line still keep their image tops on the same line.

This component owns its own responsive grid CSS (bespoke 768/1280 breakpoints
that don't match `component-auto-grid`'s fixed tiers) — it does not depend on
`component-auto-grid`.

## Attributes

### Parent `[bma_image_title_columns]`
- `class` — extra CSS class on the grid wrapper.

### Child `[bma_image_title_columns_item]`
- `image` — attachment ID (recommended, renders a responsive `<img>`) or URL.
- `title` — heading beneath the image.
- `class` — extra CSS class on the tile.

## Notes

- Bare output (no `<section>`/container wrapper) — `vc_row` owns background +
  padding per the project convention.
- No theme color/space tokens are referenced (`color: inherit`, explicit values).
- Soft-depends on `component-bakery-preview` for backend editor previews.
