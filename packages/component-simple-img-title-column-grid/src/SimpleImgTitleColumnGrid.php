<?php
/**
 * BMA Simple Image Title Column Grid shortcode (parent grid + child tile).
 *
 * Parent: [bma_simple_img_title_column_grid columns="3"] wraps child tiles.
 * Child:  [bma_simple_img_title_column_grid_item image="" title="" url=""]
 *
 * @package Balefire\Component\SimpleImgTitleColumnGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\SimpleImgTitleColumnGrid;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the image-title tile grid.
 */
final class SimpleImgTitleColumnGrid {

	/**
	 * Render the parent grid wrapper.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Child shortcodes.
	 * @return string HTML output.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns'       => '3',
				'gap'           => '6',
				'overlay_color' => '#84081c',
				'class'         => '',
			),
			$atts,
			'bma_simple_img_title_column_grid'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$cols      = self::columnCount( $atts['columns'] );
		$gap       = self::gapSize( $atts['gap'] );
		$col_class = 'lg:auto-grid-cols-' . $cols;
		$classes   = array(
			'bma-simple-img-title-column-grid',
			'bma-auto-grid',
			'auto-grid-cols-1',
			'md:auto-grid-cols-2',
			$col_class,
			'auto-grid-gap-' . $gap,
		);
		$extra     = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		$style = self::cssVarStyle( array( '--bma-simple-img-title-column-grid-hover-overlay' => $atts['overlay_color'] ) );

		return sprintf(
			'<div class="%1$s"%2$s>%3$s</div>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			$style,
			do_shortcode( (string) $content )
		);
	}

	/**
	 * Render one child tile.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Optional body content (unused by default).
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'image'         => '',
				'title'         => '',
				'link'          => '',
				'url'           => '',
				'target'        => '',
				'overlay_color' => '',
				'class'         => '',
			),
			$atts,
			'bma_simple_img_title_column_grid_item'
		);

		$title = trim( (string) $atts['title'] );
		$link  = self::parseLink( (string) $atts['link'] );
		$url   = '' !== $link['url'] ? esc_url( $link['url'] ) : esc_url( trim( (string) $atts['url'] ) );
		if ( '' === $title && '' === trim( (string) $atts['image'] ) ) {
			return '';
		}

		$image_html = self::imageHtml( $atts['image'], $title );
		$classes    = array( 'bma-simple-img-title-column-grid__item' );
		$extra      = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		$style_vars = array();
		if ( '' !== trim( (string) $atts['overlay_color'] ) ) {
			$style_vars['--bma-simple-img-title-column-grid-hover-overlay'] = $atts['overlay_color'];
		}
		$style = self::cssVarStyle( $style_vars );

		$inner  = '<span class="bma-simple-img-title-column-grid__media">' . $image_html . '</span>';
		$inner .= '<span class="bma-simple-img-title-column-grid__shade" aria-hidden="true"></span>';
		if ( '' !== $title ) {
			$inner .= '<span class="bma-simple-img-title-column-grid__title">' . esc_html( $title ) . '</span>';
		}

		if ( '' !== $url ) {
			$target = '' !== $link['target'] ? $link['target'] : (string) $atts['target'];
			$target = in_array( $target, array( '_blank', '_self' ), true ) ? $target : '_self';
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

	/** Register shortcodes. */
	public static function register(): void {
		add_shortcode( 'bma_simple_img_title_column_grid', array( self::class, 'render' ) );
		add_shortcode( 'bma_simple_img_title_column_grid_item', array( self::class, 'renderItem' ) );
	}

	/** Register WPBakery elements. */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'Image Over Grid', 'balefire' ),
				'base'                    => 'bma_simple_img_title_column_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_SimpleImgTitleColumnGrid',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Image title tiles with color overlay hover.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'bma_simple_img_title_column_grid_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => array( '3', '2', '4', '5', '6' ),
						'std'        => '3',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Gap', 'balefire' ),
						'param_name' => 'gap',
						'value'      => array(
							'20px' => '5',
							'24px' => '6',
							'48px' => '12',
						),
						'std'        => '6',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay color', 'balefire' ),
						'param_name'  => 'overlay_color',
						'value'       => '#84081c',
						'description' => __( 'Default matches the David Tours deep red hover state.', 'balefire' ),
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
				'name'            => __( 'Image Title Tile', 'balefire' ),
				'base'            => 'bma_simple_img_title_column_grid_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_SimpleImgTitleColumnGridItem',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single image tile with title overlay.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_simple_img_title_column_grid' ),
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
						'type'       => 'textfield',
						'heading'    => __( 'URL fallback', 'balefire' ),
						'param_name' => 'url',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Target', 'balefire' ),
						'param_name' => 'target',
						'value'      => array(
							'Same tab' => '_self',
							'New tab'  => '_blank',
						),
						'std'        => '_self',
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
				'WPBakeryShortCode_BMA_SimpleImgTitleColumnGrid',
				array()
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_SimpleImgTitleColumnGridItem',
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
		if ( ! class_exists( 'WPBakeryShortCode_BMA_SimpleImgTitleColumnGrid' ) ) {
			eval( 'class WPBakeryShortCode_BMA_SimpleImgTitleColumnGrid extends \WPBakeryShortCodesContainer {}' );
		}
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
	 * Resolve the image HTML for an attachment ID.
	 *
	 * @param mixed  $image Attachment ID.
	 * @param string $title Fallback alt/title context.
	 * @return string
	 */
	private static function imageHtml( $image, string $title ): string {
		if ( empty( $image ) || ! is_numeric( $image ) ) {
			return '';
		}

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
				'class'    => 'bma-simple-img-title-column-grid__img',
				'alt'      => $alt,
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);

		return $html ? $html : '';
	}

	/**
	 * Parse/sanitize supported desktop column counts.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	private static function columnCount( $value ): int {
		$count = (int) $value;
		return in_array( $count, array( 2, 3, 4, 5, 6 ), true ) ? $count : 3;
	}

	/**
	 * Parse/sanitize supported auto-grid gap classes.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function gapSize( $value ): string {
		$gap = (string) $value;
		return in_array( $gap, array( '5', '6', '12' ), true ) ? $gap : '6';
	}

	/**
	 * Build inline CSS custom property declarations for validated colors.
	 *
	 * @param array<string,mixed> $vars CSS variable map.
	 * @return string Attribute string, including leading space, or ''.
	 */
	private static function cssVarStyle( array $vars ): string {
		$decls = array();
		foreach ( $vars as $name => $value ) {
			$color = self::sanitizeCssColor( (string) $value );
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
