<?php

// Create Shortcode wppedia_navigation
// Shortcode: [wppedia_navigation]
function create_wppedianavigation_shortcode() {

	ob_start();

	wppedia_template()->initial_letter_navigation();

	echo ob_get_clean();

}
add_shortcode( 'wppedia_navigation', 'create_wppedianavigation_shortcode' );
