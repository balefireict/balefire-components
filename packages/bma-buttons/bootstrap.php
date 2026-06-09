<?php
/**
 * Balefire/bma-buttons — bootstrap.
 *
 * Defines a thin global function wrapper, registers the [bma_buttons]
 * shortcode, and wires the WPBakery vc_map on vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Components\Buttons
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_buttons_render' ) ) {
	/**
	 * Render the [bma_buttons] shortcode. Programmatic equivalent of
	 * do_shortcode('[bma_buttons ...]') — returns the HTML string.
	 *
	 * @param array $atts Shortcode attributes (same as the shortcode).
	 * @return string HTML output, or '' when no buttons have label+url.
	 */
	function bma_buttons_render( array $atts ): string {
		return \Balefire\Components\Buttons\Buttons::render( $atts );
	}
}

if ( ! function_exists( 'bma_buttons_is_external_url' ) ) {
	/**
	 * True if $url points to a different host than the current site.
	 *
	 * @param string $url URL to test.
	 * @return bool
	 */
	function bma_buttons_is_external_url( string $url ): bool {
		return \Balefire\Components\Buttons\Buttons::isExternalUrl( $url );
	}
}

add_action(
	'plugins_loaded',
	function (): void {
		\Balefire\Components\Buttons\Buttons::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Components\Buttons\Buttons::class, 'vcMap' ) );
		}
	},
	20
);
