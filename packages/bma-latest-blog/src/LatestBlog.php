<?php
/**
 * BMA Latest Blog shortcode.
 *
 * Renders a grid of the latest N posts. Each post is rendered as a
 * PostCard (Balefire\Components\LatestBlog\PostCard) — the same card
 * markup is available publicly via PostCard::render( $post_id ) for
 * blog archives / search results / other consumers.
 *
 * Shortcode tag: [latest_blog] (rockerbox original) + [bma_latest_blog] alias.
 *
 * Source of truth class. Global function wrapper and vc_map wiring live
 * in bootstrap.php.
 *
 * @package Balefire\Components\LatestBlog
 */

declare( strict_types=1 );

namespace Balefire\Components\LatestBlog;

defined( 'ABSPATH' ) || exit;

/**
 * Static [bma_latest_blog] shortcode renderer.
 *
 * @package Balefire\Components\LatestBlog
 */
final class LatestBlog {

	/**
	 * Render the [latest_blog] / [bma_latest_blog] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when no posts.
	 */
	public static function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'count'         => '3',
				'show_category' => 'false',
			),
			$atts,
			'latest_blog'
		);

		$count         = max( 1, min( 12, (int) $atts['count'] ) );
		$show_category = filter_var( $atts['show_category'], FILTER_VALIDATE_BOOLEAN );

		$query = new \WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => $count,
				'post_status'    => 'publish',
				'no_found_rows'  => true,
			)
		);

		if ( ! $query->have_posts() ) {
			return '';
		}

		$cards = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$cards[] = PostCard::render( null, $show_category );
		}
		wp_reset_postdata();

		if ( empty( $cards ) ) {
			return '';
		}

		return sprintf(
			'<div class="bma-latest-blog">%s</div>',
			implode( '', $cards )
		);
	}

	/**
	 * Register both [latest_blog] and [bma_latest_blog] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'latest_blog', array( self::class, 'render' ) );
		add_shortcode( 'bma_latest_blog', array( self::class, 'render' ) );
	}

	/**
	 * WPBakery vc_map registration.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'        => __( 'BMA Latest Blog', 'balefire' ),
				'base'        => 'latest_blog',
				'category'    => __( 'BMA Elements', 'balefire' ),
				'description' => __( 'Latest blog posts grid.', 'balefire' ),
				'icon'        => 'vc_icon-vc-post-grid',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Count', 'balefire' ),
						'param_name'  => 'count',
						'value'       => '3',
						'description' => __( 'Number of posts to show (1-12).', 'balefire' ),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'Show category', 'balefire' ),
						'param_name'  => 'show_category',
						'value'       => array(
							__( 'Yes', 'balefire' ) => 'true',
						),
						'std'         => '',
						'description' => __( 'Render the category pill above the title.', 'balefire' ),
					),
				),
			)
		);
	}
}
