<?php
/**
 * Balefire Component: Accordion FAQ
 */

declare( strict_types=1 );

namespace Balefire\Component\AccordionFaq;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

const SLUG      = 'accordion-faq';
const SHORTCODE = 'bma_accordion_faq';

\add_action( 'init', static function (): void {
    \add_shortcode( SHORTCODE, [ Renderer::class, 'render' ] );
} );

\add_action( 'vc_before_init', static function (): void {
    if ( ! function_exists( 'vc_map' ) ) {
        return;
    }
    $bakery = __DIR__ . '/bakery.php';
    if ( is_readable( $bakery ) ) {
        require_once $bakery;
    }
} );

\add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
    $paths[] = __DIR__ . '/../acf-json';
    return $paths;
} );
