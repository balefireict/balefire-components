# component-header-split

**Type:** `scaffolding`
**Shortcode:** `[bma_header_split]`
**CSS root:** `.bma-c-header-split`
**CSS owned by:** host theme (this package ships **no CSS**)
**ACF group:** none

## What it does

Renders the site header: logo + primary nav + secondary nav + mobile drawer toggle.

This is a `scaffolding`-type component that intentionally ships **no CSS**. The host theme owns header styles entirely (because header design is one of the things that varies most per client). The component provides:

- Stable DOM structure with predictable hooks (`#header`, `.bma-c-header__*` BEM classes, `#nav-main`, `#nav-secondary`, `.mobile-menu-toggle`)
- Logo lookup via `wp_custom_logo()` with a configurable filter (`bma_c_header/logo_html`)
- Primary nav from the `primary` menu location
- Secondary nav from the `secondary` menu location (utility links, CTA buttons)
- Mobile drawer toggle button (markup only — drawer behavior lives in theme JS)
- Filterable wrapper attributes (`bma_c_header/wrapper_atts`)

## Usage

The host theme registers two menu locations:

```php
register_nav_menus( [
    'primary'   => __( 'Primary Navigation', 'theme-textdomain' ),
    'secondary' => __( 'Secondary Navigation (CTAs + utility)', 'theme-textdomain' ),
] );
```

Then in `header.php`:

```php
<?php echo do_shortcode( '[bma_header_split]' ); ?>
```

Or as a Bakery element if the page template allows it.

## Shortcode attributes

| Attribute     | Values                              | Default | Notes                                                  |
|---------------|-------------------------------------|---------|--------------------------------------------------------|
| `sticky`      | `true` \| `false`                   | `true`  | Adds `.bma-c-header-split--sticky` for theme CSS hook   |
| `blur`        | `true` \| `false`                   | `true`  | Adds `.bma-c-header-split--blur`                       |
| `align`       | `left` \| `center` \| `right`       | —       | Universal attr                                         |
| `variant`     | `light` \| `dark` \| brand-defined  | —       | Universal attr                                         |
| `container`   | `default` \| `wide` \| `narrow` \| `full` | —  | Universal attr (header inner row max-width)            |
| `id`, `class` | string                              | —       | Universal attrs                                        |
| `primary_menu`   | menu slug                        | `primary`   | Override menu location for primary nav              |
| `secondary_menu` | menu slug                        | `secondary` | Override menu location for secondary nav            |

## DOM hooks the theme can rely on

```html
<header id="header" class="bma-c-header-split bma-c-header-split--sticky bma-c-header-split--blur" role="banner">
    <div class="bma-c-header-split__inner">
        <a class="bma-c-header-split__logo" href="<home>" rel="home">
            <img src="..." alt="..." />   <!-- or filtered SVG via bma_c_header_split/logo_html -->
        </a>

        <nav class="bma-c-header-split__nav-primary" role="navigation" id="nav-main-wrapper" aria-label="Primary">
            <ul id="nav-main" class="nostyle">...</ul>
        </nav>

        <div class="bma-c-header-split__utility">
            <nav class="bma-c-header-split__nav-secondary" role="navigation" id="nav-secondary-wrapper" aria-label="Secondary">
                <ul id="nav-secondary" class="nostyle">...</ul>
            </nav>

            <button
                class="bma-c-header-split__toggle mobile-menu-toggle"
                type="button"
                aria-controls="default-sidebar"
                aria-expanded="false"
                data-drawer-target="default-sidebar"
                data-drawer-toggle="default-sidebar">
                <span class="sr-only">Open sidebar</span>
                <svg>...</svg>
            </button>
        </div>
    </div>
</header>
```

Class names match the existing rockerbox staging DOM where possible (`#nav-main`, `#nav-secondary`, `.mobile-menu-toggle`, `data-drawer-*`) so existing theme CSS and any drawer JS keeps working.

## Filters

| Filter                          | Default                           | Purpose                                  |
|---------------------------------|-----------------------------------|------------------------------------------|
| `bma_c_header_split/logo_html`        | `wp_custom_logo()` output         | Replace logo markup (e.g. inline SVG)   |
| `bma_c_header_split/wrapper_atts`     | `['class' => '...', ...]`         | Mutate root `<header>` attributes        |
| `bma_c_header_split/primary_args`     | `wp_nav_menu` args                | Change primary nav rendering             |
| `bma_c_header_split/secondary_args`   | `wp_nav_menu` args                | Change secondary nav rendering           |
| `bma_c_header_split/toggle_html`      | `<button>...</button>`            | Replace mobile drawer toggle markup      |

## CSS expectations (for the host theme)

The theme should provide:

- Base layout for `.bma-c-header-split__inner` (flex row, max-width, padding)
- Sticky/blur behavior keyed off `.bma-c-header-split--sticky`, `.bma-c-header-split--blur`
- Primary nav vertical-on-mobile, horizontal-on-desktop with submenu logic
- Secondary nav inline + `.btn` styling for CTAs
- Mobile drawer trigger visibility
- Container variants on `.bma-c-header-split__inner`
- `align` / `variant` modifier handling per design system

The Rockerbox theme provides a reference implementation in
`wp-content/themes/balefire/assets/css/header.css` once Phase 3 lands.

## Why no CSS in this package

Headers are one of the highest-variance design surfaces between clients:
sticky/non-sticky, transparent/solid, mega-menu/simple, dark/light, logo position, etc.
Shipping opinionated CSS here would force every consuming theme to override it,
which is worse than starting blank. The contract is the DOM + class names.
