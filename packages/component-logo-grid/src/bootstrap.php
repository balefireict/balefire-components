<?php
/**
 * balefireict/component-logo-grid — bootstrap.
 *
 * Defines thin global function wrappers, registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\LogoGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_logo_grid_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_logo_grid]Items[/bma_logo_grid]').
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_logo_grid_render( array $atts = array(), string $content = '' ): string {
		return \Balefire\Component\LogoGrid\LogoGrid::render( $atts, $content );
	}
}

if ( ! function_exists( 'bma_logo_grid_item_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_logo_grid_item]') with image attr.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_logo_grid_item_render( array $atts = array() ): string {
		return \Balefire\Component\LogoGrid\LogoGrid::renderItem( $atts );
	}
}

$bma_logo_grid_boot = function (): void {
		\Balefire\Component\LogoGrid\LogoGrid::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Component\LogoGrid\LogoGrid::class, 'vcMap' ) );
			add_action( 'vc_after_init', array( \Balefire\Component\LogoGrid\LogoGrid::class, 'registerContainerClass' ) );
		}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_logo_grid_boot();
} else {
	add_action( 'plugins_loaded', $bma_logo_grid_boot, 20 );
}
unset( $bma_logo_grid_boot );
