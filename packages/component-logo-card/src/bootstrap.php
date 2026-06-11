<?php
/**
 * Balefire Component: Logo Card — bootstrap.
 *
 * Attribute-driven WPBakery parent/child container. No ACF reads.
 *
 * Registers the parent + child shortcodes and wires the WPBakery mapping
 * (bakery.php) on vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\LogoCard
 */

declare( strict_types=1 );

namespace Balefire\Component\LogoCard;

defined( 'ABSPATH' ) || exit;

const SLUG      = 'logo-card';
const SHORTCODE = 'bma_logo_card';

// Shortcode registration (parent + child).
\add_action( 'init', static function (): void {
	Renderer::register();
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
