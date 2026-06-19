<?php
/**
 * BMA Gallery Grid shortcode.
 *
 * [bma_gallery_grid field="acffg_gallery_grid" columns="3" size="medium"]
 *
 * Reads an ACF Gallery field from the current post and renders a CSS-grid of
 * thumbnails. By default each thumbnail opens full-size in fslightbox; setting
 * the acffg_gallery_grid_disable_lightbox ACF toggle (or disable_lightbox="1"
 * shortcode attr) renders plain images and skips the fslightbox enqueue
 * entirely — for small source images (e.g. 640x480 fleet photos).
 *
 * @package Balefire\Component\GalleryGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\GalleryGrid;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the ACF-driven gallery grid + lightbox.
 */
final class GalleryGrid {

	/**
	 * Register the shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'bma_gallery_grid', 'bma_gallery_grid_shortcode' );
	}

	/**
	 * Resolve the web URL to the bundled fslightbox.min.js.
	 *
	 * Uses content_url() so the path resolves to the Composer vendor dir
	 * (wp-content/vendor/balefireict/component-gallery-grid/src/assets/),
	 * which is web-accessible both for symlinked local dev and committed-vendor
	 * prod deploys.
	 *
	 * @return string
	 */
	public static function fslightboxUrl(): string {
		// set_url_scheme() matches the page protocol so an https page never
		// loads an http script (mixed-content block).
		return set_url_scheme( content_url( '/vendor/balefireict/component-gallery-grid/src/assets/fslightbox.min.js' ) );
	}

	/**
	 * Render the gallery grid.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output (empty string if no gallery / ACF absent).
	 */
	public static function render( array $atts ): string {
		// Soft-dep on ACF — render nothing if the plugin is absent.
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'field'            => 'acffg_gallery_grid',
				'columns'          => '3',
				'size'             => 'medium',
				'post_id'          => '',
				'disable_lightbox' => '',
				'class'            => '',
			),
			$atts,
			'bma_gallery_grid'
		);

		$field   = trim( (string) self::attr( $atts, 'field' ) );
		$post_id = trim( (string) self::attr( $atts, 'post_id' ) );
		$post_id = '' !== $post_id ? (int) $post_id : (int) get_the_ID();

		$gallery = get_field( $field, $post_id );
		if ( empty( $gallery ) || ! is_array( $gallery ) ) {
			return '';
		}

		// Normalize ACF gallery items to [full_url, thumb_url, alt].
		$images = array();
		foreach ( $gallery as $item ) {
			$src = self::imageSources( $item, trim( (string) self::attr( $atts, 'size' ) ) );
			if ( '' !== $src[0] ) {
				$images[] = $src;
			}
		}

		if ( empty( $images ) ) {
			return '';
		}

		// Lightbox: an explicit shortcode attr wins; otherwise read the ACF
		// toggle (acffg_gallery_grid_disable_lightbox) on the current post.
		// When disabled (e.g. small 640x480 fleet images), render plain images
		// and skip the fslightbox enqueue entirely.
		$dl_attr          = strtolower( trim( (string) self::attr( $atts, 'disable_lightbox' ) ) );
		$disable_lightbox = '' !== $dl_attr
			? in_array( $dl_attr, array( '1', 'true', 'yes', 'on' ), true )
			: (bool) get_field( 'acffg_gallery_grid_disable_lightbox', $post_id );

		// Enqueue fslightbox only when the lightbox is enabled.
		if ( ! $disable_lightbox ) {
			wp_enqueue_script( 'bma-fslightbox' );
		}

		// Unique lightbox group per shortcode instance so galleries don't merge.
		static $instance = 0;
		++$instance;
		$group = 'gallery-grid-' . $post_id . '-' . $instance;

		// Column count (default 3) drives the CSS grid via a custom property.
		$columns = max( 1, min( 8, (int) self::attr( $atts, 'columns' ) ?: 3 ) );

		$class = 'gallery-grid';
		if ( $disable_lightbox ) {
			$class .= ' gallery-grid--no-lightbox';
		}
		$extra = trim( (string) self::attr( $atts, 'class' ) );
		if ( '' !== $extra ) {
			$class .= ' ' . sanitize_html_class( $extra );
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $class ); ?>" style="--gallery-grid-cols:<?php echo (int) $columns; ?>">
			<?php foreach ( $images as list( $full, $thumb, $alt ) ) : ?>
				<?php if ( $disable_lightbox ) : ?>
					<figure class="gallery-grid__item">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
					</figure>
				<?php else : ?>
					<a class="gallery-grid__item" href="<?php echo esc_url( $full ); ?>" data-fslightbox="<?php echo esc_attr( $group ); ?>">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Resolve [full_url, thumb_url, alt] from an ACF gallery item.
	 *
	 * Handles all ACF gallery return formats: image array, attachment ID.
	 *
	 * @param mixed  $item ACF gallery item.
	 * @param string $size Thumbnail size name (e.g. 'medium', 'thumbnail').
	 * @return array{0:string,1:string,2:string}
	 */
	private static function imageSources( $item, string $size ): array {
		$size = '' !== $size ? $size : 'medium';

		// Image array (ACF return format "Image Array").
		if ( is_array( $item ) ) {
			$full  = isset( $item['url'] ) ? (string) $item['url'] : '';
			$thumb = isset( $item['sizes'][ $size ] ) ? (string) $item['sizes'][ $size ] : $full;
			$alt   = isset( $item['alt'] ) ? (string) $item['alt'] : '';
			return array( $full, $thumb, $alt );
		}

		// Attachment ID (ACF return format "ID").
		if ( is_numeric( $item ) ) {
			$id    = (int) $item;
			$full  = (string) wp_get_attachment_image_url( $id, 'full' );
			$thumb = (string) ( wp_get_attachment_image_url( $id, $size ) ?: $full );
			$alt   = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
			return array( $full, $thumb, $alt );
		}

		return array( '', '', '' );
	}

	/**
	 * Register the WPBakery element.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'        => __( 'Gallery Grid', 'balefire' ),
				'base'        => 'bma_gallery_grid',
				'category'    => __( 'Custom Elements', 'balefire' ),
				'description' => __( 'BMA — ACF image gallery grid with fslightbox lightbox. Reads the acffg_gallery_grid field.', 'balefire' ),
				'icon'        => 'vc_icon-vc-media-grid',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'ACF field name', 'balefire' ),
						'param_name'  => 'field',
						'value'       => 'acffg_gallery_grid',
						'admin_label' => true,
						'description' => __( 'Name of the ACF Gallery field to read from the current post. Defaults to acffg_gallery_grid.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Columns', 'balefire' ),
						'param_name'  => 'columns',
						'value'       => '3',
						'description' => __( 'Number of columns (1-8). Default 3.', 'balefire' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Thumbnail size', 'balefire' ),
						'param_name'  => 'size',
						'value'       => array(
							'Medium'   => 'medium',
							'Thumbnail' => 'thumbnail',
							'Large'    => 'large',
							'Full'     => 'full',
						),
						'std'         => 'medium',
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Disable lightbox', 'balefire' ),
						'param_name'  => 'disable_lightbox',
						'value'       => array(
							__( 'Auto (use page setting)', 'balefire' ) => '',
							__( 'No', 'balefire' )  => 'false',
							__( 'Yes', 'balefire' ) => 'true',
						),
						'std'         => '',
						'admin_label' => true,
						'description' => __( 'Skip the lightbox for this element. Auto falls back to the page\'s Disable Lightbox toggle (acffg_gallery_grid_disable_lightbox); Yes forces it off (no lightbox script); No forces it on. Hand-writing the shortcode also works: [bma_gallery_grid disable_lightbox="true"].', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class', 'balefire' ),
						'param_name'  => 'class',
					),
				),
			)
		);
	}

	/**
	 * Read a value that may have been written by WPBakery with underscores
	 * converted to hyphens.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $name Canonical snake_case param name.
	 * @return string
	 */
	private static function attr( array $atts, string $name ): string {
		$hyphen = str_replace( '_', '-', $name );
		foreach ( array( $name, $hyphen ) as $key ) {
			if ( isset( $atts[ $key ] ) && '' !== (string) $atts[ $key ] ) {
				return (string) $atts[ $key ];
			}
		}

		return '';
	}
}
