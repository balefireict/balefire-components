<?php
/**
 * balefireict/component-portrait-slider — bootstrap.
 *
 * Defines the ORIGINAL rockerbox global function wrappers (so existing
 * themes keep working), registers the parent + child shortcodes, wires
 * vc_map on vc_before_init, and registers the WPBakeryShortCodesContainer
 * subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\PortraitSlider
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_portrait_slider_attr' ) ) {
	/**
	 * Read a WPBakery attribute that may use underscores/hyphens/compact aliases.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $snake   Canonical (snake_case) param name.
	 * @param string $compact Optional compact alias.
	 * @return string
	 */
	function bma_portrait_slider_attr( array $atts, string $snake, string $compact = '' ): string {
		return \Balefire\Component\PortraitSlider\PortraitSlider::attr( $atts, $snake, $compact );
	}
}

if ( ! function_exists( 'bma_render_portrait_slide' ) ) {
	/**
	 * Render a single portrait slide from a normalized data array.
	 *
	 * @param array $data title, image, href, new_tab.
	 * @return string
	 */
	function bma_render_portrait_slide( array $data ): string {
		return \Balefire\Component\PortraitSlider\PortraitSlider::renderSlideData( $data );
	}
}

if ( ! function_exists( 'bma_portrait_slider_wrap' ) ) {
	/**
	 * Wrap pre-rendered slides in the Swiper carousel scaffolding.
	 *
	 * @param string $inner Pre-rendered .swiper-slide markup.
	 * @return string
	 */
	function bma_portrait_slider_wrap( string $inner ): string {
		return \Balefire\Component\PortraitSlider\PortraitSlider::wrap( $inner );
	}
}

if ( ! function_exists( 'bma_portrait_slider_shortcode' ) ) {
	/**
	 * Parent [bma_portrait_slider] shortcode callback.
	 *
	 * @param mixed       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes.
	 * @param string      $tag     Shortcode tag.
	 * @return string
	 */
	function bma_portrait_slider_shortcode( $atts, $content = null, string $tag = '' ): string {
		return \Balefire\Component\PortraitSlider\PortraitSlider::renderSlider( $atts, $content, $tag );
	}
}

if ( ! function_exists( 'bma_portrait_slide_shortcode' ) ) {
	/**
	 * Child [bma_portrait_slide] shortcode callback.
	 *
	 * @param mixed       $atts    Shortcode attributes.
	 * @param string|null $content Unused.
	 * @param string      $tag     Shortcode tag.
	 * @return string
	 */
	function bma_portrait_slide_shortcode( $atts, $content = null, string $tag = '' ): string {
		return \Balefire\Component\PortraitSlider\PortraitSlider::renderSlide( $atts, $content, $tag );
	}
}

$bma_portrait_slider_boot = function (): void {
	\Balefire\Component\PortraitSlider\PortraitSlider::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\PortraitSlider\PortraitSlider::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\PortraitSlider\PortraitSlider::class, 'registerPreviewClasses' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_portrait_slider_boot();
} else {
	add_action( 'plugins_loaded', $bma_portrait_slider_boot, 20 );
}
unset( $bma_portrait_slider_boot );
