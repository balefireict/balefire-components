<?php
/**
 * balefireict/component-cta-bar — bootstrap.
 *
 * @package Balefire\Component\CtaBar
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_cta_bar_shortcode' ) ) {
	/**
	 * Render the [bma_cta_bar] shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content WYSIWYG subtext.
	 * @return string HTML output.
	 */
	function bma_cta_bar_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\CtaBar\CtaBar::render( (array) $atts, $content );
	}
}

$bma_cta_bar_boot = static function (): void {
	\Balefire\Component\CtaBar\CtaBar::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\CtaBar\CtaBar::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\CtaBar\CtaBar::class, 'registerPreviewClasses' ) );
	}
};

if ( did_action( 'plugins_loaded' ) ) {
	$bma_cta_bar_boot();
} else {
	add_action( 'plugins_loaded', $bma_cta_bar_boot, 20 );
}
unset( $bma_cta_bar_boot );
