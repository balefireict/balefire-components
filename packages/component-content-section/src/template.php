<?php
/**
 * Template partial for [bma_content_section].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\ContentSection\Renderer;

$has_media = ! empty( $args['image'] ) && is_array( $args['image'] );
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-content-section__inner">

        <?php if ( $has_media && in_array( $args['layout'], [ 'image-top', 'image-left', 'image-right' ], true ) ) : ?>
            <figure class="bma-c-content-section__media">
                <img src="<?php echo esc_url( $args['image']['url'] ); ?>"
                     alt="<?php echo esc_attr( $args['image']['alt'] ?? '' ); ?>"
                     loading="lazy"
                     width="<?php echo esc_attr( $args['image']['width'] ?? '' ); ?>"
                     height="<?php echo esc_attr( $args['image']['height'] ?? '' ); ?>">
            </figure>
        <?php endif; ?>

        <div class="bma-c-content-section__text">
            <?php if ( ! empty( $args['headline'] ) ) : ?>
                <h2 class="bma-c-content-section__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h2>
            <?php endif; ?>

            <?php if ( ! empty( $args['body'] ) ) : ?>
                <div class="bma-c-content-section__body"><?php echo wp_kses_post( $args['body'] ); ?></div>
            <?php endif; ?>

            <?php if ( ! empty( $args['cta'] ) ) : ?>
                <div class="bma-c-content-section__actions">
                    <?php echo Renderer::render_cta( is_array( $args['cta'] ) ? $args['cta'] : null ); ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>
