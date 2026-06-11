<?php
/**
 * BMA FAQ shortcode (parent + child).
 *
 * Parent: [bma_faq title="..." style="no-borders"] wraps enclosed
 *         [bma_faq_item] children. Renders an optional heading and a
 *         <div class="bma-faq__list"> wrapper. A bare [bma_faq] with no
 *         children renders nothing.
 * Child:  [bma_faq_item question="..."]Answer HTML[/bma_faq_item]
 *         Renders one native <details>/<summary> accordion item. FAQ
 *         items always render closed — open-by-default is intentionally
 *         unsupported site-wide.
 *
 * Source of truth classes. Global function wrappers (bma_faq_shortcode,
 * bma_faq_item_shortcode, bma_faq_attr, bma_faq_render_title,
 * bma_faq_item_class, bma_faq_render_item) are defined in bootstrap.php.
 * add_shortcode, vc_map, and the WPBakeryShortCodesContainer subclass are
 * also wired there.
 *
 * Ported from rockerbox balefire theme: inc/shortcodes/bma-faq.php.
 *
 * @package Balefire\Component\Faq
 */

declare( strict_types=1 );

namespace Balefire\Component\Faq;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_faq] / [bma_faq_item] shortcodes.
 *
 * @package Balefire\Component\Faq
 */
final class Faq {

	public const STYLE_CHOICES = array( '', 'no-borders' );

	/**
	 * Read a value that WPBakery may have written with underscores converted
	 * to hyphens. Checks the snake_case key, its kebab-case variant, and an
	 * optional compact alias, returning the first non-empty match.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $snake   The canonical snake_case attribute name.
	 * @param string $compact Optional compact alias.
	 * @return string The resolved value, or '' if none set.
	 */
	public static function attr( array $atts, string $snake, string $compact = '' ): string {
		$kebab = str_replace( '_', '-', $snake );
		foreach ( array_filter( array( $snake, $kebab, $compact ) ) as $key ) {
			if ( isset( $atts[ $key ] ) && '' !== (string) $atts[ $key ] ) {
				return (string) $atts[ $key ];
			}
		}

		return '';
	}

	/**
	 * Render the FAQ heading.
	 *
	 * @param string $title Heading text.
	 * @return string HTML, or '' when empty.
	 */
	public static function renderTitle( string $title ): string {
		$title = trim( $title );
		if ( '' === $title ) {
			return '';
		}

		return '<h2 class="wp-block-heading has-text-align-center" id="faq-heading">' . esc_html( $title ) . '</h2>';
	}

	/**
	 * Compute the item class list for a given style variant.
	 *
	 * @param string $style Style variant ('no-borders' or '').
	 * @return string Space-separated class list.
	 */
	public static function itemClass( string $style ): string {
		$item_class = 'bma-faq__item';
		if ( 'no-borders' === $style ) {
			$item_class .= ' bma-faq__item--no-borders';
		}

		return $item_class;
	}

	/**
	 * Render one <details>/<summary> FAQ item.
	 *
	 * @param string $question Question text.
	 * @param string $answer   Answer HTML (raw shortcode content).
	 * @param string $style    Style variant.
	 * @return string HTML, or '' when the question is empty.
	 */
	public static function renderItem( string $question, string $answer, string $style = 'no-borders' ): string {
		$question = trim( $question );
		$answer   = trim( $answer );

		if ( '' === $question ) {
			return '';
		}

		$answer = wp_kses_post( do_shortcode( $answer ) );
		// WPBakery/the_content can wrap nested shortcode HTML as `</p>...<p>`.
		$answer = preg_replace( '/^\s*<\/p>\s*/i', '', (string) $answer );
		$answer = preg_replace( '/\s*<p>\s*$/i', '', (string) $answer );
		$answer = preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', (string) $answer );

		// FAQ items always render closed — open-by-default is intentionally unsupported (site-wide directive).
		return sprintf(
			'<details class="%s"><summary class="bma-faq__question">%s</summary><div class="bma-faq__answer">%s</div></details>',
			esc_attr( self::itemClass( $style ) ),
			esc_html( $question ),
			trim( (string) $answer )
		);
	}

	/**
	 * Render the parent [bma_faq] shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed [bma_faq_item] children.
	 * @param string      $tag     Shortcode tag.
	 * @return string HTML output, or '' when there are no children.
	 */
	public static function render( $atts, $content = null, string $tag = '' ): string {
		$atts = shortcode_atts(
			array(
				'style' => 'no-borders',
				'title' => '',
			),
			(array) $atts,
			$tag ? $tag : 'bma_faq'
		);

		$style = in_array( $atts['style'], array( '', 'no-borders' ), true ) ? $atts['style'] : 'no-borders';

		// Preferred WPBakery path: enclosed [bma_faq_item] children.
		if ( null !== $content && '' !== trim( (string) $content ) ) {
			$inner = preg_replace_callback(
				'/\[bma_faq_item\b(?![^\]]*\s_style=)/',
				static function () use ( $style ): string {
					return '[bma_faq_item _style="' . $style . '"';
				},
				shortcode_unautop( trim( (string) $content ) )
			);
			$inner = do_shortcode( (string) $inner );
			$inner = preg_replace( '/^\s*(?:<br\s*\/?>\s*)+/i', '', (string) $inner );
			$inner = preg_replace( '/(?:\s*<br\s*\/?>\s*)+$/i', '', (string) $inner );
			$inner = preg_replace( '/(<\/details>)\s*(?:<br\s*\/?>\s*)+(<details\b)/i', '$1$2', (string) $inner );
			$inner = trim( (string) $inner );

			if ( '' === $inner ) {
				return '';
			}

			$title = '' !== trim( (string) $atts['title'] ) ? (string) $atts['title'] : 'Frequently Asked Questions';

			return self::renderTitle( $title ) . '<div class="bma-faq__list">' . $inner . '</div>';
		}

		// FAQ is shortcode-children-only (no ACF source). A bare [bma_faq] with no
		// enclosed [bma_faq_item] children renders nothing.
		return '';
	}

	/**
	 * Render one [bma_faq_item] child shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Answer HTML.
	 * @param string      $tag     Shortcode tag.
	 * @return string HTML output.
	 */
	public static function itemShortcode( $atts, $content = null, string $tag = '' ): string {
		$atts = shortcode_atts(
			array(
				'question' => '',
				'_style'   => 'no-borders',
			),
			(array) $atts,
			$tag ? $tag : 'bma_faq_item'
		);

		return self::renderItem(
			self::attr( (array) $atts, 'question' ),
			(string) $content,
			self::attr( (array) $atts, '_style' ) ?: 'no-borders'
		);
	}

	/**
	 * Register both [bma_faq] and [bma_faq_item] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_faq', array( self::class, 'render' ) );
		add_shortcode( 'bma_faq_item', array( self::class, 'itemShortcode' ) );
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
				'name'                    => __( 'BMA FAQ', 'balefire' ),
				'base'                    => 'bma_faq',
				'php_class_name'          => 'WPBakeryShortCode_BMA_Faq',
				'category'                => __( 'BMA Elements', 'balefire' ),
				'description'             => __( 'Editable FAQ accordion list.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-toggle',
				'as_parent'               => array( 'only' => 'bma_faq_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
						'std'        => 'Frequently Asked Questions',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Style', 'balefire' ),
						'param_name' => 'style',
						'value'      => array(
							__( 'No borders', 'balefire' ) => 'no-borders',
						),
						'std'        => 'no-borders',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'BMA FAQ Item', 'balefire' ),
				'base'            => 'bma_faq_item',
				'category'        => __( 'BMA Elements', 'balefire' ),
				'description'     => __( 'A single question and answer used inside BMA FAQ.', 'balefire' ),
				'icon'            => 'vc_icon-vc-toggle',
				'as_child'        => array( 'only' => 'bma_faq' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Question', 'balefire' ),
						'param_name' => 'question',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Answer', 'balefire' ),
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
		if ( ! class_exists( 'WPBakeryShortCode_BMA_Faq' ) ) {
			eval( 'class WPBakeryShortCode_BMA_Faq extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
