<?php
/**
 * BMA Footer CTA shortcode (single element).
 *
 * [bma_footer_cta preheader="..." title="..." btn1_label="..." btn1_url="..."
 *   btn1_arrow="true" btn2_label="..." btn2_url="..."]
 *     <p>CTA copy.</p>
 * [/bma_footer_cta]
 *
 * The shortcode is intended to be placed inside a WPBakery [vc_row] (or any
 * other element) that supplies its own background color and id — e.g.
 * <div id="cta-green-gradient" class="vc_row ...">. The shortcode only emits
 * the inner content block; it does NOT add a <section>, background utility,
 * or id of its own.
 *
 * Global function wrapper (bma_footer_cta_shortcode) is defined in
 * bootstrap.php. add_shortcode and vc_map are also wired there.
 *
 * The CTA button helpers (formerly the global bma_render_cta_btns() /
 * bma_render_cta_button() / bma_cta_button_class() / bma_cta_button_styles()
 * from inc/shortcodes/bma-cta-btn.php) are inlined here as static methods.
 * If a theme still defines the globals, those are preferred.
 *
 * @package Balefire\Component\FooterCta
 */

declare( strict_types=1 );

namespace Balefire\Component\FooterCta;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_footer_cta] shortcode.
 *
 * @package Balefire\Component\FooterCta
 */
final class FooterCta {

	/**
	 * Normalize WPBakery shortcode attributes.
	 *
	 * WPBakery can serialize params with hyphenated names even when vc_map uses
	 * underscores, so keep both spellings alive.
	 *
	 * @param array $raw_atts Raw shortcode attributes.
	 * @return array Normalized attributes.
	 */
	private static function normalizeAtts( array $raw_atts ): array {
		$atts = shortcode_atts(
			array(
				'post_id'    => '',
				'preheader'  => '',
				'title'      => '',
				'content'    => '',
				'btn1_label' => '',
				'btn1_url'   => '',
				'btn1_arrow' => 'false',
				'btn2_label' => '',
				'btn2_url'   => '',
			),
			$raw_atts,
			'bma_footer_cta'
		);

		foreach ( array( 'post_id', 'btn1_label', 'btn1_url', 'btn1_arrow', 'btn2_label', 'btn2_url' ) as $key ) {
			$hyphen_key = str_replace( '_', '-', $key );
			if ( isset( $raw_atts[ $hyphen_key ] ) && '' !== (string) $raw_atts[ $hyphen_key ] ) {
				$atts[ $key ] = $raw_atts[ $hyphen_key ];
			}
		}

		foreach ( array( 'preheader', 'title', 'content', 'btn1_label', 'btn1_url', 'btn2_label', 'btn2_url' ) as $key ) {
			$atts[ $key ] = html_entity_decode( (string) $atts[ $key ], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		return $atts;
	}

	/**
	 * Get legacy ACF/postmeta values for old [bma_footer_cta] usage.
	 *
	 * This keeps existing un-migrated shortcodes from going blank, but the
	 * forward path is the WPBakery element params stored directly in
	 * post_content. Plain WP postmeta only.
	 *
	 * @param int $post_id Post ID.
	 * @return array Legacy CTA values.
	 */
	private static function legacyValues( int $post_id ): array {
		$values = array(
			'preheader'  => '',
			'title'      => '',
			'content'    => '',
			'btn1_label' => '',
			'btn1_url'   => '',
			'btn1_arrow' => 'false',
			'btn2_label' => '',
			'btn2_url'   => '',
		);

		if ( ! $post_id ) {
			return $values;
		}

		$values['preheader'] = (string) get_post_meta( $post_id, 'footer_cta_preheader', true );
		$values['title']     = (string) get_post_meta( $post_id, 'footer_cta_title', true );
		$values['content']   = (string) get_post_meta( $post_id, 'footer_cta_content', true );

		$button_count = (int) get_post_meta( $post_id, 'cta_btns', true );
		if ( $button_count > 0 ) {
			$values['btn1_label'] = (string) get_post_meta( $post_id, 'cta_btns_0_label', true );
			$values['btn1_url']   = (string) get_post_meta( $post_id, 'cta_btns_0_url', true );
			$values['btn1_arrow'] = get_post_meta( $post_id, 'cta_btns_0_show_arrow', true ) ? 'true' : 'false';
		}
		if ( $button_count > 1 ) {
			$values['btn2_label'] = (string) get_post_meta( $post_id, 'cta_btns_1_label', true );
			$values['btn2_url']   = (string) get_post_meta( $post_id, 'cta_btns_1_url', true );
		}

		return $values;
	}

	/**
	 * Render the centered footer CTA content block.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed content from WPBakery textarea_html.
	 * @return string HTML output.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = self::normalizeAtts( $atts );

		$post_id = ! empty( $atts['post_id'] ) ? (int) $atts['post_id'] : (int) get_the_ID();

		$has_direct_values = '' !== trim( (string) $atts['preheader'] )
			|| '' !== trim( (string) $atts['title'] )
			|| '' !== trim( (string) $atts['content'] )
			|| ( null !== $content && '' !== trim( (string) $content ) )
			|| '' !== trim( (string) $atts['btn1_label'] )
			|| '' !== trim( (string) $atts['btn1_url'] )
			|| '' !== trim( (string) $atts['btn2_label'] )
			|| '' !== trim( (string) $atts['btn2_url'] );

		if ( ! $has_direct_values ) {
			$atts    = array_merge( $atts, self::legacyValues( $post_id ) );
			$content = $atts['content'];
		}

		$preheader = trim( (string) $atts['preheader'] );
		$title     = trim( (string) $atts['title'] );
		$body      = null !== $content && '' !== trim( (string) $content )
			? (string) $content
			: (string) $atts['content'];

		$buttons = array(
			array(
				'label'      => (string) $atts['btn1_label'],
				'url'        => (string) $atts['btn1_url'],
				'style'      => 'white',
				'show_arrow' => filter_var( $atts['btn1_arrow'], FILTER_VALIDATE_BOOLEAN ),
			),
		);

		$btn2_label = trim( (string) $atts['btn2_label'] );
		$btn2_url   = trim( (string) $atts['btn2_url'] );
		if ( '' !== $btn2_label && '' !== $btn2_url ) {
			$buttons[] = array(
				'label'      => $btn2_label,
				'url'        => $btn2_url,
				'style'      => 'transparent',
				'show_arrow' => false,
			);
		}

		ob_start();
		// The wrapping <section>/bg/id is provided by the WPBakery vc_row that
		// contains this shortcode. We only emit the inner bma-cta block.
		?>
		<div class="bma-cta"><div class="bma-container">
			<div class="bma-cta__inner">
				<?php if ( '' !== $preheader ) : ?>
					<p class="bma-cta__preheader"><?php echo esc_html( $preheader ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $title ) : ?>
					<h2 class="bma-cta__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== trim( $body ) ) : ?>
					<div class="bma-cta__content"><?php echo wp_kses_post( do_shortcode( $body ) ); ?></div>
				<?php endif; ?>

				<?php
				echo self::renderButtons( $buttons ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes each attribute.
				?>
			</div>
		</div></div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render a list of cta_btns repeater rows.
	 *
	 * Prefers a theme-defined global bma_render_cta_btns() when present,
	 * otherwise uses the inlined implementation below.
	 *
	 * @param array $buttons Repeater rows.
	 * @return string HTML output. Empty string if no valid buttons.
	 */
	private static function renderButtons( array $buttons ): string {
		if ( function_exists( 'bma_render_cta_btns' ) ) {
			return (string) bma_render_cta_btns( $buttons );
		}

		$out = '';
		foreach ( $buttons as $row ) {
			$btn = self::renderButton( is_array( $row ) ? $row : array() );
			if ( '' !== $btn ) {
				$out .= $btn;
			}
		}
		if ( '' === $out ) {
			return '';
		}
		return '<div class="bma-cta__actions">' . $out . '</div>';
	}

	/**
	 * Allowed style values for renderButton().
	 *
	 * @return array
	 */
	private static function buttonStyles(): array {
		if ( function_exists( 'bma_cta_button_styles' ) ) {
			return (array) bma_cta_button_styles();
		}
		return array( 'white', 'primary', 'secondary', 'transparent' );
	}

	/**
	 * Map a cta_btns style value to a CSS class fragment.
	 *
	 * @param string $style One of buttonStyles().
	 * @return string Class fragment (e.g. 'btn btn-white' or 'btn-transparent').
	 */
	private static function buttonClass( string $style ): string {
		if ( function_exists( 'bma_cta_button_class' ) ) {
			return (string) bma_cta_button_class( $style );
		}
		switch ( $style ) {
			case 'transparent':
				return 'btn-transparent';
			case 'primary':
			case 'secondary':
				return 'btn btn-' . $style;
			case 'white':
			default:
				return 'btn btn-white';
		}
	}

	/**
	 * Render a single CTA button from a cta_btns repeater row.
	 *
	 * @param array $row Repeater row. Expected keys: label, url, style, show_arrow.
	 * @return string HTML output. Empty string if label/url missing.
	 */
	private static function renderButton( array $row ): string {
		if ( function_exists( 'bma_render_cta_button' ) ) {
			return (string) bma_render_cta_button( $row );
		}

		$label = trim( (string) ( $row['label'] ?? '' ) );
		$url   = trim( (string) ( $row['url'] ?? '' ) );
		if ( '' === $label || '' === $url ) {
			return '';
		}
		$style = in_array( $row['style'] ?? '', self::buttonStyles(), true )
			? (string) $row['style']
			: 'white';
		$class = self::buttonClass( $style );

		$show_arrow = ! empty( $row['show_arrow'] );

		$arrow = '';
		if ( $show_arrow ) {
			$arrow = '<span><svg xmlns="http://www.w3.org/2000/svg" width="10.913" height="9.379" viewBox="0 0 10.913 9.379" class="bma-hero-btn-arrow size-3 shrink-0 my-0.5 text-color-3" aria-hidden="true"><path d="M10.652,13.735l3.964-3.967a.476.476,0,0,0,0-.672L10.652,5.13a.466.466,0,0,0-.332-.138.476.476,0,0,0-.34.809l3.155,3.155H4.817a.475.475,0,0,0,0,.951h8.318L9.979,13.063a.477.477,0,0,0,.342.809.467.467,0,0,0,.331-.136Z" transform="translate(-4.092 -4.742)" fill="#93a8a4" stroke="currentColor" stroke-width="0.5"/></svg></span>';
		}

		return sprintf(
			'<a href="%s" class="%s">%s%s</a>',
			esc_url( $url ),
			esc_attr( $class ),
			esc_html( $label ),
			$arrow
		);
	}

	/**
	 * Register the [bma_footer_cta] shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'bma_footer_cta', array( self::class, 'render' ) );
	}

	/**
	 * WPBakery vc_map registration.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'        => __( 'BMA Footer CTA', 'balefire' ),
				'base'        => 'bma_footer_cta',
				'category'    => __( 'BMA Elements', 'balefire' ),
				'description' => __( 'Centered footer CTA content. Use inside the CTA gradient row.', 'balefire' ),
				'icon'        => 'vc_icon-vc-button',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Preheader', 'balefire' ),
						'param_name'  => 'preheader',
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
						'heading'    => __( 'Content', 'balefire' ),
						'param_name' => 'content',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'First Button Label', 'balefire' ),
						'param_name'  => 'btn1_label',
						'admin_label' => true,
						'group'       => __( 'Buttons', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'First Button URL', 'balefire' ),
						'param_name' => 'btn1_url',
						'group'      => __( 'Buttons', 'balefire' ),
					),
					array(
						'type'        => 'checkbox',
						'heading'     => __( 'First Button Arrow', 'balefire' ),
						'param_name'  => 'btn1_arrow',
						'value'       => array( __( 'Show arrow', 'balefire' ) => 'true' ),
						'std'         => 'false',
						'description' => __( 'First button is always solid white.', 'balefire' ),
						'group'       => __( 'Buttons', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Second Button Label', 'balefire' ),
						'param_name'  => 'btn2_label',
						'admin_label' => true,
						'description' => __( 'Second button is transparent and only renders when label and URL are both set.', 'balefire' ),
						'group'       => __( 'Buttons', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Second Button URL', 'balefire' ),
						'param_name' => 'btn2_url',
						'group'      => __( 'Buttons', 'balefire' ),
					),
				),
			)
		);
	}
}
