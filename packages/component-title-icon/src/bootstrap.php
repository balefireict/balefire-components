<?php
/**
 * balefireict/component-title-icon — bootstrap.
 *
 * @package Balefire\Component\TitleIcon
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_title_icon_shortcode' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_title_icon ...]').
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_title_icon_shortcode( array $atts = array() ): string {
		return \Balefire\Component\TitleIcon\TitleIcon::render( $atts );
	}
}

$bma_title_icon_boot = function (): void {
	\Balefire\Component\TitleIcon\TitleIcon::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\TitleIcon\TitleIcon::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\TitleIcon\TitleIcon::class, 'registerPreviewClasses' ) );
	}
};

if ( did_action( 'plugins_loaded' ) ) {
	$bma_title_icon_boot();
} else {
	add_action( 'plugins_loaded', $bma_title_icon_boot, 20 );
}
unset( $bma_title_icon_boot );
