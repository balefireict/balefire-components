<?php
/**
 * balefireict/component-image-text-list — bootstrap.
 *
 * Defines thin global function wrappers, registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\ImageTextList
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_image_text_list_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_image_text_list]Body[/bma_image_text_list]').
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_image_text_list_render( array $atts = array(), string $content = '' ): string {
		return \Balefire\Component\ImageTextList\ImageTextList::render( $atts, $content );
	}
}

if ( ! function_exists( 'bma_image_text_item_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_image_text_item]Body[/bma_image_text_item]').
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Body HTML.
	 * @return string HTML output.
	 */
	function bma_image_text_item_render( array $atts = array(), string $content = '' ): string {
		return \Balefire\Component\ImageTextList\ImageTextList::renderItem( $atts, $content );
	}
}

$bma_image_text_list_boot = function (): void {
		\Balefire\Component\ImageTextList\ImageTextList::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Component\ImageTextList\ImageTextList::class, 'vcMap' ) );
			add_action( 'vc_after_init', array( \Balefire\Component\ImageTextList\ImageTextList::class, 'registerContainerClass' ) );
		}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_image_text_list_boot();
} else {
	add_action( 'plugins_loaded', $bma_image_text_list_boot, 20 );
}
unset( $bma_image_text_list_boot );
