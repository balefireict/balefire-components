# balefireict/component-brand-icon-cards

BMA Brand Icon Cards — parent/child WPBakery container of media-cards
each with a small top-right logo, large media area, title, body, and
optional link with arrow.

## Provides

- `[brand_icon_cards columns="3|4|5|6"]…[/brand_icon_cards]` (parent)
- `[brand_icon_card title="..." media_image="123" media_svg="<svg>..." logo_image="456" logo_svg="<svg>..." body="..." href="/x" new_tab="false" /]` (child)
- Aliases: `[brand-icon-cards]` and `[brand-icon-card]` (hyphenated, for legacy markup)
- Global functions: `bma_brand_icon_cards_render()`, `bma_brand_icon_card_render()`, `bma_card_media_html()`, `bma_card_logo_html()`
- WPBakery `vc_map` for both parent (container) and child

## Card attribute schema (flat, not nested)

Unlike the rockerbox original, this monorepo version uses a **flat
attribute schema** on the card shortcode. There are no nested
`[brand_icon_logo]` or `[brand_icon_icon]` shortcodes inside the card
body. The card shortcode accepts `media_image`/`media_svg` for the large
media area and `logo_image`/`logo_svg` for the top-right logo. This makes
the WPBakery editor panel show all card options in one place and removes
the HTML-walking complexity the rockerbox version needed to extract nested
shortcodes from the body content.

`media_svg` / `logo_svg` take priority over `media_image` / `logo_image`
when both are provided. SVG strings are sanitized through `bma_safe_svg()`
(provided by `balefire/bma-arrow`).

## Dependencies (soft)

- `balefire/bma-image-helper` — *not required*; the card uses
  `wp_get_attachment_image_url` directly via `CardMedia::resolveImageUrl`.
- `balefire/bma-arrow` — recommended; provides `bma_safe_svg()` for SVG
  sanitization and `bma_arrow_svg()` for the "Learn More →" link arrow.
  If not loaded, the card falls back to passing SVG through `trim()` and
  omits the arrow icon.
- `balefire/bma-href` — recommended; `bma_resolve_href()` is used to
  resolve numeric `href` values to page permalinks. If not loaded, `href`
  is treated as a literal URL.

None of these are hard `composer require` dependencies — they're all
optional integrations. This keeps the package self-contained and the
`bma-*` global function wrappers (`function_exists` guards) handle the
optional loads cleanly.

## Source of truth

`Balefire\Component\BrandIconCards\BrandIconCards` (PSR-4) and
`Balefire\Component\BrandIconCards\CardMedia` (private internal helper
for media/logo rendering). Exposes:
- `BrandIconCards::render( $atts, $content )` — parent
- `BrandIconCards::renderCard( $atts )` — child
- `BrandIconCards::register()` — add_shortcode for both (and aliases)
- `BrandIconCards::vcMap()` — vc_map for both
- `BrandIconCards::registerContainerClass()` — eval'd `WPBakeryShortCode_BMA_BrandIconCards extends WPBakeryShortCodesContainer {}` (called on `vc_after_init`)
- `CardMedia::mediaHtml( $svg, $img )` — large media HTML
- `CardMedia::logoHtml( $svg, $img )` — small logo HTML

## CSS

`src/style.css` — uses `data-cols` attribute selector +
`--bma-cols` custom property for column count (same pattern as
`bma-logo-grid`). Uses `--radius-card` and `--shadow-card` design tokens
from the consumer theme (NOT on the prohibited `--color-*` / `--space-*`
list). Falls back to explicit `0.5rem` / `0 4px 12px rgb(0 0 0 / 10%)` if
the tokens are undefined.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-brand-icon-cards", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-brand-icon-cards": "*",
        "balefire/bma-arrow": "*",
        "balefire/bma-href": "*"
    }
}
```

```css
@import "../../vendor/balefireict/component-brand-icon-cards/src/style.css";
```

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/brand-icon-cards.php`
plus `inc/shortcodes/bma-card-media.php` (the latter is folded in as the
private `CardMedia` class). API changed from nested shortcodes to flat
attributes — see "Card attribute schema" above.
