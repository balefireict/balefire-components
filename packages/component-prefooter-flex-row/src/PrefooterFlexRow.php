<?php
/**
 * BMA Prefooter Flex Row — [bma_prefooter_flex_row].
 *
 * Full-bleed CTA section over a consumer-supplied background image. By default
 * the shortcode reads site-wide ACF options fields from the General Business
 * field group using the generic acffg_ prefix. Attributes can still override
 * those values for one-off usage.
 *
 * @package Balefire\Component\PrefooterFlexRow
 */

declare( strict_types=1 );

namespace Balefire\Component\PrefooterFlexRow;

defined( 'ABSPATH' ) || exit;

/**
 * Prefooter Flex Row shortcode.
 */
final class PrefooterFlexRow {

	/**
	 * Shortcode base name.
	 */
	private const SHORTCODE = 'bma_prefooter_flex_row';

	/**
	 * Register the shortcode.
	 */
	public static function register(): void {
		add_shortcode( self::SHORTCODE, array( self::class, 'render' ) );
	}

	/**
	 * Fetch an ACF options field safely.
	 *
	 * @param string $field Field name.
	 * @return mixed
	 */
	private static function option( string $field ) {
		$field = trim( $field );
		if ( '' === $field || ! function_exists( 'get_field' ) ) {
			return '';
		}

		$value = get_field( $field, 'option' );
		return ( null === $value || false === $value ) ? '' : $value;
	}

	/**
	 * Resolve an explicit attribute, otherwise an ACF options field.
	 *
	 * @param array  $atts       Shortcode attributes.
	 * @param string $att        Attribute name.
	 * @param string $field_att  Attribute that names the ACF field.
	 * @param string $fallback   Default ACF field name.
	 * @return mixed
	 */
	private static function value( array $atts, string $att, string $field_att, string $fallback ) {
		$manual = $atts[ $att ] ?? '';
		if ( is_string( $manual ) && '' !== trim( $manual ) ) {
			return $manual;
		}
		if ( ! is_string( $manual ) && ! empty( $manual ) ) {
			return $manual;
		}

		$field = $atts[ $field_att ] ?? $fallback;
		$field = is_string( $field ) && '' !== trim( $field ) ? trim( $field ) : $fallback;
		return self::option( $field );
	}

	/**
	 * Normalize body copy from textarea_html, body att, or ACF WYSIWYG.
	 *
	 * @param mixed $body Body value.
	 * @return string
	 */
	private static function bodyHtml( $body ): string {
		$body = trim( (string) $body );
		if ( '' === $body ) {
			return '';
		}

		$html = do_shortcode( $body );
		if ( false === strpos( $html, '<' ) ) {
			$html = wpautop( $html );
		}

		return wp_kses_post( $html );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array|string $atts    Shortcode attributes.
	 * @param string|null $content Optional body override.
	 * @return string HTML output.
	 */
	public static function render( $atts = array(), ?string $content = null ): string {
		$atts = is_array( $atts ) ? $atts : array();

		$atts = shortcode_atts(
			array(
				'heading'               => '',
				'body'                  => '',
				'phone'                 => '',
				'cta_label'             => '',
				'cta_url'               => '',
				'cta_target'            => '',
				'heading_field'         => 'acffg_prefooter_flex_row_heading',
				'body_field'            => 'acffg_prefooter_flex_row_body',
				'phone_field'           => 'acffg_phone',
				'cta_label_field'       => 'acffg_prefooter_flex_row_button_label',
				'cta_url_field'         => 'acffg_prefooter_flex_row_button_url',
				'cta_new_tab_field'     => 'acffg_prefooter_flex_row_button_new_tab',
				'id'                    => '',
				'class'                 => '',
				'bg_image'              => '',
				),
			$atts,
			self::SHORTCODE
		);

		$heading = trim( (string) self::value( $atts, 'heading', 'heading_field', 'acffg_prefooter_flex_row_heading' ) );
		$phone   = trim( (string) self::value( $atts, 'phone', 'phone_field', 'acffg_phone' ) );
		$label   = trim( html_entity_decode( (string) self::value( $atts, 'cta_label', 'cta_label_field', 'acffg_prefooter_flex_row_button_label' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$url     = trim( (string) self::value( $atts, 'cta_url', 'cta_url_field', 'acffg_prefooter_flex_row_button_url' ) );

		$body_source = ( null !== $content && '' !== trim( $content ) )
			? $content
			: self::value( $atts, 'body', 'body_field', 'acffg_prefooter_flex_row_body' );
		$body = self::bodyHtml( $body_source );

		$target = trim( (string) $atts['cta_target'] );
		if ( '' === $target ) {
			$new_tab = self::option( (string) $atts['cta_new_tab_field'] );
			$target  = ( true === $new_tab || '1' === (string) $new_tab || '_blank' === (string) $new_tab ) ? '_blank' : '';
		}
		$target = '_blank' === $target ? '_blank' : '';

		$has_button = ( '' !== $label && '' !== $url );

		if ( '' === $heading && '' === $body && '' === $phone && ! $has_button ) {
			return '';
		}

		$classes = array( 'bma-prefooter-flex-row' );
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = $extra;
		}

		$id_attr = '';
		$id      = trim( (string) $atts['id'] );
		if ( '' !== $id ) {
			$id_attr = ' id="' . esc_attr( $id ) . '"';
		}

		$style_attr = '';
		$bg_image   = trim( (string) $atts['bg_image'] );
		if ( '' !== $bg_image ) {
			$style_attr = ' style="background-image:url(\'' . esc_url( $bg_image ) . '\')"';
		}

		$tel = preg_replace( '/[^0-9+]/', '', $phone );

		ob_start();
		?>
		<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above ?><?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url above ?>>
			<div class="bma-prefooter-flex-row__inner">
				<div class="bma-prefooter-flex-row__content">
					<?php if ( '' !== $heading ) : ?>
						<h2 class="bma-prefooter-flex-row__heading"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $body ) : ?>
						<div class="bma-prefooter-flex-row__body"><?php echo wp_kses_post( $body ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( $has_button || '' !== $phone ) : ?>
					<div class="bma-prefooter-flex-row__action">
						<?php if ( $has_button ) : ?>
							<a
								href="<?php echo esc_url( $url ); ?>"
								class="bma-prefooter-flex-row__btn"
								<?php echo '_blank' === $target ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
							><?php echo esc_html( $label ); ?></a>
						<?php endif; ?>
						<?php if ( '' !== $phone ) : ?>
							<a class="bma-prefooter-flex-row__phone" href="<?php echo esc_url( 'tel:' . $tel ); ?>"><?php echo esc_html( $phone ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
		return (string) ob_get_clean();
	}
}
