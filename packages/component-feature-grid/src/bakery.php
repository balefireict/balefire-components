<?php
/**
 * WPBakery mapping for the [bma_feature_grid] parent + [bma_feature_card] child.
 *
 * Loaded on vc_before_init from bootstrap.php. No ACF reads.
 *
 * @package Balefire\Component\FeatureGrid
 */

declare( strict_types=1 );

if ( ! function_exists( 'vc_map' ) ) {
    return;
}

vc_map(
    [
        'name'                    => __( 'Feature Grid', 'balefire-components' ),
        'base'                    => 'bma_feature_grid',
        'php_class_name'          => 'WPBakeryShortCode_BMA_FeatureGrid',
        'category'                => __( 'Custom Elements', 'balefire-components' ),
        'description'             => __( 'BMA — Grid of feature cards with eyebrow, headline, subhead and columns.', 'balefire-components' ),
        'icon'                    => 'icon-wpb-row',
        'as_parent'               => [ 'only' => 'bma_feature_card' ],
        'content_element'         => true,
        'show_settings_on_create' => true,
        'is_container'            => true,
        'js_view'                 => 'VcColumnView',
        'params'                  => [
            [
                'type'        => 'textfield',
                'heading'     => __( 'Eyebrow', 'balefire-components' ),
                'param_name'  => 'eyebrow',
                'admin_label' => true,
            ],
            [
                'type'       => 'textfield',
                'heading'    => __( 'Headline', 'balefire-components' ),
                'param_name' => 'headline',
            ],
            [
                'type'       => 'textarea',
                'heading'    => __( 'Subhead', 'balefire-components' ),
                'param_name' => 'subhead',
            ],
            [
                'type'       => 'dropdown',
                'heading'    => __( 'Columns', 'balefire-components' ),
                'param_name' => 'columns',
                'value'      => [
                    __( '2', 'balefire-components' ) => '2',
                    __( '3', 'balefire-components' ) => '3',
                    __( '4', 'balefire-components' ) => '4',
                ],
                'std'        => '3',
            ],
            [
                'type'       => 'el_id',
                'heading'    => __( 'Element ID', 'balefire-components' ),
                'param_name' => 'el_id',
            ],
            [
                'type'       => 'textfield',
                'heading'    => __( 'Extra CSS class', 'balefire-components' ),
                'param_name' => 'el_class',
            ],
        ],
    ]
);

vc_map(
    [
        'name'            => __( 'Feature Card', 'balefire-components' ),
        'base'            => 'bma_feature_card',
        'category'        => __( 'Custom Elements', 'balefire-components' ),
        'description'     => __( 'BMA — Single feature card with icon, title and body.', 'balefire-components' ),
        'icon'            => 'vc_icon-vc-single-image',
        'as_child'        => [ 'only' => 'bma_feature_grid' ],
        'content_element' => true,
        'params'          => [
            [
                'type'       => 'attach_image',
                'heading'    => __( 'Icon', 'balefire-components' ),
                'param_name' => 'icon',
            ],
            [
                'type'        => 'textfield',
                'heading'     => __( 'Title', 'balefire-components' ),
                'param_name'  => 'title',
                'admin_label' => true,
            ],
            [
                'type'       => 'textarea_html',
                'heading'    => __( 'Body', 'balefire-components' ),
                'param_name' => 'content',
            ],
        ],
    ]
);
