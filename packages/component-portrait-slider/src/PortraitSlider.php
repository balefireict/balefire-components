<?php
/**
 * BMA Portrait Slider shortcode (parent + child).
 *
 * Parent: [bma_portrait_slider] (alias [bma-portrait-slider]) wraps a series
 *         of [bma_portrait_slide] children in a Swiper carousel.
 * Child:  [bma_portrait_slide title="" image="" linkurl="" newtab=""]
 *         (alias [bma-portrait-slide]) renders one portrait card. Renders an
 *         <a> when a link is resolved, a <div> otherwise.
 *
 * Source of truth for the renderer logic and classes. Global function
 * wrappers (bma_portrait_slider_shortcode, bma_portrait_slide_shortcode,
 * bma_portrait_slider_attr, bma_render_portrait_slide,
 * bma_portrait_slider_wrap) are defined in bootstrap.php. add_shortcode,
 * vc_map, and the WPBakeryShortCodesContainer subclass are also wired there.
 *
 * Ported from rockerbox theme: inc/shortcodes/bma-industry-slider.php.
 *
 * @package Balefire\Component\PortraitSlider
 */

declare( strict_types=1 );

namespace Balefire\Component\PortraitSlider;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the portrait slider parent + child shortcodes.
 *
 * @package Balefire\Component\PortraitSlider
 */
final class PortraitSlider {

	/**
	 * Read a value that WPBakery may have written with underscores converted
	 * to hyphens (or with a compact alias).
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $snake   Canonical (snake_case) param name.
	 * @param string $compact Optional compact alias.
	 * @return string Resolved value, or '' when none set.
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
	 * Render a single portrait slide from a normalized data array.
	 *
	 * @param array $data title, image, href, new_tab.
	 * @return string HTML output.
	 */
	public static function renderSlideData( array $data ): string {
		$title   = trim( (string) ( $data['title'] ?? '' ) );
		$image   = (string) ( $data['image'] ?? '' );
		$href    = trim( (string) ( $data['href'] ?? '' ) );
		$new_tab = ! empty( $data['new_tab'] );

		$img_html = '';
		if ( is_numeric( $image ) && (int) $image > 0 ) {
			$img_html = wp_get_attachment_image(
				(int) $image,
				'large',
				false,
				array(
					'class'   => 'bma-portrait-slide__image',
					'loading' => 'lazy',
				)
			);
		} elseif ( '' !== $image ) {
			$img_html = '<img class="bma-portrait-slide__image" src="' . esc_url( $image ) . '" alt="" loading="lazy" />';
		}

		$tag       = $href ? 'a' : 'div';
		$tag_attrs = '';
		if ( $href ) {
			$tag_attrs = ' href="' . esc_url( $href ) . '"';
			if ( $new_tab ) {
				$tag_attrs .= ' target="_blank" rel="noopener noreferrer"';
			}
		}

		ob_start();
		?>
		<div class="swiper-slide">
			<<?php echo $tag; ?> class="bma-portrait-slide"<?php echo $tag_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag_attrs is a hardcoded attr name + esc_url'd href ?>>
				<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image / esc_url'd <img> ?>
				<div class="bma-portrait-slide__overlay" aria-hidden="true"></div>
				<div class="bma-portrait-slide__content">
					<?php if ( '' !== $title ) : ?>
						<h3 class="bma-portrait-slide__title"><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>
				</div>
			</<?php echo $tag; ?>>
		</div>
		<?php
		return trim( (string) ob_get_clean() );
	}

	/**
	 * Wrap the processed inner slides in the Swiper carousel scaffolding.
	 *
	 * @param string $inner Pre-rendered .swiper-slide markup.
	 * @return string HTML output, or '' when inner is empty.
	 */
	public static function wrap( string $inner ): string {
		if ( '' === trim( $inner ) ) {
			return '';
		}

		// Theme-only helper: enqueue Swiper assets when the theme provides it.
		if ( class_exists( '\\Balefire\\Assets' ) && method_exists( '\\Balefire\\Assets', 'needsSwiper' ) ) {
			\Balefire\Assets::needsSwiper();
		}

		$slider_id = 'bma-portrait-slider-' . substr( md5( uniqid( '', true ) ), 0, 12 );

		ob_start();
		?>
		<div class="bma-portrait-slider bma-portrait-swiper-slides">
			<div id="<?php echo esc_attr( $slider_id ); ?>" class="swiper">
				<div class="swiper-wrapper">
					<?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped slide markup ?>
				</div>
			</div>

			<button type="button" class="swiper-button-prev" aria-label="Previous slide">
				<svg class="bma-portrait-swiper-arrow" xmlns="http://www.w3.org/2000/svg" width="13.884" height="24" viewBox="0 0 13.884 24" aria-hidden="true">
					<path d="M638.554,795.729l9.384-9.3-9.6-9.762,2.239-2.486,11.646,12.272-11.646,11.728Z" transform="translate(-638.336 -774.185)" fill="currentColor"></path>
				</svg>
			</button>
			<button type="button" class="swiper-button-next" aria-label="Next slide">
				<svg class="bma-portrait-swiper-arrow" xmlns="http://www.w3.org/2000/svg" width="13.884" height="24" viewBox="0 0 13.884 24" aria-hidden="true">
					<path d="M638.554,795.729l9.384-9.3-9.6-9.762,2.239-2.486,11.646,12.272-11.646,11.728Z" transform="translate(-638.336 -774.185)" fill="currentColor"></path>
				</svg>
			</button>

			<div class="swiper-pagination"></div>

			<script>
			(function() {
				var config = {"slidesPerView":1,"spaceBetween":16,"loop":true,"breakpoints":{"640":{"slidesPerView":2},"1024":{"slidesPerView":4}},"pagination":{"el":"#<?php echo esc_js( $slider_id ); ?> ~ .swiper-pagination","clickable":true},"navigation":{"nextEl":"#<?php echo esc_js( $slider_id ); ?> ~ .swiper-button-next","prevEl":"#<?php echo esc_js( $slider_id ); ?> ~ .swiper-button-prev"}};
				var selector = '#<?php echo esc_js( $slider_id ); ?>';
				var attempts = 0;
				function init() {
					if (typeof Swiper !== 'undefined') {
						new Swiper(selector, config);
					} else if (attempts < 20) {
						attempts++;
						setTimeout(init, 50);
					}
				}
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', init);
				} else {
					init();
				}
			})();
			</script>
		</div>
		<?php
		return trim( (string) ob_get_clean() );
	}

	/**
	 * Render the parent [bma_portrait_slider] shortcode.
	 *
	 * @param mixed       $atts    Shortcode attributes.
	 * @param string|null $content Inner [bma_portrait_slide] shortcodes.
	 * @param string      $tag     Shortcode tag.
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function renderSlider( $atts, $content = null, string $tag = '' ): string {
		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = preg_replace( '/^\s*<br\s*\/?>\s*/i', '', (string) $inner );
		$inner = preg_replace( '/<br\s*\/?>\s*(?=<div\s+class="swiper-slide\b)/i', '', (string) $inner );
		$inner = preg_replace( '/(<\/div>)\s*<br\s*\/?>/i', '$1', (string) $inner );

		return self::wrap( trim( (string) $inner ) );
	}

	/**
	 * Render one [bma_portrait_slide] child shortcode.
	 *
	 * @param mixed       $atts    Shortcode attributes.
	 * @param string|null $content Unused.
	 * @param string      $tag     Shortcode tag.
	 * @return string HTML output.
	 */
	public static function renderSlide( $atts, $content = null, string $tag = '' ): string {
		$atts = (array) $atts;
		$link = self::attr( $atts, 'linkurl', 'link_url' );

		$link_page = self::attr( $atts, 'linkpage', 'link_page' );
		// Soft dep: component-href resolves a page id or raw URL to an href.
		if ( function_exists( 'bma_resolve_href' ) ) {
			$href = (string) bma_resolve_href( $link_page, $link );
		} else {
			$href = $link;
		}

		return self::renderSlideData(
			array(
				'title'   => self::attr( $atts, 'title' ),
				'image'   => self::attr( $atts, 'image' ),
				'href'    => $href,
				'new_tab' => filter_var( self::attr( $atts, 'newtab', 'new_tab' ), FILTER_VALIDATE_BOOLEAN ),
			)
		);
	}

	/**
	 * Register both parent and child shortcodes (canonical + alias bases).
	 */
	public static function register(): void {
		add_shortcode( 'bma_portrait_slider', 'bma_portrait_slider_shortcode' );
		add_shortcode( 'bma-portrait-slider', 'bma_portrait_slider_shortcode' );
		add_shortcode( 'bma_portrait_slide', 'bma_portrait_slide_shortcode' );
		add_shortcode( 'bma-portrait-slide', 'bma_portrait_slide_shortcode' );
	}

	/**
	 * WPBakery vc_map registration for the parent + child elements.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'Portrait Slider', 'balefire' ),
				'base'                    => 'bma_portrait_slider',
				'php_class_name'          => 'WPBakeryShortCode_BMA_PortraitSlider',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Swipeable portrait image cards.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-images-carousel',
				'as_parent'               => array( 'only' => 'bma_portrait_slide' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Portrait Slide', 'balefire' ),
				'base'            => 'bma_portrait_slide',
				'php_class_name'  => 'WPBakeryShortCode_BMA_PortraitSlide',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single portrait slider card.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_portrait_slider' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
					),
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Image', 'balefire' ),
						'param_name'  => 'image',
						'description' => __( 'Portrait image from the Media Library.', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Link URL', 'balefire' ),
						'param_name' => 'linkurl',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Open in new tab', 'balefire' ),
						'param_name' => 'newtab',
						'value'      => array( __( 'Yes', 'balefire' ) => 'true' ),
					),
				),
			)
		);
	}

	/**
	 * Register the parent container subclass and the child preview element
	 * class so the elements render correctly in the WPBakery editor.
	 *
	 * When the shared BakeryPreview infra is present, the parent registers as
	 * a preview-enabled container and the child gets a thumbnail + title
	 * preview. When it is absent (soft dep), the parent falls back to the
	 * plain eval'd WPBakeryShortCodesContainer subclass and the child needs
	 * no fallback (WPBakery defaults non-container elements to FishBones).
	 *
	 * Hooked on vc_after_init.
	 */
	public static function registerPreviewClasses(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}

		if ( class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_PortraitSlider',
				array()
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_PortraitSlide',
				array(
					'image' => 'image',
					'title' => 'title',
				)
			);
			return;
		}

		// Fallback: plain container subclass (child needs none).
		if ( ! class_exists( 'WPBakeryShortCode_BMA_PortraitSlider' ) ) {
			eval( 'class WPBakeryShortCode_BMA_PortraitSlider extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
