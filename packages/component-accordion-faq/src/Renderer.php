<?php
/**
 * Renderer for [bma_accordion_faq] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\AccordionFaq;

final class Renderer {

    private static function defaults(): array {
        return [
            'align'          => 'center',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => '',
            'class'          => '',
        ];
    }

    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_accordion_faq'
        );

        $post_id = \get_the_ID() ?: 0;

        $headline = \get_field( 'bma_accordion_faq_headline', $post_id ) ?: '';
        $subhead  = \get_field( 'bma_accordion_faq_subhead', $post_id ) ?: '';
        $items    = \get_field( 'bma_accordion_faq_items', $post_id ) ?: [];

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'headline' => $headline,
            'subhead'  => $subhead,
            'items'    => is_array( $items ) ? $items : [],
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    private static function build_wrapper_atts( array $atts ): array {
        $classes = [ 'bma-c-accordion-faq' ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-accordion-faq--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_accordion_faq/wrapper_atts', $wrapper, $atts );
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
