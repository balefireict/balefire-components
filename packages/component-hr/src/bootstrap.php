<?php
/**
 * balefireict/component-hr — bootstrap.
 *
 * Defines a thin global function wrapper (preserving the original rockerbox
 * name bma_hr_shortcode), registers the shortcode, and wires vc_map on
 * vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\Hr
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_hr_shortcode' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_hr]').
	 *
	 * Keeps the original rockerbox global function name so existing themes
	 * that call it directly keep working.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_hr_shortcode( $atts = array() ): string {
		return \Balefire\Component\Hr\Hr::render( (array) $atts );
	}
}

$bma_hr_boot = function (): void {
	\Balefire\Component\Hr\Hr::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\Hr\Hr::class, 'vcMap' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_hr_boot();
} else {
	add_action( 'plugins_loaded', $bma_hr_boot, 20 );
}
unset( $bma_hr_boot );
