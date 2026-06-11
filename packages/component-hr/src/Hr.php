<?php
/**
 * BMA HR — gradient horizontal rule.
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
				'width' => '230',
			),
			(array) $atts,
			'bma_hr'
		);

		$width = max( 1, (int) $atts['width'] );

		// Static gradient id is fine here — the gradient stops and colors are
		// identical for every instance, so all <rect> fills reference the same
		// defs block and the SVG can be inlined once. The id is namespaced to
		// avoid clashing with other inline SVGs on the page.
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" '
			. 'width="' . esc_attr( (string) $width ) . '" height="4" viewBox="0 0 230 4" '
			. 'role="presentation" aria-hidden="true" focusable="false">'
			. '<defs>'
			. '<linearGradient id="bma-hr-gradient" x1="0.132" y1="0.5" x2="0.909" y2="0.5" gradientUnits="objectBoundingBox">'
			. '<stop offset="0" stop-color="#6ad0a1"/>'
			. '<stop offset="1" stop-color="#65d0e2"/>'
			. '</linearGradient>'
			. '</defs>'
			. '<rect data-name="Rectangle 988" width="230" height="4" rx="2" '
			. 'transform="translate(230 4) rotate(180)" fill="url(#bma-hr-gradient)"/>'
			. '</svg>';

		return '<div class="bma-hr">' . $svg . '</div>';
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
				'name'            => __( 'BMA HR', 'balefire' ),
				'base'            => 'bma_hr',
				'category'        => __( 'BMA Elements', 'balefire' ),
				'description'     => __( 'Gradient horizontal rule. Wrap in a vc_row for background/spacing.', 'balefire' ),
				'icon'            => 'vc_icon-vc-divider',
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Width (px)', 'balefire' ),
						'param_name'  => 'width',
						'value'       => '230',
						'description' => __( 'Rendered width in pixels. Height stays 4px and the viewBox preserves the gradient proportions.', 'balefire' ),
					),
				),
			)
		);
	}
}
