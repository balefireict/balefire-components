<?php
/**
 * balefireict/component-arrow — bootstrap.
 *
 * Defines thin global function wrappers around the PSR-4 Arrow class
 * so other packages (and legacy theme code) can call helpers by name.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\Arrow
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_arrow_svg' ) ) {
	/**
	 * Shared arrow SVG used by media cards / arrow links.
	 *
	 * @return string SVG markup.
	 */
	function bma_arrow_svg(): string {
		return \Balefire\Component\Arrow\Arrow::arrowSvg();
	}
}

if ( ! function_exists( 'bma_safe_svg' ) ) {
	/**
	 * Sanitize an SVG string with a strict allowlist of tags and attributes.
	 *
	 * @param string $svg Raw SVG markup.
	 * @return string Sanitized SVG markup, or '' for empty input.
	 */
	function bma_safe_svg( string $svg ): string {
		return \Balefire\Component\Arrow\Arrow::safeSvg( $svg );
	}
}
