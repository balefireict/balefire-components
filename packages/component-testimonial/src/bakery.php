<?php
/**
 * WPBakery mapping for [bma_testimonial]
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
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Testimonial quote with attribution and optional image.', 'balefire-components' ),
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
                'type'        => 'textfield',
                'heading'     => __( 'Extra CSS class', 'balefire-components' ),
                'param_name'  => 'class',
                'value'       => '',
            ],
        ],
    ]
);
