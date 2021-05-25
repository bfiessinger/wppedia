<?php

/**
 * Register shortcode to display the WPPedia initial char
 * navigation
 * 
 * @shortcode_name wppedia_navigation
 * @usage [wppedia_navigation]
 * 
 * @since 1.1.5
 */

defined( 'ABSPATH' ) || die();

function create_wppedia_navigation_shortcode() {

	ob_start();
	wppedia_get_template_part('nav/char', 'navigation');
	return ob_get_clean();

}
add_shortcode( 'wppedia_navigation', 'create_wppedia_navigation_shortcode' );