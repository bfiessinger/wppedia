<?php

/**
 * WPPedia Customizer
 *
 * @since 1.3.0
 */

namespace WPPedia;

// use \Kirki;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class Customizer {

	protected function __clone() {}

	public function _init() {
		add_action( 'customize_register', [ $this, 'customize_register' ] );
		add_action( 'customize_preview_init', [ $this, 'customize_preview_init' ] );
	}

	public function customize_register( $wp_customize ) {

		$wp_customize->add_section( '_wppedia_customize', [
			'title' => __( 'WPPedia', 'wppedia' ),
			'priority' => 1,
		] );

		$wp_customize->add_setting( 'wppedia_main_color', [
			'default' => '#160351',
			'transport' => 'refresh',
		] );

		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'wppedia_main_color', [
			'label' => __( 'Main Color', 'wppedia' ),
			'section' => '_wppedia_customize',
			'settings' => 'wppedia_main_color',
		] ) );

	}

	public function customize_preview_init() {
		//wp_enqueue_script( 'wppedia-customizer', get_template_directory_uri() . '/js/customizer.js', [ 'jquery', 'customize-preview' ], '', true );
	}

}
