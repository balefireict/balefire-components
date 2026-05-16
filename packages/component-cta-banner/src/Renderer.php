<?php
/**
 * Renderer for [bma_cta_banner] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\CtaBanner;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'align'          => 'center',
            'variant'        => 'gradient',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => '',
            'class'          => '',
        ];
    }

    /**
     * @param array<string,string>|string $atts
     */
    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_cta_banner'
        );

        $post_id = \get_the_ID() ?: 0;

        $headline = \get_field( 'bma_cta_banner_headline', $post_id ) ?: '';
        $subhead  = \get_field( 'bma_cta_banner_subhead', $post_id ) ?: '';
        $cta      = \get_field( 'bma_cta_banner_cta', $post_id ) ?: null;
        $variant  = \get_field( 'bma_cta_banner_variant', $post_id ) ?: $atts['variant'];

        $wrapper_atts = self::build_wrapper_atts( $atts, $variant );

        $args = [
            'headline' => $headline,
            'subhead'  => $subhead,
            'cta'      => $cta,
            'variant'  => $variant,
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    /**
     * @param array<string,string> $atts
     * @return array<string,string>
     */
    private static function build_wrapper_atts( array $atts, string $variant ): array {
        $classes = [ 'bma-c-cta-banner', "bma-c-cta-banner--{$variant}" ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-cta-banner--{$key}-{$value}";
            }
        }
        foreach ( [ 'spacing_top' => 'pt', 'spacing_bottom' => 'pb' ] as $key => $abbr ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9]+$/i', $value ) ) {
                $classes[] = "bma-c--{$abbr}-{$value}";
            }
        }

        $extra = trim( (string) $atts['class'] );
        if ( $extra !== '' ) {
            $classes[] = $extra;
        }

        $wrapper = [
            'class' => implode( ' ', array_unique( $classes ) ),
        ];

        $id = trim( (string) $atts['id'] );
        if ( $id !== '' ) {
            $wrapper['id'] = $id;
        }

        return (array) \apply_filters( 'bma_c_cta_banner/wrapper_atts', $wrapper, $atts );
    }

    /**
     * @param array<string,mixed>|null $cta
     */
    public static function render_cta( ?array $cta ): string {
        if ( empty( $cta ) || empty( $cta['url'] ) ) {
            return '';
        }

        $label   = ! empty( $cta['title'] ) ? $cta['title'] : __( 'Learn more', 'balefire-components' );
        $url     = $cta['url'];
        $target  = ! empty( $cta['target'] ) ? $cta['target'] : '_self';
        $classes = [ 'btn', 'btn-white' ];

        if ( ! empty( $cta['class'] ) ) {
            $classes[] = $cta['class'];
        }

        return sprintf(
            '<a href="%s" class="%s" target="%s">%s</a>',
            \esc_url( $url ),
            \esc_attr( implode( ' ', $classes ) ),
            \esc_attr( $target ),
            \esc_html( $label )
        );
    }

    /**
     * Renders the array as a quoted HTML attribute string.
     *
     * @param array<string,string> $atts
     */
    public static function attrs_to_html( array $atts ): string {
        $parts = [];
        foreach ( $atts as $key => $value ) {
            if ( $value === '' || $value === null ) {
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
