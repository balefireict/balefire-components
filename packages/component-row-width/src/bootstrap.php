<?php
/**
 * Bootstrap for balefireict/component-row-width.
 *
 * @package Balefire\Component\RowWidth
 */

defined( 'ABSPATH' ) || exit;

$bma_row_width_boot = static function (): void {
	\Balefire\Component\RowWidth\RowWidth::register();
};

if ( did_action( 'plugins_loaded' ) ) {
	$bma_row_width_boot();
} else {
	add_action( 'plugins_loaded', $bma_row_width_boot, 20 );
}
unset( $bma_row_width_boot );
