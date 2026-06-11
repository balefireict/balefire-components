# component-row-width

Adds a **Content Width** dropdown to WPBakery's Row and Inner Row settings
and caps the row at the chosen preset, centered with `margin-inline: auto`.

| Choice  | Class             | Width token                     |
|---------|-------------------|---------------------------------|
| Default | (none)            | —                               |
| 100%    | bma-row-w-full    | `var(--w-full, 100%)`           |
| 1080px  | bma-row-w-lg      | `var(--max-w-lg, 1080px)`       |
| 950px   | bma-row-w-md-lg   | `var(--max-w-md-lg, 950px)`     |
| 850px   | bma-row-w-md      | `var(--max-w-md, 850px)`        |
| 512px   | bma-row-w-sm      | `var(--max-w-sm, 512px)`        |
| Custom… | bma-row-w-custom  | inline `--bma-row-w` (any CSS length; bare numbers = px) |

Custom values come from a dependent textfield (shown only when Custom… is
selected), are sanitized to `number + px|%|rem|em|vw|ch`, and are injected
as `style="--bma-row-w:<value>"` on the row via the `vc_shortcode_output`
filter — invalid input is silently dropped.

Selectors are `.wpb_row.bma-row-w-*` (specificity 0,2,0) so they beat
js_composer.min.css's late-loading `.vc_row { margin-left:-15px;
margin-right:-15px }`, which at equal specificity un-centers the row and
causes horizontal scroll.

## How it works

- `vc_add_param( 'vc_row' / 'vc_row_inner', ... )` on `vc_after_init` adds
  the dropdown (param `content_width`), placed directly **above Minimum
  height** in Row Settings. WPBakery sorts edit-form params by descending
  `weight` (core params are 0), so the package pins row_title(40),
  full_width(30), gap(20) and inserts content_width at 10.
- The `vc_shortcodes_css_class` filter appends the matching `bma-row-w-*`
  class when a preset is selected.
- `src/style.css` defines the classes. The theme can override the widths by
  defining `--w-full` / `--max-w-lg` / `--max-w-md` / `--max-w-sm` on `:root`.

## No shortcode

This package registers no shortcode and no vc element — it only extends the
core Row elements.

## Caveat

"Stretch row" (vc_row's own design option) writes inline width/max-width on
the element, which wins over these classes. Use one or the other.

## CSS

Consumer imports `~/vendor/balefireict/component-row-width/src/style.css`
(or it's picked up automatically by the vmg auto-import Vite plugin).
