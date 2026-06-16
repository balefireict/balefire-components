<?php
/**
 * BMA Card Icon Top shortcode (parent grid + child tile).
 *
 * Parent: [bma_card_icon_top] wraps child tiles. Owns its own centered
 *         max-width:1050px container and a responsive grid (1 col, 3 cols >=768px).
 * Child:  [bma_card_icon_top_item image="" title=""]<body WYSIWYG>[/bma_card_icon_top_item]
 *
 * @package Balefire\Component\CardIconTop
 */

declare( strict_types=1 );

namespace Balefire\Component\CardIconTop;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the icon-top card grid.
 */
final class CardIconTop {

	/**
	 * Register the parent + child shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_card_icon_top', 'bma_card_icon_top_shortcode' );
		add_shortcode( 'bma_card_icon_top_item', 'bma_card_icon_top_item_shortcode' );
	}

	/**
	 * Render the parent grid wrapper.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Child shortcodes.
	 * @return string HTML output (empty string if no children).
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$inner = self::manualInner( $content );
		if ( '' === trim( $inner ) ) {
			return '';
		}

		$class = 'card-icon-top';
		$extra = trim( (string) self::attr( $atts, 'class' ) );
		if ( '' !== $extra ) {
			$class .= ' ' . sanitize_html_class( $extra );
		}

		return sprintf( '<div class="%s">%s</div>', esc_attr( $class ), $inner );
	}

	/**
	 * Render one child tile.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content WYSIWYG body (rich text).
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'image' => '',
				'title' => '',
				'class' => '',
			),
			$atts,
			'bma_card_icon_top_item'
		);

		$title      = trim( (string) self::attr( $atts, 'title' ) );
		$image_html = self::imageHtml( trim( (string) self::attr( $atts, 'image' ) ), $title );
		$body       = trim( (string) $content );
		$body_html  = '' !== $body ? wp_kses_post( do_shortcode( wpautop( $body ) ) ) : '';

		if ( '' === $title && '' === $image_html && '' === trim( $body_html ) ) {
			return '';
		}

		$class = 'card-icon-top__item';
		$extra = trim( (string) self::attr( $atts, 'class' ) );
		if ( '' !== $extra ) {
			$class .= ' ' . sanitize_html_class( $extra );
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<?php if ( '' !== $image_html ) : ?>
				<div class="card-icon-top__icon"><?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h3 class="card-icon-top__title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<?php if ( '' !== trim( $body_html ) ) : ?>
				<div class="card-icon-top__body"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?></div>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Process manual child shortcode content into rendered tile HTML.
	 *
	 * @param string|null $content Raw child shortcodes.
	 * @return string
	 */
	private static function manualInner( ?string $content ): string {
		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		// Strip stray <br> WPBakery may insert between sibling child tiles.
		$inner = preg_replace( '/(<\/div>)\s*<br\s*\/?>/i', '$1', (string) $inner );

		return (string) $inner;
	}

	/**
	 * Resolve the icon image HTML for an attachment ID or URL.
	 *
	 * @param string $image Attachment ID or URL.
	 * @param string $title Fallback alt text context.
	 * @return string
	 */
	private static function imageHtml( string $image, string $title ): string {
		$image = trim( $image );
		if ( '' === $image ) {
			return '';
		}

		if ( is_numeric( $image ) ) {
			$image_id = (int) $image;
			$alt      = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( '' === trim( $alt ) ) {
				$alt = $title;
			}

			$html = wp_get_attachment_image(
				$image_id,
				'full',
				false,
				array(
					'class'    => 'card-icon-top__img',
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);

			return $html ? $html : '';
		}

		return '<img class="card-icon-top__img" src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" decoding="async" />';
	}

	/**
	 * Register the WPBakery parent + child elements.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		// Parent — the grid container.
		vc_map(
			array(
				'name'                    => __( 'Card Icon Top Grid', 'balefire' ),
				'base'                    => 'bma_card_icon_top',
				'php_class_name'          => 'WPBakeryShortCode_BMA_CardIconTop',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — 3-column grid of cards with a top icon, h3 title, and rich-text body.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'bma_card_icon_top_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'description' => __( 'Label shown in the page builder only — not rendered on the front end.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Optional extra CSS class on the grid wrapper.', 'balefire' ),
					),
				),
			)
		);

		// Child — one card.
		vc_map(
			array(
				'name'            => __( 'Card Icon Top Item', 'balefire' ),
				'base'            => 'bma_card_icon_top_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_CardIconTopItem',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single card with a top icon image, h3 title, and rich-text body.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_card_icon_top' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Icon', 'balefire' ),
						'param_name'  => 'image',
						'admin_label' => true,
						'description' => __( 'Icon image shown at the top of the card.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'admin_label' => true,
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Body', 'balefire' ),
						'param_name' => 'content',
						'value'      => '',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
						'description' => __( 'Optional extra CSS class on the card.', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakery preview/editor classes (soft-dep on bakery-preview).
	 */
	public static function registerPreviewClasses(): void {
		if ( class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_CardIconTop',
				array(
					'title' => 'title',
				)
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_CardIconTopItem',
				array(
					'image' => 'image',
					'title' => 'title',
					'text'  => 'content',
				)
			);
			return;
		}

		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_CardIconTop' ) ) {
			eval( 'class WPBakeryShortCode_BMA_CardIconTop extends \\\\WPBakeryShortCodesContainer {}' );
		}
	}

	/**
	 * Read a value that may have been written by WPBakery with underscores
	 * converted to hyphens.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $name Canonical snake_case param name.
	 * @return string
	 */
	private static function attr( array $atts, string $name ): string {
		$hyphen = str_replace( '_', '-', $name );
		foreach ( array( $name, $hyphen ) as $key ) {
			if ( isset( $atts[ $key ] ) && '' !== (string) $atts[ $key ] ) {
				return (string) $atts[ $key ];
			}
		}

		return '';
	}
}
