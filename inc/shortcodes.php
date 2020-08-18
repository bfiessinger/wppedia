<?php

// Create Shortcode wppedia_navigation
// Shortcode: [wppedia_navigation]
function create_wppedia_navigation_shortcode() {

	ob_start();
	bf\wpPedia\template::getInstance()->get_char_navigation();
	echo ob_get_clean();

}
add_shortcode( 'wppedia_navigation', 'create_wppedia_navigation_shortcode' );

// Create Shortcode wppedia_searchform
// Shortcode: [wppedia_searchform]
function create_wppedia_searchform_shortcode() {

	ob_start();
	bf\wpPedia\template::getInstance()->get_search_form();
	echo ob_get_clean();

}
add_shortcode( 'wppedia_searchform', 'create_wppedia_searchform_shortcode' );
