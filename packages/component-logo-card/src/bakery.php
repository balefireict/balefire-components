<?php
/**
 * WPBakery mapping for [bma_logo_card] (parent) + [bma_logo_card_item] (child).
 *
 * Required on vc_before_init. Registers the container subclass on vc_after_init.
 *
 * @package Balefire\Component\LogoCard
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'vc_map' ) ) {
	return;
}

$column_choices = array();
foreach ( \Balefire\Component\LogoCard\Renderer::COLUMN_CHOICES as $n ) {
	$column_choices[ (string) $n ] = (string) $n;
}

vc_map(
	array(
		'name'                    => __( 'Logo Card', 'balefire-components' ),
		'base'                    => 'bma_logo_card',
		'php_class_name'          => 'WPBakeryShortCode_BMA_LogoCard',
		'category'                => __( 'Custom Elements', 'balefire-components' ),
		'description'             => __( 'BMA — Partner logo strip with linked logos.', 'balefire-components' ),
		'icon'                    => 'vc_icon-vc-images-carousel',
		'as_parent'               => array( 'only' => 'bma_logo_card_item' ),
		'content_element'         => true,
		'show_settings_on_create' => true,
		'is_container'            => true,
		'js_view'                 => 'VcColumnView',
		'params'                  => array(
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Headline', 'balefire-components' ),
				'param_name' => 'headline',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Columns', 'balefire-components' ),
				'param_name' => 'columns',
				'value'      => $column_choices,
				'std'        => (string) \Balefire\Component\LogoCard\Renderer::DEFAULT_COLUMNS,
			),
			array(
				'type'       => 'el_id',
				'heading'    => __( 'Element ID', 'balefire-components' ),
				'param_name' => 'el_id',
				'group'      => __( 'Extra', 'balefire-components' ),
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Extra CSS class', 'balefire-components' ),
				'param_name' => 'el_class',
				'group'      => __( 'Extra', 'balefire-components' ),
			),
		),
	)
);

vc_map(
	array(
		'name'            => __( 'Logo Card Item', 'balefire-components' ),
		'base'            => 'bma_logo_card_item',
		'php_class_name'  => 'WPBakeryShortCode_BMA_LogoCardItem',
		'category'        => __( 'Custom Elements', 'balefire-components' ),
		'description'     => __( 'BMA — A single logo inside a logo card.', 'balefire-components' ),
		'icon'            => 'vc_icon-vc-single-image',
		'as_child'        => array( 'only' => 'bma_logo_card' ),
		'content_element' => true,
		'params'          => array(
			array(
				'type'        => 'attach_image',
				'heading'     => __( 'Logo Image', 'balefire-components' ),
				'param_name'  => 'image',
				'description' => __( 'Logo image from the Media Library.', 'balefire-components' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Link URL', 'balefire-components' ),
				'param_name'  => 'link',
				'description' => __( 'Optional link wrapping the logo.', 'balefire-components' ),
			),
		),
	)
);

add_action(
	'vc_after_init',
	static function (): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) ) {
			return;
		}
		if ( ! class_exists( 'WPBakeryShortCode_BMA_LogoCard' ) ) {
			eval( 'class WPBakeryShortCode_BMA_LogoCard extends \\WPBakeryShortCodesContainer {}' );
		}
	}
);
