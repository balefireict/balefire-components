<?php
/**
 * WPBakery mapping for [bma_content_section]
 *
 * @package Balefire\Component\ContentSection
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Content Section', 'balefire-components' ),
        'base'        => 'bma_content_section',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-layer-shape-text',
        'description' => __( 'Generic text / image content block.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Layout', 'balefire-components' ),
                'param_name'  => 'layout',
                'value'       => [
                    __( 'Text only', 'balefire-components' )      => 'text-only',
                    __( 'Image top', 'balefire-components' )      => 'image-top',
                    __( 'Image left', 'balefire-components' )     => 'image-left',
                    __( 'Image right', 'balefire-components' )    => 'image-right',
                ],
                'std'         => 'text-only',
            ],
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Background', 'balefire-components' ),
                'param_name'  => 'background',
                'value'       => [
                    __( 'White', 'balefire-components' )          => 'white',
                    __( 'Light gray', 'balefire-components' )     => 'light',
                    __( 'Dark', 'balefire-components' )           => 'dark',
                ],
                'std'         => 'white',
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
