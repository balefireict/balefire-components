<?php
/**
 * Balefire Component: Testimonial
 *
 * Attribute-driven WPBakery element (no ACF). The quote comes from the
 * element's rich-text body ($content); other fields from shortcode atts.
 */

declare( strict_types=1 );

namespace Balefire\Component\Testimonial;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

const SLUG      = 'testimonial';
const SHORTCODE = 'bma_testimonial';

// Shortcode registration.
\add_action( 'init', static function (): void {
    \add_shortcode( SHORTCODE, [ Renderer::class, 'render' ] );
} );

// WPBakery element registration (guarded).
\add_action( 'vc_before_init', static function (): void {
    if ( ! function_exists( 'vc_map' ) ) {
        return;
    }
    $bakery = __DIR__ . '/bakery.php';
    if ( is_readable( $bakery ) ) {
        require_once $bakery;
    }
} );
