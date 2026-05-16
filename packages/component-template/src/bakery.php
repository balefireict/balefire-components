<?php
/**
 * WPBakery element registration for [bma_template].
 *
 * Loaded from bootstrap.php on the `vc_before_init` hook ONLY if vc_map() exists.
 * Replace `template` with your component slug throughout.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map( [
    'name'        => __( 'BMA: Template', 'balefire-components' ),
    'base'        => 'bma_template',
    'category'    => __( 'Balefire', 'balefire-components' ),
    'icon'        => 'icon-vc-balefire',
    'description' => __( 'Template scaffolding component.', 'balefire-components' ),
    'params'      => [
        [
            'type'        => 'dropdown',
            'heading'     => __( 'Align', 'balefire-components' ),
            'param_name'  => 'align',
            'value'       => [
                __( 'Default', 'balefire-components' ) => '',
                __( 'Left', 'balefire-components' )    => 'left',
                __( 'Center', 'balefire-components' )  => 'center',
                __( 'Right', 'balefire-components' )   => 'right',
            ],
        ],
        [
            'type'        => 'dropdown',
            'heading'     => __( 'Variant', 'balefire-components' ),
            'param_name'  => 'variant',
            'value'       => [
                __( 'Default', 'balefire-components' )  => '',
                __( 'Light', 'balefire-components' )    => 'light',
                __( 'Dark', 'balefire-components' )     => 'dark',
                __( 'Gradient', 'balefire-components' ) => 'gradient',
                __( 'Brand', 'balefire-components' )    => 'brand',
            ],
        ],
        [
            'type'        => 'dropdown',
            'heading'     => __( 'Container', 'balefire-components' ),
            'param_name'  => 'container',
            'value'       => [
                __( 'Default', 'balefire-components' ) => '',
                __( 'Wide', 'balefire-components' )    => 'wide',
                __( 'Narrow', 'balefire-components' )  => 'narrow',
                __( 'Full', 'balefire-components' )    => 'full',
            ],
        ],
        [
            'type'        => 'dropdown',
            'heading'     => __( 'Spacing Top', 'balefire-components' ),
            'param_name'  => 'spacing_top',
            'value'       => [
                __( 'Default', 'balefire-components' ) => '',
                __( 'None', 'balefire-components' )    => 'none',
                __( 'Small', 'balefire-components' )   => 'sm',
                __( 'Medium', 'balefire-components' )  => 'md',
                __( 'Large', 'balefire-components' )   => 'lg',
                __( 'X-Large', 'balefire-components' ) => 'xl',
            ],
        ],
        [
            'type'        => 'dropdown',
            'heading'     => __( 'Spacing Bottom', 'balefire-components' ),
            'param_name'  => 'spacing_bottom',
            'value'       => [
                __( 'Default', 'balefire-components' ) => '',
                __( 'None', 'balefire-components' )    => 'none',
                __( 'Small', 'balefire-components' )   => 'sm',
                __( 'Medium', 'balefire-components' )  => 'md',
                __( 'Large', 'balefire-components' )   => 'lg',
                __( 'X-Large', 'balefire-components' ) => 'xl',
            ],
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'DOM Anchor (id)', 'balefire-components' ),
            'param_name'  => 'id',
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Extra CSS class', 'balefire-components' ),
            'param_name'  => 'class',
        ],
    ],
] );
