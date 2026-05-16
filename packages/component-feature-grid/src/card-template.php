<?php
/**
 * Card template partial for feature grid.
 *
 * Available in scope:
 *   $title (string)
 *   $desc  (string)
 *   $link  (array|null)
 *   $icon  (array|null)
 */

if ( ! defined( 'ABSPATH' ) ) { return; }
?>
<div class="bma-c-feature-grid__card">
    <?php if ( ! empty( $icon ) && ! empty( $icon['url'] ) ) : ?>
        <div class="bma-c-feature-grid__card-icon">
            <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ?? '' ); ?>" loading="lazy">
        </div>
    <?php endif; ?>

    <?php if ( $title !== '' ) : ?>
        <h3 class="bma-c-feature-grid__card-title"><?php echo esc_html( $title ); ?></h3>
    <?php endif; ?>

    <?php if ( $desc !== '' ) : ?>
        <p class="bma-c-feature-grid__card-desc"><?php echo esc_html( $desc ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $link ) && ! empty( $link['url'] ) ) : ?>
        <a href="<?php echo esc_url( $link['url'] ); ?>" class="bma-c-feature-grid__card-link" target="<?php echo esc_attr( $link['target'] ?? '_self' ); ?>">
            <?php echo esc_html( $link['title'] ?? __( 'Learn more', 'balefire-components' ) ); ?>
        </a>
    <?php endif; ?>
</div>
