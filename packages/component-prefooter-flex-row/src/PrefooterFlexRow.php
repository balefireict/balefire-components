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
	 * Fetch an ACF field from the current loop post, when available.
	 *
	 * Returns '' when not in a loop, ACF is missing, the field is empty,
	 * or the field is at its default (ACF stores nothing for default_value
	 * until the post is saved, so this gracefully falls through to options).
	 *
	 * @param string $field Field name.
	 * @return mixed
	 */
	private static function post_value( string $field ) {
		$field = trim( $field );
		if ( '' === $field || ! function_exists( 'get_field' ) || ! function_exists( 'get_the_ID' ) ) {
			return '';
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		$value = get_field( $field, $post_id );
		if ( null === $value || false === $value ) {
			return '';
		}

		if ( is_string( $value ) && '' === trim( $value ) ) {
			return '';
		}

		return $value;
	}

	/**
	 * Read the default_value from the ACF field config.
	 *
	 * ACF does NOT apply `default_value` on frontend get_field() reads — it is
	 * only used as the initial form value in wp-admin. So when no per-page or
	 * options value is stored, we fall back to the field config's default.
	 *
	 * @param string $field Field name (or key).
	 * @return mixed '' when no default is configured.
	 */
	private static function field_default( string $field ) {
		$field = trim( $field );
		if ( '' === $field || ! function_exists( 'acf_get_field' ) ) {
			return '';
		}

		$config = acf_get_field( $field );
		if ( ! $config || ! array_key_exists( 'default_value', $config ) ) {
			return '';
		}

		$default = $config['default_value'];
		if ( null === $default || false === $default ) {
			return '';
		}
		if ( is_string( $default ) && '' === trim( $default ) ) {
			return '';
		}

		return $default;
	}

	/**
	 * Resolve an explicit attribute, otherwise a per-page ACF field, then ACF
	 * options, then the field config's default_value.
	 *
	 * Resolution order: manual shortcode att -> per-page field on the current
	 * post -> site-wide options field -> ACF default_value from the field
	 * config. The per-page step lets the group_acffg_prefooter_flex_row field
	 * group override centrally-managed content per page without the consumer
	 * partial having to pass every att. The default_value fallback is what
	 * makes the section render correctly out of the box before an editor has
	 * saved anything.
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

		$per_page = self::post_value( $field );
		if ( '' !== $per_page ) {
			return $per_page;
		}

		$opt = self::option( $field );
		if ( '' !== $opt ) {
			return $opt;
		}

		return self::field_default( $field );
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
			$new_tab_field = (string) $atts['cta_new_tab_field'];
			$new_tab       = self::post_value( $new_tab_field );
			if ( '' === $new_tab ) {
				$new_tab = self::option( $new_tab_field );
			}
			if ( '' === $new_tab ) {
				$new_tab = self::field_default( $new_tab_field );
			}
			$target = ( true === $new_tab || '1' === (string) $new_tab || '_blank' === (string) $new_tab ) ? '_blank' : '';
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
