<?php
/**
 * WPBakery mapping + container class for the Accordion FAQ parent/child shortcodes.
 *
 * Parent: [bma_accordion_faq title]    — as_parent only [bma_accordion_faq_item].
 * Child:  [bma_accordion_faq_item question] body — as_child only [bma_accordion_faq].
 *
 * Loaded from bootstrap.php on vc_before_init (vc_map) and the container class
 * is registered on vc_after_init.
 *
 * @package Balefire\Component\AccordionFaq
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_accordion_faq_vc_map' ) ) {
	function bma_accordion_faq_vc_map(): void {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			array(
				'name'                    => __( 'Accordion FAQ', 'balefire-components' ),
				'base'                    => 'bma_accordion_faq',
				'php_class_name'          => 'WPBakeryShortCode_BMA_AccordionFaq',
				'category'                => __( 'Custom Elements', 'balefire-components' ),
				'description'             => __( 'BMA — Editable FAQ accordion list.', 'balefire-components' ),
				'icon'                    => 'vc_icon-vc-toggle',
				'as_parent'               => array( 'only' => 'bma_accordion_faq_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'is_container'            => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Title', 'balefire-components' ),
						'param_name' => 'title',
						'std'        => 'Frequently Asked Questions',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Element ID', 'balefire-components' ),
						'param_name' => 'el_id',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Extra CSS class', 'balefire-components' ),
						'param_name' => 'el_class',
					),
				),
			)
		);

		vc_map(
			array(
				'name'            => __( 'Accordion FAQ Item', 'balefire-components' ),
				'base'            => 'bma_accordion_faq_item',
				'category'        => __( 'Custom Elements', 'balefire-components' ),
				'description'     => __( 'A single question and answer used inside BMA Accordion FAQ.', 'balefire-components' ),
				'icon'            => 'vc_icon-vc-toggle',
				'as_child'        => array( 'only' => 'bma_accordion_faq' ),
				'content_element' => true,
				'params'          => array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Question', 'balefire-components' ),
						'param_name' => 'question',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => __( 'Answer', 'balefire-components' ),
						'param_name' => 'content',
					),
				),
			)
		);
	}
}

if ( ! function_exists( 'bma_accordion_faq_register_container_class' ) ) {
	function bma_accordion_faq_register_container_class(): void {
		if ( ! class_exists( 'WPBakeryShortCodesContainer' ) || class_exists( 'WPBakeryShortCode_BMA_AccordionFaq' ) ) {
			return;
		}

		class WPBakeryShortCode_BMA_AccordionFaq extends WPBakeryShortCodesContainer {}
	}
}
