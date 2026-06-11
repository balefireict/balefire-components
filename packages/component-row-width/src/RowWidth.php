<?php
/**
 * BMA Row Width — adds a "Content Width" dropdown to WPBakery rows.
 *
 * Registers an extra dropdown param on vc_row and vc_row_inner with the
 * theme's custom width presets (plus a free-form Custom value), and appends
 * a bma-row-w-* class to the row when one is selected. The CSS caps the row
 * at the chosen width and centers it with margin-inline: auto.
 *
 * Width tokens (theme :root custom properties, with standalone fallbacks):
 *   --w-full:      100%
 *   --max-w-lg:    1080px
 *   --max-w-md-lg: 950px
 *   --max-w-md:    850px
 *   --max-w-sm:    512px
 *
 * Custom values are emitted as an inline `--bma-row-w` custom property on
 * the row element for normal rows, or on an injected inner wrapper for
 * stretched rows (via the vc_shortcode_output filter).
 *
 * @package Balefire\Component\RowWidth
 */

declare( strict_types=1 );

namespace Balefire\Component\RowWidth;

defined( 'ABSPATH' ) || exit;

/**
 * Static registrar for the row width dropdown + class filter.
 */
final class RowWidth {

	/**
	 * Param names added to vc_row / vc_row_inner.
	 */
	public const PARAM        = 'content_width';
	public const PARAM_CUSTOM = 'content_width_custom';

	/**
	 * Allowed width choices => css class suffix.
	 *
	 * @var array<string, string>
	 */
	public const CHOICES = array(
		'full'   => 'bma-row-w-full',
		'lg'     => 'bma-row-w-lg',
		'md-lg'  => 'bma-row-w-md-lg',
		'md'     => 'bma-row-w-md',
		'sm'     => 'bma-row-w-sm',
		'custom' => 'bma-row-w-custom',
	);

	/**
	 * Add the dropdown to vc_row and vc_row_inner. Runs on vc_after_init
	 * (vc_add_param needs the base elements mapped first).
	 *
	 * Placement: WPBakery sorts edit-form params by descending `weight`
	 * (core params are weight 0). To slot Content Width directly ABOVE
	 * "Minimum height" on vc_row, the params above it get higher weights:
	 * row_title 40 > full_width 30 > gap 20 > content_width 10 > rest 0.
	 */
	public static function addParams(): void {
		if ( ! function_exists( 'vc_add_param' ) ) {
			return;
		}

		$dropdown = array(
			'type'        => 'dropdown',
			'heading'     => __( 'Content Width', 'balefire' ),
			'param_name'  => self::PARAM,
			'value'       => array(
				__( 'Default', 'balefire' )  => '',
				'100%'                       => 'full',
				'1080px'                     => 'lg',
				'950px'                      => 'md-lg',
				'850px'                      => 'md',
				'512px'                      => 'sm',
				__( 'Custom…', 'balefire' )  => 'custom',
			),
			'std'         => '',
			'description' => __( 'BMA — Max content width for this row, centered with margin-inline auto.', 'balefire' ),
			'weight'      => 10,
		);

		$custom = array(
			'type'        => 'textfield',
			'heading'     => __( 'Custom Content Width', 'balefire' ),
			'param_name'  => self::PARAM_CUSTOM,
			'description' => __( 'BMA — Any CSS length (e.g. 920px, 72rem, 90%). Bare numbers are treated as px.', 'balefire' ),
			'dependency'  => array(
				'element' => self::PARAM,
				'value'   => array( 'custom' ),
			),
			'weight'      => 10,
		);

		vc_add_param( 'vc_row', $dropdown );
		vc_add_param( 'vc_row', $custom );
		vc_add_param( 'vc_row_inner', $dropdown );
		vc_add_param( 'vc_row_inner', $custom );

		// Pin the core params that should stay above Content Width.
		if ( function_exists( 'vc_update_shortcode_param' ) && class_exists( 'WPBMap' ) ) {
			$pin = array(
				'row_title'  => 40,
				'full_width' => 30,
				'gap'        => 20,
			);
			foreach ( $pin as $name => $weight ) {
				foreach ( array( 'vc_row', 'vc_row_inner' ) as $base ) {
					$existing = \WPBMap::getParam( $base, $name );
					if ( is_array( $existing ) ) {
						$existing['weight'] = $weight;
						vc_update_shortcode_param( $base, $existing );
					}
				}
			}
		}
	}

	/**
	 * Normalize a custom width value to a safe CSS length.
	 *
	 * Bare numbers become px. Anything that isn't number + (px|%|rem|em|
	 * vw|ch) is rejected (returns '').
	 *
	 * @param string $value Raw param value.
	 * @return string Sanitized CSS length or ''.
	 */
	public static function sanitizeCustomWidth( string $value ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^\d+(\.\d+)?$/', $value ) ) {
			return $value . 'px';
		}
		if ( preg_match( '/^\d+(\.\d+)?(px|%|rem|em|vw|ch)$/', $value ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Append the width class to the row's class list on the front end.
	 *
	 * Hooked on vc_shortcodes_css_class (VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG),
	 * which vc_row's template applies to its merged class string.
	 *
	 * @param string $classes Space-separated class list.
	 * @param string $base    Shortcode base (vc_row, vc_row_inner, ...).
	 * @param array  $atts    Shortcode atts.
	 * @return string Filtered class list.
	 */
	public static function filterRowClass( $classes, $base = '', $atts = array() ): string {
		if ( 'vc_row' !== $base && 'vc_row_inner' !== $base ) {
			return (string) $classes;
		}

		$choice = isset( $atts[ self::PARAM ] ) ? (string) $atts[ self::PARAM ] : '';
		if ( '' === $choice || ! isset( self::CHOICES[ $choice ] ) ) {
			return (string) $classes;
		}

		if ( 'custom' === $choice ) {
			$width = self::sanitizeCustomWidth( (string) ( $atts[ self::PARAM_CUSTOM ] ?? '' ) );
			if ( '' === $width ) {
				return (string) $classes;
			}
		}

		$classes = trim( (string) $classes . ' ' . self::CHOICES[ $choice ] );
		if ( ! empty( $atts['full_width'] ) && 'vc_row' === $base ) {
			$classes .= ' bma-row-has-inner-width';
		}

		return $classes;
	}

	/**
	 * Inject the inline --bma-row-w custom property on rows using a custom
	 * width. Hooked on vc_shortcode_output for vc_row/vc_row_inner.
	 *
	 * Targets the first opening tag that carries the bma-row-w-custom class:
	 * appends to an existing style attribute or adds one.
	 *
	 * @param string $output Rendered shortcode HTML.
	 * @param object $obj    Shortcode object.
	 * @param array  $atts   Prepared atts.
	 * @param string $base   Shortcode base.
	 * @return string Filtered output.
	 */
	public static function filterRowOutput( $output, $obj = null, $atts = array(), $base = '' ) {
		if ( 'vc_row' !== $base && 'vc_row_inner' !== $base ) {
			return $output;
		}
		if ( ! is_array( $atts ) ) {
			return $output;
		}

		$choice = isset( $atts[ self::PARAM ] ) ? (string) $atts[ self::PARAM ] : '';
		if ( '' === $choice || ! isset( self::CHOICES[ $choice ] ) ) {
			return $output;
		}

		$declaration = '';
		if ( 'custom' === $choice ) {
			$width = self::sanitizeCustomWidth( (string) ( $atts[ self::PARAM_CUSTOM ] ?? '' ) );
			if ( '' === $width ) {
				return $output;
			}
			$declaration = '--bma-row-w:' . $width . ';';
		}

		if ( ! empty( $atts['full_width'] ) && 'vc_row' === $base ) {
			return self::wrapStretchedRowInner( (string) $output, $declaration );
		}

		if ( '' === $declaration ) {
			return $output;
		}

		return self::injectStyleDeclaration( (string) $output, $declaration );
	}

	/**
	 * Add the custom-width declaration to the first row tag carrying the custom marker class.
	 *
	 * @param string $output      Rendered shortcode HTML.
	 * @param string $declaration Safe CSS declaration.
	 * @return string Filtered output.
	 */
	private static function injectStyleDeclaration( string $output, string $declaration ): string {
		return (string) preg_replace_callback(
			'/<(div|section)\b([^>]*\bbma-row-w-custom\b[^>]*)>/',
			static function ( array $m ) use ( $declaration ): string {
				if ( preg_match( '/\bstyle="([^"]*)"/', $m[2], $sm ) ) {
					$attrs = str_replace( $sm[0], 'style="' . $declaration . $sm[1] . '"', $m[2] );
					return '<' . $m[1] . $attrs . '>';
				}
				return '<' . $m[1] . $m[2] . ' style="' . $declaration . '">';
			},
			$output,
			1
		);
	}

	/**
	 * For stretched rows, keep the outer row full-bleed and cap an injected inner wrapper.
	 *
	 * @param string $output      Rendered shortcode HTML.
	 * @param string $declaration Optional safe CSS declaration for custom widths.
	 * @return string Filtered output.
	 */
	private static function wrapStretchedRowInner( string $output, string $declaration = '' ): string {
		if ( str_contains( $output, 'bma-row-width-inner' ) ) {
			return $output;
		}

		$style = '' !== $declaration ? ' style="' . esc_attr( $declaration ) . '"' : '';

		$pattern_with_clear = '/^(<div\b[^>]*\bbma-row-has-inner-width\b[^>]*>)(.*)(<\/div>)(<div class="vc_row-full-width[^>]*><\/div>)$/s';
		if ( preg_match( $pattern_with_clear, $output ) ) {
			return (string) preg_replace(
				$pattern_with_clear,
				'$1<div class="bma-row-width-inner"' . $style . '>$2</div>$3$4',
				$output,
				1
			);
		}

		return (string) preg_replace(
			'/^(<div\b[^>]*\bbma-row-has-inner-width\b[^>]*>)(.*)(<\/div>)$/s',
			'$1<div class="bma-row-width-inner"' . $style . '>$2</div>$3',
			$output,
			1
		);
	}

	/**
	 * Hook everything up.
	 */
	public static function register(): void {
		add_action( 'vc_after_init', array( self::class, 'addParams' ) );
		add_filter( 'vc_shortcodes_css_class', array( self::class, 'filterRowClass' ), 10, 3 );
		add_filter( 'vc_shortcode_output', array( self::class, 'filterRowOutput' ), 10, 4 );
	}
}
