<?php
/**
 * balefireict/component-card-icon-top — bootstrap.
 *
 * Registers the parent + child shortcodes and their WPBakery elements.
 *
 * @package Balefire\Component\CardIconTop
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_card_icon_top_shortcode' ) ) {
	/**
	 * Render the [bma_card_icon_top] parent shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child shortcodes.
	 * @return string HTML output.
	 */
	function bma_card_icon_top_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\CardIconTop\CardIconTop::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_card_icon_top_item_shortcode' ) ) {
	/**
	 * Render one [bma_card_icon_top_item] child shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content WYSIWYG body (rich text).
	 * @return string HTML output.
	 */
	function bma_card_icon_top_item_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\CardIconTop\CardIconTop::renderItem( (array) $atts, $content );
	}
}

$bma_card_icon_top_boot = static function (): void {
	\Balefire\Component\CardIconTop\CardIconTop::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\CardIconTop\CardIconTop::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\CardIconTop\CardIconTop::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_card_icon_top_boot();
} else {
	add_action( 'plugins_loaded', $bma_card_icon_top_boot, 20 );
}
unset( $bma_card_icon_top_boot );
