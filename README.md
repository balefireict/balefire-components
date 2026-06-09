# balefire-components

A Composer monorepo of self-contained `[bma_*]` shortcode packages for the
balefire WordPress theme family. Each package ships:

- A PSR-4 class (`Balefire\Components\<Name>\<Class>`) — source of truth
- A `bootstrap.php` auto-loaded by Composer that:
  - registers `add_shortcode` (if the package has one)
  - registers `vc_before_init` → `vc_map()` for the WPBakery editor
  - defines thin global function wrappers (`bma_foo_render()`, etc.) for
    templates and other packages to call without ceremony
- A vanilla CSS file in `assets/` (no Tailwind utility classes — semantic BEM)
- A `composer.json` declaring any inter-package dependencies

## Pattern: hybrid PSR-4 + global wrappers

```php
// src/Buttons.php
namespace Balefire\Components\Buttons;

final class Buttons {
    public static function render( array $atts ): string { /* ... */ }
    public static function register(): void { add_shortcode( 'bma_buttons', [ self::class, 'render' ] ); }
    public static function vcMap(): void { /* vc_map( ... ) */ }
}
```

```php
// bootstrap.php (autoloaded by Composer)
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_buttons_render' ) ) {
    function bma_buttons_render( array $atts ): string {
        return \Balefire\Components\Buttons\Buttons::render( $atts );
    }
}

add_action( 'plugins_loaded', function (): void {
    \Balefire\Components\Buttons\Buttons::register();
    if ( function_exists( 'vc_map' ) ) {
        add_action( 'vc_before_init', [ \Balefire\Components\Buttons\Buttons::class, 'vcMap' ] );
    }
}, 20 );
```

## Package layout

```
packages/<name>/
├── composer.json
├── src/
│   └── <Class>.php
├── assets/
│   └── <name>.css
├── bootstrap.php
├── README.md
└── .gitkeep
```

## CSS delivery (theme-bundled)

Packages do **not** enqueue their own CSS. The consumer theme's Vite `app.css`
imports each vendored package CSS, and Vite bundles them into one hashed
`dist/assets/app-*.css` for prod, serves them via HMR for dev. One request,
one cache-bust, no per-component enqueue.

```css
/* consumer theme resources/css/app.css */
@import "../../vendor/balefire/bma-buttons/assets/buttons.css";
@import "../../vendor/balefire/bma-latest-blog/assets/latest-blog.css";
/* ... */
```

## Consuming from a theme

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/*", "options": { "symlink": true } }
    ],
    "require": {
        "balefire/bma-buttons": "*",
        "balefire/bma-latest-blog": "*"
    }
}
```

```bash
composer install
```

Bootstrap files auto-load — no `require` calls needed in `functions.php`. Each
shortcode registers itself on `plugins_loaded` priority 20 (after WP core
shortcodes + WPBakery have loaded).

## Conventions

- **Bare output.** No `<section>` wrappers, no `bma-container` padding. The
  WPBakery `vc_row` (or the consumer theme's template) owns section/background.
- **Semantic BEM.** `.bma-<component>__<element>--<modifier>`. No Tailwind
  utility classes in markup. No `@apply` in CSS.
- **Vanilla CSS only.** No Tailwind, no preprocessor. Plain modern CSS with
  custom properties from the consumer theme.
- **No hard-coded color tokens.** Per project direction (June 2026), use
  `currentColor` / `inherit` / explicit hex only. Do not look up
  `--color-*` / `--neutral-*` tokens in package CSS — the consumer theme
  injects its own.
- **Escaped output.** All renderer output passes through `esc_html`,
  `esc_attr`, `esc_url`, `wp_kses_post` as appropriate.
- **No silent string surgery on the inner content** except for the
  `<br />` cleanup that WPBakery's editor inserts around nested shortcodes.

## Adding a new package

1. `cp -R packages/bma-image-helper packages/bma-<new>`
2. Rename `src/ImageHelper.php` → `src/<New>.php`, update the namespace
3. Rename `assets/image-helper.css` (or add `assets/<new>.css` if it has CSS)
4. Update `composer.json` (name, autoload namespace, autoload files)
5. Add a `repositories` entry to the root `composer.json`
6. Update the consumer theme's `app.css` import list
