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
function wppedia_enqueue_frontend_assets() {

	if ( ! is_wppedia_page() )
		return;

	wp_enqueue_style( 'wppedia-style', wpPediaPluginUrl . 'dist/css/style.min.css', [], null );

	// Scripts
	if ( is_singular() ) {

		// Tooltips
		wp_enqueue_script( 'wppedia_ajax_tooltips', wpPediaPluginUrl . 'dist/js/ajax_tooltip.bundle.js', [], null, true );
		wp_localize_script( 'wppedia_ajax_tooltips', 'wppedia_tooltip_props', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );

	}

	$rest_controller = new bf\wpPedia\rest_controller();
	wp_enqueue_script( 'wppedia_search', wpPediaPluginUrl . 'dist/js/search.bundle.js', [], null, true );
	wp_localize_script( 'wppedia_search', 'wppedia_search_props', [
		'postlist_url' 		=> $rest_controller->get_endpoint_url( $rest_controller->rest_endpoint_search ),
		'search_options'	=> json_encode( [
			'keys' => [ 
				'post_title' 
			],
			'threshold' => 0.5,
		] ),
		'searchinput_id'	=> apply_filters( 'wppedia_search_input_id', 'wppedia_search_input' )
	] );

	// Print inline styles
	global $content_width;
	wppedia_add_inline_style('wppedia-content-width', '.wppedia .content-area{width:100%;max-width:' . $content_width . 'px;margin-left:auto;margin-right:auto;}');
	$final_css = \bf\wpPedia\inline_style_collector::getInstance()->get_final_css();
	if ( '' != $final_css ) {
		wp_register_style( 'wppedia-inline-style', false );
		wp_enqueue_style( 'wppedia-inline-style' );
		wp_add_inline_style( 'wppedia-inline-style', $final_css );
	}

}
add_action( 'wp_enqueue_scripts', 'wppedia_enqueue_frontend_assets' );
