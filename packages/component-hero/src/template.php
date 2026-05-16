<?php
/**
 * Template partial for [bma_hero].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root element attributes
 *   $args         (array)  resolved content args
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\Hero\Renderer;

$style = '';
if ( ! empty( $args['bg_image'] ) && is_array( $args['bg_image'] ) ) {
    $style = sprintf( 'background-image:url(%s);', esc_url( $args['bg_image']['url'] ) );
}
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?><?php if ( $style ) { echo ' style="' . esc_attr( $style ) . '"'; } ?>>
    <div class="bma-c-hero__inner">

        <?php if ( ! empty( $args['eyebrow'] ) ) : ?>
            <span class="bma-c-hero__eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
        <?php endif; ?>

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h1 class="bma-c-hero__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h1>
        <?php endif; ?>

        <?php if ( ! empty( $args['subhead'] ) ) : ?>
            <p class="bma-c-hero__subhead"><?php echo esc_html( $args['subhead'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $args['primary_cta'] ) || ! empty( $args['secondary_cta'] ) ) : ?>
            <div class="bma-c-hero__actions">
                <?php if ( ! empty( $args['primary_cta'] ) ) : ?>
                    <?php echo Renderer::render_cta( is_array( $args['primary_cta'] ) ? $args['primary_cta'] : null ); ?>
                <?php endif; ?>
                <?php if ( ! empty( $args['secondary_cta'] ) ) : ?>
                    <?php echo Renderer::render_cta( is_array( $args['secondary_cta'] ) ? $args['secondary_cta'] : null ); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
