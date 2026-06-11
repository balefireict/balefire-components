# balefireict/component-card-stat

Industry stat cards: a responsive grid of cards, each showing an icon, a title,
and two stat pairs (value + label). White or dark card styles.

Ported from rockerbox `inc/shortcodes/bma-card-stat.php`.

## Shortcodes registered

- `[bma_card_stat_grid columns="3"] … [/bma_card_stat_grid]` — parent container
  (auto-grid). Emits `bma-auto-grid auto-grid-*` classes.
- `[bma_card_stat title="" icon="" left_value="" left_label="" right_value=""
  right_label="" card_style="white|dark" el_class=""] … [/bma_card_stat]` —
  one stat card.
- `[bma_stat_icon]<svg>…</svg>[/bma_stat_icon]` — inline SVG icon passthrough,
  used inside a card.

Example:

```
[bma_card_stat_grid columns="3"]
  [bma_card_stat title="Staffing"
      left_value="30%"  left_label="Avg. Eligibility"
      right_value="$2,150" right_label="Avg. Credit / Hire"]
    [bma_stat_icon]<svg>…</svg>[/bma_stat_icon]
  [/bma_card_stat]
[/bma_card_stat_grid]
```

## WPBakery elements

- BMA Card Stat Grid (parent container, `as_parent` → `bma_card_stat`)
- BMA Card Stat (child, `as_child` → `bma_card_stat_grid`)

The parent registers `WPBakeryShortCode_BMA_CardStat` (extends
`WPBakeryShortCodesContainer`) on `vc_after_init` so children are editable.

## Soft dependencies (optional, not composer-required)

- `bma_safe_svg()` — sanitizes inline SVG icons (component-arrow). Falls back to
  `wp_kses_post()`.
- `bma_render_image_or_svg()` — renders an attachment-id icon
  (component-image-helper). Falls back to `wp_get_attachment_image()`.

None are redefined here; each call is `function_exists()`-guarded.

## CSS

This package owns its CSS. Consumers import:

```
~/vendor/balefireict/component-card-stat/src/style.css
```

The auto-grid layout classes (`bma-auto-grid`, `auto-grid-cols-*`,
`auto-grid-gap-*`) are intentionally NOT styled here — they are owned by
`component-auto-grid`.
