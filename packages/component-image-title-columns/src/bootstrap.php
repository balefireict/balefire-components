<?php
/**
 * balefireict/component-image-title-columns - bootstrap.
 *
 * Registers the parent + child shortcodes and their WPBakery elements.
 *
 * @package Balefire\Component\ImageTitleColumns
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_image_title_columns_shortcode' ) ) {
	/**
	 * Render the [bma_image_title_columns] parent shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child shortcodes.
	 * @return string HTML output.
	 */
	function bma_image_title_columns_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\ImageTitleColumns\ImageTitleColumns::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_image_title_columns_item_shortcode' ) ) {
	/**
	 * Render one [bma_image_title_columns_item] child shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Unused (items are image + title only).
	 * @return string HTML output.
	 */
	function bma_image_title_columns_item_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\ImageTitleColumns\ImageTitleColumns::renderItem( (array) $atts, $content );
	}
}

$bma_image_title_columns_boot = static function (): void {
	\Balefire\Component\ImageTitleColumns\ImageTitleColumns::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\ImageTitleColumns\ImageTitleColumns::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\ImageTitleColumns\ImageTitleColumns::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_image_title_columns_boot();
} else {
	add_action( 'plugins_loaded', $bma_image_title_columns_boot, 20 );
}
unset( $bma_image_title_columns_boot );
