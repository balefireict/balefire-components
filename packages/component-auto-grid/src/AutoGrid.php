<?php
/**
 * balefireict/component-auto-grid — AutoGrid helper.
 *
 * Owns the shared `bma-auto-grid` responsive grid CSS used by several
 * rockerbox card shortcodes (simple-card, steps, card-stat,
 * team-member-centered, simple-image-card). This package registers NO
 * shortcode and NO vc_map — it only ships the grid utility CSS plus a
 * small helper for building the wrapper class string.
 *
 * Markup pattern reproduced (Tailwind utilities removed, semantics kept):
 *   <div class="bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 lg:auto-grid-cols-3">
 *
 * @package Balefire\Component\AutoGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\AutoGrid;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the shared auto-grid wrapper class strings.
 */
final class AutoGrid {

	/**
	 * Build the wrapper class string for a bma-auto-grid container.
	 *
	 * Returns the base grid class plus a mobile base column count and a
	 * desktop (lg) column count — matching the class strings the rockerbox
	 * card shortcodes emit, e.g.:
	 *   bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 lg:auto-grid-cols-3
	 *
	 * @param int    $cols_desktop Desktop (lg, >=960px) column count (1-6).
	 * @param int    $cols_mobile  Base/mobile column count (1-6). Default 1.
	 * @param string $gap          Gap token suffix (e.g. '5', '6', '12'). Default '6'.
	 * @return string Space-separated class string.
	 */
	public static function gridClasses( int $cols_desktop, int $cols_mobile = 1, string $gap = '6' ): string {
		$cols_desktop = max( 1, min( 6, $cols_desktop ) );
		$cols_mobile  = max( 1, min( 6, $cols_mobile ) );
		$gap          = trim( $gap );

		$classes = array( 'bma-auto-grid' );

		if ( '' !== $gap ) {
			$classes[] = 'auto-grid-gap-' . $gap;
		}

		$classes[] = 'auto-grid-cols-' . $cols_mobile;

		if ( $cols_desktop !== $cols_mobile ) {
			$classes[] = 'lg:auto-grid-cols-' . $cols_desktop;
		}

		return implode( ' ', $classes );
	}
}
