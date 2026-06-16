<?php
/**
 * BMA CTA Bar — [bma_cta_bar].
 *
 * Solid-background two-column CTA bar: heading + subtext on the left, an icon
 * button on the right. Built from davidtours layouts/sections printable-schedule
 * mock (solid #2e266d bar, white text, #6f779d button with calendar icon).
 *
 * Owns its full-width background; inner content is capped at 1050px centered to
 * align with sibling card grids. Drop in a stretch row for full-bleed.
 *
 * @package Balefire\Component\CtaBar
 */

declare( strict_types=1 );

namespace Balefire\Component\CtaBar;

defined( 'ABSPATH' ) || exit;

/**
 * Single-element shortcode renderer for [bma_cta_bar].
 */
final class CtaBar {

	/**
	 * Shortcode base name.
	 */
	private const SHORTCODE = 'bma_cta_bar';

	/**
	 * Calendar-check SVG icon path (viewBox 0 0 20 20, from source mock).
	 */
	private const ICON_PATH = 'M20,1.667V20H0V1.667H2.5V2.5a1.667,1.667,0,0,0,3.334,0V1.667h8.334V2.5a1.667,1.667,0,1,0,3.334,0V1.667Zm-1.667,5H1.667V18.335H18.335ZM16.668.833A.833.833,0,0,0,15,.833V2.5a.833.833,0,0,0,1.667,0ZM5,2.5a.833.833,0,1,1-1.667,0V.833A.833.833,0,1,1,5,.833Zm.833,9.775.713-.659a13.772,13.772,0,0,1,2.3,1.378,19.9,19.9,0,0,1,5.089-4.36l.233.533A22.628,22.628,0,0,0,9.2,15.835,35.663,35.663,0,0,0,5.834,12.275Z';

	/**
	 * Register the shortcode.
	 */
	public static function register(): void {
		add_shortcode( self::SHORTCODE, array( self::class, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content WYSIWYG subtext (rich text).
	 * @return string HTML output.
	 */
	public static function render( array $atts = array(), ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'heading'    => '',
				'btn_label'  => '',
				'btn_url'    => '',
				'btn_target' => '',
				'class'      => '',
			),
			$atts,
			self::SHORTCODE
		);

		$heading = trim( (string) $atts['heading'] );
		$label   = trim( (string) $atts['btn_label'] );
		$url     = trim( (string) $atts['btn_url'] );

		$body = '';
		if ( null !== $content && '' !== trim( $content ) ) {
			$body = wp_kses_post( do_shortcode( wpautop( $content ) ) );
		}

		if ( '' === $heading && '' === trim( $body ) && '' === $label ) {
			return '';
		}

		$classes = array( 'cta-bar' );
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		$target_attr = '_blank' === $atts['btn_target'] ? ' target="_blank" rel="noopener noreferrer"' : '';

		$icon = '<svg class="cta-bar__icon" viewBox="0 0 20 20" aria-hidden="true" focusable="false">'
			. '<path fill="currentColor" d="' . self::ICON_PATH . '"/>'
			. '</svg>';

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="cta-bar__inner">
				<div class="cta-bar__content">
					<?php if ( '' !== $heading ) : ?>
						<p class="cta-bar__heading"><?php echo esc_html( $heading ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $body ) ) : ?>
						<div class="cta-bar__body"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?></div>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $label ) : ?>
					<?php if ( '' !== $url ) : ?>
						<a class="cta-bar__btn" href="<?php echo esc_url( $url ); ?>"<?php echo $target_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $label ); ?></span>
						</a>
					<?php else : ?>
						<span class="cta-bar__btn">
							<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $label ); ?></span>
						</span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Register the WPBakery (VC) element mapping.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'           => __( 'CTA Bar', 'balefire' ),
				'base'           => self::SHORTCODE,
				'category'       => __( 'Custom Elements', 'balefire' ),
				'description'    => __( 'BMA — Solid-background CTA bar: heading + subtext left, icon button right.', 'balefire' ),
				'icon'           => 'vc_icon-vc-information-white',
				'php_class_name' => 'WPBakeryShortCode_BMA_CtaBar',
				'params'         => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Heading', 'balefire' ),
						'param_name'  => 'heading',
						'admin_label' => true,
						'value'       => __( 'Need a Copy of the Schedule?', 'balefire' ),
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Subtext', 'balefire' ),
						'param_name' => 'content',
						'value'      => __( 'Download or print the full Atlantic City bus schedule before your trip.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Button Label', 'balefire' ),
						'param_name'  => 'btn_label',
						'admin_label' => true,
						'value'       => __( 'Printable Schedule', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Button URL', 'balefire' ),
						'param_name' => 'btn_url',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Button Target', 'balefire' ),
						'param_name' => 'btn_target',
						'value'      => array(
							__( 'Same window', 'balefire' ) => '',
							__( 'New tab', 'balefire' )     => '_blank',
						),
						'std'        => '',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Optional extra CSS class on the bar.', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakery backend-editor preview class.
	 *
	 * Soft dependency on component-bakery-preview.
	 */
	public static function registerPreviewClasses(): void {
		if ( ! class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			return;
		}

		\Balefire\Component\BakeryPreview\Preview::registerElementClass(
			'WPBakeryShortCode_BMA_CtaBar',
			array(
				'title' => 'heading',
				'text'  => 'content',
			)
		);
	}
}
