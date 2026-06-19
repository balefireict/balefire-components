<?php
/**
 * balefireict/component-gallery-grid - bootstrap.
 *
 * Registers the [bma_gallery_grid] shortcode, the fslightbox script (enqueued
 * only when the shortcode renders), and the component ACF field-group JSON
 * path (guarded by BALEFIRE_COMPONENTS_LOAD_ACF_JSON).
 *
 * @package Balefire\Component\GalleryGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_gallery_grid_shortcode' ) ) {
	/**
	 * Render the [bma_gallery_grid] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_gallery_grid_shortcode( $atts ): string {
		return \Balefire\Component\GalleryGrid\GalleryGrid::render( (array) $atts );
	}
}

$bma_gallery_grid_boot = static function (): void {
	\Balefire\Component\GalleryGrid\GalleryGrid::register();

	// Register the fslightbox script so render() can enqueue it on demand.
	// in_footer = true: fslightbox scans <a data-fslightbox> at load time, so
	// it must run AFTER the gallery markup is in the DOM (footer).
	if ( ! wp_script_is( 'bma-fslightbox', 'registered' ) ) {
		wp_register_script(
			'bma-fslightbox',
			\Balefire\Component\GalleryGrid\GalleryGrid::fslightboxUrl(),
			array(),
			'3.7.5',
			true
		);
	}

	// Component ACF field-group JSON (soft: honours the opt-out constant).
	if ( ! \defined( 'BALEFIRE_COMPONENTS_LOAD_ACF_JSON' ) || \constant( 'BALEFIRE_COMPONENTS_LOAD_ACF_JSON' ) ) {
		add_filter(
			'acf/settings/load_json',
			static function ( array $paths ): array {
				$paths[] = dirname( __DIR__ ) . '/acf-json';
				return $paths;
			}
		);
	}

	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\GalleryGrid\GalleryGrid::class, 'vcMap' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_gallery_grid_boot();
} else {
	add_action( 'plugins_loaded', $bma_gallery_grid_boot, 20 );
}
unset( $bma_gallery_grid_boot );
