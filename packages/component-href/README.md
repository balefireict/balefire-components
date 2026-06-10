# balefireict/component-href

Link href resolver: `link_page` (page ID or URL) and `link_url` (full URL) to
absolute href. Pure PHP, no CSS, no shortcode.

## Provides

Global function (thin wrapper around `Balefire\Component\Href\Href`):

- `bma_resolve_href( $link_page, $link_url )` — returns the absolute href.
  Explicit `$link_url` wins over `$link_page`. Numeric `$link_page` is
  resolved through `get_permalink()`. Non-numeric `$link_page` is treated
  as a URL. Returns `''` if both are empty.

## Source of truth

`Balefire\Component\Href\Href` (PSR-4). The global function is a wrapper.

## Dependencies

None. Uses WordPress's `get_permalink()` and `esc_url()`.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-href", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-href": "*"
    }
}
```

The bootstrap is auto-loaded by Composer. No `require` calls needed in the
theme's `functions.php`.

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-href.php` — same
function, restructured to the hybrid PSR-4 + global-wrappers pattern.
