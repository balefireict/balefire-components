<?php
/**
 * BMA Simple Image Card shortcode (parent grid + child card).
 *
 * Parent: [bma_simple_image_card_grid columns="3"] wraps a series of child
 *         cards. Emits the bma-auto-grid wrapper (auto-grid CSS owned by the
 *         component-auto-grid package).
 * Child:  [bma_simple_image_card image="" title=""]
 *             Optional body copy.
 *         [/bma_simple_image_card]
 *         Renders one image-top card (image + title + optional body).
 *
 * Source of truth classes. Global function wrappers
 * (bma_simple_image_card_grid_shortcode, bma_simple_image_card_shortcode) are
 * defined in bootstrap.php. add_shortcode, vc_map, and the
 * WPBakeryShortCodesContainer subclass are also wired there.
 *
 * Ported from rockerbox balefire theme:
 *   inc/shortcodes/bma-simple-image-card.php
 *
 * @package Balefire\Component\SimpleImageCard
 */

declare( strict_types=1 );

namespace Balefire\Component\SimpleImageCard;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_simple_image_card_grid] / [bma_simple_image_card]
 * shortcodes.
 *
 * @package Balefire\Component\SimpleImageCard
 */
final class SimpleImageCard {

	/**
	 * Render the parent [bma_simple_image_card_grid] shortcode. Wraps the
	 * children (processed by do_shortcode) in the auto-grid wrapper.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Inner shortcodes (child cards).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => '3',
			),
			$atts,
			'bma_simple_image_card_grid'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$cols      = (int) ( $atts['columns'] ?: 3 );
		$col_class = match ( $cols ) {
			1       => 'lg:auto-grid-cols-1',
			2       => 'lg:auto-grid-cols-2',
			4       => 'lg:auto-grid-cols-4',
			default => 'lg:auto-grid-cols-3',
		};

		return '<div class="bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 '
			. esc_attr( $col_class ) . '">' . do_shortcode( (string) $content ) . '</div>';
	}

	/**
	 * Render one [bma_simple_image_card] child card.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Optional body HTML.
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'image' => '',
				'title' => '',
			),
			$atts,
			'bma_simple_image_card'
		);

		$image_url = '';
		$image_alt = '';
		if ( ! empty( $atts['image'] ) && is_numeric( $atts['image'] ) ) {
			$image_url = wp_get_attachment_image_url( (int) $atts['image'], 'large' );
			$image_alt = (string) get_post_meta( (int) $atts['image'], '_wp_attachment_image_alt', true );
		}

		$title     = trim( (string) $atts['title'] );
		$body_html = wp_kses_post( trim( do_shortcode( (string) $content ) ) );

		ob_start();
		?>
		<div class="bma-simple-image-card">
			<?php if ( $image_url ) : ?>
				<div class="bma-simple-image-card__media">
					<img decoding="async" class="bma-simple-image-card__img" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
				</div>
			<?php endif; ?>
			<div class="bma-simple-image-card__content">
				<?php if ( '' !== $title ) : ?>
					<h3 class="bma-simple-image-card__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( $body_html ) ) : ?>
					<?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Register both [bma_simple_image_card_grid] and [bma_simple_image_card]
	 * shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_simple_image_card_grid', array( self::class, 'render' ) );
		add_shortcode( 'bma_simple_image_card', array( self::class, 'renderItem' ) );
	}

	/**
	 * WPBakery vc_map registration for both parent and child.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'Image Card Grid', 'balefire' ),
				'base'                    => 'bma_simple_image_card_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_SimpleImageCard',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Image-top card grid.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-row',
				'as_parent'               => array( 'only' => 'bma_simple_image_card' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => array( '3', '1', '2', '4' ),
						'std'        => '3',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Image Card', 'balefire' ),
				'base'            => 'bma_simple_image_card',
				'php_class_name'  => 'WPBakeryShortCode_BMA_SimpleImageCardItem',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single image-top card (image + title).', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_simple_image_card_grid' ),
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
						'type'       => 'textarea_html',
						'heading'    => __( 'Body (optional)', 'balefire' ),
						'param_name' => 'content',
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakery editor classes for the parent container and the
	 * child card. Hooked on vc_after_init so the WPBakery base classes exist.
	 *
	 * When the shared BakeryPreview infra is present (soft dependency), it
	 * defines both classes: the parent as an (empty-map) container so it is
	 * recognized as a container in the editor, and the child with a preview
	 * map (thumbnail + title + body excerpt). When the infra is absent we fall
	 * back to the original plain eval for the container subclass; the child
	 * needs no fallback (WPBakery defaults to its FishBones view).
	 */
	public static function registerPreviewClasses(): void {
		if ( class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_SimpleImageCard',
				array()
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_SimpleImageCardItem',
				array(
					'image' => 'image',
					'title' => 'title',
					'text'  => 'content',
				)
			);
			return;
		}

		// Fallback: preview infra absent — plain eval the container subclass so
		// the parent is still recognized as a container in the editor.
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_SimpleImageCard' ) ) {
			eval( 'class WPBakeryShortCode_BMA_SimpleImageCard extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
