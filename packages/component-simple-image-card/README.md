# component-simple-image-card

BMA Simple Image Card — an image-top card grid for WPBakery Page Builder.

## What it is

A parent/child container pair:

- `[bma_simple_image_card_grid columns="3"]` — parent grid wrapper. Emits the
  shared `bma-auto-grid` markup (1/2/3/4 desktop columns).
- `[bma_simple_image_card image="123" title="…"]Optional body[/bma_simple_image_card]`
  — a single card (image on top, title, optional body copy).

## Shortcodes registered

- `bma_simple_image_card_grid` (parent container)
- `bma_simple_image_card` (child card)

Global function wrappers `bma_simple_image_card_grid_shortcode()` and
`bma_simple_image_card_shortcode()` are kept (original rockerbox names) for
backward compatibility.

## WPBakery elements

- "BMA Image Card Grid" (parent, `as_parent` → `bma_simple_image_card`,
  container, `js_view` VcColumnView). Backed by
  `WPBakeryShortCode_BMA_SimpleImageCard extends WPBakeryShortCodesContainer`,
  registered on `vc_after_init`.
- "BMA Image Card" (child, `as_child` → `bma_simple_image_card_grid`).

## Soft dependencies

None. Uses only WordPress core functions
(`wp_get_attachment_image_url`, `get_post_meta`, escaping helpers).

## CSS

This package owns its card CSS. Consumers import:

```
~/vendor/balefireict/component-simple-image-card/src/style.css
```

The parent's `bma-auto-grid` / `auto-grid-*` classes are styled by the separate
`component-auto-grid` package — not here.

## Source attribution

Ported from rockerbox balefire theme:
`inc/shortcodes/bma-simple-image-card.php` (markup) and
`resources/css/sections.css` (`.bma-simple-image-card` rules).
