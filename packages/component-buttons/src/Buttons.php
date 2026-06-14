<?php
/**
 * BMA Buttons shortcode.
 *
 * Renders a repeater of buttons with style, size, alignment, optional arrow,
 * optional icon (calendar, phone, or custom upload), and "open in new tab"
 * auto-detection for external links.
 *
 * Source of truth class. Global function wrapper (bma_buttons_render) is
 * defined in bootstrap.php and delegates here. add_shortcode + vc_map
 * are also wired in bootstrap.php.
 *
 * @package Balefire\Component\Buttons
 */

declare( strict_types=1 );

namespace Balefire\Component\Buttons;

defined( 'ABSPATH' ) || exit;

/**
 * Static buttons shortcode renderer.
 *
 * @package Balefire\Component\Buttons
 */
final class Buttons {

	public const STYLES      = array( 'primary', 'secondary', 'transparent', 'white', 'black' );
	public const SIZES       = array( '', 'sm' );
	public const ALIGNMENTS  = array( 'left', 'center', 'right' );
	public const ICONS       = array( '', 'calendar', 'phone', 'custom' );

	/**
	 * Render the [bma_buttons] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when no buttons have label+url.
	 */
	public static function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'align'   => 'center',
				'buttons' => '',
			),
			$atts,
			'bma_buttons'
		);

		$align       = in_array( $atts['align'], self::ALIGNMENTS, true ) ? $atts['align'] : 'center';
		$align_class = 'justify-' . ( 'left' === $align ? 'start' : ( 'right' === $align ? 'end' : 'center' ) );

		$rows = self::parseButtons( $atts['buttons'] );

		$html = '';
		foreach ( $rows as $row ) {
			$html .= self::renderButton( $row );
		}

		if ( '' === $html ) {
			return '';
		}

		return sprintf(
			'<div class="bma-buttons %s">%s</div>',
			esc_attr( $align_class ),
			$html
		);
	}

	/**
	 * Parse the param_group value into an array of button row arrays.
	 * Handles both WPBakery param_group strings and already-parsed arrays.
	 *
	 * @param string|array $raw The raw param_group value.
	 * @return array<int, array<string,string>>
	 */
	public static function parseButtons( $raw ): array {
		if ( is_array( $raw ) ) {
			return $raw;
		}

		$raw = trim( (string) $raw );
		if ( '' === $raw ) {
			return array();
		}

		if ( function_exists( 'vc_param_group_parse_atts' ) ) {
			$parsed = vc_param_group_parse_atts( $raw );
			return is_array( $parsed ) ? $parsed : array();
		}

		// Fallback: JSON decode.
		$decoded = json_decode( $raw, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Render a single button anchor from a row array.
	 *
	 * @param array $row Button data: label, url, style, size, arrow, text_color, icon, icon_custom.
	 * @return string HTML output, or '' when label or url is empty.
	 */
	public static function renderButton( array $row ): string {
		$label = trim( html_entity_decode( (string) ( $row['label'] ?? '' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$url   = trim( html_entity_decode( (string) ( $row['url'] ?? '' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

		if ( '' === $label || '' === $url ) {
			return '';
		}

		$style = strtolower( trim( (string) ( $row['style'] ?? 'primary' ) ) );
		if ( ! in_array( $style, self::STYLES, true ) ) {
			$style = 'primary';
		}

		$size = strtolower( trim( (string) ( $row['size'] ?? '' ) ) );

		$classes = array( 'btn', 'btn-' . $style );
		if ( 'sm' === $size ) {
			$classes[] = 'btn-sm';
		}

		$text_color = strtolower( trim( (string) ( $row['text_color'] ?? 'default' ) ) );
		if ( 'transparent' === $style ) {
			$classes[] = 'white' === $text_color
				? 'btn-transparent--white'
				: 'btn-transparent--default';
		}

		// Icon before label.
		$icon_html = self::renderIcon(
			(string) ( $row['icon'] ?? '' ),
			(string) ( $row['icon_custom'] ?? '' )
		);

		// Arrow after label.
		$show_arrow = filter_var( $row['arrow'] ?? false, FILTER_VALIDATE_BOOLEAN );
		$label      = rtrim( $label, " \t\n\r\0\x0B→" );

		$inner = $icon_html . esc_html( $label );
		if ( $show_arrow ) {
			$inner .= ' <span class="btn-arrow" aria-hidden="true">&rarr;</span>';
		}

		$is_external = self::isExternalUrl( $url );
		$target_attr = $is_external ? ' target="_blank" rel="noopener noreferrer"' : '';

		return sprintf(
			'<a href="%s" class="%s"%s>%s</a>',
			esc_url( $url ),
			esc_attr( implode( ' ', array_filter( $classes ) ) ),
			$target_attr,
			$inner
		);
	}

	/**
	 * Render an inline icon SVG or custom image before the button label.
	 *
	 * @param string $icon       Icon key: '', 'calendar', 'phone', 'custom'.
	 * @param string $icon_custom Attachment ID (when icon is 'custom').
	 * @return string HTML, or '' when no icon.
	 */
	public static function renderIcon( string $icon, string $icon_custom = '' ): string {
		$icon = strtolower( trim( $icon ) );

		switch ( $icon ) {
			case 'calendar':
				// ponytail: calendar+check icon path from layouts/sections/layout-buttons.svg, original 0-20 viewBox.
				return '<span class="btn-icon" aria-hidden="true">'
					. '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="1em" height="1em">'
					. '<path d="M20,1.667V20H0V1.667H2.5V2.5a1.667,1.667,0,0,0,3.334,0V1.667h8.334V2.5a1.667,1.667,0,1,0,3.334,0V1.667Zm-1.667,5H1.667V18.335H18.335ZM16.668.833A.833.833,0,0,0,15,.833V2.5a.833.833,0,0,0,1.667,0ZM5,2.5a.833.833,0,1,1-1.667,0V.833A.833.833,0,1,1,5,.833Zm.833,9.775.713-.659a13.772,13.772,0,0,1,2.3,1.378,19.9,19.9,0,0,1,5.089-4.36l.233.533A22.628,22.628,0,0,0,9.2,15.835,35.663,35.663,0,0,0,5.834,12.275Z"/>'
					. '</svg></span>';

			case 'phone':
				// ponytail: phone icon path extracted from layouts/sections/layout-buttons-alt.svg, raw viewBox preserved.
				return '<span class="btn-icon" aria-hidden="true">'
					. '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-97 413 13 20" fill="currentColor" width="1em" height="1em">'
					. '<path d="M-87.355,414.333H-95.23a1.688,1.688,0,0,0-1.687,1.688v14.625a1.688,1.688,0,0,0,1.688,1.688h7.875a1.688,1.688,0,0,0,1.688-1.687V416.021A1.688,1.688,0,0,0-87.355,414.333Zm-3.937,16.875a1.124,1.124,0,0,1-1.125-1.125,1.124,1.124,0,0,1,1.125-1.125,1.124,1.124,0,0,1,1.125,1.125A1.123,1.123,0,0,1-91.292,431.208Zm3.938-3.8a.423.423,0,0,1-.421.421h-7.032a.423.423,0,0,1-.422-.421V416.443a.423.423,0,0,1,.422-.422h7.032a.423.423,0,0,1,.421.422Z"/>'
					. '</svg></span>';

			case 'custom':
				$attach_id = (int) $icon_custom;
				if ( $attach_id <= 0 ) {
					return '';
				}
				$img = wp_get_attachment_image( $attach_id, 'full', false, array(
					'class'    => 'btn-icon-custom',
					'alt'      => '',
					'aria-hidden' => 'true',
				) );
				return $img ? '<span class="btn-icon" aria-hidden="true">' . $img . '</span>' : '';

			default:
				return '';
		}
	}

	/**
	 * True if $url points to a different host than the current site.
	 */
	public static function isExternalUrl( string $url ): bool {
		$url = trim( $url );
		if ( '' === $url || '#' === substr( $url, 0, 1 ) ) {
			return false;
		}
		if ( preg_match( '/^[a-z][a-z0-9+.-]*:/i', $url ) && ! preg_match( '/^https?:/i', $url ) ) {
			return false;
		}

		$url_host = wp_parse_url( $url, PHP_URL_HOST );
		if ( null === $url_host || '' === $url_host ) {
			return false;
		}

		$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( '' === $site_host ) {
			return false;
		}

		return strcasecmp( $url_host, $site_host ) !== 0;
	}

	/**
	 * Register the [bma_buttons] shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'bma_buttons', array( self::class, 'render' ) );
	}

	/**
	 * WPBakery vc_map registration. Called by bootstrap on vc_before_init.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$style_choices = array(
			__( 'Primary (filled, theme color)', 'balefire' )     => 'primary',
			__( 'Secondary (outlined)', 'balefire' )              => 'secondary',
			__( 'White (solid — for dark / gradient bg)', 'balefire' ) => 'white',
			__( 'Black', 'balefire' )                             => 'black',
			__( 'Transparent (text-only link)', 'balefire' )      => 'transparent',
		);

		$size_choices = array(
			__( 'Default (medium)', 'balefire' ) => '',
			__( 'Small', 'balefire' )            => 'sm',
		);

		$text_color_choices = array(
			__( 'Default (theme dark)', 'balefire' ) => 'default',
			__( 'White', 'balefire' )                => 'white',
		);

		$icon_choices = array(
			__( 'None', 'balefire' )     => '',
			__( 'Calendar', 'balefire' ) => 'calendar',
			__( 'Phone', 'balefire' )    => 'phone',
			__( 'Custom', 'balefire' )   => 'custom',
		);

		$button_params = array(
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Label', 'balefire' ),
				'param_name' => 'label',
				'admin_label' => true,
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'URL', 'balefire' ),
				'param_name' => 'url',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Style', 'balefire' ),
				'param_name' => 'style',
				'value'      => $style_choices,
				'std'        => 'primary',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Size', 'balefire' ),
				'param_name' => 'size',
				'value'      => $size_choices,
				'std'        => '',
			),
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Arrow (→)', 'balefire' ),
				'param_name' => 'arrow',
				'value'      => array( __( 'Yes', 'balefire' ) => 'true' ),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Text Color (transparent only)', 'balefire' ),
				'param_name' => 'text_color',
				'value'      => $text_color_choices,
				'std'        => 'default',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Before Text', 'balefire' ),
				'param_name' => 'icon',
				'value'      => $icon_choices,
				'std'        => '',
			),
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Custom Icon (when Icon = Custom)', 'balefire' ),
				'param_name' => 'icon_custom',
				'dependency' => array( 'element' => 'icon', 'value' => array( 'custom' ) ),
			),
		);

		vc_map(
			array(
				'name'        => __( 'Buttons', 'balefire' ),
				'base'        => 'bma_buttons',
				'category'    => __( 'Custom Elements', 'balefire' ),
				'description' => __( 'BMA — Flexible button group with alignment, icons, and per-button styling.', 'balefire' ),
				'icon'        => 'icon-wpb-ui-button',
				'params'      => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Alignment', 'balefire' ),
						'param_name' => 'align',
						'value'      => array(
							__( 'Center', 'balefire' ) => 'center',
							__( 'Left', 'balefire' )   => 'left',
							__( 'Right', 'balefire' )  => 'right',
						),
						'std'        => 'center',
					),
					array(
						'type'        => 'param_group',
						'heading'     => __( 'Buttons', 'balefire' ),
						'param_name'  => 'buttons',
						'description' => __( 'Add one or more buttons. Each has its own label, URL, style, and optional icon.', 'balefire' ),
						'value'       => '',
						'params'      => $button_params,
					),
				),
			)
		);
	}
}
