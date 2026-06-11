<?php
/**
 * Renderer for [bma_testimonial] shortcode.
 *
 * Attribute-driven (no ACF). The quote comes from the element's rich-text
 * body ($content); attribution/role/company/image come from shortcode atts.
 */

declare( strict_types=1 );

namespace Balefire\Component\Testimonial;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	/**
	 * @return array<string, string>
	 */
	private static function defaults(): array {
		return [
			'align'       => 'center',
			'variant'     => '',
			'id'          => '',
			'class'       => '',
			'attribution' => '',
			'role'        => '',
			'company'     => '',
			'image'       => '',
		];
	}

	/**
	 * @param array<string,string>|string $atts
	 */
	public static function render( $atts, ?string $content = null ): string {
		$atts = \shortcode_atts(
			self::defaults(),
			is_array( $atts ) ? $atts : [],
			'bma_testimonial'
		);

		$wrapper_atts = self::build_wrapper_atts( $atts );

		// Rich quote body comes through $content.
		$quote = '';
		if ( null !== $content && '' !== trim( $content ) ) {
			$quote = \do_shortcode( \wpautop( $content ) );
		}

		$args = [
			'quote'       => $quote,
			'attribution' => (string) $atts['attribution'],
			'role'        => (string) $atts['role'],
			'company'     => (string) $atts['company'],
			'image'       => self::render_image( (string) $atts['image'] ),
		];

		ob_start();
		include __DIR__ . '/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * Build the <img> markup from an attachment ID.
	 */
	public static function render_image( string $image_id ): string {
		$image_id = trim( $image_id );
		if ( '' === $image_id || ! is_numeric( $image_id ) || (int) $image_id <= 0 ) {
			return '';
		}

		return (string) \wp_get_attachment_image(
			(int) $image_id,
			'thumbnail',
			false,
			[
				'class'   => 'bma-c-testimonial__img',
				'loading' => 'lazy',
			]
		);
	}

	/**
	 * @param array<string,string> $atts
	 * @return array<string,string>
	 */
	private static function build_wrapper_atts( array $atts ): array {
		$classes = [ 'bma-c-testimonial' ];

		$variant = (string) $atts['variant'];
		if ( $variant !== '' && preg_match( '/^[a-z0-9_-]+$/i', $variant ) ) {
			$classes[] = "bma-c-testimonial--{$variant}";
		}

		$align = (string) $atts['align'];
		if ( $align !== '' && preg_match( '/^[a-z0-9_-]+$/i', $align ) ) {
			$classes[] = "bma-c-testimonial--align-{$align}";
		}

		$extra = trim( (string) $atts['class'] );
		if ( $extra !== '' ) {
			$classes[] = $extra;
		}

		$wrapper = [
			'class' => implode( ' ', array_unique( $classes ) ),
		];

		$id = trim( (string) $atts['id'] );
		if ( $id !== '' ) {
			$wrapper['id'] = $id;
		}

		return (array) \apply_filters( 'bma_c_testimonial/wrapper_atts', $wrapper, $atts );
	}

	/**
	 * Renders the array as a quoted HTML attribute string.
	 *
	 * @param array<string,string> $atts
	 */
	public static function attrs_to_html( array $atts ): string {
		$parts = [];
		foreach ( $atts as $key => $value ) {
			if ( $value === '' || $value === null ) {
				continue;
			}
			$parts[] = sprintf(
				'%s="%s"',
				\esc_attr( (string) $key ),
				\esc_attr( (string) $value )
			);
		}
		return implode( ' ', $parts );
	}
}
