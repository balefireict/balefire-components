# balefire/bma-buttons

BMA Buttons shortcode — renders 1-2 buttons with style, size, alignment,
optional arrow, and "open in new tab" auto-detection for external links.
First shortcode in the monorepo; sets the pattern for all subsequent
shortcode packages.

## Provides

- `[bma_buttons align="center|left|right" btn1_label="..." btn1_url="..." ...]`
- Global function `bma_buttons_render( array $atts )` for programmatic use
- WPBakery `vc_map` registration under category "BMA Elements"

### Attributes

- `align` — `center` (default), `left`, `right`
- `btn1_label`, `btn1_url` — required for first button to render
- `btn1_style` — `primary` (default), `secondary`, `white`, `black`, `transparent`
- `btn1_size` — `""` (default, medium) or `sm` (small)
- `btn1_arrow` — `true` to append ` →` to the label
- `btn1_text_color` — for `transparent` style: `default` or `white`
- `btn2_*` — same set for the optional second button. Renders only if both
  label and url are provided.

External links (different host than `home_url()`) automatically get
`target="_blank" rel="noopener noreferrer"`. Fragment-only, scheme-relative,
and non-HTTP-scheme links (mailto, tel, etc.) stay in-tab.

## CSS

`assets/buttons.css` ships the `.bma-buttons` flex wrapper. The `.btn` base
styles, modifiers, and gradients are assumed to come from the consumer
theme's own button CSS. Balefire-base has these in
`resources/css/wp-button-overrides.css`.

## Source of truth

`Balefire\Components\Buttons\Buttons` (PSR-4). Exposes:
- `Buttons::render( array $atts ): string` — shortcode callback
- `Buttons::renderButton(...)` — single-button render (reusable)
- `Buttons::isExternalUrl( string $url ): bool` — external link detector
- `Buttons::register(): void` — add_shortcode
- `Buttons::vcMap(): void` — vc_map (registered via bootstrap on vc_before_init)

## Dependencies

None. Uses WordPress `home_url()` and `esc_url()`.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-buttons", "options": { "symlink": true } }
    ],
    "require": {
        "balefire/bma-buttons": "*"
    }
}
```

```css
/* consumer theme resources/css/app.css */
@import "../../vendor/balefire/bma-buttons/assets/buttons.css";
```

The bootstrap is auto-loaded by Composer. No `require` calls needed in
the theme's `functions.php`.

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-buttons.php` —
same shortcode attrs and same render function. Added `vc_map` registration
(rockerbox version was attribute-driven only).
