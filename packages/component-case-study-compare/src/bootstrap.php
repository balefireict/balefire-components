<?php
/**
 * balefireict/component-case-study-compare — bootstrap.
 *
 * Defines a thin global function wrapper (keeps the original rockerbox
 * bma_compare_shortcode name so existing themes keep working), registers the
 * two compare shortcodes, and wires vc_map on vc_before_init.
 *
 * Self-closing element (no enclosed content) — no WPBakeryShortCodesContainer.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\CaseStudyCompare
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_compare_shortcode' ) ) {
	/**
	 * Original rockerbox global wrapper. Delegates to the component class.
	 *
	 * @param mixed       $atts      Shortcode attributes.
	 * @param string|null $content   Unused (self-closing element).
	 * @param string      $shortcode Shortcode tag.
	 * @return string HTML output.
	 */
	function bma_compare_shortcode( $atts, $content = null, string $shortcode = 'bma_compare' ): string {
		return \Balefire\Component\CaseStudyCompare\CaseStudyCompare::render( $atts, $content, $shortcode );
	}
}

$bma_case_study_compare_boot = function (): void {
	\Balefire\Component\CaseStudyCompare\CaseStudyCompare::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\CaseStudyCompare\CaseStudyCompare::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\CaseStudyCompare\CaseStudyCompare::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_case_study_compare_boot();
} else {
	add_action( 'plugins_loaded', $bma_case_study_compare_boot, 20 );
}
unset( $bma_case_study_compare_boot );
