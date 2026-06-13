<?php
/**
 * balefireict/component-cta-row — bootstrap.
 *
 * Registers the [bma_cta_row] shortcode and wires vc_map on vc_before_init.
 * Full-bleed background CTA: heading + copy left, button + phone right.
 * The consumer drops it in a WPBakery STRETCH row and hardcodes the
 * background image on .bma-cta-row in theme CSS — the component owns only
 * the gradient overlay.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\CtaRow
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_cta_row_shortcode' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_cta_row ...]').
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content WYSIWYG body.
	 * @return string HTML output.
	 */
	function bma_cta_row_shortcode( array $atts = array(), ?string $content = null ): string {
		return \Balefire\Component\CtaRow\CtaRow::render( $atts, $content );
	}
}

$bma_cta_row_boot = static function (): void {
	\Balefire\Component\CtaRow\CtaRow::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\CtaRow\CtaRow::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\CtaRow\CtaRow::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_cta_row_boot();
} else {
	add_action( 'plugins_loaded', $bma_cta_row_boot, 20 );
}
unset( $bma_cta_row_boot );
