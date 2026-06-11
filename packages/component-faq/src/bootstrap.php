<?php
/**
 * balefireict/component-faq — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox
 * names so existing themes keep working), registers the parent + child
 * shortcodes, wires vc_map on vc_before_init, and registers the
 * WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\Faq
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_faq_attr' ) ) {
	/**
	 * Read a value that WPBakery may have written with underscores converted to hyphens.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $snake   Canonical snake_case attribute name.
	 * @param string $compact Optional compact alias.
	 * @return string Resolved value, or '' if none set.
	 */
	function bma_faq_attr( array $atts, string $snake, string $compact = '' ): string {
		return \Balefire\Component\Faq\Faq::attr( $atts, $snake, $compact );
	}
}

if ( ! function_exists( 'bma_faq_render_title' ) ) {
	/**
	 * Render the FAQ heading.
	 *
	 * @param string $title Heading text.
	 * @return string HTML, or '' when empty.
	 */
	function bma_faq_render_title( string $title ): string {
		return \Balefire\Component\Faq\Faq::renderTitle( $title );
	}
}

if ( ! function_exists( 'bma_faq_item_class' ) ) {
	/**
	 * Compute the item class list for a given style variant.
	 *
	 * @param string $style Style variant.
	 * @return string Space-separated class list.
	 */
	function bma_faq_item_class( string $style ): string {
		return \Balefire\Component\Faq\Faq::itemClass( $style );
	}
}

if ( ! function_exists( 'bma_faq_render_item' ) ) {
	/**
	 * Render one <details>/<summary> FAQ item.
	 *
	 * @param string $question Question text.
	 * @param string $answer   Answer HTML.
	 * @param string $style    Style variant.
	 * @return string HTML output.
	 */
	function bma_faq_render_item( string $question, string $answer, string $style = 'no-borders' ): string {
		return \Balefire\Component\Faq\Faq::renderItem( $question, $answer, $style );
	}
}

if ( ! function_exists( 'bma_faq_shortcode' ) ) {
	/**
	 * Render the parent [bma_faq] shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed children.
	 * @param string      $tag     Shortcode tag.
	 * @return string HTML output.
	 */
	function bma_faq_shortcode( $atts, $content = null, string $tag = '' ): string {
		return \Balefire\Component\Faq\Faq::render( $atts, $content, $tag );
	}
}

if ( ! function_exists( 'bma_faq_item_shortcode' ) ) {
	/**
	 * Render one [bma_faq_item] child shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Answer HTML.
	 * @param string      $tag     Shortcode tag.
	 * @return string HTML output.
	 */
	function bma_faq_item_shortcode( $atts, $content = null, string $tag = '' ): string {
		return \Balefire\Component\Faq\Faq::itemShortcode( $atts, $content, $tag );
	}
}

$bma_faq_boot = function (): void {
	\Balefire\Component\Faq\Faq::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\Faq\Faq::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\Faq\Faq::class, 'registerContainerClass' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_faq_boot();
} else {
	add_action( 'plugins_loaded', $bma_faq_boot, 20 );
}
unset( $bma_faq_boot );
