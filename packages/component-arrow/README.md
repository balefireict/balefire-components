# balefireict/component-arrow

Shared arrow SVG + safe SVG sanitizer. Pure PHP, no CSS, no shortcode.

## Provides

Global functions (thin wrappers around `Balefire\Component\Arrow\Arrow`):

- `bma_arrow_svg()` — returns the inline SVG string for the chevron arrow
  used in media cards / arrow links. Class is `bma-arrow` (was
  `w-[1.375rem] h-[0.75rem] shrink-0` in Tailwind form). Consumer theme can
  size via `.bma-arrow { width: 1.375rem; height: 0.75rem; }` in their CSS.
- `bma_safe_svg( $svg )` — strict allowlist sanitizer for inline SVG
  markup. Use when reading SVG from disk or from user input.

## Source of truth

`Balefire\Component\Arrow\Arrow` (PSR-4). The global functions are wrappers.

## Dependencies

None. Note that `balefire/bma-image-helper` will use `bma_safe_svg()` if it
exists (optional integration). If this package is not loaded, the image
helper falls back to passing raw SVG through `trim()`.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-arrow", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-arrow": "*"
    }
}
```

The bootstrap is auto-loaded by Composer. No `require` calls needed in the
theme's `functions.php`.

## Ported from

- `rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-arrow.php` — `bma_arrow_svg()`
- `rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-safe-svg.php` — `bma_safe_svg()`

Both merged into one package since they're conceptually SVG-related.
