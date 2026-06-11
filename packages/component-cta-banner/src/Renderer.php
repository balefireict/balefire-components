<?php
/**
 * Renderer for [bma_cta_banner] shortcode.
 *
 * Attribute-driven (no ACF). Content comes from shortcode atts and the
 * WYSIWYG body ($content).
 */

declare( strict_types=1 );

namespace Balefire\Component\CtaBanner;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	/**
	 * @return array<string, string>
	 */
	private static function defaults(): array {
		return [
			'align'      => 'center',
			'variant'    => 'gradient',
			'id'         => '',
			'class'      => '',
			'headline'   => '',
			'cta_label'  => '',
			'cta_url'    => '',
			'cta_style'  => 'white',
			'cta_target' => '',
		];
	}

	/**
	 * @param array<string,string>|string $atts
	 */
	public static function render( $atts, ?string $content = null ): string {
		$atts = \shortcode_atts(
			self::defaults(),
			is_array( $atts ) ? $atts : [],
			'bma_cta_banner'
		);

		$variant = (string) ( $atts['variant'] ?: 'gradient' );

		$wrapper_atts = self::build_wrapper_atts( $atts, $variant );

		// Rich WYSIWYG body comes through $content.
		$body = '';
		if ( null !== $content && '' !== trim( $content ) ) {
			$body = \do_shortcode( \wpautop( $content ) );
		}

		$args = [
			'headline' => (string) $atts['headline'],
			'body'     => $body,
			'cta'      => self::render_cta(
				(string) $atts['cta_label'],
				(string) $atts['cta_url'],
				(string) $atts['cta_style'],
				(string) $atts['cta_target']
			),
		];

		ob_start();
		include __DIR__ . '/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,string> $atts
	 * @return array<string,string>
	 */
	private static function build_wrapper_atts( array $atts, string $variant ): array {
		$classes = [ 'bma-c-cta-banner', "bma-c-cta-banner--{$variant}" ];

		$align = (string) $atts['align'];
		if ( $align !== '' && preg_match( '/^[a-z0-9_-]+$/i', $align ) ) {
			$classes[] = "bma-c-cta-banner--align-{$align}";
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

		return (array) \apply_filters( 'bma_c_cta_banner/wrapper_atts', $wrapper, $atts );
	}

	/**
	 * Render the CTA button from flat attributes.
	 */
	public static function render_cta( string $label, string $url, string $style = 'white', string $target = '' ): string {
		$label = trim( html_entity_decode( $label, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$url   = trim( $url );

		if ( '' === $label || '' === $url ) {
			return '';
		}

		$style   = strtolower( trim( $style ) );
		$classes = [ 'btn', 'btn-' . ( $style !== '' ? $style : 'white' ) ];

		$target_attr = ( '_blank' === $target )
			? ' target="_blank" rel="noopener noreferrer"'
			: '';

		return sprintf(
			'<a href="%s" class="%s"%s>%s</a>',
			\esc_url( $url ),
			\esc_attr( implode( ' ', $classes ) ),
			$target_attr,
			\esc_html( $label )
		);
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
