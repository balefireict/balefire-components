# balefireict/component-hr

Gradient horizontal rule — a decorative SVG divider centered in its container.

## What it is

A single (non-container) WPBakery / shortcode element that outputs a bare,
centered gradient `<hr>`-style SVG. It emits no `<section>` and no section
padding — wrap it in a `vc_row` for background, spacing, or an id.

## Shortcodes registered

- `[bma_hr width="230"]` — renders the gradient divider. `width` is the
  rendered pixel width (default `230`); height stays 4px and the viewBox
  preserves the gradient proportions.

Global function wrapper `bma_hr_shortcode( $atts )` is preserved from the
original rockerbox source for themes that call it directly.

## VC elements

- **BMA HR** (`bma_hr`) — category "BMA Elements". One `width` textfield param.

## Soft deps

None.

## CSS

This package owns its CSS. Consumers must import:

```
~/vendor/balefireict/component-hr/src/style.css
```

It only sets `display: flex; justify-content: center` on `.bma-hr` (the
rockerbox markup achieved this with the Tailwind `flex justify-center`
utilities; there were no dedicated `.bma-hr` rules in the theme CSS).

## Source attribution

Ported from rockerbox `inc/shortcodes/bma-hr.php`.
