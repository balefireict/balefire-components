<?php
/**
 * Template partial for [bma_gravity_form_block].
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\GravityFormBlock\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-gravity-form-block__inner">

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h2 class="bma-c-gravity-form-block__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $args['subhead'] ) ) : ?>
            <p class="bma-c-gravity-form-block__subhead"><?php echo esc_html( $args['subhead'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $args['form_id'] ) && function_exists( 'gravity_form' ) ) : ?>
            <div class="bma-c-gravity-form-block__form">
                <?php gravity_form( $args['form_id'], false, false, false, null, true ); ?>
            </div>
        <?php elseif ( ! empty( $args['form_id'] ) ) : ?>
            <div class="bma-c-gravity-form-block__form">
                <?php echo do_shortcode( '[gravityform id="' . esc_attr( $args['form_id'] ) . '" title="false" description="false" ajax="true"]' ); ?>
            </div>
        <?php endif; ?>

    </div>
</section>
