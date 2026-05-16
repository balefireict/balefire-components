<?php
/**
 * Renderer for [bma_content_section] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\ContentSection;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'layout'         => 'text-only',
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
            'bma_content_section'
        );

        $post_id = \get_the_ID() ?: 0;

        $headline   = \get_field( 'bma_content_section_headline', $post_id ) ?: '';
        $body       = \get_field( 'bma_content_section_body', $post_id ) ?: '';
        $image      = \get_field( 'bma_content_section_image', $post_id ) ?: null;
        $cta        = \get_field( 'bma_content_section_cta', $post_id ) ?: null;
        $layout     = \get_field( 'bma_content_section_layout', $post_id ) ?: $atts['layout'];
        $background = \get_field( 'bma_content_section_background', $post_id ) ?: 'white';

        $wrapper_atts = self::build_wrapper_atts( $atts, $layout, $background );

        $args = [
            'headline'   => $headline,
            'body'       => $body,
            'image'      => $image,
            'cta'        => $cta,
            'layout'     => $layout,
            'background' => $background,
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    /**
     * @param array<string,string> $atts
     * @return array<string,string>
     */
    private static function build_wrapper_atts( array $atts, string $layout, string $background ): array {
        $classes = [ 'bma-c-content-section', "bma-c-content-section--{$layout}", "bma-c-content-section--bg-{$background}" ];

        foreach ( [ 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-content-section--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_content_section/wrapper_atts', $wrapper, $atts );
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
        $classes = [ 'btn', 'btn-primary' ];

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
