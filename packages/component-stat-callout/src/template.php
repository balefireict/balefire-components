<?php
/**
 * Template partial for [bma_stat_callout].
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\StatCallout\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-stat-callout__inner">

        <?php if ( ! empty( $args['stats'] ) ) : ?>
            <div class="bma-c-stat-callout__list bma-c-stat-callout__list--<?php echo esc_attr( $args['columns'] ); ?>">
                <?php foreach ( $args['stats'] as $stat ) : ?>
                    <?php if ( is_array( $stat ) ) : ?>
                        <?php echo Renderer::render_stat( $stat ); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
