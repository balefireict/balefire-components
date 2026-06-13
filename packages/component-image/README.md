# balefireict/component-image

BMA Image — a single image rendered inside a `<figure class="bma-image">` with
controls for object-fit, crop position (object-position), aspect ratio, and
optional rounded corners. WPBakery (WPBakery Page Builder) element.

Ported from rockerbox `inc/shortcodes/bma-image-text.php` (the `[bma_image]`
element). The original expressed fit/crop/aspect/rounded as Tailwind utility
classes; this package emits semantic modifier classes and ships the vanilla CSS
that reproduces the same visual result.

## Shortcodes registered

- `[bma_image id="123" size="full" fit="object-cover" crop="object-center" aspect="aspect-video" rounded="true" link="false" link_url="" link_target=""]`

Defaults: `size=full`, `fit=object-cover`, `crop=object-center`,
`aspect=aspect-video`, `rounded=true`, `link=false`. An empty/invalid `id`
(or an attachment that resolves to no URL) renders nothing.

- `size` — any registered thumbnail size name; unknown names fall back to
  `full`.
- `link` — `true` wraps the image in an anchor. Renders only when
  `link_url` is also set; `link_target="_blank"` opens a new window (adds
  `rel="noopener noreferrer"`).

## Params (vc_map)

- `id` — attach_image (media library)
- `size` — dropdown built from `get_intermediate_image_sizes()` at map
  time (Full + thumbnail/medium/large/etc. as registered on the site)
- `fit` — dropdown: object-cover / object-contain / object-fill / object-none / default
- `crop` — dropdown: object-center + 8 edge/corner positions
- `aspect` — dropdown: aspect-video / aspect-square / aspect-4/3 / aspect-3/4 / aspect-16/9 / aspect-21/9 / aspect-auto / default (none)
- `rounded` — checkbox
- `link` — checkbox (default off). Link URL + Link Target params show
  only when checked (vc dependency).

## Emitted markup

```html
<figure class="bma-image bma-image--aspect-video bma-image--rounded">
  <img class="bma-image__img bma-image--cover" decoding="async" src="…" alt="…" loading="lazy" />
</figure>
```

Linked:

```html
<figure class="bma-image bma-image--aspect-video bma-image--rounded">
  <a href="…" class="bma-image__link" target="_blank" rel="noopener noreferrer">
    <img class="bma-image__img bma-image--cover" … />
  </a>
</figure>
```

Aspect-ratio and rounded modifiers sit on the `<figure>`; fit and crop modifiers
sit on the `<img>`. The `.bma-image__link` wrapper is display:block +
height:100% so aspect/fit behave identically when linked.

## Global function wrappers

The original rockerbox global names are preserved (via `function_exists`
guards in `src/bootstrap.php`) so existing themes keep working:

- `bma_image_shortcode( array $atts ): string`
- `bma_image_fit_class( string $value ): string`
- `bma_image_crop_class( string $value ): string`
- `bma_image_aspect_class( string $value ): string`

## Soft deps

None. This package is self-contained. (Note: it is unrelated to the image
*helper* package whose `bma_render_image_or_svg` / `bma_inline_svg_attachment`
functions live elsewhere — this element does not depend on them.)

## CSS

This package owns its CSS. Consumers must import the stylesheet:

```
~/vendor/balefireict/component-image/src/style.css
```

No Tailwind color/space tokens are referenced. The rounded-corner radius uses
`var(--radius-card, 0.5rem)` and the bottom margin uses
`var(--bma-image-gap, 2rem)`, both with standalone fallbacks.

## Source attribution

rockerbox `wp-content/themes/balefire/inc/shortcodes/bma-image-text.php` and the
`.bma-image` rule in `resources/css/sections.css`.
