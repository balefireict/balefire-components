<?php
/**
 * WPBakery mapping for [bma_cta_banner]
 *
 * Attribute-driven (no ACF). Rich body uses the element's content area.
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
        'category'    => __( 'Custom Elements', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'BMA — Call-to-action banner with headline, body, and button.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'textfield',
                'heading'     => __( 'Headline', 'balefire-components' ),
                'param_name'  => 'headline',
                'admin_label' => true,
            ],
            [
                'type'        => 'textarea_html',
                'heading'     => __( 'Body', 'balefire-components' ),
                'param_name'  => 'content',
                'description' => __( 'Rich text shown beneath the headline.', 'balefire-components' ),
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Button Label', 'balefire-components' ),
                'param_name'  => 'cta_label',
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Button URL', 'balefire-components' ),
                'param_name'  => 'cta_url',
            ],
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Button Style', 'balefire-components' ),
                'param_name'  => 'cta_style',
                'value'       => [
                    __( 'White (for dark/gradient bg)', 'balefire-components' ) => 'white',
                    __( 'Primary', 'balefire-components' )                       => 'primary',
                    __( 'Secondary', 'balefire-components' )                     => 'secondary',
                    __( 'Black', 'balefire-components' )                         => 'black',
                ],
                'std'         => 'white',
            ],
            [
                'type'        => 'dropdown',
                'heading'     => __( 'Open Button In', 'balefire-components' ),
                'param_name'  => 'cta_target',
                'value'       => [
                    __( 'Same tab', 'balefire-components' ) => '',
                    __( 'New tab', 'balefire-components' )  => '_blank',
                ],
                'std'         => '',
            ],
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
                'heading'     => __( 'Element ID', 'balefire-components' ),
                'param_name'  => 'id',
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Extra CSS class', 'balefire-components' ),
                'param_name'  => 'class',
            ],
        ],
    ]
);
