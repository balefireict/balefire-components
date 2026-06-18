# component-prefooter-flex-row

Full-bleed background CTA section: heading + WYSIWYG copy on the left, a big
red CTA button with the business phone number under it on the right. Built
from davidtours `layouts/sections/bg-cta.svg`.

Shortcode: `[bma_prefooter_flex_row]`

This is shortcode-only on purpose. It does not register a WPBakery element.
Drop `[bma_prefooter_flex_row]` into a text/shortcode block inside a Bakery row and manage
its content from the consumer site's General Business ACF options page.

## Bring your own background image

The component does NOT set the background image. Put the shortcode in a
WPBakery **stretch row** and hardcode the image in theme CSS:

```css
.bma-prefooter-flex-row {
    background-image: url(../img/prefooter-flex-row.webp);
}
```

(or pass the `class` att and target that for per-instance images). Cover
sizing/positioning is pre-set; the component owns the dark gradient overlay
(`::before`, upper-right black -> lower-left #454545) so the image always
reads dark and the white text stays legible.

## Default ACF options fields

The default field names use the generic `acffg_` ACF field-group prefix:

| Content | Default field |
|---|---|
| Heading | `acffg_prefooter_flex_row_heading` |
| Body | `acffg_prefooter_flex_row_body` |
| Button label | `acffg_prefooter_flex_row_button_label` |
| Button URL | `acffg_prefooter_flex_row_button_url` |
| Button new-tab flag | `acffg_prefooter_flex_row_button_new_tab` |
| Phone | `acffg_phone` |

ACF is a soft dependency. If ACF is unavailable and no manual attrs/content are
provided, the shortcode renders nothing.

## Attributes

Manual attrs override ACF values. Field-name attrs let another consumer point
the shortcode at different option fields without forking the package.

| Att | Default | Notes |
|---|---|---|
| `heading` | `''` | Manual H2 override. |
| `body` / enclosed content | `''` | Manual body override. Enclosed content wins over `body`. |
| `cta_label` | `''` | Manual button text. Button renders only when label + URL set. |
| `cta_url` | `''` | Manual button href. Relative URLs are fine. |
| `cta_target` | `''` | `_blank` for new tab, otherwise same window. |
| `phone` | `''` | Manual phone number override. |
| `heading_field` | `acffg_prefooter_flex_row_heading` | ACF options field name. |
| `body_field` | `acffg_prefooter_flex_row_body` | ACF options field name. |
| `cta_label_field` | `acffg_prefooter_flex_row_button_label` | ACF options field name. |
| `cta_url_field` | `acffg_prefooter_flex_row_button_url` | ACF options field name. |
| `cta_new_tab_field` | `acffg_prefooter_flex_row_button_new_tab` | ACF options field name. |
| `phone_field` | `acffg_phone` | ACF options field name. |
| `id` | `''` | Element ID. |
| `class` | `''` | Extra class on the section — bg-image hook. |
