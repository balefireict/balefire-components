<?php
/**
 * balefireict/component-accordion-faq — bootstrap.
 *
 * Registers the parent + child shortcodes, wires vc_map on vc_before_init,
 * and registers the WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\AccordionFaq
 */

declare( strict_types=1 );

namespace Balefire\Component\AccordionFaq;

defined( 'ABSPATH' ) || exit;

const SLUG = 'accordion-faq';

\add_action( 'init', static function (): void {
	Renderer::register();
} );

\add_action( 'vc_before_init', static function (): void {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}
	$bakery = __DIR__ . '/bakery.php';
	if ( is_readable( $bakery ) ) {
		require_once $bakery;
	}
	if ( function_exists( 'bma_accordion_faq_vc_map' ) ) {
		bma_accordion_faq_vc_map();
	}
} );

\add_action( 'vc_after_init', static function (): void {
	$bakery = __DIR__ . '/bakery.php';
	if ( is_readable( $bakery ) ) {
		require_once $bakery;
	}
	if ( function_exists( 'bma_accordion_faq_register_container_class' ) ) {
		bma_accordion_faq_register_container_class();
	}
} );
