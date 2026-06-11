<?php
/**
 * Template partial for [bma_testimonial].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args (quote, attribution, role, company, image)
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\Testimonial\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-testimonial__inner">

        <?php if ( ! empty( $args['image'] ) ) : ?>
            <div class="bma-c-testimonial__media">
                <?php echo $args['image']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $args['quote'] ) ) : ?>
            <blockquote class="bma-c-testimonial__quote">
                <?php echo wp_kses_post( $args['quote'] ); ?>
            </blockquote>
        <?php endif; ?>

        <?php if ( ! empty( $args['attribution'] ) ) : ?>
            <cite class="bma-c-testimonial__cite">
                <span class="bma-c-testimonial__name"><?php echo esc_html( $args['attribution'] ); ?></span>
                <?php if ( ! empty( $args['role'] ) || ! empty( $args['company'] ) ) : ?>
                    <span class="bma-c-testimonial__meta">
                        <?php
                        $meta_parts = array_filter( [ $args['role'], $args['company'] ] );
                        echo esc_html( implode( ', ', $meta_parts ) );
                        ?>
                    </span>
                <?php endif; ?>
            </cite>
        <?php endif; ?>

    </div>
</section>
