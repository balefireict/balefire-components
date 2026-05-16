<?php
/**
 * WPBakery mapping for [bma_hero]
 *
 * @package Balefire\Component\Hero
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Hero', 'balefire-components' ),
        'base'        => 'bma_hero',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Hero section with headline, subhead, CTAs and optional background image.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Alignment', 'balefire-components' ),
                'param_name'  => 'align',
                'value'       => [
                    __( 'Center', 'balefire-components' ) => 'center',
                    __( 'Left', 'balefire-components' )   => 'left',
                ],
                'std'         => 'center',
            ],
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Variant', 'balefire-components' ),
                'param_name'  => 'variant',
                'value'       => [
                    __( 'Dark overlay', 'balefire-components' ) => 'dark',
                    __( 'Light overlay', 'balefire-components' ) => 'light',
                    __( 'Gradient', 'balefire-components' )   => 'gradient',
                ],
                'std'         => 'light',
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
