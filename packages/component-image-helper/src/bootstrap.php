<?php
/**
 * balefireict/component-image-helper — bootstrap.
 *
 * Defines thin global function wrappers around the PSR-4 ImageHelper class
 * so other packages (and legacy theme code) can call helpers by name without
 * PSR-4 ceremony.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\ImageHelper
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_render_image_or_svg' ) ) {
	/**
	 * Render an attachment image or an SVG string.
	 *
	 * @param int|string $value     Attachment ID or raw SVG string.
	 * @param string     $size      Image size for wp_get_attachment_image (default 'full').
	 * @param string     $img_class CSS class(es) for the img tag.
	 * @return string HTML output, or '' when $value is empty.
	 */
	function bma_render_image_or_svg( $value, string $size = 'full', string $img_class = '' ): string {
		return \Balefire\Component\ImageHelper\ImageHelper::renderImageOrSvg( $value, $size, $img_class );
	}
}

if ( ! function_exists( 'bma_inline_svg_attachment' ) ) {
	/**
	 * Return sanitized inline SVG markup for an SVG attachment ID, or '' if not applicable.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return string Inline SVG markup or ''.
	 */
	function bma_inline_svg_attachment( int $attachment_id ): string {
		return \Balefire\Component\ImageHelper\ImageHelper::inlineSvgAttachment( $attachment_id );
	}
}
