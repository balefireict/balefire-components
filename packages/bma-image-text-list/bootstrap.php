<?php
/**
 * Balefire/bma-image-text-list — bootstrap.
 *
 * Defines thin global function wrappers, registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Components\ImageTextList
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
		return \Balefire\Components\ImageTextList\ImageTextList::render( $atts, $content );
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
		return \Balefire\Components\ImageTextList\ImageTextList::renderItem( $atts, $content );
	}
}

add_action(
	'plugins_loaded',
	function (): void {
		\Balefire\Components\ImageTextList\ImageTextList::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Components\ImageTextList\ImageTextList::class, 'vcMap' ) );
			add_action( 'vc_after_init', array( \Balefire\Components\ImageTextList\ImageTextList::class, 'registerContainerClass' ) );
		}
	},
	20
);
