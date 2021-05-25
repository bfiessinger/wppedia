<?php

/**
 * Register shortcode to display the WPPedia searchform
 * 
 * @shortcode_name wppedia_searchform
 * @usage [wppedia_searchform]
 * 
 * @since 1.1.5
 */

defined( 'ABSPATH' ) || die();
 
function create_wppedia_searchform_shortcode() {

	ob_start();
	wppedia_get_template_part('search/form');
	return ob_get_clean();

}
add_shortcode( 'wppedia_searchform', 'create_wppedia_searchform_shortcode' );