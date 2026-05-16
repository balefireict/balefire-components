<?php
/**
 * Renderer for [bma_header_split] shortcode.
 *
 * Outputs the site <header> with logo, primary nav, secondary nav,
 * and mobile drawer toggle. Stable BEM hooks for theme CSS:
 *   .bma-c-header-split, .bma-c-header-split__inner, .bma-c-header-split__logo,
 *   .bma-c-header-split__nav-primary, .bma-c-header-split__nav-secondary,
 *   .bma-c-header-split__utility, .bma-c-header-split__toggle
 * Plus legacy IDs/classes for compatibility with existing rockerbox JS:
 *   #header, #nav-main, #nav-main-wrapper,
 *   #nav-secondary, #nav-secondary-wrapper, .mobile-menu-toggle,
 *   data-drawer-target, data-drawer-toggle, aria-controls.
 */

declare( strict_types=1 );

namespace Balefire\Component\HeaderSplit;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            // Universal.
            'align'          => '',
            'variant'        => '',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => 'header',
            'class'          => '',
            // Component-specific.
            'sticky'         => 'true',
            'blur'           => 'true',
            'primary_menu'   => 'primary',
            'secondary_menu' => 'secondary',
        ];
    }

    /**
     * @param array<string,string>|string $atts
     */
    public static function render( $atts, ?string $content = null ): string {
        $atts = \shortcode_atts(
            self::defaults(),
            is_array( $atts ) ? $atts : [],
            'bma_header_split'
        );

        $wrapper_atts = self::build_wrapper_atts( $atts );
        $logo_html    = self::build_logo_html();

        ob_start();
        include __DIR__ . '/template.php';
        return (string) ob_get_clean();
    }

    /**
     * @param array<string,string> $atts
     * @return array<string,string>
     */
    private static function build_wrapper_atts( array $atts ): array {
        $classes = [ 'bma-c-header-split' ];

        if ( self::is_truthy( $atts['sticky'] ) ) {
            $classes[] = 'bma-c-header-split--sticky';
        }
        if ( self::is_truthy( $atts['blur'] ) ) {
            $classes[] = 'bma-c-header-split--blur';
        }

        foreach ( [ 'align', 'variant', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-header-split--{$key}-{$value}";
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
            'id'    => (string) $atts['id'] ?: 'header',
            'class' => implode( ' ', array_unique( $classes ) ),
            'role'  => 'banner',
        ];

        /**
         * Filter the <header> root attributes.
         *
         * @param array<string,string> $wrapper
         * @param array<string,string> $atts
         */
        return (array) \apply_filters( 'bma_c_header_split/wrapper_atts', $wrapper, $atts );
    }

    private static function build_logo_html(): string {
        if ( \function_exists( 'has_custom_logo' ) && \has_custom_logo() ) {
            $html = \get_custom_logo();
        } else {
            $name = \get_bloginfo( 'name' );
            $home = \esc_url( \home_url( '/' ) );
            $html = sprintf(
                '<a class="bma-c-header-split__logo-fallback" href="%s" rel="home">%s</a>',
                $home,
                \esc_html( $name )
            );
        }

        /**
         * Filter the logo HTML. Replace to inject inline SVG, etc.
         *
         * @param string $html
         */
        return (string) \apply_filters( 'bma_c_header_split/logo_html', $html );
    }

    /**
     * @param array<string,string> $atts
     */
    public static function render_primary_nav( array $atts ): string {
        $args = [
            'theme_location' => (string) ( $atts['primary_menu'] ?? 'primary' ),
            'container'      => false,
            'menu_class'     => 'nostyle bma-c-header-split__nav-primary-list',
            'menu_id'        => 'nav-main',
            'fallback_cb'    => false,
            'echo'           => false,
            'depth'          => 3,
        ];
        /** @var array<string,mixed> $args */
        $args = (array) \apply_filters( 'bma_c_header_split/primary_args', $args, $atts );

        $menu = \wp_nav_menu( $args );
        return is_string( $menu ) ? $menu : '';
    }

    /**
     * @param array<string,string> $atts
     */
    public static function render_secondary_nav( array $atts ): string {
        $args = [
            'theme_location' => (string) ( $atts['secondary_menu'] ?? 'secondary' ),
            'container'      => false,
            'menu_class'     => 'nostyle bma-c-header-split__nav-secondary-list',
            'menu_id'        => 'nav-secondary',
            'fallback_cb'    => false,
            'echo'           => false,
            'depth'          => 1,
        ];
        /** @var array<string,mixed> $args */
        $args = (array) \apply_filters( 'bma_c_header_split/secondary_args', $args, $atts );

        $menu = \wp_nav_menu( $args );
        return is_string( $menu ) ? $menu : '';
    }

    public static function render_toggle(): string {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="37" height="32" viewBox="0 0 37 32" fill="currentColor" aria-hidden="true">'
             . '<path d="M0,32V27.427H18.5V32ZM0,18.285V13.715H37v4.571ZM0,4.571V0H37V4.571Z"></path>'
             . '</svg>';

        $html = sprintf(
            '<button class="bma-c-header-split__toggle mobile-menu-toggle" type="button"'
            . ' aria-controls="default-sidebar" aria-expanded="false"'
            . ' data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar">'
            . '<span class="sr-only">%s</span>%s</button>',
            \esc_html__( 'Open sidebar', 'balefire-components' ),
            $svg
        );

        /** @param string $html */
        return (string) \apply_filters( 'bma_c_header_split/toggle_html', $html );
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

    private static function is_truthy( $value ): bool {
        if ( is_bool( $value ) ) {
            return $value;
        }
        $string = strtolower( trim( (string) $value ) );
        return in_array( $string, [ '1', 'true', 'yes', 'on' ], true );
    }
}
