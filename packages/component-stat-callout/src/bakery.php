<?php
/**
 * WPBakery mapping for [bma_stat_callout]
 *
 * @package Balefire\Component\StatCallout
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Stat Callout', 'balefire-components' ),
        'base'        => 'bma_stat_callout',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Grid of statistics with large numbers and labels.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Columns', 'balefire-components' ),
                'param_name'  => 'columns',
                'value'       => [
                    __( '2', 'balefire-components' ) => '2',
                    __( '3', 'balefire-components' ) => '3',
                    __( '4', 'balefire-components' ) => '4',
                ],
                'std'         => '4',
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
