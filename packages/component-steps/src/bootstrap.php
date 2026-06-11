<?php
/**
 * balefireict/component-steps — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox names),
 * registers the parent + child + icon shortcodes, wires vc_map on
 * vc_before_init, and registers the WPBakeryShortCodesContainer subclass on
 * vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\Steps
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_steps_shortcode' ) ) {
	/**
	 * Render the [bma_steps] parent grid.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_steps_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\Steps\Steps::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_step_shortcode' ) ) {
	/**
	 * Render one [bma_step] child card.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML.
	 * @return string HTML output.
	 */
	function bma_step_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\Steps\Steps::renderStep( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_step_icon_shortcode' ) ) {
	/**
	 * Render the [bma_step_icon] inline SVG passthrough.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Raw SVG markup.
	 * @return string Sanitised SVG, or ''.
	 */
	function bma_step_icon_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\Steps\Steps::renderStepIcon( (array) $atts, $content );
	}
}

$bma_steps_boot = function (): void {
	\Balefire\Component\Steps\Steps::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\Steps\Steps::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\Steps\Steps::class, 'registerContainerClass' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_steps_boot();
} else {
	add_action( 'plugins_loaded', $bma_steps_boot, 20 );
}
unset( $bma_steps_boot );
