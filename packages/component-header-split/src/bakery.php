<?php
/**
 * WPBakery mapping for [bma_header_split]
 *
 * @package Balefire\Component\HeaderSplit
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Header (Split)', 'balefire-components' ),
        'base'        => 'bma_header_split',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Site header with logo, primary nav, secondary nav, and mobile toggle.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Sticky', 'balefire-components' ),
                'param_name'  => 'sticky',
                'value'       => [
                    __( 'Yes', 'balefire-components' ) => 'true',
                    __( 'No', 'balefire-components' )  => 'false',
                ],
                'std'         => 'true',
            ],
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Backdrop blur', 'balefire-components' ),
                'param_name'  => 'blur',
                'value'       => [
                    __( 'Yes', 'balefire-components' ) => 'true',
                    __( 'No', 'balefire-components' )  => 'false',
                ],
                'std'         => 'true',
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Primary menu location', 'balefire-components' ),
                'param_name'  => 'primary_menu',
                'value'       => 'primary-nav',
                'description' => __( 'Theme location slug for the primary navigation.', 'balefire-components' ),
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Secondary menu location', 'balefire-components' ),
                'param_name'  => 'secondary_menu',
                'value'       => 'secondary-nav',
                'description' => __( 'Theme location slug for the secondary navigation.', 'balefire-components' ),
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
