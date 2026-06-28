<?php
/**
 * BMA Reviews Slider — [vmg_reviews_slider].
 *
 * Full-bleed testimonial slider over a `review` custom post type. One quote on
 * screen at a time, crossfading. Big serif quote, gold star pill, author +
 * source badge (multicolor Google "G" for Google reviews), faint edge
 * chevrons.
 *
 * BARE output: no background or padding wrapper of its own beyond vertical
 * rhythm. Drop it in a full-width WPBakery row and style the row background
 * (the consumer owns bg color/image/blur). Text colors assume a dark section.
 *
 * CPT-backed component. It reads a site-provided `review` post type with:
 *   - post_title   = reviewer name
 *   - post_content = review text
 *   - meta vmg_rating       (0–5, decimal ok)
 *   - meta vmg_review_date  (Y-m-d) — reserved, not rendered in the slide
 *   - meta vmg_source_url   (link to the original review)
 *   - taxonomy review_source   (google|facebook|theknot)
 *   - taxonomy review_category (optional content filter)
 * Where that CPT does not exist (any non-David-Tours theme), the WP_Query
 * returns nothing, the htmx fragment comes back empty, and the slider hides
 * itself — safe no-op.
 *
 * PAGE-CACHE SAFE (htmx fragment): the shortcode renders a cacheable shell
 * only; the slides load client-side via htmx from an admin-ajax endpoint
 * (wp_ajax_[nopriv_]vmg_reviews_slider) that sends nocache_headers(). Page
 * caches (WPE, Cloudflare) can hold the page forever — reviews stay fresh.
 * Slide count is hard-capped at MAX_REVIEWS (12) per fetch.
 *
 * Requires htmx on window (the consumer theme enqueues htmx globally — the
 * vmg theme bundles it via main.js). Carousel JS itself ships inline.
 * src/style.css is auto-imported by the consumer theme's Vite pipeline.
 *
 * @package Balefire\Component\ReviewsSlider
 */

declare( strict_types=1 );

namespace Balefire\Component\ReviewsSlider;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [vmg_reviews_slider] shortcode.
 *
 * @package Balefire\Component\ReviewsSlider
 */
final class ReviewsSlider {

	/**
	 * Hard cap on slides per fetch. Keeps the htmx fragment payload small.
	 */
	private const MAX_REVIEWS = 12;

	/**
	 * Register the shortcode base + the public htmx fragment endpoint.
	 */
	public static function register(): void {
		add_shortcode( 'vmg_reviews_slider', array( self::class, 'render' ) );
		add_action( 'wp_ajax_vmg_reviews_slider', array( self::class, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_vmg_reviews_slider', array( self::class, 'ajax' ) );
	}

	/**
	 * WPBakery editor mapping. Surfaces the element under "Custom Elements"
	 * with every shortcode att exposed as a param. Hooked on vc_before_init.
	 *
	 * No php_class_name: WPBakery defaults non-container elements to
	 * WPBakeryShortCodeFishBones, which is all this needs.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'        => __( 'Reviews Slider', 'balefire' ),
				'base'        => 'vmg_reviews_slider',
				'category'    => __( 'Custom Elements', 'balefire' ),
				'icon'        => 'vc_icon-vc-images-carousel',
				'description' => __( 'BMA — Crossfading testimonial slider over the Reviews CPT. Bare output — drop it in a full-width row and style the row background.', 'balefire' ),
				'params'      => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Eyebrow', 'balefire' ),
						'param_name'  => 'eyebrow',
						'value'       => 'Customers Give Us High Praise',
						'admin_label' => true,
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Source Filter', 'balefire' ),
						'param_name' => 'source',
						'value'      => array(
							__( 'All Sources', 'balefire' ) => '',
							__( 'Google', 'balefire' )      => 'google',
							__( 'Facebook', 'balefire' )    => 'facebook',
							__( 'The Knot', 'balefire' )    => 'theknot',
						),
						'std'        => '',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Category Filter', 'balefire' ),
						'param_name'  => 'category',
						'description' => __( 'review_category slug(s), comma-separated. Leave blank for all.', 'balefire' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Minimum Rating', 'balefire' ),
						'param_name' => 'min_rating',
						'value'      => array(
							__( 'Any', 'balefire' )       => '',
							__( '5 stars only', 'balefire' ) => '5',
							__( '4 stars and up', 'balefire' ) => '4',
							__( '3 stars and up', 'balefire' ) => '3',
						),
						'std'        => '',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Count', 'balefire' ),
						'param_name'  => 'count',
						'value'       => '12',
						'description' => __( 'Max 12 per fetch (hard cap). Slides load fresh via htmx, so page caching never stales them.', 'balefire' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Order', 'balefire' ),
						'param_name' => 'orderby',
						'value'      => array(
							__( 'Newest first', 'balefire' ) => 'date',
							__( 'Random', 'balefire' )       => 'rand',
						),
						'std'        => 'date',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Autoplay', 'balefire' ),
						'param_name' => 'autoplay',
						'value'      => array( __( 'Advance automatically', 'balefire' ) => 'yes' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Autoplay Interval (ms)', 'balefire' ),
						'param_name'  => 'interval',
						'value'       => '7000',
						'dependency'  => array(
							'element' => 'autoplay',
							'value'   => array( 'yes' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Source slug => human label.
	 *
	 * @return array<string,string>
	 */
	private static function sourceLabels(): array {
		return array(
			'google'   => 'Google',
			'facebook' => 'Facebook',
			'theknot'  => 'The Knot',
		);
	}

	/**
	 * Standard multi-color Google "G" glyph.
	 *
	 * @return string
	 */
	private static function googleIcon(): string {
		return '<svg class="reviews-slide__source-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">'
			. '<path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>'
			. '<path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/>'
			. '<path fill="#FBBC05" d="M5.84 14.09a6.6 6.6 0 0 1 0-4.18V7.07H2.18a11 11 0 0 0 0 9.86l3.66-2.84z"/>'
			. '<path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>'
			. '</svg>';
	}

	/**
	 * Round Facebook glyph.
	 *
	 * @return string
	 */
	private static function facebookIcon(): string {
		return '<svg class="reviews-slide__source-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">'
			. '<circle cx="12" cy="12" r="12" fill="#1877F2"/>'
			. '<path fill="#fff" d="M15.12,12.47l.38-2.47h-2.37V8.4c0-.68.33-1.34,1.4-1.34h1.08V4.96s-.98-.17-1.91-.17c-1.95,0-3.22,1.18-3.22,3.32V10H8.31v2.47h2.17v5.98h2.65v-5.98Z"/>'
			. '</svg>';
	}

	/**
	 * Round The Knot glyph.
	 *
	 * @return string
	 */
	private static function knotIcon(): string {
		return '<svg class="reviews-slide__source-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 84.12 84.12" width="20" height="20" aria-hidden="true" focusable="false">'
			. '<circle cx="42.06" cy="42.06" r="42.06" fill="#f4c"/>'
			. '<path d="M60.16,51.24c-2,5.37-5.16,9.61-10.27,9.71-7.28.23-14.09-9.39-16.91-13.85-.23-.23,0-.23,0-.23,4.93,1.41,12.45,1.17,17.38-2.11,3.52-2.58,7.28-6.11,9.39-14.32,0,0,0-.23-.23-.23l-8.45-1.64-.23.23c-1.17,5.4-4.93,11.51-14.56,9.63l1.88-22.78-.23-.23-8.45-1.41s-.23,0-.23.23l-1.64,20.43h-.23c-.94-.94-2.35-2.35-3.29-3.52h-.23c-2.58,1.64-4.93,3.29-7.28,4.7v.23c1.88,4.23,6.81,12.68,9.39,16.44v.1-.1l-.47,6.81c-.23,3.76-.7,7.98-.7,8.92l.23.23,8.69,1.41s.23,0,.23-.23l.47-6.57h.23c4.23,4.23,8.92,7.04,14.09,7.04,4.55.23,10.62-1.78,14.62-6.81,2.55-3.22,3.03-4.63,4.24-7.78l-7.41-4.28Z"/>'
			. '</svg>';
	}

	/**
	 * Chevron SVG (paths lifted from layouts/sections/reviews-slider.svg).
	 *
	 * @param string $dir 'prev' or 'next'.
	 * @return string
	 */
	private static function chevron( string $dir ): string {
		$path = 'prev' === $dir
			? 'M21.328,33.1,6.913,18.816l14.75-15L18.224,0,.335,18.851,18.224,36.867Z'
			: 'M.335,33.1,14.75,18.816,0,3.819,3.439,0,21.328,18.851,3.439,36.867Z';
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.4 37.9" width="22" height="38" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . $path . '"/></svg>';
	}

	/**
	 * Inline carousel engine. Crossfades one slide at a time, with prev/next,
	 * drag/swipe, keyboard arrows, and optional autoplay (paused on hover,
	 * focus, and when the tab is hidden). Shipped inline so the package needs
	 * no theme JS. Guard prevents double-binding across multiple instances.
	 *
	 * @return string
	 */
	private static function inlineScript(): string {
		ob_start();
		?>
		<script>
		(function () {
			if (window.__vmgReviewsSliderInit) { return; }
			window.__vmgReviewsSliderInit = true;

			function initReviewsSliders() {
				document.querySelectorAll('[data-reviews-slider]').forEach(function (root) {
					if (root.dataset.reviewsBound) { return; }

					var slides = Array.prototype.slice.call(root.querySelectorAll('.reviews-slide'));
					// Slides arrive via an htmx fragment — if they're not in
					// yet, skip WITHOUT marking bound; htmx:afterSettle re-runs.
					if (slides.length === 0) { return; }
					root.dataset.reviewsBound = '1';
					if (slides.length < 2) { root.setAttribute('data-single', ''); return; }

					var n = slides.length;
					var active = 0;
					var timer = null;
					var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
					var autoplay = root.dataset.autoplay === 'true' && !reduceMotion;
					var interval = Math.max(2000, parseInt(root.dataset.interval, 10) || 7000);

					function render() {
						slides.forEach(function (slide, i) {
							var on = i === active;
							slide.dataset.pos = on ? 'active' : 'hidden';
							slide.setAttribute('aria-hidden', on ? 'false' : 'true');
						});
					}
					function step(dir) { active = (active + dir + n) % n; render(); }
					function stop() { if (timer) { clearInterval(timer); timer = null; } }
					function start() { if (!autoplay) { return; } stop(); timer = setInterval(function () { step(1); }, interval); }
					function go(dir) { step(dir); start(); }

					var prev = root.querySelector('.reviews-slider__prev');
					var next = root.querySelector('.reviews-slider__next');
					if (prev) { prev.addEventListener('click', function () { go(-1); }); }
					if (next) { next.addEventListener('click', function () { go(1); }); }

					root.addEventListener('keydown', function (e) {
						if (e.key === 'ArrowLeft') { go(-1); e.preventDefault(); }
						else if (e.key === 'ArrowRight') { go(1); e.preventDefault(); }
					});

					var track = root.querySelector('.reviews-slider__track');
					var startX = null;
					if (track) {
						track.addEventListener('pointerdown', function (e) { startX = e.clientX; track.setPointerCapture(e.pointerId); });
						track.addEventListener('pointermove', function (e) {
							if (startX === null) { return; }
							var dx = e.clientX - startX;
							if (Math.abs(dx) > 40) { go(dx < 0 ? 1 : -1); startX = null; }
						});
						var endGesture = function () { startX = null; };
						track.addEventListener('pointerup', endGesture);
						track.addEventListener('pointercancel', endGesture);
					}

					root.addEventListener('pointerenter', stop);
					root.addEventListener('pointerleave', start);
					root.addEventListener('focusin', stop);
					root.addEventListener('focusout', start);
					document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });

					render();
					start();
				});
			}

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initReviewsSliders);
			} else {
				initReviewsSliders();
			}

			// Slides arrive as an htmx fragment after page load — re-run the
			// init once the swap settles, and hide any slider whose fragment
			// came back empty (no published reviews / CPT absent).
			document.body.addEventListener('htmx:afterSettle', function (e) {
				var root = e.target && e.target.closest ? e.target.closest('[data-reviews-slider]') : null;
				if (root && !root.querySelector('.reviews-slide')) { root.hidden = true; }
				initReviewsSliders();
			});
		})();
		</script>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Clamp a requested slide count to 1..MAX_REVIEWS. Legacy -1 / 0 /
	 * garbage all resolve to the cap.
	 *
	 * @param mixed $count Raw count value.
	 * @return int
	 */
	private static function clampCount( $count ): int {
		$count = (int) $count;
		return ( $count < 1 ) ? self::MAX_REVIEWS : min( $count, self::MAX_REVIEWS );
	}

	/**
	 * Render the [vmg_reviews_slider] shortcode — the cacheable SHELL only.
	 *
	 * Eyebrow + empty track + nav chevrons. The track htmx-GETs the slide
	 * fragment from admin-ajax on load, so page caches never stale the
	 * reviews and the payload stays capped at MAX_REVIEWS. If the fragment
	 * comes back empty, the inline engine hides the whole section.
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render( $atts ): string {
		$atts = shortcode_atts(
			array(
				'eyebrow'    => 'Customers Give Us High Praise',
				'source'     => '',
				'category'   => '',
				'min_rating' => '',
				'count'      => (string) self::MAX_REVIEWS,
				'orderby'    => 'date',
				'autoplay'   => 'no',
				'interval'   => '7000',
			),
			(array) $atts,
			'vmg_reviews_slider'
		);

		$autoplay = 'yes' === $atts['autoplay'] ? 'true' : 'false';
		$interval = max( 2000, (int) $atts['interval'] );

		$fragment_url = add_query_arg(
			array(
				'action'     => 'vmg_reviews_slider',
				'source'     => rawurlencode( $atts['source'] ),
				'category'   => rawurlencode( $atts['category'] ),
				'min_rating' => rawurlencode( $atts['min_rating'] ),
				'count'      => self::clampCount( $atts['count'] ),
				'orderby'    => 'rand' === $atts['orderby'] ? 'rand' : 'date',
			),
			admin_url( 'admin-ajax.php' )
		);

		ob_start();
		?>
		<div
			class="reviews-slider"
			data-reviews-slider
			data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
			data-interval="<?php echo esc_attr( (string) $interval ); ?>"
			role="region"
			aria-roledescription="<?php esc_attr_e( 'carousel', 'balefire' ); ?>"
			aria-label="<?php esc_attr_e( 'Customer reviews', 'balefire' ); ?>"
		>
			<div class="reviews-slider__inner">
				<?php if ( '' !== $atts['eyebrow'] ) : ?>
					<p class="reviews-slider__eyebrow"><?php echo esc_html( $atts['eyebrow'] ); ?></p>
				<?php endif; ?>

				<div
					class="reviews-slider__track"
					aria-live="polite"
					hx-get="<?php echo esc_url( $fragment_url ); ?>"
					hx-trigger="load once"
					hx-swap="innerHTML"
				></div>
			</div>

			<button type="button" class="reviews-slider__nav reviews-slider__prev" aria-label="<?php esc_attr_e( 'Previous review', 'balefire' ); ?>">
				<?php echo self::chevron( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?>
			</button>
			<button type="button" class="reviews-slider__nav reviews-slider__next" aria-label="<?php esc_attr_e( 'Next review', 'balefire' ); ?>">
				<?php echo self::chevron( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?>
			</button>
		</div>
		<?php
		echo self::inlineScript(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline script

		return (string) ob_get_clean();
	}

	/**
	 * admin-ajax endpoint (wp_ajax_[nopriv_]vmg_reviews_slider): prints the
	 * slide fragment for htmx. Public, read-only, inputs sanitized, count
	 * hard-capped — no nonce (a nonce baked into a page-cached shell would
	 * expire and break the fetch). Sends nocache_headers() so CDN/page
	 * caches never hold the fragment — that's the cache-busting point.
	 */
	public static function ajax(): void {
		nocache_headers();

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- public read-only fragment.
		$args = array(
			'source'     => sanitize_text_field( wp_unslash( $_GET['source'] ?? '' ) ),
			'category'   => sanitize_text_field( wp_unslash( $_GET['category'] ?? '' ) ),
			'min_rating' => sanitize_text_field( wp_unslash( $_GET['min_rating'] ?? '' ) ),
			'count'      => self::clampCount( wp_unslash( $_GET['count'] ?? self::MAX_REVIEWS ) ),
			'orderby'    => 'rand' === sanitize_key( wp_unslash( $_GET['orderby'] ?? '' ) ) ? 'rand' : 'date',
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		echo self::renderSlides( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fragment built from escaped parts.
		wp_die( '', '', array( 'response' => 200 ) );
	}

	/**
	 * Build the slide-only fragment (the htmx payload).
	 *
	 * @param array $args source|category|min_rating|count|orderby (sanitized).
	 * @return string Slide markup, or '' when nothing matches.
	 */
	private static function renderSlides( array $args ): string {
		$tax_query = array();
		if ( '' !== $args['source'] ) {
			$tax_query[] = array(
				'taxonomy' => 'review_source',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $args['source'] ) ),
			);
		}
		if ( '' !== $args['category'] ) {
			$tax_query[] = array(
				'taxonomy' => 'review_category',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $args['category'] ) ),
			);
		}

		$meta_query = array();
		if ( '' !== $args['min_rating'] && is_numeric( $args['min_rating'] ) ) {
			$meta_query[] = array(
				'key'     => 'vmg_rating',
				'value'   => (float) $args['min_rating'],
				'type'    => 'DECIMAL(3,1)',
				'compare' => '>=',
			);
		}

		$query = new \WP_Query(
			array(
				'post_type'      => 'review',
				'post_status'    => 'publish',
				'posts_per_page' => self::clampCount( $args['count'] ),
				'orderby'        => 'rand' === $args['orderby'] ? 'rand' : 'date',
				'order'          => 'DESC',
				'tax_query'      => $tax_query ?: null, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'meta_query'     => $meta_query ?: null, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'no_found_rows'  => true,
			)
		);

		if ( ! $query->have_posts() ) {
			return '';
		}

		$source_labels = self::sourceLabels();
		$google_icon   = self::googleIcon();
		$facebook_icon = self::facebookIcon();
		$knot_icon     = self::knotIcon();

		ob_start();
		$first = true;
		while ( $query->have_posts() ) :
			$query->the_post();
			$post_id = get_the_ID();
			$rating  = (float) get_post_meta( $post_id, 'vmg_rating', true );
			$stars   = max( 0, min( 5, (int) round( $rating ) ) );
			$url     = (string) get_post_meta( $post_id, 'vmg_source_url', true );
			$text    = trim( wp_strip_all_tags( get_the_content() ) );

			$source_terms = wp_get_object_terms( $post_id, 'review_source', array( 'fields' => 'slugs' ) );
			$source       = is_array( $source_terms ) && $source_terms ? $source_terms[0] : '';
			$source_label = $source_labels[ $source ] ?? '';
			?>
			<article
				class="reviews-slide"
				data-pos="<?php echo $first ? 'active' : 'hidden'; ?>"
				aria-hidden="<?php echo $first ? 'false' : 'true'; ?>"
				aria-roledescription="<?php esc_attr_e( 'slide', 'balefire' ); ?>"
			>
				<?php if ( $stars > 0 ) : ?>
					<div
						class="reviews-slide__stars"
						role="img"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %d: number of stars out of 5. */ __( '%d out of 5 stars', 'balefire' ), $stars ) ); ?>"
					>
						<?php echo str_repeat( '<span class="reviews-slide__star" aria-hidden="true">&#9733;</span>', $stars ); ?>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $text ) : ?>
					<blockquote class="reviews-slide__quote">&ldquo;<?php echo esc_html( $text ); ?>&rdquo;</blockquote>
				<?php endif; ?>

				<footer class="reviews-slide__footer">
					<cite class="reviews-slide__author">&ndash;&nbsp;<?php the_title(); ?></cite>
					<?php if ( 'google' === $source ) : ?>
						<span class="reviews-slide__source" aria-label="<?php esc_attr_e( 'Google review', 'balefire' ); ?>">
							<?php
							if ( '' !== $url ) {
								printf(
									'<a href="%s" target="_blank" rel="noopener nofollow" aria-label="%s">%s</a>',
									esc_url( $url ),
									esc_attr__( 'View this review on Google', 'balefire' ),
									$google_icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG
								);
							} else {
								echo $google_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG
							}
							?>
						</span>
					<?php elseif ( 'facebook' === $source ) : ?>
						<span class="reviews-slide__source" aria-label="<?php esc_attr_e( 'Facebook review', 'balefire' ); ?>">
							<?php
							if ( '' !== $url ) {
								printf(
									'<a href="%s" target="_blank" rel="noopener nofollow" aria-label="%s">%s</a>',
									esc_url( $url ),
									esc_attr__( 'View this review on Facebook', 'balefire' ),
									$facebook_icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG
								);
							} else {
								echo $facebook_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG
							}
							?>
						</span>
					<?php elseif ( 'theknot' === $source ) : ?>
						<span class="reviews-slide__source" aria-label="<?php esc_attr_e( 'The Knot review', 'balefire' ); ?>">
							<?php
							if ( '' !== $url ) {
								printf(
									'<a href="%s" target="_blank" rel="noopener nofollow" aria-label="%s">%s</a>',
									esc_url( $url ),
									esc_attr__( 'View this review on The Knot', 'balefire' ),
									$knot_icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG
								);
							} else {
								echo $knot_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG
							}
							?>
						</span>
					<?php elseif ( '' !== $source_label ) : ?>
						<span class="reviews-slide__source reviews-slide__source--text">
							<?php if ( '' !== $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( $source_label ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $source_label ); ?>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</footer>
			</article>
			<?php
			$first = false;
		endwhile;
		wp_reset_postdata();

		return (string) ob_get_clean();
	}
}
