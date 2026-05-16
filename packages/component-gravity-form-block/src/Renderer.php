<?php
/**
 * Renderer for [bma_gravity_form_block] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\GravityFormBlock;

final class Renderer {

    private static function defaults(): array {
        return [
            'align'          => 'center',
            'variant'        => 'light',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => '',
            'class'          => '',
            'headline'       => '',
            'subhead'        => '',
            'form_id'        => '',
        ];
    }

    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_gravity_form_block'
        );

        $post_id = \get_the_ID() ?: 0;

        $headline  = (string) ( $atts['headline'] ?? \get_field( 'bma_gravity_form_block_headline', $post_id ) ?? '' );
        $subhead   = (string) ( $atts['subhead'] ?? \get_field( 'bma_gravity_form_block_subhead', $post_id ) ?? '' );
        $form_id   = (int) ( $atts['form_id'] ?? \get_field( 'bma_gravity_form_block_form_id', $post_id ) ?? 0 );
        $variant   = (string) ( $atts['variant'] ?: ( \get_field( 'bma_gravity_form_block_variant', $post_id ) ?: 'light' ) );

        $wrapper_atts = self::build_wrapper_atts( $atts, $variant );

        $args = [
            'headline' => $headline,
            'subhead'  => $subhead,
            'form_id'  => $form_id,
            'variant'  => $variant,
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    private static function build_wrapper_atts( array $atts, string $variant ): array {
        $classes = [ 'bma-c-gravity-form-block', "bma-c-gravity-form-block--{$variant}" ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-gravity-form-block--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_gravity_form_block/wrapper_atts', $wrapper, $atts );
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
