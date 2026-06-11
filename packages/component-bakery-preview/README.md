# component-bakery-preview

Shared WPBakery backend-editor preview infrastructure for BMA elements.
Instead of a bare grey element bar, elements registered through this
package show a thumbnail (resolved the same way `vc_single_image` does it:
`wpb_getImageBySize( [ 'attach_id' => $id, 'thumb_size' => 'thumbnail' ] )`)
plus the element's title and a 120-char content excerpt — quickly readable
for content management.

## No shortcode

This package registers nothing on its own. Other components opt in from
their `vc_after_init` registration step.

## Usage

Replace the plain eval'd container class with:

```php
// Container parent (extends WPBakeryShortCodesContainer):
\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
    'WPBakeryShortCode_BMA_Faq',
    array( 'title' => 'title' )
);

// Single element (extends WPBakeryShortCode):
\Balefire\Component\BakeryPreview\Preview::registerElementClass(
    'WPBakeryShortCode_BMA_Image',
    array( 'image' => 'id' )
);
```

Param map keys:

| Key     | Meaning |
|---------|---------|
| `image` | name of an `attach_image` param to thumbnail |
| `title` | name of a text param shown bold |
| `text`  | name of a param excerpted to 120 chars; use `'content'` for the `textarea_html` body |

Both methods are class_exists-guarded and safe to call when WPBakery is
absent (no-op). The generated class overrides `outputTitle()` to append the
preview and captures `$content` in `contentAdmin()` so `textarea_html`
bodies can be excerpted.

Consumers should use this as a soft dep: components guard with
`class_exists( '\Balefire\Component\BakeryPreview\Preview' )` and fall back
to the plain eval'd class when the package isn't installed.

## CSS

A tiny admin stylesheet is printed inline once via `admin_print_styles`
(`.bma-vc-preview*` classes). No front-end CSS — `owns_css` is false.
