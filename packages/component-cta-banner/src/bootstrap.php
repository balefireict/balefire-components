<?php
/**
 * Balefire Component: CTA Banner
 *
 * Field-group type component. Reads ACF fields from the current page.
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

// Register ACF JSON load path.
\add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
    $paths[] = __DIR__ . '/../acf-json';
    return $paths;
} );
