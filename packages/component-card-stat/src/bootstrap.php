<?php
/**
 * balefireict/component-card-stat — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox
 * function names), registers the parent grid + child card + icon shortcodes,
 * wires vc_map on vc_before_init, and registers the backend-editor
 * preview classes (or the plain WPBakeryShortCodesContainer fallback)
 * on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\CardStat
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_card_stat_grid_shortcode' ) ) {
	/**
	 * Render the [bma_card_stat_grid] parent container.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner child shortcodes.
	 * @return string HTML output.
	 */
	function bma_card_stat_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\CardStat\CardStat::renderGrid( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_card_stat_shortcode' ) ) {
	/**
	 * Render one [bma_card_stat] child card.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed content.
	 * @return string HTML output.
	 */
	function bma_card_stat_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\CardStat\CardStat::renderCard( (array) $atts, $content );
	}
}

$bma_card_stat_boot = function (): void {
	\Balefire\Component\CardStat\CardStat::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\CardStat\CardStat::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\CardStat\CardStat::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_card_stat_boot();
} else {
	add_action( 'plugins_loaded', $bma_card_stat_boot, 20 );
}
unset( $bma_card_stat_boot );
