<?php
/**
 * balefireict/component-simple-card — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox
 * shortcode-callback names), registers the parent + child shortcodes, wires
 * vc_map on vc_before_init, and registers the WPBakeryShortCodesContainer
 * subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\SimpleCard
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_simple_card_grid_shortcode' ) ) {
	/**
	 * Render the parent [bma_simple_card_grid] shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes (children).
	 * @return string HTML output.
	 */
	function bma_simple_card_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\SimpleCard\SimpleCard::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_simple_card_shortcode' ) ) {
	/**
	 * Render one [bma_simple_card] child shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML.
	 * @return string HTML output.
	 */
	function bma_simple_card_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\SimpleCard\SimpleCard::renderCard( (array) $atts, $content );
	}
}

$bma_simple_card_boot = function (): void {
	\Balefire\Component\SimpleCard\SimpleCard::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\SimpleCard\SimpleCard::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\SimpleCard\SimpleCard::class, 'registerContainerClass' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_simple_card_boot();
} else {
	add_action( 'plugins_loaded', $bma_simple_card_boot, 20 );
}
unset( $bma_simple_card_boot );
