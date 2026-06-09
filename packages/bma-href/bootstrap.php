<?php
/**
 * Balefire/bma-href — bootstrap.
 *
 * Defines a thin global function wrapper around the PSR-4 Href class
 * so other packages (and legacy theme code) can call it by name.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Components\Href
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_resolve_href' ) ) {
	/**
	 * Resolve a link target from a link_page (id|url) or link_url.
	 *
	 * @param string $link_page Page ID (numeric) or full URL.
	 * @param string $link_url  Fallback full URL.
	 * @return string Resolved href, or '' if none.
	 */
	function bma_resolve_href( string $link_page, string $link_url ): string {
		return \Balefire\Components\Href\Href::resolve( $link_page, $link_url );
	}
}
