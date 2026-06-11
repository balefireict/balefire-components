<?php
/**
 * BMA Team Member (Centered) shortcode (parent + child).
 *
 * Parent: [bma_team_grid columns="3" el_id=""] wraps a series of children
 *         and emits the bma-auto-grid container markup (the auto-grid CSS
 *         itself is owned by component-auto-grid, not this package).
 * Child:  [bma_team_member image="" name="" role=""]
 *         Renders one centered team member: circular photo + name + role.
 *
 * `image` = attachment id (preferred) or full URL.
 *
 * Source of truth classes. Global function wrappers (bma_team_grid_shortcode,
 * bma_team_member_shortcode) are defined in bootstrap.php. add_shortcode,
 * vc_map, and the WPBakeryShortCodesContainer subclass are also wired there.
 *
 * Ported from rockerbox inc/shortcodes/bma-team-member-centered.php.
 *
 * @package Balefire\Component\TeamMemberCentered
 */

declare( strict_types=1 );

namespace Balefire\Component\TeamMemberCentered;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the [bma_team_grid] + [bma_team_member] shortcodes.
 *
 * @package Balefire\Component\TeamMemberCentered
 */
final class TeamMemberCentered {

	/**
	 * Render the parent [bma_team_grid] container.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Inner shortcodes (children).
	 * @return string HTML output, or '' when content is empty.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'columns' => '3',
				'el_id'   => '',
			),
			$atts,
			'bma_team_grid'
		);

		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$cols      = (int) ( $atts['columns'] ?: 3 );
		$col_class = match ( $cols ) {
			2       => 'lg:auto-grid-cols-2',
			3       => 'lg:auto-grid-cols-3',
			5       => 'lg:auto-grid-cols-5',
			6       => 'lg:auto-grid-cols-6',
			default => 'lg:auto-grid-cols-4',
		};

		// 3-col founders rows use the larger gap-12; smaller grids use gap-6.
		$gap_class = 3 === $cols ? 'gap-12 auto-grid-gap-12' : 'gap-6 auto-grid-gap-6';

		$id_attr = '' !== trim( (string) $atts['el_id'] )
			? ' id="' . esc_attr( $atts['el_id'] ) . '"'
			: '';

		return '<div class="bma-auto-grid ' . esc_attr( $gap_class )
			. ' auto-grid-cols-1 ' . esc_attr( $col_class ) . '"' . $id_attr . '>'
			. do_shortcode( (string) $content ) . '</div>';
	}

	/**
	 * Render one [bma_team_member] child (circular photo + name + role).
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function renderMember( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'image' => '',
				'name'  => '',
				'role'  => '',
			),
			$atts,
			'bma_team_member'
		);

		$name = trim( (string) $atts['name'] );
		$role = trim( (string) $atts['role'] );

		// Resolve the photo (attachment id or URL) to an <img>.
		$img_html = '';
		if ( '' !== (string) $atts['image'] ) {
			if ( is_numeric( $atts['image'] ) ) {
				$img_html = wp_get_attachment_image(
					(int) $atts['image'],
					'full',
					false,
					array(
						'class' => 'bma-team-member-centered__img',
						'alt'   => $name,
					)
				);
			} else {
				$img_html = '<img class="bma-team-member-centered__img" src="'
					. esc_url( $atts['image'] ) . '" alt="' . esc_attr( $name ) . '" />';
			}
		}

		ob_start();
		?>
		<div class="bma-team-member-centered">
			<?php if ( '' !== $img_html ) : ?>
				<div class="bma-team-member-centered__media">
					<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
			<div class="bma-team-member-centered__content">
				<?php if ( '' !== $name ) : ?>
					<h3 class="bma-team-member-centered__name"><?php echo esc_html( $name ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== $role ) : ?>
					<p class="bma-team-member-centered__role"><?php echo esc_html( $role ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Register both [bma_team_grid] and [bma_team_member] shortcodes.
	 */
	public static function register(): void {
		add_shortcode( 'bma_team_grid', array( self::class, 'render' ) );
		add_shortcode( 'bma_team_member', array( self::class, 'renderMember' ) );
	}

	/**
	 * WPBakery vc_map registration for both parent and child.
	 */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'Team Grid', 'balefire' ),
				'base'                    => 'bma_team_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_TeamMemberCentered',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — Centered team-member grid (circular photos).', 'balefire' ),
				'icon'                    => 'vc_icon-vc-row',
				'as_parent'               => array( 'only' => 'bma_team_member' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Columns (desktop)', 'balefire' ),
						'param_name' => 'columns',
						'value'      => array( '3', '2', '4', '5', '6' ),
						'std'        => '3',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Grid ID (optional)', 'balefire' ),
						'param_name' => 'el_id',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Team Member', 'balefire' ),
				'base'            => 'bma_team_member',
				'php_class_name'  => 'WPBakeryShortCode_BMA_TeamMember',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single centered team member (photo, name, role).', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_team_grid' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Photo', 'balefire' ),
						'param_name' => 'image',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Name', 'balefire' ),
						'param_name' => 'name',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Role', 'balefire' ),
						'param_name' => 'role',
					),
				),
			)
		);
	}

	/**
	 * Register the WPBakery backend-editor classes for the parent container
	 * and the child element. Hooked on vc_after_init so the WPBakery base
	 * classes are loaded.
	 *
	 * When the shared BakeryPreview infra is present it generates
	 * preview-enabled classes (thumbnail + name/role excerpt) for both the
	 * parent container and the child. The Preview class is a soft dependency:
	 * when it is absent the parent container falls back to the plain eval'd
	 * WPBakeryShortCodesContainer subclass (so the editor still recognizes it
	 * as a container), and the child needs no fallback — WPBakery defaults to
	 * the FishBones view when the php_class_name class does not exist.
	 */
	public static function registerPreviewClasses(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}

		if ( class_exists( '\\Balefire\\Component\\BakeryPreview\\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_TeamMemberCentered',
				array()
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_TeamMember',
				array(
					'image' => 'image',
					'title' => 'name',
					'text'  => 'role',
				)
			);
			return;
		}

		// Soft-dep fallback: keep the parent recognized as a container.
		if ( ! class_exists( 'WPBakeryShortCode_BMA_TeamMemberCentered' ) ) {
			eval( 'class WPBakeryShortCode_BMA_TeamMemberCentered extends \\WPBakeryShortCodesContainer {}' );
		}
	}
}
