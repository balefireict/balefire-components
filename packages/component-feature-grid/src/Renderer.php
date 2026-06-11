<?php
/**
 * Renderer for the [bma_feature_grid] parent + [bma_feature_card] child shortcodes.
 *
 * Attribute-driven WPBakery container. No ACF reads — all content comes from
 * shortcode attributes and the child shortcode body ($content).
 *
 * @package Balefire\Component\FeatureGrid
 */

declare( strict_types=1 );

namespace Balefire\Component\FeatureGrid;

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

final class Renderer {

    /** @var array<int,string> */
    public const COLUMN_CHOICES = [ '2', '3', '4' ];

    public const DEFAULT_COLUMNS = '3';

    /**
     * Parent shortcode defaults.
     *
     * @return array<string,string>
     */
    private static function parent_defaults(): array {
        return [
            'eyebrow'  => '',
            'headline' => '',
            'subhead'  => '',
            'columns'  => self::DEFAULT_COLUMNS,
            'el_id'    => '',
            'el_class' => '',
        ];
    }

    /**
     * Child shortcode defaults.
     *
     * @return array<string,string>
     */
    private static function card_defaults(): array {
        return [
            'icon'  => '',
            'title' => '',
        ];
    }

    /**
     * Render the parent [bma_feature_grid] shortcode.
     *
     * @param array<string,string>|string $atts    Shortcode attributes.
     * @param string|null                  $content Inner child shortcodes.
     * @return string HTML output, or '' when there are no children.
     */
    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::parent_defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_feature_grid'
        );

        if ( null === $content || '' === trim( (string) $content ) ) {
            return '';
        }

        $inner = \do_shortcode( \shortcode_unautop( trim( (string) $content ) ) );
        $inner = (string) preg_replace( '/^\s*(?:<br\s*\/?>\s*)+/i', '', (string) $inner );
        $inner = (string) preg_replace( '/(?:<br\s*\/?>\s*)+$/i', '', (string) $inner );
        $inner = (string) preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', $inner );
        $inner = trim( (string) $inner );

        if ( '' === $inner ) {
            return '';
        }

        $columns = (string) $atts['columns'];
        if ( ! in_array( $columns, self::COLUMN_CHOICES, true ) ) {
            $columns = self::DEFAULT_COLUMNS;
        }

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'eyebrow'  => (string) $atts['eyebrow'],
            'headline' => (string) $atts['headline'],
            'subhead'  => (string) $atts['subhead'],
            'columns'  => $columns,
            'inner'    => $inner,
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    /**
     * Render one [bma_feature_card] child.
     *
     * @param array<string,string>|string $atts    Shortcode attributes.
     * @param string|null                  $content Body HTML (textarea_html).
     * @return string HTML output, or '' when the card has no content.
     */
    public static function render_card( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::card_defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_feature_card'
        );

        $title    = trim( (string) $atts['title'] );
        $icon_url = self::resolve_image_url( $atts['icon'] );

        $body = (string) ( $content ?? '' );
        $body = trim( (string) \do_shortcode( \shortcode_unautop( $body ) ) );
        $body = (string) preg_replace( '/<p>(?:\s|&nbsp;)*<\/p>/i', '', $body );
        $body = trim( (string) \wp_kses_post( $body ) );

        if ( '' === $title && '' === $icon_url && '' === $body ) {
            return '';
        }

        ob_start();
        include __DIR__ . '/card-template.php';
        return trim( (string) ob_get_clean() );
    }

    /**
     * Resolve an image attribute (attachment id or url) to a URL.
     *
     * @param mixed $icon Attachment id, url, or array with a 'url' key.
     * @return string URL, or '' when it cannot be resolved.
     */
    private static function resolve_image_url( $icon ): string {
        if ( is_array( $icon ) ) {
            $icon = $icon['url'] ?? '';
        }
        $icon = trim( (string) $icon );
        if ( '' === $icon ) {
            return '';
        }
        if ( is_numeric( $icon ) ) {
            $url = \wp_get_attachment_image_url( (int) $icon, 'full' );
            return $url ? (string) $url : '';
        }
        return $icon;
    }

    /**
     * Build the root wrapper attributes for the parent element.
     *
     * @param array<string,string> $atts Resolved parent attributes.
     * @return array<string,string>
     */
    private static function build_wrapper_atts( array $atts ): array {
        $classes = [ 'bma-c-feature-grid' ];

        $extra = trim( (string) $atts['el_class'] );
        if ( '' !== $extra ) {
            $classes[] = $extra;
        }

        $wrapper = [
            'class' => implode( ' ', array_unique( $classes ) ),
        ];

        $id = trim( (string) $atts['el_id'] );
        if ( '' !== $id ) {
            $wrapper['id'] = $id;
        }

        return $wrapper;
    }

    /**
     * Convert an attribute map to an escaped HTML attribute string.
     *
     * @param array<string,string> $atts Attribute map.
     * @return string
     */
    public static function attrs_to_html( array $atts ): string {
        $parts = [];
        foreach ( $atts as $key => $value ) {
            if ( '' === $value || null === $value ) {
                continue;
            }
            $parts[] = sprintf(
                '%s="%s"',
                \esc_attr( (string) $key ),
                \esc_attr( (string) $value )
            );
        }
        return implode( ' ', $parts );
    }
}
