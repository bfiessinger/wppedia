<?php

/**
 * Include Styles and Scripts
 * 
 * @since 1.0.0
 */

/**
 * Enqueue Assets
 * 
 * @since 1.0.0 
 */
function wppedia_enqueue_scripts() {

	if ( ! wppedia_utils()->is_wiki_post_type() )
		return;

	// Load Styles
	if ( 'on' == wppedia_utils()->get_option( \bf\wpPedia\options\plugin_settings::$settings_general_page, 'wppedia_layout_use-inline-styles' ) ) {

		// Initial Letter Navigation Component
		wppedia_add_inline_style( wpPediaPluginDir . 'dist/css/components_navigation.min.css' );

		if ( is_wppedia() && is_singular() ) {

			// Tooltips
			wppedia_add_inline_style( wpPediaPluginDir . 'dist/css/components_tooltip.min.css' );

		}

	} else {

		wp_enqueue_style( 'wppedia-base', wpPediaPluginUrl . 'dist/css/style.min.css', [], null );

	}

	// Scripts
	if ( is_singular() ) {

		// Tooltips
		wp_enqueue_script( 'wppedia_ajax_tooltips', wpPediaPluginUrl . 'dist/js/ajax_tooltip.bundle.js', [], null, true );
		wp_localize_script( 'wppedia_ajax_tooltips', 'wppedia_tooltip_props', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );

	}

}
add_action( 'wp_enqueue_scripts', 'wppedia_enqueue_scripts' );

/**
 * Print inline styles
 * 
 * @since 1.0.0
 */
function wppedia_print_inline_styles() {

	if ( 'on' != wppedia_utils()->get_option( \bf\wpPedia\options\plugin_settings::$settings_general_page, 'wppedia_layout_use-inline-styles' ) )
		return;

	$final_css = \bf\wpPedia\inline_style_collector::getInstance()->get_final_css();
	if ( '' == $final_css )
		return;

	echo '<style>' . $final_css . '</style>';

}
add_action( 'wp_head', 'wppedia_print_inline_styles' );
