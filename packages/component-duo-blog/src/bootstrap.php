<?php
/**
 * balefireict/component-duo-blog — bootstrap.
 *
 * Registers the [bma_duo_blog] shortcode and its WPBakery element.
 *
 * @package Balefire\Component\DuoBlog
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_duo_blog_shortcode' ) ) {
	/**
	 * Render the [bma_duo_blog] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_duo_blog_shortcode( $atts ): string {
		return \Balefire\Component\DuoBlog\DuoBlog::render( (array) $atts );
	}
}

$bma_duo_blog_boot = static function (): void {
	\Balefire\Component\DuoBlog\DuoBlog::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\DuoBlog\DuoBlog::class, 'vcMap' ) );
	}
};

// WP load order: plugins_loaded fires before theme functions.php. When this
// autoloader is required from a theme, boot immediately if the hook already ran.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_duo_blog_boot();
} else {
	add_action( 'plugins_loaded', $bma_duo_blog_boot, 20 );
}
unset( $bma_duo_blog_boot );
