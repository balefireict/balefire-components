<?php
/**
 * BMA Simple Card shortcode (parent grid + child card).
 *
 * Parent: [bma_simple_card_grid columns="3"] wraps a series of child cards
 *         in the shared bma-auto-grid wrapper (auto-grid CSS lives in
 *         component-auto-grid, not here).
 * Child:  [bma_simple_card title="..."]Body[/bma_simple_card] renders one
 *         text-only bordered card. Title via attribute, body copy is the
 *         enclosed (rich text) content.
 *
 * Source of truth classes. Global function wrappers
 * (bma_simple_card_grid_shortcode, bma_simple_card_shortcode) are defined in
 * bootstrap.php. add_shortcode, vc_map, and the WPBakeryShortCodesContainer
 * subclass are also wired there.
 *
 * Ported from rockerbox theme inc/shortcodes/bma-simple-card.php.
 *
 * @package Balefire\Component\SimpleCard
 */

declare( strict_types=1 );

namespace Balefire\Component\SimpleCard;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_simple_card_grid] / [bma_simple_card] pair.
 *
 * @package Balefire\Component\SimpleCard
 */
final class SimpleCard {

	/**
	 * Render the parent [bma_simple_card_grid] shortcode. Wraps the children
	 * (processed by do_shortcode) in the shared bma-auto-grid wrapper.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => '3',
			),
			$atts,
			'bma_simple_card_grid'
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
	 * Render one [bma_simple_card] child.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML (passes between tags).
	 * @return string HTML output.
	 */
	public static function renderCard( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'title' => '',
			),
			$atts,
			'bma_simple_card'
		);

		$title     = trim( (string) $atts['title'] );
		$body_html = wp_kses_post( trim( do_shortcode( (string) $content ) ) );

		ob_start();
		?>
		<div class="bma-simple-card">
			<div class="bma-simple-card__content">
				<?php if ( '' !== $title ) : ?>
					<h3 class="bma-simple-card__title wp-block-heading"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( $body_html ) ) : ?>
					<p class="wp-block-paragraph"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Register both [bma_simple_card_grid] and [bma_simple_card] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_simple_card_grid', array( self::class, 'render' ) );
		add_shortcode( 'bma_simple_card', array( self::class, 'renderCard' ) );
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
				'name'                    => __( 'BMA Simple Card Grid', 'balefire' ),
				'base'                    => 'bma_simple_card_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_SimpleCard',
				'category'                => __( 'BMA Cards', 'balefire' ),
				'description'             => __( 'Text-only bordered card grid.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-row',
				'as_parent'               => array( 'only' => 'bma_simple_card' ),
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
				'name'            => __( 'BMA Simple Card', 'balefire' ),
				'base'            => 'bma_simple_card',
				'category'        => __( 'BMA Cards', 'balefire' ),
				'description'     => __( 'A single text-only bordered card.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_simple_card_grid' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Body', 'balefire' ),
						'param_name' => 'content',
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakeryShortCodesContainer subclass that the parent
	 * shortcode needs to be recognized as a container in the editor.
	 * Hooked on vc_after_init so the parent class is loaded.
	 */
	public static function registerContainerClass(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_SimpleCard' ) ) {
			eval( 'class WPBakeryShortCode_BMA_SimpleCard extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
