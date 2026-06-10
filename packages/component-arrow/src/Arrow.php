<?php
/**
 * Shared arrow SVG + safe SVG sanitizer.
 *
 * Source of truth class. Global function wrappers (bma_arrow_svg,
 * bma_safe_svg) are defined in bootstrap.php and delegate here.
 *
 * Used by bma-brand-icon-cards, bma-card-media, and any package that
 * renders a chevron arrow or needs to sanitize inline SVG.
 *
 * @package Balefire\Component\Arrow
 */

declare( strict_types=1 );

namespace Balefire\Component\Arrow;

defined( 'ABSPATH' ) || exit;

/**
 * Static arrow SVG + safe-svg sanitizer.
 *
 * @package Balefire\Component\Arrow
 */
final class Arrow {

	/**
	 * Shared arrow SVG used by media cards / arrow links.
	 *
	 * Class names use vanilla defaults (no Tailwind utility classes); the
	 * consuming theme can style via descendant selectors if needed.
	 *
	 * @return string SVG markup.
	 */
	public static function arrowSvg(): string {
		return '<svg class="bma-arrow" xmlns="http://www.w3.org/2000/svg" width="22.62" height="12.5" viewBox="0 0 22.62 12.5" aria-hidden="true"><path d="M12.87,16.808l5.358-5.362a.643.643,0,0,0,0-.908L12.87,5.178a.63.63,0,0,0-.449-.186.643.643,0,0,0-.459,1.094l4.264,4.264H-3.063a.643.643,0,0,0-.642.642.643.643,0,0,0,.642.642H16.226L11.961,15.9a.644.644,0,0,0,.462,1.093.632.632,0,0,0,.447-.184Z" transform="translate(3.955 -4.742)" fill="currentColor" stroke="currentColor" stroke-width="0.5"/></svg>';
	}

	/**
	 * Sanitize an SVG string with a strict allowlist of tags and attributes.
	 *
	 * @param string $svg Raw SVG markup.
	 * @return string Sanitized SVG markup, or '' for empty input.
	 */
	public static function safeSvg( string $svg ): string {
		if ( '' === trim( $svg ) ) {
			return '';
		}

		$svg_allowlist = array(
			'svg'      => array(
				'xmlns'       => true,
				'xmlns:xlink' => true,
				'viewbox'     => true,
				'width'       => true,
				'height'      => true,
				'fill'        => true,
				'class'       => true,
				'aria-hidden' => true,
				'role'        => true,
				'focusable'   => true,
				'id'          => true,
				'style'       => true,
				'version'     => true,
			),
			'defs'     => array(),
			'g'        => array(
				'fill'   => true,
				'class'  => true,
				'id'     => true,
				'style'  => true,
				'transform' => true,
			),
			'path'     => array(
				'd'            => true,
				'fill'         => true,
				'fill-rule'    => true,
				'clip-rule'    => true,
				'stroke'       => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
				'class'        => true,
				'id'           => true,
				'style'        => true,
				'transform'    => true,
			),
			'circle'   => array(
				'cx'    => true,
				'cy'    => true,
				'r'     => true,
				'fill'  => true,
				'class' => true,
				'id'    => true,
				'style' => true,
				'stroke' => true,
				'stroke-width' => true,
			),
			'ellipse'  => array(
				'cx'    => true,
				'cy'    => true,
				'rx'    => true,
				'ry'    => true,
				'fill'  => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'rect'     => array(
				'x'      => true,
				'y'      => true,
				'width'  => true,
				'height' => true,
				'rx'     => true,
				'ry'     => true,
				'fill'   => true,
				'class'  => true,
				'id'     => true,
				'style'  => true,
			),
			'line'     => array(
				'x1'           => true,
				'y1'           => true,
				'x2'           => true,
				'y2'           => true,
				'stroke'       => true,
				'stroke-width' => true,
				'class'        => true,
				'id'           => true,
			),
			'polyline' => array(
				'points' => true,
				'fill'   => true,
				'stroke' => true,
				'stroke-width' => true,
				'class'  => true,
				'id'     => true,
			),
			'polygon'  => array(
				'points' => true,
				'fill'   => true,
				'stroke' => true,
				'stroke-width' => true,
				'class'  => true,
				'id'     => true,
			),
			'title'    => array(),
			'desc'     => array(),
			'use'      => array(
				'xlink:href' => true,
				'href'       => true,
				'x'          => true,
				'y'          => true,
				'width'      => true,
				'height'     => true,
				'fill'       => true,
				'class'      => true,
				'id'         => true,
			),
			'clipPath' => array(
				'id' => true,
				'class' => true,
			),
			'linearGradient' => array(
				'id'           => true,
				'x1'           => true,
				'y1'           => true,
				'x2'           => true,
				'y2'           => true,
				'gradientUnits' => true,
			),
			'radialGradient' => array(
				'id'           => true,
				'cx'           => true,
				'cy'           => true,
				'r'            => true,
				'gradientUnits' => true,
			),
			'stop'     => array(
				'offset' => true,
				'stop-color' => true,
				'stop-opacity' => true,
			),
			'mask'     => array(
				'id' => true,
			),
			'pattern'  => array(
				'id'            => true,
				'x'             => true,
				'y'             => true,
				'width'         => true,
				'height'        => true,
				'patternUnits'  => true,
			),
			'symbol'   => array(
				'id'           => true,
				'viewBox'      => true,
			),
			'animate'  => array(
				'attributeName' => true,
				'from'          => true,
				'to'            => true,
				'dur'           => true,
				'repeatCount'   => true,
				'values'        => true,
				'type'          => true,
			),
			'text'     => array(
				'x'        => true,
				'y'        => true,
				'fill'     => true,
				'class'    => true,
				'id'       => true,
				'style'    => true,
				'font-size' => true,
			),
			'tspan'    => array(
				'x'      => true,
				'y'      => true,
				'dx'     => true,
				'dy'     => true,
				'fill'   => true,
				'class'  => true,
				'id'     => true,
			),
			'filter'   => array(
				'id'    => true,
				'class' => true,
			),
			'feDropShadow' => array(
				'dx'      => true,
				'dy'      => true,
				'stdDeviation' => true,
				'flood-color' => true,
				'flood-opacity' => true,
			),
			'feGaussianBlur' => array(
				'stdDeviation' => true,
				'in' => true,
			),
			'feMerge' => array(
				'id' => true,
			),
			'feMergeNode' => array(
				'in' => true,
			),
			'foreignObject' => array(
				'x'      => true,
				'y'      => true,
				'width'  => true,
				'height' => true,
			),
			'image'    => array(
				'href'          => true,
				'xlink:href'    => true,
				'x'             => true,
				'y'             => true,
				'width'         => true,
				'height'        => true,
				'preserveAspectRatio' => true,
			),
		);

		return wp_kses( $svg, $svg_allowlist );
	}
}
