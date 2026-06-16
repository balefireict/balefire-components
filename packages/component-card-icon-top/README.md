# component-card-icon-top

A 3-column grid of white info cards — icon image on top, red h3 title, WYSIWYG
body. Parent/child WPBakery container. Vanilla CSS (BEM, explicit values, no theme
tokens).

Source mock: `layouts/layouts-elements/card-icon-top.svg`.

## Usage

    [bma_card_icon_top title="AC Casino Pickup Schedule"]
        [bma_card_icon_top_item image="123" title="Resorts Casino"]<p><strong>Arrival:</strong> 12:35 PM</p>
        <p><strong>Departure:</strong> 6:00 PM</p>
        <p><strong>Location:</strong> Charter bus at Resorts Casino</p>[/bma_card_icon_top_item]
        [bma_card_icon_top_item image="124" title="Caesars Casino"]...[/bma_card_icon_top_item]
        [bma_card_icon_top_item image="125" title="Tropicana Casino"]...[/bma_card_icon_top_item]
    [/bma_card_icon_top]

## Params

**Parent `[bma_card_icon_top]`**
- `title` — backend-only label (not rendered).
- `class` — extra CSS class on the grid wrapper.

The parent owns `max-width: 1050px; margin-inline: auto` and a responsive grid
(1 col default, 3 cols >=768px).

**Child `[bma_card_icon_top_item]`**
- `image` — attachment ID (Icon media field).
- `title` — card h3 title.
- body (content) — WPBakery WYSIWYG (rich text).
- `class` — extra CSS class on the card.

## Notes

- No CSS variables — explicit values (`#84081c`, `#fff`, px sizes).
- `component-bakery-preview` is a soft dep (backend thumbnails); the element
  works without it (falls back to an eval'd container class).
