# component-fancy-hover-grid

BMA Fancy Hover Grid — 3-column portrait image tiles with bottom-left titles and a full color overlay hover state.

## What it is

A parent/child WPBakery container pair:

- `[bma_fancy_hover_grid overlay_color="#00338f"]` — parent grid wrapper. Emits the shared `bma-auto-grid` markup.
- `[bma_fancy_hover_grid_item image="123" title="Casino Tours" text="Optional hover line" link="url:%2Ftours%2Fcasino%2F"]` — a single portrait tile.

## Content sources

The parent grid supports three sources via the `source` att (WPBakery dropdown):

- `source="manual"` (default) — child Fancy Hover Tile elements inside the container.
- `source="posts"` — automatic post loop. Params: `post_type` (public post types dropdown), `orderby` (`date_desc`/`date_asc`/`menu_order`/`title`/`rand`), `max_posts` (default 3). Tiles use featured image, post title, excerpt, permalink.
- `source="acf"` — ACF Relationship (or Post Object) field on the **current** post/page. Params: `acf_field` (field name, required), `max_posts` (0 = no limit), plus optional `acf_image_field` / `acf_title_field` / `acf_text_field` — field names read **on each related post**; blanks fall back to featured image / post title / excerpt.

Sourced tile data is filterable: `apply_filters( 'bma_fancy_hover_grid_post_tile', $data, $post_id, $atts )` where `$data = {image, title, text, url}`.

## Aspect ratio

`aspect` att on the parent (WPBakery dropdown, mirrors bma-image): `portrait` (368:467, default), `square`, `3-4`, `4-3`, `16-9`, `21-9`, `auto`. Emits a `bma-fancy-hover-grid--aspect-*` modifier that sets `--bma-fancy-hover-grid-aspect` on the tiles.

## Tile text

Tiles accept an optional `text` line (manual att / WPBakery textarea, or excerpt/ACF text for sourced tiles). It fades in below the title on hover/focus.

The component also registers typo/hyphen aliases for manual shortcode usage: `[bma_protrait_grid]`, `[bma-fancy-hover-grid]`, and `[bma-protrait-grid]` plus matching `_item` / `-item` child aliases. WPBakery uses the canonical underscore names.

Default visual behavior is based on `davidtours/layouts/sections/fancy-hover-grid.svg`:

- default tile: image, dark wash, bottom gradient, white title at bottom-left
- hover/focus tile: full `#00338f` color overlay at 86.2% opacity
- title position: bottom 35px, left 32px
- title hover animation: `margin-block-end` from `0` to `3rem`
- card radius: 5px
- card ratio: 368 / 467

## Shortcodes registered

- `bma_fancy_hover_grid` (parent container)
- `bma_fancy_hover_grid_item` (child tile)

Global function wrappers:

- `bma_fancy_hover_grid_shortcode()`
- `bma_fancy_hover_grid_item_shortcode()`

## WPBakery elements

- "Fancy Hover Grid" (parent, Custom Elements, searchable by "BMA")
- "Fancy Hover Tile" (child, Custom Elements, searchable by "BMA")

## CSS

Consumers import:

```css
@import "~/vendor/balefireict/component-fancy-hover-grid/src/style.css";
```

The parent grid's `bma-auto-grid` / `auto-grid-*` classes are styled by `component-auto-grid`.

## Source attribution

Reference mockup: `/Users/nusser/Herd/davidtours/layouts/sections/fancy-hover-grid.svg`.
Color reference: David Tours blue `#00338f`.
