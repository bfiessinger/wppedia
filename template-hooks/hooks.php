<?php

use WPPedia\options;

/**
 * Global template hooks
 */
require_once __DIR__ . '/global.php';

/**
 * Loop and post template hooks
 */
require_once __DIR__ . '/loop.php';
require_once __DIR__ . '/singular.php';

/**
 * Modules
 */
require_once __DIR__ . '/nav.php';
require_once __DIR__ . '/search.php';

/**
 * Global template hooks
 */
add_action( 'wppedia_before_main_content', 'wppedia_wrapper_start', 10 );

add_action( 'wppedia_after_main_content', 'wppedia_wrapper_end', 20 );

add_action( 'wppedia_sidebar', 'wppedia_sidebar', 10 );

/**
 * Singular page hooks
 */
add_action( 'wppedia_before_single_post', 'wppedia_entry_content_start', 10 );

add_action( 'wppedia_single_post', 'wppedia_single_featured_image', 10 );
add_action( 'wppedia_single_post', 'wppedia_single_title', 20 );
add_action( 'wppedia_single_post', 'wppedia_single_content', 30 );
add_action( 'wppedia_single_post', 'wppedia_single_link_pages', 40 );

add_action( 'wppedia_after_single_post', 'wppedia_entry_content_end', 10 );

/**
 * Archive page hooks
 */
add_action( 'wppedia_before_post_loop', 'wppedia_postlist_wrapper_start', 10 );

add_action( 'wppedia_before_loop_item_title', 'wppedia_loop_featured_image', 10 );

add_action( 'wppedia_loop_item_title', 'wppedia_loop_item_title', 10 );

add_action( 'wppedia_after_loop_item_title', 'wppedia_loop_excerpt', 10 );

add_action( 'wppedia_after_post_loop', 'wppedia_postlist_wrapper_end', 10 );

add_action( 'wppedia_after_main_content', 'wppedia_posts_pagination', 10 );

/**
 * Template Hooks after init
 */
add_action('wp', function() {
	
	/**
	 * Global template hooks
	 */
	if (
		((is_wppedia_frontpage() || is_wppedia_archive()) && false != options::get_option('archive', 'show_nav')) || 
		(is_wppedia_singular() && false != options::get_option('singular', 'show_nav'))
	) {
		add_action( 'wppedia_before_main_content', 'wppedia_navigation', 20 );
	}
	
	if (
		((is_wppedia_frontpage() || is_wppedia_archive()) && false != options::get_option('archive', 'show_searchbar')) ||
		(is_wppedia_singular() && false != options::get_option('singular', 'show_searchbar'))
	) {
		add_action( 'wppedia_before_main_content', 'wppedia_searchform', 30 );
	}

	/**
	 * Archive template hooks
	 */
	if (is_wppedia_frontpage()) {
		add_action( 'wppedia_archive_description', 'wppedia_frontpage_archive_description', 10 );
	}
	
	if (is_tax()) {
		add_action( 'wppedia_archive_description', 'wppedia_taxonomy_archive_description', 10 );
	}

});
