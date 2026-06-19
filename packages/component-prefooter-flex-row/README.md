# component-prefooter-flex-row

Full-bleed background CTA section: heading + WYSIWYG copy on the left, a big
red CTA button with the business phone number under it on the right. Built
from davidtours `layouts/sections/bg-cta.svg`.

Shortcode: `[bma_prefooter_flex_row]`

This is shortcode-only on purpose. It does not register a WPBakery element.
Drop `[bma_prefooter_flex_row]` into a text/shortcode block inside a Bakery row,
or render it via a theme partial that toggles it on per page (see below).

## Shipped ACF field group

`acf-json/group_acffg_prefooter_flex_row.json` is a per-page field group
located on `post_type == page`. It owns:

| Field | Key | Type | Default |
|---|---|---|---|
| Show Prefooter Flex Row | `acffg_prefooter_flex_row_show` | `true_false` | Off |
| Background Image URL | `acffg_prefooter_flex_row_bg_image` | `url` | (theme default) |
| Heading | `acffg_prefooter_flex_row_heading` | `text` | `Effortless travel` |
| Body | `acffg_prefooter_flex_row_body` | `wysiwyg` | (full reference copy) |
| Button Label | `acffg_prefooter_flex_row_button_label` | `text` | `Request A Quote` |
| Button URL | `acffg_prefooter_flex_row_button_url` | `text` | `/quote-request/` |
| Open Button in New Tab | `acffg_prefooter_flex_row_button_new_tab` | `true_false` | Off |

Everything except the toggle is hidden behind ACF conditional logic until the
toggle is flipped on. Defaults are pre-filled from the David Tours reference
SVG so the section renders correctly the moment an editor enables it; every
field is overridable per page.

Phone is intentionally NOT in this group. It is sourced automatically from
General Business via `acffg_phone` (see Attributes below).

The bootstrap registers this group's acf-json load path, guarded by
`BALEFIRE_COMPONENTS_LOAD_ACF_JSON` (see the monorepo skill). Consumers that
want only their own `acf-json/` should define that constant `false` before the
site autoloader runs.

## Value resolution order

For each content field (heading / body / button label / button URL / new-tab),
the renderer resolves in this order:

1. Manual shortcode attribute (see Attributes below)
2. Per-page ACF field on the current post (the shipped field group)
3. Site-wide ACF options field (e.g. a consumer-side General Business group)

This lets a consumer ship centralized content via options while still allowing
per-page overrides from this package's field group, with no partial changes.

## Bring your own background image

The component does NOT set the background image. Either:

- Pass a `bg_image` shortcode attribute (a full URL) — rendered as an inline
  `style="background-image:url('...')"` on the section. The per-page
  Background Image URL field feeds this through the theme partial.
- Put the shortcode in a WPBakery **stretch row** and hardcode the image in
  theme CSS:

```css
.bma-prefooter-flex-row {
    background-image: url(../img/prefooter-flex-row.webp);
}
```

Cover sizing/positioning is pre-set; the component owns the dark gradient
overlay (`::before`, upper-right black -> lower-left #454545) so the image
always reads dark and the white text stays legible.

## Attributes

Manual attrs override ACF values. Field-name attrs let another consumer point
the shortcode at different field names without forking the package.

| Att | Default | Notes |
|---|---|---|
| `heading` | `''` | Manual H2 override. |
| `body` / enclosed content | `''` | Manual body override. Enclosed content wins over `body`. |
| `cta_label` | `''` | Manual button text. Button renders only when label + URL set. |
| `cta_url` | `''` | Manual button href. Relative URLs are fine. |
| `cta_target` | `''` | `_blank` for new tab, otherwise same window. |
| `phone` | `''` | Manual phone number override. |
| `heading_field` | `acffg_prefooter_flex_row_heading` | ACF field name (per-page + options). |
| `body_field` | `acffg_prefooter_flex_row_body` | ACF field name (per-page + options). |
| `cta_label_field` | `acffg_prefooter_flex_row_button_label` | ACF field name (per-page + options). |
| `cta_url_field` | `acffg_prefooter_flex_row_button_url` | ACF field name (per-page + options). |
| `cta_new_tab_field` | `acffg_prefooter_flex_row_button_new_tab` | ACF field name (per-page + options). |
| `phone_field` | `acffg_phone` | ACF **options** field name (General Business). |
| `bg_image` | `''` | Full URL; emits inline `background-image` style. |
| `id` | `''` | Element ID. |
| `class` | `''` | Extra class on the section. |

ACF is a soft dependency. If ACF is unavailable and no manual attrs/content are
provided, the shortcode renders nothing.
