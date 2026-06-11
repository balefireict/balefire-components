<?php
/**
 * BMA Bakery Preview — shared WPBakery backend-editor preview infrastructure.
 *
 * Gives BMA elements readable previews in the backend editor: thumbnail
 * (mirrors how vc_single_image does it, via wpb_getImageBySize on the
 * attach_image param) and title/content excerpt, instead of a bare
 * grey element name.
 *
 * Usage from a component's vcMap()/registerContainerClass():
 *
 *   // Single (non-container) element:
 *   Preview::registerElementClass( 'WPBakeryShortCode_BMA_Image', array(
 *       'image' => 'id',          // attach_image param to thumbnail
 *       'title' => 'title',       // param to show as the headline
 *       'text'  => 'content',     // param (or 'content') for the excerpt
 *   ) );
 *
 *   // Container parent element:
 *   Preview::registerContainerClass( 'WPBakeryShortCode_BMA_Faq', array(
 *       'title' => 'title',
 *   ) );
 *
 * Both must be called on vc_after_init (after vc_map has run), exactly like
 * the plain eval'd classes they replace. The generated classes override
 * outputTitle() to append the preview block under the element title bar.
 *
 * @package Balefire\Component\BakeryPreview
 */

declare( strict_types=1 );

namespace Balefire\Component\BakeryPreview;

defined( 'ABSPATH' ) || exit;

/**
 * Static factory for preview-enabled WPBakery shortcode classes.
 */
final class Preview {

	/**
	 * Per-class preview param config, keyed by generated class name.
	 *
	 * @var array<string, array{image?: string, title?: string, text?: string}>
	 */
	private static array $config = array();

	/**
	 * Whether the admin CSS has been hooked.
	 *
	 * @var bool
	 */
	private static bool $css_hooked = false;

	/**
	 * Look up the preview config for a generated class.
	 *
	 * @param string $class_name Generated class name.
	 * @return array{image?: string, title?: string, text?: string}
	 */
	public static function configFor( string $class_name ): array {
		return self::$config[ $class_name ] ?? array();
	}

	/**
	 * Register a preview-enabled NON-container element class.
	 *
	 * @param string $class_name php_class_name from vc_map (e.g. WPBakeryShortCode_BMA_Image).
	 * @param array  $map        Param map: image / title / text => param_name.
	 */
	public static function registerElementClass( string $class_name, array $map ): void {
		self::register( $class_name, $map, 'WPBakeryShortCode' );
	}

	/**
	 * Register a preview-enabled CONTAINER element class.
	 *
	 * @param string $class_name php_class_name from vc_map.
	 * @param array  $map        Param map: image / title / text => param_name.
	 */
	public static function registerContainerClass( string $class_name, array $map ): void {
		self::register( $class_name, $map, 'WPBakeryShortCodesContainer' );
	}

	/**
	 * Shared implementation: define the class extending $parent with an
	 * outputTitle() override that appends the preview html.
	 *
	 * @param string $class_name Generated class name.
	 * @param array  $map        Param map.
	 * @param string $parent     Parent WPBakery class.
	 */
	private static function register( string $class_name, array $map, string $parent ): void {
		if ( ! class_exists( $parent ) ) {
			return;
		}
		self::$config[ $class_name ] = $map;
		self::hookCss();

		if ( class_exists( $class_name, false ) ) {
			return;
		}

		// WPBakery discovers shortcode classes by literal symbol name, so the
		// class must be eval-defined (same pattern WP core + WPBakery use).
		// contentAdmin() sets $this->atts before calling outputTitle(), but the
		// textarea_html "content" param arrives as the $content argument, not
		// in atts — capture it so the preview can excerpt it.
		// phpcs:ignore Squiz.PHP.Eval.Discouraged
		eval(
			'class ' . $class_name . ' extends \\' . $parent . ' {
				public $bma_preview_content = \'\';
				public function contentAdmin( $atts, $content = null ) {
					$this->bma_preview_content = is_string( $content ) ? $content : \'\';
					return parent::contentAdmin( $atts, $content );
				}
				protected function outputTitle( $title ) {
					$html = parent::outputTitle( $title );
					return $html . \\Balefire\\Component\\BakeryPreview\\Preview::renderPreview(
						\'' . $class_name . '\',
						is_array( $this->atts ) ? $this->atts : array(),
						$this->bma_preview_content
					);
				}
			}'
		);
	}

	/**
	 * Render the preview block for an element instance.
	 *
	 * Thumbnail resolution mirrors vc_single_image: wpb_getImageBySize() with
	 * thumb_size "thumbnail" on the attach_image param value, falling back to
	 * wp_get_attachment_image() when the helper is unavailable.
	 *
	 * @param string $class_name Generated class name (config key).
	 * @param array  $atts       Element atts as saved in the editor.
	 * @param string $content    Raw $content passed to contentAdmin (textarea_html "content" param).
	 * @return string Preview HTML ('' when there is nothing to show).
	 */
	public static function renderPreview( string $class_name, array $atts, string $content = '' ): string {
		$map = self::configFor( $class_name );
		if ( array() === $map ) {
			return '';
		}

		$img_html = '';
		if ( isset( $map['image'] ) ) {
			$raw = isset( $atts[ $map['image'] ] ) ? (string) $atts[ $map['image'] ] : '';
			$id  = (int) preg_replace( '/[^\d]/', '', $raw );
			if ( $id > 0 ) {
				if ( function_exists( 'wpb_getImageBySize' ) ) {
					$img = wpb_getImageBySize(
						array(
							'attach_id'  => $id,
							'thumb_size' => 'thumbnail',
						)
					);
					if ( is_array( $img ) && ! empty( $img['thumbnail'] ) ) {
						$img_html = $img['thumbnail'];
					}
				}
				if ( '' === $img_html ) {
					$img_html = (string) wp_get_attachment_image( $id, 'thumbnail' );
				}
			}
		}

		$title_text = '';
		if ( isset( $map['title'], $atts[ $map['title'] ] ) ) {
			$title_text = wp_strip_all_tags( (string) $atts[ $map['title'] ] );
		}

		$body_text = '';
		if ( isset( $map['text'] ) ) {
			$raw_body = 'content' === $map['text']
				? $content
				: ( isset( $atts[ $map['text'] ] ) ? (string) $atts[ $map['text'] ] : '' );
			if ( '' === $raw_body && isset( $atts[ $map['text'] ] ) ) {
				$raw_body = (string) $atts[ $map['text'] ];
			}
			$body_text = trim( wp_strip_all_tags( $raw_body ) );
			if ( function_exists( 'mb_substr' ) && mb_strlen( $body_text ) > 120 ) {
				$body_text = mb_substr( $body_text, 0, 120 ) . '…';
			}
		}

		if ( '' === $img_html && '' === $title_text && '' === $body_text ) {
			return '';
		}

		$out  = '<div class="bma-vc-preview">';
		if ( '' !== $img_html ) {
			$out .= '<span class="bma-vc-preview__thumb">' . $img_html . '</span>';
		}
		if ( '' !== $title_text || '' !== $body_text ) {
			$out .= '<span class="bma-vc-preview__text">';
			if ( '' !== $title_text ) {
				$out .= '<strong class="bma-vc-preview__title">' . esc_html( $title_text ) . '</strong>';
			}
			if ( '' !== $body_text ) {
				$out .= '<span class="bma-vc-preview__body">' . esc_html( $body_text ) . '</span>';
			}
			$out .= '</span>';
		}
		$out .= '</div>';

		return $out;
	}

	/**
	 * Print the small admin stylesheet once, on the backend editor screen.
	 */
	public static function hookCss(): void {
		if ( self::$css_hooked ) {
			return;
		}
		self::$css_hooked = true;
		add_action( 'admin_print_styles', array( self::class, 'printCss' ) );
	}

	/**
	 * Output the preview CSS inline (tiny — not worth an enqueue/file).
	 */
	public static function printCss(): void {
		echo '<style id="bma-vc-preview-css">
.bma-vc-preview{display:flex;align-items:center;gap:10px;margin:6px 0 2px;padding:6px 8px;background:#fff;border:1px solid #e2e4e7;border-radius:4px;min-height:34px;}
.bma-vc-preview__thumb img{display:block;width:44px;height:44px;object-fit:cover;border-radius:3px;}
.bma-vc-preview__text{display:flex;flex-direction:column;gap:1px;min-width:0;}
.bma-vc-preview__title{font-size:13px;line-height:1.3;color:#23282d;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.bma-vc-preview__body{font-size:11px;line-height:1.4;color:#6b7280;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.wpb_bma_element .wpb_element_wrapper > .bma-vc-preview{margin-top:0;}
</style>';
	}
}
