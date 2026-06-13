# component-reviews-slider

Full-bleed testimonial slider over a `review` custom post type. One quote on
screen at a time, crossfading: serif quote, gold star pill, author + source
badge (multicolor Google "G" for Google reviews), faint edge chevrons.

Shortcode: `[vmg_reviews_slider]`

## CPT-backed

Unlike the attribute-driven components in this monorepo, this one reads a
site-provided `review` post type. It is intentionally coupled to the David
Tours data model:

- `post_title` — reviewer name
- `post_content` — review text
- meta `vmg_rating` (0–5, decimal ok)
- meta `vmg_review_date` (Y-m-d) — reserved
- meta `vmg_source_url` — link to the original review
- taxonomy `review_source` (`google` | `facebook` | `theknot`)
- taxonomy `review_category` (optional content filter)

Where that CPT does not exist, the internal `WP_Query` returns nothing and the
shortcode renders an empty string — a safe no-op in any other theme.

## Attributes

| Att | Default | Notes |
|---|---|---|
| `eyebrow` | `Customers Give Us High Praise` | Uppercase kicker. |
| `source` | `''` | Filter: `google,facebook,theknot` (comma list). |
| `category` | `''` | `review_category` slug(s). |
| `min_rating` | `''` | e.g. `5` — only reviews with `vmg_rating >=`. |
| `count` | `-1` | `-1` = all. |
| `orderby` | `date` | `date` or `rand`. |
| `autoplay` | `no` | `yes` to advance automatically. |
| `interval` | `7000` | Autoplay ms between slides. |

## Bare output — bring your own background

No background, image, or full-width wrapper. The component renders only the
slider (with vertical padding). Text colors assume a **dark** section, so drop
it in a full-width WPBakery row and style the row background (color / image /
blur). `.reviews-slider` is `position: relative` so the absolute nav chevrons
anchor correctly; `.reviews-slider__inner` caps content to 820px and centers it.

## Self-contained JS

Ships its own inline carousel JS (prev/next, drag/swipe, keyboard arrows,
hover/focus/tab-visibility-aware autoplay) — no theme JS dependency. The init
is guarded against double-binding, so multiple instances on a page are fine.

CSS lives in `src/style.css` and is auto-imported by the consumer theme's Vite
pipeline (the `/* @balefire-component-styles */` marker in `app.css`). No
design-token lookups; explicit values + component-scoped custom properties.

## WPBakery element

Mapped under **Custom Elements → "Reviews Slider"** (`vc_before_init`). Every
shortcode att is exposed as a param. No `php_class_name` — WPBakery defaults
non-container elements to `WPBakeryShortCodeFishBones`, which is all this needs.
