<?php
/**
 * Renderer for [bma_stat_callout] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\StatCallout;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'align'          => 'center',
            'columns'        => '4',
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
            'bma_stat_callout'
        );

        $post_id = \get_the_ID() ?: 0;

        $stats   = \get_field( 'bma_stat_callout_stats', $post_id ) ?: [];
        $columns = \get_field( 'bma_stat_callout_columns', $post_id ) ?: $atts['columns'];

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'stats'   => is_array( $stats ) ? $stats : [],
            'columns' => $columns,
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
        $classes = [ 'bma-c-stat-callout' ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-stat-callout--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_stat_callout/wrapper_atts', $wrapper, $atts );
    }

    /**
     * @param array<string,mixed> $stat
     */
    public static function render_stat( array $stat ): string {
        $value = ! empty( $stat['value'] ) ? $stat['value'] : '';
        $label = ! empty( $stat['label'] ) ? $stat['label'] : '';

        if ( $value === '' && $label === '' ) {
            return '';
        }

        ob_start();
        include __DIR__ . '/stat-template.php';
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
