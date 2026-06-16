# component-cta-bar

A solid-background two-column CTA bar — heading + subtext on the left, an icon
button on the right. Single-element WPBakery shortcode. Vanilla CSS (BEM, explicit
values, no theme tokens).

Source mock: printable-schedule bar (solid `#2e266d`, white text, `#6f779d` button
with calendar icon).

## Usage

    [bma_cta_bar heading="Need a Copy of the Schedule?" btn_label="Printable Schedule" btn_url="/schedule.pdf" btn_target="_blank"]Download or print the full Atlantic City bus schedule before your trip.[/bma_cta_bar]

## Params

- `heading` — bold heading text (left).
- body (content) — WYSIWYG subtext under the heading.
- `btn_label` — button text.
- `btn_url` — button link (omit for a non-link button).
- `btn_target` — `_blank` for new tab, empty for same window.
- `class` — extra CSS class.

The bar owns its `#2e266d` background; inner content is capped at 1050px centered.
Drop in a stretch row for full-bleed. The calendar icon is hardcoded in the button.

## Notes

- No CSS variables — explicit values (`#2e266d`, `#6f779d`, px sizes).
- Pairs with `bg-blue-overlay-top` (flush-bottom row class) when placed directly
  below a card grid section.
