<?php
/**
 * Stat item template partial for stat callout.
 *
 * Available in scope:
 *   $value (string)
 *   $label (string)
 */

if ( ! defined( 'ABSPATH' ) ) { return; }
?>
<div class="bma-c-stat-callout__stat">
    <?php if ( $value !== '' ) : ?>
        <span class="bma-c-stat-callout__value"><?php echo esc_html( $value ); ?></span>
    <?php endif; ?>
    <?php if ( $label !== '' ) : ?>
        <span class="bma-c-stat-callout__label"><?php echo esc_html( $label ); ?></span>
    <?php endif; ?>
</div>
