<?php
/**
 * Balefire Component: CTA Banner
 *
 * Attribute-driven WPBakery element (no ACF). Content comes from
 * shortcode attributes + the element's rich-text body ($content).
 */

declare( strict_types=1 );

namespace Balefire\Component\CtaBanner;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

const SLUG      = 'cta-banner';
const SHORTCODE = 'bma_cta_banner';

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
