<?php
/**
 * BMA Stat Callout shortcode (parent + child).
 *
 * Parent: [bma_stat_callout columns="2|3|4"]…[/bma_stat_callout]
 *         Renders the grid wrapper section.
 * Child:  [bma_stat value="99%" label="Uptime"]
 *         Renders a single stat item.
 *
 * Attribute-driven: no ACF reads. Source of truth class. Global function
 * wrappers and the WPBakery container-class registration live in bootstrap.php.
 *
 * @package Balefire\Component\StatCallout
 */

declare( strict_types=1 );

namespace Balefire\Component\StatCallout;

defined( 'ABSPATH' ) || exit;

/**
 * Static stat callout parent + child renderers.
 *
 * @package Balefire\Component\StatCallout
 */
final class StatCallout {

	public const COLUMN_CHOICES  = array( 2, 3, 4 );
	public const DEFAULT_COLUMNS = 4;

	/**
	 * Render the parent [bma_stat_callout] shortcode. Wraps children in
	 * the grid element. Returns '' when there is no child content.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => (string) self::DEFAULT_COLUMNS,
				'id'      => '',
				'class'   => '',
			),
			$atts,
			'bma_stat_callout'
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
		$inner = (string) preg_replace( '/(<\/[^>]+>)\s*<br\s*\/?>\s*(<div class="bma-c-stat-callout__stat\b)/i', '$1$2', (string) $inner );
		$inner = trim( (string) $inner );

		if ( '' === $inner ) {
			return '';
		}

		$classes = array( 'bma-c-stat-callout' );
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = $extra;
		}

		$id        = trim( (string) $atts['id'] );
		$id_attr   = '' !== $id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '';
		$list_mod  = sprintf( 'bma-c-stat-callout__list--%d', $columns );

		return sprintf(
			'<section class="%1$s"%2$s><div class="bma-c-stat-callout__inner"><div class="bma-c-stat-callout__list %3$s">%4$s</div></div></section>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			$id_attr, // already escaped above.
			esc_attr( $list_mod ),
			$inner
		);
	}

	/**
	 * Render one [bma_stat] child.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when the stat has no content.
	 */
	public static function renderStat( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'value' => '',
				'label' => '',
			),
			$atts,
			'bma_stat'
		);

		$value = trim( (string) $atts['value'] );
		$label = trim( (string) $atts['label'] );

		if ( '' === $value && '' === $label ) {
			return '';
		}

		ob_start();
		?>
		<div class="bma-c-stat-callout__stat">
			<?php if ( '' !== $value ) : ?>
				<span class="bma-c-stat-callout__value"><?php echo esc_html( $value ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $label ) : ?>
				<span class="bma-c-stat-callout__label"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
		</div>
		<?php
		$html = (string) ob_get_clean();
		$html = (string) preg_replace( '/>\s+</', '><', $html );

		return trim( (string) $html );
	}

	/**
	 * Register both [bma_stat_callout] and [bma_stat] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_stat_callout', array( self::class, 'render' ) );
		add_shortcode( 'bma_stat', array( self::class, 'renderStat' ) );
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
				'name'                    => __( 'Stat Callout', 'balefire' ),
				'base'                    => 'bma_stat_callout',
				'php_class_name'          => 'WPBakeryShortCode_BMA_StatCallout',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Grid of statistics with large numbers and labels.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'bma_stat' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns', 'balefire' ),
						'param_name' => 'columns',
						'value'      => $column_choices,
						'std'        => (string) self::DEFAULT_COLUMNS,
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Element ID', 'balefire' ),
						'param_name' => 'id',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra CSS class', 'balefire' ),
						'param_name' => 'class',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Stat', 'balefire' ),
				'base'            => 'bma_stat',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — Single statistic value and label.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_stat_callout' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Value', 'balefire' ),
						'param_name'  => 'value',
						'description' => __( 'The large number or value (e.g. "99%", "$2M").', 'balefire' ),
						'admin_label' => true,
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Label', 'balefire' ),
						'param_name'  => 'label',
						'description' => __( 'Description label below the value.', 'balefire' ),
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
		if ( ! class_exists( 'WPBakeryShortCode_BMA_StatCallout' ) ) {
			eval( 'class WPBakeryShortCode_BMA_StatCallout extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
