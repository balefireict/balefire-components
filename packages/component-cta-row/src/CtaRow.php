<?php
/**
 * BMA CTA Row — [bma_cta_row].
 *
 * Full-bleed CTA section over a consumer-supplied background image, built
 * from davidtours layouts/sections/bg-cta.svg. Heading + WYSIWYG copy on the
 * left; a big red CTA button with the business phone number under it on the
 * right. Large screens: flex row, space-between, vertically centered. Small
 * screens: stacked columns, centered.
 *
 * The component deliberately does NOT set the background image — drop the
 * element in a WPBakery STRETCH row and hardcode the image on .bma-cta-row
 * (or a custom class via the `class` att) in theme CSS. The component owns
 * the dark gradient overlay (::before) so the image always reads dark; cover
 * sizing/positioning defaults are pre-set so the consumer only supplies
 * background-image.
 *
 * Phone: the `phone` att wins when set; when blank it falls back to the ACF
 * options page field named by `phone_field` (default vmg_phone) — the
 * site-wide-business-value exception to the no-ACF rule (footer pattern).
 *
 * @package Balefire\Component\CtaRow
 */

declare( strict_types=1 );

namespace Balefire\Component\CtaRow;

defined( 'ABSPATH' ) || exit;

/**
 * CTA Row single-element shortcode.
 */
final class CtaRow {

	/**
	 * Shortcode base name.
	 */
	private const SHORTCODE = 'bma_cta_row';

	/**
	 * Register the shortcode.
	 */
	public static function register(): void {
		add_shortcode( self::SHORTCODE, array( self::class, 'render' ) );
	}

	/**
	 * Resolve the display phone number by source.
	 *
	 * 'manual' uses the phone att as typed. 'field' reads the ACF options
	 * page field named by $field (site-wide business value — the
	 * footer-pattern exception). Safe no-op where ACF is absent.
	 *
	 * @param string $source 'manual' or 'field'.
	 * @param string $manual Manual phone att.
	 * @param string $field  ACF options field name.
	 * @return string
	 */
	private static function resolvePhone( string $source, string $manual, string $field ): string {
		if ( 'field' === $source ) {
			if ( ! function_exists( 'get_field' ) ) {
				return '';
			}
			$field = '' !== trim( $field ) ? trim( $field ) : 'vmg_phone';
			return trim( (string) get_field( $field, 'option' ) );
		}
		return trim( $manual );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content WYSIWYG body copy.
	 * @return string HTML output.
	 */
	public static function render( array $atts = array(), ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'heading'      => '',
				'phone_source' => 'manual',
				'phone'        => '',
				'phone_field'  => 'vmg_phone',
				'cta_label'    => '',
				'cta_url'      => '',
				'cta_target'   => '',
				'id'           => '',
				'class'        => '',
			),
			$atts,
			self::SHORTCODE
		);

		$heading = trim( (string) $atts['heading'] );
		$source  = 'field' === $atts['phone_source'] ? 'field' : 'manual';
		$phone   = self::resolvePhone( $source, (string) $atts['phone'], (string) $atts['phone_field'] );
		$label   = trim( html_entity_decode( (string) $atts['cta_label'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$url     = trim( (string) $atts['cta_url'] );

		$body = '';
		if ( null !== $content && '' !== trim( $content ) ) {
			$body = do_shortcode( wpautop( $content ) );
		}

		$has_button = ( '' !== $label && '' !== $url );

		if ( '' === $heading && '' === $body && '' === $phone && ! $has_button ) {
			return '';
		}

		$classes = array( 'bma-cta-row' );
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = $extra;
		}

		$id_attr = '';
		$id      = trim( (string) $atts['id'] );
		if ( '' !== $id ) {
			$id_attr = ' id="' . esc_attr( $id ) . '"';
		}

		$tel = preg_replace( '/[^0-9+]/', '', $phone );

		ob_start();
		?>
		<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above ?>>
			<div class="bma-cta-row__inner">
				<div class="bma-cta-row__content">
					<?php if ( '' !== $heading ) : ?>
						<h2 class="bma-cta-row__heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $body ) : ?>
						<div class="bma-cta-row__body"><?php echo wp_kses_post( $body ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( $has_button || '' !== $phone ) : ?>
					<div class="bma-cta-row__action">
						<?php if ( $has_button ) : ?>
							<a
								href="<?php echo esc_url( $url ); ?>"
								class="bma-cta-row__btn"
								<?php echo '_blank' === $atts['cta_target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
							><?php echo esc_html( $label ); ?></a>
						<?php endif; ?>
						<?php if ( '' !== $phone ) : ?>
							<a class="bma-cta-row__phone" href="<?php echo esc_url( 'tel:' . $tel ); ?>"><?php echo esc_html( $phone ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
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
				'name'           => __( 'CTA Row', 'balefire' ),
				'base'           => self::SHORTCODE,
				'category'       => __( 'Custom Elements', 'balefire' ),
				'description'    => __( 'BMA — Full-bleed CTA over a background image: heading + copy left, button + phone right. Drop in a stretch row; hardcode the bg image on .bma-cta-row in theme CSS — the element owns the gradient overlay.', 'balefire' ),
				'icon'           => 'vc_icon-vc-information-white',
				'php_class_name' => 'WPBakeryShortCode_BMA_CtaRow',
				'params'         => array(

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Heading', 'balefire' ),
						'param_name'  => 'heading',
						'admin_label' => true,
					),

					array(
						'type'        => 'textarea_html',
						'heading'     => __( 'Body Copy', 'balefire' ),
						'param_name'  => 'content',
						'description' => __( 'Short supporting copy under the heading.', 'balefire' ),
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Button Label', 'balefire' ),
						'param_name'  => 'cta_label',
						'admin_label' => true,
					),

					array(
						'type'       => 'textfield',
						'heading'    => __( 'Button URL', 'balefire' ),
						'param_name' => 'cta_url',
					),

					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Button Target', 'balefire' ),
						'param_name' => 'cta_target',
						'value'      => array(
							__( 'Same window', 'balefire' ) => '',
							__( 'New tab', 'balefire' )     => '_blank',
						),
						'std'        => '',
					),

					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Phone Source', 'balefire' ),
						'param_name'  => 'phone_source',
						'value'       => array(
							__( 'Manual', 'balefire' )            => 'manual',
							__( 'ACF Options Field', 'balefire' ) => 'field',
						),
						'std'         => 'manual',
						'description' => __( 'Type a number, or pull the site-wide number from an ACF options page field.', 'balefire' ),
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Phone Number', 'balefire' ),
						'param_name'  => 'phone',
						'dependency'  => array(
							'element' => 'phone_source',
							'value'   => array( 'manual' ),
						),
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Options Phone Field', 'balefire' ),
						'param_name'  => 'phone_field',
						'value'       => 'vmg_phone',
						'description' => __( 'ACF options page field name to pull the phone number from.', 'balefire' ),
						'dependency'  => array(
							'element' => 'phone_source',
							'value'   => array( 'field' ),
						),
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Element ID', 'balefire' ),
						'param_name'  => 'id',
						'group'       => __( 'Advanced', 'balefire' ),
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra Class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Extra class on the section — handy hook for the background image.', 'balefire' ),
						'group'       => __( 'Advanced', 'balefire' ),
					),

				),
			)
		);
	}

	/**
	 * Register the WPBakery backend-editor preview class.
	 *
	 * Soft dependency on component-bakery-preview: only runs when the Preview
	 * class is present. Non-container elements need no fallback — WPBakery
	 * defaults to its FishBones class when php_class_name does not exist.
	 *
	 * Runs on vc_after_init (after vc_map).
	 */
	public static function registerPreviewClasses(): void {
		if ( ! class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			return;
		}

		\Balefire\Component\BakeryPreview\Preview::registerElementClass(
			'WPBakeryShortCode_BMA_CtaRow',
			array(
				'title' => 'heading',
				'text'  => 'content',
			)
		);
	}
}
