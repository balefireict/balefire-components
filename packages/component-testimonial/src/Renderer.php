<?php
/**
 * Renderer for [bma_testimonial] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\Testimonial;

final class Renderer {

    private static function defaults(): array {
        return [
            'align'          => 'center',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => '',
            'class'          => '',
            'quote'          => '',
            'attribution'    => '',
            'role'           => '',
            'company'        => '',
            'image'          => '',
        ];
    }

    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_testimonial'
        );

        $post_id = \get_the_ID() ?: 0;

        $quote       = (string) ( $atts['quote'] ?? \get_field( 'bma_testimonial_quote', $post_id ) ?? '' );
        $attribution = (string) ( $atts['attribution'] ?? \get_field( 'bma_testimonial_attribution', $post_id ) ?? '' );
        $role        = (string) ( $atts['role'] ?? \get_field( 'bma_testimonial_role', $post_id ) ?? '' );
        $company     = (string) ( $atts['company'] ?? \get_field( 'bma_testimonial_company', $post_id ) ?? '' );
        $image       = \is_array( $atts['image'] ?? null ) ? $atts['image'] : ( \get_field( 'bma_testimonial_image', $post_id ) ?: null );

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'quote'       => $quote,
            'attribution' => $attribution,
            'role'        => $role,
            'company'     => $company,
            'image'       => $image,
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    private static function build_wrapper_atts( array $atts ): array {
        $classes = [ 'bma-c-testimonial' ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-testimonial--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_testimonial/wrapper_atts', $wrapper, $atts );
    }

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
