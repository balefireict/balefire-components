<?php
/**
 * BMA Portrait Grid shortcode (parent grid + child tile).
 *
 * Parent: [bma_portrait_grid] wraps child portrait tiles.
 * Child:  [bma_portrait_grid_item image="" title="" link=""]
 *
 * @package Balefire\Component\PortraitGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\PortraitGrid;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the portrait tile grid.
 */
final class PortraitGrid {

	/**
	 * Render the parent grid wrapper.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Child shortcodes.
	 * @return string HTML output.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = self::normalizeAtts( $atts );
		$atts = shortcode_atts(
			array(
				'overlay_color'   => '#00338f',
				'overlay_opacity' => '0.862',
				'class'           => '',
			),
			$atts,
			'bma_portrait_grid'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$classes = array(
			'bma-portrait-grid',
			'bma-auto-grid',
			'auto-grid-cols-1',
			'md:auto-grid-cols-2',
			'lg:auto-grid-cols-3',
			'auto-grid-gap-6',
		);
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		$style = self::cssVarStyle(
			array(
				'--bma-portrait-grid-overlay-color'   => self::attr( $atts, 'overlay_color' ),
				'--bma-portrait-grid-overlay-opacity' => self::attr( $atts, 'overlay_opacity' ),
			)
		);

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = preg_replace( '/^\s*<br\s*\/?>\s*/i', '', (string) $inner );
		$inner = preg_replace( '/<br\s*\/?>\s*(?=<(?:a|div)\s+class="bma-portrait-grid__item\b)/i', '', (string) $inner );
		$inner = preg_replace( '/(<\/(?:a|div)>)\s*<br\s*\/?>/i', '$1', (string) $inner );

		if ( '' === trim( (string) $inner ) ) {
			return '';
		}

		return sprintf(
			'<div class="%1$s"%2$s>%3$s</div>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			$style,
			trim( (string) $inner )
		);
	}

	/**
	 * Render one child tile.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Optional body content (unused by default).
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = self::normalizeAtts( $atts );
		$atts = shortcode_atts(
			array(
				'image'         => '',
				'title'         => '',
				'link'          => '',
				'overlay_color' => '',
				'class'         => '',
			),
			$atts,
			'bma_portrait_grid_item'
		);

		$title = trim( (string) self::attr( $atts, 'title' ) );
		$link  = self::parseLink( (string) self::attr( $atts, 'link' ) );
		$url   = esc_url( $link['url'] );

		if ( '' === $title && '' === trim( (string) self::attr( $atts, 'image' ) ) ) {
			return '';
		}

		$image_html = self::imageHtml( self::attr( $atts, 'image' ), $title );
		$classes    = array( 'bma-portrait-grid__item' );
		$extra      = trim( (string) self::attr( $atts, 'class' ) );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		$style_vars = array();
		if ( '' !== trim( (string) self::attr( $atts, 'overlay_color' ) ) ) {
			$style_vars['--bma-portrait-grid-overlay-color'] = self::attr( $atts, 'overlay_color' );
		}
		$style = self::cssVarStyle( $style_vars );

		$inner  = '<span class="bma-portrait-grid__media">' . $image_html . '</span>';
		$inner .= '<span class="bma-portrait-grid__wash" aria-hidden="true"></span>';
		$inner .= '<span class="bma-portrait-grid__color" aria-hidden="true"></span>';
		$inner .= '<span class="bma-portrait-grid__shade" aria-hidden="true"></span>';
		if ( '' !== $title ) {
			$inner .= '<h3 class="bma-portrait-grid__title">' . esc_html( $title ) . '</h3>';
		}

		if ( '' !== $url ) {
			$target = in_array( $link['target'], array( '_blank', '_self' ), true ) ? $link['target'] : '_self';
			$rel    = '_blank' === $target ? ' rel="noopener noreferrer"' : '';
			return sprintf(
				'<a class="%1$s" href="%2$s" target="%3$s"%4$s%5$s>%6$s</a>',
				esc_attr( implode( ' ', array_unique( $classes ) ) ),
				$url,
				esc_attr( $target ),
				$rel,
				$style,
				$inner
			);
		}

		return sprintf(
			'<div class="%1$s"%2$s>%3$s</div>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			$style,
			$inner
		);
	}

	/** Register shortcodes, including manual typo/hyphen aliases. */
	public static function register(): void {
		add_shortcode( 'bma_portrait_grid', 'bma_portrait_grid_shortcode' );
		add_shortcode( 'bma_portrait_grid_item', 'bma_portrait_grid_item_shortcode' );
		add_shortcode( 'bma_protrait_grid', 'bma_portrait_grid_shortcode' );
		add_shortcode( 'bma_protrait_grid_item', 'bma_portrait_grid_item_shortcode' );
		add_shortcode( 'bma-portrait-grid', 'bma_portrait_grid_shortcode' );
		add_shortcode( 'bma-portrait-grid-item', 'bma_portrait_grid_item_shortcode' );
		add_shortcode( 'bma-protrait-grid', 'bma_portrait_grid_shortcode' );
		add_shortcode( 'bma-protrait-grid-item', 'bma_portrait_grid_item_shortcode' );
	}

	/** Register WPBakery elements. */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'Portrait Grid', 'balefire' ),
				'base'                    => 'bma_portrait_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_PortraitGrid',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — 3-column portrait image grid with color overlay hover.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'bma_portrait_grid_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay color', 'balefire' ),
						'param_name'  => 'overlay_color',
						'value'       => '#00338f',
						'description' => __( 'Default matches the David Tours blue overlay in the portrait grid reference.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay opacity', 'balefire' ),
						'param_name'  => 'overlay_opacity',
						'value'       => '0.862',
						'description' => __( 'Use a decimal from 0 to 1.', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra class', 'balefire' ),
						'param_name' => 'class',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Portrait Tile', 'balefire' ),
				'base'            => 'bma_portrait_grid_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_PortraitGridItem',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single portrait image tile with title and link.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_portrait_grid' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Image', 'balefire' ),
						'param_name' => 'image',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
					),
					array(
						'type'       => 'vc_link',
						'heading'    => __( 'Link', 'balefire' ),
						'param_name' => 'link',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay color override', 'balefire' ),
						'param_name'  => 'overlay_color',
						'description' => __( 'Optional. Leave blank to inherit the parent grid color.', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra class', 'balefire' ),
						'param_name' => 'class',
					),
				),
			)
		);
	}

	/** Register WPBakery preview/editor classes. */
	public static function registerPreviewClasses(): void {
		if ( class_exists( '\Balefire\Component\BakeryPreview\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_PortraitGrid',
				array()
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_PortraitGridItem',
				array(
					'image' => 'image',
					'title' => 'title',
				)
			);
			return;
		}

		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_PortraitGrid' ) ) {
			eval( 'class WPBakeryShortCode_BMA_PortraitGrid extends \\WPBakeryShortCodesContainer {}' );
		}
	}

	/**
	 * Read a value that may have been written by WPBakery with underscores converted to hyphens.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $name Canonical snake_case param name.
	 * @return string
	 */
	private static function attr( array $atts, string $name ): string {
		$hyphen = str_replace( '_', '-', $name );
		foreach ( array( $name, $hyphen ) as $key ) {
			if ( isset( $atts[ $key ] ) && '' !== (string) $atts[ $key ] ) {
				return (string) $atts[ $key ];
			}
		}

		return '';
	}

	/**
	 * Normalize WPBakery hyphenated shortcode attributes back to canonical snake_case.
	 *
	 * @param array $atts Raw shortcode attributes.
	 * @return array
	 */
	private static function normalizeAtts( array $atts ): array {
		foreach ( array( 'overlay_color', 'overlay_opacity' ) as $name ) {
			$hyphen = str_replace( '_', '-', $name );
			if ( ! isset( $atts[ $name ] ) && isset( $atts[ $hyphen ] ) ) {
				$atts[ $name ] = $atts[ $hyphen ];
			}
		}

		return $atts;
	}

	/**
	 * Parse a WPBakery vc_link attribute into URL and target pieces.
	 *
	 * @param string $raw Raw vc_link value.
	 * @return array{url:string,target:string}
	 */
	private static function parseLink( string $raw ): array {
		$result = array(
			'url'    => '',
			'target' => '',
		);

		if ( '' === trim( $raw ) ) {
			return $result;
		}

		if ( function_exists( 'vc_build_link' ) ) {
			$link = vc_build_link( $raw );
			if ( is_array( $link ) ) {
				$result['url']    = isset( $link['url'] ) ? trim( (string) $link['url'] ) : '';
				$result['target'] = isset( $link['target'] ) ? trim( (string) $link['target'] ) : '';
				return $result;
			}
		}

		parse_str( html_entity_decode( $raw ), $parts );
		$result['url']    = isset( $parts['url'] ) ? trim( (string) $parts['url'] ) : '';
		$result['target'] = isset( $parts['target'] ) ? trim( (string) $parts['target'] ) : '';

		return $result;
	}

	/**
	 * Resolve the image HTML for an attachment ID or URL.
	 *
	 * @param mixed  $image Attachment ID or URL.
	 * @param string $title Fallback alt/title context.
	 * @return string
	 */
	private static function imageHtml( $image, string $title ): string {
		$image = trim( (string) $image );
		if ( '' === $image ) {
			return '';
		}

		if ( is_numeric( $image ) ) {
			$image_id = (int) $image;
			$alt      = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( '' === trim( $alt ) ) {
				$alt = $title;
			}

			$html = wp_get_attachment_image(
				$image_id,
				'large',
				false,
				array(
					'class'    => 'bma-portrait-grid__img',
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);

			return $html ? $html : '';
		}

		return '<img class="bma-portrait-grid__img" src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" decoding="async" />';
	}

	/**
	 * Build inline CSS custom property declarations for validated colors/numbers.
	 *
	 * @param array<string,mixed> $vars CSS variable map.
	 * @return string Attribute string, including leading space, or ''.
	 */
	private static function cssVarStyle( array $vars ): string {
		$decls = array();
		foreach ( $vars as $name => $value ) {
			$value = (string) $value;
			if ( str_ends_with( $name, '-opacity' ) ) {
				$opacity = self::sanitizeOpacity( $value );
				if ( '' !== $opacity ) {
					$decls[] = $name . ':' . $opacity;
				}
				continue;
			}

			$color = self::sanitizeCssColor( $value );
			if ( '' !== $color ) {
				$decls[] = $name . ':' . $color;
			}
		}

		if ( empty( $decls ) ) {
			return '';
		}

		return ' style="' . esc_attr( implode( ';', $decls ) ) . '"';
	}

	/**
	 * Sanitize decimal opacity values.
	 *
	 * @param string $value Raw opacity.
	 * @return string Safe opacity or ''.
	 */
	private static function sanitizeOpacity( string $value ): string {
		$value = trim( $value );
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$opacity = (float) $value;
		if ( $opacity < 0 || $opacity > 1 ) {
			return '';
		}

		return rtrim( rtrim( sprintf( '%.3F', $opacity ), '0' ), '.' );
	}

	/**
	 * Conservative CSS color sanitizer for hex/rgb(a)/hsl(a)/named currentColor.
	 *
	 * @param string $value Raw color.
	 * @return string Safe color or ''.
	 */
	private static function sanitizeCssColor( string $value ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $value ) ) {
			return $value;
		}
		if ( preg_match( '/^rgba?\(\s*[0-9.]+%?\s*,\s*[0-9.]+%?\s*,\s*[0-9.]+%?(?:\s*,\s*(?:0|1|0?\.[0-9]+))?\s*\)$/i', $value ) ) {
			return $value;
		}
		if ( preg_match( '/^hsla?\(\s*[0-9.]+(?:deg)?\s*,\s*[0-9.]+%\s*,\s*[0-9.]+%(?:\s*,\s*(?:0|1|0?\.[0-9]+))?\s*\)$/i', $value ) ) {
			return $value;
		}
		if ( 'currentColor' === $value ) {
			return $value;
		}
		return '';
	}
}
