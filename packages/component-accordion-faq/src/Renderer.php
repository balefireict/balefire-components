<?php
/**
 * Renderer for the [bma_accordion_faq] parent + [bma_accordion_faq_item] child shortcodes.
 *
 * Attribute-driven, shortcode-children-only. No ACF source.
 *
 * Parent: [bma_accordion_faq title="Frequently Asked Questions"] wraps a series
 *         of children. Renders the FAQ section.
 * Child:  [bma_accordion_faq_item question="Question?"]Answer HTML.[/bma_accordion_faq_item]
 *         Renders one <details> accordion row.
 *
 * @package Balefire\Component\AccordionFaq
 */

declare( strict_types=1 );

namespace Balefire\Component\AccordionFaq;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	/**
	 * Render the parent [bma_accordion_faq] shortcode. Wraps the children
	 * (already processed by do_shortcode) in the FAQ section markup.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes (children).
	 * @return string HTML output, or '' when there is no child content.
	 */
	public static function render( $atts, ?string $content = null ): string {
		$atts = \shortcode_atts(
			array(
				'title'    => '',
				'el_id'    => '',
				'el_class' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'bma_accordion_faq'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = (string) preg_replace( '/^\s*(?:<br\s*\/?>\s*)+/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(?:\s*<br\s*\/?>\s*)+$/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(<\/details>)\s*(?:<br\s*\/?>\s*)+(<details\b)/i', '$1$2', (string) $inner );
		$inner = (string) preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', (string) $inner );
		$inner = trim( (string) $inner );

		if ( '' === $inner ) {
			return '';
		}

		$classes = array( 'bma-c-accordion-faq' );
		$extra   = trim( (string) $atts['el_class'] );
		if ( '' !== $extra ) {
			$classes[] = $extra;
		}

		$id_attr = '';
		$el_id   = trim( (string) $atts['el_id'] );
		if ( '' !== $el_id ) {
			$id_attr = ' id="' . esc_attr( $el_id ) . '"';
		}

		$title      = trim( (string) $atts['title'] );
		$title_html = '';
		if ( '' !== $title ) {
			$title_html = '<h2 class="bma-c-accordion-faq__headline">' . esc_html( $title ) . '</h2>';
		}

		return '<section class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $id_attr . '>'
			. '<div class="bma-c-accordion-faq__inner">'
			. $title_html
			. '<div class="bma-c-accordion-faq__list">' . $inner . '</div>'
			. '</div>'
			. '</section>';
	}

	/**
	 * Render one [bma_accordion_faq_item] child.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML (passes between opening/closing tags).
	 * @return string HTML output, or '' when there is no question.
	 */
	public static function renderItem( $atts, ?string $content = null ): string {
		$atts = \shortcode_atts(
			array(
				'question' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'bma_accordion_faq_item'
		);

		$question = trim( (string) $atts['question'] );
		if ( '' === $question ) {
			return '';
		}

		$answer = do_shortcode( shortcode_unautop( (string) $content ) );
		// WPBakery/the_content can wrap nested shortcode HTML as `</p>...<p>`.
		$answer = (string) preg_replace( '/^\s*<\/p>\s*/i', '', (string) $answer );
		$answer = (string) preg_replace( '/\s*<p>\s*$/i', '', (string) $answer );
		$answer = (string) preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', (string) $answer );
		$answer = trim( wp_kses_post( (string) $answer ) );

		// FAQ items always render closed (open-by-default unsupported site-wide).
		return sprintf(
			'<details class="bma-c-accordion-faq__item"><summary class="bma-c-accordion-faq__question">%s</summary><div class="bma-c-accordion-faq__answer">%s</div></details>',
			esc_html( $question ),
			$answer
		);
	}

	/**
	 * Register both [bma_accordion_faq] and [bma_accordion_faq_item] shortcodes.
	 */
	public static function register(): void {
		\add_shortcode( 'bma_accordion_faq', array( self::class, 'render' ) );
		\add_shortcode( 'bma_accordion_faq_item', array( self::class, 'renderItem' ) );
	}
}
