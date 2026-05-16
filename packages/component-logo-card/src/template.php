<?php
/**
 * Template partial for [bma_logo_card].
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\LogoCard\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-logo-card__inner">

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h2 class="bma-c-logo-card__headline"><?php echo esc_html( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $args['logos'] ) ) : ?>
            <div class="bma-c-logo-card__list bma-c-logo-card__list--<?php echo esc_attr( $args['columns'] ); ?>">
                <?php foreach ( $args['logos'] as $logo ) : ?>
                    <?php if ( is_array( $logo ) ) : ?>
                        <?php echo Renderer::render_logo( $logo ); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
