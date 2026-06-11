<?php
/**
 * balefireict/component-auto-grid — bootstrap.
 *
 * CSS-utility package: owns the shared `bma-auto-grid` grid classes. It
 * registers NO shortcode and NO vc_map, so there are no hooks here — only
 * the ABSPATH guard and a global wrapper function delegating to the helper
 * so other packages/themes can build the wrapper class string.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\AutoGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_auto_grid_classes' ) ) {
	/**
	 * Build the shared bma-auto-grid wrapper class string.
	 *
	 * @param int    $cols_desktop Desktop (lg) column count (1-6).
	 * @param int    $cols_mobile  Base/mobile column count (1-6). Default 1.
	 * @param string $gap          Gap token suffix (e.g. '6'). Default '6'.
	 * @return string Space-separated class string.
	 */
	function bma_auto_grid_classes( int $cols_desktop, int $cols_mobile = 1, string $gap = '6' ): string {
		return \Balefire\Component\AutoGrid\AutoGrid::gridClasses( $cols_desktop, $cols_mobile, $gap );
	}
}
