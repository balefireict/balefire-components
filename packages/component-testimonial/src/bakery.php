<?php
/**
 * WPBakery mapping for [bma_testimonial]
 *
 * Attribute-driven (no ACF). The rich quote uses the element's content area.
 *
 * @package Balefire\Component\Testimonial
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Testimonial', 'balefire-components' ),
        'base'        => 'bma_testimonial',
        'category'    => __( 'Custom Elements', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'BMA — Testimonial quote with attribution and optional image.', 'balefire-components' ),
        'params'      => [
            [
                'type'        => 'textarea_html',
                'heading'     => __( 'Quote', 'balefire-components' ),
                'param_name'  => 'content',
                'description' => __( 'The testimonial quote text.', 'balefire-components' ),
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Attribution Name', 'balefire-components' ),
                'param_name'  => 'attribution',
                'admin_label' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Role / Title', 'balefire-components' ),
                'param_name'  => 'role',
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Company', 'balefire-components' ),
                'param_name'  => 'company',
            ],
            [
                'type'        => 'attach_image',
                'heading'     => __( 'Photo', 'balefire-components' ),
                'param_name'  => 'image',
                'description' => __( 'Optional headshot from the Media Library.', 'balefire-components' ),
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
