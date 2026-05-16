# balefire-components

Reusable section-level components for Balefire Marketing + Advertising classic
WordPress themes. Each subpackage under `packages/` ships PHP + ACF JSON + CSS
source and gets composed into a consumer theme alongside Vite + WPBakery +
Advanced Custom Fields PRO.

## Namespace conventions

| Concept            | Convention                              | Example                              |
|--------------------|------------------------------------------|--------------------------------------|
| Package name       | `balefireict/component-<slug>`           | `balefireict/component-hero`         |
| PHP namespace      | `Balefire\\Component\\<PascalSlug>`      | `Balefire\\Component\\Hero`          |
| CSS root class     | `.bma-c-<slug>`                          | `.bma-c-hero`                        |
| Shortcode tag      | `bma_<slug>`                             | `[bma_hero]`                         |
| ACF group key      | `group_bma_<slug>`                       | `group_bma_hero`                     |
| ACF field prefix   | `field_bma_<slug>_<name>`                | `field_bma_hero_headline`            |
| Bakery element     | `bma_<slug>` (matches shortcode tag)     | —                                    |
| CPT slug (if any)  | `bma_block` (shared) or `bma_<slug>`     | `bma_block`, `bma_testimonial`       |

> `bma_` / `bma-` chosen for namespace clarity (Balefire Marketing + Advertising).
> The `bf_*` prefix is reserved for any future framework-level helpers; component
> consumers always interact with `bma_*`.

## Component types

Each component declares ONE of three content models in its `README.md`:

| Type            | When to use                                                   | Backing store                    |
|-----------------|---------------------------------------------------------------|----------------------------------|
| `scaffolding`   | No editable content — pure markup/CSS with Bakery attrs only  | none                             |
| `field-group`   | One instance per page; content authored on the page itself    | ACF group attached to page post  |
| `cpt-backed`    | Reusable, multi-instance — referenced by `[bma_<slug> id="N"]` | `bma_block` CPT or dedicated CPT |

All components support universal Bakery attributes:

- `align` (left | center | right)
- `variant` (light | dark | gradient | brand — component-specific values allowed)
- `container` (default | wide | narrow | full)
- `spacing_top` / `spacing_bottom` (none | sm | md | lg | xl)
- `id` (DOM anchor)
- `class` (extra CSS class on root)

## Tokens contract

Components **consume** these tokens from the host theme; they MUST NOT redefine them:

- Colors: `--color-primary`, `--color-secondary`, `--color-light`, `--color-dark`, plus numbered palette `--color-1`...`--color-19` and their `-dark` variants
- Type: `--font-1`, `--text-xs`...`--text-6xl`
- Spacing: `--space-3xs`...`--space-3xl`, `--spacing-section`, `--spacing-gutter`
- Containers: `--container-content`, `--container-site`, `--container-narrow`, `--container-medium`, `--container-large`
- Other: `--radius-card`, `--radius-ui`, `--shadow-card`, `--tracking-label`

Components MAY define local override tokens scoped under their root class:

```css
.bma-c-hero {
    --bma-c-hero-bg: var(--color-light);
    --bma-c-hero-min-h: 60vh;
}
```

## Consuming this library

In a consumer theme's repo root `composer.json`:

```json
{
    "repositories": [
        { "type": "vcs", "url": "git@github.com:balefireict/balefire-components.git" }
    ],
    "require": {
        "balefireict/component-hero": "^1.0",
        "balefireict/component-simple-card": "^1.0"
    },
    "config": {
        "vendor-dir": "wp-content/vendor"
    }
}
```

Composer 2 reads each subpackage's `composer.json` from the VCS repo and installs them under `wp-content/vendor/balefireict/`.

## Repo layout

```
balefire-components/
├── composer.json           ← metapackage manifest, path repo for local dev
├── README.md               ← you are here
├── packages/
│   ├── component-template/ ← copy this to scaffold a new component
│   ├── component-hero/
│   ├── component-simple-card/
│   └── ...
└── docs/
    ├── component-spec.md
    ├── tokens-contract.md
    └── consumer-theme-integration.md
```

## Versioning

- Mono-repo tags `v1.0.0`, `v1.1.0`, etc. apply to all subpackages.
- Consumers pin with `^1.0` and get every subpackage's improvements together.
- If a subpackage needs to diverge fast, split it to its own repo (semver-preserving).

## Scaffolding a new component

```bash
cp -r packages/component-template packages/component-<slug>
# edit composer.json, src/*, acf-json/* — replace TEMPLATE with the slug
```

See `packages/component-template/README.md` for the full template walkthrough.

## License

MIT — public, but optimized for Balefire Marketing + Advertising client builds.
