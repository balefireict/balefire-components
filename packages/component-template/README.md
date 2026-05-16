# component-template

**Type:** `scaffolding` (change this when copying — one of `scaffolding`, `field-group`, `cpt-backed`)
**Shortcode:** `[bma_template]`
**CSS root:** `.bma-c-template`
**ACF group:** none (this is a scaffolding component)

## What this is

Copy this directory to bootstrap a new component:

```bash
cd packages
cp -r component-template component-<slug>
cd component-<slug>
```

Then in the new directory, run these find/replaces (every occurrence):

| Find          | Replace with          | Example for "hero"     |
|---------------|----------------------|------------------------|
| `template`    | `<slug>`             | `hero`                 |
| `Template`    | `<PascalSlug>`       | `Hero`                 |
| `TEMPLATE`    | `<UPPER_SLUG>`       | `HERO`                 |

Files to touch:

- `composer.json` — `name`, `description`, `psr-4` namespace, `extra.balefire-component.*`
- `src/bootstrap.php` — namespace, shortcode tag, CSS class
- `src/Renderer.php` — namespace, ACF field lookups (if `field-group` or `cpt-backed`)
- `src/template.php` — `.bma-c-<slug>` class on root
- `src/style.css` — `.bma-c-<slug>` selectors
- `src/bakery.php` — `vc_map` base name + params
- `acf-json/` — drop the field group JSON file here (export from WP admin)
- `README.md` — update type, shortcode, description

## File layout

```
component-<slug>/
├── composer.json
├── README.md
├── acf-json/                  ← ACF field group JSON (omit for scaffolding)
├── src/
│   ├── bootstrap.php          ← autoload-files entry, registers hooks
│   ├── Renderer.php           ← shortcode render callback
│   ├── template.php           ← HTML template (PHP partial)
│   ├── style.css              ← Vite entry, .bma-c-<slug> scoped
│   ├── script.js              ← optional, omit if no JS
│   └── bakery.php             ← vc_map registration
└── tests/                     ← optional PHPUnit / snapshot tests
```

## Lifecycle

1. **`bootstrap.php`** runs at composer autoload-files time (once, on every request).
   - Adds `acf-json/` to ACF's load paths.
   - Registers the shortcode on `init`.
   - Registers the Bakery element on `vc_before_init`.
   - Adds the CSS source path to the `balefire/component/css_manifest` filter so the host theme's Vite config discovers it.
2. **`Renderer::render($atts)`** runs when the shortcode is encountered:
   - Validates / defaults attributes.
   - For `field-group` or `cpt-backed` types, calls `get_field()` to load content.
   - Includes `template.php` with the resolved data.
3. **`template.php`** prints the HTML. Use `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` for output.
4. **`style.css`** is compiled by the host theme's Vite into `dist/components/<slug>-<hash>.css` and enqueued only when `has_shortcode($post->post_content, 'bma_<slug>')` is true.

## Type-specific notes

### `scaffolding`

- No `acf-json/` directory needed.
- `Renderer::render()` reads attributes from `$atts` only.
- Use for: containers, spacers, two-col wrappers, header/footer parts.

### `field-group`

- Drop the exported ACF group into `acf-json/group_bma_<slug>.json`.
- The group's "Location" rule attaches it to whatever post type/template is appropriate (usually `page` or specific page templates).
- One instance per page (because the ACF group is on the page).
- `Renderer::render()` calls `get_field('name')` with no post_id (uses current page).

### `cpt-backed`

- Drop the exported ACF group into `acf-json/group_bma_<slug>.json`, location = "Post Type is bma_block" (or a dedicated CPT).
- Shortcode signature: `[bma_<slug> id="123"]`.
- `Renderer::render()` calls `get_field('name', (int) $atts['id'])`.
- The host theme registers the `bma_block` CPT (or each component registers its own dedicated CPT in `bootstrap.php`).

## Universal attributes

Every component supports these regardless of type. Implement parsing in `Renderer.php`:

- `align`: `left | center | right`
- `variant`: component-defined values
- `container`: `default | wide | narrow | full`
- `spacing_top`, `spacing_bottom`: `none | sm | md | lg | xl`
- `id`: DOM anchor
- `class`: extra CSS class on the root element

## Tokens

Consume host-theme tokens (`var(--color-primary)`, `var(--font-1)`, etc.). Never define `--color-*` or `--font-*` inside a component. See `../../README.md` "Tokens contract" for the full list.

If you need component-local tuning tokens, scope them under your root class:

```css
.bma-c-template {
    --bma-c-template-bg: var(--color-light);
    --bma-c-template-pad: var(--spacing-section);
}
```
