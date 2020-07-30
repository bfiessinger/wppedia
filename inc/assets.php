<?php

/**
 * Include Styles and Scripts
 * 
 * @since 1.0.0
 */

function wppedia_enqueue_stylesheets() {

	if ( ! wppedia_utils()->is_wiki_post_type() )
		return;

	if ( wppedia_utils()->get_option( \bf\wpPedia\admin::$settings_general_page, 'wppedia_layout_use-inline-styles' ) == 'on' ) {

		wppedia_add_inline_style( wpPediaPluginDir . 'dist/css/components_navigation.min.css' );

	} else {

		wp_enqueue_style( 'wppedia-base', wpPediaPluginUrl . 'dist/css/style.min.css', [], null );

	}	

}
add_action( 'wp_enqueue_scripts', 'wppedia_enqueue_stylesheets' );

function wppedia_print_inline_styles() {

	if ( wppedia_utils()->get_option( \bf\wpPedia\admin::$settings_general_page, 'wppedia_layout_use-inline-styles' ) != 'on' )
		return;

	echo '<style>' . \bf\wpPedia\inline_style_collector::getInstance()->get_final_css() . '</style>';

}
add_action( 'wp_head', 'wppedia_print_inline_styles' );
