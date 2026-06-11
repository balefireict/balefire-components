# component-footer-cta

BMA Footer CTA — a centered footer call-to-action content block: optional
preheader, title, body copy, and up to two buttons (a solid white primary with
optional arrow, and an optional transparent secondary).

Ported from the rockerbox balefire theme
(`inc/shortcodes/bma-footer-cta.php`). The CTA button helpers from
`inc/shortcodes/bma-cta-btn.php` (`bma_render_cta_btns`,
`bma_render_cta_button`, `bma_cta_button_class`, `bma_cta_button_styles`) are
inlined here as static methods; if a theme still defines those globals, they
are preferred via `function_exists()`.

## Shortcodes registered

- `[bma_footer_cta]` — single (self-closing or enclosing) element.

It emits only:

```html
<div class="bma-cta"><div class="bma-container">…</div></div>
```

No `<section>`, no `id`, no background class. The wrapping WPBakery `[vc_row]`
(e.g. `<div id="cta-green-gradient" class="vc_row bma-bg-gradient">`) supplies
those.

### Attributes

`preheader`, `title`, `content` (textarea_html), `btn1_label`, `btn1_url`,
`btn1_arrow` (true/false), `btn2_label`, `btn2_url`, plus `post_id`.
Hyphenated attribute spellings (`btn1-label` etc.) are normalized to
underscores. A legacy `get_post_meta` fallback (`footer_cta_*`, `cta_btns*`)
keeps un-migrated shortcodes from going blank.

## WPBakery elements

- "BMA Footer CTA" (`bma_footer_cta`) under the "BMA Elements" category.

## Soft dependencies

None required. The arrow SVG is inlined. If a theme defines the global
`bma_render_cta_btns()` / `bma_render_cta_button()` / `bma_cta_button_class()`
/ `bma_cta_button_styles()` functions, they are used in preference to the
inlined implementations.

## CSS

This package owns its CSS. Consumers import:

```
~/vendor/balefireict/component-footer-cta/src/style.css
```

Covers `.bma-cta`, `.bma-cta__inner`, `.bma-cta__preheader`,
`.bma-cta__title`, `.bma-cta__content`, and `.bma-cta__actions` (the button
row, replacing the stripped Tailwind utilities). Contextual
`.bma-bg-gradient` / `.bma-bg-dark` overrides are included so the block
degrades correctly inside those rows. Page-specific `#cta-green-gradient`
rules remain in the theme.

## Source attribution

rockerbox `wp-content/themes/balefire/inc/shortcodes/bma-footer-cta.php`
(+ inlined `inc/shortcodes/bma-cta-btn.php`), CSS from
`resources/css/sections.css`.
