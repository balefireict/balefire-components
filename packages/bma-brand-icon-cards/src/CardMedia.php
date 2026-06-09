<?php
/**
 * CardMedia — image/SVG rendering helpers for card-style components.
 *
 * Used internally by Balefire\Components\BrandIconCards\BrandIconCards.
 * Exposed via global function wrappers (bma_card_media_html,
 * bma_card_logo_html) for backward compatibility with legacy theme code.
 *
 * Renders a media element from a raw SVG string (priority) or an image
 * attachment ID/URL. SVG strings are sanitized through bma_safe_svg()
 * (provided by balefire/bma-arrow).
 *
 * @package Balefire\Components\BrandIconCards
 */

declare( strict_types=1 );

namespace Balefire\Components\BrandIconCards;

defined( 'ABSPATH' ) || exit;

/**
 * Static media/logo card helper.
 *
 * @package Balefire\Components\BrandIconCards
 */
final class CardMedia {

	/**
	 * Build the media+icon HTML for a card from an SVG string or image id/url.
	 *
	 * @param string     $svg Raw SVG markup (priority).
	 * @param int|string $img Attachment id, url, or array with 'url' key.
	 * @return string Media HTML (safe), or '' if neither provided.
	 */
	public static function mediaHtml( string $svg, $img = '' ): string {
		$svg = trim( $svg );
		if ( '' !== $svg ) {
			$safe_svg = function_exists( 'bma_safe_svg' )
				? bma_safe_svg( $svg )
				: $svg;
			return '<div class="bma-card-media__media-inner">' . $safe_svg . '</div>';
		}
		if ( ! empty( $img ) ) {
			$url = self::resolveImageUrl( $img );
			if ( $url ) {
				return '<img src="' . esc_url( $url ) . '" alt="" class="bma-card-media__img" loading="lazy" />';
			}
		}
		return '';
	}

	/**
	 * Build the top-right logo HTML for a card from an SVG string or image id/url.
	 *
	 * @param string     $svg Raw SVG markup (priority).
	 * @param int|string $img Attachment id, url, or array with 'url' key.
	 * @return string Logo HTML (safe), or '' if neither provided.
	 */
	public static function logoHtml( string $svg, $img = '' ): string {
		$svg = trim( $svg );
		if ( '' !== $svg ) {
			$safe_svg = function_exists( 'bma_safe_svg' )
				? bma_safe_svg( $svg )
				: $svg;
			return '<div class="bma-card-media__top-logo">' . $safe_svg . '</div>';
		}
		if ( ! empty( $img ) ) {
			$url = self::resolveImageUrl( $img );
			if ( $url ) {
				return '<div class="bma-card-media__top-logo"><img src="' . esc_url( $url ) . '" alt="" class="bma-card-media__top-logo-img" loading="lazy" /></div>';
			}
		}
		return '';
	}

	/**
	 * Resolve an image value to a URL.
	 *
	 * Numeric values are treated as attachment IDs and resolved via
	 * wp_get_attachment_image_url(). Strings are treated as direct URLs.
	 * Arrays with a 'url' key (ACF file/image return shape) are unwrapped.
	 *
	 * @param mixed $img Attachment ID, URL, or array.
	 * @return string URL, or '' if resolution fails.
	 */
	private static function resolveImageUrl( $img ): string {
		if ( is_array( $img ) ) {
			$img = $img['url'] ?? '';
		}
		if ( '' === $img ) {
			return '';
		}
		if ( is_numeric( $img ) ) {
			$url = wp_get_attachment_image_url( (int) $img, 'full' );
			return $url ? (string) $url : '';
		}
		return (string) $img;
	}
}
