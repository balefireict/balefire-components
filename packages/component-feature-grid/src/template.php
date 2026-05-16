<?php
/**
 * Template partial for [bma_feature_grid].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\FeatureGrid\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-feature-grid__inner">

        <?php if ( ! empty( $args['eyebrow'] ) ) : ?>
            <span class="bma-c-feature-grid__eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
        <?php endif; ?>

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h2 class="bma-c-feature-grid__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $args['subhead'] ) ) : ?>
            <p class="bma-c-feature-grid__subhead"><?php echo esc_html( $args['subhead'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $args['cards'] ) ) : ?>
            <div class="bma-c-feature-grid__list bma-c-feature-grid__list--<?php echo esc_attr( $args['columns'] ); ?>">
                <?php foreach ( $args['cards'] as $card ) : ?>
                    <?php if ( is_array( $card ) ) : ?>
                        <?php echo Renderer::render_card( $card ); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
