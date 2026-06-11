# component-team-member-centered

BMA Team Member (Centered) — a circular-photo team grid built from a WPBakery
parent container plus repeatable member children. Ported from the rockerbox
balefire theme shortcode.

## What it is

A centered team-member grid: each member is a circular photo with a name and
role stacked below, centered. The parent emits an auto-grid container; children
render the individual member cards.

## Shortcodes registered

- `bma_team_grid` (parent container) — wraps members and emits the
  `bma-auto-grid` grid markup. Attributes:
  - `columns` — desktop column count (2, 3, 4, 5, 6; default 3). 3-col founder
    rows use a larger gap.
  - `el_id` — optional `id` attribute on the grid wrapper.
- `bma_team_member` (child) — one centered member. Attributes:
  - `image` — attachment id (preferred) or full image URL.
  - `name` — member name.
  - `role` — member role/title.

Original rockerbox global functions `bma_team_grid_shortcode()` and
`bma_team_member_shortcode()` are preserved as thin wrappers in
`src/bootstrap.php` so existing themes keep working.

## WPBakery elements

- BMA Team Grid (container, `as_parent` → `bma_team_member`)
- BMA Team Member (child, `as_child` → `bma_team_grid`)

The container class `WPBakeryShortCode_BMA_TeamMemberCentered` is registered on
`vc_after_init` so the backend editor treats the grid as a real container.

## Soft deps

None. (No bma_safe_svg / image-helper / href / Assets calls are used by this
component.)

## CSS

This package owns its card-internal CSS only. The consumer must import:

    ~/vendor/balefireict/component-team-member-centered/src/style.css

The grid layout classes emitted by the parent (`bma-auto-grid`,
`auto-grid-cols-N`, `auto-grid-gap-N`, `lg:auto-grid-cols-N`) are intentionally
NOT styled here — they are owned by the separate `component-auto-grid` package.

## Source attribution

Ported from rockerbox
`wp-content/themes/balefire/inc/shortcodes/bma-team-member-centered.php`.
