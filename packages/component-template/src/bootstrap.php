<?php
/**
 * Balefire Component: Template (scaffolding example)
 *
 * This file runs via composer autoload-files. It registers hooks for ACF,
 * shortcode, Bakery, and the host-theme CSS manifest. When copying this
 * template, replace `template` / `Template` / `TEMPLATE` with your slug.
 */

declare( strict_types=1 );

namespace Balefire\Component\Template;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

const SLUG       = 'template';
const SHORTCODE  = 'bma_template';
const CSS_CLASS  = 'bma-c-template';
const PACKAGE_DIR = __DIR__ . '/..'; // /vendor/balefireict/component-template

// 1) ACF JSON load path (skip for scaffolding components with no acf-json/)
\add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
    $candidate = realpath( PACKAGE_DIR . '/acf-json' );
    if ( $candidate && is_dir( $candidate ) ) {
        $paths[] = $candidate;
    }
    return $paths;
} );

// 2) Shortcode
\add_action( 'init', static function (): void {
    \add_shortcode( SHORTCODE, [ Renderer::class, 'render' ] );
} );

// 3) WPBakery element registration (guarded — Bakery may be deactivated)
\add_action( 'vc_before_init', static function (): void {
    if ( ! function_exists( 'vc_map' ) ) {
        return;
    }
    $bakery = PACKAGE_DIR . '/src/bakery.php';
    if ( is_readable( $bakery ) ) {
        require_once $bakery;
    }
} );

// 4) Announce CSS source to the host theme's Vite build
\add_filter( 'balefire/component/css_manifest', static function ( array $manifest ): array {
    $manifest[ SLUG ] = realpath( PACKAGE_DIR . '/src/style.css' ) ?: null;
    return $manifest;
} );
