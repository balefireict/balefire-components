<?php
/**
 * Renderer for [bma_hero] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\Hero;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'align'          => 'center',
            'variant'        => 'light',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => '',
            'class'          => '',
            'eyebrow'        => '',
            'headline'       => '',
            'subhead'        => '',
            'primary_cta'    => '',
            'secondary_cta'  => '',
            'bg_image'       => '',
        ];
    }

    /**
     * @param array<string,string>|string $atts
     */
    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_hero'
        );

        $post_id = \get_the_ID() ?: 0;

        $eyebrow       = (string) ( $atts['eyebrow'] ?? \get_field( 'bma_hero_eyebrow', $post_id ) ?? '' );
        $headline      = (string) ( $atts['headline'] ?? \get_field( 'bma_hero_headline', $post_id ) ?? '' );
        $subhead       = (string) ( $atts['subhead'] ?? \get_field( 'bma_hero_subhead', $post_id ) ?? '' );
        $primary_cta   = \is_array( $atts['primary_cta'] ?? null ) ? $atts['primary_cta'] : ( \get_field( 'bma_hero_primary_cta', $post_id ) ?: null );
        $secondary_cta = \is_array( $atts['secondary_cta'] ?? null ) ? $atts['secondary_cta'] : ( \get_field( 'bma_hero_secondary_cta', $post_id ) ?: null );
        $bg_image      = \is_array( $atts['bg_image'] ?? null ) ? $atts['bg_image'] : ( \get_field( 'bma_hero_bg_image', $post_id ) ?: null );
        $variant       = (string) ( $atts['variant'] ?: ( \get_field( 'bma_hero_variant', $post_id ) ?: 'light' ) );

        $wrapper_atts = self::build_wrapper_atts( $atts, $variant );

        $args = [
            'eyebrow'       => $eyebrow,
            'headline'      => $headline,
            'subhead'       => $subhead,
            'primary_cta'   => $primary_cta,
            'secondary_cta' => $secondary_cta,
            'bg_image'      => $bg_image,
            'variant'       => $variant,
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
        $classes = [ 'bma-c-hero', "bma-c-hero--{$variant}" ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-hero--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_hero/wrapper_atts', $wrapper, $atts );
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
        $classes = [ 'btn' ];

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
