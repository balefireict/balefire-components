# component-simple-text-card

Text-only info card with color picker + text align support. CSS-only component — markup is hand-placed (HTML block or WPBakery raw column).

## Usage

```html
<div class="simple-text-card">
    <p class="simple-text-card__eyebrow">10:50 AM — Feasterville</p>
    <h3 class="simple-text-card__title">2400 Old Lincoln Highway</h3>
    <div class="simple-text-card__body">Right past the Radisson…</div>
</div>
```

## Modifiers

**Background** — default is `--vmg-gray-50`.

Named brand colors:
- `.simple-text-card--blue` / `--navy` / `--indigo` / `--red` (auto-flip text to white)
- `.simple-text-card--violet` / `--white`

Color picker: set `--stc-bg` inline — `style="--stc-bg: #e3e8ff"`.

**Text align**: `.simple-text-card--center`, `.simple-text-card--right` (default left).

## BEM structure

- `.simple-text-card` — card shell (flex column, padding, radius)
- `.simple-text-card__eyebrow` — accent label (bold, `--stc-eyebrow` color)
- `.simple-text-card__title` — main heading (Montserrat bold, tag-agnostic)
- `.simple-text-card__body` — description text (regular weight)

All text colors and sizes pull from `theme.css` design tokens via CSS custom properties (`--stc-bg`, `--stc-fg`, `--stc-eyebrow`).
