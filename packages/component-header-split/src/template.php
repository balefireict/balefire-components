<?php
/**
 * Template partial for [bma_header_split].
 *
 * Available in scope:
 *   $atts         (array)  resolved shortcode attributes
 *   $wrapper_atts (array)  root <header> element attributes
 *   $logo_html    (string) prefiltered logo HTML
 *
 * The host theme owns all styling. This file ships stable DOM hooks only.
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\HeaderSplit\Renderer;
?>
<header <?php echo Renderer::attrs_to_html( $wrapper_atts ); // already escaped ?>>
    <div class="bma-c-header-split__inner">

        <div class="bma-c-header-split__logo">
            <?php echo $logo_html; // pre-filtered/sanitized by core get_custom_logo() ?>
        </div>

        <nav
            class="bma-c-header-split__nav-primary"
            id="nav-main-wrapper"
            role="navigation"
            aria-label="<?php esc_attr_e( 'Primary navigation', 'balefire-components' ); ?>">
            <?php echo Renderer::render_primary_nav( $atts ); ?>
        </nav>

        <div class="bma-c-header-split__utility">
            <nav
                class="bma-c-header-split__nav-secondary"
                id="nav-secondary-wrapper"
                role="navigation"
                aria-label="<?php esc_attr_e( 'Secondary navigation', 'balefire-components' ); ?>">
                <?php echo Renderer::render_secondary_nav( $atts ); ?>
            </nav>

            <?php echo Renderer::render_toggle(); ?>
        </div>

    </div>
</header>
