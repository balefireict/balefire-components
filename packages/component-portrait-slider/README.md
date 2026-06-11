# balefireict/component-portrait-slider

Swipeable portrait image-card slider for WordPress + WPBakery, ported from the
rockerbox `balefire` theme. A parent container holds any number of portrait
slide children rendered inside a [Swiper](https://swiperjs.com/) carousel.

## Shortcodes registered

| Shortcode | Alias | Role |
|-----------|-------|------|
| `[bma_portrait_slider]` | `[bma-portrait-slider]` | Parent container (wraps slides) |
| `[bma_portrait_slide]`  | `[bma-portrait-slide]`  | Child — one portrait card |

Example:

```
[bma_portrait_slider]
  [bma_portrait_slide title="Healthcare" image="123" linkurl="/healthcare" newtab="true"]
  [bma_portrait_slide title="Finance" image="124"]
[/bma_portrait_slider]
```

Child params: `title`, `image` (attachment ID or URL), `linkurl`, `newtab`.

## WPBakery elements

Registered on `vc_before_init` via `vc_map`:

- **BMA Portrait Slider** — container (`as_parent` → `bma_portrait_slide`,
  `is_container`, `js_view => VcColumnView`). Its
  `WPBakeryShortCode_BMA_PortraitSlider` container class is registered on
  `vc_after_init`.
- **BMA Portrait Slide** — child (`as_child` → `bma_portrait_slider`) with
  Title, Image, Link URL and "Open in new tab" params.

## Soft dependencies (optional, gated)

These are provided by other balefire-components packages and are called only
when present (guarded by `function_exists` / `class_exists`):

- `bma_resolve_href()` (component-href) — resolves a page/URL link. Falls back
  to the raw Link URL when absent.
- `\Balefire\Assets::needsSwiper()` (consumer theme only) — enqueues Swiper
  assets. Guarded; never fatal if absent.

This package does **not** `composer require` these — install the relevant
packages if you need their behavior.

## Swiper is a consumer responsibility

This package ships **no** Swiper library code or CSS:

- The consumer theme **must enqueue Swiper's JS** (`window.Swiper`). The inline
  init script emitted by the parent shortcode retries up to 20 times (50ms
  apart) waiting for `window.Swiper`, then initializes.
- The consumer theme **must enqueue Swiper's own core CSS**
  (`swiper-bundle.min.css`). `src/style.css` only styles the `.bma-portrait-*`
  card internals plus component-scoped arrow/pagination overrides.

## CSS

Import the package stylesheet from your theme build:

```css
@import "~/vendor/balefireict/component-portrait-slider/src/style.css";
```

`owns_css: true`. No Tailwind color/space tokens are referenced. Overlay colors
and card radius/padding can be themed via the component-scoped custom
properties (all carry standalone fallbacks):
`--bma-portrait-overlay-from`, `--bma-portrait-overlay-to`, `--radius-card`,
`--spacing-card`, `--bma-container-max`.

## Source attribution

Ported from rockerbox theme:
`inc/shortcodes/bma-industry-slider.php` and `.bma-portrait-*` rules in
`resources/css/swiper.css`.
