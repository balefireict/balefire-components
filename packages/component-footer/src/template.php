<?php
/**
 * Template partial for [bma_footer].
 *
 * Available in scope:
 *   $wrapper_atts (array)  root <footer> element attributes
 *   $args         (array)  pre-resolved content args
 *
 * The host theme owns all styling. This file ships stable DOM hooks only.
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

use Balefire\Component\Footer\Renderer;
?>
<footer <?php echo Renderer::attrs_to_html( $wrapper_atts ); ?>>
    <div class="bma-c-footer__inner">

        <div class="bma-c-footer__logo">
            <?php echo $args['logo_html']; // pre-filtered/sanitized by core get_custom_logo() ?>
        </div>

        <?php if ( ! empty( $args['quote_link'] ) || ! empty( $args['phone'] ) ) : ?>
        <div class="bma-c-footer__cta-row">
            <?php if ( ! empty( $args['quote_link'] ) ) : ?>
                <div class="bma-c-footer__quote">
                    <a href="<?php echo esc_url( $args['quote_link'] ); ?>" class="btn" title="Get A Quote">
                        <?php esc_html_e( 'Get A Quote', 'balefire-components' ); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $args['phone'] ) ) : ?>
                <div class="bma-c-footer__phone">
                    <a href="tel:<?php echo esc_attr( $args['phone'] ); ?>" id="phone-number-footer" title="Call us at <?php echo esc_attr( $args['phone'] ); ?>">
                        <?php echo esc_html( $args['phone'] ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $args['address'] ) || ! empty( $args['city'] ) || ! empty( $args['state'] ) || ! empty( $args['zipcode'] ) ) : ?>
        <div class="bma-c-footer__address">
            <p>
                <?php
                echo esc_html( $args['site_name'] );
                if ( ! empty( $args['address'] ) ) {
                    echo ' &bull; ' . esc_html( $args['address'] );
                }
                if ( ! empty( $args['city'] ) || ! empty( $args['state'] ) || ! empty( $args['zipcode'] ) ) {
                    echo ', ';
                    $parts = array_filter( [ $args['city'], $args['state'] . ( ! empty( $args['zipcode'] ) ? ' ' . $args['zipcode'] : '' ) ] );
                    echo esc_html( implode( ', ', $parts ) );
                }
                ?>
            </p>
        </div>
        <?php endif; ?>

        <div class="bma-c-footer__nav">
            <?php echo Renderer::render_footer_nav( $args ); ?>
        </div>

        <?php if ( ! empty( $args['instagram'] ) || ! empty( $args['facebook'] ) ) : ?>
        <div class="bma-c-footer__social">
            <?php if ( ! empty( $args['instagram'] ) ) : ?>
                <a href="<?php echo esc_url( $args['instagram'] ); ?>" title="<?php esc_attr_e( 'Follow us on Instagram', 'balefire-components' ); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" aria-hidden="true">
                        <path d="M20,11.9c-4.5,0-8.1,3.6-8.1,8.1s3.6,8.1,8.1,8.1s8.1-3.6,8.1-8.1S24.5,11.9,20,11.9z M20,25.6c-3.1,0-5.6-2.5-5.6-5.6s2.5-5.6,5.6-5.6s5.6,2.5,5.6,5.6S23.1,25.6,20,25.6z M27.8,9.7c-0.6,0-1.1,0.2-1.5,0.6c-0.4,0.4-0.6,0.9-0.6,1.5c0,0.6,0.2,1.1,0.6,1.5c0.4,0.4,0.9,0.6,1.5,0.6c0.6,0,1.1-0.2,1.5-0.6c0.4-0.4,0.6-0.9,0.6-1.5c0-0.6-0.2-1.1-0.6-1.5C28.9,9.9,28.4,9.7,27.8,9.7z M20,0C14.5,0,13.8,0,11.7,0.1C9.7,0.2,8.3,0.5,7,1.1c-1.3,0.5-2.4,1.3-3.4,2.3C2.6,4.3,1.8,5.5,1.3,6.8C0.7,8.1,0.3,9.6,0.2,11.5C0,13.6,0,14.3,0,19.9c0,5.5,0,6.2,0.2,8.3c0.1,1.9,0.5,3.4,1.1,4.7c0.5,1.3,1.3,2.4,2.3,3.4c1,1,2.1,1.7,3.4,2.3c1.3,0.5,2.8,0.9,4.7,1.1c2.1,0.1,2.8,0.2,8.3,0.2c5.5,0,6.2,0,8.3-0.2c1.9-0.1,3.4-0.5,4.7-1.1c1.3-0.5,2.4-1.3,3.4-2.3c1-1,1.7-2.1,2.3-3.4c0.5-1.3,0.9-2.8,1.1-4.7c0.1-2.1,0.2-2.8,0.2-8.3c0-5.5,0-6.2-0.2-8.3c-0.1-1.9-0.5-3.4-1.1-4.7c-0.5-1.3-1.3-2.4-2.3-3.4c-1-1-2.1-1.7-3.4-2.3c-1.3-0.5-2.8-0.9-4.7-1.1C26.2,0,25.5,0,20,0z M20,36.1c-5.4,0-6.1,0-8.2-0.2c-1.7-0.1-2.7-0.4-3.3-0.6c-0.8-0.3-1.4-0.7-2-1.2c-0.5-0.5-1-1.1-1.2-2c-0.3-0.6-0.5-1.6-0.6-3.3C4.4,26.6,4.4,26,4.4,20.5c0-5.4,0-6.1,0.2-8.2c0.1-1.7,0.4-2.7,0.6-3.3c0.3-0.8,0.7-1.4,1.2-2c0.5-0.5,1.1-1,2-1.2c0.6-0.3,1.6-0.5,3.3-0.6c2.1-0.1,2.8-0.2,8.2-0.2c5.4,0,6.1,0,8.2,0.2c1.7,0.1,2.7,0.4,3.3,0.6c0.8,0.3,1.4,0.7,2,1.2c0.5,0.5,1,1.1,1.2,2c0.3,0.6,0.5,1.6,0.6,3.3c0.1,2.1,0.2,2.8,0.2,8.2c0,5.4,0,6.1-0.2,8.2c-0.1,1.7-0.4,2.7-0.6,3.3c-0.3,0.8-0.7,1.4-1.2,2c-0.5,0.5-1.1,1-2,1.2c-0.6,0.3-1.6,0.5-3.3,0.6C26.1,36,25.4,36.1,20,36.1z"/>
                    </svg>
                </a>
            <?php endif; ?>
            <?php if ( ! empty( $args['facebook'] ) ) : ?>
                <a href="<?php echo esc_url( $args['facebook'] ); ?>" title="<?php esc_attr_e( 'Follow us on Facebook', 'balefire-components' ); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" aria-hidden="true">
                        <path d="M33.9,2H6.1C3.8,2,2,3.8,2,6.1v27.8C2,36.2,3.8,38,6.1,38h15.1V24.7h-4.1v-4.8h4.1v-3.5c0-4.1,2.2-6.2,6.2-6.2c1.7,0,3.2,0.1,3.6,0.2v4.2h-2.5c-1.9,0-2.3,0.9-2.3,2.2v2.9h4.6l-0.6,4.8h-4v13.3h4.7c2.3,0,4.1-1.8,4.1-4.1V6.1C38,3.8,36.2,2,33.9,2z"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

    <div class="bma-c-footer__meta">
        <div class="bma-c-footer__meta-inner">
            <div class="bma-c-footer__copyright">
                &copy;<?php echo esc_html( $args['year'] ); ?> <?php echo esc_html( $args['site_name'] ); ?>. All rights reserved.
            </div>
            <div class="bma-c-footer__credit">
                <a href="https://balefireagency.com" target="_blank" title="Web design by Balefire" rel="noopener noreferrer">
                    <?php esc_html_e( 'Web Design by Balefire', 'balefire-components' ); ?>
                </a>
            </div>
        </div>
    </div>
</footer>
