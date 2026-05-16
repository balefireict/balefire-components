<?php
/**
 * WPBakery mapping for [bma_footer]
 *
 * @package Balefire\Component\Footer
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Footer', 'balefire-components' ),
        'base'        => 'bma_footer',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Site footer with logo, nav, contact info, and copyright.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'textfield',
                'heading'     => __( 'Footer menu location', 'balefire-components' ),
                'param_name'  => 'footer_menu',
                'value'       => 'footer-nav',
                'description' => __( 'Theme location slug for the footer navigation.', 'balefire-components' ),
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Extra CSS class', 'balefire-components' ),
                'param_name'  => 'class',
                'value'       => '',
            ],
        ],
    ]
);
