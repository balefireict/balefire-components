<?php
/**
 * BMA Logo Grid shortcode (parent + child).
 *
 * Parent: [bma_logo_grid columns="5"]…[/bma_logo_grid]
 *         Renders the grid wrapper.
 * Child:  [bma_logo_grid_item image=""] — single logo (attachment ID).
 *
 * Source of truth class. Global function wrappers and the WPBakery
 * container-class registration live in bootstrap.php.
 *
 * @package Balefire\Components\LogoGrid
 */

declare( strict_types=1 );

namespace Balefire\Components\LogoGrid;

defined( 'ABSPATH' ) || exit;

/**
 * Static logo grid parent + child renderers.
 *
 * @package Balefire\Components\LogoGrid
 */
final class LogoGrid {

	public const COLUMN_CHOICES = array( 1, 2, 3, 4, 5, 6 );
	public const DEFAULT_COLUMNS = 5;

	/**
	 * Render the parent [bma_logo_grid] shortcode. Wraps children in the
	 * grid element. `data-cols` is set on the wrapper for CSS to read.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => (string) self::DEFAULT_COLUMNS,
			),
			$atts,
			'bma_logo_grid'
		);

		$columns = (int) $atts['columns'];
		if ( $columns < 1 || $columns > 6 ) {
			$columns = self::DEFAULT_COLUMNS;
		}

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = (string) preg_replace( '/^\s*<br\s*\/?>\s*/i', '', (string) $inner );
		$inner = (string) preg_replace( '/<br\s*\/?>\s*(?=<div\s+class="bma-logo-grid-item\b)/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(<\/div>)\s*<br\s*\/?>/i', '$1', (string) $inner );

		$inner = trim( (string) $inner );
		if ( '' === $inner ) {
			return '';
		}

		return sprintf(
			'<div class="bma-logo-grid" data-cols="%d" role="list">%s</div>',
			$columns,
			$inner
		);
	}

	/**
	 * Render one [bma_logo_grid_item] child.
	 *
	 * @param array $atts Shortcode attributes (only 'image').
	 * @return string HTML output, or '' when no image.
	 */
	public static function renderItem( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'image' => '',
			),
			$atts,
			'bma_logo_grid_item'
		);

		$image_id = (string) $atts['image'];
		if ( '' === $image_id || ! is_numeric( $image_id ) || (int) $image_id <= 0 ) {
			return '';
		}

		$img = wp_get_attachment_image(
			(int) $image_id,
			'full',
			false,
			array(
				'class'   => 'bma-logo-grid-item__img',
				'loading' => 'lazy',
			)
		);

		if ( '' === $img ) {
			return '';
		}

		return '<div class="bma-logo-grid-item" role="listitem">' . $img . '</div>';
	}

	/**
	 * Register both [bma_logo_grid] and [bma_logo_grid_item] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_logo_grid', array( self::class, 'render' ) );
		add_shortcode( 'bma_logo_grid_item', array( self::class, 'renderItem' ) );
	}

	/**
	 * WPBakery vc_map registration for both parent and child.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$column_choices = array();
		foreach ( self::COLUMN_CHOICES as $n ) {
			$column_choices[ (string) $n ] = (string) $n;
		}

		vc_map(
			array(
				'name'                    => __( 'BMA Logo Grid', 'balefire' ),
				'base'                    => 'bma_logo_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_LogoGrid',
				'category'                => __( 'BMA Elements', 'balefire' ),
				'description'             => __( 'Partner / client logo grid.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-images-carousel',
				'as_parent'               => array( 'only' => 'bma_logo_grid_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => $column_choices,
						'std'        => (string) self::DEFAULT_COLUMNS,
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'BMA Logo Grid Item', 'balefire' ),
				'base'            => 'bma_logo_grid_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_LogoGridItem',
				'category'        => __( 'BMA Elements', 'balefire' ),
				'description'     => __( 'A single logo inside a logo grid.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_logo_grid' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Logo Image', 'balefire' ),
						'param_name'  => 'image',
						'description' => __( 'Logo image from the Media Library.', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakeryShortCodesContainer subclass on vc_after_init.
	 */
	public static function registerContainerClass(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_LogoGrid' ) ) {
			eval( 'class WPBakeryShortCode_BMA_LogoGrid extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
