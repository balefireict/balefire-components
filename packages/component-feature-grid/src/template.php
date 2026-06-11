<?php
/**
 * Template partial for [bma_feature_grid].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args (eyebrow, headline, subhead,
 *                          columns, inner)
 *
 * @package Balefire\Component\FeatureGrid
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\FeatureGrid\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-feature-grid__inner">

        <?php if ( '' !== $args['eyebrow'] ) : ?>
            <span class="bma-c-feature-grid__eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
        <?php endif; ?>

        <?php if ( '' !== $args['headline'] ) : ?>
            <h2 class="bma-c-feature-grid__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( '' !== $args['subhead'] ) : ?>
            <p class="bma-c-feature-grid__subhead"><?php echo esc_html( $args['subhead'] ); ?></p>
        <?php endif; ?>

        <div class="bma-c-feature-grid__list bma-c-feature-grid__list--<?php echo esc_attr( $args['columns'] ); ?>">
            <?php
            // $args['inner'] is the rendered child shortcode markup; child
            // output is already escaped via esc_html / esc_url / wp_kses_post.
            echo $args['inner']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </div>

    </div>
</section>
