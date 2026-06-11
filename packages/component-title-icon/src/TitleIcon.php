<?php
/**
 * BMA Title Icon — icon + title row with supporting paragraph.
 *
 * @package Balefire\Component\TitleIcon
 */

declare( strict_types=1 );

namespace Balefire\Component\TitleIcon;

defined( 'ABSPATH' ) || exit;

/**
 * Icon + title single-element shortcode.
 */
final class TitleIcon {

	/**
	 * Shortcode base name.
	 */
	private const SHORTCODE = 'bma_title_icon';

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
				'title'          => 'Premium Charters',
				'text'           => 'Philadelphia’s most professional and seamless charter bus experience.',
				'tag'            => 'h2',
				'align'          => 'center',
				'max_width'      => '598px',
				'icon_width'     => '40px',
				'icon_height'    => '36px',
				'gap'            => '24px',
				'title_color'    => '#ffffff',
				'text_color'     => '#e3e8ff',
				'icon_color'     => '#6f779d',
				'title_size'     => '55px',
				'text_size'      => '17px',
			),
			$atts,
			self::SHORTCODE
		);

		$title = trim( (string) $atts['title'] );
		$text  = trim( (string) $atts['text'] );
		if ( '' === $title && '' === $text ) {
			return '';
		}

		$tag   = self::sanitizeHeadingTag( (string) $atts['tag'] );
		$align = self::sanitizeChoice( (string) $atts['align'], array( 'left', 'center', 'right' ), 'center' );
		$style = sprintf(
			'--bma-title-icon-max-width:%s;--bma-title-icon-icon-width:%s;--bma-title-icon-icon-height:%s;--bma-title-icon-gap:%s;--bma-title-icon-title-color:%s;--bma-title-icon-text-color:%s;--bma-title-icon-icon-color:%s;--bma-title-icon-title-size:%s;--bma-title-icon-text-size:%s;',
			esc_attr( self::sanitizeLength( (string) $atts['max_width'], '598px' ) ),
			esc_attr( self::sanitizeLength( (string) $atts['icon_width'], '40px' ) ),
			esc_attr( self::sanitizeLength( (string) $atts['icon_height'], '36px' ) ),
			esc_attr( self::sanitizeLength( (string) $atts['gap'], '24px' ) ),
			esc_attr( self::sanitizeHexColor( (string) $atts['title_color'], '#ffffff' ) ),
			esc_attr( self::sanitizeHexColor( (string) $atts['text_color'], '#e3e8ff' ) ),
			esc_attr( self::sanitizeHexColor( (string) $atts['icon_color'], '#6f779d' ) ),
			esc_attr( self::sanitizeLength( (string) $atts['title_size'], '55px' ) ),
			esc_attr( self::sanitizeLength( (string) $atts['text_size'], '17px' ) )
		);

		$classes = esc_attr( 'bma-title-icon bma-title-icon--align-' . $align );

		ob_start();
		?>
		<div class="<?php echo $classes; ?>" style="<?php echo $style; ?>">
			<?php if ( '' !== $title ) : ?>
				<div class="bma-title-icon__heading-row">
					<span class="bma-title-icon__icon" aria-hidden="true"><?php echo self::renderIconSvg(); ?></span>
					<<?php echo esc_html( $tag ); ?> class="bma-title-icon__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
				</div>
			<?php endif; ?>
			<?php if ( '' !== $text ) : ?>
				<p class="bma-title-icon__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render the extracted XD icon as a scalable SVG using currentColor.
	 *
	 * @return string SVG markup.
	 */
	private static function renderIconSvg(): string {
		return '<svg class="bma-title-icon__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39.587 36" role="presentation" aria-hidden="true" focusable="false">'
			. '<path d="M21.13,37.256c-.235,0-.458-.152-.77-.524s-.619-.764-.915-1.141l-.184-.235L7.191,20l-4.97-6.325-.1-.127c-.04-.049-.079-.1-.118-.148-.661-.849-.661-.916,0-1.8Q3.14,10.093,4.278,8.587C5.856,6.5,7.489,4.334,9.083,2.2a1.611,1.611,0,0,1,1.425-.7c3.489.008,7.2.012,11.363.012q4.953,0,9.9-.006a1.446,1.446,0,0,1,1.315.568c1.544,2.069,3.123,4.165,4.651,6.192q1.29,1.712,2.579,3.425a4.525,4.525,0,0,1,.317.51c.048.085.1.174.152.264l.039.065-.041.064c-.061.094-.116.184-.17.271a5.48,5.48,0,0,1-.336.5c-1.376,1.762-2.892,3.695-4.769,6.082q-3.02,3.841-6.042,7.681l-4.058,5.158-1.949,2.48-1.415,1.8c-.049.062-.1.126-.175.207-.3.34-.518.478-.743.478m0-2.577L29.61,15.215c-.768-.069-3.76-.114-7.726-.114-4.554,0-8.406.059-9.226.137Zm16.9-21.009c-.226.024-.4.043-.574.078-.6.123-1.208.253-1.8.378-1.182.252-2.405.512-3.614.737A1.226,1.226,0,0,0,31,15.688c-1.5,3.5-3.046,7.044-4.542,10.473q-.716,1.642-1.432,3.284-.344.791-.687,1.583l-.211.486L38.173,13.656l-.136.014M18.114,31.492a.151.151,0,0,0,0-.039l-1.959-4.5c-1.634-3.748-3.323-7.624-4.975-11.44a.969.969,0,0,0-.82-.621c-1.308-.255-2.634-.53-3.916-.8q-.964-.2-1.928-.4a3.387,3.387,0,0,0-.381-.046l-.047,0ZM28.836,13.586c-.864-1.3-6.627-8.433-7.689-9.509-.91.885-6.822,8.172-7.722,9.509Zm-18.143-.121L9.511,4.18,9.5,4.187a1.057,1.057,0,0,0-.156.149C7.631,6.625,5.769,9.115,3.653,11.948l0,.007s.01.023.018.04l.008.016ZM32.894,4.3a39.953,39.953,0,0,0-.733,4.5c-.064.566-.145,1.142-.223,1.7-.134.958-.273,1.946-.342,2.968l7.1-1.482ZM30.13,12.63l.053-.016,1.224-9.587H22.3Zm-18.061-.136c1.127-1.12,6.861-8.073,7.79-9.453-.564-.051-1.882-.081-3.572-.081-2.589,0-4.868.065-5.432.152Z" transform="translate(-1.384 -1.378)" fill="currentColor"/>'
			. '<path d="M9.124.244h.006Q14.8.257,20.479.256q4.957,0,9.913-.006h0a1.327,1.327,0,0,1,1.212.519c2.4,3.214,4.821,6.411,7.23,9.617a8.91,8.91,0,0,1,.461.763c-.186.287-.324.541-.5.765Q36.422,14.96,34.032,18q-5.048,6.421-10.1,12.839-1.683,2.14-3.364,4.282c-.054.069-.112.136-.17.2-.259.29-.456.437-.652.437s-.407-.159-.677-.48c-.376-.448-.735-.912-1.1-1.372L.934,12.22c-.072-.092-.147-.182-.219-.274-.625-.8-.63-.806,0-1.65C3.079,7.162,5.45,4.038,7.8.894A1.487,1.487,0,0,1,9.124.244m1.47,11.116c.6-.39,7.6-8.906,8.087-9.795-.45-.071-2.036-.1-3.778-.1a52.983,52.983,0,0,0-5.568.178l1.258,9.721M30.162,1.528h-9.5L28.7,11.393l.207-.061,1.252-9.8M11.826,12.33H27.667c-.262-.619-7.017-8.988-7.9-9.8-.6.457-7.621,9.1-7.942,9.8m-2.373-.089L8.22,2.558a3.512,3.512,0,0,0-.354.328Q5,6.7,2.151,10.524c-.013.017,0,.055,0,.082a1.085,1.085,0,0,0,.061.134l7.239,1.5m20.627,0,7.45-1.554L31.451,2.641a39.765,39.765,0,0,0-.8,4.765c-.18,1.578-.475,3.146-.576,4.832m6.987-.115c-.463.057-.741.072-1.011.128-1.8.372-3.6.777-5.408,1.114a1.344,1.344,0,0,0-1.138.9c-1.97,4.595-3.977,9.173-5.973,13.757q-.5,1.152-1,2.3l.171.065L37.066,12.123m-20.33,18.2.115-.126a.518.518,0,0,0-.012-.167Q13.371,22.06,9.906,14.091A1.087,1.087,0,0,0,9,13.4c-1.951-.381-3.9-.8-5.843-1.2-.179-.037-.365-.041-.725-.078L16.737,30.32m3.012,3.287L28.4,13.736c-.451-.092-4.082-.136-7.9-.136-4.39,0-9.031.057-9.4.162l8.65,19.845M9.124,0A1.722,1.722,0,0,0,7.6.748c-1.594,2.135-3.226,4.3-4.8,6.388Q1.66,8.642.524,10.149C.186,10.6,0,10.844,0,11.135s.184.527.519.957l0,0c.039.051.08.1.12.15l.1.125,5.035,6.408,12,15.275.184.235c.3.378.6.768.917,1.143a1.249,1.249,0,0,0,.864.568,1.22,1.22,0,0,0,.834-.519c.05-.056.116-.132.18-.213l1.42-1.807,1.944-2.474,4.063-5.165q3.019-3.837,6.036-7.674c1.911-2.431,3.382-4.307,4.769-6.083a5.549,5.549,0,0,0,.344-.515c.053-.086.108-.176.168-.268l.083-.128-.078-.13c-.053-.088-.1-.176-.15-.261a4.639,4.639,0,0,0-.326-.523C38.174,9.1,37.3,7.934,36.452,6.811,34.926,4.785,33.347,2.69,31.8.623A1.575,1.575,0,0,0,30.4.006h0c-3.957,0-7.014.006-9.913.006-4.154,0-7.866,0-11.348-.012Zm.484,1.84c.7-.077,2.894-.135,5.3-.135,1.5,0,2.712.024,3.351.065-1.154,1.616-6.116,7.636-7.479,9.083L10.1,5.644Zm11.563-.068h8.713l-.037.286L28.7,11.009Zm-8.9,10.313c1.178-1.648,6.413-8.1,7.488-9.213,1.189,1.261,6.316,7.6,7.46,9.213ZM2.445,10.539C4.528,7.75,6.359,5.3,8.038,3.063l1.13,8.869-1.792-.371Zm27.9,1.395c.072-.96.2-1.893.33-2.8.078-.558.159-1.134.224-1.7a40.787,40.787,0,0,1,.673-4.228l5.53,7.318Zm-14.074,17.4-5.416-6.887L2.973,12.423c.047.006.091.012.132.021.643.131,1.3.267,1.927.4,1.283.267,2.609.542,3.918.8a.844.844,0,0,1,.731.549c1.656,3.824,3.348,7.708,4.986,11.464l1.6,3.68m6.967-.014q.261-.6.522-1.2.714-1.639,1.429-3.277c1.5-3.432,3.045-6.981,4.545-10.481a1.108,1.108,0,0,1,.958-.753c1.211-.226,2.434-.486,3.618-.737.588-.125,1.2-.254,1.8-.378.125-.026.256-.043.41-.06L28.3,22.876ZM11.455,13.967c1.066-.07,4.749-.122,9.045-.122,3.726,0,6.588.039,7.546.1L21.759,28.381,19.749,33,14.071,19.968Z" fill="currentColor"/>'
			. '</svg>';
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
				'name'           => __( 'Icon Title', 'balefire' ),
				'base'           => self::SHORTCODE,
				'category'       => __( 'Custom Elements', 'balefire' ),
				'description'    => __( 'BMA — Icon and title in a row, with supporting paragraph below.', 'balefire' ),
				'icon'           => 'vc_icon-vc-custom-heading',
				'php_class_name' => 'WPBakeryShortCode_BMA_TitleIcon',
				'params'         => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'value'       => 'Premium Charters',
						'admin_label' => true,
					),
					array(
						'type'        => 'textarea',
						'heading'     => __( 'Paragraph', 'balefire' ),
						'param_name'  => 'text',
						'value'       => 'Philadelphia’s most professional and seamless charter bus experience.',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Heading Tag', 'balefire' ),
						'param_name' => 'tag',
						'value'      => array(
							'H1' => 'h1',
							'H2' => 'h2',
							'H3' => 'h3',
							'H4' => 'h4',
						),
						'std'        => 'h2',
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
						'type'        => 'textfield',
						'heading'     => __( 'Max Width', 'balefire' ),
						'param_name'  => 'max_width',
						'value'       => '598px',
						'description' => __( 'Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Icon Width', 'balefire' ),
						'param_name'  => 'icon_width',
						'value'       => '40px',
						'description' => __( 'Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Icon Height', 'balefire' ),
						'param_name'  => 'icon_height',
						'value'       => '36px',
						'description' => __( 'Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Icon / Title Gap', 'balefire' ),
						'param_name'  => 'gap',
						'value'       => '24px',
						'description' => __( 'Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Title Color', 'balefire' ),
						'param_name' => 'title_color',
						'value'      => '#ffffff',
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Paragraph Color', 'balefire' ),
						'param_name' => 'text_color',
						'value'      => '#e3e8ff',
					),
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Icon Color', 'balefire' ),
						'param_name' => 'icon_color',
						'value'      => '#6f779d',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title Font Size', 'balefire' ),
						'param_name'  => 'title_size',
						'value'       => '55px',
						'description' => __( 'Bare numbers are treated as px.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Paragraph Font Size', 'balefire' ),
						'param_name'  => 'text_size',
						'value'       => '17px',
						'description' => __( 'Bare numbers are treated as px.', 'balefire' ),
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
			'WPBakeryShortCode_BMA_TitleIcon',
			array(
				'title' => 'title',
				'text'  => 'text',
			)
		);
	}

	/**
	 * Sanitize a heading tag.
	 *
	 * @param string $tag Requested tag.
	 * @return string Safe heading tag.
	 */
	private static function sanitizeHeadingTag( string $tag ): string {
		return self::sanitizeChoice( strtolower( trim( $tag ) ), array( 'h1', 'h2', 'h3', 'h4' ), 'h2' );
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
	 * @param string $fallback Fallback length.
	 * @return string Safe CSS length.
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
	 * @param string $fallback Fallback hex color.
	 * @return string Safe hex color.
	 */
	private static function sanitizeHexColor( string $value, string $fallback ): string {
		$value = trim( $value );
		if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}
}
