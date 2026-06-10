# balefire-components

A Composer monorepo of self-contained `[bma_*]` shortcode component packages
for the balefire WordPress theme family (`balefire-base` and forks like
`vmg`/davidtours). Each package ships:

- A PSR-4 class (`Balefire\Component\<Name>\<Class>`) — source of truth
- A `src/bootstrap.php` auto-loaded by Composer that:
  - registers `add_shortcode` (if the package has one)
  - registers `vc_before_init` → `vc_map()` for the WPBakery editor
  - defines thin global function wrappers (`bma_foo_render()`, etc.) for
    templates and other packages to call without ceremony
- Vanilla CSS at `src/style.css` (no Tailwind utility classes — semantic BEM)
- A `composer.json` with `extra.balefire-component` metadata (slug, type,
  shortcode, css_class, owns_css)

## Naming conventions

| Concept        | Convention                           | Example                         |
|----------------|--------------------------------------|---------------------------------|
| Package name   | `balefireict/component-<slug>`       | `balefireict/component-buttons` |
| PHP namespace  | `Balefire\Component\<PascalSlug>`    | `Balefire\Component\Buttons`    |
| Shortcode tag  | `bma_<slug>`                         | `[bma_buttons]`                 |
| CSS root class | `.bma-<slug>`                        | `.bma-buttons`                  |
| CSS entry      | `src/style.css`                      |                                 |
| Bootstrap      | `src/bootstrap.php` (autoload.files) |                                 |

## Pattern: hybrid PSR-4 + global wrappers

```php
// src/Buttons.php
namespace Balefire\Component\Buttons;

final class Buttons {
    public static function render( array $atts ): string { /* ... */ }
    public static function register(): void { add_shortcode( 'bma_buttons', [ self::class, 'render' ] ); }
    public static function vcMap(): void { /* vc_map( ... ) */ }
}
```

```php
// src/bootstrap.php (autoloaded by Composer)
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_buttons_render' ) ) {
    function bma_buttons_render( array $atts ): string {
        return \Balefire\Component\Buttons\Buttons::render( $atts );
    }
}

$boot = static function (): void {
    \Balefire\Component\Buttons\Buttons::register();
    if ( function_exists( 'vc_map' ) ) {
        add_action( 'vc_before_init', [ \Balefire\Component\Buttons\Buttons::class, 'vcMap' ] );
    }
};
// Theme autoloaders run AFTER plugins_loaded has fired — boot immediately
// on that path, otherwise hook normally.
if ( did_action( 'plugins_loaded' ) ) {
    $boot();
} else {
    add_action( 'plugins_loaded', $boot, 20 );
}
unset( $boot );
```

## Package layout

```
packages/component-<slug>/
├── composer.json          (autoload.files: src/bootstrap.php, PSR-4, extra.balefire-component)
├── README.md
├── acf-json/              (optional — field-group components)
└── src/
    ├── bootstrap.php
    ├── <Class>.php
    └── style.css          (only when owns_css)
```

## CSS delivery (theme-bundled)

Packages do **not** enqueue their own CSS. The consumer theme's Vite `app.css`
imports each vendored package CSS, and Vite bundles them into one hashed
`dist/assets/app-*.css` for prod, serves them via HMR for dev. One request,
one cache-bust, no per-component enqueue.

```css
/* consumer theme resources/css/app.css — `~/vendor/...` is a Vite alias */
@import "~/vendor/balefireict/component-buttons/src/style.css";
@import "~/vendor/balefireict/component-latest-blog/src/style.css";
/* ... */
```

## Consuming from a site

Site-level `wp-content/composer.json` (the monorepo must be a Herd sibling —
`../../balefire-components/` relative to `wp-content/`):

```json
{
    "repositories": [
        { "type": "path", "url": "../../balefire-components/packages/*", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-buttons": "dev-main",
        "balefireict/component-latest-blog": "dev-main"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

```bash
cd wp-content && composer install
```

Bootstrap files auto-load — no `require` calls needed in `functions.php`
beyond the site-level `wp-content/vendor/autoload.php` (the theme's
2-tier autoload handles this). Shortcodes self-register on
`plugins_loaded` priority 20, or immediately when the hook already fired.

Scaffold a whole consumer site with
`balefire-base/.../scripts/new-site.sh <site-dir> <balefire|vmg>`.

### Deploying to WPE / CI

Path repos can't resolve on the server. Either commit `wp-content/vendor/`
to the site repo, or in CI: check out this monorepo as a sibling and run
`COMPOSER_MIRROR_PATH_REPOS=1 composer install --no-dev` (real copies,
rsync-safe) before pushing.

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

1. `cp -R packages/component-image-helper packages/component-<slug>`
2. Rename `src/ImageHelper.php` → `src/<Pascal>.php`, update the namespace to
   `Balefire\Component\<Pascal>`
3. Add `src/style.css` if it has CSS (and set `owns_css: true` in
   `extra.balefire-component`)
4. Update `composer.json` (name, autoload namespace, autoload files, extra)
5. Add `"balefireict/component-<slug>": "dev-main"` to the root
   `composer.json` require (the wildcard path repo picks the package up
   automatically)
6. Update the consumer theme's `app.css` import list
