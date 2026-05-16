<?php
/**
 * Template partial for [bma_cta_banner].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\CtaBanner\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-cta-banner__inner">

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h2 class="bma-c-cta-banner__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $args['subhead'] ) ) : ?>
            <p class="bma-c-cta-banner__subhead"><?php echo esc_html( $args['subhead'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $args['cta'] ) ) : ?>
            <div class="bma-c-cta-banner__action">
                <?php echo Renderer::render_cta( is_array( $args['cta'] ) ? $args['cta'] : null ); ?>
            </div>
        <?php endif; ?>

    </div>
</section>
