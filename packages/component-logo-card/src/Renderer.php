<?php
/**
 * Renderer for [bma_logo_card] (parent) and [bma_logo_card_item] (child).
 *
 * Attribute-driven WPBakery parent/child container. No ACF reads.
 *
 * Parent: [bma_logo_card headline="" columns="5"]…[/bma_logo_card]
 * Child:  [bma_logo_card_item image="123" link=""]
 *
 * @package Balefire\Component\LogoCard
 */

declare( strict_types=1 );

namespace Balefire\Component\LogoCard;

defined( 'ABSPATH' ) || exit;

/**
 * Static logo-card parent + child renderers.
 */
final class Renderer {

	public const COLUMN_CHOICES  = array( 3, 4, 5, 6 );
	public const DEFAULT_COLUMNS = 5;

	/**
	 * Render the parent [bma_logo_card] shortcode.
	 *
	 * @param array<string,string>|string $atts    Shortcode attributes.
	 * @param string|null                  $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( $atts, ?string $content = null ): string {
		$atts = \shortcode_atts(
			array(
				'headline' => '',
				'columns'  => (string) self::DEFAULT_COLUMNS,
				'el_id'    => '',
				'el_class' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'bma_logo_card'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = \do_shortcode( \shortcode_unautop( trim( (string) $content ) ) );
		$inner = (string) preg_replace( '/^\s*<br\s*\/?>\s*/i', '', (string) $inner );
		$inner = (string) preg_replace( '/<br\s*\/?>\s*(?=<(?:a|div)\s+[^>]*class="bma-c-logo-card__logo)/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(<\/(?:a|div)>)\s*<br\s*\/?>/i', '$1', (string) $inner );
		$inner = trim( (string) $inner );

		if ( '' === $inner ) {
			return '';
		}

		$columns = (int) $atts['columns'];
		if ( $columns < 3 || $columns > 6 ) {
			$columns = self::DEFAULT_COLUMNS;
		}

		$classes = array( 'bma-c-logo-card' );
		$extra   = trim( (string) $atts['el_class'] );
		if ( '' !== $extra ) {
			$classes[] = $extra;
		}

		$section_atts = sprintf( ' class="%s"', \esc_attr( implode( ' ', $classes ) ) );
		$el_id        = trim( (string) $atts['el_id'] );
		if ( '' !== $el_id ) {
			$section_atts .= sprintf( ' id="%s"', \esc_attr( $el_id ) );
		}

		$headline_html = '';
		$headline      = (string) $atts['headline'];
		if ( '' !== trim( $headline ) ) {
			$headline_html = sprintf(
				'<h2 class="bma-c-logo-card__headline">%s</h2>',
				\esc_html( $headline )
			);
		}

		return sprintf(
			'<section%1$s><div class="bma-c-logo-card__inner">%2$s<div class="bma-c-logo-card__list bma-c-logo-card__list--%3$d">%4$s</div></div></section>',
			$section_atts,
			$headline_html,
			$columns,
			$inner
		);
	}

	/**
	 * Render one [bma_logo_card_item] child.
	 *
	 * @param array<string,string>|string $atts Shortcode attributes.
	 * @return string HTML output, or '' when no valid image.
	 */
	public static function renderItem( $atts ): string {
		$atts = \shortcode_atts(
			array(
				'image' => '',
				'link'  => '',
			),
			is_array( $atts ) ? $atts : array(),
			'bma_logo_card_item'
		);

		$image_id = (string) $atts['image'];
		if ( '' === $image_id || ! is_numeric( $image_id ) || (int) $image_id <= 0 ) {
			return '';
		}

		$img = \wp_get_attachment_image(
			(int) $image_id,
			'full',
			false,
			array(
				'loading' => 'lazy',
			)
		);

		if ( '' === (string) $img ) {
			return '';
		}

		$link = trim( (string) $atts['link'] );
		if ( '' !== $link ) {
			return sprintf(
				'<a href="%s" class="bma-c-logo-card__logo">%s</a>',
				\esc_url( $link ),
				$img
			);
		}

		return sprintf( '<div class="bma-c-logo-card__logo">%s</div>', $img );
	}

	/**
	 * Register both parent and child shortcodes.
	 */
	public static function register(): void {
		\add_shortcode( 'bma_logo_card', array( self::class, 'render' ) );
		\add_shortcode( 'bma_logo_card_item', array( self::class, 'renderItem' ) );
	}
}
