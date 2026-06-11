<?php
/**
 * Balefire Component: Feature Grid — bootstrap.
 *
 * Registers the parent [bma_feature_grid] + child [bma_feature_card]
 * attribute-driven WPBakery container shortcodes, wires the vc_map on
 * vc_before_init, and registers the WPBakeryShortCodesContainer subclass on
 * vc_after_init. No ACF reads.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\FeatureGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\FeatureGrid;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

const SLUG         = 'feature-grid';
const SHORTCODE    = 'bma_feature_grid';
const CHILD_SHORTCODE = 'bma_feature_card';

// Shortcode registration (parent + child).
\add_action( 'init', static function (): void {
    \add_shortcode( SHORTCODE, [ Renderer::class, 'render' ] );
    \add_shortcode( CHILD_SHORTCODE, [ Renderer::class, 'render_card' ] );
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

// Register the container class for the parent element.
\add_action( 'vc_after_init', static function (): void {
    if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
        return;
    }
    if ( ! class_exists( 'WPBakeryShortCode_BMA_FeatureGrid' ) ) {
        eval( 'class WPBakeryShortCode_BMA_FeatureGrid extends \\WPBakeryShortCodesContainer {}' );
    }
} );
