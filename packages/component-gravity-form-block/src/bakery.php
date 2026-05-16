<?php
/**
 * WPBakery mapping for [bma_gravity_form_block]
 *
 * @package Balefire\Component\GravityFormBlock
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Gravity Form Block', 'balefire-components' ),
        'base'        => 'bma_gravity_form_block',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Gravity Form wrapper with headline and optional background variant.', 'balefire-components' ),
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
                    __( 'Light', 'balefire-components' )    => 'light',
                    __( 'Dark', 'balefire-components' )     => 'dark',
                    __( 'Gradient', 'balefire-components' ) => 'gradient',
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
