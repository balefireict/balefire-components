<?php
/**
 * WPBakery mapping for [bma_cta_banner]
 *
 * @package Balefire\Component\CtaBanner
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'CTA Banner', 'balefire-components' ),
        'base'        => 'bma_cta_banner',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Call-to-action banner with headline and button.', 'balefire-components' ),
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
                    __( 'Gradient', 'balefire-components' ) => 'gradient',
                    __( 'Dark', 'balefire-components' )     => 'dark',
                    __( 'Light', 'balefire-components' )    => 'light',
                ],
                'std'         => 'gradient',
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
