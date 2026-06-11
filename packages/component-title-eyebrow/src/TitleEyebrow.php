<?php
/**
 * BMA Title + Eyebrow — section heading with a small uppercase eyebrow above it.
 *
 * Ported from rockerbox theme: inc/shortcodes/bma-title-eyebrow.php
 *
 * @package Balefire\Component\TitleEyebrow
 */

declare( strict_types=1 );

namespace Balefire\Component\TitleEyebrow;

defined( 'ABSPATH' ) || exit;

/**
 * Title + Eyebrow single-element shortcode.
 */
final class TitleEyebrow {

	/**
	 * Shortcode base name.
	 */
	private const SHORTCODE = 'bma_title_eyebrow';

	/**
	 * Register the shortcode.
	 */
	public static function register(): void {
		add_shortcode( self::SHORTCODE, array( self::class, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render( array $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'eyebrow' => '',
				'title'   => '',
				'align'   => 'center',
				'color'   => 'default',
			),
			$atts,
			self::SHORTCODE
		);

		$eyebrow = trim( (string) $atts['eyebrow'] );
		$title   = trim( (string) $atts['title'] );
		$align   = in_array( strtolower( (string) $atts['align'] ), array( 'left', 'center', 'right' ), true ) ? strtolower( (string) $atts['align'] ) : 'center';
		$color   = in_array( strtolower( (string) $atts['color'] ), array( 'default', 'white' ), true ) ? strtolower( (string) $atts['color'] ) : 'default';

		if ( '' === $eyebrow && '' === $title ) {
			return '';
		}

		$wrapper_classes = array( 'bma-preheader-and-title', 'bma-title-eyebrow' );
		if ( 'center' === $align ) {
			$wrapper_classes[] = 'text-center';
		} elseif ( 'right' === $align ) {
			$wrapper_classes[] = 'text-right';
		} else {
			$wrapper_classes[] = 'text-left';
		}
		if ( 'white' === $color ) {
			$wrapper_classes[] = 'bma-title-eyebrow--white';
		}
		$wrapper_class = esc_attr( implode( ' ', $wrapper_classes ) );

		ob_start();
		?>
		<div class="<?php echo $wrapper_class; ?>">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="bma-preheader"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h2 class="bma-preheader-and-title__heading">
					<?php echo esc_html( $title ); ?>
				</h2>
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
				'name'        => __( 'BMA Title + Eyebrow', 'balefire' ),
				'base'        => self::SHORTCODE,
				'category'    => __( 'BMA Elements', 'balefire' ),
				'description' => __( 'Section heading with a small uppercase eyebrow above it.', 'balefire' ),
				'icon'        => 'vc_icon-vc-custom-heading',
				'params'      => array(

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Heading Eyebrow', 'balefire' ),
						'param_name'  => 'eyebrow',
						'description' => __( 'Small uppercase label shown above the heading.', 'balefire' ),
						'admin_label' => true,
					),

					array(
						'type'        => 'textfield',
						'heading'     => __( 'Heading', 'balefire' ),
						'param_name'  => 'title',
						'description' => __( 'The main section heading (H2).', 'balefire' ),
						'admin_label' => true,
					),

					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Alignment', 'balefire' ),
						'param_name' => 'align',
						'value'      => array(
							__( 'Left', 'balefire' )   => 'left',
							__( 'Center', 'balefire' ) => 'center',
							__( 'Right', 'balefire' )  => 'right',
						),
						'std'        => 'center',
					),

					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Text Color', 'balefire' ),
						'param_name' => 'color',
						'value'      => array(
							__( 'Default (inherits)', 'balefire' ) => 'default',
							__( 'White', 'balefire' )              => 'white',
						),
						'std'        => 'default',
					),

				),
			)
		);
	}
}
