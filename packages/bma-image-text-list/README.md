# balefire/bma-image-text-list

BMA Image Text List — parent/child WPBakery container of vertical image-left
text rows with optional links. The parent wraps the children; each child
renders a media + title + body row that becomes an `<a>` when `href` is set.

## Provides

- `[bma_image_text_list color="default|white"]…[/bma_image_text_list]`
- `[bma_image_text_item image="" title="" href="" new_tab=""]Body[/bma_image_text_item]`
- Global functions: `bma_image_text_list_render()`, `bma_image_text_item_render()`
- WPBakery `vc_map` for both parent (container) and child

## Source of truth

`Balefire\Components\ImageTextList\ImageTextList` (PSR-4). Exposes:
- `ImageTextList::render( $atts, $content )` — parent
- `ImageTextList::renderItem( $atts, $content )` — child
- `ImageTextList::register()` — add_shortcode for both
- `ImageTextList::vcMap()` — vc_map for both
- `ImageTextList::registerContainerClass()` — eval'd `WPBakeryShortCode_BMA_ImageTextList extends WPBakeryShortCodesContainer {}` (called on `vc_after_init`)

## CSS

`assets/image-text-list.css` ships the full BEM layout. Mobile (<640px)
stacks the media above the content.

## Dependencies

None. Uses WordPress `wp_get_attachment_image()`, `wp_kses_post()`,
`wpautop()`, `do_shortcode()`, `shortcode_atts()`.

## Consuming

```json
{
    "repositories": [
        { "type": "path", "url": "../balefire-components/packages/bma-image-text-list", "options": { "symlink": true } }
    ],
    "require": {
        "balefire/bma-image-text-list": "*"
    }
}
```

```css
@import "../../vendor/balefire/bma-image-text-list/assets/image-text-list.css";
```

The bootstrap is auto-loaded by Composer. The `WPBakeryShortCode_BMA_ImageTextList`
container class is registered on `vc_after_init` via `eval()` (WordPress's
documented pattern for parent shortcodes — the class must be a literal symbol
that `WPBakeryShortCodesContainer::findChildrenShortcodes()` can find by name).

## Ported from

`rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-image-text-list.php`
— same two shortcodes, same attributes, same inner-content cleanup rules.
Renamed `WPBakeryShortCode_BMA_Image_Text_List` → `WPBakeryShortCode_BMA_ImageTextList`
(removed underscores per PSR-4 namespace convention).
