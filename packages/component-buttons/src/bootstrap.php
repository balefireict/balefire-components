<?php
/**
 * balefireict/component-buttons — bootstrap.
 *
 * Registers the parent [bma_buttons] + child [bma_button] shortcodes and
 * their WPBakery elements (parent container + child button).
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\Buttons
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_buttons_render' ) ) {
	/**
	 * Render the parent [bma_buttons] shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child [bma_button] shortcodes.
	 * @return string HTML output.
	 */
	function bma_buttons_render( $atts, $content = null ): string {
		return \Balefire\Component\Buttons\Buttons::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_button_render' ) ) {
	/**
	 * Render a single child [bma_button] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_button_render( array $atts ): string {
		return \Balefire\Component\Buttons\Buttons::renderButton( $atts );
	}
}

$bma_buttons_boot = static function (): void {
	\Balefire\Component\Buttons\Buttons::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\Buttons\Buttons::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\Buttons\Buttons::class, 'registerPreviewClasses' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\Buttons\Buttons::class, 'allowContainersInInnerColumns' ), 20 );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired — boot now.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_buttons_boot();
} else {
	add_action( 'plugins_loaded', $bma_buttons_boot, 20 );
}
unset( $bma_buttons_boot );
