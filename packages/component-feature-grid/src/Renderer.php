<?php
/**
 * Renderer for [bma_feature_grid] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\FeatureGrid;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'align'          => 'center',
            'columns'        => '3',
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
            'bma_feature_grid'
        );

        $post_id = \get_the_ID() ?: 0;

        $eyebrow  = \get_field( 'bma_feature_grid_eyebrow', $post_id ) ?: '';
        $headline = \get_field( 'bma_feature_grid_headline', $post_id ) ?: '';
        $subhead  = \get_field( 'bma_feature_grid_subhead', $post_id ) ?: '';
        $cards    = \get_field( 'bma_feature_grid_cards', $post_id ) ?: [];
        $columns  = \get_field( 'bma_feature_grid_columns', $post_id ) ?: $atts['columns'];

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'eyebrow'  => $eyebrow,
            'headline' => $headline,
            'subhead'  => $subhead,
            'cards'    => is_array( $cards ) ? $cards : [],
            'columns'  => $columns,
        ];

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    /**
     * @param array<string,string> $atts
     * @return array<string,string>
     */
    private static function build_wrapper_atts( array $atts ): array {
        $classes = [ 'bma-c-feature-grid' ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-feature-grid--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_feature_grid/wrapper_atts', $wrapper, $atts );
    }

    /**
     * @param array<string,mixed> $card
     */
    public static function render_card( array $card ): string {
        $title = ! empty( $card['title'] ) ? $card['title'] : '';
        $desc  = ! empty( $card['description'] ) ? $card['description'] : '';
        $link  = ! empty( $card['link'] ) && is_array( $card['link'] ) ? $card['link'] : null;
        $icon  = ! empty( $card['icon'] ) && is_array( $card['icon'] ) ? $card['icon'] : null;

        ob_start();
        include __DIR__ . '/card-template.php';
        return (string) ob_get_clean();
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
