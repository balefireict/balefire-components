# component-cta-row

Full-bleed background CTA section: heading + WYSIWYG copy on the left, a big
red CTA button with the business phone number under it on the right. Built
from davidtours `layouts/sections/bg-cta.svg`.

Shortcode: `[bma_cta_row]`

## Bring your own background image

The component does NOT set the background image. Drop the element in a
WPBakery **stretch row** and hardcode the image in theme CSS:

```css
.bma-cta-row {
    background-image: url(../img/cta-row.webp);
}
```

(or target the Extra Class att for per-instance images). Cover
sizing/positioning is pre-set; the component owns the dark gradient overlay
(`::before`, upper-right black -> lower-left #454545) so the image always
reads dark and the white text stays legible.

## Layout

- Large (>= 900px): flex row, `space-between`, vertically centered, text left.
- Small: stacked columns, everything centered.
- Button: 261x72 per the layout SVG, #c8102e, 3px radius, bold ~23px.
- Phone: ~31px medium, 0.04em tracking, centered under the button, tel: link.

## Phone resolution

`phone_source` picks the source explicitly (WPBakery dropdown — Manual /
ACF Options Field):

- `manual` (default): renders the `phone` att as typed.
- `field`: reads the ACF options-page field named by `phone_field`
  (default `vmg_phone`) via `get_field( $field, 'option' )` — the
  site-wide-business-value exception to the no-ACF rule (same reasoning
  as component-footer). Safe no-op (no phone rendered) where ACF is
  absent.

In the editor the Phone Number / Options Phone Field inputs show or hide
based on the chosen source (vc dependency).

## Attributes

| Att | Default | Notes |
|---|---|---|
| `heading` | `''` | H2. |
| `content` (body) | `''` | WYSIWYG body copy under the heading. |
| `cta_label` | `''` | Button text. Button renders only when label + URL set. |
| `cta_url` | `''` | Button href. |
| `cta_target` | `''` | `_blank` for new tab (adds rel noopener). |
| `phone_source` | `manual` | `manual` or `field`. |
| `phone` | `''` | Phone number (manual source). |
| `phone_field` | `vmg_phone` | ACF options field name (field source). |
| `id` | `''` | Element ID. |
| `class` | `''` | Extra class on the section — bg-image hook. |

## WPBakery element

Mapped under **Custom Elements → "CTA Row"** (`vc_before_init`).
Backend-editor preview via component-bakery-preview (soft dep, vc_after_init):
shows heading + body excerpt.
