# balefireict/component-buttons

BMA Buttons shortcode — renders a repeater of buttons with alignment,
per-button style/size/arrow/text-color, optional icons (calendar or custom
upload), a Phone link type, and "open in new tab" auto-detection for
external links.

## Provides

- `[bma_buttons align="center|left|right" buttons="..."]`
- Global function `bma_buttons_render( array $atts )` for programmatic use
- WPBakery `vc_map` registration with `param_group` repeater

### Attributes

- `type` — `default` (a normal button, the default) or `phone` (a phone-icon +
  number tel: link). Selecting Phone hides the button fields below.
- `align` — `center` (default), `left`, `right` (parent `[bma_buttons]`)
- `buttons` — WPBakery `param_group` repeater. Each row supports:
  - `label` — button text (required, Default type)
  - `url` — link URL (required, Default type)
  - `style` — `primary` (default), `secondary`, `white`, `black`, `transparent`
  - `size` — `"md"` (default) or `sm`
  - `arrow` — `true` to append → after label
  - `text_color` — for `transparent` style: `default` or `white`
  - `icon` — `""` (none), `calendar`, `custom`
  - `icon_custom` — attachment ID (when `icon` is `custom`)

External links (different host than `home_url()`) automatically get
`target="_blank" rel="noopener noreferrer"`. Fragment-only, scheme-relative,
and non-HTTP-scheme links (mailto, tel, etc.) stay in-tab.

## Phone type (`type="phone"`)

Renders `<a class="bma-btn-phone" href="tel:+1-…">` containing the phone-icon
SVG and the phone number. The number resolves from, in order:

1. the manual `phone` shortcode att (per-element override), then
2. the ACF options field named by `phone_field` (default `acffg_phone`).

When no number is available the button renders nothing (no dead anchor).
`tel:` is normalized to digits with a `+1` country code (10-digit numbers are
prefixed; 11-digit numbers starting with `1` are kept).

The link inherits its color from context (`currentColor`), so the icon and
text always match. Consumer themes color `.bma-btn-phone` for their
secondary/brand color — e.g. `color: var(--vmg-blue-dark);` (#00186c) on
davidtours/vmg. The `.btn-icon` rules above handle icon sizing.

`renderIcon('phone')` is retained for backward compatibility with existing
`[bma_button icon="phone"]` content; the icon picker no longer offers Phone
(use the Type dropdown instead).

## Icons

Built-in SVG icons render inline before the label text:
- **Calendar** — from David Tours layout-buttons.svg (calendar with checkmark)
- **Custom** — uploads any image/SVG from the media library

Icons inherit `currentColor` for fill, matching the button text color.

## CSS

`src/style.css` ships the `.bma-buttons` flex wrapper + `.btn-icon` inline
layout. The `.btn` base styles, modifiers, and gradients come from the
consumer theme's own button CSS.

## Source of truth

`Balefire\Component\Buttons\Buttons` (PSR-4). Exposes:
- `Buttons::render( array $atts ): string` — shortcode callback
- `Buttons::renderButton( array $row ): string` — single-button render (reusable)
- `Buttons::renderIcon( string $icon, string $icon_custom ): string` — inline SVG icon
- `Buttons::parseButtons( string|array $raw ): array` — param_group parser
- `Buttons::isExternalUrl( string $url ): bool` — external link detector
- `Buttons::register(): void` — add_shortcode
- `Buttons::vcMap(): void` — vc_map (registered via bootstrap on vc_before_init)

## Dependencies

None. Uses WordPress `home_url()`, `esc_url()`, `wp_get_attachment_image()`.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/component-buttons", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-buttons": "*"
    }
}
```

```css
/* consumer theme resources/css/app.css */
@import "../../vendor/balefireict/component-buttons/src/style.css";
```

The bootstrap is auto-loaded by Composer. No `require` calls needed in
the theme's `functions.php`.
