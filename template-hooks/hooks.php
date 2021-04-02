<?php

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
 * Hook: wppedia_before_main_content
 * 
 */
add_action( 'wppedia_before_main_content', 'wppedia_wrapper_start', 10 );
add_action( 'wppedia_before_main_content', 'wppedia_navigation', 20 );
add_action( 'wppedia_before_main_content', 'wppedia_searchform', 30 );

/**
 * Hook: wppedia_before_single_post
 * 
 */
add_action( 'wppedia_before_single_post', 'wppedia_entry_content_start', 10 );

/**
 * Hook: wppedia_loop_content
 * 
 */
add_action( 'wppedia_loop_content', 'wppedia_loop_item_wrapper_start', 10 );
add_action( 'wppedia_loop_content', 'wppedia_loop_title', 20 );
add_action( 'wppedia_loop_content', 'wppedia_loop_excerpt', 30 );
add_action( 'wppedia_loop_content', 'wppedia_loop_item_wrapper_end', 40 );

/**
 * Hook: wppedia_after_single_post
 * 
 */
add_action( 'wppedia_after_single_post', 'wppedia_entry_content_end', 10 );

/**
 * Hook: wppedia_sidebar
 * 
 */
add_action( 'wppedia_sidebar', 'wppedia_sidebar', 10 );


/**
 * Hook: wppedia_after_main_content
 *
 */
add_action( 'wppedia_after_main_content', 'wppedia_posts_pagination', 10 );
add_action( 'wppedia_after_main_content', 'wppedia_wrapper_end', 20 );
