<?php
/**
 * balefireict/component-image — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox
 * global names so existing themes keep working), registers the [bma_image]
 * shortcode, and wires vc_map on vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\Image
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_image_shortcode' ) ) {
	/**
	 * Render the [bma_image] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_image_shortcode( array $atts ): string {
		return \Balefire\Component\Image\Image::render( $atts );
	}
}

if ( ! function_exists( 'bma_image_fit_class' ) ) {
	/**
	 * Validate an object-fit value.
	 *
	 * @param string $value Raw fit value.
	 * @return string Validated value.
	 */
	function bma_image_fit_class( string $value ): string {
		return \Balefire\Component\Image\Image::fitClass( $value );
	}
}

if ( ! function_exists( 'bma_image_crop_class' ) ) {
	/**
	 * Validate an object-position (crop) value.
	 *
	 * @param string $value Raw crop value.
	 * @return string Validated value.
	 */
	function bma_image_crop_class( string $value ): string {
		return \Balefire\Component\Image\Image::cropClass( $value );
	}
}

if ( ! function_exists( 'bma_image_aspect_class' ) ) {
	/**
	 * Validate an aspect-ratio value.
	 *
	 * @param string $value Raw aspect value.
	 * @return string Validated value.
	 */
	function bma_image_aspect_class( string $value ): string {
		return \Balefire\Component\Image\Image::aspectClass( $value );
	}
}

$bma_image_boot = function (): void {
	\Balefire\Component\Image\Image::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\Image\Image::class, 'vcMap' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_image_boot();
} else {
	add_action( 'plugins_loaded', $bma_image_boot, 20 );
}
unset( $bma_image_boot );
