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

	if ( ! bf\wpPedia\helper::getInstance()->is_wiki_post_type() )
		return;

	// Load Styles
	if ( 'on' == bf\wpPedia\helper::getInstance()->get_option( \bf\wpPedia\options\plugin_settings::$settings_general_page, 'wppedia_layout_use-inline-styles' ) ) {

		// Initial Letter Navigation Component
		wppedia_add_inline_style( 'wppedia_component_navigation', wpPediaPluginDir . 'dist/css/components_navigation.min.css' );

		// Searchbar
		wppedia_add_inline_style( 'wppedia_component_searchbar', wpPediaPluginDir . 'dist/css/components_search.min.css' );

		if ( is_singular() ) {

			// Tooltips
			wppedia_add_inline_style( 'wppedia_component_tooltip', wpPediaPluginDir . 'dist/css/components_tooltip.min.css' );

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

	$WPPedia_REST = new bf\wpPedia\rest();
	wp_enqueue_script( 'wppedia_search', wpPediaPluginUrl . 'dist/js/search.bundle.js', [], null, true );
	wp_localize_script( 'wppedia_search', 'wppedia_search_props', [
		'postlist_url' 		=> $WPPedia_REST->get_endpoint_url( $WPPedia_REST->rest_endpoint_search ),
		'search_options'	=> json_encode( [
			'keys' => [ 
				'post_title' 
			],
			'threshold' => 0.5,
		] ),
		'searchinput_id'	=> bf\wpPedia\template::getInstance()->__filtered_search_input_id()
	] );

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
