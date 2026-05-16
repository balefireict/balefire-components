<?php
/**
 * Balefire Component: Header
 *
 * Scaffolding-type component. Ships DOM + nav rendering hooks. No CSS.
 * Host theme owns header styling.
 */

declare( strict_types=1 );

namespace Balefire\Component\HeaderSplit;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

const SLUG      = 'header-split';
const SHORTCODE = 'bma_header_split';

// Shortcode registration.
\add_action( 'init', static function (): void {
    \add_shortcode( SHORTCODE, [ Renderer::class, 'render' ] );
} );

// WPBakery element registration (guarded — Bakery may be deactivated).
\add_action( 'vc_before_init', static function (): void {
    if ( ! function_exists( 'vc_map' ) ) {
        return;
    }
    $bakery = __DIR__ . '/bakery.php';
    if ( is_readable( $bakery ) ) {
        require_once $bakery;
    }
} );

// This component owns no CSS. Skip the css_manifest filter.
