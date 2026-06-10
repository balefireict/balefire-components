<?php
/**
 * balefireict/component-brand-icon-cards — bootstrap.
 *
 * Defines thin global function wrappers, registers the parent + child
 * shortcodes (with hyphenated aliases), wires vc_map on vc_before_init,
 * and registers the WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\BrandIconCards
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_card_media_html' ) ) {
	/**
	 * Build the media+icon HTML for a card from an SVG string or image id/url.
	 *
	 * @param string     $svg Raw SVG markup (priority).
	 * @param int|string $img Attachment id, url, or array with 'url' key.
	 * @return string Media HTML (safe), or '' if neither provided.
	 */
	function bma_card_media_html( string $svg, $img = '' ): string {
		return \Balefire\Component\BrandIconCards\CardMedia::mediaHtml( $svg, $img );
	}
}

if ( ! function_exists( 'bma_card_logo_html' ) ) {
	/**
	 * Build the top-right logo HTML for a card from an SVG string or image id/url.
	 *
	 * @param string     $svg Raw SVG markup (priority).
	 * @param int|string $img Attachment id, url, or array with 'url' key.
	 * @return string Logo HTML (safe), or '' if neither provided.
	 */
	function bma_card_logo_html( string $svg, $img = '' ): string {
		return \Balefire\Component\BrandIconCards\CardMedia::logoHtml( $svg, $img );
	}
}

if ( ! function_exists( 'bma_brand_icon_cards_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[brand_icon_cards]Cards[/brand_icon_cards]').
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_brand_icon_cards_render( array $atts = array(), string $content = '' ): string {
		return \Balefire\Component\BrandIconCards\BrandIconCards::render( $atts, $content );
	}
}

if ( ! function_exists( 'bma_brand_icon_card_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[brand_icon_card]').
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_brand_icon_card_render( array $atts = array() ): string {
		return \Balefire\Component\BrandIconCards\BrandIconCards::renderCard( $atts );
	}
}

$bma_brand_icon_cards_boot = function (): void {
		\Balefire\Component\BrandIconCards\BrandIconCards::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Component\BrandIconCards\BrandIconCards::class, 'vcMap' ) );
			add_action( 'vc_after_init', array( \Balefire\Component\BrandIconCards\BrandIconCards::class, 'registerContainerClass' ) );
		}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_brand_icon_cards_boot();
} else {
	add_action( 'plugins_loaded', $bma_brand_icon_cards_boot, 20 );
}
unset( $bma_brand_icon_cards_boot );
