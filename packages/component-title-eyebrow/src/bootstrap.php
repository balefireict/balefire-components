<?php
/**
 * balefireict/component-title-eyebrow — bootstrap.
 *
 * Defines a thin global function wrapper that keeps the original rockerbox
 * shortcode-callback name working, registers the shortcode, and wires vc_map
 * on vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\TitleEyebrow
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_title_eyebrow_shortcode' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_title_eyebrow ...]').
	 *
	 * Keeps the original rockerbox global callback name so existing themes
	 * referencing it keep working.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_title_eyebrow_shortcode( array $atts = array() ): string {
		return \Balefire\Component\TitleEyebrow\TitleEyebrow::render( $atts );
	}
}

$bma_title_eyebrow_boot = function (): void {
	\Balefire\Component\TitleEyebrow\TitleEyebrow::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\TitleEyebrow\TitleEyebrow::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\TitleEyebrow\TitleEyebrow::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_title_eyebrow_boot();
} else {
	add_action( 'plugins_loaded', $bma_title_eyebrow_boot, 20 );
}
unset( $bma_title_eyebrow_boot );
