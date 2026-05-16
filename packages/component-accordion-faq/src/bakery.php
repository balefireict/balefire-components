<?php
/**
 * WPBakery mapping for [bma_accordion_faq]
 *
 * @package Balefire\Component\AccordionFaq
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'        => __( 'Accordion FAQ', 'balefire-components' ),
        'base'        => 'bma_accordion_faq',
        'category'    => __( 'Balefire', 'balefire-components' ),
        'icon'        => 'icon-wpb-row',
        'description' => __( 'Accordion-style FAQ section with questions and answers.', 'balefire-components' ),
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
