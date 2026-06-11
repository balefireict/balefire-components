<?php
/**
 * balefireict/component-footer-cta — bootstrap.
 *
 * Defines a thin global function wrapper (keeping the original rockerbox
 * shortcode callback name so existing themes keep working), registers the
 * [bma_footer_cta] shortcode, and wires vc_map on vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\FooterCta
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_footer_cta_shortcode' ) ) {
	/**
	 * Render the centered footer CTA content block.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed content from WPBakery textarea_html.
	 * @return string HTML output.
	 */
	function bma_footer_cta_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\FooterCta\FooterCta::render(
			is_array( $atts ) ? $atts : array(),
			$content
		);
	}
}

$bma_footer_cta_boot = function (): void {
	\Balefire\Component\FooterCta\FooterCta::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\FooterCta\FooterCta::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\FooterCta\FooterCta::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_footer_cta_boot();
} else {
	add_action( 'plugins_loaded', $bma_footer_cta_boot, 20 );
}
unset( $bma_footer_cta_boot );
