<?php
/**
 * BMA HR — CSS horizontal rule.
 *
 * Editable content (preferred):
 *   [bma_hr]
 *
 * Outputs bare markup (no <section>, no bma-section padding). Wrap in a
 * WPBakery vc_row for background/spacing/id.
 *
 * Source of truth classes. The global function wrapper (bma_hr_shortcode)
 * is defined in bootstrap.php. add_shortcode and vc_map are wired there too.
 *
 * Ported from rockerbox inc/shortcodes/bma-hr.php.
 *
 * @package Balefire\Component\Hr
 */

declare( strict_types=1 );

namespace Balefire\Component\Hr;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_hr] shortcode.
 *
 * @package Balefire\Component\Hr
 */
final class Hr {

	/**
	 * Render the [bma_hr] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render( array $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'width'  => '230px',
				'height' => '4px',
				'color'  => '#65d0e2',
			),
			(array) $atts,
			'bma_hr'
		);

		$width  = self::sanitizeLength( (string) $atts['width'], '230px' );
		$height = self::sanitizeLength( (string) $atts['height'], '4px' );
		$color  = self::sanitizeHexColor( (string) $atts['color'], '#65d0e2' );
		$style  = sprintf(
			'--bma-hr-width:%s;--bma-hr-height:%s;--bma-hr-color:%s;',
			esc_attr( $width ),
			esc_attr( $height ),
			esc_attr( $color )
		);

		return '<div class="bma-hr" style="' . $style . '" role="presentation" aria-hidden="true"></div>';
	}

	/**
	 * Sanitize a CSS length value.
	 *
	 * @param string $value    Raw value.
	 * @param string $fallback Fallback length.
	 * @return string Safe CSS length.
	 */
	private static function sanitizeLength( string $value, string $fallback ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return $fallback;
		}
		if ( preg_match( '/^\d+(\.\d+)?$/', $value ) ) {
			return $value . 'px';
		}
		if ( preg_match( '/^\d+(\.\d+)?(px|rem|em|%)$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	/**
	 * Sanitize a hex color value.
	 *
	 * @param string $value    Raw value.
	 * @param string $fallback Fallback hex color.
	 * @return string Safe hex color.
	 */
	private static function sanitizeHexColor( string $value, string $fallback ): string {
		$value = trim( $value );
		if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	/**
	 * Register the [bma_hr] shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'bma_hr', array( self::class, 'render' ) );
	}

	/**
	 * WPBakery vc_map registration.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'            => __( 'HR', 'balefire' ),
				'base'            => 'bma_hr',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — CSS horizontal rule. Wrap in a vc_row for background/spacing.', 'balefire' ),
				'icon'            => 'vc_icon-vc-divider',
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Width', 'balefire' ),
						'param_name'  => 'width',
						'value'       => '230px',
						'description' => __( 'BMA — CSS width. Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Height', 'balefire' ),
						'param_name'  => 'height',
						'value'       => '4px',
						'description' => __( 'BMA — CSS height. Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Color', 'balefire' ),
						'param_name'  => 'color',
						'value'       => '#65d0e2',
						'description' => __( 'BMA — Solid hex color for the rule.', 'balefire' ),
					),
				),
			)
		);
	}
}
