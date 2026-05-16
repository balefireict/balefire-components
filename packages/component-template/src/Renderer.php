<?php
/**
 * Renderer for [bma_template] shortcode.
 *
 * Replace `Template` with your component's PascalSlug throughout.
 */

declare( strict_types=1 );

namespace Balefire\Component\Template;

final class Renderer {

    /**
     * Universal attributes supported by every Balefire component.
     *
     * @return array<string, mixed>
     */
    private static function universal_defaults(): array {
        return [
            'align'          => '',     // left | center | right
            'variant'        => '',     // light | dark | gradient | brand | component-defined
            'container'      => '',     // default | wide | narrow | full
            'spacing_top'    => '',     // none | sm | md | lg | xl
            'spacing_bottom' => '',     // none | sm | md | lg | xl
            'id'             => '',     // DOM anchor
            'class'          => '',     // extra root class
        ];
    }

    /**
     * Component-specific attribute defaults. Override in your component.
     *
     * @return array<string, mixed>
     */
    private static function component_defaults(): array {
        return [
            // Example:
            // 'heading' => '',
            // 'cta_label' => '',
        ];
    }

    /**
     * @param array<string, string>|string $atts
     * @param string|null                  $content
     */
    public static function render( $atts, ?string $content = null ): string {
        $defaults = array_merge( self::universal_defaults(), self::component_defaults() );
        $atts     = \shortcode_atts( $defaults, is_array( $atts ) ? $atts : [], 'bma_template' );

        $root_classes = self::build_root_classes( $atts );
        $root_attrs   = self::build_root_attrs( $atts, $root_classes );

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    /**
     * Compile the root element's class list from universal + per-component attrs.
     *
     * @param array<string, mixed> $atts
     */
    private static function build_root_classes( array $atts ): string {
        $classes = [ 'bma-c-template' ];

        foreach ( [ 'align', 'variant', 'container' ] as $key ) {
            $value = (string) ( $atts[ $key ] ?? '' );
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-template--{$key}-{$value}";
            }
        }

        foreach ( [ 'spacing_top' => 'pt', 'spacing_bottom' => 'pb' ] as $key => $abbrev ) {
            $value = (string) ( $atts[ $key ] ?? '' );
            if ( $value !== '' && preg_match( '/^[a-z0-9]+$/i', $value ) ) {
                $classes[] = "bma-c--{$abbrev}-{$value}";
            }
        }

        $extra = trim( (string) ( $atts['class'] ?? '' ) );
        if ( $extra !== '' ) {
            $classes[] = $extra;
        }

        return implode( ' ', array_unique( $classes ) );
    }

    /**
     * Build root element attribute string (id, class).
     *
     * @param array<string, mixed> $atts
     */
    private static function build_root_attrs( array $atts, string $classes ): string {
        $parts = [
            sprintf( 'class="%s"', \esc_attr( $classes ) ),
        ];

        $dom_id = trim( (string) ( $atts['id'] ?? '' ) );
        if ( $dom_id !== '' ) {
            $parts[] = sprintf( 'id="%s"', \esc_attr( $dom_id ) );
        }

        return implode( ' ', $parts );
    }
}
