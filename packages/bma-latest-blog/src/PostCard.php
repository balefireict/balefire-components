<?php
/**
 * PostCard — renders a single blog post card.
 *
 * Vanilla, inlined from rockerbox's `inc/post-card.php`. No Tailwind utility
 * classes, no `--color-*` / `--space-*` token lookups (per project rule).
 * Spacing and colors are explicit hex / rem values; the consumer theme can
 * override via descendant selectors if needed.
 *
 * Used internally by Balefire\Components\LatestBlog\LatestBlog.
 * Public static method `PostCard::render( $post_id )` is exposed so blog
 * archives, search results, and other consumers can render the same card
 * markup without re-implementing it.
 *
 * @package Balefire\Components\LatestBlog
 */

declare( strict_types=1 );

namespace Balefire\Components\LatestBlog;

defined( 'ABSPATH' ) || exit;

/**
 * Static post card renderer.
 *
 * @package Balefire\Components\LatestBlog
 */
final class PostCard {

	/**
	 * Render a single blog post card.
	 *
	 * @param int|null $post_id        Post ID; null = current post in the loop.
	 * @param bool     $show_category  Render the category pill above the title.
	 * @return string HTML output, or '' when post is invalid or no content.
	 */
	public static function render( ?int $post_id = null, bool $show_category = false ): string {
		$post = get_post( $post_id );
		if ( null === $post || 'publish' !== get_post_status( $post ) ) {
			return '';
		}

		$post_id = (int) $post->ID;

		// Categories (first one only) for the optional pill.
		$category_html = '';
		if ( $show_category ) {
			$cats = get_the_category( $post_id );
			if ( ! empty( $cats ) ) {
				$cat = $cats[0];
				$category_html = sprintf(
					'<a class="bma-post-card__category" href="%s">%s</a>',
					esc_url( get_category_link( (int) $cat->term_id ) ),
					esc_html( $cat->name )
				);
			}
		}

		// Featured image (medium-large, 16:9 in CSS via aspect-ratio).
		$media_html = '';
		if ( has_post_thumbnail( $post_id ) ) {
			$media_html = get_the_post_thumbnail(
				$post_id,
				'medium_large',
				array(
					'class'    => 'bma-post-card__img',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}

		// Excerpt: prefer the manual excerpt, fall back to stripped content.
		$excerpt = '';
		if ( '' !== trim( (string) $post->post_excerpt ) ) {
			$excerpt = wp_strip_all_tags( (string) $post->post_excerpt );
		} else {
			$excerpt = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 24, '…' );
		}

		ob_start();
		?>
		<article id="post-<?php echo (int) $post_id; ?>" <?php post_class( 'bma-post-card' ); ?>>
			<a class="bma-post-card__link" href="<?php the_permalink( $post_id ); ?>">
				<?php if ( '' !== $media_html ) : ?>
					<div class="bma-post-card__media"><?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail returns safe HTML ?></div>
				<?php endif; ?>

				<div class="bma-post-card__body">
					<?php echo $category_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url + esc_html inside ?>

					<h3 class="bma-post-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>

					<?php if ( '' !== $excerpt ) : ?>
						<p class="bma-post-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>

					<span class="bma-post-card__cta">
						Read more<?php
						$arrow = function_exists( 'bma_arrow_svg' ) ? bma_arrow_svg() : '';
						echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</span>
				</div>
			</a>
		</article>
		<?php
		$html = (string) ob_get_clean();
		return trim( (string) preg_replace( '/>\s+</', '><', $html ) );
	}
}
