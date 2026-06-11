<?php
/**
 * BMA Buttons shortcode.
 *
 * Renders 1-2 buttons with style, size, alignment, optional arrow, and
 * "open in new tab" auto-detection for external links.
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

	/**
	 * Render the [bma_buttons] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when no buttons have label+url.
	 */
	public static function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'align'           => 'center',
				'btn1_label'      => '',
				'btn1_url'        => '',
				'btn1_style'      => 'primary',
				'btn1_size'       => '',
				'btn1_arrow'      => 'false',
				'btn1_text_color' => 'default',
				'btn2_label'      => '',
				'btn2_url'        => '',
				'btn2_style'      => 'primary',
				'btn2_size'       => '',
				'btn2_arrow'      => 'false',
				'btn2_text_color' => 'default',
			),
			$atts,
			'bma_buttons'
		);

		$align       = in_array( $atts['align'], self::ALIGNMENTS, true ) ? $atts['align'] : 'center';
		$align_class = 'justify-' . ( 'left' === $align ? 'start' : ( 'right' === $align ? 'end' : 'center' ) );

		$buttons  = self::renderButton(
			(string) $atts['btn1_label'],
			(string) $atts['btn1_url'],
			(string) $atts['btn1_style'],
			(string) $atts['btn1_size'],
			filter_var( $atts['btn1_arrow'], FILTER_VALIDATE_BOOLEAN ),
			(string) $atts['btn1_text_color']
		);
		$buttons .= self::renderButton(
			(string) $atts['btn2_label'],
			(string) $atts['btn2_url'],
			(string) $atts['btn2_style'],
			(string) $atts['btn2_size'],
			filter_var( $atts['btn2_arrow'], FILTER_VALIDATE_BOOLEAN ),
			(string) $atts['btn2_text_color']
		);

		if ( '' === $buttons ) {
			return '';
		}

		return sprintf(
			'<div class="bma-buttons %s">%s</div>',
			esc_attr( $align_class ),
			$buttons
		);
	}

	/**
	 * Render a single button anchor.
	 *
	 * @param string $label      Button label.
	 * @param string $url        Button URL.
	 * @param string $style      One of STYLES.
	 * @param string $size       One of SIZES ('' or 'sm').
	 * @param bool   $show_arrow Append " →" to the label.
	 * @param string $text_color For transparent style: 'default' or 'white'.
	 * @return string HTML output, or '' when label or url is empty.
	 */
	public static function renderButton( string $label, string $url, string $style, string $size, bool $show_arrow, string $text_color ): string {
		$label = trim( html_entity_decode( $label, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$url   = trim( html_entity_decode( $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

		if ( '' === $label || '' === $url ) {
			return '';
		}

		$style = strtolower( trim( $style ) );
		if ( ! in_array( $style, self::STYLES, true ) ) {
			$style = 'primary';
		}

		$classes = array( 'btn', 'btn-' . $style );
		if ( 'sm' === $size ) {
			$classes[] = 'btn-sm';
		}

		if ( 'transparent' === $style ) {
			$classes[] = 'white' === strtolower( trim( $text_color ) )
				? 'btn-transparent--white'
				: 'btn-transparent--default';
		}

		$label = rtrim( $label, " \t\n\r\0\x0B→" );
		if ( $show_arrow ) {
			$label .= ' →';
		}

		// External links get target=_blank + rel noopener/noreferrer.
		// Hash-only and scheme-less same-page links stay in-tab.
		$is_external = self::isExternalUrl( $url );
		$target_attr = $is_external ? ' target="_blank" rel="noopener noreferrer"' : '';

		return sprintf(
			'<a href="%s" class="%s"%s>%s</a>',
			esc_url( $url ),
			esc_attr( implode( ' ', array_filter( $classes ) ) ),
			$target_attr,
			esc_html( $label )
		);
	}

	/**
	 * True if $url points to a different host than the current site.
	 * Fragment-only and relative links return false. Scheme-relative and
	 * absolute URLs are compared by host; mailto/tel/javascript fall
	 * through and are not marked external.
	 */
	public static function isExternalUrl( string $url ): bool {
		$url = trim( $url );
		if ( '' === $url || '#' === substr( $url, 0, 1 ) ) {
			return false;
		}
		// Non-HTTP schemes (mailto:, tel:, javascript:, sms:, etc.) — skip.
		if ( preg_match( '/^[a-z][a-z0-9+.-]*:/i', $url ) && ! preg_match( '/^https?:/i', $url ) ) {
			return false;
		}

		// Resolve relative URLs against the site URL to compare hosts.
		$url_host = wp_parse_url( $url, PHP_URL_HOST );
		if ( null === $url_host || '' === $url_host ) {
			// Relative or scheme-relative — treat as same-site.
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
			__( 'White (solid pill — for dark / gradient bg)', 'balefire' ) => 'white',
			__( 'Black', 'balefire' )                             => 'black',
			__( 'Transparent (text-only link)', 'balefire' )      => 'transparent',
		);

		$size_choices = array(
			__( 'Default (medium)', 'balefire' ) => '',
			__( 'Small (40px tall)', 'balefire' ) => 'sm',
		);

		$text_color_choices = array(
			__( 'Default (theme dark)', 'balefire' ) => 'default',
			__( 'White', 'balefire' )                => 'white',
		);

		$btn_params = function ( string $prefix ) use ( $style_choices, $size_choices, $text_color_choices ): array {
			return array(
				array(
					'type'       => 'textfield',
					'heading'    => sprintf( __( '%s Label', 'balefire' ), strtoupper( $prefix ) ),
					'param_name' => $prefix . '_label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => sprintf( __( '%s URL', 'balefire' ), strtoupper( $prefix ) ),
					'param_name' => $prefix . '_url',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => sprintf( __( '%s Style', 'balefire' ), strtoupper( $prefix ) ),
					'param_name' => $prefix . '_style',
					'value'      => $style_choices,
					'std'        => 'primary',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => sprintf( __( '%s Size', 'balefire' ), strtoupper( $prefix ) ),
					'param_name' => $prefix . '_size',
					'value'      => $size_choices,
					'std'        => '',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => sprintf( __( '%s Show Arrow (→)', 'balefire' ), strtoupper( $prefix ) ),
					'param_name' => $prefix . '_arrow',
					'value'      => array( __( 'Yes', 'balefire' ) => 'true' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => sprintf( __( '%s Text Color (transparent only)', 'balefire' ), strtoupper( $prefix ) ),
					'param_name' => $prefix . '_text_color',
					'value'      => $text_color_choices,
					'std'        => 'default',
				),
			);
		};

		vc_map(
			array(
				'name'            => __( 'Buttons', 'balefire' ),
				'base'            => 'bma_buttons',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — Up to two buttons with style, size, arrow, and alignment.', 'balefire' ),
				'icon'            => 'icon-wpb-ui-button',
				'params'          => array_merge(
					array(
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
					),
					$btn_params( 'btn1' ),
					$btn_params( 'btn2' ),
				),
			)
		);
	}
}
