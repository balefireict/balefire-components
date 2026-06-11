# balefireict/component-faq

BMA FAQ — a native `<details>`/`<summary>` accordion list with a `+`/`−`
chevron indicator. Parent/child WPBakery container shortcode, vanilla CSS.

FAQ items always render closed; open-by-default is intentionally unsupported.
A bare `[bma_faq]` with no children renders nothing.

## Shortcodes registered

- `bma_faq` — parent container. Attributes:
  - `title` (default "Frequently Asked Questions") — heading text.
  - `style` (default `no-borders`) — item style variant.
- `bma_faq_item` — child item. Attributes:
  - `question` — the question text (summary).
  - content — the answer HTML (between tags).

Example:

```
[bma_faq title="Frequently Asked Questions"]
  [bma_faq_item question="Question?"]Answer HTML.[/bma_faq_item]
[/bma_faq]
```

The parent also normalizes hyphenated/compact attribute keys via the
`bma_faq_attr()` helper (WPBakery may write underscores as hyphens).

## WPBakery elements

- **BMA FAQ** (`bma_faq`) — container element (`as_parent` → `bma_faq_item`,
  `is_container`, `js_view` VcColumnView). Registers
  `WPBakeryShortCode_BMA_Faq extends WPBakeryShortCodesContainer` on
  `vc_after_init`.
- **BMA FAQ Item** (`bma_faq_item`) — child element (`as_child` → `bma_faq`).

## Soft dependencies

None. This component is self-contained.

## CSS

This package owns its CSS. Consumers import:

```
@import "~/vendor/balefireict/component-faq/src/style.css";
```

CSS covers the FAQ list, item borders, `no-borders` variant, the question
summary (with default marker removed), the `+`/`−` chevron pseudo-element, the
answer block, and `.bma-bg-dark` overrides.

## Source attribution

Ported from the rockerbox balefire theme:
`inc/shortcodes/bma-faq.php` (PHP) and `resources/css/sections.css` (section 14, CSS).
