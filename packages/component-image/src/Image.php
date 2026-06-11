<?php
/**
 * BMA Image shortcode — WPBakery element.
 *
 * Renders a <figure class="bma-image"> wrapping an <img> with object-fit,
 * crop position, aspect ratio, and optional rounded-corner controls. The
 * original rockerbox source expressed these as Tailwind utility classes;
 * this port emits semantic modifier classes (bma-image--cover,
 * bma-image--crop-top, bma-image--aspect-video, bma-image--rounded, …)
 * whose visual behaviour is defined in src/style.css.
 *
 * Usage (shortcode):
 *   [bma_image id="123" fit="object-cover" crop="object-center"
 *             aspect="aspect-video" rounded="true"]
 *
 * Defaults: fit=object-cover, crop=object-center, aspect=aspect-video, rounded=true.
 *
 * Source of truth classes. Global function wrappers (bma_image_shortcode,
 * bma_image_fit_class, bma_image_crop_class, bma_image_aspect_class) are
 * defined in bootstrap.php. add_shortcode and vc_map are wired there too.
 *
 * Ported from rockerbox inc/shortcodes/bma-image-text.php.
 *
 * @package Balefire\Component\Image
 */

declare( strict_types=1 );

namespace Balefire\Component\Image;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer + registration for the [bma_image] shortcode.
 *
 * @package Balefire\Component\Image
 */
final class Image {

	/**
	 * Valid object-fit values accepted by the `fit` param.
	 *
	 * @var string[]
	 */
	public const FIT_CHOICES = array( 'object-cover', 'object-contain', 'object-fill', 'object-none', 'default' );

	/**
	 * Valid object-position values accepted by the `crop` param.
	 *
	 * @var string[]
	 */
	public const CROP_CHOICES = array(
		'object-center', 'object-top-left', 'object-top', 'object-top-right',
		'object-left', 'object-right', 'object-bottom-left', 'object-bottom',
		'object-bottom-right',
	);

	/**
	 * Valid aspect-ratio values accepted by the `aspect` param.
	 *
	 * @var string[]
	 */
	public const ASPECT_CHOICES = array(
		'aspect-auto', 'aspect-square', 'aspect-video',
		'aspect-3/4', 'aspect-4/3', 'aspect-16/9', 'aspect-21/9',
		'default',
	);

	/**
	 * Register the [bma_image] shortcode.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_shortcode( 'bma_image', array( self::class, 'render' ) );
	}

	/**
	 * Validate the `fit` value, falling back to object-cover.
	 *
	 * @param string $value Raw fit value.
	 * @return string Validated value.
	 */
	public static function fitClass( string $value ): string {
		return in_array( $value, self::FIT_CHOICES, true ) ? $value : 'object-cover';
	}

	/**
	 * Validate the `crop` value, falling back to object-center.
	 *
	 * @param string $value Raw crop value.
	 * @return string Validated value.
	 */
	public static function cropClass( string $value ): string {
		return in_array( $value, self::CROP_CHOICES, true ) ? $value : 'object-center';
	}

	/**
	 * Validate the `aspect` value, falling back to aspect-video.
	 *
	 * @param string $value Raw aspect value.
	 * @return string Validated value.
	 */
	public static function aspectClass( string $value ): string {
		return in_array( $value, self::ASPECT_CHOICES, true ) ? $value : 'aspect-video';
	}

	/**
	 * Map a validated object-fit token to a semantic bma-image modifier class.
	 *
	 * @param string $fit Validated fit token.
	 * @return string Modifier class, or '' for default.
	 */
	private static function fitModifier( string $fit ): string {
		$map = array(
			'object-cover'   => 'bma-image--cover',
			'object-contain' => 'bma-image--contain',
			'object-fill'    => 'bma-image--fill',
			'object-none'    => 'bma-image--fit-none',
		);

		return $map[ $fit ] ?? '';
	}

	/**
	 * Map a validated object-position token to a semantic bma-image modifier class.
	 *
	 * @param string $crop Validated crop token.
	 * @return string Modifier class, or '' for center default.
	 */
	private static function cropModifier( string $crop ): string {
		$map = array(
			'object-center'       => '',
			'object-top-left'     => 'bma-image--crop-top-left',
			'object-top'          => 'bma-image--crop-top',
			'object-top-right'    => 'bma-image--crop-top-right',
			'object-left'         => 'bma-image--crop-left',
			'object-right'        => 'bma-image--crop-right',
			'object-bottom-left'  => 'bma-image--crop-bottom-left',
			'object-bottom'       => 'bma-image--crop-bottom',
			'object-bottom-right' => 'bma-image--crop-bottom-right',
		);

		return $map[ $crop ] ?? '';
	}

	/**
	 * Map a validated aspect token to a semantic bma-image modifier class.
	 *
	 * @param string $aspect Validated aspect token.
	 * @return string Modifier class, or '' when no ratio applies.
	 */
	private static function aspectModifier( string $aspect ): string {
		$map = array(
			'aspect-auto'   => 'bma-image--aspect-auto',
			'aspect-square' => 'bma-image--aspect-square',
			'aspect-video'  => 'bma-image--aspect-video',
			'aspect-3/4'    => 'bma-image--aspect-3-4',
			'aspect-4/3'    => 'bma-image--aspect-4-3',
			'aspect-16/9'   => 'bma-image--aspect-16-9',
			'aspect-21/9'   => 'bma-image--aspect-21-9',
		);

		return $map[ $aspect ] ?? '';
	}

	/**
	 * Render the [bma_image] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output, or '' when the image cannot be resolved.
	 */
	public static function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'id'      => '',
				'fit'     => 'object-cover',
				'crop'    => 'object-center',
				'aspect'  => 'aspect-video',
				'rounded' => 'true',
			),
			$atts,
			'bma_image'
		);

		$image_id = (int) $atts['id'];
		if ( ! $image_id ) {
			return '';
		}

		$image_url = wp_get_attachment_image_url( $image_id, 'full' );
		$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		if ( ! $image_url ) {
			return '';
		}

		$fit     = self::fitClass( (string) $atts['fit'] );
		$crop    = self::cropClass( (string) $atts['crop'] );
		$aspect  = self::aspectClass( (string) $atts['aspect'] );
		$rounded = filter_var( $atts['rounded'], FILTER_VALIDATE_BOOLEAN );

		// Figure carries the aspect + rounded modifiers; the img carries fit + crop.
		$figure_classes = array( 'bma-image' );
		if ( 'default' !== $atts['aspect'] ) {
			$aspect_mod = self::aspectModifier( $aspect );
			if ( '' !== $aspect_mod ) {
				$figure_classes[] = $aspect_mod;
			}
		}
		if ( $rounded ) {
			$figure_classes[] = 'bma-image--rounded';
		}

		$img_classes = array( 'bma-image__img' );
		$fit_mod     = self::fitModifier( $fit );
		if ( '' !== $fit_mod ) {
			$img_classes[] = $fit_mod;
		}
		$crop_mod = self::cropModifier( $crop );
		if ( '' !== $crop_mod ) {
			$img_classes[] = $crop_mod;
		}

		return sprintf(
			'<figure class="%s"><img decoding="async" src="%s" alt="%s" class="%s" loading="lazy" /></figure>',
			esc_attr( implode( ' ', $figure_classes ) ),
			esc_url( $image_url ),
			esc_attr( $image_alt ),
			esc_attr( implode( ' ', $img_classes ) )
		);
	}

	/**
	 * Register the WPBakery element mapping.
	 *
	 * @return void
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'        => __( 'BMA Image', 'balefire' ),
				'base'        => 'bma_image',
				'category'    => __( 'BMA Elements', 'balefire' ),
				'description' => __( 'Single image with fit, crop, and aspect ratio controls.', 'balefire' ),
				'icon'        => 'vc_icon-vc-single-image',
				'params'      => array(

					array(
						'type'        => 'attach_image',
						'heading'     => __( 'Image', 'balefire' ),
						'param_name'  => 'id',
						'description' => __( 'Select an image from the media library.', 'balefire' ),
					),

					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Image Fit', 'balefire' ),
						'param_name' => 'fit',
						'value'      => array(
							__( 'Cover (fill, crop overflow)', 'balefire' ) => 'object-cover',
							__( 'Contain (fit inside)', 'balefire' )       => 'object-contain',
							__( 'Fill (stretch)', 'balefire' )             => 'object-fill',
							__( 'None (original size)', 'balefire' )       => 'object-none',
							__( 'Default (no crop)', 'balefire' )          => 'default',
						),
						'std'        => 'object-cover',
					),

					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Crop Position', 'balefire' ),
						'param_name' => 'crop',
						'value'      => array(
							__( 'Center', 'balefire' )       => 'object-center',
							__( 'Top Left', 'balefire' )     => 'object-top-left',
							__( 'Top', 'balefire' )          => 'object-top',
							__( 'Top Right', 'balefire' )    => 'object-top-right',
							__( 'Left', 'balefire' )         => 'object-left',
							__( 'Right', 'balefire' )        => 'object-right',
							__( 'Bottom Left', 'balefire' )  => 'object-bottom-left',
							__( 'Bottom', 'balefire' )       => 'object-bottom',
							__( 'Bottom Right', 'balefire' ) => 'object-bottom-right',
						),
						'std'        => 'object-center',
					),

					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Aspect Ratio', 'balefire' ),
						'param_name' => 'aspect',
						'value'      => array(
							__( 'Video (16/9)', 'balefire' )     => 'aspect-video',
							__( 'Square (1/1)', 'balefire' )     => 'aspect-square',
							__( 'Standard (4/3)', 'balefire' )   => 'aspect-4/3',
							__( 'Portrait (3/4)', 'balefire' )   => 'aspect-3/4',
							__( 'Widescreen (16/9)', 'balefire' ) => 'aspect-16/9',
							__( 'Ultrawide (21/9)', 'balefire' ) => 'aspect-21/9',
							__( 'Auto', 'balefire' )             => 'aspect-auto',
							__( 'None', 'balefire' )             => 'default',
						),
						'std'        => 'aspect-video',
					),

					array(
						'type'       => 'checkbox',
						'heading'    => __( 'Rounded Corners', 'balefire' ),
						'param_name' => 'rounded',
						'value'      => array( __( 'Yes', 'balefire' ) => 'true' ),
						'std'        => 'true',
					),

				),
			)
		);
	}
}
