<?php
/**
 * Template partial for [bma_testimonial].
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\Testimonial\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-testimonial__inner">

        <?php if ( ! empty( $args['image'] ) && is_array( $args['image'] ) ) : ?>
            <div class="bma-c-testimonial__media">
                <img src="<?php echo esc_url( $args['image']['url'] ); ?>" alt="<?php echo esc_attr( $args['image']['alt'] ?? '' ); ?>" loading="lazy">
            </div>
        <?php endif; ?>

        <blockquote class="bma-c-testimonial__quote">
            <?php echo wp_kses_post( $args['quote'] ); ?>
        </blockquote>

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
