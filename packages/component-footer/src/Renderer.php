<?php
/**
 * Renderer for [bma_footer] shortcode.
 *
 * Outputs the site <footer> with logo, CTA row, footer nav,
 * address, social links, and copyright. Stable BEM hooks for theme CSS:
 *   .bma-c-footer, .bma-c-footer__inner, .bma-c-footer__logo,
 *   .bma-c-footer__cta-row, .bma-c-footer__nav, .bma-c-footer__address,
 *   .bma-c-footer__social, .bma-c-footer__meta
 */

declare( strict_types=1 );

namespace Balefire\Component\Footer;

final class Renderer {

    /**
     * @return array<string, string>
     */
    private static function defaults(): array {
        return [
            'align'          => '',
            'variant'        => '',
            'container'      => '',
            'spacing_top'    => '',
            'spacing_bottom' => '',
            'id'             => 'footer',
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
            'bma_footer'
        );

        $wrapper_atts = self::build_wrapper_atts( $atts );

        $args = [
            'logo_html'     => self::build_logo_html(),
            'quote_link'    => \get_field( 'bma_quote_link', 'option' ),
            'phone'         => \get_field( 'bma_business_phone', 'option' ),
            'address'       => \get_field( 'bma_business_address', 'option' ),
            'city'          => \get_field( 'bma_business_city', 'option' ),
            'state'         => \get_field( 'bma_business_state', 'option' ),
            'zipcode'       => \get_field( 'bma_business_zipcode', 'option' ),
            'instagram'     => \get_field( 'bma_social_instagram_link', 'option' ),
            'facebook'      => \get_field( 'bma_social_facebook_link', 'option' ),
            'footer_menu'   => $atts['footer_menu'] ?? 'footer-nav',
            'site_name'     => \get_bloginfo( 'name' ),
            'year'          => (string) \date( 'Y' ),
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
        $classes = [ 'bma-c-footer' ];

        foreach ( [ 'align', 'variant', 'container' ] as $key ) {
            $value = (string) $atts[ $key ];
            if ( $value !== '' && preg_match( '/^[a-z0-9_-]+$/i', $value ) ) {
                $classes[] = "bma-c-footer--{$key}-{$value}";
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
            'id'    => (string) $atts['id'] ?: 'footer',
            'class' => implode( ' ', array_unique( $classes ) ),
            'role'  => 'contentinfo',
        ];

        return (array) \apply_filters( 'bma_c_footer/wrapper_atts', $wrapper, $atts );
    }

    private static function build_logo_html(): string {
        if ( \function_exists( 'has_custom_logo' ) && \has_custom_logo() ) {
            $html = \get_custom_logo();
        } else {
            $name = \get_bloginfo( 'name' );
            $home = \esc_url( \home_url( '/' ) );
            $html = sprintf(
                '<a class="bma-c-footer__logo-fallback" href="%s" rel="home">%s</a>',
                $home,
                \esc_html( $name )
            );
        }

        return (string) \apply_filters( 'bma_c_footer/logo_html', $html );
    }

    /**
     * @param array<string,mixed> $args
     */
    public static function render_footer_nav( array $args ): string {
        $location = (string) ( $args['footer_menu'] ?? 'footer-nav' );
        if ( ! \has_nav_menu( $location ) ) {
            return '';
        }

        $menu = \wp_nav_menu( [
            'theme_location' => $location,
            'container'      => false,
            'menu_class'     => 'nostyle bma-c-footer__nav-list',
            'menu_id'        => 'nav-footer',
            'fallback_cb'    => false,
            'echo'           => false,
            'depth'          => 1,
        ] );

        return is_string( $menu ) ? $menu : '';
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
