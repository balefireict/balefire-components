<?php
/**
 * BMA Card Stat shortcode (parent grid + stat card child).
 *
 * Parent: [bma_card_stat_grid columns="3"] wraps a series of stat cards and
 *         emits the bma-auto-grid classes (auto-grid CSS owned by
 *         component-auto-grid, not this package).
 * Child:  [bma_card_stat title="" icon="" left_value="" left_label=""
 *             right_value="" right_label="" card_style="white|dark"]
 *             [bma_stat_icon]<svg>…</svg>[/bma_stat_icon]
 *         [/bma_card_stat]
 *         Renders one card: icon + title head, then two stat pairs.
 *
 * Source of truth classes. Global function wrappers
 * (bma_card_stat_grid_shortcode, bma_card_stat_shortcode) are defined in
 * bootstrap.php. add_shortcode, vc_map, and the WPBakeryShortCodesContainer
 * subclass are also wired there.
 *
 * Ported from rockerbox inc/shortcodes/bma-card-stat.php.
 *
 * @package Balefire\Component\CardStat
 */

declare( strict_types=1 );

namespace Balefire\Component\CardStat;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the BMA Card Stat parent grid + child card shortcodes.
 *
 * @package Balefire\Component\CardStat
 */
final class CardStat {

	public const CARD_STYLES = array( 'white', 'dark' );

	/**
	 * Render the parent [bma_card_stat_grid] container. Wraps the children
	 * (processed via do_shortcode) in the auto-grid wrapper.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner child shortcodes.
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function renderGrid( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => '3',
			),
			$atts,
			'bma_card_stat_grid'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$cols      = (int) ( $atts['columns'] ?: 3 );
		$col_class = match ( $cols ) {
			1       => 'lg:auto-grid-cols-1',
			2       => 'lg:auto-grid-cols-2',
			4       => 'lg:auto-grid-cols-4',
			5       => 'lg:auto-grid-cols-5',
			default => 'lg:auto-grid-cols-3',
		};

		return '<div class="bma-auto-grid auto-grid-gap-6 auto-grid-cols-1 '
			. esc_attr( $col_class ) . '">' . do_shortcode( (string) $content ) . '</div>';
	}

	/**
	 * Render one [bma_card_stat] child card.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed content (may hold [bma_stat_icon]).
	 * @return string HTML output.
	 */
	public static function renderCard( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'title'       => '',
				'icon'        => '',
				'left_value'  => '',
				'left_label'  => '',
				'right_value' => '',
				'right_label' => '',
				'card_style'  => 'white',
				'variant'     => '',
				'theme'       => '',
				'style'       => '',
				'el_class'    => '',
				'class'       => '',
			),
			$atts,
			'bma_card_stat'
		);

		// Pull inline SVG icon out of the enclosed content.
		$icon_svg = '';
		$body     = (string) $content;
		if ( preg_match( '/\[bma_stat_icon\](.*?)\[\/bma_stat_icon\]/is', $body, $m ) ) {
			$icon_svg = $m[1];
		}

		$icon_html = '';
		if ( '' !== trim( $icon_svg ) ) {
			$icon_html = function_exists( 'bma_safe_svg' )
				? bma_safe_svg( $icon_svg )
				: wp_kses_post( $icon_svg );
		} elseif ( '' !== (string) $atts['icon'] ) {
			if ( function_exists( 'bma_render_image_or_svg' ) ) {
				$icon_html = bma_render_image_or_svg( $atts['icon'], 'full', 'bma-card-stat__img' );
			} elseif ( ctype_digit( (string) $atts['icon'] ) && function_exists( 'wp_get_attachment_image' ) ) {
				$icon_html = wp_get_attachment_image(
					(int) $atts['icon'],
					'full',
					false,
					array( 'class' => 'bma-card-stat__img' )
				);
			}
		}

		$title       = trim( (string) $atts['title'] );
		$left_value  = trim( (string) $atts['left_value'] );
		$left_label  = trim( (string) $atts['left_label'] );
		$right_value = trim( (string) $atts['right_value'] );
		$right_label = trim( (string) $atts['right_label'] );

		$card_style = trim( (string) ( $atts['card_style'] ?: $atts['variant'] ?: $atts['theme'] ?: $atts['style'] ?: 'white' ) );
		$card_style = strtolower( $card_style );
		if ( ! in_array( $card_style, self::CARD_STYLES, true ) ) {
			$card_style = 'white';
		}

		$extra_classes = trim( (string) ( $atts['el_class'] ?: $atts['class'] ) );
		$class_parts   = array( 'bma-card-stat', 'bma-card-stat--' . $card_style );
		if ( '' !== $extra_classes ) {
			$class_parts = array_merge(
				$class_parts,
				array_filter(
					array_map(
						'sanitize_html_class',
						preg_split( '/\s+/', $extra_classes ) ?: array()
					)
				)
			);
		}
		$card_classes = implode( ' ', array_unique( array_filter( $class_parts ) ) );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $card_classes ); ?>">
			<div class="bma-card-stat__head">
				<?php if ( '' !== $title ) : ?>
					<h3 class="bma-card-stat__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== $icon_html ) : ?>
					<span class="bma-card-stat__icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
			</div>
			<div class="bma-card-stat__stats">
				<div class="bma-card-stat__stat">
					<span class="bma-card-stat__value"><?php echo esc_html( $left_value ); ?></span>
					<span class="bma-card-stat__label"><?php echo esc_html( $left_label ); ?></span>
				</div>
				<div class="bma-card-stat__stat">
					<span class="bma-card-stat__value"><?php echo esc_html( $right_value ); ?></span>
					<span class="bma-card-stat__label"><?php echo esc_html( $right_label ); ?></span>
				</div>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Inline icon passthrough for [bma_stat_icon]<svg>…</svg>[/bma_stat_icon].
	 *
	 * @param array       $atts    Shortcode attributes (unused).
	 * @param string|null $content Inline SVG markup.
	 * @return string Sanitized SVG.
	 */
	public static function renderIcon( $atts, ?string $content = null ): string {
		$svg = (string) $content;
		return function_exists( 'bma_safe_svg' )
			? bma_safe_svg( $svg )
			: wp_kses_post( $svg );
	}

	/**
	 * Register all three shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_card_stat_grid', array( self::class, 'renderGrid' ) );
		add_shortcode( 'bma_card_stat', array( self::class, 'renderCard' ) );
		add_shortcode( 'bma_stat_icon', array( self::class, 'renderIcon' ) );
	}

	/**
	 * WPBakery vc_map registration for both parent grid and child card.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'BMA Card Stat Grid', 'balefire' ),
				'base'                    => 'bma_card_stat_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_CardStat',
				'category'                => __( 'BMA Cards', 'balefire' ),
				'description'             => __( 'Industry stat cards (icon + title + two stats).', 'balefire' ),
				'icon'                    => 'vc_icon-vc-row',
				'as_parent'               => array( 'only' => 'bma_card_stat' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => array( '3', '1', '2', '4', '5' ),
						'std'        => '3',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'BMA Card Stat', 'balefire' ),
				'base'            => 'bma_card_stat',
				'category'        => __( 'BMA Cards', 'balefire' ),
				'description'     => __( 'A single industry stat card.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_card_stat_grid' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
					),
					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Icon (image)', 'balefire' ),
						'param_name'  => 'icon',
						'description' => __( 'Or use [bma_stat_icon]<svg>…</svg>[/bma_stat_icon] in content.', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Left value', 'balefire' ),
						'param_name' => 'left_value',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Left label', 'balefire' ),
						'param_name' => 'left_label',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Right value', 'balefire' ),
						'param_name' => 'right_value',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Right label', 'balefire' ),
						'param_name' => 'right_label',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Card style', 'balefire' ),
						'param_name' => 'card_style',
						'value'      => array(
							__( 'White', 'balefire' ) => 'white',
							__( 'Dark', 'balefire' )  => 'dark',
						),
						'std'        => 'white',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra CSS class', 'balefire' ),
						'param_name'  => 'el_class',
						'description' => __( 'Optional custom class added to the card wrapper.', 'balefire' ),
					),
					array(
						'type'        => 'textarea_html',
						'heading'     => __( 'Icon SVG (content)', 'balefire' ),
						'param_name'  => 'content',
						'description' => __( 'Wrap inline SVG in [bma_stat_icon]…[/bma_stat_icon].', 'balefire' ),
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakeryShortCodesContainer subclass so the parent grid is
	 * recognized as a container in the editor. Hooked on vc_after_init.
	 */
	public static function registerContainerClass(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_CardStat' ) ) {
			eval( 'class WPBakeryShortCode_BMA_CardStat extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
