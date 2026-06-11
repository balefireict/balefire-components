<?php
/**
 * balefireict/component-simple-img-title-column-grid — bootstrap.
 *
 * Registers the parent + child shortcodes and their WPBakery elements.
 *
 * @package Balefire\Component\SimpleImgTitleColumnGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_simple_img_title_column_grid_shortcode' ) ) {
	/**
	 * Render the [bma_simple_img_title_column_grid] parent shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child shortcodes.
	 * @return string HTML output.
	 */
	function bma_simple_img_title_column_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\SimpleImgTitleColumnGrid\SimpleImgTitleColumnGrid::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_simple_img_title_column_grid_item_shortcode' ) ) {
	/**
	 * Render one [bma_simple_img_title_column_grid_item] child shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Optional body content (unused by default).
	 * @return string HTML output.
	 */
	function bma_simple_img_title_column_grid_item_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\SimpleImgTitleColumnGrid\SimpleImgTitleColumnGrid::renderItem( (array) $atts, $content );
	}
}

$bma_simple_img_title_column_grid_boot = static function (): void {
	\Balefire\Component\SimpleImgTitleColumnGrid\SimpleImgTitleColumnGrid::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\SimpleImgTitleColumnGrid\SimpleImgTitleColumnGrid::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\SimpleImgTitleColumnGrid\SimpleImgTitleColumnGrid::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_simple_img_title_column_grid_boot();
} else {
	add_action( 'plugins_loaded', $bma_simple_img_title_column_grid_boot, 20 );
}
unset( $bma_simple_img_title_column_grid_boot );
