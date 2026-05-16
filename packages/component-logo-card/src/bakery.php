<?php
/**
 * WPBakery mapping for [bma_logo_card]
 *
 * @package Balefire\Component\LogoCard
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Logo Card', 'balefire-components' ),
        'base'        => 'bma_logo_card',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Partner logo strip with linked logos.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Columns', 'balefire-components' ),
                'param_name'  => 'columns',
                'value'       => [
                    __( '3', 'balefire-components' ) => '3',
                    __( '4', 'balefire-components' ) => '4',
                    __( '5', 'balefire-components' ) => '5',
                    __( '6', 'balefire-components' ) => '6',
                ],
                'std'         => '5',
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
