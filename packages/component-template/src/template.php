<?php
/**
 * Template partial for [bma_template].
 *
 * Available in scope:
 *   $atts          (array)   resolved shortcode attributes
 *   $root_classes  (string)  precomputed class list
 *   $root_attrs    (string)  precomputed root element attributes (class + id)
 *
 * Replace this with your component's HTML structure. Always escape output.
 */

if ( ! defined( 'ABSPATH' ) ) { return; }
?>
<section <?php echo $root_attrs; // already escaped in Renderer ?>>
    <div class="bma-c-template__inner">
        <p class="bma-c-template__placeholder">
            <?php \esc_html_e( 'Component template — replace with your markup.', 'balefire-components' ); ?>
        </p>
    </div>
</section>
