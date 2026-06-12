<?php
/**
 * balefireict/component-fancy-hover-grid — bootstrap.
 *
 * Registers the parent + child shortcodes and their WPBakery elements.
 *
 * @package Balefire\Component\FancyHoverGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_fancy_hover_grid_shortcode' ) ) {
	/**
	 * Render the [bma_fancy_hover_grid] parent shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child shortcodes.
	 * @return string HTML output.
	 */
	function bma_fancy_hover_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\FancyHoverGrid\FancyHoverGrid::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_fancy_hover_grid_item_shortcode' ) ) {
	/**
	 * Render one [bma_fancy_hover_grid_item] child shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Optional body content (unused by default).
	 * @return string HTML output.
	 */
	function bma_fancy_hover_grid_item_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\FancyHoverGrid\FancyHoverGrid::renderItem( (array) $atts, $content );
	}
}

$bma_fancy_hover_grid_boot = static function (): void {
	\Balefire\Component\FancyHoverGrid\FancyHoverGrid::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\FancyHoverGrid\FancyHoverGrid::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\FancyHoverGrid\FancyHoverGrid::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_fancy_hover_grid_boot();
} else {
	add_action( 'plugins_loaded', $bma_fancy_hover_grid_boot, 20 );
}
unset( $bma_fancy_hover_grid_boot );
