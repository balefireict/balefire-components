<?php
/**
 * BMA Image Text List shortcode (parent + child).
 *
 * Parent: [bma_image_text_list color="default|white"] wraps a series of
 *         children. Renders the list wrapper.
 * Child:  [bma_image_text_item image="" title="" href="" new_tab=""]
 *             Body
 *         [/bma_image_text_item]
 *         Renders one image-left text row. Renders an <a> if href is set,
 *         a <div> otherwise.
 *
 * Source of truth classes. Global function wrappers (bma_image_text_list_render,
 * bma_image_text_item_render) are defined in bootstrap.php. add_shortcode,
 * vc_map, and the WPBakeryShortCodesContainer subclass are also wired there.
 *
 * @package Balefire\Component\ImageTextList
 */

declare( strict_types=1 );

namespace Balefire\Component\ImageTextList;

defined( 'ABSPATH' ) || exit;

/**
 * Static parent renderer for the [bma_image_text_list] shortcode.
 *
 * @package Balefire\Component\ImageTextList
 */
final class ImageTextList {

	public const COLOR_CHOICES = array( 'default', 'white' );

	/**
	 * Render the parent [bma_image_text_list] shortcode. Wraps the children
	 * (which were already processed by do_shortcode) in the list element.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'color' => 'default',
			),
			$atts,
			'bma_image_text_list'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$color   = strtolower( trim( (string) $atts['color'] ) );
		$classes = array( 'bma-image-text-list' );
		if ( 'white' === $color ) {
			$classes[] = 'bma-image-text-list--white';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = (string) preg_replace( '/^\s*(?:<br\s*\/?>\s*)+/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(?:\s*<br\s*\/?>\s*)+$/i', '', (string) $inner );
		$inner = (string) preg_replace( '/(<\/div>)\s*(?:<br\s*\/?>\s*)+(<div class="bma-image-text-item\b)/i', '$1$2', (string) $inner );

		return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . trim( (string) $inner ) . '</div>';
	}

	/**
	 * Render one [bma_image_text_item] child.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Body HTML (passes between opening/closing tags).
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'image'   => '',
				'title'   => '',
				'href'    => '',
				'new_tab' => '',
			),
			$atts,
			'bma_image_text_item'
		);

		$image_id = (int) $atts['image'];
		$title    = trim( (string) $atts['title'] );
		$href     = trim( (string) $atts['href'] );
		$new_tab  = filter_var( $atts['new_tab'], FILTER_VALIDATE_BOOLEAN );

		$body_html = trim( (string) do_shortcode( shortcode_unautop( (string) $content ) ) );
		$body_html = (string) preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', (string) $body_html );
		$body_html = trim( wp_kses_post( (string) $body_html ) );
		if ( '' !== $body_html && ! preg_match( '/<(p|ul|ol|blockquote|table|h[1-6])\b/i', (string) $body_html ) ) {
			$body_html = wpautop( (string) $body_html );
		}

		$image_html = '';
		if ( $image_id > 0 ) {
			$image_html = wp_get_attachment_image(
				$image_id,
				'full',
				false,
				array(
					'class'    => 'bma-image-text-item__img',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}

		$link_attrs = '';
		if ( '' !== $href ) {
			$link_attrs = ' href="' . esc_url( $href ) . '"';
			if ( $new_tab ) {
				$link_attrs .= ' target="_blank" rel="noopener noreferrer"';
			}
		}

		$item_classes = array( 'bma-image-text-item' );
		if ( '' !== $href ) {
			$item_classes[] = 'bma-image-text-item--linked';
		}
		$inner_tag   = '' !== $href ? 'a' : 'div';
		$inner_attrs = ' class="bma-image-text-item__inner"' . $link_attrs;

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>"><<?php echo $inner_tag; ?><?php echo $inner_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $inner_attrs is a hardcoded class name + esc_url'd href ?>
			<?php if ( '' !== $image_html ) : ?>
				<figure class="bma-image-text-item__media"><?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image returns safe HTML ?></figure>
			<?php endif; ?>
			<div class="bma-image-text-item__content">
				<?php if ( '' !== $title ) : ?>
					<h3 class="bma-image-text-item__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== $body_html ) : ?>
					<div class="bma-image-text-item__body"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post + wpautop ?></div>
				<?php endif; ?>
			</div>
		</<?php echo $inner_tag; ?>></div>
		<?php
		$html = (string) ob_get_clean();
		$html = (string) preg_replace( '/>\s+</', '><', $html );

		return trim( (string) $html );
	}

	/**
	 * Register both [bma_image_text_list] and [bma_image_text_item] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_image_text_list', array( self::class, 'render' ) );
		add_shortcode( 'bma_image_text_item', array( self::class, 'renderItem' ) );
	}

	/**
	 * WPBakery vc_map registration for both parent and child.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'BMA Image Text List', 'balefire' ),
				'base'                    => 'bma_image_text_list',
				'php_class_name'          => 'WPBakeryShortCode_BMA_ImageTextList',
				'category'                => __( 'BMA Elements', 'balefire' ),
				'description'             => __( 'Vertical image-left text rows with optional links.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-row',
				'as_parent'               => array( 'only' => 'bma_image_text_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Text color', 'balefire' ),
						'param_name' => 'color',
						'value'      => array(
							__( 'Default', 'balefire' ) => 'default',
							__( 'White', 'balefire' )   => 'white',
						),
						'std'        => 'default',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'BMA Image Text Item', 'balefire' ),
				'base'            => 'bma_image_text_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_ImageTextItem',
				'category'        => __( 'BMA Elements', 'balefire' ),
				'description'     => __( 'One image-left text row, with an optional link.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_image_text_list' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Image', 'balefire' ),
						'param_name' => 'image',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
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
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Body', 'balefire' ),
						'param_name' => 'content',
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakeryShortCodesContainer subclass that the parent
	 * shortcode needs to be recognized as a container in the editor.
	 * Hooked on vc_after_init so the parent class is loaded.
	 */
	public static function registerContainerClass(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_ImageTextList' ) ) {
			eval( 'class WPBakeryShortCode_BMA_ImageTextList extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
