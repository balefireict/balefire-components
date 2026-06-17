<?php
/**
 * BMA CTA Bar — [bma_cta_bar].
 *
 * Solid-background two-column CTA bar: heading + subtext on the left, a button
 * on the right. Built from davidtours layouts/sections printable-schedule mock.
 *
 * Owns its full-width background; inner content is capped at 1050px centered to
 * align with sibling card grids. Drop in a stretch row for full-bleed.
 *
 * Button supports optional before/after icons (calendar, phone, custom upload)
 * using the same icon markup as component-buttons (.btn-icon class).
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

	/** Register the shortcode. */
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
				'heading'          => '',
				'btn_label'        => '',
				'btn_url'          => '',
				'btn_target'       => '',
				'icon_before'      => '',
				'icon_before_custom' => '',
				'icon_after'       => '',
				'icon_after_custom'  => '',
				'class'            => '',
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

		// Build button inner HTML: [icon_before] label [icon_after].
		$btn_inner = '';
		if ( '' !== $label ) {
			$icon_before_html = self::renderIcon( (string) $atts['icon_before'], (string) $atts['icon_before_custom'] );
			$icon_after_html  = self::renderIcon( (string) $atts['icon_after'], (string) $atts['icon_after_custom'] );

			$btn_inner = $icon_before_html . '<span>' . esc_html( $label ) . '</span>' . $icon_after_html;
		}

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
							<?php echo $btn_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php else : ?>
						<span class="cta-bar__btn">
							<?php echo $btn_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render an inline icon SVG or custom image.
	 *
	 * Uses the same .btn-icon class and SVG paths as component-buttons so the
	 * two components share icon styles.
	 *
	 * @param string $icon       Icon key: '', 'calendar', 'phone', 'custom'.
	 * @param string $icon_custom Attachment ID (when icon is 'custom').
	 * @return string HTML, or '' when no icon.
	 */
	public static function renderIcon( string $icon, string $icon_custom = '' ): string {
		$icon = strtolower( trim( $icon ) );

		switch ( $icon ) {
			case 'calendar':
				return '<span class="btn-icon" aria-hidden="true">'
					. '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="1em" height="1em">'
					. '<path d="M20,1.667V20H0V1.667H2.5V2.5a1.667,1.667,0,0,0,3.334,0V1.667h8.334V2.5a1.667,1.667,0,1,0,3.334,0V1.667Zm-1.667,5H1.667V18.335H18.335ZM16.668.833A.833.833,0,0,0,15,.833V2.5a.833.833,0,0,0,1.667,0ZM5,2.5a.833.833,0,1,1-1.667,0V.833A.833.833,0,1,1,5,.833Zm.833,9.775.713-.659a13.772,13.772,0,0,1,2.3,1.378,19.9,19.9,0,0,1,5.089-4.36l.233.533A22.628,22.628,0,0,0,9.2,15.835,35.663,35.663,0,0,0,5.834,12.275Z"/>'
					. '</svg></span>';

			case 'phone':
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

	/** Register the WPBakery (VC) element mapping. */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$icon_choices = array(
			__( 'None', 'balefire' )     => '',
			__( 'Calendar', 'balefire' ) => 'calendar',
			__( 'Phone', 'balefire' )    => 'phone',
			__( 'Custom', 'balefire' )   => 'custom',
		);

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
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Button Label', 'balefire' ),
						'param_name'  => 'btn_label',
						'admin_label' => true,
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
						'type'       => 'dropdown',
						'heading'    => __( 'Icon Before Text', 'balefire' ),
						'param_name' => 'icon_before',
						'value'      => $icon_choices,
						'std'        => '',
					),
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Custom Icon (Before)', 'balefire' ),
						'param_name'  => 'icon_before_custom',
						'description' => __( 'Only used when Icon Before is set to Custom.', 'balefire' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Icon After Text', 'balefire' ),
						'param_name' => 'icon_after',
						'value'      => $icon_choices,
						'std'        => '',
					),
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Custom Icon (After)', 'balefire' ),
						'param_name'  => 'icon_after_custom',
						'description' => __( 'Only used when Icon After is set to Custom.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Optional extra CSS class on the bar.', 'balefire' ),
					),
					/*
					 * textarea_html MUST be the last param — WPBakery's TinyMCE
					 * integration corrupts the shortcode serialization when it is
					 * not the final param in the list (known VC bug).
					 */
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Subtext', 'balefire' ),
						'param_name' => 'content',
						'value'      => __( 'Download or print the full Atlantic City bus schedule before your trip.', 'balefire' ),
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
