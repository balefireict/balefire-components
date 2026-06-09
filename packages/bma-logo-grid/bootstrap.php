<?php
/**
 * Balefire/bma-logo-grid — bootstrap.
 *
 * Defines thin global function wrappers, registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Components\LogoGrid
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
		return \Balefire\Components\LogoGrid\LogoGrid::render( $atts, $content );
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
		return \Balefire\Components\LogoGrid\LogoGrid::renderItem( $atts );
	}
}

add_action(
	'plugins_loaded',
	function (): void {
		\Balefire\Components\LogoGrid\LogoGrid::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Components\LogoGrid\LogoGrid::class, 'vcMap' ) );
			add_action( 'vc_after_init', array( \Balefire\Components\LogoGrid\LogoGrid::class, 'registerContainerClass' ) );
		}
	},
	20
);
