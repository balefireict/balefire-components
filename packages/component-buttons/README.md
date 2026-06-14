# balefireict/component-buttons

BMA Buttons shortcode — renders a repeater of buttons with alignment,
per-button style/size/arrow/text-color, optional icons (calendar, phone,
or custom upload), and "open in new tab" auto-detection for external links.

## Provides

- `[bma_buttons align="center|left|right" buttons="..."]`
- Global function `bma_buttons_render( array $atts )` for programmatic use
- WPBakery `vc_map` registration with `param_group` repeater

### Attributes

- `align` — `center` (default), `left`, `right`
- `buttons` — WPBakery `param_group` repeater. Each row supports:
  - `label` — button text (required)
  - `url` — link URL (required)
  - `style` — `primary` (default), `secondary`, `white`, `black`, `transparent`
  - `size` — `""` (default) or `sm`
  - `arrow` — `true` to append → after label
  - `text_color` — for `transparent` style: `default` or `white`
  - `icon` — `""` (none), `calendar`, `phone`, `custom`
  - `icon_custom` — attachment ID (when `icon` is `custom`)

External links (different host than `home_url()`) automatically get
`target="_blank" rel="noopener noreferrer"`. Fragment-only, scheme-relative,
and non-HTTP-scheme links (mailto, tel, etc.) stay in-tab.

## Icons

Built-in SVG icons render inline before the label text:
- **Calendar** — from David Tours layout-buttons.svg (calendar with checkmark)
- **Phone** — from David Tours layout-buttons-alt.svg (device outline)
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
