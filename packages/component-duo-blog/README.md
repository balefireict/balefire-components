# component-duo-blog

Latest blog posts rendered as large image-cover cards in a 2-column grid.
Built from `davidtours/layouts/sections/duo-blog.svg`.

Each card: featured image fill, dark wash + bottom gradient, white
semibold title bottom-left, "Read More" button (`#5689e3`). The whole
card is one link to the post.

## Shortcode

```
[bma_duo_blog count="2" button_text="Read More"]
```

| Att | Default | Notes |
|---|---|---|
| `count` | `2` | Latest posts to show, clamped 1-6. |
| `button_text` | `Read More` | Card button label. |
| `class` | `''` | Extra class on the grid wrapper. |

## WPBakery

Mapped as **"Duo Blog"** under *Custom Elements* (description starts
with "BMA —" so devs can search "bma").

## Grid

Columns/gap come from `component-auto-grid` (soft dependency — the
consumer theme is expected to have it installed; this package only emits
the `bma-auto-grid auto-grid-cols-1 md:auto-grid-cols-2 auto-grid-gap-12`
classes). 1 column below 782px, 2 columns above.

## CSS

`src/style.css` — consumer theme imports it via the Vite
`@balefire-component-styles` marker (davidtours) or a manual `@import`.
No theme design-token lookups; explicit hex per monorepo rules.
Cards keep a `500 / 303` aspect ratio from the SVG reference.
