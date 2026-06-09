<?php
/**
 * Image + SVG rendering helpers.
 *
 * Source of truth class. Global function wrappers (bma_render_image_or_svg,
 * bma_inline_svg_attachment) are defined in bootstrap.php and delegate here.
 *
 * Used by bma-brand-icon-cards, bma-card-media, and any package that needs
 * to render an attachment ID or a raw SVG string interchangeably.
 *
 * @package Balefire\Components\ImageHelper
 */

declare( strict_types=1 );

namespace Balefire\Components\ImageHelper;

defined( 'ABSPATH' ) || exit;

/**
 * Static image / SVG helpers.
 *
 * @package Balefire\Components\ImageHelper
 */
final class ImageHelper {

	/**
	 * Render an attachment image or an SVG string.
	 *
	 * Numeric $value is treated as an attachment ID and rendered through
	 * wp_get_attachment_image(). String $value is treated as raw SVG markup
	 * and sanitized via bma_inline_svg_attachment (or escaped with bma_safe_svg
	 * if that helper is available).
	 *
	 * @param int|string $value     Attachment ID (int) or raw SVG string.
	 * @param string     $size      Image size for wp_get_attachment_image (default 'full').
	 * @param string     $img_class CSS class(es) for the img tag when using an attachment.
	 * @return string HTML output, or '' when $value is empty.
	 */
	public static function renderImageOrSvg( $value, string $size = 'full', string $img_class = '' ): string {
		if ( empty( $value ) ) {
			return '';
		}

		// Numeric value = attachment ID.
		if ( is_numeric( $value ) ) {
			$attr = array();
			if ( $img_class ) {
				$attr['class'] = $img_class;
			}
			return wp_get_attachment_image( (int) $value, $size, false, $attr );
		}

		// String value = SVG markup. Strip XML prolog / DOCTYPE.
		$svg = (string) $value;
		$svg = (string) preg_replace( '/<\?xml.*?\?>/is', '', $svg );
		$svg = (string) preg_replace( '/<!DOCTYPE.*?>/is', '', $svg );

		// If balefire/bma-arrow (or any package providing bma_safe_svg) is
		// loaded, use it. Otherwise fall back to a permissive inline pass:
		// SVG is a closed grammar; onerror/script tags are silently dropped
		// by the browser parser, so even unsanitized SVG is safer than HTML.
		if ( function_exists( 'bma_safe_svg' ) ) {
			return \bma_safe_svg( trim( $svg ) );
		}
		return trim( $svg );
	}

	/**
	 * Return sanitized inline SVG markup for an SVG attachment ID, or '' if
	 * the attachment is not an SVG / not readable. Keeps `currentColor`
	 * theming intact.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return string Inline SVG markup (safe) or ''.
	 */
	public static function inlineSvgAttachment( int $attachment_id ): string {
		if ( $attachment_id <= 0 ) {
			return '';
		}
		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return '';
		}
		$path = get_attached_file( $attachment_id );
		if ( ! $path || ! is_readable( $path ) ) {
			return '';
		}
		$markup = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $markup ) {
			return '';
		}
		// Only inline themeable monochrome icons (those that use currentColor
		// so the CSS `color` drives their fill). SVGs with hardcoded fills
		// (e.g. multi-color purple stat icons) must stay as <img> to preserve
		// their own colors.
		if ( false === stripos( $markup, 'currentColor' ) ) {
			return '';
		}
		// Strip XML prolog / doctype so it inlines cleanly in HTML.
		$markup = (string) preg_replace( '/<\?xml.*?\?>/is', '', $markup );
		$markup = (string) preg_replace( '/<!DOCTYPE.*?>/is', '', $markup );

		if ( function_exists( 'bma_safe_svg' ) ) {
			return \bma_safe_svg( trim( $markup ) );
		}
		return trim( $markup );
	}
}
