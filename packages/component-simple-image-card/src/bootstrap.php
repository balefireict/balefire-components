<?php
/**
 * balefireict/component-simple-image-card — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox global
 * function names so existing themes keep working), registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\SimpleImageCard
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_simple_image_card_grid_shortcode' ) ) {
	/**
	 * Render the [bma_simple_image_card_grid] parent shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_simple_image_card_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\SimpleImageCard\SimpleImageCard::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_simple_image_card_shortcode' ) ) {
	/**
	 * Render the [bma_simple_image_card] child shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Body HTML.
	 * @return string HTML output.
	 */
	function bma_simple_image_card_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\SimpleImageCard\SimpleImageCard::renderItem( (array) $atts, $content );
	}
}

$bma_simple_image_card_boot = function (): void {
		\Balefire\Component\SimpleImageCard\SimpleImageCard::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Component\SimpleImageCard\SimpleImageCard::class, 'vcMap' ) );
			add_action( 'vc_after_init', array( \Balefire\Component\SimpleImageCard\SimpleImageCard::class, 'registerContainerClass' ) );
		}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_simple_image_card_boot();
} else {
	add_action( 'plugins_loaded', $bma_simple_image_card_boot, 20 );
}
unset( $bma_simple_image_card_boot );
