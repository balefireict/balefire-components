<?php
/**
 * Template partial for [bma_accordion_faq].
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\AccordionFaq\Renderer;
?>
<section <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-accordion-faq__inner">

        <?php if ( ! empty( $args['headline'] ) ) : ?>
            <h2 class="bma-c-accordion-faq__headline"><?php echo wp_kses_post( $args['headline'] ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $args['subhead'] ) ) : ?>
            <p class="bma-c-accordion-faq__subhead"><?php echo esc_html( $args['subhead'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $args['items'] ) ) : ?>
            <div class="bma-c-accordion-faq__list">
                <?php foreach ( $args['items'] as $idx => $item ) : ?>
                    <?php if ( is_array( $item ) ) : ?>
                        <details class="bma-c-accordion-faq__item" <?php echo $idx === 0 ? 'open' : ''; ?>>
                            <summary class="bma-c-accordion-faq__question">
                                <?php echo esc_html( $item['question'] ?? '' ); ?>
                            </summary>
                            <div class="bma-c-accordion-faq__answer">
                                <?php echo wp_kses_post( $item['answer'] ?? '' ); ?>
                            </div>
                        </details>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
