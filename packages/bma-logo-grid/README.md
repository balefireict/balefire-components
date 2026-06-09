# balefire/bma-logo-grid

BMA Logo Grid — parent/child WPBakery container of partner/client logos.
Parent renders the grid wrapper with a `data-cols` attribute; child renders
one logo per attachment ID.

## Provides

- `[bma_logo_grid columns="1..6"]…[/bma_logo_grid]`
- `[bma_logo_grid_item image=""]` — single logo (attachment ID)
- Global functions: `bma_logo_grid_render()`, `bma_logo_grid_item_render()`
- WPBakery `vc_map` for both parent (container) and child

## How columns work

The wrapper has `data-cols="N"` and the CSS reads it via the
`--bma-cols` custom property. No dynamic class needed in markup. The
`grid-template-columns: repeat(var(--bma-cols), minmax(0, 1fr))` rule
sets the actual columns. Mobile (<768px) forces 2 columns unless the
grid is 1-col.

## Source of truth

`Balefire\Components\LogoGrid\LogoGrid` (PSR-4). Exposes:
- `LogoGrid::render( $atts, $content )` — parent
- `LogoGrid::renderItem( $atts )` — child
- `LogoGrid::register()` — add_shortcode for both
- `LogoGrid::vcMap()` — vc_map for both
- `LogoGrid::registerContainerClass()` — eval'd `WPBakeryShortCode_BMA_LogoGrid extends WPBakeryShortCodesContainer {}` (called on `vc_after_init`)

## CSS

`assets/logo-grid.css` — uses `data-cols` attribute selector + CSS custom
property so the markup stays static. The previous Tailwind implementation
emitted `md:auto-grid-cols-N` modifier classes per render; this is cleaner.

## Dependencies

None.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-logo-grid", "options": { "symlink": true } }
    ],
    "require": {
        "balefire/bma-logo-grid": "*"
    }
}
```

```css
@import "../../vendor/balefire/bma-logo-grid/assets/logo-grid.css";
```

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-logo-grid.php`
— same shortcodes, same attributes. Replaced the `auto-grid-cols-N` Tailwind
modifier with a `data-cols` attribute + CSS custom property (cleaner, no
utility classes, no JS-required class swap on resize).
