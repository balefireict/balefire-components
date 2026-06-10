<?php
/**
 * balefireict/component-latest-blog — bootstrap.
 *
 * Defines thin global function wrappers, registers the [latest_blog] and
 * [bma_latest_blog] shortcodes, and wires vc_map on vc_before_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\LatestBlog
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_latest_blog_render' ) ) {
	/**
	 * Programmatic equivalent of do_shortcode('[latest_blog]').
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_latest_blog_render( array $atts = array() ): string {
		return \Balefire\Component\LatestBlog\LatestBlog::render( $atts );
	}
}

if ( ! function_exists( 'bma_post_card' ) ) {
	/**
	 * Programmatic equivalent of including the legacy post-card partial.
	 *
	 * Echoes the card HTML for the current post in the loop (or the
	 * optional $post_id). Same markup as the inline include in the
	 * [bma_latest_blog] shortcode.
	 *
	 * @param int|null $post_id Post ID; null = current post.
	 * @return string HTML output.
	 */
	function bma_post_card( ?int $post_id = null ): string {
		return \Balefire\Component\LatestBlog\PostCard::render( $post_id, false );
	}
}

$bma_latest_blog_boot = function (): void {
		\Balefire\Component\LatestBlog\LatestBlog::register();
		if ( function_exists( 'vc_map' ) ) {
			add_action( 'vc_before_init', array( \Balefire\Component\LatestBlog\LatestBlog::class, 'vcMap' ) );
		}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_latest_blog_boot();
} else {
	add_action( 'plugins_loaded', $bma_latest_blog_boot, 20 );
}
unset( $bma_latest_blog_boot );
