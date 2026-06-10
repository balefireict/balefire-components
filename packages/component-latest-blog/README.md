# balefireict/component-latest-blog

BMA Latest Blog shortcode + Post Card partial.

Renders a grid of the latest N posts. Each post is rendered as a PostCard
— the same card markup is available publicly via
`Balefire\Component\LatestBlog\PostCard::render( $post_id, $show_category )`
so blog archives, search results, and other consumers can render the same
card without re-implementing it.

## Provides

- `[latest_blog count="3" show_category="false"]` — rockerbox shortcode name (preserved for backward compat)
- `[bma_latest_blog ...]` — alias
- Global functions: `bma_latest_blog_render()`, `bma_post_card( $post_id )`
- WPBakery `vc_map` for the shortcode (uses the legacy `latest_blog` base)

## Card attribute schema

- `count` (1-12, default 3) — number of posts to show
- `show_category` (true/false, default false) — render the category pill above the title

## Vanilla CSS (no Tailwind)

Per project rule, this package's CSS uses explicit hex colors and rem
spacing, not Tailwind utility classes or `--color-*` / `--space-*` token
lookups. The card surface is transparent (no card border, no shadow) — the
`bma-post-card__media` figure has the rounded corners and the image fills
it. This matches the original rockerbox card visual.

## PostCard::render public API

```php
// In blog archive template, search results, related posts, etc.
echo \Balefire\Component\LatestBlog\PostCard::render( $post_id, true );
```

Signature:
- `int|null $post_id` — null = current post in the loop
- `bool $show_category` — render the category pill above the title

Returns `''` if the post is invalid (not found or not published).

## Source of truth

`Balefire\Component\LatestBlog\LatestBlog` (PSR-4) and
`Balefire\Component\LatestBlog\PostCard` (PSR-4). Exposes:
- `LatestBlog::render( $atts )` — shortcode callback
- `LatestBlog::register()` — add_shortcode for `[latest_blog]` and `[bma_latest_blog]`
- `LatestBlog::vcMap()` — vc_map (registered via bootstrap on vc_before_init)
- `PostCard::render( $post_id, $show_category )` — single card render (public API for archives/search)

## Dependencies (soft)

- `balefire/bma-arrow` — recommended; provides `bma_arrow_svg()` for the
  "Read more →" arrow in the card CTA. If not loaded, the arrow is omitted
  (graceful degradation via `function_exists` guard).

None of these are hard `composer require` dependencies — they're all
optional integrations.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-latest-blog", "options": { "symlink": true } }
    ],
    "require": {
        "balefireict/component-latest-blog": "*"
    }
}
```

```css
@import "../../vendor/balefireict/component-latest-blog/src/style.css";
```

## Ported from

- `rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-latest-blog.php` — shortcode + query loop
- `rockerbox/wp-content/themes/balefire/inc/post-card.php` — partial markup (vanilla-rewritten, inlined into PostCard.php as the source of truth)

The post-card markup that was previously a `require __DIR__ . '/../post-card.php'`
sibling file in rockerbox is now inlined into `PostCard::render()`. The
public `PostCard::render( $post_id )` method gives the same affordance
for blog archive / search templates — they can call it instead of doing a
`require` on a partial that doesn't exist in the monorepo.
