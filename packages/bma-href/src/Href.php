<?php
/**
 * Link href resolver.
 *
 * Source of truth class. Global function wrapper (bma_resolve_href) is
 * defined in bootstrap.php and delegates here.
 *
 * Used by bma-brand-icon-cards, bma-card-media, and any package that
 * accepts a `link_page` (page ID or URL) and `link_url` (full URL) pair.
 *
 * @package Balefire\Components\Href
 */

declare( strict_types=1 );

namespace Balefire\Components\Href;

defined( 'ABSPATH' ) || exit;

/**
 * Static link href resolver.
 *
 * @package Balefire\Components\Href
 */
final class Href {

	/**
	 * Resolve a media-card link target from a link_page (id|url) or link_url.
	 *
	 * Explicit link_url wins over a page id. Stale link_page values (deleted
	 * pages, draft ids) would otherwise return '' from get_permalink and
	 * silently kill the link.
	 *
	 * @param string $link_page Page ID (numeric) or full URL.
	 * @param string $link_url  Fallback full URL.
	 * @return string Resolved href, or '' if none.
	 */
	public static function resolve( string $link_page, string $link_url ): string {
		if ( '' !== $link_url ) {
			return esc_url( $link_url );
		}
		if ( '' !== $link_page ) {
			return is_numeric( $link_page )
				? (string) get_permalink( (int) $link_page )
				: esc_url( $link_page );
		}
		return '';
	}
}
