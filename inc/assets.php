<?php

/**
 * Include Styles and Scripts
 * 
 * @since 1.0.0
 */

function wppedia_enqueue_assets() {

	if ( ! wppedia_utils()->is_wiki_post_type() )
		return;

	wp_enqueue_style( 'wppedia-base', wpPediaPluginUrl . 'dist/css/style.min.css', [], null );

}
add_action( 'wp_enqueue_scripts', 'wppedia_enqueue_assets', 202 );
