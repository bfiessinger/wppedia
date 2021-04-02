<?php

/**
 * Start of the main wrapper of WPPedia content
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_wrapper_start' ) ) {
	function wppedia_wrapper_start() {
		wppedia_get_template_part('global/wrapper', 'start');
	}
}

/**
 * End of the main Wrapper of WPPedia content
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_wrapper_end' ) ) {
	function wppedia_wrapper_end() {
		wppedia_get_template_part('global/wrapper', 'end');
	}
}

/**
 * Display the WPPedia Sidebar
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_sidebar' ) ) {
	function wppedia_sidebar() {
		wppedia_get_template_part('global/sidebar');
	}
}