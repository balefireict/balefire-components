<?php
/**
 * BMA Case Study Compare — WPBakery element (before/after comparison).
 *
 * Renders two card-icon-break cards with the supplied arrow SVG between them.
 * Existing [bma_compare] content uses this card-break variant automatically;
 * [bma_compare_cards] is the explicit new shortcode alias. Both shortcodes map
 * to the same renderer.
 *
 * Self-closing attribute-driven element (no enclosed content):
 *   [bma_compare
 *      left_icon="2773"  left_title="Before Rockerbox"
 *      left_body="$160,000 in credits<br>Normal results<br>Manual process"
 *      right_icon="2770" right_title="With Rockerbox"
 *      right_body="$950,000 in credits<br>Optimized results<br>Automated process"]
 *
 * Icons: numeric attachment id OR full URL. Body allows <br>, <strong>.
 *
 * Ported from rockerbox/wp-content/themes/balefire/inc/shortcodes/bma-case-study-compare.php.
 * The icon-card render helpers (bma_render_icon_card / bma_icon_card_icon_html) from
 * bma-icon-card.php are inlined here as private static methods (the only logic this
 * component needs); when a theme provides the global bma_render_icon_card(), it is
 * preferred so a host theme stays the source of truth.
 *
 * @package Balefire\Component\CaseStudyCompare
 */

declare( strict_types=1 );

namespace Balefire\Component\CaseStudyCompare;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_compare] / [bma_compare_cards] shortcodes.
 *
 * @package Balefire\Component\CaseStudyCompare
 */
final class CaseStudyCompare {

	/**
	 * Register both shortcodes against the shared renderer.
	 */
	public static function register(): void {
		add_shortcode( 'bma_compare', array( self::class, 'render' ) );
		add_shortcode( 'bma_compare_cards', array( self::class, 'render' ) );
	}

	/**
	 * Shortcode entry point. Normalizes atts and renders the card-break variant.
	 *
	 * @param mixed       $atts      Raw shortcode attributes.
	 * @param string|null $content   Unused (self-closing element).
	 * @param string      $shortcode Shortcode tag for shortcode_atts filters.
	 * @return string HTML output.
	 */
	public static function render( $atts, $content = null, string $shortcode = 'bma_compare' ): string {
		unset( $content );

		$atts = self::normalizeAtts( $atts, $shortcode );

		return self::renderCardBreak( $atts );
	}

	/**
	 * Normalize shared compare shortcode attributes.
	 *
	 * @param mixed  $atts      Raw shortcode attributes.
	 * @param string $shortcode Shortcode tag for shortcode_atts filters.
	 * @return array<string,string>
	 */
	private static function normalizeAtts( $atts, string $shortcode ): array {
		return shortcode_atts(
			array(
				'left_icon'   => '',
				'left_title'  => '',
				'left_body'   => '',
				'right_icon'  => '',
				'right_title' => '',
				'right_body'  => '',
			),
			(array) $atts,
			$shortcode
		);
	}

	/**
	 * Render the case-study compare as a two-card card-icon-break variant.
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 * @return string
	 */
	private static function renderCardBreak( array $atts ): string {
		$left_card  = self::renderCard( $atts['left_icon'], (string) $atts['left_title'], (string) $atts['left_body'] );
		$right_card = self::renderCard( $atts['right_icon'], (string) $atts['right_title'], (string) $atts['right_body'] );

		if ( '' === $left_card && '' === $right_card ) {
			return '';
		}

		ob_start();
		?>
		<div class="bma-case-study-compare bma-case-study-compare--card-break">
			<div class="bma-case-study-compare__cards">
				<?php echo $left_card; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( '' !== $left_card && '' !== $right_card ) : ?>
					<div class="bma-case-study-compare__arrow" aria-hidden="true">
						<?php echo self::arrowSvg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
				<?php echo $right_card; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
		$html = (string) ob_get_clean();
		$html = preg_replace( '/>\s+</', '><', $html );

		return trim( (string) $html );
	}

	/**
	 * Render one compare side using the card-icon-break card renderer.
	 *
	 * @param int|string $icon  Attachment id or URL.
	 * @param string     $title Card title.
	 * @param string     $body  Card body. Newlines are treated as line breaks.
	 * @return string
	 */
	private static function renderCard( $icon, string $title, string $body ): string {
		$body = trim( $body );
		$body = preg_replace( '/<br\s*\/?>/i', "\n", $body );
		$body = preg_replace( '/\R+/', '<br>', (string) $body );

		$icon_html = '';
		if ( '' !== (string) $icon ) {
			$icon_html = self::iconCardIconHtml( '', $icon, 'h-16 w-16 object-contain' );
		}

		if ( '' === trim( (string) $icon_html ) && '' === trim( $title ) && '' === trim( $body ) ) {
			return '';
		}

		$data = array(
			'icon_html' => $icon_html,
			'title'     => $title,
			'body_html' => wp_kses_post( do_shortcode( $body ) ),
		);

		// Prefer a theme-provided global so a host theme stays source of truth.
		if ( function_exists( 'bma_render_icon_card' ) ) {
			return bma_render_icon_card( $data, 'break' );
		}

		return self::renderIconCard( $data, 'break' );
	}

	/**
	 * Supplied card-to-card transition arrow.
	 *
	 * @return string Safe SVG markup.
	 */
	private static function arrowSvg(): string {
		$svg = '<svg class="bma-case-study-compare__arrow-svg" xmlns="http://www.w3.org/2000/svg" width="37.224" height="37.225" viewBox="0 0 37.224 37.225" focusable="false"><path d="M20.627,1.995A18.613,18.613,0,1,0,39.238,20.608,18.622,18.622,0,0,0,20.627,1.995Zm0,2.792a15.82,15.82,0,1,1-15.82,15.82,15.827,15.827,0,0,1,15.82-15.82Zm2.845,8.778s2.8,2.8,6.06,6.067a1.4,1.4,0,0,1,0,1.975c-3.263,3.265-6.058,6.065-6.058,6.065a1.382,1.382,0,0,1-.981.4A1.4,1.4,0,0,1,21.5,25.7l3.682-3.68H12.718a1.4,1.4,0,0,1,0-2.792H25.18L21.5,15.54a1.389,1.389,0,0,1,.011-1.962,1.4,1.4,0,0,1,.989-.413A1.373,1.373,0,0,1,23.471,13.565Z" transform="translate(-2.014 -1.995)" fill="#93a8a4"/></svg>';

		// Soft dep: component-svg-helper provides bma_safe_svg(); pass through otherwise.
		if ( function_exists( 'bma_safe_svg' ) ) {
			return bma_safe_svg( $svg );
		}

		return $svg;
	}

	// -----------------------------------------------------------------------
	// Inlined icon-card helpers (from rockerbox inc/shortcodes/bma-icon-card.php).
	// Only the logic needed by this component is inlined. Global equivalents,
	// when defined by a theme, are preferred above.
	// -----------------------------------------------------------------------

	/**
	 * Render an icon (inline SVG priority, attachment id fallback) for an icon card.
	 *
	 * Soft deps (component-svg-helper / component-image-helper):
	 * bma_safe_svg(), bma_inline_svg_attachment(), bma_render_image_or_svg().
	 *
	 * @param string     $svg       Raw SVG markup (priority).
	 * @param int|string $icon      Attachment id (int) or raw SVG string.
	 * @param string     $img_class CSS class for the <img> when using an attachment.
	 * @return string Icon HTML (safe).
	 */
	private static function iconCardIconHtml( string $svg, $icon = '', string $img_class = '' ): string {
		// Prefer a theme-provided global so a host theme stays source of truth.
		if ( function_exists( 'bma_icon_card_icon_html' ) ) {
			return bma_icon_card_icon_html( $svg, $icon, $img_class );
		}

		$svg = trim( $svg );
		if ( '' !== $svg ) {
			return function_exists( 'bma_safe_svg' ) ? bma_safe_svg( $svg ) : $svg;
		}
		if ( ! empty( $icon ) ) {
			// For SVG attachments, inline the file contents so `fill="currentColor"`
			// (the icon's green) is preserved. An <img> would break currentColor.
			if ( is_numeric( $icon ) && function_exists( 'bma_inline_svg_attachment' ) ) {
				$inline = bma_inline_svg_attachment( (int) $icon );
				if ( '' !== $inline ) {
					return $inline;
				}
			}
			if ( function_exists( 'bma_render_image_or_svg' ) ) {
				return bma_render_image_or_svg( $icon, 'full', $img_class );
			}
			// Last-resort fallback: a plain <img> when no helper is available and
			// the icon is a URL (numeric ids need the attachment helper).
			if ( ! is_numeric( $icon ) ) {
				return '<img src="' . esc_url( (string) $icon ) . '" alt="" class="' . esc_attr( $img_class ) . '" />';
			}
		}
		return '';
	}

	/**
	 * Render a single icon card in the card-icon-break variant.
	 *
	 * @param array  $data {
	 *     @type string $icon_html Pre-rendered icon HTML (img or inline SVG, safe).
	 *     @type string $title     Plain-text title.
	 *     @type string $body_html Body HTML (already safe).
	 * }
	 * @param string $variant 'break' (circle icon-bg) or 'stacked' (icon-breathe).
	 * @return string Card HTML.
	 */
	private static function renderIconCard( array $data, string $variant = 'break' ): string {
		$icon_html = (string) ( $data['icon_html'] ?? '' );
		$title     = trim( (string) ( $data['title'] ?? '' ) );
		$title_top = trim( (string) ( $data['title_top'] ?? '' ) );
		$body_html = (string) ( $data['body_html'] ?? '' );
		$style     = in_array( (string) ( $data['style'] ?? '' ), array( 'card', 'cards', 'boxed' ), true ) ? 'cards' : 'plain';
		// The WYSIWYG body comes wrapped in <p> tags; once embedded in the_content,
		// wpautop re-wraps and emits stray empty <p></p>. Strip the paragraph
		// wrappers (single-sentence card bodies) so no block tags survive for
		// wpautop to mangle. Multi-paragraph bodies collapse to <br><br>.
		$body_html = preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', $body_html );
		$body_html = preg_replace( '/<\/p>\s*<p[^>]*>/i', '<br><br>', $body_html );
		$body_html = preg_replace( '/<\/?p[^>]*>/i', '', $body_html );
		// Trim orphan <br> left where wpautop's empty paragraphs used to be.
		$body_html = preg_replace( '/^(?:\s*<br\s*\/?>\s*)+/i', '', $body_html );
		$body_html = preg_replace( '/(?:\s*<br\s*\/?>\s*)+$/i', '', $body_html );
		$body_html = trim( (string) $body_html );

		ob_start();

		if ( 'stacked' === $variant ) :
			$card_class = 'bma-icon-card';
			if ( 'cards' === $style ) {
				$card_class .= ' bma-icon-card--card';
			}
			?>
			<div class="<?php echo esc_attr( $card_class ); ?>">
				<?php if ( '' !== $icon_html ) : ?>
					<div class="bma-icon-card__icon icon-breathe"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
				<?php if ( '' !== $title ) : ?>
					<h3 class="bma-icon-card__title">
						<?php if ( '' !== $title_top ) : ?>
							<span class="bma-icon-card__title-top"><?php echo esc_html( $title_top ); ?></span>
						<?php endif; ?>
						<?php echo esc_html( $title ); ?>
					</h3>
				<?php endif; ?>
				<?php if ( '' !== trim( $body_html ) ) : ?>
					<div class="bma-icon-card__body"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
			<?php
		else :
			?>
			<div class="bma-card-icon-break">
				<?php if ( '' !== $icon_html ) : ?>
					<div class="bma-card-icon-break__icon">
						<div class="icon-bg">
							<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="bma-card-icon-break__content">
					<?php if ( '' !== $title ) : ?>
						<h3><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>
					<?php if ( '' !== trim( $body_html ) ) : ?>
						<p><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php
		endif;

		$html = (string) ob_get_clean();
		// Collapse inter-tag whitespace so wpautop (run later on the_content) does
		// not inject stray <p>/<br> tags between our block-level elements.
		$html = preg_replace( '/>\s+</', '><', $html );

		return trim( (string) $html );
	}

	// -----------------------------------------------------------------------
	// WPBakery element registration
	// -----------------------------------------------------------------------

	/**
	 * Register both compare elements with WPBakery (vc_before_init).
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$params = self::vcParams();

		vc_map(
			array(
				'name'        => __( 'BMA Case Study Compare', 'balefire' ),
				'base'        => 'bma_compare',
				'category'    => __( 'BMA Elements', 'balefire' ),
				'description' => __( 'Before/after card-break comparison with transition arrow.', 'balefire' ),
				'icon'        => 'vc_icon-vc-row',
				'params'      => $params,
			)
		);

		vc_map(
			array(
				'name'        => __( 'BMA Compare Card Break', 'balefire' ),
				'base'        => 'bma_compare_cards',
				'category'    => __( 'BMA Cards', 'balefire' ),
				'description' => __( 'Two card-icon-break cards with a centered arrow between them.', 'balefire' ),
				'icon'        => 'vc_icon-vc-row',
				'params'      => $params,
			)
		);
	}

	/**
	 * Shared WPBakery params for compare shortcodes.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function vcParams(): array {
		return array(
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Left icon', 'balefire' ),
				'param_name' => 'left_icon',
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Left title', 'balefire' ),
				'param_name' => 'left_title',
			),
			array(
				'type'        => 'textarea',
				'heading'     => __( 'Left body', 'balefire' ),
				'param_name'  => 'left_body',
				'description' => __( 'Allows line breaks, <br>, and <strong>.', 'balefire' ),
			),
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Right icon', 'balefire' ),
				'param_name' => 'right_icon',
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Right title', 'balefire' ),
				'param_name' => 'right_title',
			),
			array(
				'type'        => 'textarea',
				'heading'     => __( 'Right body', 'balefire' ),
				'param_name'  => 'right_body',
				'description' => __( 'Allows line breaks, <br>, and <strong>.', 'balefire' ),
			),
		);
	}
}
