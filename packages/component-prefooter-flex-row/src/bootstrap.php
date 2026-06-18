<?php
/**
 * balefireict/component-prefooter-flex-row — bootstrap.
 *
 * Registers the [bma_prefooter_flex_row] shortcode. This package intentionally does not
 * register a WPBakery element; content is sourced from General Business ACF
 * options by default and rendered wherever the shortcode is placed.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\PrefooterFlexRow
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// Register the per-page ACF field group (acffg_prefooter_flex_row_show toggle) from
// this package's acf-json/. Consumers opt out globally by defining
// BALEFIRE_COMPONENTS_LOAD_ACF_JSON = false before the site autoloader.
if ( ! defined( 'BALEFIRE_COMPONENTS_LOAD_ACF_JSON' ) || constant( 'BALEFIRE_COMPONENTS_LOAD_ACF_JSON' ) ) {
	add_filter( 'acf/settings/load_json', static function ( array $paths ): array {
		$candidate = realpath( __DIR__ . '/../acf-json' );
		if ( $candidate && is_dir( $candidate ) ) {
			$paths[] = $candidate;
		}
		return $paths;
	} );
}

if ( ! function_exists( 'bma_prefooter_flex_row_shortcode' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[bma_prefooter_flex_row]').
	 *
	 * @param array|string $atts    Shortcode attributes.
	 * @param string|null $content Optional body override.
	 * @return string HTML output.
	 */
	function bma_prefooter_flex_row_shortcode( $atts = array(), ?string $content = null ): string {
		return \Balefire\Component\PrefooterFlexRow\PrefooterFlexRow::render( $atts, $content );
	}
}

$bma_prefooter_flex_row_boot = static function (): void {
	\Balefire\Component\PrefooterFlexRow\PrefooterFlexRow::register();
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_prefooter_flex_row_boot();
} else {
	add_action( 'plugins_loaded', $bma_prefooter_flex_row_boot, 20 );
}
unset( $bma_prefooter_flex_row_boot );
