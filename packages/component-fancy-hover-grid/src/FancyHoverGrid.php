<?php
/**
 * BMA Fancy Hover Grid shortcode (parent grid + child tile).
 *
 * Parent: [bma_fancy_hover_grid] wraps child portrait tiles, or builds tiles
 * automatically from a post loop / ACF relationship field via the `source` att.
 * Child:  [bma_fancy_hover_grid_item image="" title="" text="" link=""]
 *
 * @package Balefire\Component\FancyHoverGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\FancyHoverGrid;

defined( 'ABSPATH' ) || exit;

/**
 * Static renderer for the portrait tile grid.
 */
final class FancyHoverGrid {

	/**
	 * Valid aspect tokens (mirrors the bma-image pattern).
	 *
	 * @var string[]
	 */
	private const ASPECT_CHOICES = array( 'portrait', 'square', '3-4', '4-3', '16-9', '21-9', 'auto' );

	/**
	 * Render the parent grid wrapper.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Child shortcodes (manual source only).
	 * @return string HTML output.
	 */
	public static function render( array $atts, ?string $content = null ): string {
		$atts = self::normalizeAtts( $atts );
		$atts = shortcode_atts(
			array(
				'source'          => 'manual',
				'post_type'       => 'post',
				'taxonomy'        => 'category',
				'max_posts'       => '3',
				'orderby'         => 'date_desc',
				'acf_field'       => '',
				'acf_image_field' => '',
				'acf_title_field' => '',
				'acf_text_field'  => '',
				'aspect'          => 'portrait',
				'overlay_color'   => '#00338f',
				'overlay_opacity' => '0.862',
				'hover_blur'      => '3px',
				'class'           => '',
			),
			$atts,
			'bma_fancy_hover_grid'
		);

		$source = in_array( $atts['source'], array( 'manual', 'posts', 'acf', 'terms' ), true ) ? $atts['source'] : 'manual';
		$aspect = in_array( $atts['aspect'], self::ASPECT_CHOICES, true ) ? $atts['aspect'] : 'portrait';

		if ( 'manual' === $source ) {
			$inner = self::manualInner( $content );
		} else {
			$inner = self::loopInner( $source, $atts );
		}

		if ( '' === trim( (string) $inner ) ) {
			return '';
		}

		$classes = array(
			'bma-fancy-hover-grid',
			'bma-fancy-hover-grid--aspect-' . $aspect,
			'bma-auto-grid',
			'auto-grid-cols-1',
			'md:auto-grid-cols-2',
			'lg:auto-grid-cols-3',
			'auto-grid-gap-6',
		);
		$extra   = trim( (string) $atts['class'] );
		if ( '' !== $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		$style = self::cssVarStyle(
			array(
				'--bma-fancy-hover-grid-overlay-color'   => self::attr( $atts, 'overlay_color' ),
				'--bma-fancy-hover-grid-overlay-opacity' => self::attr( $atts, 'overlay_opacity' ),
				'--bma-fancy-hover-grid-hover-blur'      => self::attr( $atts, 'hover_blur' ),
			)
		);

		return sprintf(
			'<div class="%1$s"%2$s>%3$s</div>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			$style,
			trim( (string) $inner )
		);
	}

	/**
	 * Process manual child shortcode content.
	 *
	 * @param string|null $content Raw child shortcodes.
	 * @return string
	 */
	private static function manualInner( ?string $content ): string {
		if ( null === $content || '' === trim( (string) $content ) ) {
			return '';
		}

		$inner = do_shortcode( shortcode_unautop( trim( (string) $content ) ) );
		$inner = preg_replace( '/^\s*<br\s*\/?>\s*/i', '', (string) $inner );
		$inner = preg_replace( '/<br\s*\/?>\s*(?=<(?:a|div)\s+class="bma-fancy-hover-grid__item\b)/i', '', (string) $inner );
		$inner = preg_replace( '/(<\/(?:a|div)>)\s*<br\s*\/?>/i', '$1', (string) $inner );

		return (string) $inner;
	}

	/**
	 * Build tiles from a post loop or ACF relationship field.
	 *
	 * @param string $source Validated source ('posts'|'acf').
	 * @param array  $atts   Normalized parent atts.
	 * @return string
	 */
	private static function loopInner( string $source, array $atts ): string {
		$max = max( 0, (int) self::attr( $atts, 'max_posts' ) );

		if ( 'terms' === $source ) {
			return self::termsInner( $atts, $max );
		}

		$post_ids = 'acf' === $source
			? self::relationshipPostIds( (string) self::attr( $atts, 'acf_field' ), $max )
			: self::queriedPostIds( (string) self::attr( $atts, 'post_type' ), (string) self::attr( $atts, 'orderby' ), $max );

		if ( empty( $post_ids ) ) {
			return '';
		}

		$field_map = array(
			'image' => 'acf' === $source ? trim( (string) self::attr( $atts, 'acf_image_field' ) ) : '',
			'title' => 'acf' === $source ? trim( (string) self::attr( $atts, 'acf_title_field' ) ) : '',
			'text'  => 'acf' === $source ? trim( (string) self::attr( $atts, 'acf_text_field' ) ) : '',
		);

		$out = '';
		foreach ( $post_ids as $post_id ) {
			$data = self::postTileData( $post_id, $field_map );

			/**
			 * Filter the resolved tile data for a loop/relationship-sourced tile.
			 *
			 * @param array $data    {image, title, text, url} tile data.
			 * @param int   $post_id Source post ID.
			 * @param array $atts    Parent shortcode atts.
			 */
			$data = apply_filters( 'bma_fancy_hover_grid_post_tile', $data, $post_id, $atts );

			$out .= self::tileHtml(
				array(
					'image_html'    => self::imageHtml( $data['image'], (string) $data['title'] ),
					'title'         => (string) $data['title'],
					'text'          => (string) $data['text'],
					'url'           => (string) $data['url'],
					'target'        => '_self',
					'overlay_color' => '',
					'class'         => '',
				)
			);
		}

		return $out;
	}

	/**
	 * Build tiles from a taxonomy's terms. Each tile pulls image/title/text
	 * from the term's ACF Relationship Info fields (group_vmg_relationship_info),
	 * falling back to the term name and a stripped description.
	 *
	 * @param array $atts Normalized parent atts.
	 * @param int   $max  Max terms (0 = all).
	 * @return string
	 */
	private static function termsInner( array $atts, int $max ): string {
		$taxonomy = sanitize_key( (string) self::attr( $atts, 'taxonomy' ) );
		if ( '' === $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return '';
		}

		$order_map = array(
			'date_desc'  => array( 'term_id', 'DESC' ),
			'date_asc'   => array( 'term_id', 'ASC' ),
			'menu_order' => array( 'name', 'ASC' ),
			'title'      => array( 'name', 'ASC' ),
			'rand'       => array( 'name', 'ASC' ),
		);
		$orderby   = (string) self::attr( $atts, 'orderby' );
		$pair      = $order_map[ $orderby ] ?? $order_map['menu_order'];

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => $pair[0],
				'order'      => $pair[1],
				'number'     => $max > 0 ? $max : 0,
			)
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		$out = '';
		foreach ( $terms as $term ) {
			$data = self::termTileData( $term );

			/**
			 * Filter the resolved tile data for a taxonomy-term-sourced tile.
			 *
			 * @param array    $data {image, title, text, url} tile data.
			 * @param \WP_Term $term Source term.
			 * @param array    $atts Parent shortcode atts.
			 */
			$data = apply_filters( 'bma_fancy_hover_grid_term_tile', $data, $term, $atts );

			$out .= self::tileHtml(
				array(
					'image_html'    => self::imageHtml( $data['image'], (string) $data['title'] ),
					'title'         => (string) $data['title'],
					'text'          => (string) $data['text'],
					'url'           => (string) $data['url'],
					'target'        => '_self',
					'overlay_color' => '',
					'class'         => '',
				)
			);
		}

		return $out;
	}

	/**
	 * Resolve img/title/text/url for a taxonomy term from its Relationship Info
	 * ACF fields, with fallbacks to the term name / description / archive link.
	 *
	 * @param \WP_Term $term Source term.
	 * @return array{image:mixed,title:string,text:string,url:string}
	 */
	private static function termTileData( \WP_Term $term ): array {
		$acf      = function_exists( 'get_field' );
		$term_ref = $term->taxonomy . '_' . $term->term_id;

		$image = '';
		if ( $acf ) {
			$image = self::acfImageValue( get_field( 'vmg_relationship_image', $term_ref ) );
		}

		$title = '';
		if ( $acf ) {
			$title = trim( (string) get_field( 'vmg_relationship_title', $term_ref ) );
		}
		if ( '' === $title ) {
			$title = $term->name;
		}

		$text = '';
		if ( $acf ) {
			$text = trim( wp_strip_all_tags( (string) get_field( 'vmg_relationship_content', $term_ref ) ) );
		}
		if ( '' === $text ) {
			$text = trim( wp_strip_all_tags( (string) $term->description ) );
		}
		if ( '' !== $text ) {
			$text = wp_trim_words( $text, 24 );
		}

		$link = get_term_link( $term );

		return array(
			'image' => $image,
			'title' => (string) $title,
			'text'  => $text,
			'url'   => is_wp_error( $link ) ? '' : (string) $link,
		);
	}

	/**
	 * Run a small post query for the loop source.
	 *
	 * @param string $post_type Post type slug.
	 * @param string $orderby   Order token.
	 * @param int    $max       Max posts (0 = default 3).
	 * @return int[]
	 */
	private static function queriedPostIds( string $post_type, string $orderby, int $max ): array {
		$post_type = sanitize_key( $post_type );
		if ( '' === $post_type || ! post_type_exists( $post_type ) ) {
			return array();
		}

		$order_map = array(
			'date_desc'  => array( 'date', 'DESC' ),
			'date_asc'   => array( 'date', 'ASC' ),
			'menu_order' => array( 'menu_order', 'ASC' ),
			'title'      => array( 'title', 'ASC' ),
			'rand'       => array( 'rand', 'DESC' ),
		);
		$pair      = $order_map[ $orderby ] ?? $order_map['date_desc'];

		$query = new \WP_Query(
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => $max > 0 ? $max : 3,
				'orderby'             => $pair[0],
				'order'               => $pair[1],
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
				'fields'              => 'ids',
			)
		);

		return array_map( 'intval', $query->posts );
	}

	/**
	 * Resolve post IDs from an ACF relationship (or post object) field on the current post.
	 *
	 * @param string $field_name ACF field name.
	 * @param int    $max        Max posts (0 = unlimited).
	 * @return int[]
	 */
	private static function relationshipPostIds( string $field_name, int $max ): array {
		$field_name = trim( $field_name );
		if ( '' === $field_name || ! function_exists( 'get_field' ) ) {
			return array();
		}

		$value = get_field( $field_name );
		if ( empty( $value ) ) {
			return array();
		}
		if ( ! is_array( $value ) ) {
			$value = array( $value );
		}

		$ids = array();
		foreach ( $value as $item ) {
			if ( $item instanceof \WP_Post ) {
				$ids[] = (int) $item->ID;
			} elseif ( is_numeric( $item ) ) {
				$ids[] = (int) $item;
			}
		}
		$ids = array_values( array_filter( array_unique( $ids ) ) );

		if ( $max > 0 ) {
			$ids = array_slice( $ids, 0, $max );
		}

		return $ids;
	}

	/**
	 * Resolve img/title/text/url for a sourced post. Optional ACF field names
	 * override the defaults (featured image / post title / excerpt).
	 *
	 * @param int   $post_id   Post ID.
	 * @param array $field_map Optional ACF field names keyed image/title/text.
	 * @return array{image:mixed,title:string,text:string,url:string}
	 */
	private static function postTileData( int $post_id, array $field_map ): array {
		$acf = function_exists( 'get_field' );

		$image = '';
		if ( $acf && '' !== $field_map['image'] ) {
			$image = self::acfImageValue( get_field( $field_map['image'], $post_id ) );
		}
		if ( '' === (string) $image ) {
			$image = (int) get_post_thumbnail_id( $post_id );
			$image = $image > 0 ? $image : '';
		}

		$title = '';
		if ( $acf && '' !== $field_map['title'] ) {
			$title = trim( (string) get_field( $field_map['title'], $post_id ) );
		}
		if ( '' === $title ) {
			$title = get_the_title( $post_id );
		}

		$text = '';
		if ( $acf && '' !== $field_map['text'] ) {
			$text = trim( wp_strip_all_tags( (string) get_field( $field_map['text'], $post_id ) ) );
		}
		if ( '' === $text ) {
			$text = trim( wp_strip_all_tags( (string) get_the_excerpt( $post_id ) ) );
		}
		if ( '' !== $text ) {
			$text = wp_trim_words( $text, 24 );
		}

		return array(
			'image' => $image,
			'title' => (string) $title,
			'text'  => $text,
			'url'   => (string) get_permalink( $post_id ),
		);
	}

	/**
	 * Normalize an ACF image field value (array / ID / URL) to an ID or URL string.
	 *
	 * @param mixed $value Raw ACF value.
	 * @return string Attachment ID (numeric string) or URL, or ''.
	 */
	private static function acfImageValue( $value ): string {
		if ( is_array( $value ) ) {
			if ( ! empty( $value['ID'] ) ) {
				return (string) (int) $value['ID'];
			}
			if ( ! empty( $value['url'] ) ) {
				return (string) $value['url'];
			}
			return '';
		}
		if ( is_numeric( $value ) && (int) $value > 0 ) {
			return (string) (int) $value;
		}
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			return trim( $value );
		}

		return '';
	}

	/**
	 * Render one child tile (manual source).
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Optional body content (unused by default).
	 * @return string HTML output.
	 */
	public static function renderItem( array $atts, ?string $content = null ): string {
		$atts = self::normalizeAtts( $atts );
		$atts = shortcode_atts(
			array(
				'image'         => '',
				'title'         => '',
				'text'          => '',
				'link'          => '',
				'overlay_color' => '',
				'class'         => '',
			),
			$atts,
			'bma_fancy_hover_grid_item'
		);

		$title = trim( (string) self::attr( $atts, 'title' ) );
		$link  = self::parseLink( (string) self::attr( $atts, 'link' ) );

		if ( '' === $title && '' === trim( (string) self::attr( $atts, 'image' ) ) ) {
			return '';
		}

		return self::tileHtml(
			array(
				'image_html'    => self::imageHtml( self::attr( $atts, 'image' ), $title ),
				'title'         => $title,
				'text'          => trim( (string) self::attr( $atts, 'text' ) ),
				'url'           => $link['url'],
				'target'        => $link['target'],
				'overlay_color' => (string) self::attr( $atts, 'overlay_color' ),
				'class'         => trim( (string) self::attr( $atts, 'class' ) ),
			)
		);
	}

	/**
	 * Shared tile markup for manual and sourced tiles.
	 *
	 * @param array $tile {image_html, title, text, url, target, overlay_color, class}.
	 * @return string
	 */
	private static function tileHtml( array $tile ): string {
		$url   = esc_url( (string) $tile['url'] );
		$title = (string) $tile['title'];
		$text  = (string) $tile['text'];

		$classes = array( 'bma-fancy-hover-grid__item' );
		if ( '' !== (string) $tile['class'] ) {
			$classes[] = sanitize_html_class( (string) $tile['class'] );
		}

		$style_vars = array();
		if ( '' !== trim( (string) $tile['overlay_color'] ) ) {
			$style_vars['--bma-fancy-hover-grid-overlay-color'] = (string) $tile['overlay_color'];
		}
		$style = self::cssVarStyle( $style_vars );

		$inner  = '<span class="bma-fancy-hover-grid__media">' . $tile['image_html'] . '</span>';
		$inner .= '<span class="bma-fancy-hover-grid__wash" aria-hidden="true"></span>';
		$inner .= '<span class="bma-fancy-hover-grid__color" aria-hidden="true"></span>';
		$inner .= '<span class="bma-fancy-hover-grid__shade" aria-hidden="true"></span>';
		if ( '' !== $title ) {
			$inner .= '<h3 class="bma-fancy-hover-grid__title">' . esc_html( $title ) . '</h3>';
		}
		if ( '' !== $text ) {
			$inner .= '<p class="bma-fancy-hover-grid__text">' . esc_html( $text ) . '</p>';
		}

		if ( '' !== $url ) {
			$target = in_array( $tile['target'], array( '_blank', '_self' ), true ) ? $tile['target'] : '_self';
			$rel    = '_blank' === $target ? ' rel="noopener noreferrer"' : '';
			return sprintf(
				'<a class="%1$s" href="%2$s" target="%3$s"%4$s%5$s>%6$s</a>',
				esc_attr( implode( ' ', array_unique( $classes ) ) ),
				$url,
				esc_attr( $target ),
				$rel,
				$style,
				$inner
			);
		}

		return sprintf(
			'<div class="%1$s"%2$s>%3$s</div>',
			esc_attr( implode( ' ', array_unique( $classes ) ) ),
			$style,
			$inner
		);
	}

	/** Register shortcodes, including manual typo/hyphen aliases. */
	public static function register(): void {
		add_shortcode( 'bma_fancy_hover_grid', 'bma_fancy_hover_grid_shortcode' );
		add_shortcode( 'bma_fancy_hover_grid_item', 'bma_fancy_hover_grid_item_shortcode' );
		add_shortcode( 'bma_protrait_grid', 'bma_fancy_hover_grid_shortcode' );
		add_shortcode( 'bma_protrait_grid_item', 'bma_fancy_hover_grid_item_shortcode' );
		add_shortcode( 'bma-fancy-hover-grid', 'bma_fancy_hover_grid_shortcode' );
		add_shortcode( 'bma-fancy-hover-grid-item', 'bma_fancy_hover_grid_item_shortcode' );
		add_shortcode( 'bma-protrait-grid', 'bma_fancy_hover_grid_shortcode' );
		add_shortcode( 'bma-protrait-grid-item', 'bma_fancy_hover_grid_item_shortcode' );
	}

	/** Register WPBakery elements. */
	public static function vcMap(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$post_type_options = array();
		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $slug => $object ) {
			if ( 'attachment' === $slug ) {
				continue;
			}
			$post_type_options[ $object->labels->name . ' (' . $slug . ')' ] = $slug;
		}

		$taxonomy_options = array();
		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $slug => $object ) {
			$taxonomy_options[ $object->labels->name . ' (' . $slug . ')' ] = $slug;
		}

		vc_map(
			array(
				'name'                    => __( 'Fancy Hover Grid', 'balefire' ),
				'base'                    => 'bma_fancy_hover_grid',
				'php_class_name'          => 'WPBakeryShortCode_BMA_FancyHoverGrid',
				'category'                => __( 'Custom Elements', 'balefire' ),
				'description'             => __( 'BMA — 3-column portrait image grid with color overlay hover.', 'balefire' ),
				'icon'                    => 'vc_icon-vc-media-grid',
				'as_parent'               => array( 'only' => 'bma_fancy_hover_grid_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Title', 'balefire' ),
						'param_name'  => 'title',
						'description' => __( 'Label shown in the page builder only — not rendered on the front end.', 'balefire' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Content source', 'balefire' ),
						'param_name'  => 'source',
						'std'         => 'manual',
						'admin_label' => true,
						'value'       => array(
							__( 'Manual tiles', 'balefire' )     => 'manual',
							__( 'Post loop', 'balefire' )        => 'posts',
							__( 'ACF relationship', 'balefire' ) => 'acf',
							__( 'Taxonomy terms', 'balefire' )   => 'terms',
						),
						'description' => __( 'Manual = add Fancy Hover Tile elements inside this grid. Post loop / ACF relationship / Taxonomy terms build the tiles automatically.', 'balefire' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Post type', 'balefire' ),
						'param_name'  => 'post_type',
						'std'         => 'post',
						'admin_label' => true,
						'value'       => $post_type_options,
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'posts' ),
						),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Taxonomy', 'balefire' ),
						'param_name'  => 'taxonomy',
						'std'         => 'category',
						'admin_label' => true,
						'value'       => $taxonomy_options,
						'description' => __( 'Each term becomes a tile. Image, title, and text come from the term\'s Relationship Info fields, falling back to the term name and description.', 'balefire' ),
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'terms' ),
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Order', 'balefire' ),
						'param_name' => 'orderby',
						'std'        => 'date_desc',
						'value'      => array(
							__( 'Newest first', 'balefire' ) => 'date_desc',
							__( 'Oldest first', 'balefire' ) => 'date_asc',
							__( 'Menu order', 'balefire' )   => 'menu_order',
							__( 'Title A–Z', 'balefire' )    => 'title',
							__( 'Random', 'balefire' )       => 'rand',
						),
						'dependency' => array(
							'element' => 'source',
							'value'   => array( 'posts', 'terms' ),
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'ACF relationship field name', 'balefire' ),
						'param_name'  => 'acf_field',
						'admin_label' => true,
						'description' => __( 'Field name of an ACF Relationship (or Post Object) field on the current page/post.', 'balefire' ),
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'acf' ),
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Max posts', 'balefire' ),
						'param_name'  => 'max_posts',
						'value'       => '3',
						'description' => __( 'Maximum tiles to show. 0 = no limit for ACF relationships / taxonomy terms.', 'balefire' ),
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'posts', 'acf', 'terms' ),
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'ACF image field (optional)', 'balefire' ),
						'param_name'  => 'acf_image_field',
						'description' => __( 'Field name on the related post. Blank = featured image.', 'balefire' ),
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'acf' ),
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'ACF title field (optional)', 'balefire' ),
						'param_name'  => 'acf_title_field',
						'description' => __( 'Field name on the related post. Blank = post title.', 'balefire' ),
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'acf' ),
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'ACF text field (optional)', 'balefire' ),
						'param_name'  => 'acf_text_field',
						'description' => __( 'Field name on the related post. Blank = excerpt.', 'balefire' ),
						'dependency'  => array(
							'element' => 'source',
							'value'   => array( 'acf' ),
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Aspect ratio', 'balefire' ),
						'param_name' => 'aspect',
						'std'        => 'portrait',
						'value'      => array(
							__( 'Portrait (368:467)', 'balefire' ) => 'portrait',
							__( 'Square (1:1)', 'balefire' )       => 'square',
							__( 'Portrait (3:4)', 'balefire' )     => '3-4',
							__( 'Landscape (4:3)', 'balefire' )    => '4-3',
							__( 'Video (16:9)', 'balefire' )       => '16-9',
							__( 'Ultrawide (21:9)', 'balefire' )   => '21-9',
							__( 'Auto (natural image)', 'balefire' ) => 'auto',
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay color', 'balefire' ),
						'param_name'  => 'overlay_color',
						'value'       => '#00338f',
						'description' => __( 'Default matches the David Tours blue overlay in the portrait grid reference.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay opacity', 'balefire' ),
						'param_name'  => 'overlay_opacity',
						'value'       => '0.862',
						'description' => __( 'Use a decimal from 0 to 1.', 'balefire' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover backdrop blur', 'balefire' ),
						'param_name'  => 'hover_blur',
						'value'       => '3px',
						'description' => __( 'Blur behind the color overlay on hover, in px or rem (e.g. 3px, 0.25rem). Set 0 to disable.', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra class', 'balefire' ),
						'param_name' => 'class',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Fancy Hover Tile', 'balefire' ),
				'base'            => 'bma_fancy_hover_grid_item',
				'php_class_name'  => 'WPBakeryShortCode_BMA_FancyHoverGridItem',
				'category'        => __( 'Custom Elements', 'balefire' ),
				'description'     => __( 'BMA — A single portrait image tile with title, text, and link.', 'balefire' ),
				'icon'            => 'vc_icon-vc-single-image',
				'as_child'        => array( 'only' => 'bma_fancy_hover_grid' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'attach_image',
						'heading'    => __( 'Image', 'balefire' ),
						'param_name' => 'image',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire' ),
						'param_name' => 'title',
					),
					array(
						'type'        => 'textarea',
						'heading'     => __( 'Text', 'balefire' ),
						'param_name'  => 'text',
						'description' => __( 'Optional. Short line revealed on hover beneath the title.', 'balefire' ),
					),
					array(
						'type'       => 'vc_link',
						'heading'    => __( 'Link', 'balefire' ),
						'param_name' => 'link',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Hover overlay color override', 'balefire' ),
						'param_name'  => 'overlay_color',
						'description' => __( 'Optional. Leave blank to inherit the parent grid color.', 'balefire' ),
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra class', 'balefire' ),
						'param_name' => 'class',
					),
				),
			)
		);
	}

	/** Register WPBakery preview/editor classes. */
	public static function registerPreviewClasses(): void {
		if ( class_exists( '\Balefire\Component\BakeryPreview\Preview' ) ) {
			\Balefire\Component\BakeryPreview\Preview::registerContainerClass(
				'WPBakeryShortCode_BMA_FancyHoverGrid',
				array(
					'title' => 'title',
				)
			);
			\Balefire\Component\BakeryPreview\Preview::registerElementClass(
				'WPBakeryShortCode_BMA_FancyHoverGridItem',
				array(
					'image' => 'image',
					'title' => 'title',
					'text'  => 'text',
				)
			);
			return;
		}

		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_FancyHoverGrid' ) ) {
			eval( 'class WPBakeryShortCode_BMA_FancyHoverGrid extends \\WPBakeryShortCodesContainer {}' );
		}
	}

	/**
	 * Read a value that may have been written by WPBakery with underscores converted to hyphens.
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

	/**
	 * Normalize WPBakery hyphenated shortcode attributes back to canonical snake_case.
	 *
	 * @param array $atts Raw shortcode attributes.
	 * @return array
	 */
	private static function normalizeAtts( array $atts ): array {
		$names = array(
			'overlay_color',
			'overlay_opacity',
			'hover_blur',
			'post_type',
			'taxonomy',
			'max_posts',
			'acf_field',
			'acf_image_field',
			'acf_title_field',
			'acf_text_field',
		);
		foreach ( $names as $name ) {
			$hyphen = str_replace( '_', '-', $name );
			if ( ! isset( $atts[ $name ] ) && isset( $atts[ $hyphen ] ) ) {
				$atts[ $name ] = $atts[ $hyphen ];
			}
		}

		return $atts;
	}

	/**
	 * Parse a WPBakery vc_link attribute into URL and target pieces.
	 *
	 * @param string $raw Raw vc_link value.
	 * @return array{url:string,target:string}
	 */
	private static function parseLink( string $raw ): array {
		$result = array(
			'url'    => '',
			'target' => '',
		);

		if ( '' === trim( $raw ) ) {
			return $result;
		}

		if ( function_exists( 'vc_build_link' ) ) {
			$link = vc_build_link( $raw );
			if ( is_array( $link ) ) {
				$result['url']    = isset( $link['url'] ) ? trim( (string) $link['url'] ) : '';
				$result['target'] = isset( $link['target'] ) ? trim( (string) $link['target'] ) : '';
				return $result;
			}
		}

		parse_str( html_entity_decode( $raw ), $parts );
		$result['url']    = isset( $parts['url'] ) ? trim( (string) $parts['url'] ) : '';
		$result['target'] = isset( $parts['target'] ) ? trim( (string) $parts['target'] ) : '';

		return $result;
	}

	/**
	 * Resolve the image HTML for an attachment ID or URL.
	 *
	 * @param mixed  $image Attachment ID or URL.
	 * @param string $title Fallback alt/title context.
	 * @return string
	 */
	private static function imageHtml( $image, string $title ): string {
		$image = trim( (string) $image );
		if ( '' === $image ) {
			return '';
		}

		if ( is_numeric( $image ) ) {
			$image_id = (int) $image;
			$alt      = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( '' === trim( $alt ) ) {
				$alt = $title;
			}

			$html = wp_get_attachment_image(
				$image_id,
				'large',
				false,
				array(
					'class'    => 'bma-fancy-hover-grid__img',
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);

			return $html ? $html : '';
		}

		return '<img class="bma-fancy-hover-grid__img" src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" decoding="async" />';
	}

	/**
	 * Build inline CSS custom property declarations for validated colors/numbers.
	 *
	 * @param array<string,mixed> $vars CSS variable map.
	 * @return string Attribute string, including leading space, or ''.
	 */
	private static function cssVarStyle( array $vars ): string {
		$decls = array();
		foreach ( $vars as $name => $value ) {
			$value = (string) $value;
			if ( str_ends_with( $name, '-opacity' ) ) {
				$opacity = self::sanitizeOpacity( $value );
				if ( '' !== $opacity ) {
					$decls[] = $name . ':' . $opacity;
				}
				continue;
			}

			if ( str_ends_with( $name, '-blur' ) ) {
				$length = self::sanitizeCssLength( $value );
				if ( '' !== $length ) {
					$decls[] = $name . ':' . $length;
				}
				continue;
			}

			$color = self::sanitizeCssColor( $value );
			if ( '' !== $color ) {
				$decls[] = $name . ':' . $color;
			}
		}

		if ( empty( $decls ) ) {
			return '';
		}

		return ' style="' . esc_attr( implode( ';', $decls ) ) . '"';
	}

	/**
	 * Sanitize decimal opacity values.
	 *
	 * @param string $value Raw opacity.
	 * @return string Safe opacity or ''.
	 */
	private static function sanitizeOpacity( string $value ): string {
		$value = trim( $value );
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$opacity = (float) $value;
		if ( $opacity < 0 || $opacity > 1 ) {
			return '';
		}

		return rtrim( rtrim( sprintf( '%.3F', $opacity ), '0' ), '.' );
	}

	/**
	 * Sanitize a CSS length (px/rem/em). Bare numbers are treated as px.
	 *
	 * @param string $value Raw length.
	 * @return string Safe length or ''.
	 */
	private static function sanitizeCssLength( string $value ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		if ( is_numeric( $value ) ) {
			return $value . 'px';
		}
		if ( preg_match( '/^[0-9]*\.?[0-9]+(px|rem|em)$/', $value ) ) {
			return $value;
		}
		return '';
	}

	/**
	 * Conservative CSS color sanitizer for hex/rgb(a)/hsl(a)/named currentColor.
	 *
	 * @param string $value Raw color.
	 * @return string Safe color or ''.
	 */
	private static function sanitizeCssColor( string $value ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $value ) ) {
			return $value;
		}
		if ( preg_match( '/^rgba?\(\s*[0-9.]+%?\s*,\s*[0-9.]+%?\s*,\s*[0-9.]+%?(?:\s*,\s*(?:0|1|0?\.[0-9]+))?\s*\)$/i', $value ) ) {
			return $value;
		}
		if ( preg_match( '/^hsla?\(\s*[0-9.]+(?:deg)?\s*,\s*[0-9.]+%\s*,\s*[0-9.]+%(?:\s*,\s*(?:0|1|0?\.[0-9]+))?\s*\)$/i', $value ) ) {
			return $value;
		}
		if ( 'currentColor' === $value ) {
			return $value;
		}
		return '';
	}
}
