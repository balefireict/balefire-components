<?php
/**
 * BMA Buttons shortcode (parent group + child button).
 *
 * Parent: [bma_buttons align="center|left|right"] wraps child buttons.
 * Child:  [bma_button type="default|phone" label="" url="" style="" size="" arrow="" icon=""]
 *
 * Each child is either a Default button (own style/size/arrow/text-color and
 * an optional calendar or custom icon) or a Phone link (phone-icon SVG +
 * number from the acffg_phone ACF options field, rendered as a tel:+1- link).
 * External links auto-detect target="_blank".
 *
 * @package Balefire\Component\Buttons
 */

declare( strict_types=1 );

namespace Balefire\Component\Buttons;

defined( 'ABSPATH' ) || exit;

/**
 * Static buttons shortcode renderer — parent group + child button.
 *
 * @package Balefire\Component\Buttons
 */
final class Buttons {

	public const STYLES      = array( 'primary', 'secondary', 'transparent', 'white', 'black' );
	public const SIZES       = array( '', 'sm' );
	public const ALIGNMENTS  = array( 'left', 'center', 'right' );

	/**
	 * Render the parent [bma_buttons] wrapper.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child [bma_button] shortcodes.
	 * @return string HTML output.
	 */
	public static function render( array $atts, ?string $content = null ): string {
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
		$inner       = '';

		if ( null !== $content && '' !== trim( (string) $content ) ) {
			$inner = do_shortcode( (string) $content );
		} else {
			$buttons = self::parseButtonGroupAtts( (string) $atts['buttons'] );
			foreach ( $buttons as $button_atts ) {
				$inner .= self::renderButton( $button_atts );
			}
		}

		if ( '' === trim( $inner ) ) {
			return '';
		}

		return sprintf(
			'<div class="bma-buttons %s">%s</div>',
			esc_attr( $align_class ),
			$inner
		);
	}

	/**
	 * Parse WPBakery param_group button data.
	 *
	 * @param string $buttons Encoded param_group value.
	 * @return array<int,array<string,mixed>>
	 */
	private static function parseButtonGroupAtts( string $buttons ): array {
		$buttons = trim( $buttons );
		if ( '' === $buttons ) {
			return array();
		}

		if ( function_exists( 'vc_param_group_parse_atts' ) ) {
			$parsed = vc_param_group_parse_atts( $buttons );
			return is_array( $parsed ) ? $parsed : array();
		}

		$decoded = json_decode( urldecode( $buttons ), true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Render a single child [bma_button] anchor.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when label or url is empty.
	 */
	public static function renderButton( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'type'        => 'default',
				'label'       => '',
				'url'         => '',
				'style'       => 'primary',
				'size'        => 'md',
				'arrow'       => 'false',
				'text_color'  => 'default',
				'icon'        => '',
				'icon_custom' => '',
				'phone'       => '',
				'phone_field' => 'acffg_phone',
			),
			$atts,
			'bma_button'
		);

		// Phone type renders a phone-icon + tel: link; the button fields below
		// are irrelevant, so branch before label/url validation.
		$type = strtolower( trim( (string) $atts['type'] ) );
		if ( 'phone' === $type ) {
			return self::renderPhone( $atts );
		}

		$label = trim( html_entity_decode( (string) $atts['label'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$url   = trim( html_entity_decode( (string) $atts['url'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

		if ( '' === $label || '' === $url ) {
			return '';
		}

		$style = strtolower( trim( (string) $atts['style'] ) );
		if ( ! in_array( $style, self::STYLES, true ) ) {
			$style = 'primary';
		}

		$size = strtolower( trim( (string) $atts['size'] ) );

		$classes = array( 'btn', 'btn-' . $style );
		if ( 'sm' === $size ) {
			$classes[] = 'btn-sm';
		}

		$text_color = strtolower( trim( (string) $atts['text_color'] ) );
		if ( 'transparent' === $style ) {
			$allowed_text_colors = array( 'default', 'white', 'primary', 'secondary' );
			if ( ! in_array( $text_color, $allowed_text_colors, true ) ) {
				$text_color = 'default';
			}
			$classes[] = 'btn-transparent--' . $text_color;
		}

		$icon_html = self::renderIcon(
			(string) $atts['icon'],
			(string) $atts['icon_custom']
		);

		$show_arrow = filter_var( $atts['arrow'], FILTER_VALIDATE_BOOLEAN );
		// Strip trailing arrow characters from the label (multibyte-safe).
		// PHP rtrim() can't strip multibyte chars reliably.
		$label = preg_replace( '/[\s\x{2192}\x{202F}]+$/u', '', $label );

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
	 * Render a "Phone" type button: phone-icon SVG + the phone number as a
	 * tel: link. The number resolves from the manual `phone` att, falling back
	 * to the ACF options field named by `phone_field` (default acffg_phone).
	 * Returns '' when no number is available so no dead anchor renders.
	 *
	 * The link inherits its color from context (currentColor) so the icon and
	 * text always match — consumer themes color .bma-btn-phone for their
	 * secondary/brand color.
	 *
	 * @param array $atts Shortcode attributes (phone, phone_field).
	 * @return string HTML, or '' when no phone number.
	 */
	public static function renderPhone( array $atts ): string {
		$phone = trim( (string) $atts['phone'] );
		if ( '' === $phone ) {
			$phone = trim( (string) self::acfOption( (string) $atts['phone_field'] ) );
		}
		if ( '' === $phone ) {
			return '';
		}

		$tel  = self::phoneTel( $phone );
		// Reuse the phone device icon (currentColor) — same path as the SVG ref.
		$icon = self::renderIcon( 'phone' );

		return sprintf(
			'<a class="bma-btn-phone" href="%s">%s%s</a>',
			esc_url( 'tel:' . $tel ),
			$icon,
			esc_html( $phone )
		);
	}

	/**
	 * Read a site-wide ACF options field. Soft dependency — returns '' when
	 * ACF is absent, the field name is empty, or no value is stored.
	 *
	 * @param string $field ACF options field name.
	 * @return mixed Resolved value, or '' when none.
	 */
	private static function acfOption( string $field ) {
		$field = trim( $field );
		if ( '' === $field || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$value = get_field( $field, 'option' );
		return ( null === $value || false === $value ) ? '' : $value;
	}

	/**
	 * Build a tel: URI body from a phone string. Strips to digits and applies
	 * a +1 country code when missing (10-digit → +1…; 11-digit starting with 1
	 * → +1…). Mirrors the acffg_phone → tel:+1-{phone} intent.
	 *
	 * @param string $phone Raw phone string.
	 * @return string tel: URI body, e.g. "+187****4395".
	 */
	private static function phoneTel( string $phone ): string {
		$digits = preg_replace( '/[^0-9]/', '', $phone );
		if ( null === $digits ) {
			$digits = '';
		}
		if ( 11 === strlen( $digits ) && isset( $digits[0] ) && '1' === $digits[0] ) {
			return '+' . $digits;
		}
		return '+1' . $digits;
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
				// ponytail: calendar+check icon from layouts/sections/layout-buttons.svg.
				return '<span class="btn-icon" aria-hidden="true">'
					. '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="1em" height="1em">'
					. '<path d="M20,1.667V20H0V1.667H2.5V2.5a1.667,1.667,0,0,0,3.334,0V1.667h8.334V2.5a1.667,1.667,0,1,0,3.334,0V1.667Zm-1.667,5H1.667V18.335H18.335ZM16.668.833A.833.833,0,0,0,15,.833V2.5a.833.833,0,0,0,1.667,0ZM5,2.5a.833.833,0,1,1-1.667,0V.833A.833.833,0,1,1,5,.833Zm.833,9.775.713-.659a13.772,13.772,0,0,1,2.3,1.378,19.9,19.9,0,0,1,5.089-4.36l.233.533A22.628,22.628,0,0,0,9.2,15.835,35.663,35.663,0,0,0,5.834,12.275Z"/>'
					. '</svg></span>';

			case 'phone':
				// ponytail: phone icon from layouts/sections/layout-buttons-alt.svg.
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

	/** Register both shortcodes. */
	public static function register(): void {
		add_shortcode( 'bma_buttons', array( self::class, 'render' ) );
		add_shortcode( 'bma_button', array( self::class, 'renderButton' ) );
	}

	/**
	 * Shared WPBakery fields for one button inside the Buttons param_group.
	 *
	 * @param array<string,string> $style_choices Style dropdown choices.
	 * @param array<string,string> $size_choices Size dropdown choices.
	 * @param array<string,string> $text_color_choices Transparent text color choices.
	 * @param array<string,string> $icon_choices Icon dropdown choices.
	 * @return array<int,array<string,mixed>>
	 */
	private static function buttonParamGroupFields( array $style_choices, array $size_choices, array $text_color_choices, array $icon_choices ): array {
		return array(
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Type', 'balefire' ),
				'param_name'  => 'type',
				'value'       => array(
					__( 'Default (button)', 'balefire' )    => 'default',
					__( 'Phone (icon + number)', 'balefire' ) => 'phone',
				),
				'std'         => 'default',
				'admin_label' => true,
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Label', 'balefire' ),
				'param_name'  => 'label',
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
				'std'        => 'md',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Show Arrow (→)', 'balefire' ),
				'param_name' => 'arrow',
				'value'      => array(
					__( 'No', 'balefire' )  => 'false',
					__( 'Yes', 'balefire' ) => 'true',
				),
				'std'        => 'false',
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
				'type'        => 'attach_image',
				'heading'     => __( 'Custom Icon Image', 'balefire' ),
				'param_name'  => 'icon_custom',
				'description' => __( 'Upload an icon. Only used when Icon is set to Custom.', 'balefire' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Phone Number', 'balefire' ),
				'param_name'  => 'phone',
				'description' => __( 'Override the site-wide phone (acffg_phone). Leave empty to use the global field.', 'balefire' ),
			),
		);
	}

	/** Register WPBakery elements (Buttons wrapper + legacy child button). */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$style_choices = array(
			__( 'Primary (filled, theme color)', 'balefire' )     => 'primary',
			__( 'Secondary (outlined)', 'balefire' )              => 'secondary',
			__( 'White (solid)', 'balefire' )                     => 'white',
			__( 'Black', 'balefire' )                             => 'black',
			__( 'Transparent (text-only link)', 'balefire' )      => 'transparent',
		);

		$size_choices = array(
			__( 'Default', 'balefire' ) => 'md',
			__( 'Small', 'balefire' )   => 'sm',
		);

		$text_color_choices = array(
			__( 'Default (theme dark)', 'balefire' )        => 'default',
			__( 'White', 'balefire' )                       => 'white',
			__( 'Primary (theme primary)', 'balefire' )     => 'primary',
			__( 'Secondary (theme secondary)', 'balefire' ) => 'secondary',
		);

		// 'phone' removed from the icon picker — use Type → Phone instead.
		// renderIcon( 'phone' ) stays for back-compat with existing
		// [bma_button icon="phone"] content.
		$icon_choices = array(
			__( 'None', 'balefire' )     => '',
			__( 'Calendar', 'balefire' ) => 'calendar',
			__( 'Custom', 'balefire' )   => 'custom',
		);

		// Parent: button group container. This is intentionally a real
		// WPBakery container so the Backend Editor shows a Buttons wrapper with
		// sortable Button children, not a param_group field.
		vc_map(
			array(
				'name'                    => __( 'Buttons', 'balefire' ),
				'base'                    => 'bma_buttons',
				'php_class_name'          => 'WPBakeryShortCode_BMA_Buttons',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Button group with alignment. Add Button items inside.', 'balefire' ),
				'icon'                    => 'icon-wpb-ui-button',
				'as_parent'               => array( 'only' => 'bma_button' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
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
			)
		);

		// Child button. Add these inside the Buttons wrapper so the editor flow is
		// "Buttons" (alignment/group) -> one or more "Button" items.
		vc_map(
			array(
				'name'            => __( 'Button', 'balefire' ),
				'base'            => 'bma_button',
				'php_class_name'  => 'WPBakeryShortCode_BMA_Button',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — Button item. Add inside the Buttons wrapper for alignment and multiple CTAs.', 'balefire' ),
				'icon'            => 'icon-wpb-ui-button',
				'as_child'        => array( 'only' => 'bma_buttons' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Type', 'balefire' ),
						'param_name'  => 'type',
						'value'       => array(
							__( 'Default (button)', 'balefire' )    => 'default',
							__( 'Phone (icon + number)', 'balefire' ) => 'phone',
						),
						'std'         => 'default',
						'description' => __( 'Phone reads the site-wide acffg_phone field and renders a tel:+1- link. The button fields below are hidden for Phone.', 'balefire' ),
						'admin_label' => true,
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Label', 'balefire' ),
						'param_name'  => 'label',
						'admin_label' => true,
						'dependency'  => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'URL', 'balefire' ),
						'param_name' => 'url',
						'dependency' => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Style', 'balefire' ),
						'param_name' => 'style',
						'value'      => $style_choices,
						'std'        => 'primary',
						'dependency' => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Size', 'balefire' ),
						'param_name' => 'size',
						'value'      => $size_choices,
						'std'        => 'md',
						'dependency' => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Show Arrow (→)', 'balefire' ),
						'param_name' => 'arrow',
						'value'      => array(
							__( 'No', 'balefire' )  => 'false',
							__( 'Yes', 'balefire' ) => 'true',
						),
						'std'        => 'false',
						'dependency' => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Text Color (transparent only)', 'balefire' ),
						'param_name' => 'text_color',
						'value'      => $text_color_choices,
						'std'        => 'default',
						'dependency' => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Icon Before Text', 'balefire' ),
						'param_name' => 'icon',
						'value'      => $icon_choices,
						'std'        => '',
						'dependency' => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Custom Icon Image', 'balefire' ),
						'param_name'  => 'icon_custom',
						'description' => __( 'Upload an icon. Only used when Icon is set to Custom.', 'balefire' ),
						'dependency'  => array( 'element' => 'type', 'value' => array( 'default' ) ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Phone Number', 'balefire' ),
						'param_name'  => 'phone',
						'description' => __( 'Override the site-wide phone (acffg_phone). Leave empty to use the global field.', 'balefire' ),
						'admin_label' => true,
						'dependency'  => array( 'element' => 'type', 'value' => array( 'phone' ) ),
					),
				),
			)
		);
	}

	/** Register WPBakery preview/editor container classes. */
	public static function registerPreviewClasses(): void {
		if ( class_exists( '\Balefire\Component\BakeryPreview\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_Buttons',
				array()
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_Button',
				array(
					'label' => 'label',
				)
			);
			return;
		}

		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_Buttons' ) ) {
			eval( 'class WPBakeryShortCode_BMA_Buttons extends \WPBakeryShortCodesContainer {}' );
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_Button' ) && class_exists( 'WPBakeryShortCode' ) ) {
			eval( 'class WPBakeryShortCode_BMA_Button extends \WPBakeryShortCode {}' );
		}
	}

	/** Allow Buttons containers inside WPBakery nested row columns. */
	public static function allowContainersInInnerColumns(): void {
		if ( function_exists( 'vc_map_update' ) ) {
			vc_map_update( 'vc_column_inner', array( 'allowed_container_element' => true ) );
		}
	}
}
