<?php
/**
 * BMA Brand Icon Cards shortcode (parent + child).
 *
 * Parent: [brand_icon_cards columns="3|4|5|6"]…[/brand_icon_cards]
 *         Renders the grid wrapper.
 * Child:  [brand_icon_card
 *              title="..."
 *              media_image="123"     // large media image (id, url, or array)
 *              media_svg="<svg>..."  // OR raw SVG, takes priority
 *              logo_image="456"      // small top-right logo (id, url, or array)
 *              logo_svg="<svg>..."
 *              body="Body HTML"      // body content (passed through wp_kses_post)
 *              href="/link"
 *              new_tab="false"
 *          /]
 *         Renders a single card.
 *
 * Note: this monorepo version uses a FLAT card attribute schema (no nested
 * [brand_icon_logo] / [brand_icon_icon] shortcodes inside the card body).
 * Rockerbox's original implementation used HTML walking to extract those
 * nested shortcodes from the body — preserved there for legacy content,
 * simplified here so the package is self-contained and the WPBakery editor
 * shows all card options in a single panel.
 *
 * Source of truth class. Global function wrappers and the WPBakery
 * container-class registration live in bootstrap.php.
 *
 * @package Balefire\Component\BrandIconCards
 */

declare( strict_types=1 );

namespace Balefire\Component\BrandIconCards;

defined( 'ABSPATH' ) || exit;

/**
 * Static brand icon cards parent + child renderers.
 *
 * @package Balefire\Component\BrandIconCards
 */
final class BrandIconCards {

	public const COLUMN_CHOICES = array( 3, 4, 5, 6 );
	public const DEFAULT_COLUMNS = 3;

	/**
	 * Render the parent [brand_icon_cards] shortcode. Wraps children in
	 * the grid element. `data-cols` is set on the wrapper for CSS to read.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => (string) self::DEFAULT_COLUMNS,
			),
			$atts,
			'brand_icon_cards'
		);

		$columns = (int) $atts['columns'];
		if ( ! in_array( $columns, self::COLUMN_CHOICES, true ) ) {
			$columns = self::DEFAULT_COLUMNS;
		}

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = (string) preg_replace( '/^\s*(?:<br\s*\/?>\s*)+/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(?:<br\s*\/?>\s*)+$/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(<\/[^>]+>)\s*<br\s*\/?>\s*(<div class="bma-brand-icon-card\b)/i', '$1$2', (string) $inner );
		$inner = trim( (string) $inner );

		if ( '' === $inner ) {
			return '';
		}

		return sprintf(
			'<div class="bma-brand-icon-cards" data-cols="%d">%s</div>',
			$columns,
			$inner
		);
	}

	/**
	 * Render one [brand_icon_card] child.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when card has no content at all.
	 */
	public static function renderCard( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'title'       => '',
				'media_image' => '',
				'media_svg'   => '',
				'logo_image'  => '',
				'logo_svg'    => '',
				'body'        => '',
				'href'        => '',
				'new_tab'     => '',
			),
			$atts,
			'brand_icon_card'
		);

		$title      = trim( (string) $atts['title'] );
		$media_svg  = (string) $atts['media_svg'];
		$media_img  = $atts['media_image'];
		$logo_svg   = (string) $atts['logo_svg'];
		$logo_img   = $atts['logo_image'];
		$body_html  = (string) $atts['body'];
		$href       = trim( (string) $atts['href'] );
		$new_tab    = filter_var( $atts['new_tab'], FILTER_VALIDATE_BOOLEAN );

		// Resolve href via bma-href if loaded, else fall back to esc_url.
		if ( '' !== $href && function_exists( 'bma_resolve_href' ) ) {
			// If href is numeric, treat as page id; otherwise treat as URL.
			$href = bma_resolve_href( $href, '' );
		}

		$media_html = CardMedia::mediaHtml( $media_svg, $media_img );
		$logo_html  = CardMedia::logoHtml( $logo_svg, $logo_img );
		$body_html  = trim( (string) do_shortcode( shortcode_unautop( $body_html ) ) );
		$body_html  = (string) preg_replace( '/<p>(\s|&nbsp;)*<\/p>/i', '', $body_html );
		$body_html  = trim( (string) wp_kses_post( $body_html ) );
		if ( '' === $title && '' === $media_html && '' === $logo_html && '' === $body_html ) {
			return '';
		}

		$tag        = '' !== $href ? 'a' : 'div';
		$tag_attrs  = '';
		if ( '' !== $href ) {
			$tag_attrs = ' href="' . esc_url( $href ) . '"';
			if ( $new_tab ) {
				$tag_attrs .= ' target="_blank" rel="noopener noreferrer"';
			}
		}

		$arrow_svg = function_exists( 'bma_arrow_svg' ) ? bma_arrow_svg() : '';

		ob_start();
		?>
		<<?php echo $tag; ?> class="bma-brand-icon-card"<?php echo $tag_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag_attrs is a hardcoded class name + esc_url'd href ?>>
			<?php if ( '' !== $logo_html ) : ?>
				<div class="bma-brand-icon-card__logo"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized SVG / esc_url'd img ?></div>
			<?php endif; ?>

			<?php if ( '' !== $media_html ) : ?>
				<div class="bma-brand-icon-card__media"><?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized SVG / esc_url'd img ?></div>
			<?php endif; ?>

			<div class="bma-brand-icon-card__body">
				<?php if ( '' !== $title ) : ?>
					<h3 class="bma-brand-icon-card__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>

				<?php if ( '' !== $body_html ) : ?>
					<div class="bma-brand-icon-card__text"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post + do_shortcode ?></div>
				<?php endif; ?>

				<?php if ( '' !== $href ) : ?>
					<span class="bma-brand-icon-card__arrow">
						Learn More<?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				<?php endif; ?>
			</div>
		</<?php echo $tag; ?>>
		<?php
		$html = (string) ob_get_clean();
		$html = (string) preg_replace( '/>\s+</', '><', $html );

		return trim( (string) $html );
	}

	/**
	 * Register both [brand_icon_cards] and [brand_icon_card] shortcodes.
	 * Also registers hyphenated aliases for legacy markup (rockerbox used both).
	 */
	public static function register(): void {
		add_shortcode( 'brand_icon_cards', array( self::class, 'render' ) );
		add_shortcode( 'brand_icon_card', array( self::class, 'renderCard' ) );
		add_shortcode( 'brand-icon-cards', array( self::class, 'render' ) );
		add_shortcode( 'brand-icon-card', array( self::class, 'renderCard' ) );
	}

	/**
	 * WPBakery vc_map registration for both parent and child.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$column_choices = array();
		foreach ( self::COLUMN_CHOICES as $n ) {
			$column_choices[ (string) $n ] = (string) $n;
		}

		vc_map(
			array(
				'name'                    => __( 'Brand Icon Cards', 'balefire' ),
				'base'                    => 'brand_icon_cards',
				'php_class_name'          => 'WPBakeryShortCode_BMA_BrandIconCards',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Grid of brand / icon cards with logo, media, title, body, and link.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'brand_icon_card' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => $column_choices,
						'std'        => (string) self::DEFAULT_COLUMNS,
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Brand Icon Card', 'balefire' ),
				'base'            => 'brand_icon_card',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — Single brand/icon card.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'brand_icon_cards' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'admin_label' => true,
					),
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Media Image (large)', 'balefire' ),
						'param_name' => 'media_image',
					),
					array(
						'type'        => 'textarea_raw_html',
						'heading'     => __( 'Media SVG (raw, takes priority over media_image)', 'balefire' ),
						'param_name'  => 'media_svg',
						'description' => __( 'Paste raw SVG markup here. Use Media Image for raster images.', 'balefire' ),
					),
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Logo Image (small, top-right)', 'balefire' ),
						'param_name' => 'logo_image',
					),
					array(
						'type'        => 'textarea_raw_html',
						'heading'     => __( 'Logo SVG (raw, takes priority over logo_image)', 'balefire' ),
						'param_name'  => 'logo_svg',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Body', 'balefire' ),
						'param_name' => 'body',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Link URL', 'balefire' ),
						'param_name' => 'href',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Open in new tab', 'balefire' ),
						'param_name' => 'new_tab',
						'value'      => array( __( 'Yes', 'balefire' ) => 'true' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakeryShortCodesContainer subclass on vc_after_init.
	 */
	public static function registerContainerClass(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_BrandIconCards' ) ) {
			eval( 'class WPBakeryShortCode_BMA_BrandIconCards extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
