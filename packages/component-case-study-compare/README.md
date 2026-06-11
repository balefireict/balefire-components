# balefireict/component-case-study-compare

BMA Case Study Compare — a before/after comparison rendered as two
card-icon-break cards with a centered transition arrow between them. Self-closing
WPBakery element (no enclosed content), attribute driven.

## Provides

- `[bma_compare left_icon="" left_title="" left_body="" right_icon="" right_title="" right_body=""]`
- `[bma_compare_cards ...]` — explicit alias; same renderer, same attributes
- Global function: `bma_compare_shortcode()` (original rockerbox name, kept for back-compat)
- WPBakery `vc_map` for both shortcodes ("BMA Case Study Compare" and "BMA Compare Card Break")

Icons accept a numeric attachment id or a full URL. Bodies allow line breaks,
`<br>`, and `<strong>`.

## Source of truth

`Balefire\Component\CaseStudyCompare\CaseStudyCompare` (PSR-4). Exposes:
- `CaseStudyCompare::render( $atts, $content, $shortcode )` — shared renderer
- `CaseStudyCompare::register()` — add_shortcode for both tags
- `CaseStudyCompare::vcMap()` — vc_map for both (called on `vc_before_init`)

The icon-card render helpers (`bma_render_icon_card` / `bma_icon_card_icon_html`)
from rockerbox `inc/shortcodes/bma-icon-card.php` are **inlined** here as private
static methods (the only logic this component needs). When a host theme defines
the matching global functions, those globals are preferred so the theme stays the
source of truth.

## Soft dependencies (all `function_exists`-guarded, none composer-required)

- `bma_safe_svg()` — SVG sanitiser (component-svg-helper). Falls back to raw SVG passthrough.
- `bma_inline_svg_attachment()` — inline an SVG attachment so `currentColor` survives.
- `bma_render_image_or_svg()` — render an attachment/URL icon. Falls back to a plain `<img>` for URL icons.

If none are present the component still renders cleanly (numeric icon ids simply
yield no icon without an attachment helper).

## CSS

`src/style.css` ships the full layout in vanilla CSS, scoped to
`.bma-case-study-compare`. No Tailwind color/space tokens; component-scoped
custom properties carry explicit fallbacks. The `icon-breathe` animation is
ported scoped to this component. Consumers import:

```css
@import "../../vendor/balefireict/component-case-study-compare/src/style.css";
```

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/component-case-study-compare", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-case-study-compare": "*"
    }
}
```

The bootstrap is auto-loaded by Composer (`autoload.files`).

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-case-study-compare.php`
— same two shortcodes, same vc_map params, same attribute handling and escaping.
Inlines the needed helpers from `inc/shortcodes/bma-icon-card.php`.
