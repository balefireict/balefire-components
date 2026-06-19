# component-gallery-grid

BMA Gallery Grid — an ACF-gallery-driven responsive image grid with a
fslightbox lightbox. A straight (non-container) shortcode reads an ACF Gallery
field and renders a CSS-grid of thumbnails; clicking a thumbnail opens the
full-size image in a lightbox.

## Shortcode

```html
[bma_gallery_grid]
```

Defaults to reading the `acffg_gallery_grid` ACF Gallery field from the current
post. All attributes optional:

| Attribute | Default | Description |
|-----------|---------|-------------|
| `field`   | `acffg_gallery_grid` | ACF Gallery field name to read. |
| `columns` | `3`     | Grid column count (1–8). |
| `size`    | `medium` | WordPress thumbnail size for the grid image (`thumbnail`/`medium`/`large`/`full`). |
| `post_id` | _(current)_ | Override the post to read the field from. |
| `class`   | _(none)_ | Extra CSS class on the grid wrapper. |

```html
[bma_gallery_grid field="custom_gallery" columns="4" size="large"]
```

## Asset loading (conditional enqueue)

fslightbox (banthagroup fork, v3.7.5) is bundled at
`src/assets/fslightbox.min.js`. The script is **registered** in the bootstrap
and **enqueued only inside `render()`** — so the ~32 KB JS loads exclusively on
pages where the gallery shortcode is present. fslightbox self-injects its own
CSS inside the JS bundle, so there is no separate `.min.css`.

The script URL is resolved via `content_url()` to
`wp-content/vendor/balefireict/component-gallery-grid/src/assets/fslightbox.min.js`,
which is web-accessible both for symlinked local dev and committed-vendor prod
deploys. It loads in the footer (`in_footer = true`) so fslightbox's DOM scan
of `<a data-fslightbox>` runs after the gallery markup exists.

## ACF field group

Ships `acf-json/group_acffg_gallery_grid.json` — a "Gallery Grid" field group
with one Gallery field named `acffg_gallery_grid` (return format: Image Array),
located on `page` by default. Consumers can extend the location rule. Loads
under the standard `BALEFIRE_COMPONENTS_LOAD_ACF_JSON` gate (true in David
Tours / vmg).

## Disabling the lightbox (small images)

For small source images where a lightbox adds nothing (e.g. 640x480 fleet
photos), turn the lightbox off per-page with the **Disable Lightbox** ACF
toggle (`acffg_gallery_grid_disable_lightbox`). When on:

- Thumbnails render as plain `<figure>` images — no `<a>`, no click-to-zoom.
- The fslightbox script is **not enqueued** (the page ships zero lightbox JS/CSS).
- A `gallery-grid--no-lightbox` modifier class is added (drops the zoom cursor).

Override per-element with the shortcode attribute (takes precedence over the
ACF toggle):

```html
[bma_gallery_grid disable_lightbox="1"]   <!-- force off -->
[bma_gallery_grid disable_lightbox="0"]   <!-- force on, ignoring the ACF toggle -->
```

The component owns its own grid CSS (`src/style.css`), bundled into the
consumer theme's Vite dist like every other CSS-bearing component:

```css
@import "~/vendor/balefireict/component-gallery-grid/src/style.css";
```

Default 3 columns, 10px gap. The column count is a CSS custom property
(`--gallery-grid-cols`) set inline from the `columns` att, so editors can
change it per-instance without touching CSS. Thumbnails render at their natural
aspect ratio; add `aspect-ratio` + `object-fit: cover` to the img rule if you
want uniform square/cropped tiles.

## Notes

- Bare output (no container/padding) — `vc_row` owns background + padding.
- Soft-depends on ACF (`function_exists('get_field')` guard); never
  composer-requires it.
- Each shortcode instance gets a unique `data-fslightbox` group so multiple
  galleries on one page stay independent.
