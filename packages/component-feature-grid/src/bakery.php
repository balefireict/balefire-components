<?php
/**
 * WPBakery mapping for [bma_feature_grid]
 *
 * @package Balefire\Component\FeatureGrid
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Feature Grid', 'balefire-components' ),
        'base'        => 'bma_feature_grid',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Grid of feature cards with icon, title, description and link.', 'balefire-components' ),
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
                'heading'     => __( 'Columns', 'balefire-components' ),
                'param_name'  => 'columns',
                'value'       => [
                    __( '2', 'balefire-components' ) => '2',
                    __( '3', 'balefire-components' ) => '3',
                    __( '4', 'balefire-components' ) => '4',
                ],
                'std'         => '3',
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
