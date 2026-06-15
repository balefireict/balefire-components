<?php
/**
 * BMA Simple Text Card — text-only info card.
 *
 * All colors, sizes, and padding are exposed as shortcode params and output
 * as inline CSS custom properties (--stc-*). Empty params fall back to the
 * theme-var defaults defined in style.css.
 *
 * @package Balefire\Component\SimpleTextCard
 */

declare( strict_types=1 );

namespace Balefire\Component\SimpleTextCard;

defined( 'ABSPATH' ) || exit;

/**
 * Single-element shortcode renderer for [bma_simple_text_card].
 */
final class SimpleTextCard {

	/**
	 * Shortcode base name.
	 */
	private const SHORTCODE = 'bma_simple_text_card';

	/**
	 * Register the shortcode.
	 */
	public static function register(): void {
		add_shortcode( self::SHORTCODE, array( self::class, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML (rich text between tags).
	 * @return string HTML output.
	 */
	public static function render( array $atts = array(), ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'eyebrow'       => '',
				'title'         => '',
				'bg_color'      => '',
				'text_color'    => '',
				'eyebrow_color' => '',
				'align'         => 'left',
				'padding_y'     => '',
				'padding_x'     => '',
				'eyebrow_size'  => '',
				'title_size'    => '',
				'body_size'     => '',
			),
			$atts,
			self::SHORTCODE
		);

		$eyebrow = trim( (string) $atts['eyebrow'] );
		$title   = trim( (string) $atts['title'] );
		$body    = trim( (string) $content );

		if ( '' === $title && '' === $body && '' === $eyebrow ) {
			return '';
		}

		$body_html = '' !== $body ? wp_kses_post( do_shortcode( $body ) ) : '';

		$align = self::sanitizeChoice( strtolower( trim( (string) $atts['align'] ) ), array( 'left', 'center', 'right' ), 'left' );

		/*
		 * Build the class list: base + align modifier.
		 */
		$classes = 'simple-text-card';
		if ( 'center' === $align || 'right' === $align ) {
			$classes .= ' simple-text-card--' . $align;
		}

		/*
		 * Build inline style from --stc-* custom properties.
		 * Only emit a property when the param is explicitly set — empty
		 * values fall back to the theme-var defaults in style.css.
		 */
		$vars = array();

		$bg = self::sanitizeHexColor( trim( (string) $atts['bg_color'] ), '' );
		if ( '' !== $bg ) {
			$vars[] = '--stc-bg:' . $bg;
		}

		$fg = self::sanitizeHexColor( trim( (string) $atts['text_color'] ), '' );
		if ( '' !== $fg ) {
			$vars[] = '--stc-fg:' . $fg;
		}

		$eyebrow_color = self::sanitizeHexColor( trim( (string) $atts['eyebrow_color'] ), '' );
		if ( '' !== $eyebrow_color ) {
			$vars[] = '--stc-eyebrow:' . $eyebrow_color;
		}

		$pad_y = self::sanitizeLength( trim( (string) $atts['padding_y'] ), '' );
		if ( '' !== $pad_y ) {
			$vars[] = '--stc-padding-y:' . $pad_y;
		}

		$pad_x = self::sanitizeLength( trim( (string) $atts['padding_x'] ), '' );
		if ( '' !== $pad_x ) {
			$vars[] = '--stc-padding-x:' . $pad_x;
		}

		$eyebrow_size = self::sanitizeLength( trim( (string) $atts['eyebrow_size'] ), '' );
		if ( '' !== $eyebrow_size ) {
			$vars[] = '--stc-eyebrow-size:' . $eyebrow_size;
		}

		$title_size = self::sanitizeLength( trim( (string) $atts['title_size'] ), '' );
		if ( '' !== $title_size ) {
			$vars[] = '--stc-title-size:' . $title_size;
		}

		$body_size = self::sanitizeLength( trim( (string) $atts['body_size'] ), '' );
		if ( '' !== $body_size ) {
			$vars[] = '--stc-body-size:' . $body_size;
		}

		$style = '';
		if ( ! empty( $vars ) ) {
			$style = ' style="' . esc_attr( implode( ';', $vars ) ) . '"';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $classes ); ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="simple-text-card__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h3 class="simple-text-card__title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<?php if ( '' !== trim( $body_html ) ) : ?>
				<div class="simple-text-card__body"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?></div>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Register the WPBakery (VC) element mapping.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'           => __( 'Simple Text Card', 'balefire' ),
				'base'           => self::SHORTCODE,
				'category'       => __( 'Custom Elements', 'balefire' ),
				'description'    => __( 'BMA — Text-only info card with eyebrow, title, body, colors, sizes, and padding.', 'balefire' ),
				'icon'           => 'vc_icon-vc-single-image',
				'php_class_name' => 'WPBakeryShortCode_BMA_SimpleTextCard',
				'params'         => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Eyebrow', 'balefire' ),
						'param_name'  => 'eyebrow',
						'description' => __( 'Small accent label above the title (time, category, etc.). Optional.', 'balefire' ),
						'admin_label' => true,
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
						'type'       => 'dropdown',
						'heading'    => __( 'Text Align', 'balefire' ),
						'param_name' => 'align',
						'value'      => array(
							__( 'Left', 'balefire' )   => 'left',
							__( 'Center', 'balefire' ) => 'center',
							__( 'Right', 'balefire' )  => 'right',
						),
						'std'        => 'left',
						'group'      => __( 'Layout', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Padding Y (top/bottom)', 'balefire' ),
						'param_name'  => 'padding_y',
						'description' => __( 'Vertical padding. Bare numbers = px (e.g. 32). Also accepts rem/em. Leave empty for default.', 'balefire' ),
						'group'       => __( 'Layout', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Padding X (left/right)', 'balefire' ),
						'param_name'  => 'padding_x',
						'description' => __( 'Horizontal padding. Bare numbers = px (e.g. 48). Also accepts rem/em. Leave empty for default.', 'balefire' ),
						'group'       => __( 'Layout', 'balefire' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Background Color', 'balefire' ),
						'param_name' => 'bg_color',
						'group'      => __( 'Colors', 'balefire' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Text Color', 'balefire' ),
						'param_name' => 'text_color',
						'group'      => __( 'Colors', 'balefire' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Eyebrow Color', 'balefire' ),
						'param_name' => 'eyebrow_color',
						'group'      => __( 'Colors', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Eyebrow Font Size', 'balefire' ),
						'param_name'  => 'eyebrow_size',
						'description' => __( 'Bare numbers = px. Leave empty for default.', 'balefire' ),
						'group'       => __( 'Typography', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title Font Size', 'balefire' ),
						'param_name'  => 'title_size',
						'description' => __( 'Bare numbers = px. Leave empty for default.', 'balefire' ),
						'group'       => __( 'Typography', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Body Font Size', 'balefire' ),
						'param_name'  => 'body_size',
						'description' => __( 'Bare numbers = px. Leave empty for default.', 'balefire' ),
						'group'       => __( 'Typography', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakery backend-editor preview class.
	 */
	public static function registerPreviewClasses(): void {
		if ( ! class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			return;
		}

		\Balefire\Component\BakeryPreview\Preview::registerElementClass(
			'WPBakeryShortCode_BMA_SimpleTextCard',
			array(
				'title' => 'title',
				'text'  => 'content',
			)
		);
	}

	/**
	 * Sanitize a dropdown-like choice.
	 *
	 * @param string $value    Requested value.
	 * @param array  $allowed  Allowed values.
	 * @param string $fallback Fallback value.
	 * @return string Safe value.
	 */
	private static function sanitizeChoice( string $value, array $allowed, string $fallback ): string {
		$value = strtolower( trim( $value ) );
		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	/**
	 * Sanitize a CSS length value.
	 *
	 * @param string $value    Raw value.
	 * @param string $fallback Fallback length (empty string = no override).
	 * @return string Safe CSS length, or empty string.
	 */
	private static function sanitizeLength( string $value, string $fallback ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return $fallback;
		}
		if ( preg_match( '/^\d+(\.\d+)?$/', $value ) ) {
			return $value . 'px';
		}
		if ( preg_match( '/^\d+(\.\d+)?(px|rem|em|%)$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	/**
	 * Sanitize a hex color value.
	 *
	 * @param string $value    Raw value.
	 * @param string $fallback Fallback hex color (empty string = no override).
	 * @return string Safe hex color, or empty string.
	 */
	private static function sanitizeHexColor( string $value, string $fallback ): string {
		$value = trim( $value );
		if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}
}
