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

$bma_buttons_boot = function (): void {
		\Balefire\Components\Buttons\Buttons::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Components\Buttons\Buttons::class, 'vcMap' ) );
		}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_buttons_boot();
} else {
	add_action( 'plugins_loaded', $bma_buttons_boot, 20 );
}
unset( $bma_buttons_boot );
