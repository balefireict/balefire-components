<?php
/**
 * BMA Duo Blog shortcode.
 *
 * Renders the latest N posts (default 2) as large image-cover cards in a
 * 2-column grid. Each card: featured image fill, dark wash + bottom
 * gradient, white title bottom-left, "Read More" button.
 *
 * Built from davidtours/layouts/sections/duo-blog.svg.
 *
 * Source of truth class. Global function wrapper and vc_map wiring live
 * in bootstrap.php.
 *
 * @package Balefire\Component\DuoBlog
 */

declare( strict_types=1 );

namespace Balefire\Component\DuoBlog;

defined( 'ABSPATH' ) || exit;

/**
 * Static [bma_duo_blog] shortcode renderer.
 */
final class DuoBlog {

	/**
	 * Render the [bma_duo_blog] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when no posts.
	 */
	public static function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'count'       => '2',
				'button_text' => 'Read More',
				'class'       => '',
			),
			$atts,
			'bma_duo_blog'
		);

		$count       = max( 1, min( 6, (int) $atts['count'] ) );
		$button_text = trim( (string) $atts['button_text'] );
		if ( '' === $button_text ) {
			$button_text = 'Read More';
		}

		$query = new \WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $count,
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			)
		);

		if ( ! $query->have_posts() ) {
			return '';
		}

		$cards = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$cards[] = self::cardHtml( (int) get_the_ID(), $button_text );
		}
		wp_reset_postdata();

		$cards = array_filter( $cards );
		if ( empty( $cards ) ) {
			return '';
		}

		$classes = array(
			'bma-duo-blog',
			'bma-auto-grid',
			'auto-grid-cols-1',
			'md:auto-grid-cols-2',
			'auto-grid-gap-12',
		);
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		return sprintf(
			'<div class="%1$s">%2$s</div>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			implode( '', $cards )
		);
	}

	/**
	 * Render one post card.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $button_text Button label.
	 * @return string
	 */
	private static function cardHtml( int $post_id, string $button_text ): string {
		$title = get_the_title( $post_id );
		$url   = (string) get_permalink( $post_id );
		if ( '' === $title || '' === $url ) {
			return '';
		}

		$image_html = '';
		$thumb_id   = (int) get_post_thumbnail_id( $post_id );
		if ( $thumb_id > 0 ) {
			$image_html = wp_get_attachment_image(
				$thumb_id,
				'large',
				false,
				array(
					'class'   => 'bma-duo-blog__img',
					'loading' => 'lazy',
				)
			);
		}

		return sprintf(
			'<a class="bma-duo-blog__card%1$s" href="%2$s">%3$s<span class="bma-duo-blog__content"><span class="bma-duo-blog__title">%4$s</span><span class="bma-duo-blog__btn">%5$s</span></span></a>',
			'' === $image_html ? ' bma-duo-blog__card--no-image' : '',
			esc_url( $url ),
			$image_html,
			esc_html( $title ),
			esc_html( $button_text )
		);
	}

	/**
	 * Register the [bma_duo_blog] shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'bma_duo_blog', array( self::class, 'render' ) );
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
				'name'        => __( 'Duo Blog', 'balefire' ),
				'base'        => 'bma_duo_blog',
				'category'    => __( 'Custom Elements', 'balefire' ),
				'description' => __( 'BMA — Latest posts as large image cards, 2-column grid.', 'balefire' ),
				'icon'        => 'vc_icon-vc-post-grid',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Count', 'balefire' ),
						'param_name'  => 'count',
						'value'       => '2',
						'description' => __( 'Number of latest posts to show (1-6). Default 2.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Button text', 'balefire' ),
						'param_name'  => 'button_text',
						'value'       => 'Read More',
						'description' => __( 'Label for the card button.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'value'       => '',
						'description' => __( 'Optional extra CSS class on the grid wrapper.', 'balefire' ),
					),
				),
			)
		);
	}
}
