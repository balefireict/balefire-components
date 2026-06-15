<?php
/**
 * balefireict/component-simple-text-card — bootstrap.
 *
 * @package Balefire\Component\SimpleTextCard
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_simple_text_card_shortcode' ) ) {
	/**
	 * Programmatic wrapper for do_shortcode('[bma_simple_text_card ...]').
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML.
	 * @return string HTML output.
	 */
	function bma_simple_text_card_shortcode( array $atts = array(), ?string $content = null ): string {
		return \Balefire\Component\SimpleTextCard\SimpleTextCard::render( $atts, $content );
	}
}

$bma_simple_text_card_boot = function (): void {
	\Balefire\Component\SimpleTextCard\SimpleTextCard::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\SimpleTextCard\SimpleTextCard::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\SimpleTextCard\SimpleTextCard::class, 'registerPreviewClasses' ) );
	}
};

if ( did_action( 'plugins_loaded' ) ) {
	$bma_simple_text_card_boot();
} else {
	add_action( 'plugins_loaded', $bma_simple_text_card_boot, 20 );
}
unset( $bma_simple_text_card_boot );
