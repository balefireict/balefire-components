<?php
/**
 * Template partial for [bma_cta_banner].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args (headline, body, cta)
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\CtaBanner\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-cta-banner__inner">

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h2 class="bma-c-cta-banner__headline"><?php echo esc_html( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $args['body'] ) ) : ?>
            <div class="bma-c-cta-banner__body"><?php echo wp_kses_post( $args['body'] ); ?></div>
        <?php endif; ?>

        <?php if ( ! empty( $args['cta'] ) ) : ?>
            <div class="bma-c-cta-banner__action">
                <?php echo $args['cta']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        <?php endif; ?>

    </div>
</section>
