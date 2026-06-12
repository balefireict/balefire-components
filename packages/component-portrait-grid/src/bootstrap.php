<?php
/**
 * balefireict/component-portrait-grid — bootstrap.
 *
 * Registers the parent + child shortcodes and their WPBakery elements.
 *
 * @package Balefire\Component\PortraitGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_portrait_grid_shortcode' ) ) {
	/**
	 * Render the [bma_portrait_grid] parent shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child shortcodes.
	 * @return string HTML output.
	 */
	function bma_portrait_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\PortraitGrid\PortraitGrid::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_portrait_grid_item_shortcode' ) ) {
	/**
	 * Render one [bma_portrait_grid_item] child shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Optional body content (unused by default).
	 * @return string HTML output.
	 */
	function bma_portrait_grid_item_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\PortraitGrid\PortraitGrid::renderItem( (array) $atts, $content );
	}
}

$bma_portrait_grid_boot = static function (): void {
	\Balefire\Component\PortraitGrid\PortraitGrid::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\PortraitGrid\PortraitGrid::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\PortraitGrid\PortraitGrid::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_portrait_grid_boot();
} else {
	add_action( 'plugins_loaded', $bma_portrait_grid_boot, 20 );
}
unset( $bma_portrait_grid_boot );
