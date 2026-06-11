<?php
/**
 * Card template partial for [bma_feature_card].
 *
 * Available in scope:
 *   $title    (string)  card title
 *   $icon_url (string)  resolved icon image URL
 *   $body     (string)  body HTML (already through wp_kses_post)
 *
 * @package Balefire\Component\FeatureGrid
 */

if ( ! defined( 'ABSPATH' ) ) { return; }
?>
<div class="bma-c-feature-grid__card">
    <?php if ( '' !== $icon_url ) : ?>
        <div class="bma-c-feature-grid__card-icon">
            <img src="<?php echo esc_url( $icon_url ); ?>" alt="" loading="lazy">
        </div>
    <?php endif; ?>

    <?php if ( '' !== $title ) : ?>
        <h3 class="bma-c-feature-grid__card-title"><?php echo esc_html( $title ); ?></h3>
    <?php endif; ?>

    <?php if ( '' !== $body ) : ?>
        <div class="bma-c-feature-grid__card-desc"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?></div>
    <?php endif; ?>
</div>
