# balefire-components: ACF → attribute-driven conversion

GOAL: No package reads ACF. Content comes from WPBakery element attributes,
mirroring the rockerbox reference shortcodes which are the source of truth:
  /Users/admin/Herd/rockerbox/wp-content/themes/balefire/inc/shortcodes/

## Two shapes

### Scalar components (no repeating items)
Reference: component-cta-banner (already converted, in this repo).
- Renderer::render($atts, $content): shortcode_atts defaults, no get_field.
- Rich body via $content -> wp_kses_post(do_shortcode(wpautop($content))).
- Scalar text -> textfield -> esc_html. Image -> attach_image -> wp_get_attachment_image_url.
- bakery.php: textfield/textarea_html/attach_image/dropdown params. content => 'content'.
- bootstrap.php: init add_shortcode + vc_before_init require bakery.php. NO acf/settings/load_json block.

### Repeating components (parent + child container)
Reference: rockerbox bma-faq.php and bma-logo-grid.php.
- Parent vc_map: php_class_name, as_parent => ['only' => 'CHILD'], content_element true,
  is_container true, js_view 'VcColumnView', show_settings_on_create true.
- Child vc_map: as_child => ['only' => 'PARENT'], content_element true.
- Parent shortcode: shortcode_unautop(trim($content)) -> do_shortcode -> strip stray <br> and empty <p>.
- Child rich text uses textarea_html ($content). Child image uses attach_image.
- Register container class on vc_after_init:
  class WPBakeryShortCode_Foo extends WPBakeryShortCodesContainer {}
  (guarded by class_exists checks).
- Bare parent with no children renders nothing (return '').

## Per-package cleanup (every package)
1. Rewrite Renderer.php (no get_field).
2. Rewrite bakery.php (attribute params).
3. Rewrite template.php where present (esc on atts not ACF).
4. bootstrap.php: drop the `acf/settings/load_json` block.
5. composer.json: extra.balefire-component.type "field-group" -> "scaffolding".
6. Delete the package acf-json/ directory.
7. CSS in src/style.css stays (no ACF coupling) — keep class names stable.

## Status
- [x] cta-banner (scalar) DONE + verified on davidtours.test
- [x] component-footer REMOVED (site-global data, not a section component)
- [x] hero REMOVED
- [x] content-section REMOVED
- [x] testimonial (scalar): quote(content), attribution, role, company, image(attach_image)
- [x] gravity-form-block REMOVED
- [x] accordion-faq (parent/child): [bma_accordion_faq title] + [bma_accordion_faq_item question] body
- [x] logo-card (parent/child): [bma_logo_card headline columns] + [bma_logo_card_item image]
- [x] feature-grid (parent/child): [bma_feature_grid eyebrow headline subhead columns] + [bma_feature_card icon(attach_image) title] body
- [x] stat-callout (parent/child): [bma_stat_callout columns] + [bma_stat value label]
- [x] header-split REMOVED

## Workflow
Local only. Browser-verify on davidtours.test. STOP before git push.
After all packages: cd /Users/admin/Herd/davidtours/wp-content && composer update <pkgs>,
then rm stale vendor symlinks if any, composer dump-autoload.
