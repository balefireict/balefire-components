# balefire/bma-image-helper

Image + SVG rendering helpers for other bma_* shortcode packages. Pure PHP,
no CSS, no shortcode.

## Provides

Global functions (thin wrappers around `Balefire\Components\ImageHelper\ImageHelper`):

- `bma_render_image_or_svg( $value, $size = 'full', $img_class = '' )` — renders
  an attachment image (if $value is numeric) or raw SVG markup (if string).
  Strips XML prolog / DOCTYPE. Calls `bma_safe_svg()` if available (provided by
  `balefire/bma-arrow` or any package that ships one); otherwise returns the
  trimmed SVG as-is.
- `bma_inline_svg_attachment( $attachment_id )` — reads an SVG attachment from
  disk and returns inline markup, but only if the SVG uses `currentColor` (so
  CSS can theme it). Returns `''` for non-SVG or hardcoded-fill SVGs.

## Source of truth

`Balefire\Components\ImageHelper\ImageHelper` (PSR-4). The global functions are
wrappers.

## Dependencies

None. Optional integration: if `balefire/bma-arrow` (or any package providing
`bma_safe_svg()`) is loaded, output is sanitized through it.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-image-helper", "options": { "symlink": true } }
    ],
    "require": {
        "balefire/bma-image-helper": "*"
    }
}
```

```bash
composer update balefire/bma-image-helper
```

The bootstrap is auto-loaded by Composer's `autoload.files`. No `require`
calls needed in the theme's `functions.php`.

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-image.php` — same
two functions, restructured to the hybrid PSR-4 + global-wrappers pattern.
