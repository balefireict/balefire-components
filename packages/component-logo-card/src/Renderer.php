<?php
/**
 * Renderer for [bma_logo_card] shortcode.
 */

declare( strict_types=1 );

namespace Balefire\Component\LogoCard;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'align'          => 'center',
            'columns'        => '5',
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
            'bma_logo_card'
        );

        $post_id = \get_the_ID() ?: 0;

        $headline = \get_field( 'bma_logo_card_headline', $post_id ) ?: '';
        $logos    = \get_field( 'bma_logo_card_logos', $post_id ) ?: [];
        $columns  = \get_field( 'bma_logo_card_columns', $post_id ) ?: $atts['columns'];

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'headline' => $headline,
            'logos'    => is_array( $logos ) ? $logos : [],
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
        $classes = [ 'bma-c-logo-card' ];

        foreach ( [ 'align', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-logo-card--{$key}-{$value}";
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

        return (array) \apply_filters( 'bma_c_logo_card/wrapper_atts', $wrapper, $atts );
    }

    /**
     * @param array<string,mixed> $logo
     */
    public static function render_logo( array $logo ): string {
        $image = ! empty( $logo['image'] ) && is_array( $logo['image'] ) ? $logo['image'] : null;
        $link  = ! empty( $logo['link'] ) && is_array( $logo['link'] ) ? $logo['link'] : null;

        if ( empty( $image ) || empty( $image['url'] ) ) {
            return '';
        }

        $img_tag = sprintf(
            '<img src="%s" alt="%s" loading="lazy">',
            \esc_url( $image['url'] ),
            \esc_attr( $image['alt'] ?? '' )
        );

        if ( ! empty( $link ) && ! empty( $link['url'] ) ) {
            return sprintf(
                '<a href="%s" class="bma-c-logo-card__logo" target="%s">%s</a>',
                \esc_url( $link['url'] ),
                \esc_attr( $link['target'] ?? '_self' ),
                $img_tag
            );
        }

        return sprintf( '<div class="bma-c-logo-card__logo">%s</div>', $img_tag );
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
