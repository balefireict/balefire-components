# balefireict/component-auto-grid

Shared responsive grid utility for the balefire / rockerbox card components.
This package owns the `bma-auto-grid` grid CSS so the individual card
packages don't each redefine it.

## What it is

A CSS-only utility package. It provides:

- `.bma-auto-grid` — a flex-wrap row that centers the last (orphan) row.
- `.auto-grid-cols-N` (1–6) — sets each child's width so N items fit per row.
- `.md:auto-grid-cols-N` / `.lg:auto-grid-cols-N` — responsive column counts
  at 782px (md) and 960px (lg).
- `.auto-grid-gap-N` (5 / 6 / 12) — inter-item gap via `--auto-grid-gap`.

Markup pattern (Tailwind utilities removed, semantic classes kept):

```html
<div class="bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 lg:auto-grid-cols-3">
  <div><!-- card --></div>
  <div><!-- card --></div>
  <div><!-- card --></div>
</div>
```

## Shortcodes / VC elements

None. This package registers **no shortcode** and **no vc_map**. It exists
solely to own the shared grid CSS plus a small class-string helper.

## Helper

```php
// Class on the package:
\Balefire\Component\AutoGrid\AutoGrid::gridClasses(3);          // bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 lg:auto-grid-cols-3
\Balefire\Component\AutoGrid\AutoGrid::gridClasses(2, 1, '12'); // bma-auto-grid auto-grid-gap-12 auto-grid-cols-1 lg:auto-grid-cols-2

// Global wrapper (same signature):
bma_auto_grid_classes($cols_desktop, $cols_mobile = 1, $gap = '6');
```

## Soft deps

None.

## CSS note

Consumers import the stylesheet from the installed package:

```
~/vendor/balefireict/component-auto-grid/src/style.css
```

The CSS is vanilla — no Tailwind color/space tokens. The gap custom
property (`--auto-grid-gap`) has an explicit `1.5rem` fallback so the grid
works standalone.

## Source attribution

Ported from the rockerbox balefire theme:
`resources/css/sections.css` (auto-grid utilities block) and the
`bma-auto-grid` markup emitted by `inc/shortcodes/bma-simple-card.php`,
`bma-steps.php`, `bma-card-stat.php`, `bma-team-member-centered.php`,
`bma-simple-image-card.php`.
