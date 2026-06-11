<?php
/**
 * BMA Steps shortcode (parent grid + step child + step icon).
 *
 * Parent: [bma_steps columns="3"] wraps a series of [bma_step] children in an
 *         auto-grid. Column count drives the lg:auto-grid-cols-N class.
 * Child:  [bma_step icon="123" title="..."]Body[/bma_step] renders one
 *         simple-steps-card (icon, title, body). The icon is a numeric
 *         attachment id OR an inline [bma_step_icon]<svg>…</svg>[/bma_step_icon].
 * Icon:   [bma_step_icon]<svg>…</svg>[/bma_step_icon] passthrough sanitiser.
 *
 * Markup keeps the bma-auto-grid / auto-grid-cols-N / auto-grid-gap-6 classes
 * (component-auto-grid owns their CSS) and the simple-steps-card element
 * classes (this package's style.css owns their CSS).
 *
 * Global function wrappers (bma_steps_shortcode, bma_step_shortcode,
 * bma_step_icon_shortcode) are defined in bootstrap.php. add_shortcode,
 * vc_map, and the WPBakeryShortCodesContainer subclass are also wired there.
 *
 * Source: rockerbox theme inc/shortcodes/bma-steps.php.
 *
 * @package Balefire\Component\Steps
 */

declare( strict_types=1 );

namespace Balefire\Component\Steps;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_steps] / [bma_step] / [bma_step_icon] shortcodes.
 *
 * @package Balefire\Component\Steps
 */
final class Steps {

	/**
	 * Render the parent [bma_steps] grid. Wraps the children (processed via
	 * do_shortcode) in a bma-auto-grid container sized by the columns attr.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => '3',
			),
			$atts,
			'bma_steps'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$cols      = (int) ( $atts['columns'] ?: 3 );
		$col_class = match ( $cols ) {
			1       => 'lg:auto-grid-cols-1',
			2       => 'lg:auto-grid-cols-2',
			4       => 'lg:auto-grid-cols-4',
			default => 'lg:auto-grid-cols-3',
		};

		return '<div class="bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 '
			. esc_attr( $col_class ) . '">' . do_shortcode( (string) $content ) . '</div>';
	}

	/**
	 * Render one [bma_step] child card.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Body HTML (may contain a [bma_step_icon] tag).
	 * @return string HTML output.
	 */
	public static function renderStep( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'icon'  => '',
				'title' => '',
			),
			$atts,
			'bma_step'
		);

		$body       = (string) $content;
		$inline_svg = '';
		if ( preg_match( '/\[bma_step_icon\](.*?)\[\/bma_step_icon\]/is', $body, $m ) ) {
			$inline_svg = $m[1];
			$body       = str_replace( $m[0], '', $body );
		}

		$icon_html = self::iconHtml( $inline_svg, $atts['icon'], 'simple-steps-card__img' );
		$title     = trim( (string) $atts['title'] );
		$body_html = wp_kses_post( trim( do_shortcode( $body ) ) );

		ob_start();
		?>
		<div class="simple-steps-card">
			<?php if ( '' !== $icon_html ) : ?>
				<div class="simple-steps-card__icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon HTML is already sanitised by the soft-dep helpers ?></div>
			<?php endif; ?>
			<div class="simple-steps-card__content">
				<?php if ( '' !== $title ) : ?>
					<h3 class="wp-block-heading"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( $body_html ) ) : ?>
					<p class="has-text-align-center wp-block-paragraph"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already through wp_kses_post ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render the inline [bma_step_icon] passthrough. Sanitises raw SVG markup
	 * via the bma_safe_svg() soft dep when present; falls back to '' otherwise.
	 *
	 * @param array       $atts    Shortcode attributes (unused).
	 * @param string|null $content Raw SVG markup.
	 * @return string Sanitised SVG, or ''.
	 */
	public static function renderStepIcon( array $atts, ?string $content = null ): string {
		$svg = (string) $content;
		if ( function_exists( 'bma_safe_svg' ) ) {
			return (string) bma_safe_svg( $svg );
		}
		return '';
	}

	/**
	 * Resolve the icon HTML: inline SVG priority, attachment id fallback.
	 *
	 * Mirrors the theme's bma_icon_card_icon_html() helper but guards every
	 * soft-dep call (bma_safe_svg, bma_inline_svg_attachment,
	 * bma_render_image_or_svg) so the component never fatals standalone.
	 *
	 * @param string     $svg       Raw inline SVG markup (priority).
	 * @param int|string $icon      Attachment id (numeric) or raw SVG string.
	 * @param string     $img_class CSS class for the <img> when using an attachment.
	 * @return string Icon HTML (safe), or ''.
	 */
	private static function iconHtml( string $svg, $icon = '', string $img_class = '' ): string {
		$svg = trim( $svg );
		if ( '' !== $svg ) {
			return function_exists( 'bma_safe_svg' ) ? (string) bma_safe_svg( $svg ) : '';
		}

		if ( ! empty( $icon ) ) {
			// SVG attachments are inlined so fill="currentColor" survives.
			if ( is_numeric( $icon ) && function_exists( 'bma_inline_svg_attachment' ) ) {
				$inline = (string) bma_inline_svg_attachment( (int) $icon );
				if ( '' !== $inline ) {
					return $inline;
				}
			}
			if ( function_exists( 'bma_render_image_or_svg' ) ) {
				return (string) bma_render_image_or_svg( $icon, 'full', $img_class );
			}
		}

		return '';
	}

	/**
	 * Register the [bma_steps], [bma_step] and [bma_step_icon] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_steps', array( self::class, 'render' ) );
		add_shortcode( 'bma_step', array( self::class, 'renderStep' ) );
		add_shortcode( 'bma_step_icon', array( self::class, 'renderStepIcon' ) );
	}

	/**
	 * WPBakery vc_map registration for the parent container and the child.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'BMA Steps', 'balefire' ),
				'base'                    => 'bma_steps',
				'php_class_name'          => 'WPBakeryShortCode_BMA_Steps',
				'category'                => __( 'BMA Cards', 'balefire' ),
				'description'             => __( 'Numbered steps grid (icon, title, body).', 'balefire' ),
				'icon'                    => 'vc_icon-vc-row',
				'as_parent'               => array( 'only' => 'bma_step' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => array( '3', '1', '2', '4' ),
						'std'        => '3',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'BMA Step', 'balefire' ),
				'base'            => 'bma_step',
				'category'        => __( 'BMA Cards', 'balefire' ),
				'description'     => __( 'A single step card (icon, title, body).', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_steps' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Icon', 'balefire' ),
						'param_name' => 'icon',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
					),
					array(
						'type'        => 'textarea_html',
						'heading'     => __( 'Body', 'balefire' ),
						'param_name'  => 'content',
						'description' => __( 'Step description. For an inline SVG icon use [bma_step_icon]<svg>…</svg>[/bma_step_icon].', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakeryShortCodesContainer subclass so the editor treats
	 * the parent as a container and its children remain editable. Hooked on
	 * vc_after_init so the base class is loaded.
	 */
	public static function registerContainerClass(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_Steps' ) ) {
			eval( 'class WPBakeryShortCode_BMA_Steps extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
