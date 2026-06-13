<?php
/**
 * balefireict/component-reviews-slider — bootstrap.
 *
 * Registers the [vmg_reviews_slider] shortcode, its WPBakery element mapping
 * (vc_before_init), and the public admin-ajax fragment endpoint
 * (wp_ajax_[nopriv_]vmg_reviews_slider) that htmx pulls the slides from.
 * CPT-backed component: it reads a site-provided `review` post type and the
 * fragment comes back empty where that CPT is absent, so it is safe to load
 * in any consumer theme.
 *
 * Requires htmx on window (the consumer theme enqueues it globally — vmg
 * bundles htmx.org via main.js). Carousel JS itself ships inline; CSS in
 * src/style.css.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\ReviewsSlider
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'vmg_reviews_slider_shortcode' ) ) {
	/**
	 * Render the [vmg_reviews_slider] shortcode.
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function vmg_reviews_slider_shortcode( $atts ): string {
		return \Balefire\Component\ReviewsSlider\ReviewsSlider::render( $atts );
	}
}

$bma_reviews_slider_boot = static function (): void {
	\Balefire\Component\ReviewsSlider\ReviewsSlider::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\ReviewsSlider\ReviewsSlider::class, 'vcMap' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_reviews_slider_boot();
} else {
	add_action( 'plugins_loaded', $bma_reviews_slider_boot, 20 );
}
unset( $bma_reviews_slider_boot );
