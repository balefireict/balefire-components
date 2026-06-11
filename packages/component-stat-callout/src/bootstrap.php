<?php
/**
 * balefireict/component-stat-callout — bootstrap.
 *
 * Defines thin global function wrappers, registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Attribute-driven: no ACF reads anywhere.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\StatCallout
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_stat_callout_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_stat_callout]…[/bma_stat_callout]').
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_stat_callout_render( array $atts = array(), string $content = '' ): string {
		return \Balefire\Component\StatCallout\StatCallout::render( $atts, $content );
	}
}

if ( ! function_exists( 'bma_stat_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_stat]').
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_stat_render( array $atts = array() ): string {
		return \Balefire\Component\StatCallout\StatCallout::renderStat( $atts );
	}
}

$bma_stat_callout_boot = function (): void {
	\Balefire\Component\StatCallout\StatCallout::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\StatCallout\StatCallout::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\StatCallout\StatCallout::class, 'registerContainerClass' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_stat_callout_boot();
} else {
	add_action( 'plugins_loaded', $bma_stat_callout_boot, 20 );
}
unset( $bma_stat_callout_boot );
