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
	 * Whether the backend-editor sync JS has been hooked.
	 *
	 * @var bool
	 */
	private static bool $js_hooked = false;

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
		self::hookJs();

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

	/**
	 * Hook the backend-editor sync JS once, on the WPBakery editor screen.
	 *
	 * WHY THIS EXISTS: WPBakery server-renders each element's backend template
	 * exactly once, via Vc_Shortcodes_Manager::template() which calls
	 * contentAdmin( array(), $content ) with EMPTY atts. vc_map_get_attributes()
	 * then fills the vc_map `value` defaults, so our server-rendered
	 * `.bma-vc-preview` card is frozen at the DEFAULT title/text/thumbnail. JS
	 * clones that template per instance and only re-hydrates WPBakery's own
	 * recognized holders (.wpb_vc_param_value inputs + .admin_label_* spans) from
	 * the live Backbone model — it never touches our custom preview div. Result:
	 * the admin_label shows the saved value while the preview shows the default.
	 * This JS closes that gap by syncing the preview card from the live model.
	 */
	public static function hookJs(): void {
		if ( self::$js_hooked ) {
			return;
		}
		self::$js_hooked = true;
		add_action( 'admin_print_footer_scripts', array( self::class, 'printJs' ), 100 );
	}

	/**
	 * Build a { shortcode_base: { image, title, text } } config from the
	 * per-class config, resolving each generated class back to its shortcode
	 * base via the WPBakery map.
	 *
	 * @return array<string, array{image?: string, title?: string, text?: string}>
	 */
	private static function jsConfigByBase(): array {
		if ( array() === self::$config || ! class_exists( 'WPBMap' ) ) {
			return array();
		}

		// Map php_class_name => base from the registered shortcodes.
		$shortcodes = \WPBMap::getShortCodes();
		if ( ! is_array( $shortcodes ) ) {
			return array();
		}

		$class_to_base = array();
		foreach ( $shortcodes as $base => $settings ) {
			if ( isset( $settings['php_class_name'] ) ) {
				$class_to_base[ $settings['php_class_name'] ] = $base;
			}
		}

		$by_base = array();
		foreach ( self::$config as $class_name => $map ) {
			if ( isset( $class_to_base[ $class_name ] ) ) {
				$by_base[ $class_to_base[ $class_name ] ] = $map;
			}
		}

		return $by_base;
	}

	/**
	 * Output the backend-editor preview-sync JS once, in the editor footer.
	 */
	public static function printJs(): void {
		// Only the post/page edit screens can host the WPBakery backend editor.
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( null !== $screen && 'post' !== $screen->base ) {
				return;
			}
		}

		$by_base = self::jsConfigByBase();
		if ( array() === $by_base ) {
			return;
		}

		$config_json = wp_json_encode( $by_base );
		if ( false === $config_json ) {
			return;
		}

		?>
<script id="bma-vc-preview-sync">
( function ( $ ) {
	'use strict';
	if ( typeof window.vc === 'undefined' || ! window.vc.events || ! window.vc.shortcodes ) {
		return;
	}

	var CONFIG = <?php echo $config_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — JSON-encoded config. ?>;
	var thumbCache = {};

	function stripTags( value ) {
		if ( ! value ) {
			return '';
		}
		return $( '<div/>' ).html( String( value ) ).text().replace( /\s+/g, ' ' ).trim();
	}

	function excerpt( value ) {
		var text = stripTags( value );
		return text.length > 120 ? text.slice( 0, 120 ) + '\u2026' : text;
	}

	function setThumb( $thumb, attachId ) {
		if ( ! attachId ) {
			$thumb.remove();
			return;
		}
		if ( thumbCache[ attachId ] ) {
			$thumb.html( '<img src="' + thumbCache[ attachId ] + '" alt="" />' );
			return;
		}
		$.ajax( {
			type: 'POST',
			url: window.ajaxurl,
			data: {
				action: 'wpb_single_image_src',
				content: attachId,
				size: 'thumbnail',
				_vcnonce: window.vcAdminNonce
			},
			dataType: 'html'
		} ).done( function ( src ) {
			if ( ! src ) {
				return;
			}
			thumbCache[ attachId ] = src;
			$thumb.html( '<img src="' + src + '" alt="" />' );
		} );
	}

	function syncModel( model ) {
		if ( ! model || ! model.get ) {
			return;
		}
		var base = model.get( 'shortcode' );
		var cfg = CONFIG[ base ];
		if ( ! cfg ) {
			return;
		}

		var $node = $( '[data-model-id="' + model.id + '"]' );
		if ( ! $node.length ) {
			return;
		}
		var $wrap = $node.children( '.wpb_element_wrapper, .vc_element-wrapper' ).first();
		if ( ! $wrap.length ) {
			return;
		}

		var params = model.get( 'params' ) || {};

		var title = cfg.title ? stripTags( params[ cfg.title ] ) : '';

		var body = '';
		if ( cfg.text ) {
			body = ( 'content' === cfg.text ) ? ( params.content || '' ) : ( params[ cfg.text ] || '' );
			body = excerpt( body );
		}

		var attachId = '';
		if ( cfg.image ) {
			attachId = String( params[ cfg.image ] || '' ).replace( /[^0-9]/g, '' );
		}

		var $preview = $wrap.children( '.bma-vc-preview' ).first();

		if ( ! title && ! body && ! attachId ) {
			$preview.remove();
			return;
		}

		if ( ! $preview.length ) {
			$preview = $( '<div class="bma-vc-preview"></div>' );
			var $title = $wrap.children( '.wpb_element_title' ).first();
			if ( $title.length ) {
				$preview.insertAfter( $title );
			} else {
				$wrap.prepend( $preview );
			}
		}

		var $thumb = $preview.children( '.bma-vc-preview__thumb' ).first();
		if ( attachId ) {
			if ( ! $thumb.length ) {
				$thumb = $( '<span class="bma-vc-preview__thumb"></span>' ).prependTo( $preview );
			}
			setThumb( $thumb, attachId );
		} else {
			$thumb.remove();
		}

		var $text = $preview.children( '.bma-vc-preview__text' ).first();
		if ( title || body ) {
			if ( ! $text.length ) {
				$text = $( '<span class="bma-vc-preview__text"></span>' ).appendTo( $preview );
			}
			var html = '';
			if ( title ) {
				html += '<strong class="bma-vc-preview__title"></strong>';
			}
			if ( body ) {
				html += '<span class="bma-vc-preview__body"></span>';
			}
			$text.html( html );
			if ( title ) {
				$text.children( '.bma-vc-preview__title' ).text( title );
			}
			if ( body ) {
				$text.children( '.bma-vc-preview__body' ).text( body );
			}
		} else {
			$text.remove();
		}
	}

	function syncAll() {
		if ( ! window.vc.shortcodes || ! window.vc.shortcodes.each ) {
			return;
		}
		window.vc.shortcodes.each( syncModel );
	}

	// Live updates: WPBakery defers this event after every element's params
	// change AND on each element's initial render.
	Object.keys( CONFIG ).forEach( function ( base ) {
		window.vc.events.on( 'backend.shortcodeViewChangeParams:' + base, syncAll );
	} );

	// New / cloned elements.
	window.vc.events.on( 'shortcodes:add', syncModel );

	// Initial passes — the tree may build after our script runs.
	$( syncAll );
	$( window ).on( 'load', syncAll );
	window.setTimeout( syncAll, 1200 );
}( window.jQuery ) );
</script>
		<?php
	}
}
