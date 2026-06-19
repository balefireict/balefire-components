<?php
/**
 * BMA Image Title Columns shortcode (parent grid + child tile).
 *
 * Parent: [bma_image_title_columns] wraps child tiles. Owns its own responsive
 *         flex grid (bespoke breakpoints that don't match component-auto-grid's
 *         fixed tiers): flex-wrap + justify-content:center so an uneven last
 *         row centers, align-items:flex-start so unequal titles keep the image
 *         tops aligned. Responsive: 1 col (<768px), 3 cols (768-1279px),
 *         4 cols (>=1280px).
 * Child:  [bma_image_title_columns_item image="" title=""]
 *
 * @package Balefire\Component\ImageTitleColumns
 */

declare( strict_types=1 );

namespace Balefire\Component\ImageTitleColumns;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the image-title tile grid.
 */
final class ImageTitleColumns {

	/**
	 * Register the parent + child shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_image_title_columns', 'bma_image_title_columns_shortcode' );
		add_shortcode( 'bma_image_title_columns_item', 'bma_image_title_columns_item_shortcode' );
	}

	/**
	 * Render the parent grid wrapper.
	 *
	 * Emits the component-auto-grid class set for the responsive flex grid plus
	 * the component's own root class. Bare output (no container/max-width) —
	 * the wrapping vc_row owns background + padding.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Child shortcodes.
	 * @return string HTML output (empty string if no children).
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$inner = self::manualInner( $content );
		if ( '' === trim( $inner ) ) {
			return '';
		}

		// Owns its own flex grid (component-auto-grid's fixed tiers don't match
		// the bespoke 1/3/4 breakpoints): flex-wrap + justify-center centers an
		// uneven last row; align-items:flex-start keeps tile tops aligned.
		$class = 'image-title-columns';
		$extra = trim( (string) self::attr( $atts, 'class' ) );
		if ( '' !== $extra ) {
			$class .= ' ' . sanitize_html_class( $extra );
		}

		return sprintf( '<div class="%s">%s</div>', esc_attr( $class ), $inner );
	}

	/**
	 * Render one child tile (image on top, title below).
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Unused (items are image + title only).
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'image'  => '',
				'title'  => '',
				'url'    => '',
				'target' => 'site',
				'class'  => '',
			),
			$atts,
			'bma_image_title_columns_item'
		);

		$title      = trim( (string) self::attr( $atts, 'title' ) );
		$image_raw  = trim( (string) self::attr( $atts, 'image' ) );
		$image_html = self::imageHtml( $image_raw, $title );

		if ( '' === $title && '' === $image_html ) {
			return '';
		}

		$class = 'image-title-columns__item';
		$extra = trim( (string) self::attr( $atts, 'class' ) );
		if ( '' !== $extra ) {
			$class .= ' ' . sanitize_html_class( $extra );
		}

		// Optional link: wrap the whole tile in an <a> when url is set.
		$url          = trim( (string) self::attr( $atts, 'url' ) );
		$target_blank = 'new' === trim( (string) self::attr( $atts, 'target' ) );
		$tag          = '' !== $url ? 'a' : 'div';
		$open_attrs   = '';
		if ( '' !== $url ) {
			$open_attrs = ' href="' . esc_url( $url ) . '"';
			if ( $target_blank ) {
				$open_attrs .= ' target="_blank" rel="noopener noreferrer"';
			}
		}

		ob_start();
		?>
		<<?php echo esc_html( $tag ); ?> class="<?php echo esc_attr( $class ); ?>"<?php echo $open_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( '' !== $image_html ) : ?>
				<div class="image-title-columns__media"><?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h3 class="image-title-columns__title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Process manual child shortcode content into rendered tile HTML.
	 *
	 * @param string|null $content Raw child shortcodes.
	 * @return string
	 */
	private static function manualInner( ?string $content ): string {
		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		// Strip stray <br> WPBakery may insert between sibling child tiles.
		$inner = preg_replace( '/(<\/div>)\s*<br\s*\/?>/i', '$1', (string) $inner );

		return (string) $inner;
	}

	/**
	 * Resolve the tile image HTML for an attachment ID or URL.
	 *
	 * @param string $image Attachment ID or URL.
	 * @param string $title Fallback alt text context.
	 * @return string
	 */
	private static function imageHtml( string $image, string $title ): string {
		$image = trim( $image );
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
				'full',
				false,
				array(
					'class'    => 'image-title-columns__img',
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);

			return $html ? $html : '';
		}

		return '<img class="image-title-columns__img" src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" decoding="async" />';
	}

	/**
	 * Register the WPBakery parent + child elements.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		// Parent — the grid container.
		vc_map(
			array(
				'name'                    => __( 'Image Title Columns', 'balefire' ),
				'base'                    => 'bma_image_title_columns',
				'php_class_name'          => 'WPBakeryShortCode_BMA_ImageTitleColumns',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — responsive grid of image tiles with a centered title beneath each. Uneven last row centers.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'bma_image_title_columns_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'description' => __( 'Label shown in the page builder only — not rendered on the front end.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Optional extra CSS class on the grid wrapper.', 'balefire' ),
					),
				),
			)
		);

		// Child — one image + title tile.
		vc_map(
			array(
				'name'            => __( 'Image Title Tile', 'balefire' ),
				'base'            => 'bma_image_title_columns_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_ImageTitleColumnsItem',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — a single image tile with a centered title beneath it.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_image_title_columns' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Image', 'balefire' ),
						'param_name'  => 'image',
						'admin_label' => true,
						'description' => __( 'Image shown at the top of the tile (cropped to a 3:2 box).', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'admin_label' => true,
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Link URL', 'balefire' ),
						'param_name'  => 'url',
						'admin_label' => true,
						'description' => __( 'Optional. Wraps the whole tile in a link.', 'balefire' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Link target', 'balefire' ),
						'param_name'  => 'target',
						'value'       => array(
							__( 'Same Window', 'balefire' ) => 'site',
							__( 'New Window', 'balefire' )  => 'new',
						),
						'std'         => 'site',
						'dependency'  => array(
							'element'   => 'url',
							'not_empty' => true,
						),
						'description' => __( 'Same Window opens in the current tab; New Window opens in a new tab.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Optional extra CSS class on the tile.', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakery preview/editor classes (soft-dep on bakery-preview).
	 */
	public static function registerPreviewClasses(): void {
		if ( class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_ImageTitleColumns',
				array(
					'title' => 'title',
				)
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_ImageTitleColumnsItem',
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
		if ( ! class_exists( 'WPBakeryShortCode_BMA_ImageTitleColumns' ) ) {
			eval( 'class WPBakeryShortCode_BMA_ImageTitleColumns extends \\\\WPBakeryShortCodesContainer {}' );
		}
	}

	/**
	 * Read a value that may have been written by WPBakery with underscores
	 * converted to hyphens.
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
}
